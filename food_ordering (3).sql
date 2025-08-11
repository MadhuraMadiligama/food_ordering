-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307
-- Generation Time: Aug 04, 2025 at 06:16 AM
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
-- Database: `food_ordering`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `email`, `password`) VALUES
(1, 'admin@example.com', '$2y$10$/QTWTrIFagGE2adtPpcFwOv8ujQznb2btIpEMffcV7KRp4097sz46');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `food_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Rice'),
(2, 'Drinks'),
(3, 'Snacks');

-- --------------------------------------------------------

--
-- Table structure for table `food_items`
--

CREATE TABLE `food_items` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `category` varchar(50) DEFAULT 'Uncategorized',
  `new_category_id` int(11) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `food_items`
--

INSERT INTO `food_items` (`id`, `name`, `description`, `price`, `image`, `category_id`, `created_at`, `category`, `new_category_id`, `stock`) VALUES
(8, 'bun', 'big', 100.00, '../assets/uploads/1749625474_Great Buns 1.jpg', 3, '2025-06-08 08:00:13', 'Uncategorized', NULL, 48),
(10, 'Burger Bun', 'chees+veggis', 250.00, '../assets/uploads/1750050671_l-intro-1693940201.jpg', 3, '2025-06-08 08:15:37', 'Uncategorized', NULL, 0),
(11, 'Burger bun', 'Chiken', 300.00, '../assets/uploads/1750013185_l-intro-1693940201.jpg', 3, '2025-06-08 08:29:32', 'Uncategorized', NULL, 30),
(12, 'Submareen', 'Crispi', 250.00, '../assets/uploads/OIP.jpeg', 3, '2025-06-08 08:34:40', 'Uncategorized', NULL, 0),
(16, 'EGB', '1L', 150.00, '../assets/uploads/1750007880_OIP.webp', 2, '2025-06-11 07:16:30', 'Uncategorized', NULL, 1),
(19, 'Rice', 'chiken', 550.00, '../assets/uploads/1750007529_Red-Beans-and-Rice.jpg', 1, '2025-06-15 15:36:23', 'Uncategorized', NULL, 24),
(21, 'coc', 'cool', 100.00, '../assets/uploads/1750007500_coca-cola-the-coca-cola-company-bottle-drink.jpg', 2, '2025-06-15 15:47:18', 'Uncategorized', NULL, 18),
(23, 'Mountain Dew', 'cool', 120.00, '../assets/uploads/1750008163_OIP (1).webp', 2, '2025-06-15 17:22:43', 'Uncategorized', NULL, 49),
(24, 'Rice', 'veg', 400.00, '../assets/uploads/1750015416_Bak Kwa fried rice 011.jpg', 1, '2025-06-15 19:23:36', 'Uncategorized', NULL, 10),
(25, 'Pizza', 'chees+tomato', 900.00, '../assets/uploads/1750016628_OIP (2).webp', 3, '2025-06-15 19:43:48', 'Uncategorized', NULL, 6),
(26, 'ABC', '', 1000.00, '../assets/uploads/1750048457_Food-fried-bun_2560x1600.jpg', 3, '2025-06-16 04:34:17', 'Uncategorized', NULL, 1000);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL DEFAULT 'Pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `payment_status` varchar(50) NOT NULL DEFAULT 'Unpaid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `order_date`, `status`, `payment_method`, `created_at`, `name`, `phone`, `address`, `total`, `notes`, `payment_status`) VALUES
(2, 1, '2025-06-08 15:00:20', 'Pending', NULL, '2025-06-08 09:30:20', 'Madhura Madiligama', '0758936236', '255asdf', 0.03, NULL, 'Unpaid'),
(13, 1, '2025-06-10 18:59:58', 'pending', NULL, '2025-06-10 13:29:58', NULL, NULL, NULL, 100.00, NULL, 'Unpaid'),
(14, 1, '2025-06-10 20:22:30', 'pending', NULL, '2025-06-10 14:52:30', NULL, NULL, NULL, 100.00, NULL, 'Unpaid'),
(15, 1, '2025-06-10 20:35:14', 'Preparing', NULL, '2025-06-10 15:05:14', NULL, NULL, NULL, 100.00, NULL, 'Unpaid'),
(16, 1, '2025-06-10 20:46:29', 'pending', NULL, '2025-06-10 15:16:29', NULL, NULL, NULL, 300.00, NULL, 'Unpaid'),
(17, 1, '2025-06-11 11:19:38', 'Completed', NULL, '2025-06-11 05:49:38', NULL, NULL, NULL, 250.00, NULL, 'Unpaid'),
(18, 1, '2025-06-11 11:22:37', 'Completed', NULL, '2025-06-11 05:52:37', NULL, NULL, NULL, 100.00, NULL, 'Unpaid'),
(19, 1, '2025-06-11 11:33:52', 'paid', 'card', '2025-06-11 06:03:52', NULL, NULL, NULL, 250.00, NULL, 'Unpaid'),
(20, 1, '2025-06-11 11:41:51', 'pending', NULL, '2025-06-11 06:11:51', NULL, NULL, NULL, 300.00, NULL, 'Unpaid'),
(21, 1, '2025-06-11 11:42:34', 'pending', NULL, '2025-06-11 06:12:34', NULL, NULL, NULL, 100.00, NULL, 'Unpaid'),
(22, 1, '2025-06-11 11:48:34', 'pending', NULL, '2025-06-11 06:18:34', NULL, NULL, NULL, 100.00, NULL, 'Unpaid'),
(23, 1, '2025-06-11 11:54:41', 'paid', 'cod', '2025-06-11 06:24:41', NULL, NULL, NULL, 250.00, NULL, 'Unpaid'),
(24, 1, '2025-06-11 11:57:39', 'paid', 'card', '2025-06-11 06:27:39', NULL, NULL, NULL, 100.00, NULL, 'Unpaid'),
(25, 1, '2025-06-11 11:58:28', 'paid', 'transfer', '2025-06-11 06:28:28', NULL, NULL, NULL, 300.00, NULL, 'Unpaid'),
(26, 3, '2025-06-11 12:06:19', 'paid', 'cod', '2025-06-11 06:36:19', NULL, NULL, NULL, 250.00, NULL, 'Unpaid'),
(27, 3, '2025-06-11 14:09:26', 'paid', 'cod', '2025-06-11 08:39:26', NULL, NULL, NULL, 300.00, NULL, 'Unpaid'),
(28, 3, '2025-06-11 14:10:04', 'Completed', 'card', '2025-06-11 08:40:04', NULL, NULL, NULL, 100.00, NULL, 'Unpaid'),
(29, 3, '2025-06-11 14:38:12', 'paid', 'cod', '2025-06-11 09:08:12', NULL, NULL, NULL, 250.00, NULL, 'Unpaid'),
(30, 3, '2025-06-11 14:51:15', 'pending', NULL, '2025-06-11 09:21:15', NULL, NULL, NULL, 100.00, NULL, 'Unpaid'),
(31, 3, '2025-06-11 14:51:15', 'Pending', NULL, '2025-06-11 09:21:15', NULL, NULL, NULL, NULL, 'extra egg', 'Unpaid'),
(32, 3, '2025-06-11 14:51:42', 'pending', NULL, '2025-06-11 09:21:42', NULL, NULL, NULL, 250.00, NULL, 'Unpaid'),
(33, 3, '2025-06-11 14:51:42', 'Pending', NULL, '2025-06-11 09:21:42', NULL, NULL, NULL, NULL, 'egg', 'Unpaid'),
(34, 3, '2025-06-11 14:52:01', 'pending', NULL, '2025-06-11 09:22:01', NULL, NULL, NULL, 250.00, NULL, 'Unpaid'),
(36, 3, '2025-06-11 14:52:35', 'pending', NULL, '2025-06-11 09:22:35', NULL, NULL, NULL, 300.00, NULL, 'Unpaid'),
(37, 3, '2025-06-11 14:52:35', 'paid', 'cod', '2025-06-11 09:22:35', NULL, NULL, NULL, NULL, 'egg', 'Unpaid'),
(38, 3, '2025-06-11 14:53:32', 'pending', NULL, '2025-06-11 09:23:32', NULL, NULL, NULL, 100.00, NULL, 'Unpaid'),
(39, 3, '2025-06-11 15:02:40', 'Preparing', NULL, '2025-06-11 09:32:40', NULL, NULL, NULL, 250.00, 'rgg', 'Unpaid'),
(40, 3, '2025-06-11 21:06:07', 'Cancelled', 'transfer', '2025-06-11 15:36:07', NULL, NULL, NULL, 100.00, NULL, 'Unpaid'),
(41, 3, '2025-06-11 21:10:31', 'Completed', 'card', '2025-06-11 15:40:31', NULL, NULL, NULL, 250.00, NULL, 'Unpaid'),
(42, 3, '2025-06-12 21:58:26', 'paid', 'cod', '2025-06-12 16:28:26', NULL, NULL, NULL, 300.00, 'spice', 'Unpaid'),
(43, 5, '2025-06-13 23:44:19', 'Completed', 'card', '2025-06-13 18:14:19', NULL, NULL, NULL, 100.00, '11', 'Unpaid'),
(44, 6, '2025-06-14 18:36:23', 'Completed', 'card', '2025-06-14 13:06:23', NULL, NULL, NULL, 1000.00, '', 'Unpaid'),
(45, 6, '2025-06-14 18:54:57', 'Completed', 'cod', '2025-06-14 13:24:57', NULL, NULL, NULL, 500.00, 'extra egg', 'Unpaid'),
(46, 6, '2025-06-14 19:00:02', 'paid', 'cod', '2025-06-14 13:30:02', NULL, NULL, NULL, 150.00, '', 'Unpaid'),
(47, 6, '2025-06-14 19:10:46', 'paid', 'transfer', '2025-06-14 13:40:46', 'new', '0784455125', '123', 100.00, 'extra egg', 'Unpaid'),
(48, 6, '2025-06-14 19:15:29', 'paid', 'transfer', '2025-06-14 13:45:29', 'Tharushani', '0784455125', 'gampola', 350.00, '', 'Unpaid'),
(49, 6, '2025-06-14 19:29:55', 'paid', 'transfer', '2025-06-14 13:59:55', 'Madhura Madiligama', '0758936236', '255asdf', 300.00, '', 'Unpaid'),
(50, 6, '2025-06-14 19:33:18', 'Completed', 'card', '2025-06-14 14:03:18', 'Tharushani', '0784455125', 'gampola', 300.00, '`1', 'Unpaid'),
(51, 6, '2025-06-14 19:40:32', 'paid', 'transfer', '2025-06-14 14:10:32', 'Tharushani', '0784455125', 'gampola', 250.00, 'no', 'Unpaid'),
(52, 6, '2025-06-14 19:41:29', 'Cancelled', 'transfer', '2025-06-14 14:11:29', 'Madhura Madiligama', '0758936236', 'x', 150.00, 'q', 'Unpaid'),
(53, 6, '2025-06-14 20:11:11', 'Preparing', 'card', '2025-06-14 14:41:11', 'Tharushani', '0784455125', 'gampola', 500.00, 'cheess', 'Unpaid'),
(54, 6, '2025-06-14 22:06:31', 'Completed', 'transfer', '2025-06-14 16:36:31', 'Madhura Madiligama', '0758936236', '255asdf', 300.00, 'q', 'Unpaid'),
(55, 6, '2025-06-14 22:07:42', 'Cancelled', NULL, '2025-06-14 16:37:42', 'new', '0784455125', '123', 250.00, '', 'Unpaid'),
(56, 6, '2025-06-14 22:11:39', 'Cancelled', NULL, '2025-06-14 16:41:39', 'Tharushani', '0784455125', 'gampola', 150.00, 'q', 'Unpaid'),
(57, 6, '2025-06-14 22:22:24', 'Cancelled', NULL, '2025-06-14 16:52:24', 'kasun', '0778495625', 'no:123', 1000.00, '98', 'Unpaid'),
(58, 6, '2025-06-14 22:25:02', 'Cancelled', 'transfer', '2025-06-14 16:55:02', 'Madhura Madiligama', '0758936236', 'x', 100.00, 'q', 'Unpaid'),
(59, 6, '2025-06-14 22:38:44', 'Cancelled', NULL, '2025-06-14 17:08:44', 'Madhura Madiligama', '0758936236', 'x', 250.00, 'q', 'Unpaid'),
(60, 6, '2025-06-15 21:07:41', 'Completed', 'transfer', '2025-06-15 15:37:41', 'Madhura Madiligama', '0758936236', 'x', 2200.00, 'q', 'Unpaid'),
(61, 6, '2025-06-15 21:17:38', 'Completed', 'transfer', '2025-06-15 15:47:38', 'Madhura Madiligama', '0758936236', 'x', 100.00, 'q', 'Unpaid'),
(62, 6, '2025-06-15 22:06:35', 'paid', 'transfer', '2025-06-15 16:36:35', 'Madhura Madiligama', '0758936236', '255asdf', 100.00, '', 'Unpaid'),
(63, 3, '2025-06-16 00:14:56', 'paid', 'transfer', '2025-06-15 18:44:56', 'Tharushani', '0784455125', 'gampola', 150.00, '', 'Unpaid'),
(64, 8, '2025-06-16 00:40:56', 'paid', 'card', '2025-06-15 19:10:56', 'kasun', '0784512159', 'peradeniya', 100.00, 'cool', 'Unpaid'),
(65, 10, '2025-06-16 01:25:45', 'Cancelled', 'transfer', '2025-06-15 19:55:45', 'kasun', '0784512159', 'peradeniya', 100.00, '', 'Unpaid'),
(66, 11, '2025-06-16 09:27:52', 'paid', 'card', '2025-06-16 03:57:52', 'mashi', '075432321', '108/1, Rathubokkuwa', 100.00, 'chees', 'Unpaid'),
(67, 12, '2025-06-16 10:31:45', 'paid', 'transfer', '2025-06-16 05:01:45', 'Madhura Madiligama', '0758936236', '255asdf', 251000.00, 'as', 'Unpaid'),
(68, 12, '2025-06-16 10:32:31', 'paid', 'transfer', '2025-06-16 05:02:31', 'Madhura Madiligama', '0758936236', '255asdf', 100.00, '', 'Unpaid'),
(69, 12, '2025-06-16 10:37:48', 'pending', NULL, '2025-06-16 05:07:48', 'Madhura Madiligama', '0758936236', '255asdf', 150.00, '', 'Unpaid'),
(70, 12, '2025-06-16 10:38:05', 'paid', 'card', '2025-06-16 05:08:05', 'Madhura Madiligama', '0758936236', '255asdf', 150.00, '', 'Unpaid'),
(71, 12, '2025-06-16 10:39:17', 'paid', 'transfer', '2025-06-16 05:09:17', 'Madhura Madiligama', '0758936236', '255asdf', 150.00, '', 'Unpaid'),
(72, 12, '2025-06-16 10:55:53', 'paid', 'transfer', '2025-06-16 05:25:53', 'Madhura Madiligama', '0758936236', '255asdf', 300.00, 'as', 'Unpaid'),
(73, 12, '2025-06-16 11:04:58', 'paid', 'transfer', '2025-06-16 05:34:58', 'Lakshman', '0758936236', '255asdf', 400.00, 'as', 'Unpaid'),
(74, 12, '2025-06-16 11:06:08', 'paid', 'transfer', '2025-06-16 05:36:08', 'Lakshman', '0758936236', 'x', 250000.00, '', 'Unpaid'),
(75, 12, '2025-06-16 11:13:59', 'paid', 'transfer', '2025-06-16 05:43:59', 'Lakshman', '0758936230', 'xc', 900.00, '', 'Unpaid'),
(76, 12, '2025-06-16 11:47:25', 'Completed', 'card', '2025-06-16 06:17:25', 'Lakshman', '0758936230', 'gampola', 202500.00, '', 'Unpaid'),
(77, 12, '2025-06-29 13:08:25', 'paid', 'card', '2025-06-29 07:38:25', 'Lakshman', '0758936230', 'no:221 kandy', 300.00, '', 'Unpaid'),
(78, 12, '2025-06-29 14:38:23', 'pending', NULL, '2025-06-29 09:08:23', 'Lakshman', '0758936230', 'no:221 kandy', 1000.00, '', 'Unpaid'),
(79, 12, '2025-06-29 14:43:10', 'paid', 'transfer', '2025-06-29 09:13:10', 'new3', '0758936236', 'new3', 1000.00, '', 'Unpaid'),
(80, 8, '2025-06-29 21:46:44', 'paid', 'transfer', '2025-06-29 16:16:44', 'kasun', '0758936236', '255asdf', 220.00, '', 'Unpaid'),
(81, 8, '2025-06-29 22:18:55', 'paid', 'card', '2025-06-29 16:48:55', 'kasun', '0784512159', 'peradeniya', 1100.00, '', 'Unpaid'),
(82, 8, '2025-06-29 22:30:53', 'Completed', 'card', '2025-06-29 17:00:53', 'kasun', '0758936236', '255asdf', 1000.00, '', 'Unpaid');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `food_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `food_id`, `quantity`, `price`) VALUES
(17, 13, 8, 1, 100.00),
(18, 14, 8, 1, 100.00),
(19, 15, 8, 1, 100.00),
(20, 16, 8, 3, 100.00),
(21, 17, 10, 1, 250.00),
(22, 18, 8, 1, 100.00),
(23, 19, 10, 1, 250.00),
(25, 21, 8, 1, 100.00),
(26, 22, 8, 1, 100.00),
(27, 23, 12, 1, 250.00),
(28, 24, 8, 1, 100.00),
(29, 25, 11, 1, 300.00),
(30, 26, 10, 1, 250.00),
(31, 27, 16, 2, 150.00),
(32, 28, 8, 1, 100.00),
(33, 29, 12, 1, 250.00),
(34, 30, 8, 1, 100.00),
(35, 32, 10, 1, 250.00),
(36, 34, 10, 1, 250.00),
(37, 36, 11, 1, 300.00),
(38, 38, 8, 1, 100.00),
(39, 39, 10, 1, 250.00),
(40, 40, 8, 1, 100.00),
(41, 41, 10, 1, 250.00),
(42, 42, 16, 2, 150.00),
(43, 43, 8, 1, 100.00),
(44, 44, 10, 4, 250.00),
(46, 46, 16, 1, 150.00),
(47, 47, 8, 1, 100.00),
(48, 48, 8, 1, 100.00),
(49, 48, 12, 1, 250.00),
(50, 49, 11, 1, 300.00),
(52, 51, 12, 1, 250.00),
(53, 52, 16, 1, 150.00),
(54, 53, 10, 2, 250.00),
(55, 54, 11, 1, 300.00),
(56, 55, 12, 1, 250.00),
(57, 56, 16, 1, 150.00),
(58, 57, 10, 4, 250.00),
(59, 58, 8, 1, 100.00),
(60, 59, 12, 1, 250.00),
(61, 60, 19, 4, 550.00),
(62, 61, 21, 1, 100.00),
(63, 62, 21, 1, 100.00),
(64, 63, 16, 1, 150.00),
(65, 64, 21, 1, 100.00),
(66, 65, 8, 1, 100.00),
(67, 66, 8, 1, 100.00),
(68, 67, 26, 251, 1000.00),
(69, 68, 8, 1, 100.00),
(70, 69, 16, 1, 150.00),
(71, 70, 16, 1, 150.00),
(72, 71, 16, 1, 150.00),
(73, 72, 8, 3, 100.00),
(74, 73, 8, 4, 100.00),
(75, 74, 26, 250, 1000.00),
(76, 75, 25, 1, 900.00),
(77, 76, 26, 200, 1000.00),
(78, 76, 8, 25, 100.00),
(79, 77, 11, 1, 300.00),
(80, 78, 26, 1, 1000.00),
(81, 79, 26, 1, 1000.00),
(82, 80, 8, 1, 100.00),
(83, 80, 23, 1, 120.00),
(84, 81, 8, 1, 100.00),
(85, 81, 26, 1, 1000.00),
(86, 82, 26, 1, 1000.00);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `food_id` int(11) NOT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `food_id`, `rating`, `comment`, `created_at`) VALUES
(1, 3, 8, 4, 'good', '2025-06-12 22:41:25'),
(2, 3, 8, 5, 'wow', '2025-06-12 22:43:33'),
(3, 3, 10, 5, '', '2025-06-12 22:52:26'),
(4, 3, 10, 4, '', '2025-06-12 22:52:34'),
(5, 3, 10, 4, '', '2025-06-12 22:52:38'),
(6, 3, 11, 3, 'nice', '2025-06-12 23:08:46'),
(7, 1, 16, 5, 'cool', '2025-06-12 23:25:36'),
(8, 1, 16, 4, 'wow', '2025-06-14 13:02:39');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `nic` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `registration_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `name`, `nic`, `email`, `password`, `phone`, `address`, `city`, `contact`, `registration_date`) VALUES
(1, NULL, 'supun', NULL, 'supun@gmail.com', '$2y$10$UGkpvWCFgHPHv2gE/oSja.8dMERQzPky9EdBZaHbMKvAL4HWs347a', NULL, NULL, '', NULL, '2025-06-16 11:56:24'),
(3, NULL, 'Dilki', NULL, 'dilki11@gmail.com', '$2y$10$EqKls7w.OwRZ9IVrnk.E5ubwORGoO5UaSxQGFXel8p4JDDvUlG4Xu', NULL, NULL, '', NULL, '2025-06-16 11:56:24'),
(4, NULL, 'Madhura Madiligama', NULL, 'madu@gmail.com', '$2y$10$ptWBveNgplNmSzcLlAJWD.PM8ZFufrI6v2kIJQgES7AdzkAsEiD4m', '0758936236', '123madu', '', NULL, '2025-06-16 11:56:24'),
(5, NULL, 'new', NULL, 'new@gmail.com', '$2y$10$wq6bkgSZJibRTiN.pIupEufpaHmaSHoXt8RrADhw0qdt4Fc60rHKy', NULL, NULL, '', NULL, '2025-06-16 11:56:24'),
(6, NULL, 'new3', NULL, 'new2@gmail.com', '$2y$10$svBbbLpCe8HSokXZSytc1eBpWERmEH2JSDFx4kAYpFyRzWtqF22Su', NULL, 'no:22 kandy', '', '0754565489', '2025-06-16 11:56:24'),
(7, NULL, 'Tharushani', '777778454154', 'tharu@gmail.com', '$2y$10$my3Y3tJyxXEZ9eOv4sUUhec1GP2qGXVgu2SH5a7GzDI8T4CwYqsWO', NULL, 'gampola', '', '0758936230', '2025-06-16 11:56:24'),
(8, NULL, 'kasun', '10101010101010', 'kasun1@gmail.com', '$2y$10$o2SgJ1DMbOTCZ.RR6P6KyuIlvINjUVuDgfXYAsfbHCg3HNhLECNse', NULL, 'peradeniya', '', '0784512259', '2025-06-16 11:56:24'),
(9, NULL, 'shaluka rathnayake', '200148795689', 'sha1@gmail.com', '$2y$10$AMtbDRFQP6KFmxFGFDXAPOx3oUOO5mkEL23gBiMsKcMvID8UNkXJ6', NULL, 'waligalla', '', '070254869', '2025-06-16 11:56:24'),
(10, NULL, 'nipun', '200148795655', 'nipun@gmail.com', '$2y$10$P7jYEEnZxQJxuJUhcsyHterBrSebUzuwDVGZZw7oiT1sJcLqgqIzi', NULL, '', '', '070254777', '2025-06-16 11:56:24'),
(11, NULL, 'mashi', '00254675432', 'mashi@gmail.com', '$2y$10$kIm3jmP5VXujuahVVnGCyugZappSrmAlKKB8qmG0C5yzpcHKq9LNe', NULL, '', '', '0756754321', '2025-06-16 11:56:24'),
(12, NULL, 'Lakshman', '45454545445445454', 'lakshman@example.com', '$2y$10$xa.D49eNecTEUElLUHgSCeHegx3/1sLgVPqVFg9KHlD.yJPz.pDZW', NULL, '', '', '0756754321', '2025-06-16 11:56:24'),
(15, NULL, 'laxman', '78978454544', 'laxman@example.com', '$2y$10$lWN1C/R5xrnI6RJ9aZClsewFAQhxLEtggGnreLx1BUFAa9767Hi0e', NULL, 'no:221 kandy', 'japan', '0756754321', '2025-06-17 00:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `food_id` (`food_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `food_items`
--
ALTER TABLE `food_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `new_category_id` (`new_category_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `food_id` (`food_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `food_id` (`food_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `food_items`
--
ALTER TABLE `food_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`food_id`) REFERENCES `food_items` (`id`);

--
-- Constraints for table `food_items`
--
ALTER TABLE `food_items`
  ADD CONSTRAINT `food_items_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `food_items_ibfk_2` FOREIGN KEY (`new_category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`food_id`) REFERENCES `food_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`food_id`) REFERENCES `food_items` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
