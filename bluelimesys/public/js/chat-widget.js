(function ($) {
    'use strict';

    var $widget = $('#chat-widget');
    if (!$widget.length) {
        return;
    }

    var baseUrl = $widget.data('base-url');
    var maxAttachmentBytes = Number($('#chat-attachment').data('max-size'));
    var FALLBACK_POLL_MS = 10000;
    var PRESENCE_INTERVAL_MS = 30000;
    var USER_LIST_INTERVAL_MS = 15000;
    var THREAD_SYNC_INTERVAL_MS = 5000;
    var selectedAttachment = null;

    var me = null;
    var activeUserId = null;
    var activeRecipientIds = [];
    var activeName = '';
    var composeMode = false;
    var chatUsersCache = [];
    var lastMessageId = 0;
    var isOpen = false;
    var notificationAudio = null;

    function formatTime(value) {
        if (!value) {
            return '';
        }
        var d = new Date(String(value).replace(' ', 'T'));
        if (isNaN(d.getTime())) {
            return value;
        }
        return d.toLocaleString([], { hour: '2-digit', minute: '2-digit', day: '2-digit', month: 'short' });
    }

    function initials(name) {
        var parts = $.trim(name || '?').split(/\s+/);
        var first = parts[0].charAt(0);
        var last = parts.length > 1 ? parts[parts.length - 1].charAt(0) : '';
        return (first + last).toUpperCase();
    }

    function setUnread(total) {
        total = parseInt(total, 10) || 0;
        $('.chat-unread-count')
            .text(total > 99 ? '99+' : total)
            .prop('hidden', total === 0);
    }

    function updateRecipientControls() {
        var allSelected = chatUsersCache.length > 0 && activeRecipientIds.length === chatUsersCache.length;
        $('#chat-select-all')
            .prop('checked', allSelected)
            .prop('indeterminate', activeRecipientIds.length > 0 && !allSelected);
        $('#chat-selected-count')
            .text(activeRecipientIds.length + ' selected')
            .prop('hidden', activeRecipientIds.length === 0);
        $('#chat-compose-continue').prop('hidden', activeRecipientIds.length === 0);
    }

    function updateRoleOptions() {
        var $select = $('#chat-select-role');
        $select.find('option:not(:first)').remove();
        var roles = [];
        $.each(chatUsersCache, function (_, user) {
            var role = $.trim(user.role || '');
            if (role && roles.indexOf(role) === -1) {
                roles.push(role);
            }
        });
        roles.sort();
        $.each(roles, function (_, role) {
            $select.append($('<option></option>').val(role).text(role));
        });
    }

    function unlockNotificationAudio() {
        var AudioContextType = window.AudioContext || window.webkitAudioContext;
        if (!AudioContextType) {
            return;
        }

        try {
            notificationAudio = notificationAudio || new AudioContextType();
            if (notificationAudio.state === 'suspended') {
                notificationAudio.resume().catch(function (error) {
                    if (window.console && console.warn) {
                        console.warn('Could not enable chat notification audio.', error);
                    }
                });
            }
        } catch (error) {
            if (window.console && console.warn) {
                console.warn('Could not enable chat notification audio.', error);
            }
        }
    }

    function playMessageTone() {
        if (navigator.onLine === false || !notificationAudio || notificationAudio.state !== 'running') {
            return;
        }

        var startAt = notificationAudio.currentTime;
        [660, 880].forEach(function (frequency, index) {
            var start = startAt + index * 0.13;
            var oscillator = notificationAudio.createOscillator();
            var volume = notificationAudio.createGain();
            oscillator.type = 'sine';
            oscillator.frequency.setValueAtTime(frequency, start);
            volume.gain.setValueAtTime(0.0001, start);
            volume.gain.exponentialRampToValueAtTime(0.08, start + 0.02);
            volume.gain.exponentialRampToValueAtTime(0.0001, start + 0.12);
            oscillator.connect(volume);
            volume.connect(notificationAudio.destination);
            oscillator.start(start);
            oscillator.stop(start + 0.13);
        });
    }

    function showError(message) {
        $('#chat-error').text(message).prop('hidden', false);
    }

    function clearError() {
        $('#chat-error').text('').prop('hidden', true);
    }

    function showView(view) {
        var inThread = view === 'thread';
        $('#chat-users-view').prop('hidden', inThread);
        $('#chat-thread-view').prop('hidden', !inThread);
        $('#chat-back').prop('hidden', !inThread);
        $('#chat-new-message').prop('hidden', inThread);
        $('#chat-compose-toolbar').prop('hidden', inThread || !composeMode);
        $('#chat-bulk-controls').prop('hidden', inThread || !composeMode);
        $('#chat-title').text(inThread ? activeName : 'Messages');
    }

    function openChat() {
        isOpen = true;
        $widget.prop('hidden', false);
        $('#chat-fab').hide();
        loadConversations();
        if (activeUserId !== null) {
            loadThread(false);
            $('#chat-body').trigger('focus');
        }
    }

    function closeChat() {
        isOpen = false;
        $widget.prop('hidden', true);
        $('#chat-fab').show();
    }

    function renderUsers(users) {
        var $list = $('#chat-user-list').empty();
        if (!users.length) {
            $list.append($('<li class="chat-empty"></li>').text('No other users found.'));
            updateRecipientControls();
            return;
        }
        $.each(users, function (_, u) {
            var name = u.name || u.username || ('User ' + u.id);
            var $item = $('<li class="chat-user-item"></li>').attr('data-user-id', u.id).attr('data-name', name);
            if (composeMode) {
                $item.toggleClass('selected', activeRecipientIds.indexOf(String(u.id)) !== -1)
                    .append($('<input type="checkbox" class="chat-user-checkbox">')
                        .prop('checked', activeRecipientIds.indexOf(String(u.id)) !== -1));
            }
            var online = Number(u.is_online) === 1;
            var $avatar = $('<div class="chat-avatar-wrap"></div>')
                .append($('<div class="chat-avatar"></div>').text(initials(name)))
                .append($('<span class="chat-status-dot"></span>')
                    .toggleClass('online', online)
                    .attr('title', online ? 'Online' : 'Offline')
                    .attr('aria-label', online ? 'Online' : 'Offline'));
            $item.append($avatar);

            var $info = $('<div class="chat-user-info"></div>');
            $info.append($('<div class="chat-user-name"></div>').text(name));
            $info.append($('<div class="chat-user-sub"></div>').text(u.title || u.role || ''));
            $item.append($info);

            var unread = parseInt(u.unread_count, 10) || 0;
            if (unread > 0) {
                $item.append($('<span class="chat-unread-pill"></span>').text(unread > 99 ? '99+' : unread));
            }
            $list.append($item);
        });
        updateRecipientControls();
    }

    function renderMessage(m) {
        var mine = Number(m.sender_id) === Number(me);
        var $bubble = $('<div class="chat-bubble"></div>');
        if (m.body) {
            $bubble.append($('<div class="chat-body"></div>').text(m.body));
        }
        if (m.attachment_name) {
            var href = baseUrl + '/attachment/' + encodeURIComponent(m.id);
            $bubble.append(
                $('<div class="chat-file"></div>').append(
                    $('<i class="pe-7s-paperclip"></i>'),
                    $('<a target="_blank" rel="noopener"></a>').attr('href', href).text(m.attachment_name)
                )
            );
        }
        var $meta = $('<div class="chat-meta"></div>')
            .append($('<div class="chat-time"></div>').text(formatTime(m.created_at)));
        if (mine && m.read_at) {
            $meta.append(seenIndicator());
        }
        $bubble.append($meta);
        return $('<div class="chat-row"></div>').attr('data-id', Number(m.id)).toggleClass('mine', mine).append($bubble);
    }

    function seenIndicator() {
        return $('<span class="chat-seen" title="Seen" aria-label="Seen"><i class="pe-7s-look" aria-hidden="true"></i></span>');
    }

    function updateReadReceipts(receipts) {
        $.each(receipts || [], function (_, receipt) {
            var $row = $('#chat-thread .chat-row[data-id="' + Number(receipt.id) + '"]');
            if ($row.length && !$row.find('.chat-seen').length) {
                $row.find('.chat-meta').append(seenIndicator());
            }
        });
    }

    function appendMessages(messages, scrollToEnd) {
        var thread = $('#chat-thread')[0];
        var atBottom = thread.scrollHeight - thread.scrollTop - thread.clientHeight < 60;

        $.each(messages, function (_, m) {
            lastMessageId = Math.max(lastMessageId, Number(m.id));
            if ($('#chat-thread .chat-row[data-id="' + Number(m.id) + '"]').length) {
                return;
            }
            $('#chat-thread').append(renderMessage(m));
        });

        if ($('#chat-thread').children().length === 0) {
            $('#chat-thread').append($('<div class="chat-thread-empty"></div>').text('No messages yet. Say hello!'));
        } else {
            $('#chat-thread .chat-thread-empty').remove();
        }
        if (scrollToEnd || atBottom) {
            thread.scrollTop = thread.scrollHeight;
        }
    }

    function loadThread(reset) {
        if (activeUserId === null) {
            return;
        }
        var userId = activeUserId;
        var after = reset ? 0 : lastMessageId;
        $.getJSON(baseUrl + '/thread/' + encodeURIComponent(userId), { after: after })
            .done(function (res) {
                if (userId !== activeUserId || res.status !== 'success') {
                    return;
                }
                if (reset) {
                    $('#chat-thread').empty();
                    lastMessageId = 0;
                }
                if (res.value.messages.length || reset) {
                    appendMessages(res.value.messages, reset);
                }
                updateReadReceipts(res.value.read_receipts);
                if (reset || res.value.messages.length) {
                    loadConversations();
                }
            });
    }

    function loadConversations() {
        $.getJSON(baseUrl + '/conversations')
            .done(function (res) {
                if (res.status !== 'success') {
                    return;
                }
                me = res.value.me;
                chatUsersCache = res.value.users || [];
                updateRoleOptions();
                setUnread(res.value.unread_total);
                renderUsers(res.value.users);
            });
    }

    function selectUser(userId, name) {
        activeUserId = String(userId);
        activeRecipientIds = [activeUserId];
        activeName = name;
        $('#chat-recipient-summary').text('').prop('hidden', true);
        lastMessageId = 0;
        $('.chat-recipient-input').remove();
        if (!$('#chat-recipient-id').length) {
            $('<input>', { type: 'hidden', name: 'recipient_id', id: 'chat-recipient-id' }).appendTo('#chat-form');
        }
        $('#chat-recipient-id').val(activeUserId);
        $('#chat-thread').empty();
        clearError();
        showView('thread');
        loadThread(true);
        $('#chat-body').trigger('focus');
    }

    function leaveThread() {
        activeUserId = null;
        activeRecipientIds = [];
        composeMode = false;
        $('#chat-recipient-summary').text('').prop('hidden', true);
        $('#chat-new-message')
            .text('Send to multiple')
            .attr({ title: 'Send a message to multiple people', 'aria-label': 'Send a message to multiple people' });
        $('#chat-select-all').prop({ checked: false, indeterminate: false });
        $('#chat-select-role').val('');
        updateRecipientControls();
        $('.chat-recipient-input').remove();
        if (!$('#chat-recipient-id').length) {
            $('<input>', { type: 'hidden', name: 'recipient_id', id: 'chat-recipient-id' }).appendTo('#chat-form');
        }
        showView('users');
        loadConversations();
    }

    function clearAttachment() {
        $('#chat-attachment').val('');
        selectedAttachment = null;
        $('#chat-attach-name').text('');
        $('#chat-attach-chip').prop('hidden', true);
    }

    function selectAttachment(file) {
        var $input = $('#chat-attachment');
        if (!file) {
            clearAttachment();
            return false;
        }
        if (file.size > maxAttachmentBytes) {
            clearAttachment();
            showError('Documents can be at most 100 MB.');
            return false;
        }
        var extension = '.' + file.name.split('.').pop().toLowerCase();
        var allowedExtensions = ($input.attr('accept') || '').toLowerCase().split(',');
        if (allowedExtensions.indexOf(extension) === -1) {
            clearAttachment();
            showError('This file type is not allowed.');
            return false;
        }

        selectedAttachment = file;
        $('#chat-attach-name').text(file.name);
        $('#chat-attach-chip').prop('hidden', false);
        return true;
    }

    function setUploadProgress(percent, label) {
        var value = Math.max(0, Math.min(100, percent));
        $('#chat-upload-progress').prop('hidden', false);
        $('#chat-upload-progress-value').css('width', value + '%');
        $('.chat-upload-progress-track').attr('aria-valuenow', value);
        $('#chat-upload-progress-label').text(label);
    }

    function hideUploadProgress() {
        $('#chat-upload-progress').prop('hidden', true);
        $('#chat-upload-progress-value').css('width', '0%');
        $('.chat-upload-progress-track').attr('aria-valuenow', 0);
        $('#chat-upload-progress-label').text('');
    }

    function sendHeartbeat() {
        $.ajax({
            url: baseUrl + '/heartbeat',
            method: 'POST',
            dataType: 'json'
        }).fail(function () {
            if (window.console && console.error) {
                console.error('Unable to update chat presence.');
            }
        });
    }

    function connectPush() {
        if (!window.EventSource) {
            setInterval(loadConversations, FALLBACK_POLL_MS);
            return;
        }

        var source = new EventSource(baseUrl + '/stream');
        source.addEventListener('update', function (e) {
            var data = JSON.parse(e.data);
            me = data.me;
            setUnread(data.unread_total);
            if (data.messages.some(function (message) {
                return Number(message.recipient_id) === Number(me);
            })) {
                playMessageTone();
            }
            if (!data.messages.length) {
                return;
            }
            loadConversations();
            var involvesActive = activeUserId !== null && data.messages.some(function (m) {
                return String(m.sender_id) === activeUserId || String(m.recipient_id) === activeUserId;
            });
            if (isOpen && involvesActive) {
                loadThread(false);
            }
        });
        source.onerror = function () {
            if (source.readyState === EventSource.CLOSED) {
                setInterval(loadConversations, FALLBACK_POLL_MS);
            }
        };
    }

    $(document).on('click', '.js-chat-open', function (e) {
        e.preventDefault();
        openChat();
    });
    $(document).one('pointerdown keydown', unlockNotificationAudio);
    $('#chat-fab').on('click', openChat);
    $('#chat-close').on('click', closeChat);
    $('#chat-back').on('click', leaveThread);

    $('#chat-user-list').on('click', '.chat-user-item', function () {
        var userId = $(this).attr('data-user-id');
        var name = $(this).attr('data-name');
        if (composeMode) {
            var index = activeRecipientIds.indexOf(String(userId));
            if (index === -1) {
                activeRecipientIds.push(String(userId));
            } else {
                activeRecipientIds.splice(index, 1);
            }
            $(this).toggleClass('selected', activeRecipientIds.indexOf(String(userId)) !== -1)
                .find('.chat-user-checkbox').prop('checked', activeRecipientIds.indexOf(String(userId)) !== -1);
            updateRecipientControls();
            return;
        }
        selectUser(userId, name);
    });

    $('#chat-select-all').on('change', function () {
        activeRecipientIds = this.checked ? chatUsersCache.map(function (user) {
            return String(user.id);
        }) : [];
        renderUsers(chatUsersCache);
    });

    $('#chat-select-role').on('change', function () {
        var role = $(this).val();
        if (!role) {
            return;
        }
        $.each(chatUsersCache, function (_, user) {
            if ($.trim(user.role || '') === role && activeRecipientIds.indexOf(String(user.id)) === -1) {
                activeRecipientIds.push(String(user.id));
            }
        });
        $(this).val('');
        renderUsers(chatUsersCache);
    });

    $('#chat-attach-btn').on('click', function () {
        $('#chat-attachment').trigger('click');
    });

    $('#chat-attachment').on('change', function () {
        selectAttachment(this.files && this.files[0]);
    });
    $('#chat-attach-clear').on('click', clearAttachment);

    $widget.on('dragenter dragover', function (e) {
        var originalEvent = e.originalEvent;
        if (!originalEvent.dataTransfer || !originalEvent.dataTransfer.types ||
            Array.prototype.indexOf.call(originalEvent.dataTransfer.types, 'Files') === -1) {
            return;
        }
        e.preventDefault();
        $widget.addClass('chat-dragover');
    });

    $widget.on('dragleave', function (e) {
        var originalEvent = e.originalEvent;
        var rect = this.getBoundingClientRect();
        if (originalEvent.clientX < rect.left || originalEvent.clientX >= rect.right ||
            originalEvent.clientY < rect.top || originalEvent.clientY >= rect.bottom) {
            $widget.removeClass('chat-dragover');
        }
    });

    $widget.on('drop', function (e) {
        e.preventDefault();
        $widget.removeClass('chat-dragover');
        if ($('#chat-thread-view').prop('hidden')) {
            return;
        }
        var files = e.originalEvent.dataTransfer.files;
        if (!files || files.length === 0) {
            return;
        }
        if (files.length > 1) {
            showError('Please drop one document at a time.');
            return;
        }
        $('#chat-attachment').val('');
        clearError();
        selectAttachment(files[0]);
    });

    $('#chat-new-message').on('click', function () {
        if (composeMode) {
            composeMode = false;
            activeRecipientIds = [];
            $(this)
                .text('Send to multiple')
                .attr({ title: 'Send a message to multiple people', 'aria-label': 'Send a message to multiple people' });
            $('#chat-bulk-controls').prop('hidden', true);
            $('#chat-compose-toolbar').prop('hidden', true);
            $('#chat-selected-count').prop('hidden', true);
            $('#chat-compose-continue').prop('hidden', true);
            $('#chat-select-all').prop('checked', false);
            $('#chat-select-role').val('');
            $('#chat-title').text('Messages');
            $('#chat-recipient-summary').text('').prop('hidden', true);
            renderUsers(chatUsersCache);
            return;
        }
        composeMode = true;
        activeRecipientIds = [];
        activeUserId = null;
        $('#chat-bulk-controls').prop('hidden', false);
        $('#chat-compose-toolbar').prop('hidden', false);
        $('#chat-selected-count').text('0 selected').prop('hidden', true);
        $('#chat-compose-continue').prop('hidden', true);
        $('#chat-select-all').prop('checked', false);
        $('#chat-select-role').val('');
        $(this)
            .text('Cancel')
            .attr({ title: 'Cancel recipient selection', 'aria-label': 'Cancel recipient selection' });
        $('#chat-title').text('Choose recipients');
        renderUsers(chatUsersCache);
    });

    $('#chat-compose-continue').on('click', function () {
        if (!activeRecipientIds.length) {
            return;
        }
        composeMode = false;
        activeUserId = activeRecipientIds.length === 1 ? activeRecipientIds[0] : null;
        activeName = activeRecipientIds.length === 1 ? $('[data-user-id="' + activeUserId + '"]').attr('data-name') || 'Message' : activeRecipientIds.length + ' recipients';
        $('#chat-bulk-controls').prop('hidden', true);
        $('#chat-select-all').prop('checked', false);
        $('#chat-select-role').val('');
        lastMessageId = 0;
        $('#chat-thread').empty();
        $('#chat-recipient-id').val(activeUserId || '');
        $('#chat-recipient-id').remove();
        $('.chat-recipient-input').remove();
        $.each(activeRecipientIds, function (_, recipientId) {
            $('<input>', { type: 'hidden', name: 'recipient_ids[]', 'class': 'chat-recipient-input' }).val(recipientId).appendTo('#chat-form');
        });
        var recipientNames = activeRecipientIds.map(function (recipientId) {
            var user = chatUsersCache.find(function (item) {
                return String(item.id) === recipientId;
            });
            return user ? (user.name || user.username || ('User ' + recipientId)) : ('User ' + recipientId);
        });
        $('#chat-recipient-summary')
            .text('To: ' + recipientNames.join(', '))
            .prop('hidden', false);
        clearError();
        showView('thread');
        $('#chat-body').trigger('focus');
    });

    $('#chat-body')
        .on('input', function () {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        })
        .on('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                $('#chat-form').trigger('submit');
            }
        });

    $('#chat-form').on('submit', function (e) {
        e.preventDefault();
        clearError();

        var body = $.trim($('#chat-body').val());
        var attachment = selectedAttachment;
        var hasFile = Boolean(attachment);
        if (attachment && attachment.size > maxAttachmentBytes) {
            showError('Documents can be at most 100 MB.');
            return;
        }
        if (activeRecipientIds.length === 0) {
            showError('Choose at least one recipient.');
            return;
        }
        if (!body && !hasFile) {
            showError('Write a message or attach a document.');
            return;
        }

        var $send = $('#chat-send').prop('disabled', true);
        var formData = new FormData(this);
        formData.delete('attachment');
        if (attachment) {
            formData.append('attachment', attachment, attachment.name);
            setUploadProgress(0, 'Preparing upload...');
        }
        $.ajax({
            url: baseUrl + '/send',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            xhr: function () {
                var xhr = $.ajaxSettings.xhr();
                if (xhr.upload && attachment) {
                    xhr.upload.addEventListener('progress', function (event) {
                        if (event.lengthComputable) {
                            var percent = Math.round(event.loaded / event.total * 100);
                            setUploadProgress(percent, 'Uploading... ' + percent + '%');
                        }
                    });
                    xhr.upload.addEventListener('load', function () {
                        setUploadProgress(100, 'Upload complete. Sending...');
                    });
                }
                return xhr;
            }
        })
            .done(function (res) {
                if (res.status !== 'success') {
                    showError(res.message || 'Could not send the message.');
                    return;
                }
                $('#chat-body').val('').css('height', '');
                clearAttachment();
                appendMessages(Array.isArray(res.value) ? [res.value[0]] : [res.value], true);
                loadConversations();
            })
            .fail(function () {
                showError('Could not send the message. Please try again.');
            })
            .always(function () {
                hideUploadProgress();
                $send.prop('disabled', false);
            });
    });

    loadConversations();
    sendHeartbeat();
    setInterval(sendHeartbeat, PRESENCE_INTERVAL_MS);
    setInterval(function () {
        if (isOpen) {
            loadConversations();
        }
    }, USER_LIST_INTERVAL_MS);
    setInterval(function () {
        if (isOpen && activeUserId !== null) {
            loadThread(false);
        }
    }, THREAD_SYNC_INTERVAL_MS);
    connectPush();
})(jQuery);
