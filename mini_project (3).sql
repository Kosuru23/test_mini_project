-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 04, 2026 at 08:37 AM
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
-- Database: `mini_project`
--
CREATE DATABASE IF NOT EXISTS `mini_project` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `mini_project`;

DELIMITER $$
--
-- Functions
--
DROP FUNCTION IF EXISTS `GetOrderTotal`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `GetOrderTotal` (`target_order_id` INT) RETURNS DECIMAL(10,2) DETERMINISTIC READS SQL DATA BEGIN
    DECLARE total_sum DECIMAL(10,2);
    
    -- Calculate the sum of all items belonging to this order
    -- Math: (quantity * price_at_purchase) for every row
    SELECT SUM(quantity * price_at_purchase) 
    INTO total_sum
    FROM order_items 
    WHERE order_id = target_order_id;
    
    -- COALESCE ensures that if there are 0 items, it returns 0.00 instead of NULL
    RETURN COALESCE(total_sum, 0.00);
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

DROP TABLE IF EXISTS `country`;
CREATE TABLE `country` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `country`
--

INSERT INTO `country` (`id`, `name`) VALUES
(1, 'France'),
(2, 'USA'),
(3, 'Italy');

-- --------------------------------------------------------

--
-- Table structure for table `grape_variety`
--

DROP TABLE IF EXISTS `grape_variety`;
CREATE TABLE `grape_variety` (
  `id` int(11) NOT NULL,
  `variety_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grape_variety`
--

INSERT INTO `grape_variety` (`id`, `variety_name`) VALUES
(19, 'Barbera'),
(1, 'Cabernet Sauvignon'),
(15, 'Carmenere'),
(6, 'Chardonnay'),
(20, 'Chenin Blanc'),
(17, 'Gewürztraminer'),
(12, 'Grenache'),
(10, 'Malbec'),
(2, 'Merlot'),
(18, 'Muscat'),
(13, 'Nebbiolo'),
(3, 'Pinot Noir'),
(8, 'Riesling'),
(14, 'Sangiovese'),
(7, 'Sauvignon Blanc'),
(5, 'Shiraz'),
(4, 'Syrah'),
(11, 'Tempranillo'),
(16, 'Viognier'),
(9, 'Zinfandel');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_status` int(11) NOT NULL,
  `payment_method` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `buyer_id`, `total_amount`, `order_status`, `payment_method`, `created_at`) VALUES
(1, 7, 53.30, 1, 5, '2026-01-04 12:30:20'),
(2, 7, 35561.30, 1, 5, '2026-01-04 12:30:38'),
(3, 7, 106.60, 1, 5, '2026-01-04 12:33:56'),
(4, 7, 35452.00, 1, 5, '2026-01-04 12:34:11'),
(6, 7, 106.60, 1, 5, '2026-01-04 14:02:45'),
(8, 7, 53.30, 1, 5, '2026-01-04 14:05:54'),
(9, 7, 53.30, 1, 5, '2026-01-04 14:06:04'),
(11, 7, 56.00, 1, 5, '2026-01-04 14:11:20'),
(12, 7, 112.00, 1, 5, '2026-01-04 14:11:49'),
(13, 7, 56.00, 1, 5, '2026-01-04 14:12:06'),
(14, 7, 168.00, 1, 5, '2026-01-04 14:13:52'),
(15, 7, 26024.00, 1, 5, '2026-01-04 14:15:17'),
(16, 7, 3330.00, 1, 5, '2026-01-04 14:19:35'),
(17, 7, 1110.00, 1, 5, '2026-01-04 14:23:18'),
(18, 10, 3253.00, 1, 5, '2026-01-04 14:41:50'),
(19, 10, 36475.00, 1, 5, '2026-01-04 14:52:50'),
(20, 10, 72950.00, 1, 5, '2026-01-04 14:57:30'),
(21, 10, 36475.00, 1, 5, '2026-01-04 15:04:29'),
(22, 10, 36475.00, 1, 5, '2026-01-04 15:05:10'),
(23, 10, 1110.00, 1, 5, '2026-01-04 15:06:29'),
(24, 10, 1110.00, 1, 5, '2026-01-04 15:11:26'),
(25, 10, 1110.00, 1, 5, '2026-01-04 15:13:08'),
(26, 10, 1110.00, 1, 5, '2026-01-04 15:14:37'),
(27, 10, 1110.00, 1, 5, '2026-01-04 15:15:37'),
(28, 10, 54.00, 1, 5, '2026-01-04 15:16:10'),
(29, 10, 35558.60, 1, 5, '2026-01-04 15:16:50'),
(30, 10, 53.30, 1, 5, '2026-01-04 15:18:00');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_purchase` decimal(10,2) NOT NULL,
  `wine_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `quantity`, `price_at_purchase`, `wine_id`) VALUES
(1, 1, 1, 53.30, 39),
(2, 2, 1, 53.30, 39),
(3, 2, 1, 35452.00, 40),
(4, 2, 1, 56.00, 37),
(5, 3, 2, 53.30, 39),
(6, 4, 1, 35452.00, 40),
(7, 6, 2, 53.30, 39),
(8, 8, 1, 53.30, 39),
(9, 9, 1, 53.30, 39),
(10, 11, 1, 56.00, 37),
(11, 12, 2, 56.00, 37),
(12, 13, 1, 56.00, 37),
(13, 14, 3, 56.00, 37),
(14, 15, 8, 3253.00, 34),
(15, 16, 3, 1110.00, 28),
(16, 17, 1, 1110.00, 28),
(17, 18, 1, 3253.00, 34),
(18, 19, 1, 36475.00, 32),
(19, 20, 2, 36475.00, 32),
(20, 21, 1, 36475.00, 32),
(21, 22, 1, 36475.00, 32),
(22, 23, 1, 1110.00, 28),
(23, 24, 1, 1110.00, 28),
(24, 25, 1, 1110.00, 28),
(25, 26, 1, 1110.00, 28),
(26, 27, 1, 1110.00, 28),
(27, 28, 1, 54.00, 35),
(28, 29, 1, 35452.00, 40),
(29, 29, 2, 53.30, 39),
(30, 30, 1, 53.30, 39);

--
-- Triggers `order_items`
--
DROP TRIGGER IF EXISTS `after_order_item_delete`;
DELIMITER $$
CREATE TRIGGER `after_order_item_delete` AFTER DELETE ON `order_items` FOR EACH ROW BEGIN
    -- Automatically add the quantity back if the item is removed
    UPDATE wines 
    SET quantity = quantity + OLD.quantity 
    WHERE wine_id = OLD.wine_id;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `after_order_item_insert`;
DELIMITER $$
CREATE TRIGGER `after_order_item_insert` AFTER INSERT ON `order_items` FOR EACH ROW BEGIN
    -- Automatically subtract the quantity from the wines table
    UPDATE wines 
    SET quantity = quantity - NEW.quantity 
    WHERE wine_id = NEW.wine_id;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `update_order_total_after_item_insert`;
DELIMITER $$
CREATE TRIGGER `update_order_total_after_item_insert` AFTER INSERT ON `order_items` FOR EACH ROW BEGIN
    -- Update the orders table using our stored function
    UPDATE orders 
    SET total_amount = GetOrderTotal(NEW.order_id) 
    WHERE order_id = NEW.order_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `order_status`
--

DROP TABLE IF EXISTS `order_status`;
CREATE TABLE `order_status` (
  `status_id` int(11) NOT NULL,
  `status_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_status`
--

INSERT INTO `order_status` (`status_id`, `status_name`) VALUES
(1, 'Pending'),
(2, 'Processing'),
(3, 'Shipped'),
(4, 'Delivered'),
(5, 'Cancelled'),
(6, 'Refunded');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_provider` int(11) DEFAULT NULL,
  `payment_status` int(11) NOT NULL,
  `paid_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_method`
--

DROP TABLE IF EXISTS `payment_method`;
CREATE TABLE `payment_method` (
  `method_id` int(11) NOT NULL,
  `method_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_method`
--

INSERT INTO `payment_method` (`method_id`, `method_name`) VALUES
(1, 'Credit Card'),
(2, 'Debit Card'),
(3, 'PayPal'),
(4, 'Bank Transfer'),
(5, 'Cash on Delivery');

-- --------------------------------------------------------

--
-- Table structure for table `payment_provider`
--

DROP TABLE IF EXISTS `payment_provider`;
CREATE TABLE `payment_provider` (
  `provider_id` int(11) NOT NULL,
  `provider_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_provider`
--

INSERT INTO `payment_provider` (`provider_id`, `provider_name`) VALUES
(1, 'Stripe'),
(2, 'PayPal'),
(3, 'Square'),
(4, 'Authorize.Net'),
(5, 'Braintree');

-- --------------------------------------------------------

--
-- Table structure for table `payment_status`
--

DROP TABLE IF EXISTS `payment_status`;
CREATE TABLE `payment_status` (
  `status_id` int(11) NOT NULL,
  `status_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_status`
--

INSERT INTO `payment_status` (`status_id`, `status_name`) VALUES
(1, 'Pending'),
(2, 'Completed'),
(3, 'Failed'),
(4, 'Refunded'),
(5, 'Cancelled');

-- --------------------------------------------------------

--
-- Table structure for table `shipping`
--

DROP TABLE IF EXISTS `shipping`;
CREATE TABLE `shipping` (
  `shipping_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `country` varchar(150) NOT NULL,
  `tracking_number` varchar(150) DEFAULT NULL,
  `carrier` varchar(150) DEFAULT NULL,
  `shipping_status` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(50) DEFAULT NULL,
  `user_type_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `middle_name`, `last_name`, `email`, `password`, `phone_number`, `user_type_id`, `created_at`) VALUES
(2, 'Neil Jam', NULL, 'Juson', 'faculty@wmsu.com', '$2y$10$YckK9xtHNr/47mdfAgkPSeFe45UBlM7xxZ/tEXR2y3Qgj00mDXLi6', NULL, 2, '2026-01-04 09:43:41'),
(3, 'Dap', NULL, 'Juson', 'dapp@wmsu.com', '$2y$10$cQdIBhfx7tmEFP2cLcpB8eHogCj6F0tKLkEVTgtnYLtSNH.HSiVJ6', NULL, 2, '2026-01-04 09:49:50'),
(7, 'Neil Jam', '', 'Juson', 'gui@wmsu.com', '$2y$10$LmT8xS9XW2goIc1LfAWRXO0vgf0OfUoYQrVAIwDUXZe87h/HDasw2', '', 2, '2026-01-04 10:18:59'),
(8, 'Neil Jam', '', 'Juson', 'jusonneiljam@gmail.com', '$2y$10$5vp2.zzTCn3SuLDkEsaUzOztfEJkqAE3lVCIZk5HuC1YZoLGdHsMy', '', 1, '2026-01-04 10:40:31'),
(9, 'Neil Jam', '', 'Juson', 'neil@wmsu.com', '$2y$10$RAZh9TxRZtA1a/7/WpDTgO0YFlDJdjQtx7MnTZvOkxKn.yLVErHc6', '', 1, '2026-01-04 14:32:45'),
(10, 'gERALDINE', '', 'dsd', 'neil@wmsuedu.com', '$2y$10$INCB26H1iK9eCZst5JhGE.hns93p7EF95reL/wK342ijHwBm3wLCa', '', 2, '2026-01-04 14:33:50');

-- --------------------------------------------------------

--
-- Table structure for table `user_type`
--

DROP TABLE IF EXISTS `user_type`;
CREATE TABLE `user_type` (
  `user_type_id` int(11) NOT NULL,
  `user_type_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_type`
--

INSERT INTO `user_type` (`user_type_id`, `user_type_name`) VALUES
(1, 'admin'),
(2, 'user');

-- --------------------------------------------------------

--
-- Table structure for table `wines`
--

DROP TABLE IF EXISTS `wines`;
CREATE TABLE `wines` (
  `wine_id` int(11) NOT NULL,
  `wine_name` varchar(100) NOT NULL,
  `wine_type` int(11) NOT NULL,
  `grape_variety` int(11) NOT NULL,
  `region` varchar(100) NOT NULL,
  `country` int(11) NOT NULL,
  `alcohol_percentage` decimal(5,2) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `description` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ;

--
-- Dumping data for table `wines`
--

INSERT INTO `wines` (`wine_id`, `wine_name`, `wine_type`, `grape_variety`, `region`, `country`, `alcohol_percentage`, `quantity`, `price`, `description`, `image_url`, `created_at`) VALUES
(28, 'weqdqw', 2, 4, 'sfsddf23', 2, 1.30, 0, 1110.00, 'cascacacascasa', '1767440425_695900296b616.png', '2026-01-03 00:00:00'),
(32, 'ddasdas', 2, 4, 'dsfdsfs', 3, 2.13, 0, 36475.00, 'dsaddfdasas', '1767451906_69592d02bdba0.png', '2026-01-03 00:00:00'),
(34, 'dfds', 3, 3, 'ddsdssd', 2, 3.10, 0, 3253.00, 'fdgdfffs', '1767494549_6959d3956da03.png', '2026-01-03 00:00:00'),
(35, 'weqdqw', 3, 4, 'fgfgddf', 2, 5.60, 34, 54.00, 'dfsfsfs', '1767455912_69593ca84189c.png', '2026-01-03 00:00:00'),
(37, 'weqdqw', 16, 6, 'dsfdsfs', 1, 3.50, 28, 56.00, 'ffsfsdfssfs', '1767485268_6959af5468f74.png', '2026-01-04 00:00:00'),
(39, 'weqdqw', 6, 6, 'dsfsd', 1, 23.50, 22, 53.30, 'dsdsfsdfsd', '1767486125_6959b2ad08457.png', '2026-01-04 00:00:00'),
(40, 'sfdishisd', 11, 15, 'dsfsd', 3, 5.23, 351, 35452.00, 'dsfd', '1767494557_6959d39d3fec0.png', '2026-01-04 09:08:05');

-- --------------------------------------------------------

--
-- Table structure for table `wine_type`
--

DROP TABLE IF EXISTS `wine_type`;
CREATE TABLE `wine_type` (
  `id` int(11) NOT NULL,
  `wine_type_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wine_type`
--

INSERT INTO `wine_type` (`id`, `wine_type_name`) VALUES
(16, 'Carbonated Wine'),
(5, 'Dessert Wine'),
(11, 'Dry Wine'),
(6, 'Fortified Wine'),
(13, 'Ice Wine'),
(14, 'Late Harvest Wine'),
(9, 'Natural Wine'),
(15, 'Orange Wine'),
(8, 'Organic Wine'),
(1, 'Red Wine'),
(3, 'Rosé Wine'),
(12, 'Semi-Dry Wine'),
(4, 'Sparkling Wine'),
(7, 'Still Wine'),
(10, 'Sweet Wine'),
(2, 'White Wine');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `country`
--
ALTER TABLE `country`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `grape_variety`
--
ALTER TABLE `grape_variety`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `variety_name` (`variety_name`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `buyer_id` (`buyer_id`),
  ADD KEY `orders_ibfk_2` (`order_status`),
  ADD KEY `orders_ibfk_3` (`payment_method`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `wine_id` (`wine_id`);

--
-- Indexes for table `order_status`
--
ALTER TABLE `order_status`
  ADD PRIMARY KEY (`status_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `payments_ibfk_2` (`payment_provider`),
  ADD KEY `payments_ibfk_3` (`payment_status`);

--
-- Indexes for table `payment_method`
--
ALTER TABLE `payment_method`
  ADD PRIMARY KEY (`method_id`);

--
-- Indexes for table `payment_provider`
--
ALTER TABLE `payment_provider`
  ADD PRIMARY KEY (`provider_id`);

--
-- Indexes for table `payment_status`
--
ALTER TABLE `payment_status`
  ADD PRIMARY KEY (`status_id`);

--
-- Indexes for table `shipping`
--
ALTER TABLE `shipping`
  ADD PRIMARY KEY (`shipping_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `user_type_id` (`user_type_id`);

--
-- Indexes for table `user_type`
--
ALTER TABLE `user_type`
  ADD PRIMARY KEY (`user_type_id`);

--
-- Indexes for table `wines`
--
ALTER TABLE `wines`
  ADD PRIMARY KEY (`wine_id`),
  ADD KEY `country` (`country`),
  ADD KEY `wine_type` (`wine_type`),
  ADD KEY `grape_variety` (`grape_variety`);

--
-- Indexes for table `wine_type`
--
ALTER TABLE `wine_type`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `wine_type_name` (`wine_type_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `country`
--
ALTER TABLE `country`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `grape_variety`
--
ALTER TABLE `grape_variety`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `order_status`
--
ALTER TABLE `order_status`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_method`
--
ALTER TABLE `payment_method`
  MODIFY `method_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payment_provider`
--
ALTER TABLE `payment_provider`
  MODIFY `provider_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payment_status`
--
ALTER TABLE `payment_status`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `shipping`
--
ALTER TABLE `shipping`
  MODIFY `shipping_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user_type`
--
ALTER TABLE `user_type`
  MODIFY `user_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `wines`
--
ALTER TABLE `wines`
  MODIFY `wine_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wine_type`
--
ALTER TABLE `wine_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`order_status`) REFERENCES `order_status` (`status_id`),
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`payment_method`) REFERENCES `payment_method` (`method_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`payment_provider`) REFERENCES `payment_provider` (`provider_id`),
  ADD CONSTRAINT `payments_ibfk_3` FOREIGN KEY (`payment_status`) REFERENCES `payment_status` (`status_id`);

--
-- Constraints for table `shipping`
--
ALTER TABLE `shipping`
  ADD CONSTRAINT `shipping_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`user_type_id`) REFERENCES `user_type` (`user_type_id`) ON DELETE CASCADE;

--
-- Constraints for table `wines`
--
ALTER TABLE `wines`
  ADD CONSTRAINT `wines_ibfk_1` FOREIGN KEY (`country`) REFERENCES `country` (`id`),
  ADD CONSTRAINT `wines_ibfk_2` FOREIGN KEY (`wine_type`) REFERENCES `wine_type` (`id`),
  ADD CONSTRAINT `wines_ibfk_3` FOREIGN KEY (`grape_variety`) REFERENCES `grape_variety` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
