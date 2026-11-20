<?php

namespace App\Models\System;

use CodeIgniter\Model;

class ChatMessageModel extends Model
{
    protected $table      = 'chat_message';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = ['sender_id', 'recipient_id', 'body', 'attachment_name', 'attachment_path', 'attachment_size', 'read_at'];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public const ALLOWED_ATTACHMENT_EXTENSIONS = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'odt', 'ods', 'txt', 'csv', 'png', 'jpg', 'jpeg', 'gif', 'webp'];
    public const MAX_ATTACHMENT_BYTES = 100 * 1024 * 1024;
    public const THREAD_PAGE_SIZE = 50;

    public static function is_allowed_attachment_extension(string $extension): bool
    {
        return in_array(strtolower($extension), self::ALLOWED_ATTACHMENT_EXTENSIONS, true);
    }

    public static function safe_attachment_name(string $name): string
    {
        $name = basename(str_replace('\\', '/', $name));
        $name = preg_replace('/[^A-Za-z0-9._ -]/', '_', $name);
        $name = trim((string) $name, '. ');
        return $name === '' ? 'attachment' : mb_substr($name, 0, 200);
    }

    /**
     * Active users other than $me, with presence, unread counts, and latest conversation time.
     */
    public function conversation_list(int $me): array
    {
        $db = $this->db;
        $sql = "SELECT u.id, u.name, u.title, u.role,
                    CASE WHEN p.last_seen_at >= DATE_SUB(NOW(), INTERVAL 90 SECOND) THEN 1 ELSE 0 END AS is_online,
                    (SELECT COUNT(*) FROM chat_message m
                        WHERE m.sender_id = u.id AND m.recipient_id = ? AND m.read_at IS NULL) AS unread_count,
                    (SELECT MAX(m.created_at) FROM chat_message m
                        WHERE (m.sender_id = u.id AND m.recipient_id = ?) OR (m.sender_id = ? AND m.recipient_id = u.id)) AS last_message_at
                FROM sys_user u
                LEFT JOIN chat_presence p ON p.user_id = u.id
                WHERE u.id <> ? AND u.active = 1 AND u.deleted_at IS NULL
                ORDER BY last_message_at IS NULL, last_message_at DESC, u.name ASC";
        return $db->query($sql, [$me, $me, $me, $me])->getResultArray();
    }

    /**
     * Messages between $me and $other. With $afterId = 0 returns the latest page,
     * otherwise returns only messages newer than $afterId. Always ordered oldest first.
     */
    public function thread(int $me, int $other, int $afterId = 0): array
    {
        $builder = $this->builder()
            ->select('id, sender_id, recipient_id, body, attachment_name, attachment_size, read_at, created_at')
            ->groupStart()
                ->where('sender_id', $me)->where('recipient_id', $other)
            ->groupEnd()
            ->orGroupStart()
                ->where('sender_id', $other)->where('recipient_id', $me)
            ->groupEnd();

        if ($afterId > 0) {
            $builder->where('id >', $afterId)->orderBy('id', 'ASC');
            return $builder->get()->getResultArray();
        }

        $rows = $builder->orderBy('id', 'DESC')->limit(self::THREAD_PAGE_SIZE)->get()->getResultArray();
        return array_reverse($rows);
    }

    public function total_unread(int $me): int
    {
        return (int) $this->builder()
            ->where('recipient_id', $me)
            ->where('read_at', null)
            ->countAllResults();
    }

    public function read_receipts(int $sender, int $recipient): array
    {
        return $this->builder()
            ->select('id, read_at')
            ->where('sender_id', $sender)
            ->where('recipient_id', $recipient)
            ->where('read_at IS NOT NULL', null, false)
            ->orderBy('id', 'DESC')
            ->limit(self::THREAD_PAGE_SIZE)
            ->get()->getResultArray();
    }

    public function latest_id_for_user(int $me): int
    {
        $row = $this->builder()
            ->select('MAX(id) AS max_id')
            ->groupStart()
                ->where('sender_id', $me)->orWhere('recipient_id', $me)
            ->groupEnd()
            ->get()->getRowArray();
        return (int) ($row['max_id'] ?? 0);
    }

    /**
     * Messages sent to or by $me with an id greater than $afterId, oldest first.
     */
    public function new_messages_for_user(int $me, int $afterId): array
    {
        return $this->builder()
            ->select('id, sender_id, recipient_id, body, attachment_name, attachment_size, read_at, created_at')
            ->groupStart()
                ->where('sender_id', $me)->orWhere('recipient_id', $me)
            ->groupEnd()
            ->where('id >', $afterId)
            ->orderBy('id', 'ASC')
            ->limit(100)
            ->get()->getResultArray();
    }

    public function mark_read(int $me, int $other): void
    {
        $this->builder()
            ->set('read_at', date('Y-m-d H:i:s'))
            ->where('sender_id', $other)
            ->where('recipient_id', $me)
            ->where('read_at', null)
            ->update();
    }

    public function find_attachment_for_user(int $id, int $me): ?array
    {
        $row = $this->builder()
            ->where('id', $id)
            ->where('attachment_path IS NOT NULL', null, false)
            ->groupStart()
                ->where('sender_id', $me)->orWhere('recipient_id', $me)
            ->groupEnd()
            ->get()->getRowArray();
        return $row ?: null;
    }
}
