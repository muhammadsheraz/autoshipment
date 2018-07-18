-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 11, 2017 at 11:20 PM
-- Server version: 10.1.21-MariaDB
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tgservice`
--

-- --------------------------------------------------------

--
-- Table structure for table `tg_orders_sync`
--

CREATE TABLE `tg_orders_sync` (
  `id` bigint(50) NOT NULL,
  `salesorder_id` bigint(50) NOT NULL,
  `salesorder_customer_id` bigint(50) NOT NULL,
  `salesorder_customer_name` varchar(75) NOT NULL,
  `salesorder_customer_email` varchar(50) DEFAULT NULL,
  `salesorder_order_status` varchar(15) DEFAULT NULL,
  `salesorder_reference_number` varchar(50) DEFAULT NULL,
  `salesorder_created_time` datetime DEFAULT NULL,
  `salesorder_modified_time` datetime DEFAULT NULL,
  `salesorder_lineitems` text,
  `salesorder_shipping_address` text,
  `salesorder_billing_address` text,
  `label_file` varchar(100) DEFAULT '0',
  `packaging_slip_file` varchar(100) DEFAULT '0',
  `ups_tracking_number` varchar(25) DEFAULT '0',
  `ups_confirm_response` text,
  `ups_accept_response` text,
  `email_sent_date` datetime DEFAULT NULL,
  `last_sync` datetime DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tg_orders_sync`
--
ALTER TABLE `tg_orders_sync`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tg_orders_sync`
--
ALTER TABLE `tg_orders_sync`
  MODIFY `id` bigint(50) NOT NULL AUTO_INCREMENT;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
