-- Torres Vehicle Rental — Database Schema
-- Import this once via phpMyAdmin (XAMPP) or the mysql CLI:
--   mysql -u root -p < schema.sql
-- Safe to re-import — every statement uses IF NOT EXISTS.

CREATE DATABASE IF NOT EXISTS torres_rental
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE torres_rental;

-- Customer accounts.
CREATE TABLE IF NOT EXISTS users (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(120)    NOT NULL,
    email           VARCHAR(190)    NOT NULL UNIQUE,
    password_hash   VARCHAR(255)    NOT NULL,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Reservations submitted through the "Rent Now" / "Reserve Now" modal.
-- Each reservation belongs to the logged-in customer who made it. A
-- customer can cancel their own reservation any time via the "My
-- Reservations" page — cancelling sets status to 'cancelled' rather
-- than deleting the row, so the booking history is kept.
CREATE TABLE IF NOT EXISTS reservations (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id       INT UNSIGNED    NOT NULL,
    name          VARCHAR(120)    NOT NULL,
    phone         VARCHAR(30)     NOT NULL,
    email         VARCHAR(190)    NOT NULL,
    car           VARCHAR(120)    NOT NULL DEFAULT 'Not specified',
    color         VARCHAR(60)     NOT NULL DEFAULT '',
    location      VARCHAR(60)     NOT NULL DEFAULT '',
    pickup_date   DATE            NULL,
    pickup_time   TIME            NULL,
    return_date   DATE            NULL,
    return_time   TIME            NULL,
    status        ENUM('confirmed', 'cancelled') NOT NULL DEFAULT 'confirmed',
    created_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    cancelled_at  DATETIME        NULL,
    CONSTRAINT fk_reservations_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- Adds the columns above to a `reservations` table created before this
-- update. Each column is only added if it's missing, using a dynamic-SQL
-- check against INFORMATION_SCHEMA instead of "ADD COLUMN IF NOT EXISTS"
-- — that shorthand needs MySQL 8.0.29+ / MariaDB 10.0+, and silently (or
-- loudly) fails on older versions, which is how a live site can end up
-- missing `status`/`cancelled_at` even after "successfully" importing
-- this file. This version works on any MySQL 5.6+ / MariaDB version.
DELIMITER $$
CREATE PROCEDURE torres_add_column_if_missing(
    IN p_table VARCHAR(64), IN p_column VARCHAR(64), IN p_definition VARCHAR(255)
)
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = p_table AND COLUMN_NAME = p_column
    ) THEN
        SET @ddl = CONCAT('ALTER TABLE ', p_table, ' ADD COLUMN ', p_column, ' ', p_definition);
        PREPARE stmt FROM @ddl;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END$$
DELIMITER ;

CALL torres_add_column_if_missing('reservations', 'color', "VARCHAR(60) NOT NULL DEFAULT '' AFTER car");
CALL torres_add_column_if_missing('reservations', 'location', "VARCHAR(60) NOT NULL DEFAULT '' AFTER color");
CALL torres_add_column_if_missing('reservations', 'pickup_date', "DATE NULL AFTER location");
CALL torres_add_column_if_missing('reservations', 'pickup_time', "TIME NULL AFTER pickup_date");
CALL torres_add_column_if_missing('reservations', 'return_date', "DATE NULL AFTER pickup_time");
CALL torres_add_column_if_missing('reservations', 'return_time', "TIME NULL AFTER return_date");
CALL torres_add_column_if_missing('reservations', 'status', "ENUM('confirmed', 'cancelled') NOT NULL DEFAULT 'confirmed' AFTER return_time");
CALL torres_add_column_if_missing('reservations', 'cancelled_at', "DATETIME NULL AFTER created_at");

DROP PROCEDURE torres_add_column_if_missing;

-- Newsletter subscribers submitted through the footer form.
CREATE TABLE IF NOT EXISTS subscribers (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email           VARCHAR(190)    NOT NULL UNIQUE,
    subscribed_at   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Messages submitted through the Contact page's form.
CREATE TABLE IF NOT EXISTS messages (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(120)    NOT NULL,
    email       VARCHAR(190)    NOT NULL,
    subject     VARCHAR(190)    NOT NULL,
    message     TEXT            NOT NULL,
    created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Staff accounts. Separate from `users` (customers) — logging in here
-- does not log you in as a customer and vice versa. Create the first
-- admin with create_admin.php (delete that file afterward).
CREATE TABLE IF NOT EXISTS admins (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username        VARCHAR(60)     NOT NULL UNIQUE,
    password_hash   VARCHAR(255)    NOT NULL,
    created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Per-car, per-color stock status. car_slug matches car_slug($name)
-- from data.php (e.g. "Toyota Vios" -> "toyota-vios"). Only admin.php
-- (which requires an admin login) ever writes to this table — the
-- public site only reads from it.
CREATE TABLE IF NOT EXISTS vehicle_colors (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    car_slug    VARCHAR(150)    NOT NULL,
    color_name  VARCHAR(60)     NOT NULL,
    in_stock    TINYINT(1)      NOT NULL DEFAULT 1,
    updated_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_car_color (car_slug, color_name)
) ENGINE=InnoDB;

-- Seed every car/color from data.php as in-stock by default. Safe to
-- re-run: INSERT IGNORE skips rows that already exist.
INSERT IGNORE INTO vehicle_colors (car_slug, color_name, in_stock) VALUES
    ('toyota-vios', 'Red', 1),
    ('toyota-vios', 'Black', 1),
    ('toyota-vios', 'Blue', 1),
    ('toyota-vios', 'Grey', 1),
    ('toyota-vios', 'White', 1),
    ('toyota-innova', 'Silver', 1),
    ('toyota-innova', 'Black', 1),
    ('toyota-innova', 'Maroon', 1),
    ('fortuner', 'Black', 1),
    ('fortuner', 'Blue', 1),
    ('hiace-commuter', 'White', 1),
    ('hiace-commuter', 'Red', 1),
    ('hiace-commuter', 'Off-White', 1),
    ('honda-civic', 'Black', 1),
    ('honda-civic', 'Blue', 1),
    ('mitsubishi-xpander', 'White', 1),
    ('mitsubishi-xpander', 'Black', 1),
    ('mitsubishi-xpander', 'Red', 1),
    ('mitsubishi-xpander', 'Orange', 1),
    ('isuzu-d-max', 'White', 1),
    ('isuzu-d-max', 'Blue', 1),
    ('isuzu-d-max', 'Grey', 1),
    ('isuzu-d-max', 'Red', 1);