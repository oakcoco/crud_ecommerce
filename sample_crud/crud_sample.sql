
-- phpMyAdmin SQL Dump 
-- version 5.2.1 
-- https://www.phpmyadmin.net/ 
-- 
-- Host: 127.0.0.1 
-- Generation Time: Apr 13, 2026 at 03:26 PM 
-- Server version: 10.4.32-MariaDB 
-- PHP Version: 8.2.12

CREATE DATABASE IF NOT EXISTS `crud_sample`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `crud_sample`;

-- ------------------------------------------------------------
-- Table: items
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `items` (
  `id`          INT(11)        NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(255)   NOT NULL,
  `description` TEXT           DEFAULT NULL,
  `price`       DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
  `quantity`    INT(11)        NOT NULL DEFAULT 0,
  `image`       VARCHAR(255)   DEFAULT '',
  `created_at`  TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;


INSERT INTO `items` (`name`, `description`, `price`, `quantity`, `image`) VALUES
(
  'Chainsaw',
  '🔥 Gasoline 20 inches Chainsaw 3100W 🔥\n\nProduct Details:\n- 20 inches Chainsaw\n- Cylinders: 52 cc\n- Power: 5.2kw (7.1bhp)\n- Ignition: Electronic\n- Oil Tank: 0.32L\n- Fuel Tank: 0.68L (2T Mix)\n- Weight: 7.3 Kg (without bar and chain)\n- Bar Length: 20 inch (53cm)\n- Max RPM: 13,500 rpm\n\nTips: Use in accordance with product manual. Keep away from children.',
  13999.00,
  50,
  'chainsaw.png'
),
(
  'Sandals',
  'COLOR: BLACK / WHITE\nVery comfortable to wear!\n\nAvailable sizes: 36 / 37 / 38 / 39 / 40 / 41 / 42 / 43 / 44 / 45',
  149.99,
  30,
  'sandal.png'
),
(
  'Socks',
  '100% Brand New & High Quality\nMaterial: Cotton Blend\nSize: One Size fits 38–45',
  89.50,
  20,
  'socks.png'
);
