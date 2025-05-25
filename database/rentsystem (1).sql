-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 18, 2025 at 01:57 PM
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
(16, 73, 'Plumbing', 'The water pipes suddenly explode', '2025-05-15 09:28:04', 'completed'),
(17, 73, 'Electrical', 'We dont have electricity', '2025-05-16 10:51:09', 'completed'),
(18, 73, 'Electrical', 'hello', '2025-05-16 15:27:02', 'pending'),
(19, 73, 'Electrical', 'hello', '2025-05-16 16:12:15', 'completed');

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

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `type`, `message`, `is_read`, `created_at`, `related_id`) VALUES
(8, 1, 'property', 'New tenant request received for property: Pineview One-Bedroom – Unit 2C. Status: Pending', 1, '2025-05-15 09:24:54', 59),
(9, 1, 'payment', 'Payment received for property: Pineview One-Bedroom – Unit 2C. Status: Paid.', 1, '2025-05-15 09:26:11', 59),
(10, 1, 'maintenance', 'New maintenance request received for property: Pineview One-Bedroom – Unit 2C. Status: Pending', 1, '2025-05-15 09:28:04', 16),
(11, 1, 'maintenance', 'New maintenance request received for property: Pineview One-Bedroom – Unit 2C. Status: Pending', 1, '2025-05-16 10:51:09', 17),
(12, 1, 'maintenance', 'New maintenance request received for property: Pineview One-Bedroom – Unit 2C. Status: Pending', 1, '2025-05-16 15:27:02', 18),
(13, 1, 'maintenance', 'New maintenance request received for property: Pineview One-Bedroom – Unit 2C. Status: Pending', 1, '2025-05-16 16:12:15', 19),
(14, 2, 'property', 'Tenant request received for property: Rosewood Studio – Unit A1. Status: Pending', 1, '2025-05-16 17:00:06', 57),
(15, 2, 'property', 'Tenant request received for property: Rosewood Studio – Unit A1. Status: Pending', 1, '2025-05-16 22:45:48', 57);

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
(59, 73, '2025-05-15', '2025-06-15', 'Paid', '2025-05-15', 'Credit/Debit Card'),
(60, 73, '2025-06-16', '2025-07-15', 'Pending', NULL, NULL);

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
(57, 'Rosewood Studio – Unit A1', 'Zamboanga Street, UP Campus, Diliman, 4th District, Quezon City, Eastern Manila District, Metro Manila, 1101, Philippines', '2025-01-29', ' Rosewood Studio offers a 25 sqm ground floor unit with ceramic tile flooring, a built-in wardrobe, compact kitchenette with granite countertops, and a private bathroom with a hot and cold shower. Ideal for students or single renters who value simplicity and accessibility.', 'unavailable', 24000, 14.65650300, 121.04845800),
(58, 'Suncrest Flat – Unit B2', 'Dulong Bayan, San Jose del Monte, Bulacan, Central Luzon, 3023, Philippines', '2025-05-13', 'Suncrest Flat is a bright and airy 28 sqm studio with large windows, vinyl flooring, and a clean interior layout. It includes a private comfort room, compact kitchen area, and space for a bed and study desk—perfect for students or work-from-home tenants.', 'available', 17000, 14.82400900, 121.04736300),
(59, 'Pineview One-Bedroom – Unit 2C', 'House of Grace, J. P. Rizal Street, T&D Village, Tuktukan, Taguig District 1, Taguig, Southern Manila District, Metro Manila, 1637, Philippines', '2025-03-12', 'Pineview is a 35 sqm one-bedroom unit on the second floor featuring a full living room, private balcony, tiled floors, and a modern bathroom. The kitchen is furnished with overhead cabinets, and the layout separates sleeping, dining, and leisure areas efficiently.', 'available', 27000, 14.53091400, 121.07242600),
(60, 'Maple Heights Studio – Unit 3D', 'Tanzang Luma Road, Tanzang Luma VI, Imus, Cavite, Calabarzon, 4103, Philippines', '2024-11-20', ' Maple Heights Studio is a newly renovated 26 sqm unit on the third floor, offering a peaceful space with high ceilings, ceramic tiles, LED lighting, and a modern kitchenette. The private bathroom is neatly tiled, and the unit gets excellent natural ventilation.', 'available', 23000, 14.40941000, 120.94574000);

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
(270, 57, 1),
(271, 57, 2),
(272, 57, 3),
(273, 57, 8),
(274, 57, 9),
(275, 57, 14),
(276, 57, 15),
(277, 57, 16),
(278, 57, 17),
(368, 58, 1),
(369, 58, 2),
(370, 58, 3),
(371, 58, 9),
(372, 58, 14),
(373, 58, 16),
(374, 59, 1),
(375, 59, 2),
(376, 59, 3),
(377, 59, 9),
(378, 59, 11),
(379, 59, 13),
(380, 59, 14),
(381, 59, 15),
(382, 59, 16),
(383, 59, 18),
(384, 59, 19),
(385, 60, 1),
(386, 60, 2),
(387, 60, 8),
(388, 60, 11),
(389, 60, 14),
(390, 60, 15),
(391, 60, 16),
(392, 60, 17);

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
(23, 57, '/rent-master2/admin/assets/properties/1747271009_pexels-pixabay-53610.jpg', '/rent-master2/admin/assets/properties/1747271009_pexels-jvdm-1457847.jpg', '/rent-master2/admin/assets/properties/1747271009_pexels-fotoaibe-1571463.jpg', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(24, 58, '/rent-master2/admin/assets/properties/1747410294_pexels-pixabay-259588.jpg', '/rent-master2/admin/assets/properties/1747410294_pexels-curtis-adams-1694007-3935352.jpg', '/rent-master2/admin/assets/properties/1747410294_pexels-pixabay-276554.jpg', '/rent-master2/admin/assets/properties/1747410294_pexels-pixabay-276671.jpg', NULL, NULL, NULL, NULL, NULL, NULL),
(25, 59, '/rent-master2/admin/assets/properties/1747410391_pexels-pixabay-280222 (1).jpg', '/rent-master2/admin/assets/properties/1747410391_pexels-fotoaibe-1571462.jpg', '/rent-master2/admin/assets/properties/1747410391_pexels-falling4utah-2724749.jpg', '/rent-master2/admin/assets/properties/1747410391_pexels-pixabay-262048.jpg', NULL, NULL, NULL, NULL, NULL, NULL),
(26, 60, '../admin/assets/properties/1747410450_pexels-binyaminmellish-1396132.jpg', '/rent-master2/admin/assets/properties/1747410450_pexels-curtis-adams-1694007-3935352.jpg', '/rent-master2/admin/assets/properties/1747410450_pexels-falling4utah-2724749.jpg', '/rent-master2/admin/assets/properties/1747410450_pexels-fotoaibe-1571459.jpg', NULL, NULL, NULL, NULL, NULL, NULL);

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
(73, 1, 59, 'terminated', '2025-05-15', '2025-05-16'),
(74, 29, 58, 'terminated', '2025-05-16', '2025-05-16'),
(75, 2, 57, 'active', '2025-05-16', '2025-05-16'),
(76, 40, 57, 'terminated', '2025-05-16', '2025-05-16');

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
(15, 73, 59, 5, 'The unit is clean, well-maintained, and peaceful. Great location, friendly landlord, and perfect for students or young professionals.', '2025-05-15');

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
(2, 'George Peterson', 'georgepetersong@gmail.com', 'password123', '0934-721-9547', 'Quezon City', 'George is a freelance graphic designer who works remotely. He prefers a well-maintained and organized living space that fosters creativity and productivity. His flexible schedule allows him to keep the property in excellent condition, and he takes pride in being a responsible tenant who respects the rules of the rental home.', '/rent-master2/admin/assets/tenants/ai-generated-9009342_1280.jpg', 'tenant'),
(9, 'Angela Martinez', 'angela.martinez@example.com', 'password123', '09181234567', 'Unit 1A, 123 Mabini St, Quezon City', 'Marketing professional who enjoys urban living and values a peaceful environment.', '/rent-master2/admin/assets/tenants/6820116e3acfb.png', 'visitor'),
(16, 'Jonathan Reyes', 'admin@gmail.com', 'admin1234', '09171234567', '123 Mabini St, Quezon City', 'Property owner and manager of multiple residential units in Quezon City.\r\n\r\n', '/rent-master2/admin/assets/tenants/682535789482b.jpg', 'landlord'),
(29, 'Jane Doe', 'janedoe@gmail.com', '1234', '09876543211', '123 Mabini St, Quezon City', '1234', '/rent-master2/admin/assets/tenants/681e0159abc34.jpg', 'visitor'),
(40, 'Beatrice Santos', 'bea@gmail.com', '1234', '09221234567', 'Unit 3A, 123 Mabini St, Quezon City', ' Junior accountant who commutes daily to Makati and values convenience and affordability.', '/rent-master2/admin/assets/tenants/682546d86c105.jpg', 'visitor');

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
  ADD KEY `property_id` (`property_id`),
  ADD KEY `tenants_ibfk_1` (`user_id`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`testimonial_id`),
  ADD KEY `tenant_id` (`tenant_id`),
  ADD KEY `testimonials_ibfk_2` (`property_id`);

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
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `property_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `property_amenities`
--
ALTER TABLE `property_amenities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=393;

--
-- AUTO_INCREMENT for table `property_images`
--
ALTER TABLE `property_images`
  MODIFY `property_images_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `tenants`
--
ALTER TABLE `tenants`
  MODIFY `tenant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `testimonial_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

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
  ADD CONSTRAINT `tenants_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD CONSTRAINT `testimonials_ibfk_2` FOREIGN KEY (`property_id`) REFERENCES `properties` (`property_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
