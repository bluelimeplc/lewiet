<link rel="stylesheet" href="<?= base_url('css/chat-widget.css') ?>?v=<?= filemtime(FCPATH . 'css/chat-widget.css') ?>">

<button type="button" id="chat-fab" class="chat-fab" title="Messages" aria-label="Open messages">
    <i class="pe-7s-chat"></i>
    <span class="chat-unread-count chat-unread-badge" hidden>0</span>
</button>

<div id="chat-widget" class="chat-widget" data-base-url="<?= base_url('messages') ?>" hidden>
    <div class="chat-widget-header">
        <button type="button" class="chat-icon-btn" id="chat-back" title="Back" hidden><i class="pe-7s-angle-left"></i></button>
        <div class="chat-widget-title" id="chat-title">Messages</div>
        <button type="button" class="chat-header-action" id="chat-new-message" title="Send a message to multiple people" aria-label="Send a message to multiple people">Send to multiple</button>
        <button type="button" class="chat-icon-btn" id="chat-close" title="Close"><i class="pe-7s-close"></i></button>
    </div>

    <div class="chat-view" id="chat-users-view">
        <div class="chat-compose-toolbar" id="chat-compose-toolbar" hidden>
            <span id="chat-selected-count" hidden>0 selected</span>
            <button type="button" class="chat-compose-btn" id="chat-compose-continue" hidden>Continue</button>
            <div class="chat-bulk-controls" id="chat-bulk-controls" hidden>
                <label class="chat-select-all"><input type="checkbox" id="chat-select-all"> Select all</label>
                <select id="chat-select-role" class="chat-role-select" aria-label="Add all users with a role">
                    <option value="">Add everyone in role...</option>
                </select>
            </div>
        </div>
        <ul class="chat-user-list" id="chat-user-list">
            <li class="chat-empty">Loading...</li>
        </ul>
    </div>

    <div class="chat-view" id="chat-thread-view" hidden>
        <div class="chat-recipient-summary" id="chat-recipient-summary" hidden aria-live="polite"></div>
        <div class="chat-thread" id="chat-thread"></div>
        <form id="chat-form" class="chat-form" enctype="multipart/form-data">
            <input type="hidden" name="recipient_id" id="chat-recipient-id">
            <div class="chat-error" id="chat-error" hidden></div>
            <div class="chat-upload-progress" id="chat-upload-progress" hidden>
                <div class="chat-upload-progress-track" role="progressbar" aria-label="File upload progress" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                    <div class="chat-upload-progress-value" id="chat-upload-progress-value"></div>
                </div>
                <span id="chat-upload-progress-label" aria-live="polite"></span>
            </div>
            <div class="chat-attach-chip" id="chat-attach-chip" hidden>
                <i class="pe-7s-paperclip"></i>
                <span id="chat-attach-name"></span>
                <button type="button" class="chat-icon-btn" id="chat-attach-clear" title="Remove document"><i class="pe-7s-close"></i></button>
            </div>
            <div class="chat-input-row">
                <button type="button" class="chat-icon-btn chat-attach-btn" id="chat-attach-btn" title="Attach a document"><i class="pe-7s-paperclip"></i></button>
                <input type="file" name="attachment" id="chat-attachment" class="chat-file-input" tabindex="-1"
                    data-max-size="<?= \App\Models\System\ChatMessageModel::MAX_ATTACHMENT_BYTES ?>"
                    accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.odt,.ods,.txt,.csv,.png,.jpg,.jpeg,.gif,.webp">
                <textarea name="body" id="chat-body" rows="1" maxlength="5000" placeholder="Type a message..."></textarea>
                <button type="submit" class="chat-send-btn" id="chat-send" title="Send"><i class="pe-7s-paper-plane"></i></button>
            </div>
        </form>
    </div>
</div>

<script src="<?= base_url('js/chat-widget.js') ?>?v=<?= filemtime(FCPATH . 'js/chat-widget.js') ?>"></script>
