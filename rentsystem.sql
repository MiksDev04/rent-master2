-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 10, 2025 at 01:51 PM
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
-- Database: `rentsystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

CREATE TABLE `properties` (
  `property_id` int(11) NOT NULL,
  `property_name` varchar(100) NOT NULL,
  `property_location` varchar(200) NOT NULL,
  `property_date_created` date NOT NULL,
  `property_description` varchar(1000) NOT NULL,
  `property_image` varchar(500) NOT NULL,
  `property_status` enum('available','unavailable') NOT NULL DEFAULT 'available',
  `property_rental_price` decimal(10,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`property_id`, `property_name`, `property_location`, `property_date_created`, `property_description`, `property_image`, `property_status`, `property_rental_price`) VALUES
(1, '184 Sur Place, Los Angeles', 'Sta. Lucia Dolores, Qeuzon', '2025-03-17', '184 Sur Place is a 2,800 sq. ft. home on a 7,000 sq. ft. lot in Los Angeles, featuring 4 bedrooms, 3.5 bathrooms, and 12-foot ceilings. The 600 sq. ft. open living area is spacious and bright, while the 300 sq. ft. kitchen boasts quartz countertops and premium appliances. A 2-car garage, landscaped backyard (1,500 sq. ft.), and covered patio (250 sq. ft.) ensure comfort and modern luxury.', '/rent-master2/admin/assets/properties/house-1836070_1920.jpg', 'available', 12000),
(2, '324 Tar Place, Pune', 'Bulakin 2 Dolores, Qeuzon', '2025-03-18', '324 Tara Place is a 2,500 sq. ft. home on a 6,000 sq. ft. lot, offering 4 bedrooms, 3 bathrooms, and 10-foot ceilings. The 500 sq. ft. living area is bright and spacious, while the 250 sq. ft. kitchen features quartz countertops and stainless-steel appliances. A 2-car garage, landscaped backyard (1,200 sq. ft.), and covered patio (200 sq. ft.) provide comfort, elegance, and modern living.', '/rent-master2/admin/assets/properties/residence-2219972_1920.jpg', 'available', 5000),
(37, 'My House', 'Dolores, Quezon, Philippines', '2025-04-08', 'This is the best house in Dolores, Quezon', '/rent-master2/admin/assets/properties/florida-1744694_1920.jpg', 'available', 7000);

-- --------------------------------------------------------

--
-- Table structure for table `tenants`
--

CREATE TABLE `tenants` (
  `tenant_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `user_email` varchar(100) NOT NULL,
  `user_password` varchar(100) DEFAULT NULL,
  `user_phone_number` varchar(50) NOT NULL,
  `user_address` varchar(100) NOT NULL,
  `user_description` varchar(2000) DEFAULT NULL,
  `user_image` varchar(500) DEFAULT NULL,
  `user_role` enum('tenant','landlord','visitor') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `user_email`, `user_password`, `user_phone_number`, `user_address`, `user_description`, `user_image`, `user_role`) VALUES
(1, 'Marc Eihenburg', 'eihenburg@gmail.com', 'password123', '0945-765-3567', 'Makati City', 'Marc is a young professional working in the IT industry. He recently moved to the city for career growth and is looking for a rental home that suits his quiet and independent lifestyle. He values responsibility and ensures that his financial obligations, including rent and utilities, are always met on time.', '/rent-master2/admin/assets/tenants/man-8741800_1280.jpg', 'visitor'),
(2, 'George Peterson', 'georgepetersong@gmail.com', 'password123', '0934-721-9547', 'Quezon City', 'George is a freelance graphic designer who works remotely. He prefers a well-maintained and organized living space that fosters creativity and productivity. His flexible schedule allows him to keep the property in excellent condition, and he takes pride in being a responsible tenant who respects the rules of the rental home.', '/rent-master2/admin/assets/tenants/ai-generated-9009342_1280.jpg', 'visitor'),
(9, 'Miko Gapasan', 'mikogapasa04@gmail.com', 'miko1234', '0995-064-4707', 'Dolores, Quezon, Philippines', 'I am the strongest', '/rent-master2/admin/assets/tenants/Minimalist Modern Professional CV Resume.png', 'visitor');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`property_id`);

--
-- Indexes for table `tenants`
--
ALTER TABLE `tenants`
  ADD PRIMARY KEY (`tenant_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `property_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `tenants`
--
ALTER TABLE `tenants`
  MODIFY `tenant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tenants`
--
ALTER TABLE `tenants`
  ADD CONSTRAINT `tenants_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `tenants_ibfk_2` FOREIGN KEY (`property_id`) REFERENCES `properties` (`property_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
