-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 18, 2025 at 02:26 AM
-- Server version: 10.11.10-MariaDB
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u354989168_capstrack_db1`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_year`
--

CREATE TABLE `academic_year` (
  `id` int(11) NOT NULL,
  `start_year` int(11) NOT NULL,
  `start_month` int(11) NOT NULL DEFAULT 8,
  `start_day` int(11) NOT NULL DEFAULT 1,
  `end_year` int(11) NOT NULL,
  `end_month` int(11) NOT NULL DEFAULT 6,
  `end_day` int(11) NOT NULL DEFAULT 1,
  `nextsem_year` int(11) DEFAULT NULL,
  `nextsem_month` int(11) DEFAULT NULL,
  `nextsem_day` int(11) NOT NULL DEFAULT 1,
  `mode` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `academic_year`
--

INSERT INTO `academic_year` (`id`, `start_year`, `start_month`, `start_day`, `end_year`, `end_month`, `end_day`, `nextsem_year`, `nextsem_month`, `nextsem_day`, `mode`) VALUES
(1, 2024, 8, 12, 2025, 6, 5, 2025, 1, 14, 1),
(4, 2025, 8, 12, 2026, 6, 5, 2026, 1, 14, 1);

-- --------------------------------------------------------

--
-- Table structure for table `action_logs`
--

CREATE TABLE `action_logs` (
  `id` int(11) NOT NULL,
  `userID` int(20) NOT NULL,
  `action` mediumtext NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `action_logs`
--

INSERT INTO `action_logs` (`id`, `userID`, `action`, `date`) VALUES
(1, 20241009, 'Agus, Christian Angelo changed their password', '2024-12-11 19:27:37'),
(33, 20241013, 'BSIT 3DG1 Group 2 updated their title evaluation content', '2024-12-13 21:05:54'),
(34, 20241013, 'Cruz, Elliot Cruz has edited the title evaluation: title number: 1', '2024-12-13 21:09:48'),
(35, 20241013, 'Cruz, Elliot Cruz edited their title evaluation: title number: 1', '2024-12-13 21:10:30'),
(36, 20241013, 'Cruz, Elliot Caingat edited their title evaluation: title number: 2', '2024-12-13 21:11:27'),
(37, 20241013, 'Cruz, Elliot Caingat sent a capstone defense invitation to: Maguire, Tobey Cruz', '2024-12-13 21:21:09'),
(38, 20241013, 'Cruz, Elliot Caingat re-sent a capstone defense invitation to: Maguire, Tobey Cruz', '2024-12-13 21:23:37'),
(39, 20241013, 'Cruz, Elliot Caingat submitted their capstone paper', '2024-12-13 21:41:24'),
(40, 20241013, 'Cruz, Elliot Caingat re-submitted their capstone paper', '2024-12-13 21:44:08'),
(41, 20241013, 'Cruz, Elliot Caingat updated their email address', '2024-12-13 21:55:39'),
(42, 20241013, 'Cruz, Elliot Caingat updated their email address into: elliot@gmail.com', '2024-12-13 21:56:11'),
(43, 20241013, 'Cruz, Elliot Caingat changed their password', '2024-12-13 21:57:22'),
(44, 20241002, 'Ruffalo, Mark Banner created a new capstone group in BSIT 3DG1', '2024-12-13 23:40:51'),
(45, 20241002, 'Ruffalo, Mark Banner updated the defense date of Group 5 in BSIT 3DG1 into 2024-12-20 08:30', '2024-12-13 23:51:24'),
(46, 20241002, 'Ruffalo, Mark Banner updated the defense date of Group 1 in BSIT 3DG1 into December 20, 2024 1:25 PM', '2024-12-13 23:54:45'),
(47, 20241002, 'Ruffalo, Mark Banner accepted the capstone paper of Group 2 in BSIT 3DG1', '2024-12-14 00:07:44'),
(48, 20241002, 'Ruffalo, Mark Banner removed Garfield, Andrew  as a panelist in 2 in BSIT 3DG1', '2024-12-14 00:24:24'),
(49, 20241002, 'Ruffalo, Mark Banner removed Garfield, Andrew Parker as a panelist of Group2 in BSIT 3DG1', '2024-12-14 00:25:35'),
(50, 20241002, 'Ruffalo, Mark Banner removed Maguire, Tobey Cruz as a chairman panelist of Group 2 in BSIT 3DG1', '2024-12-14 00:26:45'),
(51, 20241002, 'Ruffalo, Mark Banner removed Holland, Tom Parker as a capstone adviser of Group 2 in BSIT 3DG1', '2024-12-14 00:28:46'),
(52, 20241002, 'Ruffalo, Mark Banner removed Cruz, Elliot Caingat as a member of Group 2 in BSIT 3DG1', '2024-12-14 00:32:48'),
(53, 20241002, 'Ruffalo, Mark Banner added Holland, Tom Parker as a capstone adviser of Group 2 in BSIT 3DG1', '2024-12-14 00:35:37'),
(54, 20241002, 'Ruffalo, Mark Banner removed Maguire, Tobey Cruz as a chairman panelist of Group 2 in BSIT 3DG1', '2024-12-14 00:41:04'),
(55, 20241002, 'Ruffalo, Mark Banner added Maguire, Tobey Cruz as the chairman panelist of Group 2 in BSIT 3DG1', '2024-12-14 00:41:12'),
(56, 20241002, 'Ruffalo, Mark Banner added Garfield, Andrew Parker as a panelist of Group 2 in BSIT 3DG1', '2024-12-14 00:42:42'),
(57, 20241002, 'Ruffalo, Mark Banner added Cruz, Elliot Caingat as a member of Group 2 in BSIT 3DG1', '2024-12-14 00:44:12'),
(58, 20241008, 'Cruz, Juan Delos Santos created the specialization: Artificial Intelligence belonging to Bachelor of Science in Information Technology', '2024-12-14 00:51:46'),
(59, 20241008, 'Cruz, Juan Delos Santos created a new section in  Web and Mobile Development belonging to Bachelor of Science in Information Technology', '2024-12-14 00:58:26'),
(60, 20241008, 'Cruz, Juan Delos Santos created a new section in  No Specialization belonging to Bachelor of Science in Information Technology', '2024-12-14 01:01:56'),
(61, 20241003, 'Gosling, Ryan Cruz added Cage, Nic Santos as a capstone adviser of Group 1 in BSIT 3AG1', '2024-12-14 12:07:55'),
(62, 20241003, 'Gosling, Ryan Cruz added Maguire, Tobey Cruz as the chairman panelist of Group 1 in BSIT 3AG1', '2024-12-14 12:08:03'),
(63, 20241003, 'Gosling, Ryan Cruz added Garfield, Andrew Parker as a panelist of Group 1 in BSIT 3AG1', '2024-12-14 12:08:11'),
(64, 20241003, 'Gosling, Ryan Cruz added Kirai, Hazurii Shin as a member of Group 1 in BSIT 3AG1', '2024-12-14 12:09:15'),
(65, 20241003, 'Gosling, Ryan Cruz removed Cage, Nic Santos as a capstone adviser of Group 1 in BSIT 3AG1', '2024-12-14 12:11:52'),
(66, 20241003, 'Gosling, Ryan Cruz removed Maguire, Tobey Cruz as a chairman panelist of Group 1 in BSIT 3AG1', '2024-12-14 12:14:25'),
(67, 20241003, 'Gosling, Ryan Cruz removed Garfield, Andrew Parker as a panelist of Group 1 in BSIT 3AG1', '2024-12-14 12:15:10'),
(68, 20241003, 'Gosling, Ryan Cruz added Garfield, Andrew Parker as a panelist of Group 1 in BSIT 3AG1', '2024-12-14 12:15:26'),
(69, 20241003, 'Gosling, Ryan Cruz removed Garfield, Andrew Parker as a panelist of Group 1 in BSIT 3AG1', '2024-12-14 12:16:02'),
(70, 20241003, 'Gosling, Ryan Cruz added Ruffalo, Mark Banner as a capstone adviser of Group 1 in BSIT 3AG1', '2024-12-14 12:16:26'),
(71, 20241003, 'Gosling, Ryan Cruz removed Kirai, Hazurii Shin as a member of Group 1 in BSIT 3AG1', '2024-12-14 12:16:33'),
(72, 20241003, 'Gosling, Ryan Cruz added Kirai, Hazurii Shin as a member of Group 1 in BSIT 3AG1', '2024-12-14 12:16:47'),
(73, 20241003, 'Gosling, Ryan Cruz removed Ruffalo, Mark Banner as a capstone adviser of Group 1 in BSIT 3AG1', '2024-12-14 12:16:56'),
(74, 20241003, 'Gosling, Ryan Cruz removed Kirai, Hazurii Shin as a member of Group 1 in BSIT 3AG1', '2024-12-14 12:17:40'),
(75, 20241001, 'Agus, Christian Angelo Robles created the specialization: Machine Learning belonging to Bachelor of Science in Artificial Technology', '2024-12-14 12:32:03'),
(76, 20241001, 'Agus, Christian Angelo Robles created a new section in  Machine Learning belonging to Bachelor of Science in Artificial Technology', '2024-12-14 12:34:12'),
(77, 20241003, 'Gosling, Ryan Cruz added Agus, Christian Angelo Robles as a member of Group 1 in BSIT 3AG1', '2024-12-14 13:39:51'),
(78, 20241003, 'Gosling, Ryan Cruz added Agus, Christian Angelo Robles as a panelist of Group 1 in BSIT 3AG1', '2024-12-14 13:40:04'),
(79, 20241003, 'Gosling, Ryan Cruz added Cage, Nic Santos as a panelist of Group 1 in BSIT 3AG1', '2024-12-14 13:40:08'),
(80, 20241003, 'Gosling, Ryan Cruz added Ruffalo, Mark Banner as a panelist of Group 1 in BSIT 3AG1', '2024-12-14 13:40:13'),
(81, 20241003, 'Gosling, Ryan Cruz added Maguire, Tobey Cruz as the chairman panelist of Group 1 in BSIT 3AG1', '2024-12-14 13:40:21'),
(82, 20241003, 'Gosling, Ryan Cruz added Holland, Tom Parker as a capstone adviser of Group 1 in BSIT 3AG1', '2024-12-14 13:40:33'),
(83, 20241003, 'Gosling, Ryan Cruz added Kirai, Hazurii Shin as a member of Group 1 in BSIT 3AG1', '2024-12-14 13:40:44'),
(84, 20241002, 'Ruffalo, Mark Banner added Agus, Gelo Robles as a member of Group 1 in BSIT 3DG1', '2024-12-14 13:47:59'),
(85, 20241068, 'Agus, Christian Angelo Robles edited their title evaluation: title number: 1', '2024-12-14 13:53:03'),
(86, 20241002, 'Ruffalo, Mark Banner removed Maguire, Tobey Cruz as a panelist of Group 1 in BSIT 3DG1', '2024-12-14 14:07:57'),
(87, 20241002, 'Ruffalo, Mark Banner removed Maguire, Tobey Cruz as a chairman panelist of Group 2 in BSIT 3DG1', '2024-12-14 14:08:44'),
(88, 20241006, 'Maguire, Tobey Cruz created a new capstone group in BSIS 3AG1', '2024-12-14 15:09:53'),
(89, 20241068, 'Agus, Angelo Robles sent a capstone defense invitation to: Maguire, Tobey Cruz', '2024-12-14 16:24:29'),
(90, 20241068, 'Agus, Angelo Robles re-sent a capstone defense invitation to: Maguire, Tobey Cruz', '2024-12-14 16:24:39'),
(91, 20241006, 'Maguire, Tobey Cruz created a new section in  No Specialization belonging to Bachelor of Science in Cybersecurity', '2024-12-14 16:32:40'),
(92, 20241003, 'Gosling, Ryan Cruz removed Maguire, Tobey Cruz as a chairman panelist of Group 1 in BSIT 3AG1', '2024-12-14 16:48:37'),
(93, 20241009, 'Agus, Gelo Robles re-sent a capstone defense invitation to: Agus, Christian Angelo Robles', '2024-12-14 18:53:36'),
(94, 20241009, 'Agus, Gelo Robles re-sent a capstone defense invitation to: Agus, Christian Angelo Robles', '2024-12-14 19:09:35'),
(95, 20241002, 'Ruffalo, Mark Banner created a new capstone group in BSIS 3AG1', '2024-12-14 19:30:53'),
(96, 20241001, 'Agus, Christian Angelo Robles created a new capstone group in BSIT 3KG1', '2024-12-14 19:38:22'),
(97, 20241002, 'Ruffalo, Mark Banner removed Gosling, Ryan Cruz as a chairman panelist of Group 1 in BSIT 3DG1', '2024-12-14 19:42:54'),
(98, 20241002, 'Ruffalo, Mark Banner removed Agus, Gelo Robles as a member of Group 1 in BSIT 3DG1', '2024-12-14 19:45:48'),
(99, 20241002, 'Ruffalo, Mark Banner added Cruz, Juan Delos Santos as a panelist of Group 1 in BSIT 3DG1', '2024-12-14 19:47:17'),
(100, 20241002, 'Ruffalo, Mark Banner removed Cruz, Juan Delos Santos as a panelist of Group 1 in BSIT 3DG1', '2024-12-14 19:48:44'),
(101, 20241002, 'Ruffalo, Mark Banner added Garfield, Andrew Parker as the chairman panelist of Group 1 in BSIT 3DG1', '2024-12-14 19:49:50'),
(102, 20241002, 'Ruffalo, Mark Banner removed Garfield, Andrew Parker as a chairman panelist of Group 1 in BSIT 3DG1', '2024-12-14 19:50:01'),
(103, 20241002, 'Ruffalo, Mark Banner removed Agus, Christian Angelo Robles as a capstone adviser of Group 1 in BSIT 3DG1', '2024-12-14 19:51:13'),
(104, 20241002, 'Ruffalo, Mark Banner added Agus, Gelo Robles as a member of Group 1 in BSIT 3DG1', '2024-12-14 19:52:48'),
(105, 20241002, 'Ruffalo, Mark Banner added Garfield, Andrew Parker as the chairman panelist of Group 1 in BSIT 3DG1', '2024-12-14 19:53:48'),
(106, 20241002, 'Ruffalo, Mark Banner added Holland, Tom Parker as a panelist of Group 1 in BSIT 3DG1', '2024-12-14 19:55:27'),
(107, 20241002, 'Ruffalo, Mark Banner added Cruz, Juan Delos Santos as a capstone adviser of Group 1 in BSIT 3DG1', '2024-12-14 19:56:18'),
(108, 20241054, 'Kirai, Hazurii Shin edited their title evaluation: title number: 2', '2024-12-14 21:00:34'),
(109, 20241001, 'Agus, Christian Angelo Robles created a new section in  Business Analytics belonging to Bachelor of Science in Information Technology', '2024-12-14 21:17:12'),
(110, 20241001, 'Agus, Christian Angelo Robles created a new capstone group in BSIT 3IG2', '2024-12-14 21:17:24'),
(111, 20241001, 'Agus, Christian Angelo Robles added Gosling, Ryan Cruz as a capstone adviser of Group 1 in BSIT 3IG2', '2024-12-14 21:17:36'),
(112, 20241001, 'Agus, Christian Angelo Robles added Holland, Tom Parker as a panelist of Group 1 in BSIT 3IG2', '2024-12-14 21:17:50'),
(113, 20241001, 'Agus, Christian Angelo Robles added Ruffalo, Mark Banner as a panelist of Group 1 in BSIT 3IG2', '2024-12-14 21:17:56'),
(114, 20241001, 'Agus, Christian Angelo Robles added Cage, Nic Santos as a panelist of Group 1 in BSIT 3IG2', '2024-12-14 21:18:00'),
(115, 20241001, 'Agus, Christian Angelo Robles added Maguire, Tobey Cruz as a panelist of Group 1 in BSIT 3IG2', '2024-12-14 21:18:05'),
(116, 20241001, 'Agus, Christian Angelo Robles created a new section in  Networking belonging to Bachelor of Science in Information Systems', '2024-12-14 21:32:46'),
(117, 20241001, 'Agus, Christian Angelo Robles created a new section in  Networking belonging to Bachelor of Science in Information Systems', '2024-12-14 21:32:52'),
(118, 20241001, 'Agus, Christian Angelo Robles created a new section in  Networking belonging to Bachelor of Science in Information Systems', '2024-12-14 21:33:04'),
(119, 20241001, 'Agus, Christian Angelo Robles created a new section in  Networking belonging to Bachelor of Science in Information Systems', '2024-12-14 21:33:12'),
(120, 20241001, 'Agus, Christian Angelo Robles created a new capstone group in BSIS 3A', '2024-12-14 21:33:22'),
(121, 20241001, 'Agus, Christian Angelo Robles added Ruffalo, Mark Banner as a capstone adviser of Group 1 in BSIS 3A', '2024-12-14 21:33:33'),
(122, 20241001, 'Agus, Christian Angelo Robles added Gosling, Ryan Cruz as the chairman panelist of Group 1 in BSIS 3A', '2024-12-14 21:33:47'),
(123, 20241001, 'Agus, Christian Angelo Robles added Maguire, Tobey Cruz as the chairman panelist of Group 1 in BSIS 3A', '2024-12-14 21:33:52'),
(124, 20241001, 'Agus, Christian Angelo Robles added Garfield, Andrew Parker as the chairman panelist of Group 1 in BSIS 3A', '2024-12-14 21:33:56'),
(125, 20241001, 'Agus, Christian Angelo Robles removed Ruffalo, Mark Banner as a capstone adviser of Group 1 in BSIS 3A', '2024-12-14 21:34:05'),
(126, 20241001, 'Agus, Christian Angelo Robles added Holland, Tom Parker as a capstone adviser of Group 1 in BSIS 3A', '2024-12-14 21:34:10'),
(127, 20241001, 'Agus, Christian Angelo Robles removed Garfield, Andrew Parker as a chairman panelist of Group 1 in BSIS 3A', '2024-12-14 21:34:20'),
(128, 20241001, 'Agus, Christian Angelo Robles added Ruffalo, Mark Banner as a panelist of Group 1 in BSIS 3A', '2024-12-14 21:34:28'),
(129, 20241003, 'Gosling, Ryan Cruz created the specialization: Service Management belonging to Bachelor of Science in Artificial Technology', '2024-12-14 21:42:19'),
(130, 20241054, 'Kirai, Hazurii Shin sent a capstone defense invitation to: Holland, Tom Parker', '2024-12-14 23:01:33'),
(131, 20241054, 'Kirai, Hazurii Shin sent a capstone defense invitation to: Ruffalo, Mark Banner', '2024-12-14 23:09:54'),
(132, 20241054, 'Kirai, Hazurii Shin submitted their capstone paper', '2024-12-14 23:14:31'),
(133, 20241002, 'Ruffalo, Mark Banner accepted the capstone paper of Group 1 in BSIT 3AG1', '2024-12-14 23:14:38'),
(134, 20241054, 'Kirai, Hazurii Shin sent a capstone defense invitation to: Cage, Nic Santos', '2024-12-14 23:17:11'),
(135, 20241002, 'Ruffalo, Mark Banner removed Holland, Tom Parker as a capstone adviser of Group 2 in BSIT 3DG1', '2024-12-14 23:25:26'),
(136, 20241002, 'Ruffalo, Mark Banner removed Garfield, Andrew Parker as a panelist of Group 2 in BSIT 3DG1', '2024-12-14 23:25:33'),
(137, 20241002, 'Ruffalo, Mark Banner removed Cruz, Elliot Caingat as a member of Group 2 in BSIT 3DG1', '2024-12-14 23:25:37'),
(138, 20241002, 'Ruffalo, Mark Banner added Holland, Tom Parker as a capstone adviser of Group 2 in BSIT 3DG1', '2024-12-14 23:27:05'),
(139, 20241002, 'Ruffalo, Mark Banner removed Holland, Tom Parker as a capstone adviser of Group 2 in BSIT 3DG1', '2024-12-14 23:27:22'),
(140, 20241002, 'Ruffalo, Mark Banner added Holland, Tom Parker as a capstone adviser of Group 2 in BSIT 3DG1', '2024-12-14 23:27:29'),
(141, 20241002, 'Ruffalo, Mark Banner added Gosling, Ryan Cruz as the chairman panelist of Group 2 in BSIT 3DG1', '2024-12-14 23:30:10'),
(142, 20241002, 'Ruffalo, Mark Banner removed Gosling, Ryan Cruz as a chairman panelist of Group 2 in BSIT 3DG1', '2024-12-14 23:30:28'),
(143, 20241002, 'Ruffalo, Mark Banner added Gosling, Ryan Cruz as the chairman panelist of Group 2 in BSIT 3DG1', '2024-12-14 23:30:38'),
(144, 20241002, 'Ruffalo, Mark Banner added Garfield, Andrew Parker as a panelist of Group 2 in BSIT 3DG1', '2024-12-14 23:33:35'),
(145, 20241002, 'Ruffalo, Mark Banner added Cruz, Elliot Caingat as a member of Group 2 in BSIT 3DG1', '2024-12-14 23:35:07'),
(146, 20241002, 'Ruffalo, Mark Banner removed Holland, Tom Parker as a capstone adviser of Group 2 in BSIT 3DG1', '2024-12-14 23:36:38'),
(147, 20241002, 'Ruffalo, Mark Banner removed Gosling, Ryan Cruz as a chairman panelist of Group 2 in BSIT 3DG1', '2024-12-14 23:37:45'),
(148, 20241002, 'Ruffalo, Mark Banner removed Garfield, Andrew Parker as a panelist of Group 2 in BSIT 3DG1', '2024-12-14 23:38:42'),
(149, 20241002, 'Ruffalo, Mark Banner removed Cruz, Elliot Caingat as a member of Group 2 in BSIT 3DG1', '2024-12-14 23:39:48'),
(150, 20241001, 'Agus, Christian Angelo Robles created a new section in  Service Management belonging to Bachelor of Science in Information Technology', '2024-12-15 03:17:12'),
(151, 20241009, 'Agus, Gelo Robles updated their email address into: agus_03_christian@gmail.com', '2024-12-15 12:45:34'),
(152, 20241009, 'Agus, Gelo Robles updated their email address into: christianagus03@gmail.com', '2024-12-15 12:46:08'),
(153, 20241009, 'Agus, Gelo Robles changed their password', '2024-12-15 12:50:43'),
(154, 20241009, 'Agus, Gelo Robles changed their password', '2024-12-15 12:55:23'),
(155, 20241009, 'Agus, Gelo Robles changed their password', '2024-12-15 12:57:19'),
(156, 20241009, 'Agus, Gelo Robles changed their password', '2024-12-15 13:05:06'),
(157, 20241002, 'Ruffalo, Mark Banner updated the defense date of Group 2 in BSIT 3DG1 into December 18, 2024 10:00 AM', '2024-12-17 18:28:38'),
(158, 20241002, 'Ruffalo, Mark Banner updated the defense date of Group 2 in BSIT 3DG1 into December 3, 2024 10:00 AM', '2024-12-17 18:28:56'),
(159, 20241002, 'Ruffalo, Mark Banner updated the defense date of Group 6 in BSIT 3DG1 into December 18, 2024 12:00 PM', '2024-12-18 21:37:41'),
(160, 20241002, 'Ruffalo, Mark Banner updated the defense date of Group 2 in BSIT 3DG1 into December 4, 2024 10:00 AM', '2024-12-18 21:40:42'),
(161, 20241002, 'Ruffalo, Mark Banner updated the defense date of Group 2 in BSIT 3DG1 into December 5, 2024 10:00 AM', '2024-12-18 21:42:40'),
(162, 20241002, 'Ruffalo, Mark Banner updated the defense date of Group 2 in BSIT 3DG1 into December 19, 2024 10:00 AM', '2024-12-18 22:03:14'),
(163, 20241002, 'Ruffalo, Mark Banner updated the defense date of Group 2 in BSIT 3DG1 into December 18, 2024 10:00 AM', '2024-12-18 22:03:22'),
(164, 20241002, 'Ruffalo, Mark Banner updated the defense date of Group 2 in BSIT 3DG1 into December 30, 2024 10:00 AM', '2024-12-18 22:04:17'),
(165, 20241002, 'Ruffalo, Mark Banner updated the defense date of Group 7 in BSIT 3DG1 into December 21, 2024 12:00 AM', '2024-12-19 09:36:41'),
(166, 20241002, 'Ruffalo, Mark Banner added Holland, Tom Parker as a capstone adviser of Group 2 in BSIT 3DG1', '2024-12-19 14:03:15'),
(167, 20241002, 'Ruffalo, Mark Banner removed Holland, Tom Parker as a capstone adviser of Group 2 in BSIT 3DG1', '2024-12-19 14:03:23'),
(168, 20241002, 'Ruffalo, Mark Banner added Holland, Tom Parker as a capstone adviser of Group 2 in BSIT 3DG1', '2024-12-19 14:03:29'),
(169, 20241002, 'Ruffalo, Mark Banner removed Holland, Tom Parker as a capstone adviser of Group 2 in BSIT 3DG1', '2024-12-19 14:05:15'),
(170, 20241002, 'Ruffalo, Mark Banner removed Garfield, Andrew Parker as a chairman panelist of Group 1 in BSIT 3DG1', '2024-12-19 14:54:08'),
(171, 20241002, 'Ruffalo, Mark Banner added Holland, Tom Parker as a capstone adviser of Group 2 in BSIT 3DG1', '2024-12-19 16:08:16'),
(172, 20241002, 'Ruffalo, Mark Banner removed Holland, Tom Parker as a capstone adviser of Group 2 in BSIT 3DG1', '2024-12-19 16:08:29'),
(173, 20241002, 'Ruffalo, Mark Banner added Agus, Christian Angelo Robles as the chairman panelist of Group 1 in BSIT 3DG1', '2024-12-19 16:42:44'),
(174, 20241002, 'Ruffalo, Mark Banner added Gosling, Ryan Cruz as the chairman panelist of Group 1 in BSIT 3DG1', '2024-12-19 16:42:49'),
(175, 20241002, 'Ruffalo, Mark Banner removed Agus, Christian Angelo Robles as a chairman panelist of Group 1 in BSIT 3DG1', '2024-12-19 16:42:57'),
(176, 20241002, 'Ruffalo, Mark Banner removed Gosling, Ryan Cruz as a chairman panelist of Group 1 in BSIT 3DG1', '2024-12-19 17:36:27'),
(177, 20241001, 'Agus, Christian Angelo Robles created the specialization: Artificial Intelligence belonging to Bachelor of Science in Cybersecurity', '2024-12-19 17:46:41'),
(178, 20241003, 'Gosling, Ryan Cruz created a new capstone group in BSAI 3A', '2024-12-19 17:55:02'),
(179, 20241003, 'Gosling, Ryan Cruz created a new capstone group in BSAI 3A', '2024-12-19 17:55:04'),
(180, 20241003, 'Gosling, Ryan Cruz added Ruffalo, Mark Banner as the chairman panelist of Group 1 in BSAI 3A', '2024-12-19 17:55:22'),
(181, 20241002, 'Ruffalo, Mark Banner added Gosling, Ryan Cruz as the chairman panelist of Group 1 in BSIT 3DG1', '2025-01-07 16:32:23'),
(182, 20241001, 'Agus, Christian Angelo Robles updated the academic year with the settings of: 2024-2025 / Start month: 08, 12 / End month: 06, 05 / Next sem year and month: 01, 13, 2025 / Mode: 1', '2025-01-15 10:07:20'),
(183, 20241001, 'Agus, Christian Angelo Robles updated the academic year with the settings of: 2024-2025 / Start month: August, 12 / End month: June, 05 / Next sem year and month: January, 14, 2025 / Mode: 1', '2025-01-15 10:13:23');

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `userID` int(20) NOT NULL,
  `projectID` int(11) NOT NULL,
  `taskID` int(11) NOT NULL,
  `description` mediumtext NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `trackingNum` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `userID`, `projectID`, `taskID`, `description`, `date`, `time`, `trackingNum`) VALUES
(122, 20241009, 1, 1, 'Student: Agus, Gelo has edited the title evaluation: title number: 1', '2024-11-19', '16:02:24', '20241104IonIQYvVGaFyTAWc'),
(123, 20241009, 1, 1, 'Student: Agus, Gelo has edited the title evaluation: title number: 1', '2024-11-19', '16:23:20', '20241104IonIQYvVGaFyTAWc'),
(126, 20241009, 1, 1, 'Student: Agus, Gelo has edited the title evaluation: title number: 1', '2024-11-23', '18:40:55', '20241104IonIQYvVGaFyTAWc'),
(127, 20241009, 1, 1, 'Student: Agus, Gelo has edited the title evaluation: title number: 1', '2024-11-23', '18:42:31', '20241104IonIQYvVGaFyTAWc'),
(128, 20241009, 1, 1, 'Student: Agus, Gelo has edited the title evaluation: title number: 1', '2024-11-23', '20:33:45', '20241104IonIQYvVGaFyTAWc'),
(129, 20241009, 1, 1, 'Student: Agus, Gelo has edited the title evaluation: title number: 1', '2024-11-23', '21:03:58', '20241104IonIQYvVGaFyTAWc'),
(130, 20241003, 1, 1, 'Panelist: Gosling, Ryan evaluated the title evaluation: title number: 1 with the evaluation: NEEDS IMPROVEMENT', '2024-11-23', '21:12:49', '20241104IonIQYvVGaFyTAWc'),
(131, 20241003, 1, 1, 'Panelist: Gosling, Ryan evaluated the title evaluation: title number: 1 with the evaluation: NEEDS IMPROVEMENT', '2024-11-23', '21:15:00', '20241104IonIQYvVGaFyTAWc'),
(132, 20241004, 1, 1, 'Panelist: Cage, Nic evaluated the title evaluation: title number: 1 with the evaluation: ACCEPTED', '2024-11-23', '21:35:22', '20241104IonIQYvVGaFyTAWc'),
(133, 20241004, 1, 1, 'Panelist: Cage, Nic evaluated the title evaluation: title number: 1 with the evaluation: ACCEPTED', '2024-11-23', '21:42:09', '20241104IonIQYvVGaFyTAWc'),
(134, 20241004, 1, 1, 'Panelist: Cage, Nic evaluated the title evaluation: title number: 1 with the evaluation: ACCEPTED', '2024-11-23', '21:42:09', '20241104IonIQYvVGaFyTAWc'),
(135, 20241003, 1, 1, 'Panelist: Gosling, Ryan evaluated the title evaluation: title number: 1 with the evaluation: NEEDS IMPROVEMENT', '2024-11-23', '21:43:16', '20241104IonIQYvVGaFyTAWc'),
(136, 20241003, 1, 1, 'Panelist: Gosling, Ryan evaluated the title evaluation: title number: 1 with the evaluation: NEEDS IMPROVEMENT', '2024-11-23', '21:43:16', '20241104IonIQYvVGaFyTAWc'),
(137, 20241003, 1, 1, 'Panelist: Gosling, Ryan evaluated the title evaluation: title number: 1 with the evaluation: NEEDS IMPROVEMENT', '2024-11-23', '21:44:16', '20241104IonIQYvVGaFyTAWc'),
(138, 20241003, 1, 1, 'Chairman Panelist: Gosling, Ryan evaluated the title evaluation: title number: 1 with the evaluation: ACCEPTED', '2024-11-23', '21:54:56', '20241104IonIQYvVGaFyTAWc'),
(139, 20241003, 1, 1, 'Panelist: Gosling, Ryan evaluated the title evaluation: title number: 1 with the evaluation: ACCEPTED', '2024-11-23', '21:54:56', '20241104IonIQYvVGaFyTAWc'),
(140, 20241009, 1, 2, 'Student: Agus, Gelo updated the defense invitation', '2024-11-23', '22:26:18', '20241104ojgysxzGRgnpXgHE'),
(141, 20241004, 1, 2, 'Panelist: Cage, Nic accepted the defense invitation', '2024-11-23', '23:29:20', '20241104ojgysxzGRgnpXgHE'),
(142, 20241009, 1, 3, 'Student: Agus, Gelo updated the capstone paper', '2024-11-23', '23:55:41', '20241104oxpfuqyymONCuamh'),
(143, 20241009, 1, 3, 'Student: Agus, Gelo updated the capstone paper and comments', '2024-11-24', '00:04:47', '20241104oxpfuqyymONCuamh'),
(144, 20241009, 1, 3, 'Student: Agus, Gelo updated the capstone paper and comments', '2024-11-24', '00:06:04', '20241104oxpfuqyymONCuamh'),
(145, 20241009, 1, 3, 'Student: Agus, Gelo updated the capstone paper', '2024-11-24', '00:06:14', '20241104oxpfuqyymONCuamh'),
(146, 20241009, 1, 3, 'Student: Agus, Gelo updated the capstone paper', '2024-11-24', '00:07:16', '20241104oxpfuqyymONCuamh'),
(147, 20241009, 1, 3, 'Student: Agus, Gelo updated the capstone paper and comments', '2024-11-24', '00:12:06', '20241104oxpfuqyymONCuamh'),
(148, 20241002, 1, 3, 'Coordinator: Ruffalo, Mark accepted the capstone paper', '2024-11-24', '00:41:12', '20241104oxpfuqyymONCuamh'),
(149, 20241009, 1, 2, 'Student: Agus, Gelo updated the defense invitation', '2024-12-10', '10:27:07', '20241104ojgysxzGRgnpXgHE'),
(150, 20241009, 1, 2, 'Student: Agus, Gelo updated the defense invitation', '2024-12-10', '10:28:37', '20241104ojgysxzGRgnpXgHE'),
(151, 20241009, 1, 3, 'Student: Agus, Gelo updated the capstone paper', '2024-12-10', '10:30:35', '20241104oxpfuqyymONCuamh'),
(152, 20241002, 1, 3, 'Coordinator: Ruffalo, Mark accepted the capstone paper', '2024-12-10', '10:32:47', '20241104oxpfuqyymONCuamh'),
(153, 20241013, 4, 1, 'Student: Cruz, Elliot has edited the title evaluation: title number: 1', '2024-12-13', '21:05:54', '20241104zWzhJAjkDcQwonXK'),
(154, 20241013, 4, 1, 'Student: Cruz, Elliot has edited the title evaluation: title number: 1', '2024-12-13', '21:09:48', '20241104zWzhJAjkDcQwonXK'),
(155, 20241013, 4, 1, 'Student: Cruz, Elliot has edited the title evaluation: title number: 1', '2024-12-13', '21:10:30', '20241104zWzhJAjkDcQwonXK'),
(156, 20241013, 4, 1, 'Student: Cruz, Elliot has edited the title evaluation: title number: 2', '2024-12-13', '21:11:27', '20241104zWzhJAjkDcQwonXK'),
(157, 20241013, 4, 2, 'Student: Cruz, Elliot updated the defense invitation', '2024-12-13', '21:21:09', '20241104YXIwGJqJttuoitrX'),
(158, 20241013, 4, 2, 'Student: Cruz, Elliot updated the defense invitation', '2024-12-13', '21:23:37', '20241104YXIwGJqJttuoitrX'),
(159, 20241013, 4, 3, 'Student: Cruz, Elliot updated the capstone paper', '2024-12-13', '21:41:24', '20241104szZVWIiiraIuZzna'),
(160, 20241013, 4, 3, 'Student: Cruz, Elliot updated the capstone paper', '2024-12-13', '21:44:08', '20241104szZVWIiiraIuZzna'),
(161, 20241002, 4, 3, 'Coordinator: Ruffalo, Mark accepted the capstone paper', '2024-12-14', '00:04:28', '20241104szZVWIiiraIuZzna'),
(162, 20241002, 4, 3, 'Coordinator: Ruffalo, Mark accepted the capstone paper', '2024-12-14', '00:07:51', '20241104szZVWIiiraIuZzna'),
(163, 20241001, 15, 1, 'Panelist: Agus, Christian Angelo evaluated the title evaluation: title number: 1 with the evaluation: ACCEPTED', '2024-12-14', '13:42:21', '20241212gIOqsBUhEKhiPvXl'),
(164, 20241001, 15, 1, 'Panelist: Agus, Christian Angelo evaluated the title evaluation: title number: 1 with the evaluation: ACCEPTED', '2024-12-14', '13:42:21', '20241212gIOqsBUhEKhiPvXl'),
(165, 20241068, 15, 1, 'Student: Agus, Christian Angelo has edited the title evaluation: title number: 1', '2024-12-14', '13:53:03', '20241212gIOqsBUhEKhiPvXl'),
(166, 20241068, 15, 2, 'Student: Agus, Angelo updated the defense invitation', '2024-12-14', '16:24:29', '20241212SPkcphEcaUAYUPhz'),
(167, 20241068, 15, 2, 'Student: Agus, Angelo updated the defense invitation', '2024-12-14', '16:24:39', '20241212SPkcphEcaUAYUPhz'),
(168, 20241009, 1, 2, 'Student: Agus, Gelo updated the defense invitation and comments', '2024-12-14', '18:52:20', '20241104ojgysxzGRgnpXgHE'),
(169, 20241009, 1, 2, 'Student: Agus, Gelo updated the defense invitation', '2024-12-14', '18:53:36', '20241104ojgysxzGRgnpXgHE'),
(170, 20241009, 1, 2, 'Student: Agus, Gelo updated the defense invitation', '2024-12-14', '19:09:35', '20241104ojgysxzGRgnpXgHE'),
(171, 20241001, 15, 1, 'Panelist: Agus, Christian Angelo evaluated the title evaluation: title number: 1 with the evaluation: ACCEPTED', '2024-12-14', '19:36:19', '20241212gIOqsBUhEKhiPvXl'),
(172, 20241001, 15, 1, 'Panelist: Agus, Christian Angelo evaluated the title evaluation: title number: 1 with the evaluation: ACCEPTED', '2024-12-14', '19:36:19', '20241212gIOqsBUhEKhiPvXl'),
(173, 20241006, 15, 2, 'Chairman Panelist: Maguire, Tobey accepted the defense invitation', '2024-12-14', '20:06:37', '20241212SPkcphEcaUAYUPhz'),
(174, 20241006, 15, 1, 'Panelist: Maguire, Tobey evaluated the title evaluation: title number: 1 with the evaluation: NEEDS IMPROVEMENT', '2024-12-14', '20:12:44', '20241212gIOqsBUhEKhiPvXl'),
(175, 20241006, 15, 1, 'Panelist: Maguire, Tobey evaluated the title evaluation: title number: 1 with the evaluation: NEEDS IMPROVEMENT', '2024-12-14', '20:12:44', '20241212gIOqsBUhEKhiPvXl'),
(176, 20241006, 15, 1, 'Panelist: Maguire, Tobey evaluated the title evaluation: title number: 1 with the evaluation: REJECTED', '2024-12-14', '20:15:37', '20241212gIOqsBUhEKhiPvXl'),
(177, 20241054, 15, 1, 'Student: Kirai, Hazurii has edited the title evaluation: title number: 2', '2024-12-14', '21:00:34', '20241212gIOqsBUhEKhiPvXl'),
(178, 20241002, 15, 1, 'Panelist: Ruffalo, Mark evaluated the title evaluation: title number: 1 with the evaluation: NEEDS IMPROVEMENT', '2024-12-14', '21:25:06', '20241212gIOqsBUhEKhiPvXl'),
(179, 20241002, 15, 1, 'Panelist: Ruffalo, Mark evaluated the title evaluation: title number: 1 with the evaluation: NEEDS IMPROVEMENT', '2024-12-14', '21:25:06', '20241212gIOqsBUhEKhiPvXl'),
(180, 20241002, 22, 1, 'Panelist: Ruffalo, Mark evaluated the title evaluation: title number: 1 with the evaluation: ACCEPTED', '2024-12-14', '21:31:03', '20241214cVhRTxdVKvnHZHzL'),
(181, 20241002, 22, 1, 'Panelist: Ruffalo, Mark evaluated the title evaluation: title number: 1 with the evaluation: ACCEPTED', '2024-12-14', '21:31:03', '20241214cVhRTxdVKvnHZHzL'),
(182, 20241002, 23, 1, 'Panelist: Ruffalo, Mark evaluated the title evaluation: title number: 1 with the evaluation: REJECTED', '2024-12-14', '21:35:33', '20241214lluquramIBaVpNnl'),
(183, 20241002, 23, 1, 'Panelist: Ruffalo, Mark evaluated the title evaluation: title number: 1 with the evaluation: REJECTED', '2024-12-14', '21:35:33', '20241214lluquramIBaVpNnl'),
(184, 20241006, 22, 1, 'Panelist: Maguire, Tobey evaluated the title evaluation: title number: 1 with the evaluation: ACCEPTED', '2024-12-14', '22:35:43', '20241214cVhRTxdVKvnHZHzL'),
(185, 20241006, 22, 1, 'Panelist: Maguire, Tobey evaluated the title evaluation: title number: 1 with the evaluation: ACCEPTED', '2024-12-14', '22:35:43', '20241214cVhRTxdVKvnHZHzL'),
(186, 20241006, 22, 1, 'Panelist: Maguire, Tobey evaluated the title evaluation: title number: 2 with the evaluation: REJECTED', '2024-12-14', '22:44:56', '20241214cVhRTxdVKvnHZHzL'),
(187, 20241006, 22, 1, 'Panelist: Maguire, Tobey evaluated the title evaluation: title number: 2 with the evaluation: REJECTED', '2024-12-14', '22:44:56', '20241214cVhRTxdVKvnHZHzL'),
(188, 20241006, 22, 1, 'Panelist: Maguire, Tobey evaluated the title evaluation: title number: 3 with the evaluation: NEEDS IMPROVEMENT', '2024-12-14', '22:54:05', '20241214cVhRTxdVKvnHZHzL'),
(189, 20241006, 22, 1, 'Panelist: Maguire, Tobey evaluated the title evaluation: title number: 3 with the evaluation: NEEDS IMPROVEMENT', '2024-12-14', '22:54:05', '20241214cVhRTxdVKvnHZHzL'),
(190, 20241054, 15, 2, 'Student: Kirai, Hazurii updated the defense invitation', '2024-12-14', '23:01:33', '20241212SPkcphEcaUAYUPhz'),
(191, 20241005, 15, 2, 'Adviser: Holland, Tom accepted the defense invitation', '2024-12-14', '23:04:44', '20241212SPkcphEcaUAYUPhz'),
(192, 20241054, 15, 2, 'Student: Kirai, Hazurii updated the defense invitation', '2024-12-14', '23:09:54', '20241212SPkcphEcaUAYUPhz'),
(193, 20241002, 15, 2, 'Panelist: Ruffalo, Mark accepted the defense invitation', '2024-12-14', '23:12:37', '20241212SPkcphEcaUAYUPhz'),
(194, 20241054, 15, 3, 'Student: Kirai, Hazurii updated the capstone paper', '2024-12-14', '23:14:31', '20241212ciQncQYIpiRoXeWd'),
(195, 20241054, 15, 2, 'Student: Kirai, Hazurii updated the defense invitation', '2024-12-14', '23:17:11', '20241212SPkcphEcaUAYUPhz'),
(196, 20241002, 24, 1, 'Panelist: Ruffalo, Mark evaluated the title evaluation: title number: 2 with the evaluation: REJECTED', '2024-12-19', '17:56:19', '20241219RhjXXYcSnCiEGTAP'),
(197, 20241002, 24, 1, 'Panelist: Ruffalo, Mark evaluated the title evaluation: title number: 2 with the evaluation: REJECTED', '2024-12-19', '17:56:19', '20241219RhjXXYcSnCiEGTAP');

-- --------------------------------------------------------

--
-- Table structure for table `advisers`
--

CREATE TABLE `advisers` (
  `id` int(11) NOT NULL,
  `adviserID` int(20) NOT NULL,
  `projectID` int(20) DEFAULT NULL,
  `academicYearID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `advisers`
--

INSERT INTO `advisers` (`id`, `adviserID`, `projectID`, `academicYearID`) VALUES
(1, 20241005, NULL, 1),
(2, 20241001, NULL, 1),
(3, 20241006, 7, 1),
(5, 20241001, NULL, 1),
(6, 20241001, NULL, 1),
(7, 20241001, NULL, 1),
(8, 20241005, NULL, 1),
(9, 20241007, NULL, 1),
(10, 20241005, NULL, 1),
(11, 20241005, NULL, 1),
(12, 20241008, NULL, 1),
(13, 20241005, NULL, 1),
(14, 20241005, NULL, 1),
(15, 20241005, NULL, 1),
(16, 20241005, NULL, 1),
(17, 20241004, NULL, 1),
(18, 20241002, NULL, 1),
(19, 20241005, 15, 1),
(20, 20241008, 1, 1),
(21, 20241003, 22, 1),
(22, 20241002, NULL, 1),
(23, 20241005, 23, 1),
(24, 20241005, NULL, 1),
(25, 20241005, NULL, 1),
(26, 20241005, NULL, 1),
(27, 20241005, NULL, 1),
(28, 20241005, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `announcement`
--

CREATE TABLE `announcement` (
  `id` int(11) NOT NULL,
  `content` longtext NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `announcement`
--

INSERT INTO `announcement` (`id`, `content`, `date`) VALUES
(1, 'Welcome to your capstone subject this semester, students and faculty!', '2025-01-18');

-- --------------------------------------------------------

--
-- Table structure for table `capstone_papers`
--

CREATE TABLE `capstone_papers` (
  `id` int(20) NOT NULL,
  `projectID` int(20) NOT NULL,
  `status` varchar(255) NOT NULL,
  `submit_date` datetime DEFAULT NULL,
  `academicYearID` int(11) NOT NULL,
  `filepath` varchar(255) NOT NULL,
  `comment` longtext NOT NULL,
  `trackingNum` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `capstone_papers`
--

INSERT INTO `capstone_papers` (`id`, `projectID`, `status`, `submit_date`, `academicYearID`, `filepath`, `comment`, `trackingNum`) VALUES
(4, 1, 'evaluating', '2024-12-10 10:30:35', 1, 'capstone_papers/1-1/CAPSTRACK PAPER CHAPTERS 1 TO 3 - PRINTABLE.pdf', 'We have re-submitted our capstone paper', '20241104oxpfuqyymONCuamh'),
(5, 4, 'evaluating', '2024-12-13 21:44:08', 1, 'capstone_papers/4-1/CAPSTRACK PAPER CHAPTERS 1 TO 3 - PRINTABLE.pdf', 'hello this is our paper', '20241104szZVWIiiraIuZzna'),
(6, 15, 'evaluating', '2024-12-14 23:14:31', 1, 'capstone_papers/15-1/CAPSTRACK PAPER CHAPTERS 1 TO 3 - PRINTABLE.pdf', '', '20241212ciQncQYIpiRoXeWd');

-- --------------------------------------------------------

--
-- Table structure for table `capstone_projects`
--

CREATE TABLE `capstone_projects` (
  `projectID` int(20) NOT NULL,
  `sectionID` int(11) NOT NULL,
  `groupNum` int(11) NOT NULL,
  `academicYearID` int(11) NOT NULL,
  `coordinatorID` int(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `title_description` longtext NOT NULL,
  `status` varchar(255) NOT NULL,
  `defense` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `capstone_projects`
--

INSERT INTO `capstone_projects` (`projectID`, `sectionID`, `groupNum`, `academicYearID`, `coordinatorID`, `title`, `title_description`, `status`, `defense`) VALUES
(0, 0, 0, 1, 0, '', '', '', ''),
(1, 1, 1, 1, 20241002, 'Waste Watch: Truck Scheduling Android Mobile Application For Malolos', 'A truck scheduling mobile application for garbage trucks in City of Malolos, Bulacan', 'active', 'approved with revisions'),
(4, 1, 2, 1, 20241002, 'TBD', 'TBD', 'active', 'pending'),
(5, 1, 3, 1, 20241002, 'TBD', 'TBD', 'active', 'pending'),
(11, 1, 4, 1, 20241002, 'TBD', 'TBD', 'active', 'pending'),
(12, 1, 5, 1, 20241002, 'TBD', 'TBD', 'active', 'pending'),
(13, 1, 6, 1, 20241002, 'TBD', 'TBD', 'active', 'pending'),
(14, 1, 7, 1, 20241002, 'TBD', 'TBD', 'active', 'pending'),
(15, 15, 1, 1, 20241003, 'TBD', 'TBD', 'active', 'pending'),
(16, 15, 2, 1, 20241003, 'TBD', 'TBD', 'active', 'pending'),
(18, 1, 8, 1, 20241002, 'TBD', 'TBD', 'active', 'pending'),
(19, 21, 1, 1, 20241006, 'TBD', 'TBD', 'active', 'pending'),
(20, 12, 1, 1, 20241002, 'TBD', 'TBD', 'active', 'pending'),
(21, 24, 1, 1, 20241005, 'TBD', 'TBD', 'active', 'pending'),
(22, 31, 1, 1, 20241002, 'TBD', 'TBD', 'active', 'pending'),
(23, 32, 1, 1, 20241001, 'TBD', 'TBD', 'active', 'pending'),
(24, 26, 1, 1, 20241003, 'TBD', 'TBD', 'active', 'pending'),
(25, 26, 2, 1, 20241003, 'TBD', 'TBD', 'active', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `coordinators`
--

CREATE TABLE `coordinators` (
  `id` int(11) NOT NULL,
  `facultyID` int(20) NOT NULL,
  `sectionID` int(11) DEFAULT NULL,
  `academicYearID` int(11) NOT NULL,
  `semester` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `coordinators`
--

INSERT INTO `coordinators` (`id`, `facultyID`, `sectionID`, `academicYearID`, `semester`) VALUES
(1, 0, 0, 1, 0),
(2, 20241002, 1, 1, 2),
(4, 20241003, 7, 1, 2),
(6, 20241003, 9, 1, 2),
(7, 20241002, NULL, 1, 1),
(8, 20241003, NULL, 1, 1),
(9, 20241002, 12, 1, 2),
(10, 20241003, 13, 1, 2),
(12, 20241003, 15, 1, 2),
(17, 20241005, 20, 1, 1),
(18, 20241006, 21, 1, 2),
(21, 20241005, 24, 1, 2),
(22, 20241007, 25, 1, 2),
(23, 20241003, 26, 1, 2),
(27, 20241008, 30, 1, 2),
(28, 20241002, 31, 1, 2),
(29, 20241001, 32, 1, 2),
(30, 20241006, 33, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `courseID` varchar(255) NOT NULL,
  `courseName` varchar(255) NOT NULL,
  `adminID` int(20) NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`courseID`, `courseName`, `adminID`, `created_at`) VALUES
('BSAI', 'Bachelor of Science in Artificial Technology', 20241003, '2024-12-14'),
('BSIC', 'Bachelor of Science in Cybersecurity', 20241006, '2024-12-19'),
('BSIS', 'Bachelor of Science in Information Systems', 20241004, '2024-11-07'),
('BSIT', 'Bachelor of Science in Information Technology', 20241008, '2024-11-04'),
('N/A', 'N/A', 0, '2024-11-04');

-- --------------------------------------------------------

--
-- Table structure for table `creation_tokens`
--

CREATE TABLE `creation_tokens` (
  `id` varchar(20) NOT NULL,
  `token` varchar(50) NOT NULL,
  `created_at` date NOT NULL,
  `activated` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `creation_tokens`
--

INSERT INTO `creation_tokens` (`id`, `token`, `created_at`, `activated`) VALUES
('20241053', '20241214fM6Mn8AzXJlw8Yjp', '2024-12-14', 'false'),
('20241054', '20241214LnfJdoJDBF1a1Ev5', '2024-12-14', 'true'),
('20241055', '20241214yTmEsBCCNCHXmqp2', '2024-12-14', 'false'),
('20241056', '202412140OgV3aCz7mNcTCkf', '2024-12-14', 'false'),
('20241057', '20241214qfIBhkIXFtEIP2ox', '2024-12-14', 'false'),
('20241058', '20241214IjqPyghVVIEmchbg', '2024-12-14', 'false'),
('20241059', '20241214Ei1dpDnuM0xeFU6k', '2024-12-14', 'false'),
('20241060', '20241214oihNGN3foDUZn4tf', '2024-12-14', 'false'),
('20241061', '20241214ybjGakS80h0oz7zI', '2024-12-14', 'false'),
('20241062', '20241214R3XwoFQtoaZlXW1Q', '2024-12-14', 'false'),
('20241063', '20241214M0l6qvdz7Srk2UbS', '2024-12-14', 'false'),
('20241064', '20241214pdMRyBnhbd2QcdwV', '2024-12-14', 'false'),
('20241065', '20241214I0QeXNZFZsnkaymk', '2024-12-14', 'false'),
('20241066', '20241214cEsbpRGwJ3k0kCto', '2024-12-14', 'false'),
('20241067', '20241214tNpsA5JOKC1onZNN', '2024-12-14', 'false'),
('20241068', '20241214yvtRaUSUKs2qVd7j', '2024-12-14', 'true'),
('20241069', '20241214WcyKh778orbq2Qhl', '2024-12-14', 'false'),
('20241070', '202412147EPlImeqEVJKunoZ', '2024-12-14', 'false');

-- --------------------------------------------------------

--
-- Table structure for table `defense_answers`
--

CREATE TABLE `defense_answers` (
  `id` int(11) NOT NULL,
  `projectID` int(20) NOT NULL,
  `panelistID` int(20) NOT NULL,
  `level` int(11) NOT NULL,
  `answer` varchar(255) NOT NULL,
  `comment` longtext NOT NULL,
  `category` varchar(255) NOT NULL,
  `academicYearID` int(11) NOT NULL,
  `date_answer` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `defense_answers`
--

INSERT INTO `defense_answers` (`id`, `projectID`, `panelistID`, `level`, `answer`, `comment`, `category`, `academicYearID`, `date_answer`) VALUES
(12, 1, 20241003, 2, 'approved with revisions', 'approved', 'defense', 1, '2024-11-23 19:36:11');

-- --------------------------------------------------------

--
-- Table structure for table `defense_dates`
--

CREATE TABLE `defense_dates` (
  `id` int(11) NOT NULL,
  `projectID` int(11) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `defense_dates`
--

INSERT INTO `defense_dates` (`id`, `projectID`, `date`) VALUES
(40, 1, '2024-12-20 13:25:00'),
(42, 5, '2024-12-20 16:00:00'),
(43, 4, '2024-12-30 10:00:00'),
(45, 11, '2024-12-18 12:00:00'),
(46, 12, '2024-12-20 08:30:00'),
(51, 13, '2024-12-18 12:00:00'),
(65, 14, '2024-12-21 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `document_templates`
--

CREATE TABLE `document_templates` (
  `id` int(11) NOT NULL,
  `taskID` int(11) NOT NULL,
  `filepath` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `document_templates`
--

INSERT INTO `document_templates` (`id`, `taskID`, `filepath`) VALUES
(9, 2, 'document_templates/2/Capstrack_ InvitationLetterDefense_Mr. Delos Reyes_Adviser.pdf'),
(10, 3, 'document_templates/3/CAPSTRACK PAPER CHAPTERS 1 TO 3 - PRINTABLE.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

CREATE TABLE `faculty` (
  `id` int(20) NOT NULL,
  `accessLevel` int(11) NOT NULL,
  `category` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`id`, `accessLevel`, `category`) VALUES
(0, 1, 'Fulltime'),
(20241001, 3, 'Fulltime'),
(20241002, 1, 'Fulltime'),
(20241003, 2, 'Fulltime'),
(20241004, 2, 'Fulltime'),
(20241005, 1, 'Fulltime'),
(20241006, 2, 'Fulltime'),
(20241007, 1, 'Fulltime'),
(20241008, 2, 'Fulltime');

-- --------------------------------------------------------

--
-- Table structure for table `faculty_count`
--

CREATE TABLE `faculty_count` (
  `id` int(11) NOT NULL,
  `facultyID` int(20) NOT NULL,
  `panelist_count` int(11) NOT NULL,
  `panelist_limit` int(11) NOT NULL DEFAULT 5,
  `adviser_count` int(11) NOT NULL,
  `adviser_limit` int(11) NOT NULL DEFAULT 5,
  `coordinator_count` int(11) NOT NULL,
  `coordinator_limit` int(11) NOT NULL DEFAULT 5,
  `academicYearID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faculty_count`
--

INSERT INTO `faculty_count` (`id`, `facultyID`, `panelist_count`, `panelist_limit`, `adviser_count`, `adviser_limit`, `coordinator_count`, `coordinator_limit`, `academicYearID`) VALUES
(1, 20241001, 1, 7, 0, 6, 1, 8, 1),
(2, 20241002, 4, 5, 0, 5, 3, 4, 1),
(3, 20241003, 2, 4, 1, 5, 5, 5, 1),
(4, 20241004, 2, 5, 0, 5, 0, 5, 1),
(5, 20241005, 2, 5, 2, 5, 2, 5, 1),
(6, 20241006, 2, 5, 1, 5, 1, 5, 1),
(7, 20241007, 1, 5, 0, 5, 1, 5, 1),
(8, 20241008, 0, 5, 1, 5, 1, 5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `forgotpass_tokens`
--

CREATE TABLE `forgotpass_tokens` (
  `id` int(11) NOT NULL,
  `token` varchar(50) NOT NULL,
  `confirm_code` varchar(255) NOT NULL,
  `created_at` date NOT NULL,
  `userID` int(20) NOT NULL,
  `activated` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `forgotpass_tokens`
--

INSERT INTO `forgotpass_tokens` (`id`, `token`, `confirm_code`, `created_at`, `userID`, `activated`) VALUES
(46, '20241211zDeE683P9TDgG6XM', 'UyivJuVc', '2024-12-11', 20241009, 'false'),
(47, '20241211Wtl9YGnyaL7N0kb2', '9HGKnMGM', '2024-12-11', 20241001, 'false'),
(48, '20241211by9EUyv4nMvvvatX', 'SS47Z6lk', '2024-12-11', 20241001, 'false'),
(49, '20241211uCetKMEKM6vHI6P2', 'Yg93TT0M', '2024-12-11', 20241001, 'false'),
(50, '20241211vL4jyopwiyU9TLK6', 'QA6qIEsN', '2024-12-11', 20241001, 'false'),
(51, '20241212kDNLtBGK7GuE47L0', 'DkPQrk9p', '2024-12-12', 20241002, 'false'),
(52, '20241219GMat4ktY56Q8EMpg', 'LbI71Gaa', '2024-12-19', 20241001, 'false');

-- --------------------------------------------------------

--
-- Table structure for table `invitations`
--

CREATE TABLE `invitations` (
  `id` int(20) NOT NULL,
  `projectID` int(20) NOT NULL,
  `facultyID` int(20) NOT NULL,
  `status` varchar(255) NOT NULL,
  `submit_date` datetime DEFAULT NULL,
  `academicYearID` int(11) NOT NULL,
  `filepath` varchar(255) NOT NULL,
  `comment` longtext NOT NULL,
  `trackingNum` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invitations`
--

INSERT INTO `invitations` (`id`, `projectID`, `facultyID`, `status`, `submit_date`, `academicYearID`, `filepath`, `comment`, `trackingNum`) VALUES
(6, 1, 20241003, 'accepted', '2024-11-07 17:03:24', 1, 'invitations/20241003-1/Group-6-LETTER-FOR-CAP-ADVISER.pdf', 'Hello panelist this is our invitation letter', '20241104ojgysxzGRgnpXgHE'),
(7, 1, 20241004, 'accepted', '2024-11-23 22:26:18', 1, 'invitations/20241004-1/Invitation Letter for Mr. Burgos.pdf', '', '20241104ojgysxzGRgnpXgHE'),
(8, 1, 20241001, 'submitted', '2024-12-14 19:09:35', 1, 'invitations/20241001-1/ISO-IEC-25010-2023.pdf', '', '20241104ojgysxzGRgnpXgHE'),
(9, 4, 20241006, 'submitted', '2024-12-13 21:23:37', 1, 'invitations/20241006-1/ENDORSEMENT-LETTER-Sir-Melvin.pdf', 'hello sir', '20241104YXIwGJqJttuoitrX'),
(10, 15, 20241006, 'accepted', '2024-12-14 16:24:39', 1, 'invitations/20241006-1/Invitation Letter.pdf', 'Hello sir this is our invitation letter', '20241212SPkcphEcaUAYUPhz'),
(11, 15, 20241005, 'accepted', '2024-12-14 23:01:33', 1, 'invitations/20241005-1/Invitation Letter.pdf', 'Hey', '20241212SPkcphEcaUAYUPhz'),
(12, 15, 20241002, 'accepted', '2024-12-14 23:09:54', 1, 'invitations/20241002-1/Invitation Letter.pdf', '', '20241212SPkcphEcaUAYUPhz'),
(13, 15, 20241004, 'evaluating', '2024-12-14 23:17:11', 1, 'invitations/20241004-1/Invitation Letter.pdf', '', '20241212SPkcphEcaUAYUPhz');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `description` mediumtext NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `panelists`
--

CREATE TABLE `panelists` (
  `id` int(11) NOT NULL,
  `panelistID` int(20) NOT NULL,
  `projectID` int(20) DEFAULT NULL,
  `level` int(11) NOT NULL,
  `academicYearID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `panelists`
--

INSERT INTO `panelists` (`id`, `panelistID`, `projectID`, `level`, `academicYearID`) VALUES
(2, 20241003, NULL, 2, 1),
(3, 20241003, NULL, 2, 1),
(4, 20241006, NULL, 2, 1),
(7, 20241006, NULL, 2, 1),
(9, 20241007, 7, 2, 1),
(12, 20241003, NULL, 2, 1),
(17, 20241003, NULL, 2, 1),
(19, 20241003, NULL, 2, 1),
(23, 20241006, NULL, 2, 1),
(24, 20241006, NULL, 2, 1),
(26, 20241006, 15, 2, 1),
(29, 20241001, 15, 1, 1),
(30, 20241004, 15, 1, 1),
(31, 20241002, 15, 1, 1),
(32, 20241006, 1, 1, 1),
(34, 20241007, NULL, 2, 1),
(35, 20241007, NULL, 2, 1),
(36, 20241005, 1, 1, 1),
(37, 20241005, 22, 1, 1),
(38, 20241002, 22, 1, 1),
(39, 20241004, 22, 1, 1),
(40, 20241006, 22, 1, 1),
(41, 20241003, 23, 2, 1),
(42, 20241006, 23, 2, 1),
(43, 20241007, NULL, 2, 1),
(44, 20241002, 23, 1, 1),
(45, 20241003, NULL, 2, 1),
(46, 20241003, NULL, 2, 1),
(48, 20241001, NULL, 2, 1),
(49, 20241003, NULL, 2, 1),
(50, 20241002, 24, 2, 1),
(51, 20241003, 1, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `previous_documents`
--

CREATE TABLE `previous_documents` (
  `id` int(11) NOT NULL,
  `projectID` int(11) NOT NULL,
  `filepath` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `previous_documents`
--

INSERT INTO `previous_documents` (`id`, `projectID`, `filepath`) VALUES
(1, 1, 'capstone_papers/1-1/3D-G1_WasteWatch_IMRAD.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `remember_tokens`
--

CREATE TABLE `remember_tokens` (
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `id` int(20) NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `remember_tokens`
--

INSERT INTO `remember_tokens` (`token`, `id`, `created_at`) VALUES
('4f343aec4186a1336589c64e1821b7d6', 20241001, '2024-12-19'),
('eba6a0f04d82cf886c78940bd4c1545d', 20241001, '2024-12-15');

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `sectionID` int(11) NOT NULL,
  `coordinatorID` int(20) NOT NULL,
  `courseID` varchar(255) NOT NULL,
  `yearLevel` int(11) NOT NULL,
  `section_letter` varchar(255) NOT NULL,
  `section_group` varchar(255) NOT NULL,
  `specialization` varchar(255) NOT NULL,
  `academicYearID` int(11) NOT NULL,
  `semester` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`sectionID`, `coordinatorID`, `courseID`, `yearLevel`, `section_letter`, `section_group`, `specialization`, `academicYearID`, `semester`) VALUES
(0, 0, 'N/A', 0, 'N/A', '', 'No Specialization', 1, 0),
(1, 20241002, 'BSIT', 3, 'D', 'G1', 'Web and Mobile Development', 1, 2),
(12, 20241002, 'BSIS', 3, 'A', 'G1', 'No Specialization', 1, 1),
(15, 20241003, 'BSIT', 3, 'A', 'G1', 'Web and Mobile Development', 1, 2),
(21, 20241005, 'BSIS', 3, 'B', 'G1', 'No Specialization', 1, 2),
(24, 20241005, 'BSIT', 3, 'K', 'G1', 'Web and Mobile Development', 1, 2),
(25, 20241007, 'BSIT', 3, 'F', 'G1', 'No Specialization', 1, 2),
(26, 20241003, 'BSAI', 3, 'A', '', 'Machine Learning', 1, 2),
(31, 20241002, 'BSIT', 3, 'I', 'G2', 'Business Analytics', 1, 2),
(32, 20241001, 'BSIS', 3, 'A', '', 'Networking', 1, 2),
(33, 20241006, 'BSIT', 3, 'E', 'G1', 'Service Management', 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `sequence_tracker`
--

CREATE TABLE `sequence_tracker` (
  `current_year` varchar(20) NOT NULL,
  `last_sequence` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sequence_tracker`
--

INSERT INTO `sequence_tracker` (`current_year`, `last_sequence`) VALUES
('2026', 1000);

-- --------------------------------------------------------

--
-- Table structure for table `specializations`
--

CREATE TABLE `specializations` (
  `id` int(11) NOT NULL,
  `courseID` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `specializations`
--

INSERT INTO `specializations` (`id`, `courseID`, `name`, `created_at`) VALUES
(0, 'N/A', 'No Specialization', '2024-11-04'),
(1, 'BSIT', 'No Specialization', '2024-11-04'),
(2, 'BSIT', 'Web and Mobile Development', '2024-11-04'),
(3, 'BSIT', 'Service Management', '2024-11-04'),
(4, 'BSIT', 'Business Analytics', '2024-11-04'),
(8, 'BSIS', 'No Specialization', '2024-11-07'),
(9, 'BSIS', 'Networking', '2024-11-07'),
(11, 'BSIS', 'Artificial Intelligence', '2024-12-11'),
(12, 'BSIT', 'Artificial Intelligence', '2024-12-13'),
(13, 'BSAI', 'No Specialization', '2024-12-14'),
(14, 'BSAI', 'Machine Learning', '2024-12-14'),
(15, 'BSAI', 'Service Management', '2024-12-14'),
(16, 'BSIC', 'No Specialization', '2024-12-19'),
(17, 'BSIC', 'Artificial Intelligence', '2024-12-19');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(20) NOT NULL,
  `projectID` int(20) DEFAULT NULL,
  `new_projectID` int(20) DEFAULT NULL,
  `studentNo` varchar(25) NOT NULL,
  `sectionID` int(11) NOT NULL,
  `new_sectionID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `projectID`, `new_projectID`, `studentNo`, `sectionID`, `new_sectionID`) VALUES
(20241009, 1, NULL, '2021106782', 1, NULL),
(20241010, 0, NULL, '2021106288', 1, NULL),
(20241011, 0, NULL, '2021106167', 1, NULL),
(20241012, 1, NULL, '2021106162', 1, NULL),
(20241013, 0, NULL, '2021101564', 1, NULL),
(20241044, 1, NULL, '2021106167', 1, NULL),
(20241051, 1, NULL, '2021174402', 1, NULL),
(20241052, 0, NULL, '2021159001', 1, NULL),
(20241054, 15, NULL, '2021106712', 15, NULL),
(20241068, 15, NULL, '2021106782', 15, NULL),
(20241070, 0, NULL, '2021106782', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` int(11) NOT NULL,
  `tag` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `tag`) VALUES
(9, 'artificial_intelligence'),
(6, 'augmented_reality'),
(11, 'business'),
(16, 'cloud_computing'),
(21, 'cloud_infrastracture'),
(7, 'data_science'),
(10, 'e-commerce'),
(14, 'education'),
(3, 'game_development'),
(12, 'healthcare'),
(13, 'internet_of_things'),
(8, 'machine_learning'),
(2, 'mobile_development'),
(4, 'networking'),
(15, 'social_media'),
(5, 'virtual_reality'),
(1, 'web_development');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `taskName` varchar(255) NOT NULL,
  `yearLevel` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `taskName`, `yearLevel`, `type`, `status`) VALUES
(1, 'title evaluation', 'all', 'static', 'enabled'),
(2, 'defense invitation', 'all', 'static', 'enabled'),
(3, 'capstone paper', 'all', 'static', 'enabled'),
(4, 'defense', 'all', 'static', 'enabled');

-- --------------------------------------------------------

--
-- Table structure for table `title_proposal`
--

CREATE TABLE `title_proposal` (
  `id` int(11) NOT NULL,
  `projectID` int(11) NOT NULL,
  `titleNum` int(11) NOT NULL,
  `title` longtext NOT NULL,
  `title_description` longtext NOT NULL,
  `introduction` longtext NOT NULL,
  `background` longtext NOT NULL,
  `importance` longtext NOT NULL,
  `scope` longtext NOT NULL,
  `result` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `title_proposal`
--

INSERT INTO `title_proposal` (`id`, `projectID`, `titleNum`, `title`, `title_description`, `introduction`, `background`, `importance`, `scope`, `result`) VALUES
(1, 1, 1, 'Waste Watch: Truck Scheduling Android Mobile Application For Malolos', 'A truck scheduling mobile application for garbage trucks in City of Malolos, Bulacan', 'sample intro', 'sample background', 'sample importance', 'SCOPE', 'accepted'),
(2, 1, 2, '', '', '', '', '', '', 'rejected'),
(3, 1, 3, '', '', '', '', '', '', 'rejected'),
(4, 4, 1, 'Clinic Management System', 'test', 'test', 'test', 'test', 'test', 'pending'),
(5, 4, 2, 'Baranggay Management System', 'test', 'test', 'test', 'test', 'test', 'pending'),
(6, 4, 3, '', '', '', '', '', '', 'pending'),
(7, 5, 1, '', '', '', '', '', '', 'pending'),
(8, 5, 2, '', '', '', '', '', '', 'pending'),
(9, 5, 3, '', '', '', '', '', '', 'pending'),
(19, 11, 1, '', '', '', '', '', '', 'pending'),
(20, 11, 2, '', '', '', '', '', '', 'pending'),
(21, 11, 3, '', '', '', '', '', '', 'pending'),
(22, 12, 1, '', '', '', '', '', '', 'pending'),
(23, 12, 2, '', '', '', '', '', '', 'pending'),
(24, 12, 3, '', '', '', '', '', '', 'pending'),
(25, 13, 1, '', '', '', '', '', '', 'pending'),
(26, 13, 2, '', '', '', '', '', '', 'pending'),
(27, 13, 3, '', '', '', '', '', '', 'pending'),
(28, 14, 1, '', '', '', '', '', '', 'pending'),
(29, 14, 2, '', '', '', '', '', '', 'pending'),
(30, 14, 3, '', '', '', '', '', '', 'pending'),
(31, 15, 1, 'Traffic Forecasting System', 'test', 'test', 'test', 'test', 'test', 'pending'),
(32, 15, 2, 'Title', 'sa', 'asa', 'asa', 'ssas', 'asas', 'pending'),
(33, 15, 3, '', '', '', '', '', '', 'pending'),
(34, 16, 1, '', '', '', '', '', '', 'pending'),
(35, 16, 2, '', '', '', '', '', '', 'pending'),
(36, 16, 3, '', '', '', '', '', '', 'pending'),
(40, 18, 1, '', '', '', '', '', '', 'pending'),
(41, 18, 2, '', '', '', '', '', '', 'pending'),
(42, 18, 3, '', '', '', '', '', '', 'pending'),
(43, 19, 1, '', '', '', '', '', '', 'pending'),
(44, 19, 2, '', '', '', '', '', '', 'pending'),
(45, 19, 3, '', '', '', '', '', '', 'pending'),
(46, 20, 1, '', '', '', '', '', '', 'pending'),
(47, 20, 2, '', '', '', '', '', '', 'pending'),
(48, 20, 3, '', '', '', '', '', '', 'pending'),
(49, 21, 1, '', '', '', '', '', '', 'pending'),
(50, 21, 2, '', '', '', '', '', '', 'pending'),
(51, 21, 3, '', '', '', '', '', '', 'pending'),
(52, 22, 1, '', '', '', '', '', '', 'pending'),
(53, 22, 2, '', '', '', '', '', '', 'pending'),
(54, 22, 3, '', '', '', '', '', '', 'pending'),
(55, 23, 1, '', '', '', '', '', '', 'pending'),
(56, 23, 2, '', '', '', '', '', '', 'pending'),
(57, 23, 3, '', '', '', '', '', '', 'pending'),
(58, 24, 1, '', '', '', '', '', '', 'pending'),
(59, 24, 2, '', '', '', '', '', '', 'pending'),
(60, 24, 3, '', '', '', '', '', '', 'pending'),
(61, 25, 1, '', '', '', '', '', '', 'pending'),
(62, 25, 2, '', '', '', '', '', '', 'pending'),
(63, 25, 3, '', '', '', '', '', '', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `title_proposal_answers`
--

CREATE TABLE `title_proposal_answers` (
  `id` int(11) NOT NULL,
  `projectID` int(11) NOT NULL,
  `panelistID` int(20) NOT NULL,
  `titleNum` int(11) NOT NULL,
  `answer` varchar(255) NOT NULL,
  `comment` longtext NOT NULL,
  `level` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `title_proposal_answers`
--

INSERT INTO `title_proposal_answers` (`id`, `projectID`, `panelistID`, `titleNum`, `answer`, `comment`, `level`) VALUES
(27, 1, 20241004, 1, 'accepted', 'Nicee', 1),
(28, 1, 20241003, 1, 'accepted', 'accepted now', 2),
(30, 15, 20241001, 1, 'accepted', '', 1),
(31, 15, 20241006, 1, 'rejected', '', 2),
(32, 15, 20241002, 1, 'needs improvement', 'Bad work.', 1),
(33, 22, 20241002, 1, 'accepted', 'Nice Work.', 1),
(34, 23, 20241002, 1, 'rejected', 'Bad Work.', 1),
(35, 22, 20241006, 1, 'accepted', 'okay', 1),
(36, 22, 20241006, 2, 'rejected', 'Bad work', 1),
(37, 22, 20241006, 3, 'needs improvement', 'Bad work', 1),
(38, 24, 20241002, 2, 'rejected', 'Bad work', 2);

-- --------------------------------------------------------

--
-- Table structure for table `title_proposal_inputs`
--

CREATE TABLE `title_proposal_inputs` (
  `id` int(11) NOT NULL,
  `projectID` int(11) NOT NULL,
  `titleNum` int(11) NOT NULL,
  `title` longtext NOT NULL,
  `title_description` longtext NOT NULL,
  `introduction` longtext NOT NULL,
  `background` longtext NOT NULL,
  `importance` longtext NOT NULL,
  `scope` longtext NOT NULL,
  `input_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `title_proposal_inputs`
--

INSERT INTO `title_proposal_inputs` (`id`, `projectID`, `titleNum`, `title`, `title_description`, `introduction`, `background`, `importance`, `scope`, `input_date`) VALUES
(1, 1, 1, 'Waste Watch: Truck Scheduling Mobile Application For Malolos, Bulacan', 'A truck scheduling mobile application for garbage trucks in malolos city, bulacan', 'sample intro', 'sample background', 'sample importance', 'SCOPE', '2024-11-19 16:02:24'),
(2, 1, 1, 'Waste Watch: Truck Scheduling Mobile Application For City of Malolos, Bulacan', 'A truck scheduling mobile application for garbage trucks in City of Malolos, Bulacan', 'sample intro', 'sample background', 'sample importance', 'SCOPE', '2024-11-19 16:23:20'),
(5, 1, 1, 'Waste Watch: Truck Scheduling Android Mobile Application For City of Malolos, Bulacan', 'A truck scheduling mobile application for garbage trucks in City of Malolos, Bulacan', 'sample intro', 'sample background', 'sample importance', 'SCOPE', '2024-11-23 18:40:55'),
(6, 1, 1, 'Waste Watch: Truck Scheduling Android Mobile Application For City of Malolos, Bulacan', 'A truck scheduling mobile application for garbage trucks in City of Malolos, Bulacan', 'sample intro', 'sample background', 'sample importance', 'SCOPE', '2024-11-23 18:42:31'),
(7, 1, 1, 'Waste Watch: Truck Scheduling Android Mobile Application For Malolenos', 'A truck scheduling mobile application for garbage trucks in City of Malolos, Bulacan', 'sample intro', 'sample background', 'sample importance', 'SCOPE', '2024-11-23 20:33:45'),
(8, 1, 1, 'Waste Watch: Truck Scheduling Android Mobile Application For Malolos', 'A truck scheduling mobile application for garbage trucks in City of Malolos, Bulacan', 'sample intro', 'sample background', 'sample importance', 'SCOPE', '2024-11-23 21:03:58'),
(9, 4, 1, 'Clinic Management System', 'test', 'test', 'test', 'test', 'test', '2024-12-13 21:05:54'),
(10, 4, 1, 'Clinical Management System', 'test', 'test', 'test', 'test', 'test', '2024-12-13 21:09:48'),
(11, 4, 1, 'Clinic Management System', 'test', 'test', 'test', 'test', 'test', '2024-12-13 21:10:30'),
(12, 4, 2, 'Baranggay Management System', 'test', 'test', 'test', 'test', 'test', '2024-12-13 21:11:27'),
(13, 15, 1, 'Traffic Forecasting System', 'test', 'test', 'test', 'test', 'test', '2024-12-14 13:53:03'),
(14, 15, 2, 'Title', 'sa', 'asa', 'asa', 'ssas', 'asas', '2024-12-14 21:00:34');

-- --------------------------------------------------------

--
-- Table structure for table `title_tags`
--

CREATE TABLE `title_tags` (
  `id` int(11) NOT NULL,
  `projectID` int(11) NOT NULL,
  `tag` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `title_tags`
--

INSERT INTO `title_tags` (`id`, `projectID`, `tag`) VALUES
(19, 1, 'web_development'),
(20, 1, 'education'),
(23, 1, 'cloud_computing');

-- --------------------------------------------------------

--
-- Table structure for table `tracking`
--

CREATE TABLE `tracking` (
  `id` int(11) NOT NULL,
  `projectID` int(11) NOT NULL,
  `taskID` int(11) NOT NULL,
  `academicYearID` int(11) NOT NULL,
  `status` varchar(255) NOT NULL,
  `number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tracking`
--

INSERT INTO `tracking` (`id`, `projectID`, `taskID`, `academicYearID`, `status`, `number`) VALUES
(1, 1, 1, 1, 'completed', '20241104IonIQYvVGaFyTAWc'),
(2, 1, 2, 1, 'evaluating', '20241104ojgysxzGRgnpXgHE'),
(3, 1, 3, 1, 'completed', '20241104oxpfuqyymONCuamh'),
(4, 1, 4, 1, 'started', '20241104JyPExyehDBluudxZ'),
(5, 4, 1, 1, 'evaluating', '20241104zWzhJAjkDcQwonXK'),
(6, 4, 2, 1, 'submitted', '20241104YXIwGJqJttuoitrX'),
(7, 4, 3, 1, 'completed', '20241104szZVWIiiraIuZzna'),
(8, 4, 4, 1, 'started', '20241104PCVoVShFtJVgqqGo'),
(9, 5, 1, 1, 'started', '20241105rwrauqXgpUgPfXZE'),
(10, 5, 2, 1, 'started', '20241105eQyjsojKHTWrqFTB'),
(11, 5, 3, 1, 'started', '20241105IQcAKcsIvDGtfjkS'),
(12, 5, 4, 1, 'started', '20241105lWZYxVRLOMOOvaUb'),
(33, 11, 1, 1, 'started', '20241212YjvahiIkvOWmUHCV'),
(34, 11, 2, 1, 'started', '20241212UiIWXToDIVAbTggZ'),
(35, 11, 3, 1, 'started', '20241212PNxSflZURVgAdHZK'),
(36, 11, 4, 1, 'started', '20241212uuXpTuzvRcXKFHPo'),
(37, 12, 1, 1, 'started', '20241212kUqAgXRODeHxMDsN'),
(38, 12, 2, 1, 'started', '20241212LAyKLupyBrjUIeuE'),
(39, 12, 3, 1, 'started', '20241212uXOQrKdMGIqDjaMV'),
(40, 12, 4, 1, 'started', '20241212NLLyxdEaBcIakWDo'),
(41, 13, 1, 1, 'started', '20241212cvzMlmlBVaJgEjWz'),
(42, 13, 2, 1, 'started', '20241212KqKGpNVhuElSYaFe'),
(43, 13, 3, 1, 'started', '20241212XDFGUKvtVngnhukP'),
(44, 13, 4, 1, 'started', '20241212UpOYyJvTwBLoFbbm'),
(45, 14, 1, 1, 'started', '20241212rcAYjMUDXCyVBXvP'),
(46, 14, 2, 1, 'started', '20241212hobVqrcfPebjizoz'),
(47, 14, 3, 1, 'started', '20241212FQgJyKpbZNnizMKk'),
(48, 14, 4, 1, 'started', '20241212beFpMPqAekglURNv'),
(49, 15, 1, 1, 'evaluating', '20241212gIOqsBUhEKhiPvXl'),
(50, 15, 2, 1, 'evaluating', '20241212SPkcphEcaUAYUPhz'),
(51, 15, 3, 1, 'submitted', '20241212ciQncQYIpiRoXeWd'),
(52, 15, 4, 1, 'started', '20241212caeYAenPeAAfgNGy'),
(53, 16, 1, 1, 'started', '20241212kxBWZolBoxsDMeGZ'),
(54, 16, 2, 1, 'started', '20241212joMuHUuAaeBSiIPu'),
(55, 16, 3, 1, 'started', '20241212KSAsORaXXuqWzefX'),
(56, 16, 4, 1, 'started', '20241212ZOjdPlGNJfPFTbqE'),
(61, 18, 1, 1, 'started', '20241213NuOSgkpudZtDBFNO'),
(62, 18, 2, 1, 'started', '20241213IbjauSFMnnaUmOem'),
(63, 18, 3, 1, 'started', '20241213GVYKWFYBtfILALSG'),
(64, 18, 4, 1, 'started', '20241213MvwimETWMJvYCTRu'),
(65, 19, 1, 1, 'started', '20241214CNpvnVvgzJMWRKCL'),
(66, 19, 2, 1, 'started', '20241214XmyTYYcuDmLtMEKE'),
(67, 19, 3, 1, 'started', '20241214ZHigwnwSVsyTBoEt'),
(68, 19, 4, 1, 'started', '20241214gqfnHrWaeUFHPHRn'),
(69, 20, 1, 1, 'started', '20241214NpGaiFwpUAiCYwdP'),
(70, 20, 2, 1, 'started', '20241214TovZSyYcsNZpRatf'),
(71, 20, 3, 1, 'started', '20241214ycZZmBILtqZNKhCt'),
(72, 20, 4, 1, 'started', '20241214agOKIuAhVzTGTpVB'),
(73, 21, 1, 1, 'started', '20241214NxAcPADgveLnOHBi'),
(74, 21, 2, 1, 'started', '20241214hyDYCKbMgUayhSrR'),
(75, 21, 3, 1, 'started', '20241214ysRVhiPzBtUHePew'),
(76, 21, 4, 1, 'started', '20241214LtSGKcSxNRVYojfE'),
(77, 22, 1, 1, 'started', '20241214cVhRTxdVKvnHZHzL'),
(78, 22, 2, 1, 'started', '20241214zUUuVeactNuIDkjK'),
(79, 22, 3, 1, 'started', '20241214LTvXgYpirRhTyPtx'),
(80, 22, 4, 1, 'started', '20241214MJkcaTtMcfbcOoel'),
(81, 23, 1, 1, 'started', '20241214lluquramIBaVpNnl'),
(82, 23, 2, 1, 'started', '20241214DRVCGccjurOOuhJv'),
(83, 23, 3, 1, 'started', '20241214lsOOfRigohCsFJZQ'),
(84, 23, 4, 1, 'started', '20241214QSJelITRpRJyMEOu'),
(85, 24, 1, 1, 'started', '20241219RhjXXYcSnCiEGTAP'),
(86, 24, 2, 1, 'started', '20241219uTbXwmtSJQYsMPBt'),
(87, 24, 3, 1, 'started', '20241219PELtMOLZuRpWmkfF'),
(88, 24, 4, 1, 'started', '20241219RDkSJgEVQJbfKjnM'),
(89, 25, 1, 1, 'started', '20241219YxwnEDZxKcGhsuuC'),
(90, 25, 2, 1, 'started', '20241219mHmsjgExRrqKQAAy'),
(91, 25, 3, 1, 'started', '20241219NmHUgvzVoGuBLVQJ'),
(92, 25, 4, 1, 'started', '20241219zfnkNDwwUOuhlwiu');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `middlename` varchar(100) NOT NULL,
  `surname` varchar(100) NOT NULL,
  `status` varchar(20) NOT NULL,
  `session` varchar(30) NOT NULL,
  `type` varchar(20) NOT NULL,
  `created_at` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `firstname`, `middlename`, `surname`, `status`, `session`, `type`, `created_at`) VALUES
(0, 'null', 'null', 'null', 'null', 'null', 'inactive', 'offline', 'faculty', '2024-11-04'),
(20241001, 'christianangelo.agus@gmail.com', '$2y$10$UaFdUuqbyVaSjD4lZk7u2eET4gmrG.XLOVFBO.2NJ9Q/bJ/DRJ61K', 'Christian Angelo', 'Robles', 'Agus', 'active', 'offline', 'faculty', '2024-11-04'),
(20241002, 'mark_ruffalo@gmail.com', '$2y$10$UaFdUuqbyVaSjD4lZk7u2eET4gmrG.XLOVFBO.2NJ9Q/bJ/DRJ61K', 'Mark', 'Banner', 'Ruffalo', 'active', 'offline', 'faculty', '2024-11-04'),
(20241003, 'ryan_gosling@gmail.com', '$2y$10$UaFdUuqbyVaSjD4lZk7u2eET4gmrG.XLOVFBO.2NJ9Q/bJ/DRJ61K', 'Ryan', 'Cruz', 'Gosling', 'active', 'offline', 'faculty', '2024-11-04'),
(20241004, 'Nicolas_cage@gmail.com', '$2y$10$UaFdUuqbyVaSjD4lZk7u2eET4gmrG.XLOVFBO.2NJ9Q/bJ/DRJ61K', 'Nic', 'Santos', 'Cage', 'active', 'offline', 'faculty', '2024-11-04'),
(20241005, 'tom_holland@gmail.com', '$2y$10$UaFdUuqbyVaSjD4lZk7u2eET4gmrG.XLOVFBO.2NJ9Q/bJ/DRJ61K', 'Tom', 'Parker', 'Holland', 'active', 'offline', 'faculty', '2024-11-04'),
(20241006, 'tobey_maguire@gmail.com', '$2y$10$UaFdUuqbyVaSjD4lZk7u2eET4gmrG.XLOVFBO.2NJ9Q/bJ/DRJ61K', 'Tobey', 'Cruz', 'Maguire', 'active', 'offline', 'faculty', '2024-11-04'),
(20241007, 'andrew_garfield@gmail.com', '$2y$10$UaFdUuqbyVaSjD4lZk7u2eET4gmrG.XLOVFBO.2NJ9Q/bJ/DRJ61K', 'Andrew', 'Parker', 'Garfield', 'active', 'offline', 'faculty', '2024-11-04'),
(20241008, 'juan.delacruz@gmail.com', '$2y$10$UaFdUuqbyVaSjD4lZk7u2eET4gmrG.XLOVFBO.2NJ9Q/bJ/DRJ61K', 'Juan', 'Delos Santos', 'Cruz', 'active', 'offline', 'faculty', '2024-11-04'),
(20241009, 'christianagus03@gmail.com', '$2y$10$UaFdUuqbyVaSjD4lZk7u2eET4gmrG.XLOVFBO.2NJ9Q/bJ/DRJ61K', 'Gelo', 'Robles', 'Agus', 'active', 'offline', 'student', '2024-11-04'),
(20241010, 'elijah@gmail.com', '$2y$10$UaFdUuqbyVaSjD4lZk7u2eET4gmrG.XLOVFBO.2NJ9Q/bJ/DRJ61K', 'Elijah ', 'Musni', 'Yasa', 'active', 'offline', 'student', '2024-11-04'),
(20241011, 'wendric@gmail.com', '$2y$10$UaFdUuqbyVaSjD4lZk7u2eET4gmrG.XLOVFBO.2NJ9Q/bJ/DRJ61K', 'Wendric', 'Gutierez', 'Dela Cruz', 'active', 'offline', 'student', '2024-11-04'),
(20241012, 'gene@gmail.com', '$2y$10$UaFdUuqbyVaSjD4lZk7u2eET4gmrG.XLOVFBO.2NJ9Q/bJ/DRJ61K', 'Gene Hanzley', 'Dela Cruz', 'Sta. Ana', 'active', 'offline', 'student', '2024-11-04'),
(20241013, 'elliot@gmail.com', '$2y$10$UaFdUuqbyVaSjD4lZk7u2eET4gmrG.XLOVFBO.2NJ9Q/bJ/DRJ61K', 'Elliot', 'Caingat', 'Cruz', 'active', 'offline', 'student', '2024-11-04'),
(20241043, 'wendric.villarin.g@gmail.com', '$2y$10$P3nrKEWkGtADt7psjxuxW.VE4JTdQ.9YKjX5ioevIK0/0dGviaSwG', 'Wendric', 'Gutierrez', 'Villarin', 'pending', 'offline', 'student', '2024-11-06'),
(20241044, 'wenwendigreat@gmail.com', '$2y$10$iWrAVWodhKodqumzidMXiuCQ5/hlpSrVdKU36iI/LHb3M5F3AFN9u', 'Wendric', 'Gutierrez', 'Villarin', 'active', 'offline', 'student', '2024-11-06'),
(20241051, 'christianangeloagus1010@gmail.com', '$2y$10$7HfoJ7detwbyMnwvfpDTJu.XXIU.4u/Dcfs.eqrtZrLrFcE48CArC', 'Gelo', 'Robles', 'Agus', 'active', 'offline', 'student', '2024-11-07'),
(20241052, 'parzivalgaming3@gmail.com', '$2y$10$czKOk2hbC7B5TheVm2WcWeKFg9lmznC1PvLTM6oXi8vqnUE0hsaiu', 'Wade', 'Parzival', 'Watts', 'pending', 'offline', 'student', '2024-11-07'),
(20241054, 'hazurii9418@gmail.com', '$2y$10$RMmXI9g0x0gPCRYkYlo1sOLGPysSInEVGxbWHJYWMiCKS037Dw39G', 'Hazurii', 'Shin', 'Kirai', 'active', 'offline', 'student', '2024-12-14'),
(20241068, 'Agus_gelo@gmail.com', '$2y$10$UaFdUuqbyVaSjD4lZk7u2eET4gmrG.XLOVFBO.2NJ9Q/bJ/DRJ61K', 'Angelo', 'Robles', 'Agus', 'active', 'offline', 'student', '2024-12-14'),
(20241070, 'agus.christianangelo.bsit@gmail.com', '$2y$10$Q4l/och1l6VQQJ/n72U8i.pY6HqFfFdzOPGMn4./ELV3Es1Z9i4My', 'Christian Angelo', 'Robles', 'Agus', 'pending', 'offline', 'student', '2024-12-14');

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id` int(20) NOT NULL,
  `userID` int(20) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `last_active` datetime NOT NULL,
  `device_info` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_year`
--
ALTER TABLE `academic_year`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `action_logs`
--
ALTER TABLE `action_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id_constraint` (`userID`);

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `logs_user_constraint` (`userID`),
  ADD KEY `logs_project_constraint` (`projectID`),
  ADD KEY `logs_task_constraint` (`taskID`),
  ADD KEY `logs_trackNum_constraint` (`trackingNum`);

--
-- Indexes for table `advisers`
--
ALTER TABLE `advisers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `adviser_project_id_constraint` (`projectID`);

--
-- Indexes for table `announcement`
--
ALTER TABLE `announcement`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `capstone_papers`
--
ALTER TABLE `capstone_papers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `paper_project_constraint` (`projectID`),
  ADD KEY `paper_year_constraint` (`academicYearID`),
  ADD KEY `paper_tracknum_constraint` (`trackingNum`);

--
-- Indexes for table `capstone_projects`
--
ALTER TABLE `capstone_projects`
  ADD PRIMARY KEY (`projectID`),
  ADD UNIQUE KEY `sectionID` (`sectionID`,`groupNum`,`academicYearID`,`coordinatorID`),
  ADD KEY `cp_coordinator_constraint` (`coordinatorID`),
  ADD KEY `cp_acadyear_constraint` (`academicYearID`);

--
-- Indexes for table `coordinators`
--
ALTER TABLE `coordinators`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `facultyID` (`facultyID`,`sectionID`,`academicYearID`),
  ADD KEY `acad_year_constraint` (`academicYearID`),
  ADD KEY `coordinator_section_id_constraint` (`sectionID`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`courseID`),
  ADD KEY `course_admin_id_constraint` (`adminID`);

--
-- Indexes for table `creation_tokens`
--
ALTER TABLE `creation_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `defense_answers`
--
ALTER TABLE `defense_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `defense_project_constraint` (`projectID`),
  ADD KEY `defense_year_constraint` (`academicYearID`);

--
-- Indexes for table `defense_dates`
--
ALTER TABLE `defense_dates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `projectID` (`projectID`);

--
-- Indexes for table `document_templates`
--
ALTER TABLE `document_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `template_task_constraint` (`taskID`);

--
-- Indexes for table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `faculty_count`
--
ALTER TABLE `faculty_count`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `facultyID` (`facultyID`,`academicYearID`);

--
-- Indexes for table `forgotpass_tokens`
--
ALTER TABLE `forgotpass_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invitations`
--
ALTER TABLE `invitations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invite_project_constraint` (`projectID`),
  ADD KEY `invite_faculty_constraint` (`facultyID`),
  ADD KEY `invite_tracking_constraint` (`trackingNum`),
  ADD KEY `invite_acadyear_constraint` (`academicYearID`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `panelists`
--
ALTER TABLE `panelists`
  ADD PRIMARY KEY (`id`),
  ADD KEY `panelist_project_id_constraint` (`projectID`);

--
-- Indexes for table `previous_documents`
--
ALTER TABLE `previous_documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD PRIMARY KEY (`token`,`id`),
  ADD KEY `remember_id_constraint` (`id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`sectionID`),
  ADD UNIQUE KEY `unique_section` (`courseID`,`yearLevel`,`section_letter`,`section_group`,`academicYearID`,`semester`) USING BTREE,
  ADD KEY `acadyear_constraint` (`academicYearID`),
  ADD KEY `coordinatorID_constraint` (`coordinatorID`),
  ADD KEY `specialization_course` (`courseID`,`specialization`);

--
-- Indexes for table `sequence_tracker`
--
ALTER TABLE `sequence_tracker`
  ADD PRIMARY KEY (`current_year`);

--
-- Indexes for table `specializations`
--
ALTER TABLE `specializations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `specialization_course_constraint` (`courseID`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `section_id_constraint` (`sectionID`),
  ADD KEY `student_projectID_constraint` (`projectID`),
  ADD KEY `student_new_projectID_constraint` (`new_projectID`),
  ADD KEY `student_new_sectionID_constraint` (`new_sectionID`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tag` (`tag`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `taskName` (`taskName`);

--
-- Indexes for table `title_proposal`
--
ALTER TABLE `title_proposal`
  ADD PRIMARY KEY (`id`),
  ADD KEY `projectID_constraint` (`projectID`);

--
-- Indexes for table `title_proposal_answers`
--
ALTER TABLE `title_proposal_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tpc_panelistID_constraint` (`panelistID`),
  ADD KEY `tpc_projectID_constraint` (`projectID`);

--
-- Indexes for table `title_proposal_inputs`
--
ALTER TABLE `title_proposal_inputs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `title_tags`
--
ALTER TABLE `title_tags`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tracking`
--
ALTER TABLE `tracking`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `number` (`number`),
  ADD KEY `tracking_projectID_constraint` (`projectID`),
  ADD KEY `trackingID_constraint` (`taskID`),
  ADD KEY `tracking_year_constraint` (`academicYearID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userID_session_constraint` (`userID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_year`
--
ALTER TABLE `academic_year`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `action_logs`
--
ALTER TABLE `action_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=184;

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=198;

--
-- AUTO_INCREMENT for table `advisers`
--
ALTER TABLE `advisers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `announcement`
--
ALTER TABLE `announcement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `capstone_papers`
--
ALTER TABLE `capstone_papers`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `capstone_projects`
--
ALTER TABLE `capstone_projects`
  MODIFY `projectID` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `coordinators`
--
ALTER TABLE `coordinators`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `defense_answers`
--
ALTER TABLE `defense_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `defense_dates`
--
ALTER TABLE `defense_dates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `document_templates`
--
ALTER TABLE `document_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `faculty_count`
--
ALTER TABLE `faculty_count`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `forgotpass_tokens`
--
ALTER TABLE `forgotpass_tokens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `invitations`
--
ALTER TABLE `invitations`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=282;

--
-- AUTO_INCREMENT for table `panelists`
--
ALTER TABLE `panelists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `previous_documents`
--
ALTER TABLE `previous_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `sectionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `specializations`
--
ALTER TABLE `specializations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `title_proposal`
--
ALTER TABLE `title_proposal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `title_proposal_answers`
--
ALTER TABLE `title_proposal_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `title_proposal_inputs`
--
ALTER TABLE `title_proposal_inputs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `title_tags`
--
ALTER TABLE `title_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `tracking`
--
ALTER TABLE `tracking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=971;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `action_logs`
--
ALTER TABLE `action_logs`
  ADD CONSTRAINT `user_id_constraint` FOREIGN KEY (`userID`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `logs_project_constraint` FOREIGN KEY (`projectID`) REFERENCES `capstone_projects` (`projectID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `logs_task_constraint` FOREIGN KEY (`taskID`) REFERENCES `tasks` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `logs_trackNum_constraint` FOREIGN KEY (`trackingNum`) REFERENCES `tracking` (`number`) ON UPDATE CASCADE,
  ADD CONSTRAINT `logs_user_constraint` FOREIGN KEY (`userID`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `capstone_papers`
--
ALTER TABLE `capstone_papers`
  ADD CONSTRAINT `paper_project_constraint` FOREIGN KEY (`projectID`) REFERENCES `capstone_projects` (`projectID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `paper_tracknum_constraint` FOREIGN KEY (`trackingNum`) REFERENCES `tracking` (`number`) ON UPDATE CASCADE,
  ADD CONSTRAINT `paper_year_constraint` FOREIGN KEY (`academicYearID`) REFERENCES `academic_year` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `capstone_projects`
--
ALTER TABLE `capstone_projects`
  ADD CONSTRAINT `cp_acadyear_constraint` FOREIGN KEY (`academicYearID`) REFERENCES `academic_year` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `cp_coordinator_constraint` FOREIGN KEY (`coordinatorID`) REFERENCES `coordinators` (`facultyID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `cp_section_constraint` FOREIGN KEY (`sectionID`) REFERENCES `sections` (`sectionID`) ON UPDATE CASCADE;

--
-- Constraints for table `coordinators`
--
ALTER TABLE `coordinators`
  ADD CONSTRAINT `acad_year_constraint` FOREIGN KEY (`academicYearID`) REFERENCES `academic_year` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `coordinator_id_constraint` FOREIGN KEY (`facultyID`) REFERENCES `faculty` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `course_admin_id_constraint` FOREIGN KEY (`adminID`) REFERENCES `faculty` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `defense_answers`
--
ALTER TABLE `defense_answers`
  ADD CONSTRAINT `defense_project_constraint` FOREIGN KEY (`projectID`) REFERENCES `capstone_projects` (`projectID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `defense_year_constraint` FOREIGN KEY (`academicYearID`) REFERENCES `academic_year` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `document_templates`
--
ALTER TABLE `document_templates`
  ADD CONSTRAINT `template_task_constraint` FOREIGN KEY (`taskID`) REFERENCES `tasks` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `faculty`
--
ALTER TABLE `faculty`
  ADD CONSTRAINT `faculty_id_constraint` FOREIGN KEY (`id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `faculty_count`
--
ALTER TABLE `faculty_count`
  ADD CONSTRAINT `faculty_userID_constraint` FOREIGN KEY (`facultyID`) REFERENCES `faculty` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `invitations`
--
ALTER TABLE `invitations`
  ADD CONSTRAINT `invite_acadyear_constraint` FOREIGN KEY (`academicYearID`) REFERENCES `academic_year` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `invite_faculty_constraint` FOREIGN KEY (`facultyID`) REFERENCES `faculty` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `invite_project_constraint` FOREIGN KEY (`projectID`) REFERENCES `capstone_projects` (`projectID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `invite_tracking_constraint` FOREIGN KEY (`trackingNum`) REFERENCES `tracking` (`number`) ON UPDATE CASCADE;

--
-- Constraints for table `remember_tokens`
--
ALTER TABLE `remember_tokens`
  ADD CONSTRAINT `remember_id_constraint` FOREIGN KEY (`id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `sections`
--
ALTER TABLE `sections`
  ADD CONSTRAINT `acadyear_constraint` FOREIGN KEY (`academicYearID`) REFERENCES `academic_year` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `coordinatorID_constraint` FOREIGN KEY (`coordinatorID`) REFERENCES `coordinators` (`facultyID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `course_id_constraint` FOREIGN KEY (`courseID`) REFERENCES `courses` (`courseID`) ON UPDATE CASCADE;

--
-- Constraints for table `specializations`
--
ALTER TABLE `specializations`
  ADD CONSTRAINT `specialization_course_constraint` FOREIGN KEY (`courseID`) REFERENCES `courses` (`courseID`) ON UPDATE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `section_id_constraint` FOREIGN KEY (`sectionID`) REFERENCES `sections` (`sectionID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `student_id_constraint` FOREIGN KEY (`id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `student_new_projectID_constraint` FOREIGN KEY (`new_projectID`) REFERENCES `capstone_projects` (`projectID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `student_new_sectionID_constraint` FOREIGN KEY (`new_sectionID`) REFERENCES `sections` (`sectionID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `student_projectID_constraint` FOREIGN KEY (`projectID`) REFERENCES `capstone_projects` (`projectID`) ON UPDATE CASCADE;

--
-- Constraints for table `title_proposal`
--
ALTER TABLE `title_proposal`
  ADD CONSTRAINT `projectID_constraint` FOREIGN KEY (`projectID`) REFERENCES `capstone_projects` (`projectID`) ON UPDATE CASCADE;

--
-- Constraints for table `tracking`
--
ALTER TABLE `tracking`
  ADD CONSTRAINT `trackingID_constraint` FOREIGN KEY (`taskID`) REFERENCES `tasks` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tracking_projectID_constraint` FOREIGN KEY (`projectID`) REFERENCES `capstone_projects` (`projectID`) ON UPDATE CASCADE,
  ADD CONSTRAINT `tracking_year_constraint` FOREIGN KEY (`academicYearID`) REFERENCES `academic_year` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
