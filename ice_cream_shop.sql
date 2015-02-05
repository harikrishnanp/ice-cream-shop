-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jan 28, 2015 at 06:04 PM
-- Server version: 5.6.11
-- PHP Version: 5.5.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `ice_cream_shop`
--
CREATE DATABASE IF NOT EXISTS `ice_cream_shop` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `ice_cream_shop`;

-- --------------------------------------------------------

--
-- Table structure for table `discount`
--

CREATE TABLE IF NOT EXISTS `discount` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'Name of discount',
  `discount_percent` float NOT NULL COMMENT 'percentage of of discount',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `ice_cream_flavours`
--

CREATE TABLE IF NOT EXISTS `ice_cream_flavours` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT 'Name of Ice cream flavour ',
  `quantity_available` int(22) unsigned NOT NULL DEFAULT '0' COMMENT 'Available quantity of flavor in Litres. ',
  `price_per_litre` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Price per liter for flavor  in USD ',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `ice_cream_flavours`
--

INSERT INTO `ice_cream_flavours` (`id`, `name`, `quantity_available`, `price_per_litre`) VALUES
(1, 'Vanilla', 393, 10),
(2, 'Chocolate', 396, 15),
(3, 'Strawberry ', 578, 12),
(4, 'Grapes', 167, 18),
(5, 'Mango', 665, 7);

-- --------------------------------------------------------

--
-- Table structure for table `milk`
--

CREATE TABLE IF NOT EXISTS `milk` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `quantity_available` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Available quantity of milk in Liters. ',
  `price_per_litre` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Price per liter for milk in USD ',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `milk`
--

INSERT INTO `milk` (`id`, `name`, `quantity_available`, `price_per_litre`) VALUES
(1, 'Skim', 426, 35),
(2, '2 percent milk', 530, 30),
(3, 'Whole', 710, 20);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'Name of product (Eg: Milk shakes, Floats)',
  `discount_percent` int(11) unsigned NOT NULL COMMENT 'Discount in percentage',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `discount_percent`) VALUES
(1, 'Ice cream cone', 1),
(2, 'Milk shake', 5),
(4, 'Float', 10);

-- --------------------------------------------------------

--
-- Table structure for table `serving_vessels`
--

CREATE TABLE IF NOT EXISTS `serving_vessels` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL COMMENT 'Name of Ice cream serving vessels ',
  `quantity_available` int(12) unsigned NOT NULL DEFAULT '0' COMMENT 'No of serving vessels availible',
  `price_per_item` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'Price of one serving vessel in USD ',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `serving_vessels`
--

INSERT INTO `serving_vessels` (`id`, `name`, `quantity_available`, `price_per_item`) VALUES
(6, 'Waffle cone', 59, 15),
(7, 'Cup', 689, 12);

-- --------------------------------------------------------

--
-- Table structure for table `sodas`
--

CREATE TABLE IF NOT EXISTS `sodas` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'Name of soda',
  `price_per_litre` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Price per liter of soda',
  `quantity_available` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Available quantity of soda in Liters. ',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `sodas`
--

INSERT INTO `sodas` (`id`, `name`, `price_per_litre`, `quantity_available`) VALUES
(1, 'Orange crush', 15, 880),
(2, 'Cherry 7UP', 10, 749),
(3, 'Mountain Dew', 8, 469),
(4, 'Coca Cola Vanilla', 15, 820),
(5, 'Strawberry Crush', 20, 350);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
