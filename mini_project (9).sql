-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 05, 2026 at 03:52 AM
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

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `assign_tracking_number` (IN `p_shipping_id` INT)   BEGIN
    DECLARE v_tracking VARCHAR(50);

    -- Only generate if tracking number is NULL
    IF NOT EXISTS (
        SELECT 1 FROM shipping
        WHERE shipping_id = p_shipping_id
          AND tracking_number IS NOT NULL
    ) THEN

        SET v_tracking = CONCAT(
            'SHIP-',
            DATE_FORMAT(NOW(), '%Y%m%d'),
            '-',
            LPAD(p_shipping_id, 6, '0'),
            '-',
            UPPER(SUBSTRING(MD5(RAND()), 1, 6))
        );

        UPDATE shipping
        SET tracking_number = v_tracking
        WHERE shipping_id = p_shipping_id;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `seed_wines_random_realistic` (IN `total` INT)   BEGIN
    DECLARE i INT DEFAULT 1;

    WHILE i <= total DO
        INSERT INTO wines
        (wine_name, wine_type, grape_variety, region, country, alcohol_percentage, quantity, price, description)
        VALUES (
            -- Wine Name: random grape + random type + 2 letters
            CONCAT(
                (SELECT variety_name FROM grape_variety ORDER BY RAND() LIMIT 1),
                ' ',
                (SELECT wine_type_name FROM wine_type ORDER BY RAND() LIMIT 1),
                ' ',
                CHAR(FLOOR(65 + RAND()*26)), CHAR(FLOOR(65 + RAND()*26))
            ),
            (SELECT id FROM wine_type ORDER BY RAND() LIMIT 1),   -- Random wine type
            (SELECT id FROM grape_variety ORDER BY RAND() LIMIT 1), -- Random grape variety
            -- Random region
            CASE FLOOR(1 + RAND()*7)
                WHEN 1 THEN 'Napa Valley'
                WHEN 2 THEN 'Bordeaux'
                WHEN 3 THEN 'Tuscany'
                WHEN 4 THEN 'Burgundy'
                WHEN 5 THEN 'Rioja'
                WHEN 6 THEN 'Marlborough'
                WHEN 7 THEN 'Barossa Valley'
            END,
            (SELECT id FROM country ORDER BY RAND() LIMIT 1),     -- Random country
            ROUND(10 + RAND()*5, 2),     -- Alcohol percentage 10-15%
            FLOOR(RAND()*200),           -- Quantity 0-199
            ROUND(500 + RAND()*1000, 2), -- Price 500-1500
            CONCAT('A fine ',
                (SELECT variety_name FROM grape_variety ORDER BY RAND() LIMIT 1),
                ' wine from ',
                CASE FLOOR(1 + RAND()*7)
                    WHEN 1 THEN 'Napa Valley'
                    WHEN 2 THEN 'Bordeaux'
                    WHEN 3 THEN 'Tuscany'
                    WHEN 4 THEN 'Burgundy'
                    WHEN 5 THEN 'Rioja'
                    WHEN 6 THEN 'Marlborough'
                    WHEN 7 THEN 'Barossa Valley'
                END,
                '. Delicious and aromatic.'
            )
        );
        SET i = i + 1;
    END WHILE;
END$$

--
-- Functions
--
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
-- Table structure for table `carrier`
--

CREATE TABLE `carrier` (
  `carrier_id` int(11) NOT NULL,
  `carrier_name` varchar(100) NOT NULL,
  `tracking_prefix` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carrier`
--

INSERT INTO `carrier` (`carrier_id`, `carrier_name`, `tracking_prefix`, `is_active`, `created_at`) VALUES
(1, 'LBC', 'LBC', 1, '2026-01-04 17:50:10'),
(2, 'J&T Express', 'JNT', 1, '2026-01-04 17:50:10'),
(3, 'DHL', 'DHL', 1, '2026-01-04 17:50:10'),
(4, 'FedEx', 'FDX', 1, '2026-01-04 17:50:10'),
(5, 'UPS', 'UPS', 1, '2026-01-04 17:50:10');

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

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
-- Stand-in structure for view `dashboard_stats_summary`
-- (See below for the actual view)
--
CREATE TABLE `dashboard_stats_summary` (
`total_wines` bigint(21)
,`total_revenue` decimal(32,2)
,`low_stock` bigint(21)
,`total_orders` bigint(21)
);

-- --------------------------------------------------------

--
-- Table structure for table `grape_variety`
--

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
-- Stand-in structure for view `low_stock_view`
-- (See below for the actual view)
--
CREATE TABLE `low_stock_view` (
`low_stock` bigint(21)
);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

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
(48, 10, 1110.00, 3, 5, '2026-01-04 18:42:59'),
(49, 10, 35505.30, 4, 4, '2026-01-04 18:52:20'),
(52, 10, 53.30, 2, 4, '2026-01-04 19:32:46'),
(53, 10, 54.00, 2, 4, '2026-01-04 23:11:01'),
(57, 10, 53.30, 1, 5, '2026-01-05 02:23:50'),
(60, 10, 5453.00, 4, 2, '2026-01-05 02:26:02'),
(62, 10, 9541.00, 1, 5, '2026-01-05 03:05:41'),
(63, 3, 9541.00, 2, 5, '2026-01-05 09:26:02'),
(64, 3, 9813.00, 2, 2, '2026-01-05 09:26:24'),
(65, 3, 3543.00, 2, 1, '2026-01-05 09:27:28'),
(66, 3, 37585.00, 2, 1, '2026-01-05 09:27:50');

--
-- Triggers `orders`
--
DELIMITER $$
CREATE TRIGGER `after_order_status_update` AFTER UPDATE ON `orders` FOR EACH ROW BEGIN
    -- Check if the status changed to Cancelled (5) or Refunded (6)
    -- AND ensure it wasn't already in one of those states to prevent double-counting
    IF (NEW.order_status IN (5, 6)) AND (OLD.order_status NOT IN (5, 6)) THEN
        
        -- Use a JOIN to update the wines table based on the items in this specific order
        UPDATE wines w
        INNER JOIN order_items oi ON w.wine_id = oi.wine_id
        SET w.quantity = w.quantity + oi.quantity
        WHERE oi.order_id = NEW.order_id;
        
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

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
(49, 48, 1, 1110.00, 28),
(50, 49, 1, 53.30, 39),
(51, 49, 1, 35452.00, 40),
(54, 52, 1, 53.30, 39),
(55, 53, 1, 54.00, 35),
(60, 57, 1, 53.30, 39),
(63, 60, 1, 5453.00, 43),
(67, 62, 1, 3543.00, 1044),
(68, 62, 1, 5453.00, 43),
(69, 62, 1, 545.00, 42),
(70, 63, 1, 5453.00, 43),
(71, 63, 1, 3543.00, 1044),
(72, 63, 1, 545.00, 42),
(73, 64, 3, 3253.00, 34),
(74, 64, 1, 54.00, 35),
(75, 65, 1, 3543.00, 1044),
(76, 66, 1, 1110.00, 28),
(77, 66, 1, 36475.00, 32);

--
-- Triggers `order_items`
--
DELIMITER $$
CREATE TRIGGER `after_order_item_delete` AFTER DELETE ON `order_items` FOR EACH ROW BEGIN
    -- Automatically add the quantity back if the item is removed
    UPDATE wines 
    SET quantity = quantity + OLD.quantity 
    WHERE wine_id = OLD.wine_id;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_order_item_insert` AFTER INSERT ON `order_items` FOR EACH ROW BEGIN
    -- Automatically subtract the quantity from the wines table
    UPDATE wines 
    SET quantity = quantity - NEW.quantity 
    WHERE wine_id = NEW.wine_id;
END
$$
DELIMITER ;
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

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_provider` int(11) DEFAULT NULL,
  `payment_status` int(11) NOT NULL,
  `paid_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `order_id`, `payment_provider`, `payment_status`, `paid_at`) VALUES
(14, 48, 6, 2, '2026-01-04 18:43:11'),
(15, 49, 4, 2, '2026-01-04 18:52:30'),
(16, 53, 2, 3, '2026-01-04 23:11:19'),
(20, 52, 5, 2, '2026-01-05 02:24:25'),
(23, 60, 5, 2, '2026-01-05 02:26:15'),
(24, 63, 6, 1, '2026-01-05 09:26:13'),
(25, 64, 5, 2, '2026-01-05 09:26:39'),
(26, 65, 4, 2, '2026-01-05 09:27:39'),
(27, 66, 3, 2, '2026-01-05 09:27:57');

--
-- Triggers `payments`
--
DELIMITER $$
CREATE TRIGGER `after_payment_status_update` AFTER UPDATE ON `payments` FOR EACH ROW BEGIN
    -- Corrected syntax: Using IN for multiple status IDs
    IF (NEW.payment_status IN (3, 4)) AND (OLD.payment_status NOT IN (3, 4)) THEN
        UPDATE wines w
        INNER JOIN order_items oi ON w.wine_id = oi.wine_id
        SET w.quantity = w.quantity + oi.quantity
        WHERE oi.order_id = NEW.order_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `payment_method`
--

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

CREATE TABLE `payment_provider` (
  `provider_id` int(11) NOT NULL,
  `method_id` int(11) NOT NULL,
  `provider_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_provider`
--

INSERT INTO `payment_provider` (`provider_id`, `method_id`, `provider_name`) VALUES
(1, 1, 'Stripe'),
(2, 3, 'PayPal'),
(3, 1, 'Square'),
(4, 1, 'Authorize.Net'),
(5, 2, 'Braintree'),
(6, 5, 'Cash');

-- --------------------------------------------------------

--
-- Table structure for table `payment_status`
--

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
(4, 'Refunded');

-- --------------------------------------------------------

--
-- Table structure for table `shipping`
--

CREATE TABLE `shipping` (
  `shipping_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `country` varchar(150) NOT NULL,
  `tracking_number` varchar(150) DEFAULT NULL,
  `carrier_id` int(11) DEFAULT NULL,
  `shipping_status` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipping`
--

INSERT INTO `shipping` (`shipping_id`, `order_id`, `address`, `city`, `postal_code`, `country`, `tracking_number`, `carrier_id`, `shipping_status`) VALUES
(10, 48, '5353fs', 'fdf', '4545', 'fdgdfgd', 'SHIP-20260104-000010-8B329E', NULL, 'Awaiting Payment'),
(11, 49, 'fsdsd', 'fddsf', 'sfsdf', 'fsdfs', 'SHIP-20260104-000011-6963BD', NULL, 'Preparing'),
(12, 53, '343', 'dsds', '33', 'dsfdd', 'SHIP-20260104-000012-F3A903', NULL, 'Preparing'),
(16, 52, 'asdas', 'asdas', 'asdas', 'asdsa', 'SHIP-20260105-000016-DDD0B9', NULL, 'Preparing'),
(19, 60, 'sadas', 'sdsa', 'sadas', 'asdas', 'SHIP-20260105-000019-F4490E', NULL, 'Preparing'),
(20, 63, '323,sd', 'asas', 'asda', 'adas', 'SHIP-20260105-000020-1CB001', NULL, 'Awaiting Payment'),
(21, 64, 'sadas', '21', 'asads', 'adas', 'SHIP-20260105-000021-EED8F6', NULL, 'Preparing'),
(22, 65, 'dsadas', 'sasd', 'sdads', 'sddas', 'SHIP-20260105-000022-441512', NULL, 'Preparing'),
(23, 66, 'dsaasd', 'asdas', 'asdd', 'sas', 'SHIP-20260105-000023-5AF744', NULL, 'Preparing');

--
-- Triggers `shipping`
--
DELIMITER $$
CREATE TRIGGER `before_insert_shipping_fallback` BEFORE INSERT ON `shipping` FOR EACH ROW BEGIN
    IF NEW.tracking_number IS NULL AND NEW.shipping_status = 'Shipped' THEN
        SET NEW.tracking_number = CONCAT(
            'SHIP-',
            DATE_FORMAT(NOW(), '%Y%m%d'),
            '-',
            LPAD(NEW.order_id, 6, '0'),
            '-',
            UPPER(SUBSTRING(MD5(RAND()), 1, 6))
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `total_orders_view`
-- (See below for the actual view)
--
CREATE TABLE `total_orders_view` (
`total_orders` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `total_revenue_view`
-- (See below for the actual view)
--
CREATE TABLE `total_revenue_view` (
`total_revenue` decimal(32,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `total_wines_view`
-- (See below for the actual view)
--
CREATE TABLE `total_wines_view` (
`total_wines` bigint(21)
);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

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
(10, 'gERALDINE', 'd', 'dsd', 'neil@wmsuedu.com', '$2y$10$INCB26H1iK9eCZst5JhGE.hns93p7EF95reL/wK342ijHwBm3wLCa', '', 2, '2026-01-04 14:33:50');

-- --------------------------------------------------------

--
-- Table structure for table `user_type`
--

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
(28, 'weqdqw', 2, 4, 'sfsddf23', 2, 1.30, 8, 1110.00, 'cascacacascasa', '1767440425_695900296b616.png', '2026-01-03 00:00:00'),
(32, 'ddasdas', 2, 4, 'dsfdsfs', 3, 2.13, 4, 36475.00, 'dsaddfdasas', '1767451906_69592d02bdba0.png', '2026-01-03 00:00:00'),
(34, 'dfds', 3, 3, 'ddsdssd', 2, 3.10, 6, 3253.00, 'fdgdfffs', '1767494549_6959d3956da03.png', '2026-01-03 00:00:00'),
(35, 'weqdqw', 3, 4, 'fgfgddf', 2, 5.60, 34, 54.00, 'dfsfsfs', '1767455912_69593ca84189c.png', '2026-01-03 00:00:00'),
(37, 'weqdqw', 16, 6, 'dsfdsfs', 1, 3.50, 36, 56.00, 'ffsfsdfssfs', '1767485268_6959af5468f74.png', '2026-01-04 00:00:00'),
(39, 'weqdqw', 6, 6, 'dsfsd', 1, 23.50, 30, 53.30, 'dsdsfsdfsd', '1767486125_6959b2ad08457.png', '2026-01-04 00:00:00'),
(40, 'sfdishisd', 11, 15, 'dsfsd', 3, 5.23, 353, 35452.00, 'dsfd', '1767494557_6959d39d3fec0.png', '2026-01-04 09:08:05'),
(41, 'sdada', 5, 1, 'dsfsd', 2, 4.50, 34, 545.00, 'dsfsdfs', '1767526726_695a514604629.png', '2026-01-04 19:38:46'),
(42, 'sdadasadasda', 13, 1, 'dsfsd', 3, 4.50, 32, 545.00, 'dsfsdfs', '1767526740_695a5154037b5.png', '2026-01-04 19:39:00'),
(43, 'adsdasdas', 1, 20, 'dsfsdasdasd', 2, 4.50, 32, 5453.00, 'dsfsdfs', '1767526754_695a5162108ae.png', '2026-01-04 19:39:14'),
(1044, 'dsds', 11, 1, 'dsfs', 3, 3.10, 344, 3543.00, 'fdsdsffsd', '1767529735_695a5d073c6c2.png', '2026-01-04 20:28:55');

-- --------------------------------------------------------

--
-- Table structure for table `wine_type`
--

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

-- --------------------------------------------------------

--
-- Structure for view `dashboard_stats_summary`
--
DROP TABLE IF EXISTS `dashboard_stats_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `dashboard_stats_summary`  AS SELECT (select `total_wines_view`.`total_wines` from `total_wines_view`) AS `total_wines`, (select `total_revenue_view`.`total_revenue` from `total_revenue_view`) AS `total_revenue`, (select `low_stock_view`.`low_stock` from `low_stock_view`) AS `low_stock`, (select `total_orders_view`.`total_orders` from `total_orders_view`) AS `total_orders` ;

-- --------------------------------------------------------

--
-- Structure for view `low_stock_view`
--
DROP TABLE IF EXISTS `low_stock_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `low_stock_view`  AS SELECT count(0) AS `low_stock` FROM `wines` WHERE `wines`.`quantity` < 10 ;

-- --------------------------------------------------------

--
-- Structure for view `total_orders_view`
--
DROP TABLE IF EXISTS `total_orders_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `total_orders_view`  AS SELECT count(0) AS `total_orders` FROM `orders` ;

-- --------------------------------------------------------

--
-- Structure for view `total_revenue_view`
--
DROP TABLE IF EXISTS `total_revenue_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `total_revenue_view`  AS SELECT sum(`orders`.`total_amount`) AS `total_revenue` FROM `orders` ;

-- --------------------------------------------------------

--
-- Structure for view `total_wines_view`
--
DROP TABLE IF EXISTS `total_wines_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `total_wines_view`  AS SELECT count(0) AS `total_wines` FROM `wines` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `carrier`
--
ALTER TABLE `carrier`
  ADD PRIMARY KEY (`carrier_id`),
  ADD UNIQUE KEY `carrier_name` (`carrier_name`);

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
  ADD UNIQUE KEY `order_id_2` (`order_id`),
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
  ADD PRIMARY KEY (`provider_id`),
  ADD KEY `fk_provider_method` (`method_id`);

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
  ADD UNIQUE KEY `tracking_number` (`tracking_number`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `shipping_ibfk_carrier` (`carrier_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `user_type_id` (`user_type_id`),
  ADD KEY `first_name` (`first_name`,`middle_name`,`last_name`);

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
  ADD KEY `grape_variety` (`grape_variety`),
  ADD KEY `wine_name` (`wine_name`,`wine_type`,`grape_variety`,`country`);

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
-- AUTO_INCREMENT for table `carrier`
--
ALTER TABLE `carrier`
  MODIFY `carrier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `country`
--
ALTER TABLE `country`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `grape_variety`
--
ALTER TABLE `grape_variety`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `order_status`
--
ALTER TABLE `order_status`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `payment_method`
--
ALTER TABLE `payment_method`
  MODIFY `method_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payment_provider`
--
ALTER TABLE `payment_provider`
  MODIFY `provider_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payment_status`
--
ALTER TABLE `payment_status`
  MODIFY `status_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `shipping`
--
ALTER TABLE `shipping`
  MODIFY `shipping_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

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
-- Constraints for table `payment_provider`
--
ALTER TABLE `payment_provider`
  ADD CONSTRAINT `fk_provider_method` FOREIGN KEY (`method_id`) REFERENCES `payment_method` (`method_id`) ON DELETE CASCADE;

--
-- Constraints for table `shipping`
--
ALTER TABLE `shipping`
  ADD CONSTRAINT `shipping_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `shipping_ibfk_carrier` FOREIGN KEY (`carrier_id`) REFERENCES `carrier` (`carrier_id`);

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
