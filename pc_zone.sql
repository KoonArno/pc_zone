-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 20, 2025 at 10:27 PM
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
-- Database: `pc_zone`
--

-- --------------------------------------------------------

--
-- Table structure for table `address`
--

CREATE TABLE `address` (
  `address_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `address_line` text NOT NULL,
  `district` varchar(100) NOT NULL,
  `subdistrict` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `postal_code` varchar(10) NOT NULL,
  `country` varchar(100) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `address`
--

INSERT INTO `address` (`address_id`, `user_id`, `full_name`, `phone_number`, `address_line`, `district`, `subdistrict`, `city`, `postal_code`, `country`, `is_default`, `created_at`) VALUES
(1, 1, 'Nattakarn Klongkratok', '0858326159', '24/5', 'Bangkapi', 'Huamak', 'Bangkok', '10240', 'ไทย', 0, '2025-03-16 14:31:02'),
(7, 5, 'pppppppppp', '123456', '11', '2', 'kfc', 'a', '123', 'ไทย', 0, '2025-03-18 11:56:03'),
(8, 5, '1', '2', '3', '4', '4', '4', '5', 'ไทย', 1, '2025-03-18 11:56:24'),
(9, 1, 'nattakarn', '0085', '12312', '123213', '123123', '2121', '123123', 'ไทย', 1, '2025-03-18 20:23:45');

-- --------------------------------------------------------

--
-- Table structure for table `bookmark`
--

CREATE TABLE `bookmark` (
  `bookmark_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookmark`
--

INSERT INTO `bookmark` (`bookmark_id`, `user_id`, `product_id`, `created_at`) VALUES
(12, 1, 90, '2025-03-19 21:12:34'),
(13, 1, 96, '2025-03-19 21:12:36'),
(14, 1, 21, '2025-03-19 21:12:39'),
(15, 1, 2, '2025-03-20 20:27:10');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL CHECK (`quantity` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`) VALUES
(48, 1, 77, 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `address_id` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL CHECK (`total_price` >= 0),
  `payment_id` int(11) DEFAULT NULL,
  `order_status` enum('processing','shipping','shipped','done','rejected') NOT NULL DEFAULT 'processing',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `address_id`, `total_price`, `payment_id`, `order_status`, `created_at`) VALUES
(1, 1, 1, 12289.00, 12, 'done', '2025-03-16 20:15:21'),
(13, 1, 1, 5390.00, 23, 'rejected', '2025-03-17 17:41:25'),
(16, 1, 1, 6990.00, 26, 'done', '2025-03-17 19:55:10'),
(18, 5, 7, 64480.00, 28, 'done', '2025-03-18 11:57:19'),
(19, 1, 1, 3680.00, 29, 'done', '2025-03-18 17:35:23'),
(20, 1, 1, 13190.00, 30, 'processing', '2025-03-19 21:11:27');

-- --------------------------------------------------------

--
-- Table structure for table `orders_item`
--

CREATE TABLE `orders_item` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL CHECK (`quantity` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders_item`
--

INSERT INTO `orders_item` (`order_item_id`, `order_id`, `product_id`, `quantity`) VALUES
(1, 1, 65, 1),
(2, 1, 24, 1),
(18, 13, 87, 1),
(21, 16, 61, 1),
(25, 18, 59, 1),
(26, 18, 24, 1),
(27, 18, 11, 1),
(28, 19, 20, 1),
(29, 19, 70, 1),
(30, 20, 76, 1),
(31, 20, 60, 1);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `proof_image` varchar(255) DEFAULT NULL,
  `payment_status` enum('pending','rejected','completed') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`payment_id`, `order_id`, `proof_image`, `payment_status`, `created_at`) VALUES
(12, 1, 'payment_1_1742211237.jpg', 'completed', '2025-03-17 11:33:57'),
(23, 13, 'payment_1_1742233297.jpg', 'rejected', '2025-03-17 17:41:37'),
(26, 16, 'payment_1_1742241320.jpg', 'completed', '2025-03-17 19:55:20'),
(28, 18, 'payment_5_1742299075.jpg', 'completed', '2025-03-18 11:57:55'),
(29, 19, 'payment_1_1742395960.jpg', 'completed', '2025-03-19 14:52:40'),
(30, 20, 'payment_1_1742502557.jpg', 'pending', '2025-03-20 20:29:17');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL CHECK (`price` >= 0),
  `type` enum('mouse','keyboard','mouse_pad','mic','monitor','headset','other') NOT NULL DEFAULT 'other'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `product_name`, `description`, `image`, `price`, `type`) VALUES
(1, 'AJAZZ AK820 MAX MAGNETIC SWITCH RGB EN/TH - DUST GRADIENT', '{\"switch\": \"Magnetic Switch (Linear)\", \"lighting\": \"RGB\", \"keycap\": \"English / Thai\", \"layout\": \"ANSI\", \"connection\": [\"USB-C to USB-A (Detachable)\"]}', 'keyboard1.jpg', 2190.00, 'keyboard'),
(2, 'AJAZZ AK680 MAX (BLACK) (MAGNETIC SWITCH NON-BACKLIT EN/TH)', '{\"switch\": \"Magnetic Switch (Linear)\", \"lighting\": \"None\", \"keycap\": \"English / Thai\", \"layout\": \"ANSI\", \"connection\": [\"USB-C to USB-A (Detachable)\"]}', 'keyboard2.jpg', 1190.00, 'keyboard'),
(3, 'WIRELESS KEYBOARD AJAZZ AK820 MAX (RETRO) (FLYING FISH SWITCH RGB EN/TH)', '{\"switch\": \"Flying Fish Switch (Linear)\", \"lighting\": \"RGB\", \"keycap\": \"English / Thai\", \"layout\": \"ANSI\", \"connection\": [\"USB-C to USB-A (Detachable)\", \"2.4GHz Wireless\", \"Bluetooth 5.0\"]}', 'keyboard3.jpg', 1590.00, 'keyboard'),
(4, 'WIRELESS KEYBOARD HYPERX ALLOY RISE 75 WIRELESS (BLACK) (HYPERX LINEAR SWITCH RGB EN/TH)', '{\"switch\": \"HyperX Linear Switch\", \"lighting\": \"RGB\", \"keycap\": \"English / Thai\", \"layout\": \"ANSI\", \"connection\": [\"USB-C to USB-A (Detachable)\", \"2.4GHz Wireless\", \"Bluetooth\"]}', 'keyboard4.jpg', 8990.00, 'keyboard'),
(5, 'KEYBOARD HYPERX ALLOY ORIGINS (BLACK) (HYPERX RED SWITCH - RGB LED - EN/TH)', '{\"switch\": \"HyperX Red Switch (Linear)\", \"lighting\": \"RGB LED\", \"keycap\": \"English / Thai\", \"layout\": \"ANSI\", \"connection\": [\"USB-C to USB-A (Detachable)\"]}', 'keyboard5.jpg', 2490.00, 'keyboard'),
(6, 'WIRELESS KEYBOARD ASUS ROG AZOTH EXTREME ASUS ROG NX STORM SWITCH AURA SYNC RGB EN - BLACK (M702)', '{\"switch\": \"Asus ROG NX Storm Switch (Clicky)\", \"lighting\": \"Aura Sync RGB\", \"keycap\": \"English\", \"layout\": \"ANSI\", \"display\": \"OLED Touchscreen\", \"connection\": [\"USB-C to USB-A (Detachable)\", \"2.4GHz Wireless\", \"Bluetooth\"]}', 'keyboard6.jpg', 15900.00, 'keyboard'),
(7, 'KEYBOARD ASUS TUF GAMING K3 GEN II (BLACK) (OPTICAL BLUE SWITCH AURA SYNC RGB EN/TH) (RA07)', '{\"switch\": \"Optical Blue Switch (Clicky)\", \"lighting\": \"Aura Sync RGB\", \"keycap\": \"English / Thai\", \"layout\": \"ANSI\", \"connection\": [\"USB-A (Non-Detachable)\"]}', 'keyboard7.jpg', 2290.00, 'keyboard'),
(8, 'KEYBOARD RAZER ORNATA V3 X (BLACK) (MEMBRANE - RGB -EN/TH)', '{\"switch\": \"Membrane\", \"lighting\": \"RGB LED\", \"keycap\": \"English / Thai\", \"layout\": \"ANSI\", \"connection\": [\"USB Wired\"]}', 'keyboard8.jpg', 1290.00, 'keyboard'),
(9, 'KEYBOARD RAZER HUNTSMAN V3 PRO TENKEYLESS (WHITE) (RAZER ANALOG OPTICAL SWITCH GEN-2 - CHROMA RGB - ', '{\"switch\": \"Razer Analog Optical Switch Gen-2 (Linear)\", \"lighting\": \"Chroma RGB\", \"keycap\": \"English\", \"layout\": \"ANSI\", \"connection\": [\"USB-C to USB-A (Detachable)\"]}', 'keyboard9.jpg', 7690.00, 'keyboard'),
(10, 'WIRELESS KEYBOARD RAZER DEATHSTALKER V2 PRO (WHITE) (RAZER CLICKY LOW-PROFILE OPTICAL SWITCH - RAZER', '{\"switch\": \"Razer Clicky Low-profile Optical Switch (Purple)\", \"lighting\": \"Razer Chroma RGB\", \"keycap\": \"English\", \"layout\": \"ANSI\", \"connection\": [\"USB-C to USB-A (Detachable)\", \"Razer HyperSpeed Wireless\", \"Bluetooth\"]}', 'keyboard10.jpg', 5990.00, 'keyboard'),
(11, 'Wooting 60HE+ | คีย์บอร์ด Rapid Trigger Gaming', '{\"switch\": \"Lekker (Hall Effect)\", \"adjustable_actuation\": \"0.1-4.0 mm\", \"features\": [\"Analog Rapid Trigger\", \"Dual Function Keys\"], \"size\": \"60%\", \"connection\": [\"USB-C\"], \"lighting\": \"RGB (Customizable via Wootility)\"}', 'keyboard11.jpg', 9290.00, 'keyboard'),
(12, 'Angry Miao AM RGB 65 Wireless Mechanical Keyboard (EN) Cloud White', '{\"design\": \"Retro-Inspired (Game Boy)\", \"size\": \"65%\", \"mount\": \"Leaf Spring Mount\", \"switch\": \"Icy Silver Pro V2 (Linear)\", \"connection\": [\"2.4GHz Wireless\", \"Bluetooth 5.1\"], \"battery_life\": \"Long-lasting\", \"lighting\": \"RGB + LED Panel (100+ Effects)\"}', 'keyboard12.jpg', 22990.00, 'keyboard'),
(13, 'Lamzu Atlantis Pro Keyboard (EN) Polar White', '{\"size\": \"65%\", \"keycap\": \"Silicone Rubber Pudding (White-Blue)\", \"lighting\": \"RGB (North Facing)\", \"body\": \"CNC Aluminum\", \"connection\": [\"USB (1000Hz Polling Rate)\"], \"switch\": \"Magnetic (Adjustable 0.2 - 3.8 mm)\", \"features\": [\"Rapid Trigger\", \"Low Latency\"]}', 'keyboard13.jpg', 6990.00, 'keyboard'),
(14, 'Mistel MD750 Delight Devil Green Wireless Mechanical Keyboard (EN) Gateron Red', '{\"key_count\": \"81\", \"keycap_material\": \"PBT Double Shot\", \"switch\": \"Gateron Red\", \"lighting\": \"RGB\", \"connection\": [\"USB-C\", \"Bluetooth 5.0\", \"2.4GHz Wireless\"], \"polling_rate\": [\"1000Hz\", \"125Hz\"], \"compatible\": [\"Windows\", \"MacOS\"]}', 'keyboard14.jpg', 2990.00, 'keyboard'),
(15, 'WIRELESS KEYBOARD VORTEX PC66 (66 KEY) SWITCH GATERON G PRO SILVER (US)', '{\"size\": \"66 Keys\", \"switch\": \"Gateron G Pro Silver\", \"connection\": [\"USB Type-C\", \"2.4G Wireless\"], \"features\": [\"Additional Keycaps\", \"Keycap & Switch Puller\", \"Hex Key\"]}', 'keyboard15.jpg', 5790.00, 'keyboard'),
(16, 'KEYBOARD ONIKUMA HIKARI G39 (BLACK) (BLUE SWITCH - RGB - EN/TH)', '{\"switch\": \"Blue Switch (Clicky)\", \"lighting\": \"RGB\", \"keycap\": \"English / Thai\", \"layout\": \"ANSI\", \"connection\": [\"USB-C to USB-A (Detachable)\"]}', 'keyboard16.jpg', 1790.00, 'keyboard'),
(17, 'WIRELESS KEYBOARD ONIKUMA AKI BLUE SWITCH RGB EN/TH - BLACK', '{\"switch\": \"Blue Switch (Clicky)\", \"lighting\": \"RGB\", \"keycap\": \"English / Thai\", \"layout\": \"ANSI\", \"connection\": [\"USB-C to USB-A (Detachable)\", \"2.4GHz Wireless\", \"Bluetooth\"]}', 'keyboard17.jpg', 1590.00, 'keyboard'),
(18, 'WIRELESS KEYBOARD LOGITECH G G913 LIGHTSPEED WIRELESS RGB GL LINEAR SWITCH (RGB LED) (EN/TH)', '{\"switch\": \"Low Profile GL Linear\", \"lighting\": \"RGB\", \"keycap\": \"English / Thai\", \"connection\": [\"Lightspeed Wireless\", \"Bluetooth\"]}', 'keyboard18.jpg', 4390.00, 'keyboard'),
(19, 'KEYBOARD LOGITECH G PRO (GX BLUE SWITCH) (RGB LED) (EN/TH)', '{\"switch\": \"GX Blue Switch (Clicky)\", \"lighting\": \"RGB\", \"keycap\": \"English / Thai\", \"connection\": [\"USB 2.0 Type-A\"]}', 'keyboard19.jpg', 3990.00, 'keyboard'),
(20, 'KEYBOARD MSI VIGOR GK50 ELITE KAILH BLUE (BLUE SWITCH) (RGB LED) (TH/EN)', '{\"switch\": \"Kailh Blue\", \"lighting\": \"RGB\", \"keycap\": \"English\", \"connection\": [\"USB 2.0\"]}', 'keyboard20.jpg', 2590.00, 'keyboard'),
(21, 'เมาส์ Lamzu Maya 4K Wireless Gaming Mouse Cloud Gray', '{\"น้ำหนัก\": \"45 กรัม\", \"ขนาด\": \"เล็ก\", \"รองรับการจับ\": \"Claw และ Fingertip\", \"Polling Rate\": \"4K\"}', 'mouse1.jpg', 4290.00, 'mouse'),
(22, 'เมาส์ Lamzu Maya X 8K Wireless Gaming Mouse Charcoal Black', '{\"เซ็นเซอร์\": \"PAW3950\", \"Polling Rate\": \"8,000Hz\", \"สวิตช์\": \"Omron Optical\", \"น้ำหนัก\": \"เบา\", \"เหมาะสำหรับ\": \"เกมเมอร์ขนาดกลาง-ใหญ่\"}', 'mouse2.jpg', 4790.00, 'mouse'),
(23, 'เมาส์ Lamzu Maya Wireless Gaming Mouse Imperial Red', '{\"น้ำหนัก\": \"45 กรัม\", \"ขนาด\": \"เล็ก\", \"รองรับการจับ\": \"Claw และ Fingertip\", \"ผิวสัมผัส\": \"เรียบเนียน\"}', 'mouse3.jpg', 3790.00, 'mouse'),
(24, 'เมาส์ Logitech G Pro X Superlight 2 Wireless Gaming Mouse White', '{\"สวิตช์\": \"LIGHTFORCE\", \"น้ำหนัก\": \"60 กรัม\", \"DPI\": \"100 - 32,000\", \"เซ็นเซอร์\": \"HERO 2\"}', 'mouse4.jpg', 5290.00, 'mouse'),
(25, 'เมาส์ไร้สาย Logitech G304 Wireless Gaming Mouse Black', '{\"DPI\": \"สูงสุด 12000\", \"เทคโนโลยี\": \"Logitech\", \"แบตเตอรี่\": \"9 เดือน\"}', 'mouse5.jpg', 1290.00, 'mouse'),
(26, 'เมาส์ Logitech MX Master 3S Performance Wireless Mouse Pale Gray', '{\"ลูกกลิ้ง\": \"MagSpeed\", \"เซ็นเซอร์\": \"Darkfield\", \"ปุ่มกด\": \"8 ปุ่ม\", \"แบตเตอรี่\": \"70 วัน\"}', 'mouse6.jpg', 4499.00, 'mouse'),
(27, 'เมาส์ Razer Viper V3 Pro Wireless Gaming Mouse Black', '{\"Polling Rate\": \"8K\", \"น้ำหนัก\": \"54 กรัม\", \"DPI\": \"สูงสุด 35000\", \"สวิตช์\": \"Optical\"}', 'mouse7.jpg', 5690.00, 'mouse'),
(28, 'เมาส์ Razer Cobra Pro Wireless Gaming Mouse Black', '{\"น้ำหนัก\": \"77 กรัม\", \"เซ็นเซอร์\": \"Focus Pro 30K Optical\", \"DPI\": \"สูงสุด 30000\", \"คลิก\": \"90 ล้านคลิก\"}', 'mouse8.jpg', 3190.00, 'mouse'),
(29, 'เมาส์ Razer DeathAdder Essential Gaming Mouse Black', '{\"ยอดขาย\": \"10 ล้านตัว\", \"คลิก\": \"10 ล้านคลิก\", \"DPI\": \"สูงสุด 6400\"}', 'mouse9.jpg', 799.00, 'mouse'),
(30, 'เมาส์ Razer Basilisk V3 Gaming Mouse Black', '{\"ปุ่มกด\": \"11 ปุ่ม\", \"เมมโมรี่\": \"5 โปรไฟล์\", \"ไฟ\": \"Razer Chroma™ RGB\"}', 'mouse10.jpg', 1990.00, 'mouse'),
(31, 'เมาส์ Nubwo NM-103W Wireless Gaming Mouse White', '{\"ไฟ\": \"RGB\", \"การเชื่อมต่อ\": \"Wireless(dongle), Bluetooth\", \"DPI\": \"สูงสุด 4,800\"}', 'mouse11.jpg', 390.00, 'mouse'),
(32, 'เมาส์ Nubwo Nimbuz X59 Gaming Mouse Black', '{\"ไฟ\": \"RGB\", \"การเชื่อมต่อ\": \"Wired\", \"DPI\": \"สูงสุด 7,200\"}', 'mouse12.jpg', 349.00, 'mouse'),
(33, 'เมาส์ Glorious Model D 2 Pro Wireless Gaming Mouse Black', '{\"น้ำหนัก\": \"60 กรัม\", \"แบตเตอรี่\": \"80 ชั่วโมง\", \"DPI\": \"100 - 26,000\", \"คลิก\": \"100 ล้านครั้ง\"}', 'mouse13.jpg', 4190.00, 'mouse'),
(34, 'เมาส์ Glorious Model D Wireless Gaming Mouse Black', '{\"น้ำหนัก\": \"69 กรัม\", \"เซ็นเซอร์\": \"Glorious BAMF Sensor\", \"การเชื่อมต่อ\": \"LAG-FREE 2.4 GHZ\"}', 'mouse14.jpg', 3590.00, 'mouse'),
(35, 'เมาส์ Glorious Model D 2 Pro 4K/8K Edition Wireless Gaming Mouse Black', '{\"น้ำหนัก\": \"เบา\", \"Polling Rate\": \"4K และ 8K\", \"รูปทรง\": \"มือขวา\"}', 'mouse15.jpg', 5490.00, 'mouse'),
(36, 'เมาส์ Glorious Model O 2 Gaming Mouse Black', '{\"น้ำหนัก\": \"59 กรัม\", \"คลิก\": \"80 ล้านครั้ง\", \"ปุ่มกด\": \"6 ปุ่ม\", \"DPI\": \"สูงสุด 26,000\"}', 'mouse16.jpg', 2690.00, 'mouse'),
(37, 'เมาส์ SteelSeries Prime Gaming Mouse Black', '{\"คลิก\": \"100 ล้านครั้ง\", \"CPI\": \"สูงสุด 18,000\", \"ทำความสะอาด\": \"ง่าย\"}', 'mouse17.jpg', 2690.00, 'mouse'),
(38, 'เมาส์ Dareu A950 Wireless Gaming Mouse Royal Blue', '{\"ไฟ\": \"RGB\", \"การเชื่อมต่อ\": \"Wired, Wireless(dongle), Bluetooth\", \"น้ำหนัก\": \"88 กรัม\"}', 'mouse18.jpg', 1990.00, 'mouse'),
(39, 'เมาส์ ThundeRobot ML602 Wireless Gaming Mouse Black', '{\"น้ำหนัก\": \"64 กรัม\", \"สวิตช์\": \"HUANO Mirco Switch\", \"DPI\": \"สูงสุด 26000\", \"Polling Rate\": \"1000Hz\"}', 'mouse19.jpg', 1390.00, 'mouse'),
(40, 'เมาส์ Signo WG-903 VORKEN Wireless Gaming Mouse Black', '{\"ปุ่มกด\": \"5 ปุ่ม\", \"เซ็นเซอร์\": \"PAW3395\", \"น้ำหนัก\": \"58 กรัม\", \"แบตเตอรี่\": \"70 ชั่วโมง\"}', 'mouse20.jpg', 1490.00, 'mouse'),
(41, 'MONITOR ACER VERO RS272 G0BPAMIX - 27 INCH IPS FHD 120Hz AMD FREESYNC NVIDIA G-SYNC', '{\"ขนาด\": \"27 นิ้ว\", \"ประเภทจอ\": \"IPS\", \"ความละเอียด\": \"1920 x 1080\", \"รีเฟรชเรท\": \"120Hz (HDMI), 75Hz (D-Sub)\", \"เวลาตอบสนอง\": \"1ms\", \"สี\": \"16.7 ล้านสี\", \"การเชื่อมต่อ\": \"1 x HDMI, 1 x D-Sub\", \"เทคโนโลยีซิงค์\": \"AMD FreeSync, Nvidia G-Sync\", \"การติดตั้ง\": \"VESA 75 x 75 mm\"}', 'monitor1.jpg', 3900.00, 'monitor'),
(42, 'MONITOR ACER PREDATOR X45 BMIIPHUZX - 44.5 INCH OLED 2K 240Hz AMD FREESYNC PREMIUM USB-C CURVED', '{\"ขนาด\": \"44.5 นิ้ว\", \"ประเภทจอ\": \"OLED\", \"ความละเอียด\": \"3440 x 1440\", \"รีเฟรชเรท\": \"240Hz\", \"เวลาตอบสนอง\": \"0.01ms\", \"สี\": \"1.07 พันล้านสี\", \"การเชื่อมต่อ\": \"2 x HDMI, 1 x DP, 1 x USB-C\", \"เทคโนโลยีซิงค์\": \"AMD FreeSync Premium\", \"การปรับตั้ง\": \"ความสูง, หมุนซ้าย-ขวา, เอียง\", \"การติดตั้ง\": \"VESA 200 x 100 mm\"}', 'monitor2.jpg', 44900.00, 'monitor'),
(43, 'MONITOR ACER XV270U Z1BMIIPRX - 27 INCH IPS 2K 270Hz AMD FREESYNC PREMIUM', '{\"ขนาด\": \"27 นิ้ว\", \"ประเภทจอ\": \"IPS\", \"ความละเอียด\": \"2560 x 1440\", \"รีเฟรชเรท\": \"270Hz\", \"เวลาตอบสนอง\": \"0.5ms\", \"สี\": \"1.07 พันล้านสี\", \"การเชื่อมต่อ\": \"2 x HDMI, 1 x DP\", \"เทคโนโลยีซิงค์\": \"AMD FreeSync Premium\", \"การปรับตั้ง\": \"ความสูง, หมุนซ้าย-ขวา, เอียง\", \"การติดตั้ง\": \"VESA 100 x 100 mm\"}', 'monitor3.jpg', 8400.00, 'monitor'),
(44, 'MONITOR ACER NITRO VG270 GBMIPX - 27 INCH IPS FHD 120Hz', '{\"ขนาด\": \"27 นิ้ว\", \"ประเภทจอ\": \"IPS\", \"ความละเอียด\": \"1920 x 1080\", \"รีเฟรชเรท\": \"120Hz\", \"เวลาตอบสนอง\": \"1ms\", \"สี\": \"16.7 ล้านสี\", \"การเชื่อมต่อ\": \"1 x HDMI, 1 x DP\", \"การปรับตั้ง\": \"เอียง\", \"การติดตั้ง\": \"VESA 100 x 100 mm\"}', 'monitor4.jpg', 3800.00, 'monitor'),
(45, 'MONITOR ACER NITRO VG220Q E3BMIIX - 21.5 INCH IPS FHD 100Hz AMD FREESYNC', '{\"ขนาด\": \"21.5 นิ้ว\", \"ประเภทจอ\": \"IPS\", \"ความละเอียด\": \"1920 x 1080\", \"รีเฟรชเรท\": \"100Hz (HDMI), 75Hz (D-Sub)\", \"เวลาตอบสนอง\": \"1ms\", \"สี\": \"16.7 ล้านสี\", \"การเชื่อมต่อ\": \"1 x HDMI, 1 x D-Sub\", \"เทคโนโลยีซิงค์\": \"AMD FreeSync\", \"การปรับตั้ง\": \"เอียง\", \"การติดตั้ง\": \"VESA 100 x 100 mm\"}', 'monitor5.jpg', 2450.00, 'monitor'),
(46, 'MONITOR ASUS ZENSCREEN - 15.6 INCH FHD OLED (MQ16AHE)', '{\"ขนาด\": \"15.6 นิ้ว\", \"ประเภทจอ\": \"OLED\", \"ความละเอียด\": \"1920 x 1080\", \"รีเฟรชเรท\": \"60Hz\", \"เวลาตอบสนอง\": \"1ms\", \"สี\": \"1.073.7 ล้านสี\", \"การเชื่อมต่อ\": \"1 x Mini-HDMI, 1 x USB-C (DP Alt Mode + 10W)\"}', 'monitor6.jpg', 14900.00, 'monitor'),
(47, 'MONITOR ASUS TUF GAMING VG259Q3A - 24.5 INCH IPS FHD 180Hz AMD FREESYNC', '{\"ขนาด\": \"24.5 นิ้ว\", \"ประเภทจอ\": \"IPS\", \"ความละเอียด\": \"1920 x 1080\", \"รีเฟรชเรท\": \"180Hz\", \"เวลาตอบสนอง\": \"1ms\", \"สี\": \"16.7 ล้านสี\", \"การเชื่อมต่อ\": \"2 x HDMI, 1 x DP\", \"เทคโนโลยีซิงค์\": \"AMD FreeSync\", \"การปรับตั้ง\": \"เอียง\", \"การติดตั้ง\": \"VESA 100 x 100 mm\"}', 'monitor7.jpg', 4500.00, 'monitor'),
(48, 'MONITOR ASUS PG34WCDM - 33.94 INCH OLED CURVED U2K 240Hz', '{\"ขนาด\": \"33.94 นิ้ว\", \"ประเภทจอ\": \"OLED\", \"ความละเอียด\": \"3440 x 1440\", \"รีเฟรชเรท\": \"240Hz\", \"เวลาตอบสนอง\": \"0.03ms\", \"สี\": \"1.07 พันล้านสี\", \"การเชื่อมต่อ\": \"2 x HDMI, 1 x DP, 1 x Type-C (DP Alt Mode)\", \"เทคโนโลยีซิงค์\": \"Adaptive-Sync\", \"การปรับตั้ง\": \"ความสูง, หมุนซ้าย-ขวา, เอียง\", \"การติดตั้ง\": \"VESA 100 x 100 mm\"}', 'monitor8.jpg', 48900.00, 'monitor'),
(49, 'MONITOR HP OMEN 27QS - 27 INCH IPS 2K 240Hz FREESYNC PREMIUM G-SYNC COMPATIBLE', '{\"ขนาด\": \"27 นิ้ว\", \"ประเภทจอ\": \"IPS\", \"ความละเอียด\": \"2560 x 1440\", \"รีเฟรชเรท\": \"240Hz (DP), 144Hz (HDMI)\", \"เวลาตอบสนอง\": \"1ms\", \"การเชื่อมต่อ\": \"1 x DP, 2 x HDMI, 1 x Headphone jack\", \"เทคโนโลยีซิงค์\": \"AMD FreeSync Premium, Nvidia G-Sync Compatible\", \"การปรับตั้ง\": \"ความสูง, หมุนซ้าย-ขวา, เอียง\", \"การติดตั้ง\": \"VESA 100 x 100 mm\"}', 'monitor9.jpg', 10900.00, 'monitor'),
(50, 'MONITOR HP OMEN 27Q - 27 INCH IPS 2K 165Hz', '{\"ขนาด\": \"27 นิ้ว\", \"ประเภทจอ\": \"IPS\", \"ความละเอียด\": \"2560 x 1440\", \"รีเฟรชเรท\": \"165Hz\", \"เวลาตอบสนอง\": \"1ms\", \"การเชื่อมต่อ\": \"2 x HDMI, 1 x DP\", \"การปรับตั้ง\": \"ความสูง, หมุนซ้าย-ขวา, เอียง\", \"การติดตั้ง\": \"VESA 100 x 100 mm\"}', 'monitor10.jpg', 6800.00, 'monitor'),
(51, 'MONITOR HP SERIES 3 PRO - 21.5 INCH FHD 322PF (9U5B0UT#AKL)', '{\"ขนาด\": \"21.5 นิ้ว\", \"ประเภทจอ\": \"IPS\", \"ความละเอียด\": \"1920 x 1080\", \"รีเฟรชเรท\": \"100Hz\", \"เวลาตอบสนอง\": \"5ms\", \"การเชื่อมต่อ\": \"1 x HDMI, 1 x VGA\", \"การปรับตั้ง\": \"เอียง\"}', 'monitor11.jpg', 3400.00, 'monitor'),
(52, 'PORTABLE MONITOR DAHUA DHI-PM16-D201S - 15.6 INCH IPS FHD 60Hz USB-C', '{\"ขนาด\": \"15.6 นิ้ว\", \"ประเภทจอ\": \"IPS\", \"ความละเอียด\": \"1920 x 1080\", \"รีเฟรชเรท\": \"60Hz\", \"เวลาตอบสนอง\": \"6ms\", \"สี\": \"16.7 ล้านสี\", \"การเชื่อมต่อ\": \"1 x Mini HDMI, 2 x USB-C\", \"การติดตั้ง\": \"VESA 75 x 75 mm\"}', 'monitor12.jpg', 13900.00, 'monitor'),
(53, 'PORTABLE MONITOR DAHUA DHI-PM16-S201ST - 15.6 INCH IPS FHD 60Hz USB-C', '{\"ขนาด\": \"15.6 นิ้ว\", \"ประเภทจอ\": \"IPS\", \"ความละเอียด\": \"1920 x 1080\", \"รีเฟรชเรท\": \"60Hz\", \"เวลาตอบสนอง\": \"6ms (Touch screen)\", \"สี\": \"16.7 ล้านสี\", \"การเชื่อมต่อ\": \"1 x Mini HDMI, 2 x USB-C\", \"การติดตั้ง\": \"VESA 75 x 75 mm\"}', 'monitor13.jpg', 6900.00, 'monitor'),
(54, 'MONITOR DAHUA LM30-E330CA - 30 INCH VA FHD 200Hz ADAPTIVE SYNC CURVED', '{\"ขนาด\": \"30 นิ้ว\", \"ประเภทจอ\": \"VA\", \"ความละเอียด\": \"2560 x 1080\", \"รีเฟรชเรท\": \"200Hz (DP), 180Hz (HDMI)\", \"เวลาตอบสนอง\": \"1ms\", \"สี\": \"16.7 ล้านสี\", \"การเชื่อมต่อ\": \"2 x HDMI, 2 x DP\", \"เทคโนโลยีซิงค์\": \"Adaptive Sync\", \"การปรับตั้ง\": \"ความสูง, หมุนซ้าย-ขวา, เอียง\", \"การติดตั้ง\": \"VESA 75 x 75 mm\"}', 'monitor14.jpg', 6600.00, 'monitor'),
(55, 'MONITOR MSI MAG 274URFW - 27 INCH RAPID IPS 4K 160Hz AMD FREESYNC PREMIUM USB-C', '{\"ขนาด\": \"27 นิ้ว\", \"ประเภทจอ\": \"Rapid IPS\", \"ความละเอียด\": \"3840 x 2160\", \"รีเฟรชเรท\": \"160Hz\", \"เวลาตอบสนอง\": \"0.5ms\", \"สี\": \"1.07 พันล้านสี\", \"การเชื่อมต่อ\": \"2 x HDMI, 1 x DP, 1 x USB-C (DP Alt Mode + PD 15W)\", \"เทคโนโลยีซิงค์\": \"AMD FreeSync Premium\", \"การปรับตั้ง\": \"ความสูง, หมุนซ้าย-ขวา, เอียง\", \"การติดตั้ง\": \"VESA 75 x 75 mm\"}', 'monitor15.jpg', 14900.00, 'monitor'),
(56, 'MONITOR MSI PRO MP341CQ - 34 INCH VA 2K 100Hz CURVED', '{\"ขนาด\": \"34 นิ้ว\", \"ประเภทจอ\": \"VA\", \"ความละเอียด\": \"3440 x 1440\", \"รีเฟรชเรท\": \"100Hz\", \"เวลาตอบสนอง\": \"1ms (MPRT)\", \"สี\": \"1.07 พันล้านสี\", \"การเชื่อมต่อ\": \"2 x HDMI, 1 x DP\", \"การติดตั้ง\": \"VESA 100 x 100 mm\"}', 'monitor16.jpg', 8900.00, 'monitor'),
(57, 'MONITOR LENOVO LEGION R34W-30 - 34 INCH VA 2K 180Hz AMD FREESYNC PREMIUM CURVED', '{\"ขนาด\": \"34 นิ้ว\", \"ประเภทจอ\": \"VA\", \"ความละเอียด\": \"3440 x 1440\", \"รีเฟรชเรท\": \"180Hz\", \"เวลาตอบสนอง\": \"0.5ms\", \"สี\": \"16.7 ล้านสี\", \"การเชื่อมต่อ\": \"2 x HDMI, 1 x DP\", \"เทคโนโลยีซิงค์\": \"AMD FreeSync Premium\", \"การปรับตั้ง\": \"ความสูง, หมุนซ้าย-ขวา, เอียง\", \"การติดตั้ง\": \"VESA 100 x 100 mm\"}', 'monitor17.jpg', 10000.00, 'monitor'),
(58, 'MONITOR LENOVO THINKVISION E22-30 - 21.5 INCH 75Hz (63EBMAR2WW)', '{\"ขนาด\": \"21.5 นิ้ว\", \"ประเภทจอ\": \"IPS\", \"ความละเอียด\": \"1920 x 1080\", \"รีเฟรชเรท\": \"75Hz\", \"เวลาตอบสนอง\": \"4ms\", \"สี\": \"16.7 ล้านสี\", \"การเชื่อมต่อ\": \"1 x HDMI, 1 x DP, 1 x VGA\", \"การปรับตั้ง\": \"เอียง, หมุนซ้าย-ขวา, หมุนแนวตั้ง\", \"การติดตั้ง\": \"VESA 100 x 100 mm\"}', 'monitor18.jpg', 4900.00, 'monitor'),
(59, 'MONITOR SAMSUNG ODYSSEY G9 G93SD LS49DG930SEXXT - 49 INCH OLED U2K 240Hz CURVED AMD FREESYNC PREMIUM', '{\"ขนาด\": \"49 นิ้ว\", \"ประเภทจอ\": \"OLED\", \"ความละเอียด\": \"5120 x 1440\", \"รีเฟรชเรท\": \"240Hz\", \"เวลาตอบสนอง\": \"0.03ms\", \"สี\": \"1 พันล้านสี\", \"การเชื่อมต่อ\": \"1 x HDMI, 1 x DP\", \"เทคโนโลยีซิงค์\": \"AMD FreeSync Premium Pro\", \"การปรับตั้ง\": \"ความสูง, เอียง\", \"การติดตั้ง\": \"VESA 100 x 100 mm\"}', 'monitor19.jpg', 49900.00, 'monitor'),
(60, 'MONITOR XIAOMI MINI LED GAMING MONITOR G PRO 27I (57449)', '{\"ขนาด\": \"27 นิ้ว\", \"ประเภทจอ\": \"IPS\", \"ความละเอียด\": \"2560 x 1440\", \"รีเฟรชเรท\": \"180Hz\", \"เวลาตอบสนอง\": \"1ms\", \"การเชื่อมต่อ\": \"2 x HDMI, 2 x DP\", \"การปรับตั้ง\": \"หมุนซ้าย-ขวา, เอียง, หมุนแนวตั้ง\", \"การติดตั้ง\": \"VESA 75 x 75 mm\"}', 'monitor20.jpg', 11990.00, 'monitor'),
(61, 'WIRELESS HEADPHONE SONY WH-ULT900N ULT WEAR WIRELESS NOISE CANCELLING HEADPHONES (BLACK)', '{\"ดีไซน์\": \"Closed-back\", \"ไดร์เวอร์\": \"40 มม.\", \"ระบบ\": \"Digital Noise Cancelling\", \"เหมาะสำหรับ\": \"ผู้ที่ต้องการฟังเพลงอย่างมีสมาธิ\"}', 'headset1.jpg', 6990.00, 'headset'),
(62, 'WIRELESS HEADPHONE SONY WH-CH520 BLUE (WH-CH520/LZ)', '{\"เทคโนโลยี\": \"DSEE\", \"การปรับแต่งเสียง\": \"ผ่านแอป Sony | Headphones Connect\", \"แบตเตอรี่\": \"50 ชั่วโมง\", \"การเชื่อมต่อ\": \"Bluetooth\", \"รองรับ\": \"การโทรแบบแฮนด์ฟรี\"}', 'headset2.jpg', 1600.00, 'headset'),
(63, 'HEADPHONE SENNHEISER MOMENTUM 4 WIRELESS (BLACK)', '{\"เทคโนโลยี\": \"Adaptive Noise Cancellation, Transparency Mode\", \"แบตเตอรี่\": \"60 ชั่วโมง\", \"การเชื่อมต่อ\": \"Bluetooth 5.2\", \"แบตเตอรี่\": \"700 mAh\", \"เวลาในการชาร์จ\": \"2 ชั่วโมง\"}', 'headset3.jpg', 12990.00, 'headset'),
(64, 'HEADPHONE SENNHEISER ACCENTUM PLUS WIRELESS (WHITE)', '{\"ไดร์เวอร์\": \"37mm\", \"เทคโนโลยี\": \"Adaptive Hybrid ANC\", \"แบตเตอรี่\": \"800 mAh\", \"เวลาในการชาร์จ\": \"3.5 ชั่วโมง\", \"การเชื่อมต่อ\": \"Bluetooth 5.2\"}', 'headset4.jpg', 8990.00, 'headset'),
(65, 'HEADPHONE SENNHEISER ACCENTUM WIRELESS (BLACK)', '{\"ไดร์เวอร์\": \"37mm\", \"เทคโนโลยี\": \"Hybrid Active Noise Cancelling\", \"แบตเตอรี่\": \"800 mAh\", \"เวลาในการชาร์จ\": \"3 ชั่วโมง\", \"การเชื่อมต่อ\": \"Bluetooth 5.2\"}', 'headset5.jpg', 6999.00, 'headset'),
(66, 'BLUETOOTH HEADPHONE PHILIPS TAH5209 (BLACK)', '{\"การเชื่อมต่อ\": \"Bluetooth 5.3\", \"น้ำหนัก\": \"เบา\", \"แบตเตอรี่\": \"65 ชั่วโมง\", \"รองรับ\": \"การโทรแบบชัดเจน\"}', 'headset6.jpg', 2290.00, 'headset'),
(67, 'WIRELESS HEADPHONES PHILIPS TAH6506BK/00 (BLACK)', '{\"การเชื่อมต่อ\": \"Bluetooth 5.0\", \"เทคโนโลยี\": \"การขจัดเสียงรบกวน\", \"น้ำหนัก\": \"บางและเบา\", \"รองรับ\": \"การจับคู่แบบหลายจุด\"}', 'headset7.jpg', 2390.00, 'headset'),
(68, 'WIRELESS HEADPHONE BOWERS & WILKINS PX7 S2 (BLUE)', '{\"เทคโนโลยี\": \"ระบบตัดเสียงรบกวนแบบไฮบริด\", \"รองรับ\": \"การเปิดรับเสียงภายนอก\", \"เซ็นเซอร์\": \"ตรวจจับการสวมใส่\", \"การเชื่อมต่อ\": \"USB-C\"}', 'headset8.jpg', 17500.00, 'headset'),
(69, 'WIRELESS BONE CONDUCTION HEADPHONE CREATIVE OUTLIER FREE PRO (MIDNIGHT BLUE)', '{\"เทคโนโลยี\": \"Bone Conduction\", \"กันน้ำ\": \"IPX8\", \"หน่วยความจำ\": \"8GB\", \"แบตเตอรี่\": \"10 ชั่วโมง\", \"การเชื่อมต่อ\": \"Bluetooth 5.3\"}', 'headset9.jpg', 3990.00, 'headset'),
(70, 'หูฟัง HyperX Cloud Earbuds II', '{\"การตอบสนองความถี่\": \"20Hz-20kHz\", \"ความต้านทาน\": \"65.2ohm\", \"ความไว\": \"105dBSPL/mW\", \"การเชื่อมต่อ\": \"3.5mm\", \"รองรับ\": \"PC, Mobile, Nintendo Switch\"}', 'headset10.jpg', 1090.00, 'headset'),
(71, 'หูฟัง HyperX Gaming Cloud III', '{\"การตอบสนองความถี่\": \"10Hz-21kHz\", \"ความต้านทาน\": \"16ohm\", \"ความไว\": \"42dBV\", \"การเชื่อมต่อ\": \"3.5mm, USB-A, USB-C\", \"รองรับ\": \"PC, PS5, Xbox, Nintendo Switch, Mac, Mobile\"}', 'headset11.jpg', 2690.00, 'headset'),
(72, 'หูฟัง HyperX Gaming Headset Cloud Alpha 4P5L1AB-UUF สีแดง', '{\"การตอบสนองความถี่\": \"13Hz-27,000Hz\", \"ความต้านทาน\": \"65 ohm\", \"ความไว\": \"43dBV\", \"ไดร์เวอร์\": \"50mm\", \"การเชื่อมต่อ\": \"3.5mm\", \"รองรับ\": \"PC, PS4, Xbox, Nintendo Switch\"}', 'headset12.jpg', 2190.00, 'headset'),
(73, 'หูฟังไร้สาย Razer Hammerhead True Wireless Pro สีดำ', '{\"การตอบสนองความถี่\": \"20 Hz – 20 kHz\", \"ความต้านทาน\": \"16ohm\", \"ความไวไมโครโฟน\": \"-26 dBFS\", \"การเชื่อมต่อ\": \"Bluetooth5.2\", \"แบตเตอรี่\": \"20 ชั่วโมง\", \"รองรับ\": \"THX\"}', 'headset13.jpg', 5490.00, 'headset'),
(74, 'หูฟังไร้สาย Asus ROG Delta II Wireless headset สีดำ', '{\"การเชื่อมต่อ\": \"Wireless, Bluetooth\", \"ความต้านทาน\": \"32 ohm\", \"รองรับ\": \"PC, IOS, Nintendo Switch, PlayStation4 and5\", \"แบตเตอรี่\": \"1800 mAh\", \"น้ำหนัก\": \"0.318 Kg\"}', 'headset14.jpg', 7590.00, 'headset'),
(75, 'หูฟังอินเอียร์ Steelseries TUSQ สีดำ', '{\"การเชื่อมต่อ\": \"USB-A, USB-C\", \"รองรับ\": \"PC, MAC, PlayStation, Xbox, Android, Switch\", \"การตอบสนองความถี่\": \"20–20000 Hz\", \"ความไวไมโครโฟน\": \"-44 dBV/Pa\", \"สาย\": \"3.5mm 1.2 m\"}', 'headset15.jpg', 1200.00, 'headset'),
(76, 'หูฟังไร้สาย Razer Gaming Kraken Quartz Kitty Edition', '{\"การตอบสนองความถี่\": \"20 Hz - 20000 Hz\", \"ความต้านทาน\": \"32 Ohms\", \"การเชื่อมต่อ\": \"Bluetooth\", \"รองรับ\": \"PC\", \"น้ำหนัก\": \"0.40 Kg\"}', 'headset16.jpg', 1200.00, 'headset'),
(77, 'แผ่นรองเมาส์ SteelSeries QcK Heavy Mousepad M', '{\"ขนาด\": \"450 x 400 มม.\", \"ความหนา\": \"6 มม.\", \"พื้นผิว\": \"นุ่ม\", \"เหมาะสำหรับ\": \"ใช้งานทั่วไป\"}', 'mousepad1.jpg', 690.00, 'mouse_pad'),
(78, 'แผ่นรองเมาส์ Razer Gigantus v2 Cloth Gaming Mouse Pad Medium', '{\"ขนาด\": \"4 ขนาด\", \"พื้นผิว\": \"Micro Fiber\", \"คุณสมบัติ\": \"กันลื่น, หนานุ่ม\", \"โลโก้\": \"Razer\"}', 'mousepad2.jpg', 329.00, 'mouse_pad'),
(79, 'แผ่นรองเมาส์ Gamesense Radar Mousepad B/W', '{\"พื้นผิว\": \"ไมโครไฟเบอร์\", \"คุณสมบัติ\": \"ทนความชื้น, เย็บขอบ\", \"ขนาด\": \"50 x 50 x 0.3 cm\"}', 'mousepad3.jpg', 1890.00, 'mouse_pad'),
(80, 'แผ่นรองเมาส์ SteelSeries QcK Prism XL Destiny 2: Lightfall Edition Mousepad', '{\"ขนาด\": \"XL\", \"พื้นผิว\": \"Soft Cloth\", \"ขนาด\": \"90 x 30 x 0.4 cm\"}', 'mousepad4.jpg', 2990.00, 'mouse_pad'),
(81, 'แผ่นรองเมาส์ Gamesense Radar 4mm Mousepad Purple', '{\"ประเภท\": \"Control\", \"ความหนา\": \"4 มม.\", \"พื้นผิว\": \"ไมโครไฟเบอร์\", \"ขนาด\": \"50 x 50 x 0.4 cm\"}', 'mousepad5.jpg', 1890.00, 'mouse_pad'),
(82, 'แผ่นรองเมาส์ Akko World Tour London Mousepad', '{\"พื้นผิว\": \"Soft Cloth\", \"ขนาด\": \"90 x 40 x 0.4 cm\"}', 'mousepad6.jpg', 650.00, 'mouse_pad'),
(83, 'แผ่นรองเมาส์ RAZER GIGANTUS V2 M MINECRAFT EDITION - GREEN-BLACK', '{\"ขนาด\": \"360 x 275 มม.\", \"ความหนา\": \"3 มม.\", \"พื้นผิว\": \"นุ่ม\", \"ประเภท\": \"แบบนุ่ม\"}', 'mousepad7.webp', 1190.00, 'mouse_pad'),
(84, 'แผ่นรองเมาส์ EGA MOUSEMAT SUKUNA SKN-MM1', '{\"ฐาน\": \"ยางธรรมชาติ\", \"การเย็บ\": \"360° Anti Fraying\", \"พื้นผิว\": \"ไมโครไฟเบอร์\", \"คุณสมบัติ\": \"กันน้ำ\"}', 'mousepad8.webp', 890.00, 'mouse_pad'),
(85, 'แผ่นรองเมาส์ GLORIOUS XXL EXTENDED (STEALTH) (G-XXL-STEALTH)', '{\"ขนาด\": \"914 x 457 x 3 มม.\", \"วัสดุ\": \"โฟมคุณภาพสูง\", \"ฐาน\": \"ยางกันลื่น\", \"พื้นผิว\": \"เรียบลื่น\", \"คุณสมบัติ\": \"ซักเครื่องได้\"}', 'mousepad9.webp', 1590.00, 'mouse_pad'),
(86, 'แผ่นรองเมาส์ SARU DMX-1', '{\"ขนาด\": \"450 x 450 x 4 มม.\", \"พื้นผิว\": \"ผ้าโพลี\", \"วัสดุ\": \"Speed\", \"ความหนา\": \"4 มม.\"}', 'mousepad10.webp', 590.00, 'mouse_pad'),
(87, 'ไมโครโฟน HyperX Gaming Quadcast2 (872V1AA)', '{\"การตอบสนองความถี่\": \"20 - 20,000 Hz\", \"ชนิด\": \"Three 14mm electret condenser capsules\", \"การเชื่อมต่อ\": \"USB\", \"รูปแบบการรับเสียง\": \"Omni Directional, Cardioid Directional, Bi-directional, Stereo\", \"ซอฟต์แวร์\": \"NGENUITY\", \"น้ำหนัก\": \"0.35 กก.\"}', 'mic1.webp', 5390.00, 'mic'),
(88, 'ไมโครโฟน Asus Gaming Rog Carnyx', '{\"การตอบสนองความถี่\": \"20 - 20,000 Hz\", \"ชนิด\": \"คอนเดนเซอร์\", \"การเชื่อมต่อ\": \"USB\", \"ซอฟต์แวร์\": \"Asus Rura Sync\", \"รองรับ\": \"PC, Mac\", \"น้ำหนัก\": \"0.7 กก.\"}', 'mic2.webp', 4690.00, 'mic'),
(89, 'ไมโครโฟน Elgato Wave 3', '{\"ความละเอียด\": \"24 บิต\", \"อัตราตัวอย่าง\": \"48 / 96 kHz\", \"การตอบสนองความถี่\": \"70 - 20000 Hz\", \"ความไว\": \"-25dBFS ถึง 15dBFS\", \"SPL สูงสุด\": \"120dB\", \"การเชื่อมต่อ\": \"USB-C\", \"น้ำหนัก\": \"0.4 กก.\"}', 'mic3.webp', 4990.00, 'mic'),
(90, 'ไมโครโฟน HyperX Gaming Quadcast2 (9A273AA)', '{\"การตอบสนองความถี่\": \"20 - 20,000 Hz\", \"ชนิด\": \"Three 14mm electret condenser capsules\", \"การเชื่อมต่อ\": \"USB\", \"รูปแบบการรับเสียง\": \"Cardioid, Omnidirectional, Bidirectional, Stereo\", \"ซอฟต์แวร์\": \"NGENUITY\", \"รองรับ\": \"PC, Laptop, PS4, PS5\", \"น้ำหนัก\": \"0.45 กก.\"}', 'mic4.webp', 7790.00, 'mic'),
(91, 'ไมโครโฟน Elgato Gaming Wave DX 0MAH9901 สีดำ', '{\"การตอบสนองความถี่\": \"50 - 15000Hz\", \"ชนิด\": \"Condenser Microphone\", \"การเชื่อมต่อ\": \"3-Pin XLR + USB-C\", \"ความไว\": \"2.5 mV/Pa, -52 dbV/Pa\", \"รองรับ\": \"Windows, Mac\", \"น้ำหนัก\": \"0.45 กก.\"}', 'mic5.webp', 3390.00, 'mic'),
(92, 'ไมโครโฟน Razer Seiren Mini USB RZ19-03450200-R3M1 สีชมพู (Quartz)', '{\"การตอบสนองความถี่\": \"20 - 20,000 Hz\", \"ชนิด\": \"Condenser\", \"การเชื่อมต่อ\": \"USB Type-A\", \"รูปแบบการรับเสียง\": \"Supercardioid\", \"ซอฟต์แวร์\": \"Razer Synapse\", \"รองรับ\": \"PC, Laptop, Mac\", \"น้ำหนัก\": \"0.28 กก.\"}', 'mic6.webp', 1350.00, 'mic'),
(93, 'ไมโครโฟน Elgato Wave 1 (10MAA9901) สีดำ', '{\"ความละเอียด\": \"24 บิต\", \"การตอบสนองความถี่\": \"70 - 20000 Hz\", \"ควบคุมเอาต์พุต\": \"9 แชนแนล\", \"การเชื่อมต่อ\": \"USB-C\", \"น้ำหนัก\": \"0.31 กก.\"}', 'mic7.webp', 4590.00, 'mic'),
(94, 'ไมโครโฟน HyperX Duocast USB Microphone', '{\"การตอบสนองความถี่\": \"20 - 20,000 Hz\", \"ชนิด\": \"คอนเดนเซอร์\", \"การเชื่อมต่อ\": \"USB\", \"รูปแบบการรับเสียง\": \"Omni Directional, Cardioid Directional\", \"ซอฟต์แวร์\": \"NGENUITY\", \"รองรับ\": \"PC, PS4, PS5, Mac\", \"น้ำหนัก\": \"0.80 กก.\"}', 'mic8.webp', 2590.00, 'mic'),
(95, 'ไมโครโฟน Onikuma Hoko RGB M730 Black', '{\"คุณสมบัติ\": \"ไฟ RGB, แผ่นกันเสียง, ปรับระดับได้, ถอดไมค์ได้\", \"เหมาะสำหรับ\": \"สตรีมมิ่ง, พอดแคสต์, เล่นเกม\"}', 'mic9.webp', 1190.00, 'mic'),
(96, '10.ไมโครโฟน Signo Condenser Microphone Maxxon MP-705 Black', '{\"คุณสมบัติ\": \"ดีเยี่ยม เหมาะสำหรับสตรีมมิ่ง, พ็อดคาสต์, ร้องเพลง และเล่นดนตรี\", \"วัสดุ\": \"ทำจากอลูมิเนียมคุณภาพดี\" , \"การเชื่อมต่อ\": \"USB 2.0 แบบ TYPE-C\" , \"ช่องสำหรับต่อหูฟัง\":  \"รองรับการเชื่อมต่อหูฟัง\" , \"ขาตั้งไมค์\": \"สามารถถอดออกได้\" }', 'MIC10.webp', 1390.00, 'mic');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('Admin','user') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password_hash`, `email`, `role`, `created_at`) VALUES
(1, 'jaono', '$2y$10$/gzjUOJQciikDoXL49D8KuUGwZ78T5okK6OkGR5PvQUVeszLrwQGG', 'nat@gmail.com', 'user', '2025-03-14 19:33:17'),
(3, 'admin', '$2y$10$uFjIaje13MMTSw43vRe10.1O6YM/PtkZsgcgAHOqH3fprE71/2BYe', 'admin@ad.com', 'Admin', '2025-03-17 18:27:12'),
(5, 'ppr', '$2y$10$GOcpvqD6gjV0ZxCfb0lfe.ufrwzF0/95QqB.LXExMk1Ug1rHK8bem', 'pp@gmail.com', 'user', '2025-03-18 11:10:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `address`
--
ALTER TABLE `address`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `bookmark`
--
ALTER TABLE `bookmark`
  ADD PRIMARY KEY (`bookmark_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_orders_address` (`address_id`),
  ADD KEY `fk_orders_payment` (`payment_id`);

--
-- Indexes for table `orders_item`
--
ALTER TABLE `orders_item`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `address`
--
ALTER TABLE `address`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `bookmark`
--
ALTER TABLE `bookmark`
  MODIFY `bookmark_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `orders_item`
--
ALTER TABLE `orders_item`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `address`
--
ALTER TABLE `address`
  ADD CONSTRAINT `address_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `bookmark`
--
ALTER TABLE `bookmark`
  ADD CONSTRAINT `bookmark_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookmark_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_address` FOREIGN KEY (`address_id`) REFERENCES `address` (`address_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_orders_payment` FOREIGN KEY (`payment_id`) REFERENCES `payment` (`payment_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders_item`
--
ALTER TABLE `orders_item`
  ADD CONSTRAINT `orders_item_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_item_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
