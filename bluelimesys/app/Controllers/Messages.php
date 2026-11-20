<?php

namespace App\Controllers;

use App\Models\System\ChatMessageModel;

class Messages extends BaseController
{
    private const STREAM_SECONDS = 25;
    private const STREAM_TICK_SECONDS = 2;

    public function conversations()
    {
        $me = $this->current_user_id();
        $model = model('System/ChatMessageModel');

        return $this->response->setJSON([
            'status' => 'success',
            'value' => [
                'me' => $me,
                'users' => $model->conversation_list($me),
                'unread_total' => $model->total_unread($me),
            ],
        ]);
    }

    public function heartbeat()
    {
        $me = $this->current_user_id();
        \Config\Database::connect()->query(
            'INSERT INTO chat_presence (user_id, last_seen_at) VALUES (?, CURRENT_TIMESTAMP) ON DUPLICATE KEY UPDATE last_seen_at = CURRENT_TIMESTAMP',
            [$me]
        );

        return $this->response->setJSON(['status' => 'success']);
    }

    /**
     * Server-Sent Events feed. Pushes new messages and the unread total to the browser.
     * The connection closes after STREAM_SECONDS; EventSource reconnects automatically and
     * sends the last event id, so no message is missed between connections.
     */
    public function stream()
    {
        $me = $this->current_user_id();
        session_write_close();

        $model = model('System/ChatMessageModel');
        $after = (int) $this->request->getHeaderLine('Last-Event-ID');
        if ($after === 0) {
            $after = $model->latest_id_for_user($me);
        }

        set_time_limit(0);
        while (ob_get_level() > 0) {
            ob_end_flush();
        }
        header('Content-Type: text/event-stream; charset=utf-8');
        header('Cache-Control: no-cache, no-transform');
        header('X-Accel-Buffering: no');

        $last_unread = null;
        $deadline = time() + self::STREAM_SECONDS;
        while (time() < $deadline && !connection_aborted()) {
            $messages = $model->new_messages_for_user($me, $after);
            $unread = $model->total_unread($me);

            if ($messages !== [] || $unread !== $last_unread) {
                foreach ($messages as $message) {
                    $after = max($after, (int) $message['id']);
                }
                $this->sse_event('update', ['me' => $me, 'unread_total' => $unread, 'messages' => $messages], $after);
                $last_unread = $unread;
            } else {
                echo ": keep-alive\n\n";
                flush();
            }

            sleep(self::STREAM_TICK_SECONDS);
        }

        exit;
    }

    public function thread(int $other_id)
    {
        $me = $this->current_user_id();
        $after = (int) $this->request->getGet('after');
        $model = model('System/ChatMessageModel');

        $model->mark_read($me, $other_id);
        $messages = $model->thread($me, $other_id, $after);

        return $this->response->setJSON([
            'status' => 'success',
            'value' => [
                'messages' => $messages,
                'read_receipts' => $model->read_receipts($me, $other_id),
                'me' => $me,
            ],
        ]);
    }

    public function send()
    {
        $me = $this->current_user_id();
        $content_length = (int) $this->request->getServer('CONTENT_LENGTH');
        $post_max_size = $this->ini_size_to_bytes((string) ini_get('post_max_size'));
        if ($content_length > 0 && $post_max_size > 0 && $content_length > $post_max_size) {
            return $this->json_error('The upload exceeds the server\'s maximum allowed size. Please try a smaller file.');
        }

        $raw_recipient = $this->request->getPost('recipient_id');
        $raw_recipients = $this->request->getPost('recipient_ids');
        $body = trim((string) $this->request->getPost('body'));
        $file = $this->request->getFile('attachment');
        if ($file !== null && !$file->isValid() && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            if (in_array($file->getError(), [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
                return $this->json_error('Documents can be at most 100 MB. The server upload limit may be lower.');
            }
            return $this->json_error('Could not upload the document. Please try again.');
        }
        $has_file = $file !== null && $file->isValid();

        $recipient_ids = [];
        if (is_array($raw_recipients)) {
            foreach ($raw_recipients as $id) {
                if (is_numeric($id)) {
                    $recipient_ids[] = (int) $id;
                }
            }
        } elseif (is_numeric($raw_recipient)) {
            $recipient_ids[] = (int) $raw_recipient;
        }
        $recipient_ids = array_values(array_unique(array_filter(
            $recipient_ids,
            static fn (int $id): bool => $id > 0
        )));

        if ($recipient_ids === []) {
            return $this->json_error('Please choose a recipient.');
        }
        if (in_array($me, $recipient_ids, true)) {
            return $this->json_error('You cannot send a message to yourself.');
        }

        $user_model = model('System/UserModel');
        $recipient_count = $user_model
            ->where('active', 1)
            ->whereIn('id', $recipient_ids)
            ->countAllResults();
        if ($recipient_count !== count($recipient_ids)) {
            return $this->json_error('The selected user is not available.');
        }

        if ($body === '' && !$has_file) {
            return $this->json_error('Write a message or attach a document.');
        }
        if (mb_strlen($body) > 5000) {
            return $this->json_error('Messages can be at most 5000 characters.');
        }

        $row = ['sender_id' => $me, 'body' => $body !== '' ? $body : null];
        $stored_name = null;

        if ($has_file) {
            $extension = strtolower((string) $file->getClientExtension());
            if (!ChatMessageModel::is_allowed_attachment_extension($extension)) {
                return $this->json_error('This file type is not allowed.');
            }
            if ($file->getSize() > ChatMessageModel::MAX_ATTACHMENT_BYTES) {
                return $this->json_error('Documents can be at most 100 MB.');
            }

            $directory = $this->attachment_directory();
            if (!is_dir($directory) && !mkdir($directory, 0750, true)) {
                return $this->json_error('Could not store the document. Please try again.');
            }

            $stored_name = bin2hex(random_bytes(16)) . '.' . $extension;
            if (!$file->move($directory, $stored_name)) {
                return $this->json_error('Could not store the document. Please try again.');
            }

            $row['attachment_name'] = ChatMessageModel::safe_attachment_name($file->getClientName());
            $row['attachment_path'] = $stored_name;
            $row['attachment_size'] = $file->getSize();
        }

        $model = model('System/ChatMessageModel');
        $db = \Config\Database::connect();
        $messages = [];
        $db->transStart();
        try {
            foreach ($recipient_ids as $recipient_id) {
                $recipient_row = $row;
                $recipient_row['recipient_id'] = $recipient_id;
                $model->insert($recipient_row);
                $message = $model->where('id', $model->getInsertID())->first();
                if ($message) {
                    $messages[] = $message;
                }
            }
        } catch (\Throwable $e) {
            $db->transComplete();
            if ($stored_name !== null) {
                @unlink($this->attachment_directory() . DIRECTORY_SEPARATOR . $stored_name);
            }
            return $this->json_error('Could not send the message. Please try again.');
        }
        $db->transComplete();
        if (!$db->transStatus() || count($messages) !== count($recipient_ids)) {
            if ($stored_name !== null) {
                @unlink($this->attachment_directory() . DIRECTORY_SEPARATOR . $stored_name);
            }
            return $this->json_error('Could not send the message. Please try again.');
        }

        return $this->response->setJSON([
            'status' => 'success',
            'value' => count($messages) === 1 ? $messages[0] : $messages,
        ]);
    }

    public function attachment(int $id)
    {
        $me = $this->current_user_id();
        $row = model('System/ChatMessageModel')->find_attachment_for_user($id, $me);
        if ($row === null) {
            return $this->response->setStatusCode(404);
        }

        $full_path = $this->attachment_directory() . DIRECTORY_SEPARATOR . basename((string) $row['attachment_path']);
        if (!is_file($full_path)) {
            return $this->response->setStatusCode(404);
        }

        return $this->response
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->download($row['attachment_name'], file_get_contents($full_path));
    }

    private function current_user_id(): int
    {
        $loggeduserdata = \BluelimeSystem::$Authentication::get_logged_user_data();
        return (int) $loggeduserdata['id'];
    }

    private function ini_size_to_bytes(string $size): int
    {
        if (!preg_match('/^\s*(\d+)\s*([KMG]?)\s*$/i', $size, $matches)) {
            return 0;
        }

        $bytes = (int) $matches[1];
        switch (strtoupper($matches[2])) {
            case 'G':
                return $bytes * 1024 * 1024 * 1024;
            case 'M':
                return $bytes * 1024 * 1024;
            case 'K':
                return $bytes * 1024;
            default:
                return $bytes;
        }
    }

    private function json_error(string $message)
    {
        return $this->response->setJSON(['status' => 'error', 'message' => $message]);
    }

    private function sse_event(string $event, array $data, int $id): void
    {
        echo "id: {$id}\n";
        echo "event: {$event}\n";
        echo 'data: ' . json_encode($data) . "\n\n";
        flush();
    }

    private function attachment_directory(): string
    {
        $directory = trim((string) ($_ENV['CHAT_ATTACHMENT_PATH'] ?? ''));
        if ($directory === '') {
            throw new \RuntimeException('The CHAT_ATTACHMENT_PATH environment variable must be configured.');
        }

        return rtrim($directory, '/\\');
    }
}
