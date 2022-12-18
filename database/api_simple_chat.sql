-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 18, 2022 at 12:42 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `api_simple_chat`
--

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `user_id` varchar(7) NOT NULL,
  `username` varchar(100) NOT NULL,
  `phonenumber` varchar(14) NOT NULL,
  `auth_key` varchar(10) NOT NULL,
  `ts_insert` timestamp NULL DEFAULT current_timestamp(),
  `status` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `user_id`, `username`, `phonenumber`, `auth_key`, `ts_insert`, `status`) VALUES
(1, 'USR-001', 'Ricky Raven', '081905377261', '1234567890', '2022-12-16 20:07:11', 1),
(10, 'USR-102', 'Giovanni', '0987621312', 'iElXTchBKQ', '2022-12-17 04:58:10', 1),
(11, 'USR-263', 'Rico', '098762987', 'BO5SN0x6Ki', '2022-12-17 04:59:04', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_message`
--

CREATE TABLE `user_message` (
  `id` int(11) NOT NULL,
  `conversation_id` varchar(7) NOT NULL,
  `message_id` varchar(7) NOT NULL,
  `sender_user_id` varchar(7) NOT NULL,
  `receiver_user_id` varchar(7) NOT NULL,
  `message` longtext DEFAULT NULL,
  `is_read` int(2) NOT NULL DEFAULT 0,
  `ts_insert` timestamp NULL DEFAULT current_timestamp(),
  `status` int(2) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_message`
--

INSERT INTO `user_message` (`id`, `conversation_id`, `message_id`, `sender_user_id`, `receiver_user_id`, `message`, `is_read`, `ts_insert`, `status`) VALUES
(1, 'COV-590', 'MSG-524', 'USR-001', 'USR-102', 'Hai, senang berkenalan dengan anda!', 0, '2022-12-17 06:50:15', 1),
(2, 'COV-590', 'MSG-843', 'USR-001', 'USR-102', 'Haii', 0, '2022-12-17 06:52:24', 1),
(3, 'COV-321', 'MSG-482', 'USR-001', 'USR-263', 'Hai Rico!!', 1, '2022-12-17 06:53:13', 1),
(4, 'COV-321', 'MSG-359', 'USR-001', 'USR-263', 'Test Rico!!', 1, '2022-12-17 06:53:28', 1),
(5, 'COV-321', 'MSG-382', 'USR-263', 'USR-001', 'Test Rico!!', 1, '2022-12-17 06:54:26', 1),
(6, 'COV-321', 'MSG-159', 'USR-001', 'USR-263', 'Test Rico!!', 1, '2022-12-17 07:02:17', 1),
(8, 'COV-321', 'MSG-495', 'USR-001', 'USR-263', 'Thank You', 0, '2022-12-18 05:38:33', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_message`
--
ALTER TABLE `user_message`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `user_message`
--
ALTER TABLE `user_message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
