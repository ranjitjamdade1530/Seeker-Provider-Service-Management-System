-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 20, 2026 at 01:25 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ranjit`
--

-- --------------------------------------------------------

--
-- Table structure for table `areas`
--

CREATE TABLE `areas` (
  `id` int(11) NOT NULL,
  `area_name` varchar(100) NOT NULL,
  `city_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `areas`
--

INSERT INTO `areas` (`id`, `area_name`, `city_name`) VALUES
(3, 'Parel', 'Mumbai'),
(4, 'Dadar', 'Mumbai'),
(5, 'Andheri', 'Mumbai'),
(6, 'Navi Mumbai', 'Mumbai'),
(7, 'Goregaon', 'Mumbai'),
(8, 'Kharadi', 'Pune'),
(9, 'Hinjewadi', 'Pune'),
(10, 'koregaon Park', 'Pune'),
(11, 'Viman Nagar', 'Pune'),
(12, 'Hadapsar', 'Pune'),
(13, 'Koramangala', 'Bengaluru'),
(14, 'Domlur', 'Bengaluru'),
(15, 'Vijay Nagar', 'Bengaluru'),
(16, 'Jayanagar', 'Bengaluru'),
(17, 'Indira Nagar', 'Bengaluru'),
(18, 'Noida', 'Delhi'),
(19, 'Gurugram', 'Delhi'),
(20, 'New Delhi', 'Delhi'),
(21, 'Gurgaon', 'Delhi'),
(22, 'Kondapur', 'Hyderabad'),
(23, 'Madhapur', 'Hyderabad'),
(24, 'Gachibowli', 'Hyderabad'),
(26, 'Rajarampuri', 'Kolhapur');

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` int(11) NOT NULL,
  `city_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `city_name`) VALUES
(1, 'Pune'),
(2, 'Mumbai'),
(3, 'Delhi'),
(4, 'Hyderabad'),
(5, 'Bengaluru'),
(6, 'Kolhapur');

-- --------------------------------------------------------

--
-- Table structure for table `emergency_conversions`
--

CREATE TABLE `emergency_conversions` (
  `id` int(11) NOT NULL,
  `provider_id` varchar(50) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `certificate_path` varchar(255) DEFAULT NULL,
  `request_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emergency_conversions`
--

INSERT INTO `emergency_conversions` (`id`, `provider_id`, `reason`, `certificate_path`, `request_date`) VALUES
(1, 'P010', 'Accident..', 'uploads/cert_P010_1759550317.pdf', '2025-10-04 09:28:37');

-- --------------------------------------------------------

--
-- Table structure for table `providers`
--

CREATE TABLE `providers` (
  `provider_id` varchar(10) NOT NULL,
  `provider_name` varchar(100) DEFAULT NULL,
  `provider_dob` date DEFAULT NULL,
  `provider_gender` varchar(10) DEFAULT NULL,
  `provider_mobile` varchar(15) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `area` varchar(50) DEFAULT NULL,
  `date` date DEFAULT curdate(),
  `password` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `aadhar_doc` varchar(255) DEFAULT NULL,
  `status` enum('pending','active','inactive','converted') NOT NULL DEFAULT 'pending',
  `converted` tinyint(1) DEFAULT 0,
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `providers`
--

INSERT INTO `providers` (`provider_id`, `provider_name`, `provider_dob`, `provider_gender`, `provider_mobile`, `city`, `area`, `date`, `password`, `image`, `aadhar_doc`, `status`, `converted`, `email`) VALUES
('P007', 'Hari', NULL, 'Male', '9922455676', 'Mumbai', 'Parel', '2025-08-26', '123', 'uploads/1756220988_a.jpg', 'uploads/1756220988_a.jpg', 'active', 1, 'hari@gmail.com'),
('P008', 'Ranjit', NULL, 'Male', '9076896545', 'Bengaluru', 'Koramangala', '2025-09-08', '123', '1757302642_a.jpg', '1757302642_images.jpg', 'converted', 0, NULL),
('P009', 'Raj', NULL, 'Male', '9922455676', 'Bengaluru', 'Koramangala', '2025-09-08', '123', 'uploads/img_1757303305_ab1e3e8ee9.jpg', 'uploads/aadhar_1757303305_31dd022ab6.jpg', 'converted', 0, NULL),
('P010', 'Abc', '2003-09-02', 'Male', '9087676543', 'Bengaluru', 'Domlur', '2025-09-09', '123', 'uploads/img_1757407683_5d00bce641.jpg', 'uploads/aadhar_1757407683_49923e686c.jpg', 'active', 0, NULL),
('P011', 'R', NULL, 'Male', '9809876576', 'Delhi', 'Noida', '2025-09-09', '123', 'uploads/provider_1757442215_faa9465e.jpg', 'uploads/aadhar_1757436088_94d3731603.jpg', 'pending', 0, NULL),
('P012', 'K', NULL, 'Male', '9809876576', 'Mumbai', 'Parel', '2025-09-09', '123', 'uploads/img_1757437827_b9b8f36377.jpg', 'uploads/aadhar_1757437827_c9cde3d551.pdf', 'pending', 0, NULL),
('P013', 'KK', '2004-09-03', 'Male', '9809876576', 'Bengaluru', 'Koramangala', '2025-09-17', '123', 'uploads/img_1758116791_d78e454d69.jpg', 'uploads/aadhar_1758116791_933ed1ad24.pdf', 'converted', 0, NULL),
('P014', 'Adi', '2004-09-03', 'Male', '8756354622', 'Kolhapur', 'Rajarampuri', '2025-09-20', '123', 'uploads/img_1758337307_ad82679846.jpg', 'uploads/aadhar_1758337307_1b834f0708.pdf', 'pending', 0, NULL),
('P018', 'ABC', '1961-08-12', 'Male', '9922455676', 'Bengaluru', 'Koramangala', '2025-09-22', '123', 'uploads/img_1758554539_b54f195279.jpg', 'uploads/aadhar_1758554539_ffa53ff6d5.pdf', 'pending', 1, NULL),
('P019', 'ABC', '2000-11-12', 'Male', '9809876576', 'Hyderabad', 'Kondapur', '2025-09-23', '123', 'uploads/img_1758609651_cabf648407.jpg', 'uploads/aadhar_1758609651_39797039ca.pdf', 'pending', 0, 'abc@gmail.com'),
('P020', 'Rajesh', '1962-01-19', 'Male', '9922455676', 'Bengaluru', 'Koramangala', '2025-09-23', '123', 'uploads/img_1758611217_daa0b7c449.jpg', 'uploads/aadhar_1758611217_0b2e3772ac.pdf', 'pending', 1, 'rajesh@gmail.com'),
('P021', 'Babu', '1961-08-19', 'Male', '8756354622', 'Bengaluru', 'Vijay Nagar', '2025-09-23', '123', 'uploads/img_1758634078_e430226550.jpg', 'uploads/aadhar_1758634078_37de32f280.pdf', 'pending', 1, 'babu@gmail.com'),
('P022', 'Omii', '1962-08-19', 'Male', '8756354622', 'Bengaluru', 'Domlur', '2025-09-23', '123', 'uploads/img_1758635394_7b30e95b45.jpg', 'uploads/aadhar_1758635394_f65c7f33c2.pdf', 'pending', 1, 'omii@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `provider_requests`
--

CREATE TABLE `provider_requests` (
  `id` int(11) NOT NULL,
  `provider_id` varchar(50) NOT NULL,
  `time_slot` varchar(50) NOT NULL,
  `service_name` varchar(100) NOT NULL,
  `request_date` date NOT NULL DEFAULT curdate(),
  `available_date` date DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `area` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `provider_requests`
--

INSERT INTO `provider_requests` (`id`, `provider_id`, `time_slot`, `service_name`, `request_date`, `available_date`, `city`, `area`) VALUES
(6, 'P007', '9 to 12', 'Dr visit', '2025-08-26', NULL, NULL, NULL),
(7, 'P001', '9 to 12 Night', 'Bank Work', '2025-09-05', NULL, NULL, NULL),
(8, 'P001', '9 to 12 Night', 'Dr visit', '2025-09-05', NULL, NULL, NULL),
(9, 'P007', '9 to 12 Morning', 'Bank Work', '2025-09-05', NULL, NULL, NULL),
(10, 'P007', '9 to 12 Morning', 'Dr visit', '2025-09-05', NULL, NULL, NULL),
(11, 'P007', '9 to 12 Morning', 'Garden Visit', '2025-09-05', '2025-09-14', NULL, NULL),
(12, 'P007', '2 PM to 4 PM', 'Bank Work', '2025-09-06', '2025-09-06', NULL, NULL),
(13, 'P007', '2 PM to 4 PM', 'Dr visit', '2025-09-06', '2025-09-06', NULL, NULL),
(14, 'P007', '2 PM to 4 PM', 'Medical Needs', '2025-09-06', '2025-09-06', NULL, NULL),
(15, 'P010', '12 PM to 2 PM', 'Bank Work', '2025-09-09', '2025-09-09', NULL, NULL),
(16, 'P010', '12 PM to 2 PM', 'Dr visit', '2025-09-09', '2025-09-09', NULL, NULL),
(17, 'P010', '10 AM to12 PM', 'Bank Work', '2025-09-09', '2025-09-10', NULL, NULL),
(18, 'P010', '10 AM to12 PM', 'Dr visit', '2025-09-09', '2025-09-10', NULL, NULL),
(19, 'P010', '10 AM to12 PM', 'Garden Visit', '2025-09-09', '2025-09-10', NULL, NULL),
(20, 'P010', '10 AM to12 PM', 'Grocery Shop Visit', '2025-09-09', '2025-09-10', NULL, NULL),
(21, 'P010', '10 AM to12 PM', 'Medical Needs', '2025-09-09', '2025-09-10', NULL, NULL),
(22, 'P011', '10 AM to12 PM', 'Bank Work', '2025-09-09', '2025-09-09', NULL, NULL),
(23, 'P012', '10 AM to12 PM', 'Bank Work', '2025-09-10', '2025-09-11', NULL, NULL),
(24, 'P012', '10 AM to12 PM', 'Dr visit', '2025-09-10', '2025-09-11', NULL, NULL),
(25, 'P012', '10 AM to12 PM', 'Bank Work', '2025-09-10', '2025-09-11', NULL, NULL),
(26, 'P012', '10 AM to12 PM', 'Dr visit', '2025-09-10', '2025-09-11', NULL, NULL),
(27, 'P012', '8 PM to 10 PM', 'Medical Needs', '2025-09-10', '2025-09-11', NULL, NULL),
(28, 'P012', '10 AM to12 PM', 'Bank Work', '2025-09-11', '2025-09-11', NULL, NULL),
(29, 'P012', '10 AM to12 PM', 'Bank Work', '2025-09-12', '2025-09-13', NULL, NULL),
(30, 'P012', '10 AM to12 PM', 'Dr visit', '2025-09-12', '2025-09-13', NULL, NULL),
(31, 'P012', '10 AM to12 PM', 'Bank Work', '2025-09-12', '2025-09-13', 'Hyderabad', 'Gachibowli'),
(32, 'P012', '10 AM to12 PM', 'Dr visit', '2025-09-12', '2025-09-13', 'Hyderabad', 'Gachibowli'),
(33, 'P012', '8 PM to 10 PM', 'Bank Work', '2025-09-12', '2025-09-13', 'Delhi', 'Gurgaon'),
(34, 'P012', '8 PM to 10 PM', 'Dr visit', '2025-09-12', '2025-09-13', 'Delhi', 'Gurgaon'),
(35, 'P012', '8 PM to 10 PM', 'Bank Work', '2025-09-12', '2025-09-13', 'Delhi', 'Gurgaon'),
(36, 'P012', '8 PM to 10 PM', 'Dr visit', '2025-09-12', '2025-09-13', 'Delhi', 'Gurgaon'),
(37, 'P012', '10 AM to12 PM', 'Bank Work', '2025-09-12', '2025-09-13', 'Bengaluru', 'Jayanagar'),
(38, 'P012', '10 AM to12 PM', 'Dr visit', '2025-09-12', '2025-09-13', 'Bengaluru', 'Jayanagar'),
(39, 'P012', '10 AM to12 PM', 'Bank Work', '2025-09-12', '2025-09-13', 'Bengaluru', 'Jayanagar'),
(40, 'P012', '10 AM to12 PM', 'Dr visit', '2025-09-12', '2025-09-13', 'Bengaluru', 'Jayanagar'),
(41, 'P012', '12 PM to 2 PM', 'Bank Work', '2025-09-12', '2025-09-13', 'Bengaluru', 'Domlur'),
(42, 'P012', '10 AM to12 PM', 'Bank Work', '2025-09-12', '2025-09-13', 'Hyderabad', 'Gachibowli'),
(43, 'P012', '12 PM to 2 PM', 'Bank Work', '2025-09-12', '2025-09-13', 'Bengaluru', 'Domlur'),
(44, 'P012', '12 PM to 2 PM', 'Bank Work', '2025-09-12', '2025-09-13', 'Delhi', 'Gurgaon'),
(45, 'P012', '12 PM to 2 PM', 'Bank Work', '2025-09-12', '2025-09-13', 'Delhi', 'Gurgaon'),
(46, 'P013', '8 PM to 10 PM', 'Bank Work', '2025-09-20', '2025-09-21', 'Bengaluru', 'Indira Nagar'),
(47, 'P013', '8 AM to 10 AM', 'Bank Work', '2025-09-20', '2025-09-21', 'Delhi', 'Gurgaon'),
(48, 'P013', '10 AM to12 PM', 'Bank Work', '2025-09-20', '2025-09-22', 'Mumbai', 'Andheri'),
(49, 'P013', '10 AM to12 PM', 'Dr visit', '2025-09-20', '2025-09-22', 'Mumbai', 'Andheri'),
(50, 'P013', '8 AM to 10 AM', 'Bank Work', '2025-09-21', '2025-09-22', 'Bengaluru', 'Domlur'),
(51, 'P013', '6 PM to 8 PM', 'Bank Work', '2025-09-21', '2025-09-27', 'Bengaluru', 'Indira Nagar'),
(52, 'P013', '6 PM to 8 PM', 'Bank Work', '2025-09-21', '2025-09-27', 'Bengaluru', 'Indira Nagar'),
(53, 'P013', '8 PM to 10 PM', 'Bank Work', '2025-09-21', '2025-09-21', 'Bengaluru', 'Domlur'),
(54, 'P013', '6 PM to 8 PM', 'Bank Work', '2025-09-22', '2025-09-23', 'Bengaluru', 'Indira Nagar'),
(55, 'P013', '8 PM to 10 PM', 'Bank Work', '2025-09-22', '2025-09-24', 'Bengaluru', 'Indira Nagar'),
(56, 'P013', '8 PM to 10 PM', 'Bank Work', '2025-09-22', '2025-09-24', 'Bengaluru', 'Indira Nagar'),
(57, 'P013', '8 PM to 10 PM', 'Bank Work', '2025-09-22', '2025-09-24', 'Bengaluru', 'Indira Nagar'),
(58, 'P010', '12 PM to 2 PM', 'Bank Work', '2025-09-24', '2025-09-24', 'Hyderabad', 'Kondapur'),
(59, 'P010', '10 AM to12 PM', 'Bank Work', '2025-10-04', '2025-10-05', 'Delhi', 'Gurgaon'),
(60, 'P010', '10 AM to12 PM', 'Dr visit', '2025-10-04', '2025-10-05', 'Delhi', 'Gurgaon'),
(61, 'P007', '8 PM to 10 PM', 'Bank Work', '2025-10-15', '2025-10-16', 'Bengaluru', 'Domlur'),
(62, 'P007', '8 PM to 10 PM', 'Dr visit', '2025-10-15', '2025-10-16', 'Bengaluru', 'Domlur');

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `request_id` int(11) NOT NULL,
  `sicker_id` varchar(20) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `user_mobile` varchar(15) NOT NULL,
  `provider_id` varchar(20) NOT NULL,
  `request_type` varchar(100) NOT NULL,
  `city` varchar(50) NOT NULL,
  `area` varchar(50) NOT NULL,
  `slot` varchar(50) NOT NULL,
  `status` varchar(20) DEFAULT 'Pending',
  `rating` int(11) DEFAULT NULL,
  `time_spent` decimal(5,2) DEFAULT NULL,
  `request_date` datetime DEFAULT current_timestamp(),
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`request_id`, `sicker_id`, `user_name`, `user_mobile`, `provider_id`, `request_type`, `city`, `area`, `slot`, `status`, `rating`, `time_spent`, `request_date`, `note`) VALUES
(1, 'S001', 'Prasad', '9867543627', 'P007', 'Dr visit', 'Mumbai', 'Parel', '9 to 12', 'Accepted', 5, NULL, '2025-08-26 21:23:29', NULL),
(2, 'S001', 'Prasad', '9867543627', 'P007', 'Dr visit', 'Mumbai', 'Parel', '9 to 12 Morning', 'Pending', 2, NULL, '2025-09-05 13:05:50', NULL),
(3, 'S001', 'Prasad', '9867543627', 'P007', 'Bank Work', 'Mumbai', 'Parel', '2 PM to 4 PM', 'Accepted', 2, NULL, '2025-09-06 00:00:00', NULL),
(4, 'S001', 'Prasad', '9867543627', 'P010', 'Bank Work', 'Bengaluru', 'Domlur', '10 AM to12 PM', 'Accepted', 4, NULL, '2025-09-10 00:00:00', NULL),
(5, 'S001', '', '', 'P010', 'Grocery Shop Visit', 'Bengaluru', 'Domlur', '10 AM to12 PM', 'Accepted', NULL, NULL, '2025-09-10 00:00:00', NULL),
(6, 'S001', 'Prasad', '9867543627', 'P010', 'Medical Needs', 'Bengaluru', 'Domlur', '10 AM to12 PM', 'Accepted', 2, NULL, '2025-09-10 00:00:00', NULL),
(7, 'S006', 'S', '9867543627', 'P012', 'Bank Work', 'Mumbai', 'Parel', '10 AM to12 PM', 'Accepted', 2, 2.00, '2025-09-11 00:00:00', 'Come with your car'),
(8, 'S006', 'S', '9867543627', 'P012', 'Dr visit', 'Mumbai', 'Parel', '10 AM to12 PM', 'Accepted', 2, 3.00, '2025-09-11 00:00:00', 'Bike Required'),
(9, 'S006', 'S', '9867543627', 'P012', 'Bank Work', 'Delhi', 'Gurgaon', '12 PM to 2 PM', 'Accepted', 2, 2.00, '2025-09-13 00:00:00', 'Bike Req'),
(10, 'S006', 'S', '9867543627', 'P012', 'Bank Work', 'Hyderabad', 'Gachibowli', '10 AM to12 PM', 'Accepted', 2, 3.00, '2025-09-13 00:00:00', 'Car'),
(12, 'S005', 'Prasad', '9867543627', 'P013', 'Bank Work', 'Bengaluru', 'Indira Nagar', '8 PM to 10 PM', 'Accepted', 2, 2.00, '2025-09-21 00:00:00', 'Bike req'),
(13, 'S013', 'Hari', '9922455676', 'P010', 'Bank Work', 'Hyderabad', 'Kondapur', '12 PM to 2 PM', 'Accepted', 2, 2.00, '2025-09-24 00:00:00', ''),
(14, 'S016', 'KK', '9809876576', 'P010', 'Bank Work', 'Delhi', 'Gurgaon', '10 AM to12 PM', 'Pending', NULL, NULL, '2025-10-05 00:00:00', '');

-- --------------------------------------------------------

--
-- Table structure for table `request_services`
--

CREATE TABLE `request_services` (
  `id` int(11) NOT NULL,
  `service_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `request_services`
--

INSERT INTO `request_services` (`id`, `service_name`) VALUES
(1, 'Dr visit'),
(2, 'Medical Needs'),
(3, 'Grocery Shop Visit'),
(4, 'Garden Visit'),
(5, 'Bank Work');

-- --------------------------------------------------------

--
-- Table structure for table `sickers`
--

CREATE TABLE `sickers` (
  `id` int(11) NOT NULL,
  `sicker_id` varchar(10) NOT NULL,
  `sicker_name` varchar(100) NOT NULL,
  `dob` date DEFAULT NULL,
  `sicker_age` int(3) NOT NULL,
  `sicker_gender` enum('Male','Female') NOT NULL,
  `sicker_mobile` varchar(10) NOT NULL,
  `city` varchar(100) NOT NULL,
  `area` varchar(100) NOT NULL,
  `request_date` date DEFAULT curdate(),
  `password` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `aadhar_doc` varchar(255) DEFAULT NULL,
  `status` enum('pending','active','inactive') NOT NULL DEFAULT 'pending',
  `email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sickers`
--

INSERT INTO `sickers` (`id`, `sicker_id`, `sicker_name`, `dob`, `sicker_age`, `sicker_gender`, `sicker_mobile`, `city`, `area`, `request_date`, `password`, `image`, `aadhar_doc`, `status`, `email`) VALUES
(3, 'S004', 'Hari', '1946-01-02', 79, 'Male', '9867543627', '1', 'Hadapsar', '2025-08-26', '123', 'a.jpg', '1756190241_a.jpg', 'active', NULL),
(4, 'S005', 'Prasad', '1950-02-07', 75, 'Male', '9867543627', '5', 'Koramangala', '2025-09-07', '123', 'a.jpg', 'a.jpg', 'active', NULL),
(6, 'S006', 'S', '1960-01-02', 65, 'Male', '9867543627', 'Pune', 'Hinjewadi', '2025-09-09', '123', 'uploads/sicker_1757479931_37647662.jpg', 'uploads/aadhar_1757598236_51b49905.pdf', 'active', NULL),
(7, 'S007', 'p', '1960-09-10', 65, 'Male', '9076896545', 'Bengaluru', 'Koramangala', '2025-09-22', '123', 'uploads/img_1758553851_351329ff2a.jpg', 'uploads/aadhar_1758553851_4485a315ea.pdf', 'active', NULL),
(8, 'S008', 'XYZ', '1963-09-22', 62, 'Male', '8756354622', 'Pune', 'Kharadi', '2025-09-22', '123', 'uploads/img_1758554129_0a6559853f.jpg', 'uploads/aadhar_1758554129_898d52e0bf.pdf', 'active', NULL),
(11, 'S009', 'ABC', '1961-08-12', 64, 'Male', '9922455676', 'Bengaluru', 'Koramangala', '2025-09-22', '123', 'uploads/img_1758554539_b54f195279.jpg', 'uploads/aadhar_1758554539_ffa53ff6d5.pdf', 'active', NULL),
(12, 'S010', 'XYZ', '1962-07-10', 63, '', '9867543627', 'Bengaluru', 'Domlur', '2025-09-23', '123', 'a.jpg', 'Sicker_Monthly_Report_September_2025.pdf', 'active', 'xyz@gmail.com'),
(13, 'S011', 'Rajesh', '1962-01-19', 63, 'Male', '9922455676', 'Bengaluru', 'Koramangala', '2025-09-23', '123', 'uploads/img_1758611217_daa0b7c449.jpg', 'uploads/aadhar_1758611217_0b2e3772ac.pdf', 'active', NULL),
(14, 'S012', 'Omii', '1962-08-19', 63, 'Male', '8756354622', 'Bengaluru', 'Domlur', '2025-09-23', '123', 'uploads/img_1758635394_7b30e95b45.jpg', 'uploads/aadhar_1758635394_f65c7f33c2.pdf', 'active', 'omii@gmail.com'),
(15, 'S013', 'Hari', NULL, 0, 'Male', '9922455676', 'Mumbai', 'Parel', '2025-08-26', '123', 'uploads/1756220988_a.jpg', 'uploads/1756220988_a.jpg', 'active', 'hari@gmail.com'),
(16, 'S014', 'Ranjit', NULL, 0, 'Male', '9076896545', 'Bengaluru', 'Koramangala', '2025-09-24', '123', '1757302642_a.jpg', '1757302642_images.jpg', '', NULL),
(17, 'S015', 'Raj', NULL, 0, 'Male', '9922455676', 'Bengaluru', 'Koramangala', '2025-09-24', '123', 'uploads/img_1757303305_ab1e3e8ee9.jpg', 'uploads/aadhar_1757303305_31dd022ab6.jpg', '', NULL),
(18, 'S016', 'KK', '2004-09-03', 21, 'Male', '9809876576', 'Bengaluru', 'Koramangala', '2025-09-24', '123', 'uploads/img_1758116791_d78e454d69.jpg', 'uploads/aadhar_1758116791_933ed1ad24.pdf', 'active', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `time_slots`
--

CREATE TABLE `time_slots` (
  `id` int(11) NOT NULL,
  `time_range` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `time_slots`
--

INSERT INTO `time_slots` (`id`, `time_range`) VALUES
(7, '8 AM to 10 AM'),
(8, '10 AM to12 PM'),
(9, '12 PM to 2 PM'),
(12, '2 PM to 4 PM'),
(13, '4 PM to 6 PM'),
(14, '6 PM to 8 PM'),
(15, '8 PM to 10 PM');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `areas`
--
ALTER TABLE `areas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `emergency_conversions`
--
ALTER TABLE `emergency_conversions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `providers`
--
ALTER TABLE `providers`
  ADD PRIMARY KEY (`provider_id`);

--
-- Indexes for table `provider_requests`
--
ALTER TABLE `provider_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`request_id`);

--
-- Indexes for table `request_services`
--
ALTER TABLE `request_services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sickers`
--
ALTER TABLE `sickers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sicker_id` (`sicker_id`);

--
-- Indexes for table `time_slots`
--
ALTER TABLE `time_slots`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `areas`
--
ALTER TABLE `areas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `emergency_conversions`
--
ALTER TABLE `emergency_conversions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `provider_requests`
--
ALTER TABLE `provider_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `request_services`
--
ALTER TABLE `request_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sickers`
--
ALTER TABLE `sickers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `time_slots`
--
ALTER TABLE `time_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
