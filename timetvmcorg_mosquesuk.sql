-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 11, 2026 at 11:51 PM
-- Server version: 10.6.24-MariaDB-cll-lve-log
-- PHP Version: 8.4.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `timetvmcorg_mosquesuk`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` char(36) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
('c8fe5756-d06c-481c-aefa-e5b239fe65f9', 'admin', 'h@mm@d', '2025-01-02 15:46:21'),
('id', 'username', 'password', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` char(36) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`) VALUES
('', 'Time.TVMC', 'Admin.TVMC.uk'),
('3080552c-f4f2-41cd-9b76-a039fbfe0699', 'admin', 'hammad');

-- --------------------------------------------------------

--
-- Table structure for table `mosques`
--

CREATE TABLE `mosques` (
  `id` char(36) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `location_url` text DEFAULT NULL,
  `postcode` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `mosques`
--

INSERT INTO `mosques` (`id`, `name`, `address`, `location_url`, `postcode`) VALUES
('09bb971f-056b-48dc-b902-75c407b6d7fc', 'Runnymede Muslim Society', 'C81-82 High St, Egham TW20 ', '', 'TW20 9HE'),
('1700ad0b-b2f9-4da0-97c0-78ca53906674', 'Windsor Muslim Association', 'Parsonage Ln, Windsor, SL4 5EW', 'https://maps.app.goo.gl/YS79Pi6WX51q2CL87', 'SL4 5EW'),
('2a0b72e1-c81b-4561-95ab-64a0bec47340', 'Al-Tawheed Islamic Education Centre', 'Cookham Rd, Maidenhead SL6 8AJ', 'https://maps.app.goo.gl/TVFYsPhaiQmWNTL29', 'SL6 8AJ'),
('2ee565b3-a27c-4e5d-8153-03c1d07907a3', 'Medina Islamic Educational and Cultural Centre', '29 Shirley Ave, Windsor, SL4 5LH', 'https://maps.app.goo.gl/9rXwstmdnDwC6iUA6', 'SL4 5LH'),
('42f7385d-60ac-4c73-adc8-1ca39654072d', 'Masjid Al Jannah', '1 Stoke Rd, Slough SL2 5AH', 'https://maps.app.goo.gl/h8fQaZbLpfVje14v9', 'SL2 5AH'),
('67c5d4a9-992b-4649-889b-04ead7265a3d', 'Maidenhead Mosque & Islamic Centre', '13 Holmanleaze, Maidenhead, SL6 8AW', 'https://maps.app.goo.gl/y6nbhhk811eWymVL7', 'SL6 8AW'),
('bf33a8b2-ecc5-4f6c-9781-bf2d60e0d3a4', 'The Ujala Foundation', '6C Villiers Rd, Slough, SL2 1NP', 'https://maps.app.goo.gl/mPeucVsNLa1z42LA6', 'SL2 1NP'),
('d5a41605-7ade-434a-a9df-a098ef6dde42', 'Langley Islamic Centre', 'Langley Pavilion, Langley Rd, Langley, Slough, SL3 8BS', 'https://maps.app.goo.gl/qtGg4ogXpyzu6cca8', 'SL3 8BS'),
('eb02eda1-57a2-4e94-afc2-bcf45dfa9504', 'Masjid Ilyas', 'Whitby Rd, Slough, SL1 3DW', 'https://maps.app.goo.gl/AcHob6YGkyzVqmmQA', 'SL1 3DW'),
('ef6039e7-b569-4fb8-a440-3e1253bfa00a', 'Jamia Masjid & Islamic Centre', '83 Stoke Poges Ln, Slough SL1 3NY', 'https://maps.app.goo.gl/AFt1YNyu5CqKkUeY6', 'SL1 3NY'),
('f36e7985-2171-4f34-9634-ed9f5215948f', 'Al-Madni Masjid', '1 Whittle Parkway, Slough, Berkshire, SL1 6FE', 'https://maps.app.goo.gl/5916RXEE93iLDN4z7', 'SL1 6FE');

-- --------------------------------------------------------

--
-- Table structure for table `mosque_parking`
--

CREATE TABLE `mosque_parking` (
  `id` char(36) NOT NULL,
  `mosque_id` char(36) DEFAULT NULL,
  `instruction` text DEFAULT NULL,
  `onsite_parking` text DEFAULT NULL,
  `disable_bays` text DEFAULT NULL,
  `off_street_parking` text DEFAULT NULL,
  `road_name` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `distance_to_mosque` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `mosque_parking`
--

INSERT INTO `mosque_parking` (`id`, `mosque_id`, `instruction`, `onsite_parking`, `disable_bays`, `off_street_parking`, `road_name`, `address`, `distance_to_mosque`) VALUES
('', '09bb971f-056b-48dc-b902-75c407b6d7fc', NULL, 'yes', '1', 'no', '2ew', 'sedrfghjk', '2');

-- --------------------------------------------------------

--
-- Table structure for table `parking`
--

CREATE TABLE `parking` (
  `id` char(36) NOT NULL,
  `mosque_id` char(36) DEFAULT NULL,
  `onsite_parking` varchar(255) DEFAULT NULL,
  `disable_bays` varchar(255) DEFAULT NULL,
  `off_street_parking` varchar(255) DEFAULT NULL,
  `road_name` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `distance_to_mosque` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `parking`
--

INSERT INTO `parking` (`id`, `mosque_id`, `onsite_parking`, `disable_bays`, `off_street_parking`, `road_name`, `address`, `distance_to_mosque`) VALUES
('42c366eb-2909-11f0-9192-3ae40c1bd964', '67c5d4a9-992b-4649-889b-04ead7265a3d', '10', 'no', 'no', '', '', 0),
('568c6171-2909-11f0-9192-3ae40c1bd964', '2a0b72e1-c81b-4561-95ab-64a0bec47340', '10', 'no', 'no', '', '', 0),
('f6eb8dd5-2917-11f0-9192-3ae40c1bd964', 'f36e7985-2171-4f34-9634-ed9f5215948f', 'yes', 'no', 'no', 'Main Street', 'Slough', 10),
('f756de8c-2908-11f0-9192-3ae40c1bd964', '09bb971f-056b-48dc-b902-75c407b6d7fc', '40', 'no', 'Pay & Display', 'Main Street', '1234 Mosque Lane, Slough', 10);

-- --------------------------------------------------------

--
-- Table structure for table `prayertimes`
--

CREATE TABLE `prayertimes` (
  `id` int(11) NOT NULL,
  `mosque_id` char(36) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `fajar_start` time DEFAULT NULL,
  `zuhr_start` time DEFAULT NULL,
  `asr_start` time DEFAULT NULL,
  `maghrib` time DEFAULT NULL,
  `isha_start` time DEFAULT NULL,
  `fajar_jamaat` time DEFAULT NULL,
  `zuhr_jamaat` time DEFAULT NULL,
  `asr_jamaat` time DEFAULT NULL,
  `isha_jamaat` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `prayertimes`
--

INSERT INTO `prayertimes` (`id`, `mosque_id`, `date`, `fajar_start`, `zuhr_start`, `asr_start`, `maghrib`, `isha_start`, `fajar_jamaat`, `zuhr_jamaat`, `asr_jamaat`, `isha_jamaat`) VALUES
(38, 'f36e7985-2171-4f34-9634-ed9f5215948f', '2025-05-12', '06:26:00', '13:48:00', '15:15:00', '19:43:00', '22:06:00', '07:20:00', '14:04:00', '15:28:00', '23:31:00'),
(39, 'f36e7985-2171-4f34-9634-ed9f5215948f', '2025-05-13', '05:23:00', '13:42:00', '16:02:00', '20:02:00', '22:11:00', '06:46:00', '12:11:00', '16:08:00', '22:44:00'),
(139, '42f7385d-60ac-4c73-adc8-1ca39654072d', '2025-04-30', '03:50:00', '13:05:00', '17:02:00', '20:29:00', '21:34:00', '04:45:00', '13:30:00', '18:00:00', '21:59:00'),
(140, '42f7385d-60ac-4c73-adc8-1ca39654072d', '2025-05-01', '03:46:00', '13:04:00', '17:03:00', '20:31:00', '21:35:00', '04:30:00', '13:30:00', '18:30:00', '21:45:00'),
(141, '42f7385d-60ac-4c73-adc8-1ca39654072d', '2025-05-02', '03:46:00', '13:04:00', '17:03:00', '20:32:00', '21:37:00', '04:30:00', '13:30:00', '18:30:00', '21:47:00'),
(142, '42f7385d-60ac-4c73-adc8-1ca39654072d', '2025-05-08', '03:36:00', '13:04:00', '17:07:00', '20:42:00', '21:46:00', '04:15:00', '13:30:00', '18:30:00', '21:56:00'),
(143, '42f7385d-60ac-4c73-adc8-1ca39654072d', '2025-05-03', '03:44:00', '13:04:00', '17:04:00', '20:34:00', '21:38:00', '04:30:00', '13:30:00', '18:30:00', '21:48:00'),
(144, '42f7385d-60ac-4c73-adc8-1ca39654072d', '2025-05-07', '03:35:00', '13:04:00', '17:06:00', '20:40:00', '21:44:00', '04:30:00', '13:30:00', '18:30:00', '22:10:00'),
(145, '42f7385d-60ac-4c73-adc8-1ca39654072d', '2025-05-10', '03:30:00', '13:04:00', '17:08:00', '20:45:00', '21:50:00', '04:15:00', '13:30:00', '18:30:00', '22:00:00'),
(146, '42f7385d-60ac-4c73-adc8-1ca39654072d', '2025-05-11', '03:28:00', '13:04:00', '17:09:00', '20:47:00', '21:52:00', '04:15:00', '13:30:00', '18:30:00', '22:02:00'),
(147, '42f7385d-60ac-4c73-adc8-1ca39654072d', '2025-05-04', '03:42:00', '13:04:00', '17:05:00', '20:36:00', '21:39:00', '04:30:00', '13:30:00', '18:30:00', '21:49:00'),
(148, '42f7385d-60ac-4c73-adc8-1ca39654072d', '2025-05-12', '03:26:00', '13:04:00', '17:10:00', '20:48:00', '21:53:00', '04:15:00', '13:30:00', '18:30:00', '22:03:00'),
(149, '42f7385d-60ac-4c73-adc8-1ca39654072d', '2025-05-13', '03:24:00', '13:04:00', '17:10:00', '20:50:00', '21:55:00', '04:15:00', '13:30:00', '18:30:00', '22:05:00'),
(150, '42f7385d-60ac-4c73-adc8-1ca39654072d', '2025-05-14', '03:22:00', '13:04:00', '17:11:00', '20:51:00', '21:57:00', '04:15:00', '13:30:00', '18:30:00', '22:21:00'),
(151, '42f7385d-60ac-4c73-adc8-1ca39654072d', '2025-05-16', '03:19:00', '13:04:00', '17:12:00', '20:54:00', '22:01:00', '04:15:00', '13:30:00', '18:30:00', '22:11:00'),
(152, '42f7385d-60ac-4c73-adc8-1ca39654072d', '2025-05-17', '03:17:00', '13:04:00', '17:12:00', '20:56:00', '22:02:00', '04:15:00', '13:30:00', '18:30:00', '22:12:00'),
(153, '42f7385d-60ac-4c73-adc8-1ca39654072d', '2025-05-18', '03:15:00', '13:04:00', '17:13:00', '20:57:00', '22:04:00', '04:15:00', '13:30:00', '18:30:00', '22:14:00'),
(154, '42f7385d-60ac-4c73-adc8-1ca39654072d', '2025-05-19', '03:14:00', '13:04:00', '17:14:00', '20:58:00', '22:06:00', '04:15:00', '13:30:00', '18:30:00', '22:16:00'),
(155, '42f7385d-60ac-4c73-adc8-1ca39654072d', '2025-05-22', '03:09:00', '13:04:00', '17:15:00', '21:03:00', '22:11:00', '04:00:00', '13:30:00', '19:45:00', '22:21:00'),
(156, '42f7385d-60ac-4c73-adc8-1ca39654072d', '2025-05-23', '03:08:00', '13:04:00', '17:16:00', '21:04:00', '22:13:00', '04:00:00', '13:30:00', '19:45:00', '22:23:00'),
(157, '42f7385d-60ac-4c73-adc8-1ca39654072d', '2025-05-24', '03:06:00', '13:04:00', '17:16:00', '21:05:00', '22:14:00', '04:00:00', '13:30:00', '19:45:00', '22:24:00'),
(158, '42f7385d-60ac-4c73-adc8-1ca39654072d', '2025-05-25', '03:05:00', '13:04:00', '17:17:00', '21:06:00', '22:16:00', '04:00:00', '13:30:00', '19:45:00', '22:26:00'),
(159, '42f7385d-60ac-4c73-adc8-1ca39654072d', '2025-05-21', '03:11:00', '13:04:00', '17:15:00', '21:01:00', '22:09:00', '04:15:00', '13:30:00', '18:30:00', '22:31:00'),
(160, '42f7385d-60ac-4c73-adc8-1ca39654072d', '2025-05-05', '03:40:00', '13:04:00', '17:05:00', '20:37:00', '21:41:00', '04:30:00', '13:30:00', '18:30:00', '21:51:00'),
(161, '42f7385d-60ac-4c73-adc8-1ca39654072d', '2025-05-26', '03:03:00', '13:05:00', '17:17:00', '21:08:00', '22:17:00', '04:00:00', '13:30:00', '19:45:00', '22:17:00'),
(162, '42f7385d-60ac-4c73-adc8-1ca39654072d', '2025-05-09', '03:32:00', '13:04:00', '17:08:00', '20:43:00', '21:48:00', '04:15:00', '13:30:00', '18:30:00', '21:58:00'),
(163, '42f7385d-60ac-4c73-adc8-1ca39654072d', '2025-05-27', '03:02:00', '13:05:00', '17:18:00', '21:09:00', '22:19:00', '04:00:00', '13:30:00', '19:45:00', '22:29:00'),
(164, '42f7385d-60ac-4c73-adc8-1ca39654072d', '2025-05-28', '03:01:00', '13:05:00', '17:18:00', '21:10:00', '22:20:00', '04:00:00', '13:30:00', '19:45:00', '22:30:00'),
(165, '42f7385d-60ac-4c73-adc8-1ca39654072d', '2025-05-20', '03:12:00', '13:04:00', '17:14:00', '21:00:00', '22:08:00', '04:15:00', '13:30:00', '18:30:00', '22:18:00'),
(166, '42f7385d-60ac-4c73-adc8-1ca39654072d', '2025-05-29', '03:00:00', '13:05:00', '17:19:00', '21:11:00', '22:22:00', '04:00:00', '13:30:00', '19:45:00', '22:32:00'),
(167, '42f7385d-60ac-4c73-adc8-1ca39654072d', '2025-05-30', '02:58:00', '13:05:00', '17:19:00', '21:12:00', '22:23:00', '04:00:00', '13:30:00', '19:45:00', '22:33:00'),
(168, '42f7385d-60ac-4c73-adc8-1ca39654072d', '2025-05-06', '03:38:00', '13:04:00', '17:06:00', '20:39:00', '21:42:00', '04:30:00', '13:30:00', '18:30:00', '21:52:00'),
(169, '42f7385d-60ac-4c73-adc8-1ca39654072d', '2025-05-15', '03:20:00', '13:04:00', '17:11:00', '20:53:00', '21:59:00', '04:15:00', '13:30:00', '18:30:00', '22:09:00'),
(176, 'ef6039e7-b569-4fb8-a440-3e1253bfa00a', '2025-05-05', '03:40:00', '13:04:00', '17:05:00', '20:38:00', '21:41:00', '04:30:00', '13:45:00', '18:30:00', '22:00:00'),
(177, 'ef6039e7-b569-4fb8-a440-3e1253bfa00a', '2025-04-30', '03:50:00', '13:05:00', '17:02:00', '20:29:00', '21:34:00', '04:45:00', '13:45:00', '18:30:00', '22:00:00'),
(178, 'ef6039e7-b569-4fb8-a440-3e1253bfa00a', '2025-05-06', '03:38:00', '13:04:00', '17:06:00', '20:39:00', '21:42:00', '04:30:00', '13:45:00', '18:30:00', '22:00:00'),
(179, 'ef6039e7-b569-4fb8-a440-3e1253bfa00a', '2025-05-07', '03:35:00', '13:04:00', '17:06:00', '20:41:00', '21:44:00', '04:30:00', '13:45:00', '18:30:00', '22:00:00'),
(180, 'ef6039e7-b569-4fb8-a440-3e1253bfa00a', '2025-05-10', '03:30:00', '13:04:00', '17:08:00', '20:45:00', '21:50:00', '04:15:00', '13:45:00', '18:30:00', '22:15:00'),
(181, 'ef6039e7-b569-4fb8-a440-3e1253bfa00a', '2025-05-01', '03:48:00', '13:04:00', '17:03:00', '20:31:00', '21:35:00', '04:30:00', '13:45:00', '18:30:00', '22:00:00'),
(182, 'ef6039e7-b569-4fb8-a440-3e1253bfa00a', '2025-05-04', '03:42:00', '13:04:00', '17:05:00', '20:36:00', '21:39:00', '04:30:00', '13:45:00', '18:30:00', '22:00:00'),
(183, 'ef6039e7-b569-4fb8-a440-3e1253bfa00a', '2025-05-03', '03:44:00', '13:04:00', '17:04:00', '20:34:00', '21:38:00', '04:30:00', '13:45:00', '18:30:00', '22:00:00'),
(184, 'ef6039e7-b569-4fb8-a440-3e1253bfa00a', '2025-05-08', '03:34:00', '13:04:00', '17:07:00', '20:42:00', '21:46:00', '04:15:00', '13:45:00', '18:30:00', '22:15:00'),
(185, 'ef6039e7-b569-4fb8-a440-3e1253bfa00a', '2025-05-02', '03:46:00', '13:04:00', '17:03:00', '20:33:00', '21:37:00', '04:30:00', '13:45:00', '18:30:00', '22:00:00'),
(186, 'ef6039e7-b569-4fb8-a440-3e1253bfa00a', '2025-05-12', '03:26:00', '13:04:00', '17:10:00', '20:49:00', '21:53:00', '04:15:00', '13:45:00', '18:30:00', '22:15:00'),
(187, 'ef6039e7-b569-4fb8-a440-3e1253bfa00a', '2025-05-13', '03:24:00', '13:04:00', '17:10:00', '20:50:00', '21:55:00', '04:15:00', '13:45:00', '18:30:00', '22:15:00'),
(188, 'ef6039e7-b569-4fb8-a440-3e1253bfa00a', '2025-05-14', '03:22:00', '13:04:00', '17:11:00', '20:52:00', '21:57:00', '04:15:00', '13:45:00', '18:30:00', '22:15:00'),
(189, 'ef6039e7-b569-4fb8-a440-3e1253bfa00a', '2025-05-09', '03:32:00', '13:04:00', '17:08:00', '20:44:00', '21:48:00', '04:15:00', '13:45:00', '18:30:00', '22:15:00'),
(190, 'ef6039e7-b569-4fb8-a440-3e1253bfa00a', '2025-05-16', '03:19:00', '13:04:00', '17:12:00', '20:54:00', '22:01:00', '04:00:00', '13:45:00', '18:30:00', '22:30:00'),
(191, 'ef6039e7-b569-4fb8-a440-3e1253bfa00a', '2025-05-15', '03:20:00', '13:04:00', '17:11:00', '20:53:00', '21:59:00', '04:00:00', '13:45:00', '18:30:00', '22:30:00'),
(192, 'ef6039e7-b569-4fb8-a440-3e1253bfa00a', '2025-05-17', '03:17:00', '13:04:00', '17:12:00', '20:56:00', '22:02:00', '04:00:00', '13:45:00', '18:30:00', '22:30:00'),
(193, 'ef6039e7-b569-4fb8-a440-3e1253bfa00a', '2025-05-19', '03:13:00', '13:04:00', '17:13:00', '20:59:00', '22:06:00', '04:00:00', '13:45:00', '18:30:00', '22:30:00'),
(194, 'ef6039e7-b569-4fb8-a440-3e1253bfa00a', '2025-05-18', '03:15:00', '13:04:00', '17:13:00', '20:57:00', '22:04:00', '04:00:00', '13:45:00', '18:30:00', '22:30:00'),
(195, 'ef6039e7-b569-4fb8-a440-3e1253bfa00a', '2025-05-20', '03:12:00', '13:04:00', '17:14:00', '21:00:00', '22:08:00', '04:00:00', '13:45:00', '18:30:00', '22:30:00'),
(196, 'ef6039e7-b569-4fb8-a440-3e1253bfa00a', '2025-05-21', '03:10:00', '13:04:00', '17:14:00', '21:02:00', '22:10:00', '04:00:00', '13:45:00', '18:30:00', '22:30:00'),
(197, 'ef6039e7-b569-4fb8-a440-3e1253bfa00a', '2025-05-22', '03:08:00', '13:04:00', '17:15:00', '21:03:00', '22:12:00', '04:00:00', '13:45:00', '18:30:00', '22:30:00'),
(198, 'ef6039e7-b569-4fb8-a440-3e1253bfa00a', '2025-05-23', '03:07:00', '13:04:00', '17:15:00', '21:05:00', '22:13:00', '04:00:00', '13:45:00', '18:30:00', '22:30:00'),
(199, 'ef6039e7-b569-4fb8-a440-3e1253bfa00a', '2025-05-24', '03:05:00', '13:04:00', '17:16:00', '21:06:00', '22:15:00', '04:00:00', '13:45:00', '18:30:00', '22:30:00'),
(200, 'ef6039e7-b569-4fb8-a440-3e1253bfa00a', '2025-05-25', '03:03:00', '13:04:00', '17:16:00', '21:08:00', '22:17:00', '04:00:00', '13:45:00', '18:30:00', '22:30:00'),
(201, 'ef6039e7-b569-4fb8-a440-3e1253bfa00a', '2025-05-26', '03:02:00', '13:04:00', '17:17:00', '21:09:00', '22:19:00', '04:00:00', '13:45:00', '18:30:00', '22:30:00'),
(202, 'ef6039e7-b569-4fb8-a440-3e1253bfa00a', '2025-05-11', '03:28:00', '13:04:00', '17:09:00', '20:47:00', '21:52:00', '04:15:00', '13:45:00', '18:30:00', '22:15:00'),
(203, 'ef6039e7-b569-4fb8-a440-3e1253bfa00a', '2025-05-27', '03:00:00', '13:04:00', '17:17:00', '21:11:00', '22:21:00', '04:00:00', '13:45:00', '18:30:00', '22:30:00'),
(204, 'ef6039e7-b569-4fb8-a440-3e1253bfa00a', '2025-05-29', '02:57:00', '13:04:00', '17:18:00', '21:14:00', '22:25:00', '04:00:00', '13:45:00', '18:30:00', '22:30:00'),
(205, 'ef6039e7-b569-4fb8-a440-3e1253bfa00a', '2025-05-28', '02:59:00', '13:04:00', '17:18:00', '21:12:00', '22:23:00', '04:00:00', '13:45:00', '18:30:00', '22:30:00'),
(206, 'ef6039e7-b569-4fb8-a440-3e1253bfa00a', '2025-05-30', '02:56:00', '13:04:00', '17:19:00', '21:15:00', '22:27:00', '04:00:00', '13:45:00', '18:30:00', '22:30:00'),
(207, 'f36e7985-2171-4f34-9634-ed9f5215948f', '2025-08-19', '05:59:00', '11:58:00', '14:09:00', '15:58:00', '17:36:00', '06:45:00', '13:15:00', '14:30:00', '19:15:00'),
(208, 'f36e7985-2171-4f34-9634-ed9f5215948f', '2025-08-20', '05:59:00', '11:58:00', '14:09:00', '15:58:00', '17:36:00', '06:45:00', '13:15:00', '14:30:00', '19:15:00'),
(209, 'f36e7985-2171-4f34-9634-ed9f5215948f', '2025-08-22', '05:59:00', '11:58:00', '14:09:00', '15:58:00', '17:36:00', '06:45:00', '13:15:00', '14:30:00', '19:15:00'),
(210, 'f36e7985-2171-4f34-9634-ed9f5215948f', '2025-08-24', '05:59:00', '11:58:00', '14:09:00', '15:58:00', '17:36:00', '06:45:00', '13:15:00', '14:30:00', '19:15:00'),
(211, 'f36e7985-2171-4f34-9634-ed9f5215948f', '2025-08-23', '05:59:00', '11:58:00', '14:09:00', '15:58:00', '17:36:00', '06:45:00', '13:15:00', '14:30:00', '19:15:00'),
(212, 'f36e7985-2171-4f34-9634-ed9f5215948f', '2025-08-21', '05:59:00', '11:58:00', '14:09:00', '15:58:00', '17:36:00', '06:45:00', '13:15:00', '14:30:00', '19:15:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mosques`
--
ALTER TABLE `mosques`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mosque_parking`
--
ALTER TABLE `mosque_parking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mosque_id` (`mosque_id`);

--
-- Indexes for table `parking`
--
ALTER TABLE `parking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `mosque_id` (`mosque_id`);

--
-- Indexes for table `prayertimes`
--
ALTER TABLE `prayertimes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_mosque_date` (`mosque_id`,`date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `prayertimes`
--
ALTER TABLE `prayertimes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=213;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `mosque_parking`
--
ALTER TABLE `mosque_parking`
  ADD CONSTRAINT `mosque_parking_ibfk_1` FOREIGN KEY (`mosque_id`) REFERENCES `mosques` (`id`);

--
-- Constraints for table `parking`
--
ALTER TABLE `parking`
  ADD CONSTRAINT `parking_ibfk_1` FOREIGN KEY (`mosque_id`) REFERENCES `mosques` (`id`);

--
-- Constraints for table `prayertimes`
--
ALTER TABLE `prayertimes`
  ADD CONSTRAINT `prayertimes_ibfk_1` FOREIGN KEY (`mosque_id`) REFERENCES `mosques` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
