-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 04, 2026 at 08:14 PM
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
-- Database: `fossee_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `event_config`
--

CREATE TABLE `event_config` (
  `id` int(11) NOT NULL COMMENT 'Primary Key: Unique event ID.',
  `reg_start_date` varchar(20) NOT NULL,
  `reg_end_date` varchar(20) NOT NULL,
  `event_date` varchar(20) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `event_category` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Stores event configuration details.';

--
-- Dumping data for table `event_config`
--

INSERT INTO `event_config` (`id`, `reg_start_date`, `reg_end_date`, `event_date`, `event_name`, `event_category`) VALUES
(1, '2026-01-15', '2026-01-25', '2026-01-29', 'Codefest 2026', 'Hackathon'),
(2, '2026-01-10', '2026-01-19', '2026-01-31', 'Drupal Workshop', 'Online Workshop'),
(3, '2026-02-03', '2026-02-07', '2026-02-11', 'Super 30 Talks', 'Conference'),
(4, '2026-02-03', '2026-02-09', '2026-02-11', 'Dhindhora 2  ', 'One-day Workshop'),
(5, '2026-02-05', '2026-02-09', '2026-02-11', 'Metaverse', 'Hackathon');

-- --------------------------------------------------------

--
-- Table structure for table `event_registration`
--

CREATE TABLE `event_registration` (
  `id` int(11) NOT NULL COMMENT 'Primary Key: Unique registration ID.',
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `college_name` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `event_category` varchar(100) NOT NULL,
  `event_date` varchar(20) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `event_id` int(11) NOT NULL,
  `created` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Stores user registrations for events.';

--
-- Dumping data for table `event_registration`
--

INSERT INTO `event_registration` (`id`, `full_name`, `email`, `college_name`, `department`, `event_category`, `event_date`, `event_name`, `event_id`, `created`) VALUES
(1, 'Rudraksh Singh', 'rudrakshsinghkhichi82@gmail.com', 'VIT Bhopal', 'CSE', 'Hackathon', '2026-01-29', 'Codefest 2026', 1, 1769787522),
(2, 'Akshay Khanna ', 'abcd@gmail.com', 'IIT Dholakpur', 'Mechanical', 'Online Workshop', '2026-01-31', 'Drupal Workshop', 2, 1769804084),
(3, 'Gaurav Pratap', 'abcdbsdk@gmail.com', 'IISC Dholakpur', 'ECE', 'Online Workshop', '2026-01-31', 'Drupal Workshop', 2, 1769887187);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `event_config`
--
ALTER TABLE `event_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_registration`
--
ALTER TABLE `event_registration`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `event_config`
--
ALTER TABLE `event_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key: Unique event ID.', AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `event_registration`
--
ALTER TABLE `event_registration`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key: Unique registration ID.', AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
