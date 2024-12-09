-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 09, 2024 at 10:21 PM
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
-- Database: `microwave_oven`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `checkout`
--

CREATE TABLE `checkout` (
  `checkout_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mobile_no` varchar(15) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `zip_code` varchar(10) NOT NULL,
  `payment_method` enum('credit_card','cash_on_delivery') NOT NULL,
  `card_holder_name` varchar(255) DEFAULT NULL,
  `card_number` varchar(16) DEFAULT NULL,
  `expiry_date` varchar(5) DEFAULT NULL,
  `cvv` varchar(3) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `tax` decimal(10,2) NOT NULL,
  `grand_total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `checkout`
--

INSERT INTO `checkout` (`checkout_id`, `order_id`, `user_id`, `first_name`, `last_name`, `email`, `mobile_no`, `address`, `city`, `state`, `zip_code`, `payment_method`, `card_holder_name`, `card_number`, `expiry_date`, `cvv`, `total_price`, `tax`, `grand_total`) VALUES
(79, 96, 12, 'harshil', 'katrodiya', 'harshilkatrodiya999@gmail.com', '5489222271', '600, Greenfield Ave.', 'Kitchener', 'ON', 'N2C2J9', 'credit_card', 'Harshil Katrodiya', '1234567891234567', '02/30', '111', 200.00, 20.00, 220.00);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total_amount`, `created_at`) VALUES
(96, 12, 220.00, '2024-12-09 21:15:29');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `description`, `price`, `stock`, `image_url`, `created_at`) VALUES
(1, 'LG Solo Microwave Oven', '20L solo microwave oven with energy-saving features and conditions to use at home.', 100.00, 21, '1.jpg', '2024-11-23 21:11:54'),
(2, 'Whirlpool 30L Grill Microwave Oven', 'This 30L microwave oven offers grill functionality, perfect for fast cooking.', 120.00, 5, '2.jpg', '2024-11-23 22:21:06'),
(3, 'Samsung 1.1 cu. ft. Microwave', 'Samsung microwave with 10 power levels and sensor cooking technology.', 90.00, 21, '3.jpg', '2024-11-23 23:25:21'),
(4, 'Panasonic 0.9 cu. ft. Microwave Oven', 'Large capacity microwave with convection cooking and smart inverter.', 99.00, 13, '4.jpg', '2024-11-23 23:26:31'),
(5, 'GE 1.6 cu. ft. Over-the-Range Microwave', 'Over-the-range microwave with easy-to-clean interior and built-in exhaust fan.', 60.00, 77, '5.jpg', '2024-11-23 23:27:16'),
(6, 'Sharp 1.5 cu. ft. Microwave Oven', 'Sharp microwave oven with the auto defrost and reheat functions to use at home.', 124.00, 34, '6.jpg', '2024-11-23 23:27:54'),
(7, 'Breville Smart Microwave Oven', 'Breville microwave with smart technology and sensor reheat functions.', 115.00, 59, '7.jpg', '2024-11-23 23:28:34'),
(8, 'Whirlpool 1.4 cu. ft. Microwave Oven', 'Whirlpool microwave Oven with touch control and quick cooking presets to use at home.', 177.00, 10, '8.jpg', '2024-11-23 23:29:19'),
(9, 'KitchenAid 1.5 cu. ft. Microwave', 'KitchenAid microwave with a sleek design and powerful cooking options.', 50.00, 45, '9.jpg', '2024-11-23 23:30:09'),
(10, 'Cuisinart 1.2 cu. ft. Microwave Oven', 'Cuisinart microwave with 11 power levels and one-touch cooking buttons.', 77.00, 15, '10.jpg', '2024-11-23 23:31:06'),
(11, 'Cuisinart 1.2 cu. ft. Microwave Oven', 'Cuisinart microwave with 11 power levels and one-touch cooking buttons.', 50.00, 52, '11.jpg', '2024-11-23 23:31:51'),
(12, 'LG Convection Microwave Oven', 'LG Convection microwave with baking capabilities for versatile cooking.', 101.00, 32, '12.jpg', '2024-11-23 23:33:16');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `role` enum('admin','user') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `phone_number`, `address`, `created_at`, `role`) VALUES
(1, 'Admin User', 'admin@example.com', '$2y$10$aJCj6cWOw3NCzbHbYp0qXezhJ2DsMMGyxNcsoTAuFd8uSgfv9Xn6i', NULL, NULL, '2024-11-23 20:40:14', 'admin'),
(12, 'Harshil Katrodiya', 'harshilkatrodiya999@gmail.com', '$2y$10$AsmdmHXzzMWzFC.thwIAVewLz9DblyCmNGkXWVQKbP9Etfm4vzfNe', '5489222271', '600, Greenfield Ave.', '2024-12-09 21:13:52', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `checkout`
--
ALTER TABLE `checkout`
  ADD PRIMARY KEY (`checkout_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=132;

--
-- AUTO_INCREMENT for table `checkout`
--
ALTER TABLE `checkout`
  MODIFY `checkout_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `checkout`
--
ALTER TABLE `checkout`
  ADD CONSTRAINT `checkout_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `checkout_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
