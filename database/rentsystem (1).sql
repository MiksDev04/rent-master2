-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 06, 2025 at 03:02 AM
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
(17, 73, 'Electrical', 'We don\'t have electricity', '2025-05-16 10:51:09', 'completed'),
(18, 73, 'Electrical', 'hello', '2025-05-16 15:27:02', 'completed'),
(19, 73, 'Electrical', 'hello', '2025-05-16 16:12:15', 'completed'),
(20, 73, 'Structural', 'Hello', '2025-05-28 22:21:39', 'pending'),
(21, 73, 'Electrical', 'not working', '2025-05-29 10:08:10', 'pending'),
(23, 82, 'Electrical', 'We don\'t have electricity, something just explode near the kitchen. Help me please😓', '2025-06-03 22:38:24', 'completed');

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
(15, 2, 'property', 'Tenant request received for property: Rosewood Studio – Unit A1. Status: Pending', 1, '2025-05-16 22:45:48', 57),
(16, 1, 'property', 'Tenant request received for property: Maple Heights Studio – Unit 3D. Status: Pending', 1, '2025-05-25 12:48:10', 60),
(17, 41, 'property', 'New tenant request received for property: Pineview One-Bedroom – Unit 2C. Status: Pending', 1, '2025-05-25 12:52:46', 59),
(18, 41, 'payment', 'Payment received for property: Pineview One-Bedroom – Unit 2C. Status: Paid.', 1, '2025-05-25 14:07:38', 81),
(19, 41, 'property', 'Tenant request received for property: Rosewood Studio – Unit A1. Status: Pending', 1, '2025-05-25 14:11:12', 57),
(20, 41, 'payment', 'Payment received for property: Rosewood Studio – Unit A1. Status: Paid.', 1, '2025-05-25 15:11:16', 75),
(21, 1, 'property', 'Tenant request received for property: Suncrest Flat – Unit B2. Status: Pending', 1, '2025-05-28 20:16:51', 58),
(22, 1, 'payment', 'Payment received for property: Suncrest Flat – Unit B2. Status: Paid.', 1, '2025-05-28 20:30:23', 101),
(23, 1, 'maintenance', 'New maintenance request received for property: Suncrest Flat – Unit B2. Status: Pending', 1, '2025-05-28 22:21:39', 20),
(24, 41, 'property', 'Tenant request received for property: Pineview One-Bedroom – Unit 2C. Status: Pending', 1, '2025-05-29 10:07:14', 59),
(25, 1, 'maintenance', 'New maintenance request received for property: Suncrest Flat – Unit B2. Status: Pending', 1, '2025-05-29 10:08:10', 21),
(26, 41, 'property', 'Tenant request received for property: Maple Heights Studio – Unit 3D. Status: Pending', 1, '2025-05-29 14:03:09', 60),
(27, 41, 'property', 'Tenant request received for property: Maple Heights Studio – Unit 3D. Status: Pending', 1, '2025-05-29 14:06:08', 60),
(28, 41, 'payment', 'Payment received for property: Maple Heights Studio – Unit 3D. Status: Paid.', 1, '2025-05-29 14:07:18', 122),
(29, 41, 'payment', 'Payment received for property: Maple Heights Studio – Unit 3D. Status: Paid.', 1, '2025-05-29 20:57:31', 122),
(30, 41, 'maintenance', 'New maintenance request received for property: Maple Heights Studio – Unit 3D. Status: Pending', 1, '2025-05-29 21:09:48', 22),
(31, 51, 'property', 'New tenant request received for property: Maple Heights Studio – Unit 3D. Status: Pending', 1, '2025-05-30 21:18:31', 60),
(32, 51, 'payment', 'Payment received for property: Maple Heights Studio – Unit 3D. Status: Paid.', 1, '2025-05-30 21:31:16', 124),
(33, 51, 'payment', 'Payment received for property: Maple Heights Studio – Unit 3D. Status: Paid.', 1, '2025-05-30 22:09:03', 126),
(34, 51, 'property', 'Tenant request received for property: Maple Heights Studio – Unit 3D. Status: Pending', 1, '2025-06-03 20:33:42', 60),
(35, 51, 'property', 'Tenant request received for property: Pineview One-Bedroom – Unit 2C. Status: Pending', 1, '2025-06-03 21:02:43', 59),
(36, 51, 'property', 'Tenant request received for property: Pineview One-Bedroom – Unit 2C. Status: Pending', 1, '2025-06-03 21:28:55', 59),
(37, 51, 'property', 'New tenant request received for property: Maple Heights Studio – Unit 3D. Status: Pending', 1, '2025-06-03 22:12:01', 60),
(38, 51, 'maintenance', 'New maintenance request received for property: Maple Heights Studio – Unit 3D. Status: Pending', 1, '2025-06-03 22:38:24', 23),
(39, 51, 'property', 'Tenant request received for property: Suncrest Flat – Unit B2. Status: Pending', 1, '2025-06-04 21:25:29', 58);

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
(101, 73, '2025-05-28', '2025-06-27', 'Paid', '2025-05-28', 'Bank Transfer'),
(117, 73, '2025-06-28', '2025-07-27', 'Paid', '2025-05-31', NULL),
(127, 82, '2025-06-04', '2025-07-03', 'Paid', '2025-06-04', NULL);

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
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `property_capacity` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`property_id`, `property_name`, `property_location`, `property_date_created`, `property_description`, `property_status`, `property_rental_price`, `latitude`, `longitude`, `property_capacity`) VALUES
(57, 'Rosewood Studio – Unit A1', 'Zamboanga Street, UP Campus, Diliman, 4th District, Quezon City, Eastern Manila District, Metro Manila, 1101, Philippines', '2025-01-29', ' Rosewood Studio offers a 25 sqm ground floor unit with ceramic tile flooring, a built-in wardrobe, compact kitchenette with granite countertops, and a private bathroom with a hot and cold shower. Ideal for students or single renters who value simplicity and accessibility.', 'available', 24000, 14.65650300, 121.04845800, '4-6'),
(58, 'Suncrest Flat – Unit B2', 'Dulong Bayan, San Jose del Monte, Bulacan, Central Luzon, 3023, Philippines', '2025-05-13', 'Suncrest Flat is a bright and airy 28 sqm studio with large windows, vinyl flooring, and a clean interior layout. It includes a private comfort room, compact kitchen area, and space for a bed and study desk—perfect for students or work-from-home tenants.', 'available', 17000, 14.82400900, 121.04736300, '4-6'),
(59, 'Pineview One-Bedroom – Unit 2C', 'House of Grace, J. P. Rizal Street, T&D Village, Tuktukan, Taguig District 1, Taguig, Southern Manila District, Metro Manila, 1637, Philippines', '2025-03-12', 'Pineview is a 35 sqm one-bedroom unit on the second floor featuring a full living room, private balcony, tiled floors, and a modern bathroom. The kitchen is furnished with overhead cabinets, and the layout separates sleeping, dining, and leisure areas efficiently.', 'unavailable', 27000, 14.53091400, 121.07242600, '7-10'),
(60, 'Maple Heights Studio – Unit 3D', 'Tanzang Luma Road, Tanzang Luma VI, Imus, Cavite, Calabarzon, 4103, Philippines', '2024-11-20', ' Maple Heights Studio is a newly renovated 26 sqm unit on the third floor, offering a peaceful space with high ceilings, ceramic tiles, LED lighting, and a modern kitchenette. The private bathroom is neatly tiled, and the unit gets excellent natural ventilation.', 'available', 23000, 14.40941000, 120.94574000, '4-6');

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
(471, 58, 1),
(472, 58, 2),
(473, 58, 3),
(474, 58, 9),
(475, 58, 14),
(476, 58, 16),
(477, 59, 1),
(478, 59, 2),
(479, 59, 3),
(480, 59, 9),
(481, 59, 11),
(482, 59, 13),
(483, 59, 14),
(484, 59, 15),
(485, 59, 16),
(486, 59, 18),
(487, 59, 19),
(488, 57, 1),
(489, 57, 2),
(490, 57, 3),
(491, 57, 8),
(492, 57, 9),
(493, 57, 14),
(494, 57, 15),
(495, 57, 16),
(496, 57, 17),
(497, 60, 1),
(498, 60, 2),
(499, 60, 8),
(500, 60, 11),
(501, 60, 14),
(502, 60, 15),
(503, 60, 16),
(504, 60, 17);

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
(73, 1, 58, 'active', '2025-05-01', NULL),
(74, 29, 59, 'terminated', '2025-05-28', '2025-05-28'),
(75, 2, 57, 'terminated', '2025-05-16', '2025-05-25'),
(76, 40, 57, 'terminated', '2025-05-16', '2025-05-25'),
(82, 51, 58, 'terminated', '2025-06-04', '2025-06-05');

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
(15, 73, 59, 5, 'The unit is clean, well-maintained, and peaceful. Great location, friendly landlord, and perfect for students or young professionals.', '2025-05-15'),
(16, 77, 59, 5, 'This is much better than other houses that i had rented over the last 5 years 💚💚', '2025-05-25'),
(17, 77, 57, 5, 'Very nice house', '2025-05-25'),
(18, 73, 58, 5, 'My friend suggested this website to me. He mentioned that this website offers an outstanding rental houses. Looks like it is true🙂', '2025-05-28'),
(19, 77, 60, 5, 'Super affordable, I hope  the price won\'t increase😺', '2025-05-29');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `user_email` varchar(100) NOT NULL,
  `user_password` varchar(100) DEFAULT NULL,
  `user_phone_number` varchar(50) DEFAULT NULL,
  `user_address` varchar(100) DEFAULT NULL,
  `user_description` varchar(2000) DEFAULT NULL,
  `user_image` varchar(500) DEFAULT NULL,
  `user_role` enum('tenant','landlord','visitor') NOT NULL DEFAULT 'visitor'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `user_email`, `user_password`, `user_phone_number`, `user_address`, `user_description`, `user_image`, `user_role`) VALUES
(1, 'Marc Eihenburg', 'eihenburg@gmail.com', 'password123', '0945-765-3567', 'Makati City', 'Marc is a young professional working in the IT industry. He recently moved to the city for career growth and is looking for a rental home that suits his quiet and independent lifestyle. He values responsibility and ensures that his financial obligations, including rent and utilities, are always met on time.', '/admin/assets/tenants/682d728689176.jpg', 'tenant'),
(2, 'George Peterson', 'georgepetersong@gmail.com', 'password123', '0934-721-9547', 'Quezon City', 'George is a freelance graphic designer who works remotely. He prefers a well-maintained and organized living space that fosters creativity and productivity. His flexible schedule allows him to keep the property in excellent condition, and he takes pride in being a responsible tenant who respects the rules of the rental home.', '/admin/assets/tenants/682d73edde624.jpg', 'visitor'),
(9, 'Angela Martinez', 'angela.martinez@example.com', 'password123', '09181234567', 'Unit 1A, 123 Mabini St, Quezon City', 'Marketing professional who enjoys urban living and values a peaceful environment.', '/admin/assets/tenants/682d74272d05b.jpg', 'visitor'),
(16, 'Jonathan Reyes', 'admin@gmail.com', 'admin1234', '09171234567', '123 Mabini St, Quezon City', 'Property owner and manager of multiple residential units in Quezon City.\r\n\r\n', '/rent-master2/admin/assets/tenants/683edab2b3617.jpg', 'landlord'),
(29, 'Jane Doe', 'janedoe@gmail.com', '1234', '09876543211', '123 Mabini St, Quezon City', '1234', '/rent-master2/admin/assets/tenants/681e0159abc34.jpg', 'visitor'),
(40, 'Beatrice Santos', 'bea@gmail.com', '1234', '09221234567', 'Unit 3A, 123 Mabini St, Quezon City', ' Junior accountant who commutes daily to Makati and values convenience and affordability.', '/admin/assets/tenants/682d74c7c39f5.jpg', 'visitor'),
(41, 'Gojo Satoru', 'mikogapasan04@gmail.com', '1234', '09950644707', 'Dolores, Quezon, Philippines', 'I am a developer', '/rent-master2/admin/assets/tenants/6839bc8cebafc.jpg', 'visitor'),
(43, 'Mike', 'mike@gmail.com', '1234', '09221234567', 'Dolores, Quezon, Philippines', 'Nobody', '/rent-master2/admin/assets/tenants/68371a5d0cb1b.jpg', 'visitor'),
(51, 'Saitama', 'miksgapasan@gmail.com', '1234', '09950644707', 'Japan', 'One Punchhhh', '/rent-master2/admin/assets/tenants/6839bb407f673.jpg', 'visitor'),
(52, 'GAPASAN, MIKO M.', 'gapasanmikom@gmail.com', '1234', '09171234567', 'Makati City', 'Nothing', '/rent-master2/admin/assets/tenants/6841a7de18dd2.jpg', 'visitor');

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
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- AUTO_INCREMENT for table `properties`
--
ALTER TABLE `properties`
  MODIFY `property_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `property_amenities`
--
ALTER TABLE `property_amenities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=505;

--
-- AUTO_INCREMENT for table `property_images`
--
ALTER TABLE `property_images`
  MODIFY `property_images_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `tenants`
--
ALTER TABLE `tenants`
  MODIFY `tenant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `testimonial_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

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
