-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 25, 2026 at 07:24 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
CREATE DATABASE IF NOT EXISTS `crud_sample`;
USE `crud_sample`;
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `email`, `created_at`) VALUES
(1, 'admin123', '$2a$12$cJKZkdJ6V.f4U61NRGQfCeY0tnm0YKGBrri68kwzMKLzv1xq7XvpC', 'admin@shop.com', '2026-04-20 11:15:30');

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `name`, `description`, `price`, `quantity`, `image`, `created_at`, `updated_at`) VALUES
(1, 'Gasoline chainsaw', '🔥 Gasoline 20 inches Chainsaw 3100W 🔥\r\n\r\nProduct Details:\r\n- 20 inches Chainsaw\r\n- Cylinders: 52 cc\r\n- Power: 5.2kw (7.1bhp)\r\n- Ignition: Electronic\r\n- Oil Tank: 0.32L\r\n- Fuel Tank: 0.68L (2T Mix)\r\n- Weight: 7.3 Kg (without bar and chain)\r\n- Bar Length: 20 inch (53cm)\r\n- Max RPM: 13,500 rpm\r\n\r\nTips: Use in accordance with product manual. Keep away from children.', 13999.00, 50, 'chainsaw.png', '2026-04-13 10:43:36', '2026-04-13 12:35:18'),
(2, 'Sandals for outdoors and vacation', 'COLOR: BLACK / WHITE\r\nVery comfortable to wear!\r\n\r\nAvailable sizes: 36 / 37 / 38 / 39 / 40 / 41 / 42 / 43 / 44 / 45', 149.99, 30, 'sandal.png', '2026-04-13 10:43:36', '2026-04-13 12:34:47'),
(3, 'White Socks Unisex', '100% Brand New & High Quality\r\nMaterial: Cotton Blend\r\nSize: One Size fits 38–45', 89.50, 20, 'socks.png', '2026-04-13 10:43:36', '2026-04-13 12:34:14'),
(4, 'Korean Sweater Stretchable', '-Size: M, L, XL,XXL\r\n\r\n\r\n\r\n-Material: stretchable cotton\r\n\r\n\r\n\r\n-Size : WAISTLINE\r\n\r\n-M (waistline25-27),  -length 24 -chest 18\r\n\r\n\r\n\r\n-L(waistline27-29)    -Length 25.5 Chest 19\r\n\r\n\r\n\r\n-XL(waistline29-30)   -length 26.5 Cheat 19.5\r\n\r\n\r\n\r\n-2XL(waistline 31-33)   -length 27.5 Chest 20', 299.00, 30, 'item_69dce12cf0416.png', '2026-04-13 12:27:24', '2026-04-13 12:34:07'),
(5, 'Anti UV Umbrella', '● In the bar material: steel and fiber\r\n\r\n\r\n\r\n●  Number of ribs: 8 bone\r\n\r\n\r\n\r\n●  Style: Four folds umbrella\r\n\r\n\r\n\r\n● Product introduction: UV protection, sun protection, comfort and comfort for travel', 99.00, 40, 'item_69dce2648437a.png', '2026-04-13 12:32:36', '2026-04-13 12:33:43'),
(6, 'Versace Eros 100ms', 'A captivating fragrance inspired by the Greek God of love, exuding sensuality and energy.', 5200.00, 32, 'item_69dce427cedee.png', '2026-04-13 12:40:07', '2026-04-13 12:40:07'),
(7, 'Sanrio Cinnamoroll alcohol', '🌟 Discover the Fresh Sanrio Cinnamoroll Alcospray! 🌟\r\n\r\n\r\n\r\n✨ Unmatched Germ Protection\r\n\r\n- Say goodbye to 99.99% of germs and bacteria with every spray! This powerful 500ml alcospray ensures your hands and body stay clean and safe.\r\n\r\n\r\n\r\n🌸 Fruity Floral Fragrance\r\n\r\n- Enjoy a delightful blend of fruity and floral notes that leaves a refreshing scent lingering on your skin. Perfect for those who love a sweet and fresh aroma!\r\n\r\n\r\n\r\n🌿 Gentle Care with Aloe Vera\r\n\r\n- Infused with soothing aloe vera extract, this spray is gentle on your skin, making it suitable for frequent use.\r\n\r\n\r\n\r\n🎨 Variation Available\r\n\r\n- Choose the FRESH Sanrio Cinnamoroll Fruity Floral Symphony 500ml [Alcohol] for a burst of freshness and protection.\r\n\r\n\r\n\r\n🔍 Additional Information\r\n\r\n- Please note, this product does not come with a warranty. Enjoy the benefits of cleanliness and fragrance without any worries!', 150.00, 67, 'item_69dce49daf00f.png', '2026-04-13 12:41:28', '2026-04-13 12:42:05'),
(8, 'Bandaid', '- Waterproof Bandages\r\n\r\n- Non-stick medicated with Benzalkonium chloride\r\n\r\n- To help protect wounds from bacteria\r\n\r\n- Strong adhesion\r\n\r\n- Improved strip material; color blending with natural skin tone\r\n\r\n\r\n\r\nPRODUCT DESCRIPTION\r\n\r\nBand-Aid Brand Adhesive Bandages has a non-stick medicated pad with Benzalkonium chloride to help protect wounds from bacteria. It is waterproof and provides strong adhesion.\r\n\r\n\r\n\r\nDIRECTIONS FOR USE\r\n\r\nClean area thoroughly with soap and water then dry. Apply bandage. Change dressing daily. Change bandage if pad becomes wet.\r\n\r\n\r\n\r\nFORMULATION\r\n\r\nEach pad contains Benzalkonium Cholride Solution I.P. equivalent to Benzalkonium Chloride 0.5% W/W, Polyethylene Backing Film, Siliconised Release Paper, Hotmelt Adhesive\r\n\r\n\r\n\r\nABOUT THE BRAND\r\n\r\nBand-Aid Brand Adhesive Bandages have covered and protected cuts and scrapes for millions of people for over 90 years. We are committed to providing high-quality and innovative products for you and your family. \r\n\r\n\r\n\r\nWARNINGS & PRECAUTIONS\r\n\r\nCaution: the packaging of this product contains natural rubber latex which may cause allergic reaction\r\n\r\n -----------------------------------------\r\n\r\nKENVUE OFFICIAL STORE\r\n\r\n\r\n\r\n- Our iconic brands have a new home where thoughtful everyday care meets scientific precision. As a global consumer health leader, Kenvue touches the lives of more than a billion people across the globe.\r\n\r\n- Our iconic brands: Aveeno Body, Aveeno Baby, Neutrogena, Johnson’s, Listerine, Modess, Carefree, Clean & Clear, Bactidol, Band-Aid, Tylenol, Benadryl, Imodium, Combantrin, Visine, Nicorette, Sinutab, Trosyd\r\n\r\n \r\n\r\nRealize the extraordinary power of everyday care: Our purpose is clear. We believe that when people can count on care every day, it not only makes them well, it makes them whole—empowering them to engage more fully with the people, experiences and world around them.\r\n\r\n\r\n\r\nHere at the Kenvue Official Store, enjoy exclusive deals on your favorite Kenvue products and shop whenever it’s convenient, wherever you are.', 89.00, 103, 'item_69dce6bde5004.png', '2026-04-13 12:51:09', '2026-04-13 12:51:09');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'hello', '$2y$10$1uPQKaGofDEvIOuOOs.lfeLGr2KWgOqPlnnf3vtDszeSuTHB/y20W', '2026-04-22 07:28:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
