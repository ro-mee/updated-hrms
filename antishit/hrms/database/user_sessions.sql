-- SQL for Session Device Management
-- Creates a table to track active user sessions

CREATE TABLE IF NOT EXISTS user_sessions (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id       INT UNSIGNED NOT NULL,
    session_id    VARCHAR(128) NOT NULL UNIQUE,
    ip_address    VARCHAR(45)  DEFAULT NULL,
    user_agent    TEXT DEFAULT NULL,
    device        VARCHAR(100) DEFAULT NULL,
    location      VARCHAR(150) DEFAULT NULL,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_session_user (user_id),
    INDEX idx_session_id   (session_id)
) ENGINE=InnoDB;
