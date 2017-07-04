-- phpMyAdmin SQL Dump
-- version 4.1.14.8
-- http://www.phpmyadmin.net
--
-- Host: db680704532.db.1and1.com
-- Erstellungszeit: 04. Jul 2017 um 15:01
-- Server Version: 5.5.55-0+deb7u1-log
-- PHP-Version: 5.4.45-0+deb7u8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `db680704532`
--
CREATE DATABASE IF NOT EXISTS `db680704532` DEFAULT CHARACTER SET latin1 COLLATE latin1_german2_ci;
USE `db680704532`;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `commentratings`
--

DROP TABLE IF EXISTS `commentratings`;
CREATE TABLE IF NOT EXISTS `commentratings` (
  `ID` int(100) NOT NULL AUTO_INCREMENT,
  `subject_ID` varchar(100) NOT NULL,
  `comment_ID` varchar(100) NOT NULL,
  `user_ID` varchar(100) NOT NULL,
  `rating_direction` varchar(100) NOT NULL,
  `time_stamp` varchar(100) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `institutes`
--

DROP TABLE IF EXISTS `institutes`;
CREATE TABLE IF NOT EXISTS `institutes` (
  `institute_ID` int(100) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `abbr` varchar(100) NOT NULL,
  `user_ID` int(100) NOT NULL,
  `time_stamp` varchar(100) NOT NULL,
  `lastChangedBy_ID` int(100) NOT NULL,
  `time_stamp2` varchar(100) NOT NULL,
  PRIMARY KEY (`institute_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `lecturers`
--

DROP TABLE IF EXISTS `lecturers`;
CREATE TABLE IF NOT EXISTS `lecturers` (
  `lecturer_ID` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `user_ID` int(100) NOT NULL,
  `time_stamp` varchar(100) NOT NULL,
  `lastChangedBy_ID` int(100) NOT NULL,
  `time_stamp2` varchar(100) NOT NULL,
  PRIMARY KEY (`lecturer_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `lecturers_institutes`
--

DROP TABLE IF EXISTS `lecturers_institutes`;
CREATE TABLE IF NOT EXISTS `lecturers_institutes` (
  `lecturers_institutes_ID` int(100) NOT NULL AUTO_INCREMENT,
  `lecturer_ID` int(100) NOT NULL,
  `institute_ID` int(100) NOT NULL,
  PRIMARY KEY (`lecturers_institutes_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `levels`
--

DROP TABLE IF EXISTS `levels`;
CREATE TABLE IF NOT EXISTS `levels` (
  `level_ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`level_ID`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `modules`
--

DROP TABLE IF EXISTS `modules`;
CREATE TABLE IF NOT EXISTS `modules` (
  `module_ID` int(10) NOT NULL AUTO_INCREMENT,
  `code` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` varchar(100) NOT NULL,
  `ects` int(10) NOT NULL,
  `user_ID` int(10) NOT NULL,
  `time_stamp` varchar(100) NOT NULL,
  `lastChangedBy_ID` int(100) NOT NULL,
  `time_stamp2` varchar(100) NOT NULL,
  PRIMARY KEY (`module_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `modules_levels`
--

DROP TABLE IF EXISTS `modules_levels`;
CREATE TABLE IF NOT EXISTS `modules_levels` (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `module_ID` int(100) NOT NULL,
  `level_ID` int(100) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `moduletypes`
--

DROP TABLE IF EXISTS `moduletypes`;
CREATE TABLE IF NOT EXISTS `moduletypes` (
  `module_type_ID` int(100) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`module_type_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ratings`
--

DROP TABLE IF EXISTS `ratings`;
CREATE TABLE IF NOT EXISTS `ratings` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `subject_ID` int(100) NOT NULL,
  `crit1` varchar(100) NOT NULL,
  `crit2` varchar(100) NOT NULL,
  `crit3` varchar(100) NOT NULL,
  `crit4` varchar(100) NOT NULL,
  `crit5` varchar(100) NOT NULL,
  `recommendation` tinyint(1) NOT NULL,
  `comment_title` varchar(100) NOT NULL,
  `comment_body` varchar(5000) NOT NULL,
  `comment_rating` int(100) NOT NULL,
  `user_ID` int(10) NOT NULL,
  `time_stamp` varchar(100) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `subjects`
--

DROP TABLE IF EXISTS `subjects`;
CREATE TABLE IF NOT EXISTS `subjects` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `subject_name` varchar(100) NOT NULL,
  `code` varchar(100) NOT NULL,
  `identifier` varchar(100) NOT NULL,
  `lv_number` int(100) NOT NULL,
  `ECTS` varchar(100) NOT NULL,
  `semester` varchar(100) NOT NULL,
  `language` varchar(100) NOT NULL,
  `createdBy_ID` varchar(100) NOT NULL,
  `time_stamp` varchar(100) NOT NULL,
  `lastChangedBy_ID` int(100) NOT NULL,
  `time_stamp2` varchar(100) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Kürzel` (`code`),
  UNIQUE KEY `subject_name` (`subject_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `subjects_lecturers`
--

DROP TABLE IF EXISTS `subjects_lecturers`;
CREATE TABLE IF NOT EXISTS `subjects_lecturers` (
  `subjects_lecturers_ID` int(10) NOT NULL AUTO_INCREMENT,
  `subject_ID` int(10) NOT NULL,
  `lecturer_ID` int(10) NOT NULL,
  PRIMARY KEY (`subjects_lecturers_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `subjects_modules`
--

DROP TABLE IF EXISTS `subjects_modules`;
CREATE TABLE IF NOT EXISTS `subjects_modules` (
  `subjects_modules_ID` int(100) NOT NULL AUTO_INCREMENT,
  `subject_ID` int(11) NOT NULL,
  `module_ID` int(11) NOT NULL,
  PRIMARY KEY (`subjects_modules_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_ID` int(11) NOT NULL AUTO_INCREMENT,
  `admin` int(10) NOT NULL,
  `first_name` varchar(60) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(60) NOT NULL,
  `password` varchar(255) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `degree` varchar(100) NOT NULL,
  `advance` varchar(100) NOT NULL,
  `semester` varchar(100) NOT NULL,
  `info` varchar(100) NOT NULL,
  `hash` varchar(100) NOT NULL,
  `recoverhash` varchar(100) NOT NULL,
  PRIMARY KEY (`user_ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
