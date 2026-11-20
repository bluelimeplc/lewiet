CREATE TABLE IF NOT EXISTS `chat_message` (
    `id` int NOT NULL AUTO_INCREMENT,
    `sender_id` int NOT NULL,
    `recipient_id` int NOT NULL,
    `body` text NULL,
    `attachment_name` varchar(255) NULL,
    `attachment_path` varchar(500) NULL,
    `attachment_size` int NULL,
    `read_at` datetime NULL,
    `created_at` datetime NULL,
    `updated_at` datetime NULL,
    PRIMARY KEY (`id`),
    KEY `sender_recipient` (`sender_id`, `recipient_id`),
    KEY `recipient_sender_read` (`recipient_id`, `sender_id`, `read_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `chat_presence` (
    `user_id` int NOT NULL,
    `last_seen_at` datetime NOT NULL,
    PRIMARY KEY (`user_id`),
    KEY `last_seen_at` (`last_seen_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;