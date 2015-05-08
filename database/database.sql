-- phpMyAdmin SQL Dump
-- version 4.1.9
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 08, 2015 at 12:41 PM
-- Server version: 5.5.41-MariaDB
-- PHP Version: 5.6.99-hhvm

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

-- --------------------------------------------------------

--
-- Table structure for table `comment_cache`
--

CREATE TABLE IF NOT EXISTS `comment_cache` (
  `cacheid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pageid` int(10) unsigned NOT NULL,
  `c_pageid` int(10) unsigned NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`cacheid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `comment_statistic`
--

CREATE TABLE IF NOT EXISTS `comment_statistic` (
  `pageid` int(10) unsigned NOT NULL,
  `pagecount` int(10) unsigned NOT NULL,
  `commentcount` int(10) unsigned NOT NULL,
  PRIMARY KEY (`pageid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `comment_table`
--

CREATE TABLE IF NOT EXISTS `comment_table` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page_id` int(10) unsigned NOT NULL,
  `parent_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `user_name` varchar(64) DEFAULT NULL,
  `user_email` varchar(64) DEFAULT NULL,
  `submit_time` datetime NOT NULL,
  `comment` text NOT NULL,
  `ipaddress` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_table`
--

CREATE TABLE IF NOT EXISTS `user_table` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_name` varchar(64) NOT NULL,
  `token` varchar(256) NOT NULL,
  `avatar_url` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_name` (`account_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `user_table`
--

INSERT INTO `user_table` (`id`, `account_name`, `token`, `avatar_url`) VALUES
(1, 'mc', '4c16afb8JustAnExample0', 'https://api.ink.moe/img/t.jpg');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
