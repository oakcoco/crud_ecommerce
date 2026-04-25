-- ============================================================
-- Database: crud_sample
-- Description: Schema for Sample CRUD + Shopping Cart App
-- ============================================================

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

-- ------------------------------------------------------------
-- Sample Data (optional — remove if not needed)
-- ------------------------------------------------------------
INSERT INTO `items` (`name`, `description`, `price`, `quantity`, `image`) VALUES
('Chainsaw', '🔥   Gasoline 20 inches Chainsaw  3100W🔥



Product Details:

20 inches Chainsaw Set of Package

None Fill Cylinders: 52 cc

Power Machines:5.2kw (7.1bhp)

Ignition system:Electronic Chain

Oil Tank Capacity: 0.32L tank

Capacity: 0.68L Fuel : Bsn Mix + Oil 2 T

Oil mixture 2 Tak: 1L

Oil: Gasoline 50L

Whole Machine Weight: 7.3 Kg (without bar and chain)

Long Bar Max:20 inch (53cm)

Max speed. Machine Bar & Chain: 13,500 rpm

Vibration levels left / right: 7.0 / 7.0 m / s ² Saws"





Tips:

Be alert to potential security risks. Use these tools in accordance with the product manual and take the necessary personal safety precautions. Stay away from children and avoid injury! 
', 13999, 50, ''),
('Sandals', 'COLOR: BLACK WHITE
Very comfortable to wear!

Available sizes: 36/37/38/39/40/41/42/43/44/45', 149.99, 30, ''),
('Socks', '100% Brand New & High Quality
Material: Cotton Blend
Size: One Size 38-45', 89.50,  20, '');
