-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 02, 2012 at 02:48 PM
-- Server version: 5.5.16
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `pml`
--

-- --------------------------------------------------------

--
-- Table structure for table `class`
--

CREATE TABLE IF NOT EXISTS `class` (
  `id_class` mediumint(8) NOT NULL AUTO_INCREMENT,
  `class_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id_class`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `entry`
--

CREATE TABLE IF NOT EXISTS `entry` (
  `id_entry` mediumint(8) NOT NULL AUTO_INCREMENT,
  `id_reason` mediumint(8) NOT NULL DEFAULT '0',
  `id_student` mediumint(8) NOT NULL DEFAULT '0',
  `id_teacher` mediumint(8) NOT NULL DEFAULT '0',
  `entry_date` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_entry`),
  UNIQUE KEY `id_student` (`id_student`,`id_entry`),
  UNIQUE KEY `id_teacher` (`id_teacher`,`id_entry`),
  KEY `id_reason` (`id_reason`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reason`
--

CREATE TABLE IF NOT EXISTS `reason` (
  `id_reason` mediumint(8) NOT NULL AUTO_INCREMENT,
  `reason_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `reason_type` smallint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_reason`),
  KEY `reason_type` (`reason_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE IF NOT EXISTS `student` (
  `id_student` mediumint(8) NOT NULL AUTO_INCREMENT,
  `id_class` mediumint(8) NOT NULL DEFAULT '0',
  `student_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `student_surname` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id_student`),
  UNIQUE KEY `id_class` (`id_class`,`id_student`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teacher`
--

CREATE TABLE IF NOT EXISTS `teacher` (
  `id_teacher` mediumint(8) NOT NULL AUTO_INCREMENT,
  `teacher_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `teacher_surname` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `teacher_alias` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `teacher_password` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id_teacher`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
