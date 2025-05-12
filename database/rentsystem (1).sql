-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 12, 2025 at 02:54 AM
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
-- Table structure for table `amenities`
--

CREATE TABLE `amenities` (
  `amenity_id` int(11) NOT NULL,
  `amenity_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `amenities`
--

INSERT INTO `amenities` (`amenity_id`, `amenity_name`) VALUES
(1, 'WiFi'),
(2, 'Parking'),
(3, 'Air Conditioning'),
(4, 'Swimming Pool'),
(5, 'Gym'),
(6, 'Elevator'),
(7, 'Furnished'),
(8, 'Garden'),
(9, 'Balcony'),
(10, 'Pet-Friendly'),
(11, 'Security System'),
(12, 'CCTV Surveillance'),
(13, 'Fireplace'),
(14, 'Dishwasher'),
(15, 'Washer/Dryer'),
(16, 'Refrigerator'),
(17, 'Microwave'),
(18, 'Sauna'),
(19, 'Spa'),
(20, 'Jacuzzi');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_requests`
--

CREATE TABLE `maintenance_requests` (
  `request_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `category` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `request_date` datetime NOT NULL,
  `status` enum('pending','completed') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `maintenance_requests`
--

INSERT INTO `maintenance_requests` (`request_id`, `tenant_id`, `category`, `description`, `request_date`, `status`) VALUES
(1, 65, 'Appliance Repair', 'hsjhahsAS', '2025-04-26 09:43:21', 'completed'),
(2, 65, 'Plumbing', 'jhjjhh', '2025-04-26 09:43:45', 'completed'),
(3, 65, 'Electrical', 'We are electric', '2025-05-02 10:47:35', 'completed'),
(4, 67, 'Plumbing', 'hello', '2025-05-07 09:09:40', 'completed'),
(5, 67, 'Plumbing', 'hello', '2025-05-10 17:51:53', 'completed'),
(6, 72, 'Electrical', 'We are electric', '2025-05-12 08:42:44', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('payment','maintenance','property','general') NOT NULL DEFAULT 'general',
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `related_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `payment_start_date` date NOT NULL,
  `payment_end_date` date NOT NULL,
  `payment_status` enum('Paid','Pending','Overdue') NOT NULL DEFAULT 'Pending',
  `payment_date` date DEFAULT NULL,
  `payment_method` enum('GCash','Maya','Coins.ph','Bank Transfer','Credit/Debit Card') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `tenant_id`, `payment_start_date`, `payment_end_date`, `payment_status`, `payment_date`, `payment_method`) VALUES
(18, 65, '2025-05-28', '2025-06-27', 'Paid', '2025-04-27', NULL),
(19, 65, '2025-04-27', '2025-05-27', 'Paid', '2025-05-04', 'Maya'),
(25, 67, '2025-05-04', '2025-06-04', 'Pending', '2025-05-04', 'Bank Transfer'),
(53, 72, '2025-05-10', '2025-06-10', 'Paid', '2025-05-11', 'GCash'),
(54, 72, '2025-06-11', '2025-07-10', 'Pending', NULL, NULL);

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
  `property_status` enum('available','unavailable') NOT NULL DEFAULT 'available',
  `property_rental_price` decimal(10,0) NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`property_id`, `property_name`, `property_location`, `property_date_created`, `property_description`, `property_status`, `property_rental_price`, `latitude`, `longitude`) VALUES
(46, 'Super House', 'Abenida Padre Burgos, 659, Ermita, Fifth District, Manila, Capital District, Metro Manila, 1000, Philippines', '2025-04-25', 'This is the best house', 'available', 23454, 14.58923200, 120.98127400),
(49, 'Maplewood Heights Apartment', 'L. Cruz Street, Hagdang Bato Libis, Mandaluyong, Eastern Manila District, Metro Manila, 1550, Philippines', '2024-12-01', 'A spacious 2-bedroom apartment located near major business districts. Comes with parking and 24/7 security.', 'unavailable', 18000, 14.59089300, 121.03431700),
(50, 'Sunrise Condo Unit 5B', 'Naglabrahan, Guimba, Nueva Ecija, Central Luzon, Philippines', '2025-01-15', 'Modern studio unit perfect for young professionals. Features include a gym, pool, and co-working space.', 'unavailable', 22000, 15.67857700, 120.83313000),
(51, 'Palm Tree Villas House', 'Bulakin 2, Dolores, 2nd District, Quezon, Calabarzon, 4326, Philippines', '2025-03-10', 'A family-friendly house with 3 bedrooms, large backyard, and pet-friendly policy. Near schools and markets.', 'available', 25000, 14.01201200, 121.39043900);

-- --------------------------------------------------------

--
-- Table structure for table `property_amenities`
--

CREATE TABLE `property_amenities` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `amenity_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `property_amenities`
--

INSERT INTO `property_amenities` (`id`, `property_id`, `amenity_id`) VALUES
(95, 50, 9),
(96, 50, 12),
(97, 50, 14),
(98, 50, 16),
(110, 49, 9),
(111, 49, 12),
(112, 49, 14),
(113, 49, 15),
(114, 49, 16),
(120, 51, 4),
(121, 51, 5),
(122, 51, 6),
(123, 51, 18),
(124, 51, 19),
(195, 46, 1),
(196, 46, 2),
(197, 46, 4),
(198, 46, 5),
(199, 46, 13),
(200, 46, 20);

-- --------------------------------------------------------

--
-- Table structure for table `property_images`
--

CREATE TABLE `property_images` (
  `property_images_id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `image1` varchar(500) DEFAULT NULL,
  `image2` varchar(500) DEFAULT NULL,
  `image3` varchar(500) DEFAULT NULL,
  `image4` varchar(500) DEFAULT NULL,
  `image5` varchar(500) DEFAULT NULL,
  `image6` varchar(500) DEFAULT NULL,
  `image7` varchar(500) DEFAULT NULL,
  `image8` varchar(500) DEFAULT NULL,
  `image9` varchar(500) DEFAULT NULL,
  `image10` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `property_images`
--

INSERT INTO `property_images` (`property_images_id`, `property_id`, `image1`, `image2`, `image3`, `image4`, `image5`, `image6`, `image7`, `image8`, `image9`, `image10`) VALUES
(12, 46, '/rent-master2/admin/assets/properties/1746278809_holiday-home-177401_1280.jpg', '/rent-master2/admin/assets/properties/1746278809_florida-1744694_1920.jpg', '/rent-master2/admin/assets/properties/1746278809_florida-1744691_1920.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 51, '/rent-master2/admin/assets/properties/1746189409_lifestyle-3107041_1920.jpg', '/rent-master2/admin/assets/properties/1746189409_holiday-home-177401_1920.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 50, '/rent-master2/admin/assets/properties/1746189473_large-home-389271_1280.jpg', '/rent-master2/admin/assets/properties/1746189474_kitchen-9288111_1280.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 49, '/rent-master2/admin/assets/properties/1746189451_florida-1744691_1920.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tenants`
--

CREATE TABLE `tenants` (
  `tenant_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `tenant_status` enum('pending','active','terminated') NOT NULL DEFAULT 'pending',
  `tenant_date_created` date NOT NULL DEFAULT current_timestamp(),
  `tenant_terminated_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tenants`
--

INSERT INTO `tenants` (`tenant_id`, `user_id`, `property_id`, `tenant_status`, `tenant_date_created`, `tenant_terminated_at`) VALUES
(65, 9, 51, 'terminated', '2025-05-10', '2025-05-10'),
(67, 1, 49, 'active', '2025-05-04', NULL),
(68, 2, 51, 'terminated', '2025-05-04', '2025-05-04'),
(69, 29, 51, 'terminated', '2025-05-08', '2025-05-08'),
(72, 39, 50, 'active', '2025-05-10', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `testimonial_id` int(11) NOT NULL,
  `tenant_id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` varchar(2000) DEFAULT NULL,
  `created_at` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`testimonial_id`, `tenant_id`, `property_id`, `rating`, `comment`, `created_at`) VALUES
(1, 65, 46, 5, 'this house is the best', '2025-04-27'),
(2, 65, 46, 5, 'this house is the best', '2025-04-27'),
(3, 65, 46, 0, 'fgdgd', '2025-04-27'),
(4, 65, 46, 0, 'This is the best ever', '2025-04-27'),
(5, 65, 46, 5, 'nmbnb', '2025-04-27'),
(6, 65, 46, 5, 'This house is very good. I will continue in renting this house.', '2025-04-27'),
(7, 65, 46, 5, 'ddfhdfbhkdsf', '2025-05-03'),
(8, 67, 49, 5, 'I want to live here forever. This is so good🥰🥰🥰🥰', '2025-05-04'),
(9, 65, 46, 5, 'Sobrang granda😍😍', '2025-05-04'),
(10, 72, 50, 5, 'Esta casa es una de las mejores que he tenido.', '2025-05-11');

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
(1, 'Marc Eihenburg', 'eihenburg@gmail.com', 'password123', '0945-765-3567', 'Makati City', 'Marc is a young professional working in the IT industry. He recently moved to the city for career growth and is looking for a rental home that suits his quiet and independent lifestyle. He values responsibility and ensures that his financial obligations, including rent and utilities, are always met on time.', '/rent-master2/admin/assets/tenants/man-8741800_1280.jpg', 'tenant'),
(2, 'George Peterson', 'georgepetersong@gmail.com', 'password123', '0934-721-9547', 'Quezon City', 'George is a freelance graphic designer who works remotely. He prefers a well-maintained and organized living space that fosters creativity and productivity. His flexible schedule allows him to keep the property in excellent condition, and he takes pride in being a responsible tenant who respects the rules of the rental home.', '/rent-master2/admin/assets/tenants/ai-generated-9009342_1280.jpg', 'visitor'),
(9, 'Miko Gapasans', 'miksgapasan@gmail.com', 'miko1234', '0995-064-4707', 'Dolores, Quezon, Philippines', 'I am the strongest', '/rent-master2/admin/assets/tenants/6820116e3acfb.png', 'visitor'),
(16, 'Miko', 'mikogapasa04@gmail.com', 'miko1234', '0995-064-4707', 'Dolores, Quezon, Philippines', 'I am working student', '/rent-master2/admin/assets/tenants/6820295255018.jpg', 'landlord'),
(29, 'Jane Doe', 'janedoe@gmail.com', '1234', '09876543211', 'Dolores, Quezon, Philippines', '1234', '/rent-master2/admin/assets/tenants/681e0159abc34.jpg', 'visitor'),
(39, 'Jose Rizal', 'rizzzzal@gmail.com', '1234', '09876543212', 'Calamba, Laguna', 'I am the national hero of the Philippines', '/rent-master2/admin/assets/tenants/6820263669331.jpg', 'tenant');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `amenities`
--
ALTER TABLE `amenities`
  ADD PRIMARY KEY (`amenity_id`);

--
-- Indexes for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `maintenance_requests_ibfk_1` (`tenant_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `tenant_id` (`tenant_id`);

--
-- Indexes for table `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`property_id`);

--
-- Indexes for table `property_amenities`
--
ALTER TABLE `property_amenities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_amenities_ibfk_1` (`property_id`),
  ADD KEY `property_amenities_ibfk_2` (`amenity_id`);

--
-- Indexes for table `property_images`
--
ALTER TABLE `property_images`
  ADD PRIMARY KEY (`property_images_id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `tenants`
--
ALTER TABLE `tenants`
  ADD PRIMARY KEY (`tenant_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`testimonial_id`),
  ADD KEY `tenant_id` (`tenant_id`),
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
-- AUTO_INCREMENT for table `amenities`
--
ALTER TABLE `amenities`
  MODIFY `amenity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `property_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `property_amenities`
--
ALTER TABLE `property_amenities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=202;

--
-- AUTO_INCREMENT for table `property_images`
--
ALTER TABLE `property_images`
  MODIFY `property_images_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tenants`
--
ALTER TABLE `tenants`
  MODIFY `tenant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `testimonial_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `maintenance_requests`
--
ALTER TABLE `maintenance_requests`
  ADD CONSTRAINT `maintenance_requests_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`tenant_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`tenant_id`);

--
-- Constraints for table `property_amenities`
--
ALTER TABLE `property_amenities`
  ADD CONSTRAINT `property_amenities_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`property_id`),
  ADD CONSTRAINT `property_amenities_ibfk_2` FOREIGN KEY (`amenity_id`) REFERENCES `amenities` (`amenity_id`);

--
-- Constraints for table `property_images`
--
ALTER TABLE `property_images`
  ADD CONSTRAINT `property_images_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`property_id`) ON DELETE CASCADE;

--
-- Constraints for table `tenants`
--
ALTER TABLE `tenants`
  ADD CONSTRAINT `tenants_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `tenants_ibfk_2` FOREIGN KEY (`property_id`) REFERENCES `properties` (`property_id`);

--
-- Constraints for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD CONSTRAINT `testimonials_ibfk_1` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`tenant_id`),
  ADD CONSTRAINT `testimonials_ibfk_2` FOREIGN KEY (`property_id`) REFERENCES `properties` (`property_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
