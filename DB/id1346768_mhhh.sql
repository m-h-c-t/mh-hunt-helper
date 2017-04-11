-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 11, 2017 at 10:08 PM
-- Server version: 10.1.20-MariaDB
-- PHP Version: 7.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `id1346768_mhhh`
--

-- --------------------------------------------------------

--
-- Table structure for table `bases`
--

CREATE TABLE `bases` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `bases`
--

INSERT INTO `bases` (`id`, `name`) VALUES
(1644, 'Fissure'),
(1915, 'Minotaur');

-- --------------------------------------------------------

--
-- Table structure for table `charms`
--

CREATE TABLE `charms` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `charms`
--

INSERT INTO `charms` (`id`, `name`) VALUES
(1735, 'Diamond Boost'),
(851, 'Eggstra'),
(1714, 'Eggstra Charge'),
(1736, 'Gemstone Boost'),
(1822, 'Ultimate Ancient');

-- --------------------------------------------------------

--
-- Table structure for table `cheese`
--

CREATE TABLE `cheese` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `cheese`
--

INSERT INTO `cheese` (`id`, `name`) VALUES
(1732, 'Gemstone'),
(1733, 'Glowing Gruyere'),
(98, 'Gouda'),
(114, 'SUPER|brie+');

-- --------------------------------------------------------

--
-- Table structure for table `hunts`
--

CREATE TABLE `hunts` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `entry_id` int(10) UNSIGNED NOT NULL,
  `timestamp` int(11) UNSIGNED NOT NULL,
  `trap_id` int(10) UNSIGNED NOT NULL,
  `base_id` int(10) UNSIGNED NOT NULL,
  `charm_id` int(10) UNSIGNED DEFAULT NULL,
  `cheese_id` int(10) UNSIGNED NOT NULL,
  `location_id` int(10) UNSIGNED NOT NULL,
  `shield` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
  `caught` tinyint(1) UNSIGNED NOT NULL,
  `attracted` tinyint(1) UNSIGNED NOT NULL,
  `mouse_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `hunts`
--

INSERT INTO `hunts` (`user_id`, `entry_id`, `timestamp`, `trap_id`, `base_id`, `charm_id`, `cheese_id`, `location_id`, `shield`, `caught`, `attracted`, `mouse_id`) VALUES
(53772, 263424, 1491945310, 1813, 1915, 851, 114, 33, 1, 1, 1, 6),
(4839553, 166579, 1491941664, 1918, 1915, 1736, 1732, 50, 1, 1, 1, 1),
(4839553, 166584, 1491943518, 2137, 1915, 1714, 98, 54, 1, 1, 1, 5),
(4839553, 166589, 1491947267, 2137, 1915, 851, 114, 54, 1, 1, 1, 5),
(4839553, 166594, 1491948229, 1833, 1915, 851, 114, 38, 1, 1, 1, 7),
(6784428, 151538, 1491936772, 1916, 1644, 1735, 1732, 50, 1, 1, 1, 1),
(6784428, 151540, 1491937682, 1916, 1644, 1735, 1732, 50, 1, 0, 1, 2),
(6784428, 151543, 1491938594, 1916, 1644, 1735, 1732, 50, 1, 1, 1, 1),
(6784428, 151544, 1491939496, 1916, 1644, 1735, 1732, 50, 1, 0, 1, 2),
(6784428, 151550, 1491942312, 1916, 1644, 1735, 1732, 50, 1, 1, 1, 3),
(6784428, 151555, 1491945107, 1916, 1644, 1735, 1732, 50, 1, 0, 1, 2),
(6784428, 151557, 1491946051, 1916, 1644, 1735, 1732, 50, 1, 1, 1, 1),
(6784428, 151558, 1491946955, 1916, 1644, 1735, 1732, 50, 1, 1, 1, 3),
(6784428, 151561, 1491947856, 1916, 1644, 1735, 1732, 50, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `name`) VALUES
(33, 'Fiery Warpath'),
(54, 'Fort Rox'),
(50, 'Fungal Cavern'),
(38, 'King\'s Arms'),
(51, 'Zokor');

-- --------------------------------------------------------

--
-- Table structure for table `mice`
--

CREATE TABLE `mice` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `mice`
--

INSERT INTO `mice` (`id`, `name`) VALUES
(4, 'Crystal Behemoth'),
(2, 'Crystal Lurker'),
(3, 'Crystal Observer'),
(1, 'Crystal Queen'),
(7, 'Flying'),
(5, 'Mischievous Meteorite Miner'),
(6, 'Theurgy Warden');

-- --------------------------------------------------------

--
-- Table structure for table `traps`
--

CREATE TABLE `traps` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `traps`
--

INSERT INTO `traps` (`id`, `name`) VALUES
(1833, 'Chrome Sphynx Wrath'),
(1916, 'Endless Labyrinth'),
(1918, 'Infinite Labyrinth'),
(2137, 'Law Laser'),
(1813, 'Warden Slayer');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bases`
--
ALTER TABLE `bases`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `charms`
--
ALTER TABLE `charms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `cheese`
--
ALTER TABLE `cheese`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `hunts`
--
ALTER TABLE `hunts`
  ADD PRIMARY KEY (`user_id`,`entry_id`,`timestamp`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `mice`
--
ALTER TABLE `mice`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `traps`
--
ALTER TABLE `traps`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `mice`
--
ALTER TABLE `mice`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
