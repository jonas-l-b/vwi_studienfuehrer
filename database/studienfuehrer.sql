-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 28. Aug 2017 um 16:29
-- Server-Version: 10.1.16-MariaDB
-- PHP-Version: 5.6.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `studienfuehrer`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `commentratings`
--

CREATE TABLE `commentratings` (
  `ID` int(100) NOT NULL,
  `subject_ID` varchar(100) NOT NULL,
  `comment_ID` varchar(100) NOT NULL,
  `user_ID` varchar(100) NOT NULL,
  `rating_direction` varchar(100) NOT NULL,
  `time_stamp` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `commentratings`
--

INSERT INTO `commentratings` (`ID`, `subject_ID`, `comment_ID`, `user_ID`, `rating_direction`, `time_stamp`) VALUES
(1, '5', '1', '2', '1', '2017-06-08 22:10:24'),
(2, '5', '1', '1', '1', '2017-06-08 22:13:17'),
(3, '5', '2', '1', '-1', '2017-06-08 22:13:19'),
(4, '2', '5', '1', '1', '2017-08-13 23:40:29'),
(7, '5', '2', '2', '1', '2017-08-15 22:44:24'),
(22, '5', '10', '2', '1', '2017-08-17 22:56:36');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `favourites`
--

CREATE TABLE `favourites` (
  `ID` int(10) NOT NULL,
  `user_ID` int(10) NOT NULL,
  `subject_ID` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `favourites`
--

INSERT INTO `favourites` (`ID`, `user_ID`, `subject_ID`) VALUES
(36, 2, 4),
(39, 2, 2),
(46, 2, 1),
(72, 2, 5),
(73, 2, 5),
(74, 2, 5),
(75, 2, 5),
(76, 2, 5),
(77, 2, 5),
(78, 2, 6),
(79, 2, 8);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `institutes`
--

CREATE TABLE `institutes` (
  `institute_ID` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `abbr` varchar(100) NOT NULL,
  `user_ID` int(100) NOT NULL,
  `time_stamp` varchar(100) NOT NULL,
  `lastChangedBy_ID` int(100) NOT NULL,
  `time_stamp2` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `institutes`
--

INSERT INTO `institutes` (`institute_ID`, `name`, `abbr`, `user_ID`, `time_stamp`, `lastChangedBy_ID`, `time_stamp2`) VALUES
(1, 'Institut für Unternehmensführung', 'IBU', 2, '2017-06-08 21:36:39', 0, ''),
(2, 'Institut für Finanzwirtschaft, Banken und Versicherungen', 'FBV', 2, '2017-06-08 21:37:59', 0, ''),
(3, 'Institut für Informationswirtschaft und Marketing', 'IISM', 2, '2017-06-08 21:38:48', 0, ''),
(4, 'Institut für Operations Research', 'IOR', 2, '2017-06-08 21:44:54', 0, ''),
(5, 'Institut für Angewandte Materialien', 'IAM', 2, '2017-06-08 21:51:18', 0, ''),
(6, 'Institut für Angewandte Informatik und Formale Beschreibungsverfahren', 'AIFB', 2, '2017-06-08 21:56:09', 0, ''),
(7, 'Institut für Fahrzeugtechnik', 'FAST', 2, '2017-06-08 22:00:39', 0, ''),
(8, 'Karlsruhe Service Research Institute', 'KSRI', 2, '2017-06-08 22:05:13', 0, '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `lecturers`
--

CREATE TABLE `lecturers` (
  `lecturer_ID` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `user_ID` int(100) NOT NULL,
  `time_stamp` varchar(100) NOT NULL,
  `lastChangedBy_ID` int(100) NOT NULL,
  `time_stamp2` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `lecturers`
--

INSERT INTO `lecturers` (`lecturer_ID`, `first_name`, `last_name`, `user_ID`, `time_stamp`, `lastChangedBy_ID`, `time_stamp2`) VALUES
(1, 'Alexander', 'Klopfer', 2, '2017-06-08 21:36:44', 0, ''),
(2, 'Hagen', 'Lindtstädt', 2, '2017-06-08 21:37:01', 0, ''),
(3, 'Jan-Oliver', 'Strych', 2, '2017-06-08 21:38:04', 0, ''),
(4, 'Christof', 'Weinhardt', 2, '2017-06-08 21:38:53', 0, ''),
(5, 'Stefan', 'Nickel', 2, '2017-06-08 21:45:00', 0, ''),
(6, 'Steffen', 'Rebennack', 2, '2017-06-08 21:45:42', 0, ''),
(7, 'Oliver', 'Stein', 2, '2017-06-08 21:45:53', 0, ''),
(8, 'Michael', 'Hoffmann', 2, '2017-06-08 21:51:24', 0, ''),
(9, 'York', 'Sure-Vetter', 2, '2017-06-08 21:56:15', 0, ''),
(10, 'Frank', 'Grauterin', 2, '2017-06-08 22:00:45', 0, ''),
(11, 'Hans-Joachim', 'Unrau', 2, '2017-06-08 22:00:53', 0, ''),
(12, 'Gerhard', 'Satzger', 2, '2017-06-08 22:05:20', 0, '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `lecturers_institutes`
--

CREATE TABLE `lecturers_institutes` (
  `lecturers_institutes_ID` int(100) NOT NULL,
  `lecturer_ID` int(100) NOT NULL,
  `institute_ID` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `lecturers_institutes`
--

INSERT INTO `lecturers_institutes` (`lecturers_institutes_ID`, `lecturer_ID`, `institute_ID`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 2),
(4, 4, 3),
(5, 5, 4),
(6, 6, 4),
(7, 7, 4),
(8, 8, 5),
(9, 9, 6),
(10, 10, 7),
(11, 11, 7),
(12, 12, 8);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `levels`
--

CREATE TABLE `levels` (
  `level_ID` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `levels`
--

INSERT INTO `levels` (`level_ID`, `name`) VALUES
(2, 'bachelor'),
(1, 'bachelor_basic'),
(3, 'master');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `messages`
--

CREATE TABLE `messages` (
  `message_id` int(100) NOT NULL,
  `sender_id` int(100) NOT NULL,
  `receiver_id` int(100) NOT NULL,
  `message_type` varchar(100) NOT NULL,
  `area` varchar(100) NOT NULL,
  `object_id` int(11) NOT NULL,
  `answer_required` int(100) NOT NULL,
  `comment` varchar(5000) NOT NULL,
  `time_stamp` varchar(100) NOT NULL,
  `read_last_id` int(10) NOT NULL,
  `read_last_time_stamp` text NOT NULL,
  `assigned_to_id` int(10) NOT NULL,
  `assigned_to_time_stamp` varchar(100) NOT NULL,
  `processed` int(10) NOT NULL,
  `processed_by_id` int(11) NOT NULL,
  `processed_comment` varchar(5000) NOT NULL,
  `processed_comment_for_admins` varchar(5000) NOT NULL,
  `processed_time_stamp` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `messages`
--

INSERT INTO `messages` (`message_id`, `sender_id`, `receiver_id`, `message_type`, `area`, `object_id`, `answer_required`, `comment`, `time_stamp`, `read_last_id`, `read_last_time_stamp`, `assigned_to_id`, `assigned_to_time_stamp`, `processed`, `processed_by_id`, `processed_comment`, `processed_comment_for_admins`, `processed_time_stamp`) VALUES
(89, 2, -1, 'bug', '', 0, 1, 'Das ist wohl ein Bug!', '2017-08-29 00:28:46', 0, '', 0, '', 0, 0, '', '', '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `modules`
--

CREATE TABLE `modules` (
  `module_ID` int(10) NOT NULL,
  `code` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` varchar(100) NOT NULL,
  `ects` int(10) NOT NULL,
  `user_ID` int(10) NOT NULL,
  `time_stamp` varchar(100) NOT NULL,
  `lastChangedBy_ID` int(100) NOT NULL,
  `time_stamp2` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `modules`
--

INSERT INTO `modules` (`module_ID`, `code`, `name`, `type`, `ects`, `user_ID`, `time_stamp`, `lastChangedBy_ID`, `time_stamp2`) VALUES
(1, 'M-WIWI-101494', 'Grundlagen BWL 1', 'BWL', 7, 0, '2017-06-08 21:39:56', 0, ''),
(2, 'M-WIWI-101418', 'Einführung in das Operations Research', 'OR', 9, 0, '2017-06-08 21:46:56', 0, ''),
(3, 'M-MACH-101260', 'Werkstoffkunde', 'ING', 3, 0, '2017-06-08 21:50:14', 0, ''),
(4, 'M-WIWI-101399', 'Vertiefung Informatik', 'INFO', 9, 0, '2017-06-08 21:53:36', 0, ''),
(5, 'M-WIWI-101472', 'Informatik', 'INFO', 9, 0, '2017-06-08 21:58:07', 0, ''),
(6, 'M-MACH-101266', 'Fahrzeugtechnik', 'ING', 9, 0, '2017-06-08 22:02:06', 0, ''),
(7, 'M-WIWI-102754', 'Service Economics and Management', 'BWL', 9, 0, '2017-06-08 22:06:15', 0, ''),
(8, 'M-WIWI-101448', 'Service Management', 'BWL', 9, 0, '2017-06-08 22:06:57', 0, ''),
(9, 'lorem ipsum', 'Info-TEST', 'INFO', 8, 0, '2017-08-21 20:20:35', 0, '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `modules_levels`
--

CREATE TABLE `modules_levels` (
  `ID` int(10) NOT NULL,
  `module_ID` int(100) NOT NULL,
  `level_ID` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `modules_levels`
--

INSERT INTO `modules_levels` (`ID`, `module_ID`, `level_ID`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 1),
(4, 4, 2),
(5, 5, 3),
(6, 6, 2),
(7, 6, 3),
(8, 7, 3),
(9, 8, 3),
(10, 9, 3);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `moduletypes`
--

CREATE TABLE `moduletypes` (
  `module_type_ID` int(100) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `moduletypes`
--

INSERT INTO `moduletypes` (`module_type_ID`, `name`) VALUES
(1, 'BWL'),
(2, 'VWL'),
(3, 'INFO'),
(4, 'OR'),
(5, 'ING'),
(6, 'Sonstige');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `ratings`
--

CREATE TABLE `ratings` (
  `ID` int(11) NOT NULL,
  `subject_ID` int(100) NOT NULL,
  `crit1` varchar(100) NOT NULL,
  `crit2` varchar(100) NOT NULL,
  `crit3` varchar(100) NOT NULL,
  `crit4` varchar(100) NOT NULL,
  `crit5` varchar(100) NOT NULL,
  `recommendation` tinyint(1) NOT NULL,
  `comment` varchar(5000) NOT NULL,
  `comment_rating` int(100) NOT NULL,
  `user_ID` int(10) NOT NULL,
  `time_stamp` varchar(100) NOT NULL,
  `time_stamp_change` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `ratings`
--

INSERT INTO `ratings` (`ID`, `subject_ID`, `crit1`, `crit2`, `crit3`, `crit4`, `crit5`, `recommendation`, `comment`, `comment_rating`, `user_ID`, `time_stamp`, `time_stamp_change`) VALUES
(2, 5, '5', '4', '5', '6', '4', 0, 'Den Teil vom Unrau fand ich überragend! Der Grauterin hat sich allerdings gerne mal in irgendwelche irrelevanten Rechnungen verloren.. und ich weiß immer noch nicht genau, wie man seinen Namen eigentlich ausspricht!', 0, 1, '2017-06-08 22:13:06', ''),
(8, 2, '5', '2', '2', '2', '2', 0, 'adfsadf', 0, 1, '2017-08-14 00:41:01', ''),
(10, 5, '7', '4', '6', '5', '7', 1, 'Super ING-Fach, sehr empfehlenswert!', 1, 2, '2017-08-14 12:20:56', '2017-08-14 12:21:23');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `subjects`
--

CREATE TABLE `subjects` (
  `ID` int(11) NOT NULL,
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
  `time_stamp2` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `subjects`
--

INSERT INTO `subjects` (`ID`, `subject_name`, `code`, `identifier`, `lv_number`, `ECTS`, `semester`, `language`, `createdBy_ID`, `time_stamp`, `lastChangedBy_ID`, `time_stamp2`) VALUES
(1, 'Betriebswirtschaftslehre: Unternehmensführung und Informationswirtschaft', 'bwlui', 'T-WIWI-102817', 2600023, '3', 'Winter', 'Deutsch', '2', '2017-06-08 21:40:15', 0, ''),
(2, 'Einführung in das Operations Research I und II', 'or12', 'T-WIWI-102758', 2550040, '9', 'Sommer', 'Deutsch', '2', '2017-06-08 21:47:19', 0, ''),
(3, 'Werkstoffkunde I für Wirtschaftsingenieure', 'weku1', 'T-MACH-102078', 2125760, '3', 'Winter', 'Deutsch', '2', '2017-06-08 21:52:11', 0, ''),
(4, 'Angewandte Informatik II - Informatiksysteme für eCommerce', 'ai2', 'T-WIWI-102651', 2511032, '5', 'Sommer', 'Deutsch', '2', '2017-06-08 21:59:01', 2, '2017-08-21 20:20:56'),
(5, 'Grundlagen der Fahrzeugtechnik I', 'fzt1', 'T-MACH-100092', 2113805, '6', 'Winter', 'Deutsch', '2', '2017-06-08 22:02:24', 0, ''),
(6, 'Business and IT Service Management ', 'bitsem', 'T-WIWI-738103', 97531, '4,5', 'Winter', 'Deutsch', '2', '2017-08-13 18:09:45', 0, ''),
(7, 'Programmieren I', 'prog1', 'T-WIWI-102864', 123456, '5', 'Winter', 'Deutsch', '2', '2017-08-20 00:08:29', 0, ''),
(8, 'aWieBwl', 'abwl', '123456', 12345678, '3', 'Winter', 'Deutsch', '2', '2017-08-21 20:23:31', 0, '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `subjects_lecturers`
--

CREATE TABLE `subjects_lecturers` (
  `subjects_lecturers_ID` int(10) NOT NULL,
  `subject_ID` int(10) NOT NULL,
  `lecturer_ID` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `subjects_lecturers`
--

INSERT INTO `subjects_lecturers` (`subjects_lecturers_ID`, `subject_ID`, `lecturer_ID`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 2, 5),
(6, 2, 6),
(7, 2, 7),
(8, 3, 8),
(10, 5, 10),
(11, 5, 11),
(12, 6, 12),
(13, 7, 9),
(16, 4, 9),
(17, 8, 10);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `subjects_modules`
--

CREATE TABLE `subjects_modules` (
  `subjects_modules_ID` int(100) NOT NULL,
  `subject_ID` int(11) NOT NULL,
  `module_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `subjects_modules`
--

INSERT INTO `subjects_modules` (`subjects_modules_ID`, `subject_ID`, `module_ID`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 3),
(6, 5, 6),
(7, 6, 8),
(8, 7, 5),
(14, 4, 9),
(15, 4, 5),
(16, 4, 4),
(17, 8, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

CREATE TABLE `users` (
  `user_ID` int(11) NOT NULL,
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
  `recoverhash` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`user_ID`, `admin`, `first_name`, `last_name`, `username`, `email`, `password`, `active`, `degree`, `advance`, `semester`, `info`, `hash`, `recoverhash`) VALUES
(1, 0, 'Charles', 'Carmichael', 'charly123', 'charles.carmichael@gmail.com', '$2y$10$/sGQy6yKCJyzdI1UDBGsDeNfxbpHek3.3K84ZOsuc6QbWmcRAKzQG', 1, 'wiwi', 'bachelor', '5', 'yes', '', ''),
(2, 1, 'Albert', 'Einstein', 'der_albert', 'albert.einstein@student.kit.edu', '$2y$10$Olubv.Q98VXDGVHsNxXcU.wjL08FWSjZLQlSfg2epJ1enmNhLk6nW', 1, 'Physik', 'bachelor', '5', 'yes', '2ca65f58e35d9ad45bf7f3ae5cfd08f1', ''),
(3, 1, 'test', 'admin', 'test_admin', 'asdfg', 'asdfg', 1, 'asdfg', 'sdfg', 'sdfg', 'sdfgh', 'sdfgh', '');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `commentratings`
--
ALTER TABLE `commentratings`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `subject_ID` (`subject_ID`,`comment_ID`,`user_ID`);

--
-- Indizes für die Tabelle `favourites`
--
ALTER TABLE `favourites`
  ADD PRIMARY KEY (`ID`);

--
-- Indizes für die Tabelle `institutes`
--
ALTER TABLE `institutes`
  ADD PRIMARY KEY (`institute_ID`);

--
-- Indizes für die Tabelle `lecturers`
--
ALTER TABLE `lecturers`
  ADD PRIMARY KEY (`lecturer_ID`);

--
-- Indizes für die Tabelle `lecturers_institutes`
--
ALTER TABLE `lecturers_institutes`
  ADD PRIMARY KEY (`lecturers_institutes_ID`);

--
-- Indizes für die Tabelle `levels`
--
ALTER TABLE `levels`
  ADD PRIMARY KEY (`level_ID`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indizes für die Tabelle `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`);

--
-- Indizes für die Tabelle `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`module_ID`);

--
-- Indizes für die Tabelle `modules_levels`
--
ALTER TABLE `modules_levels`
  ADD PRIMARY KEY (`ID`);

--
-- Indizes für die Tabelle `moduletypes`
--
ALTER TABLE `moduletypes`
  ADD PRIMARY KEY (`module_type_ID`);

--
-- Indizes für die Tabelle `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`ID`);

--
-- Indizes für die Tabelle `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Kürzel` (`code`),
  ADD UNIQUE KEY `subject_name` (`subject_name`);

--
-- Indizes für die Tabelle `subjects_lecturers`
--
ALTER TABLE `subjects_lecturers`
  ADD PRIMARY KEY (`subjects_lecturers_ID`);

--
-- Indizes für die Tabelle `subjects_modules`
--
ALTER TABLE `subjects_modules`
  ADD PRIMARY KEY (`subjects_modules_ID`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_ID`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `commentratings`
--
ALTER TABLE `commentratings`
  MODIFY `ID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT für Tabelle `favourites`
--
ALTER TABLE `favourites`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;
--
-- AUTO_INCREMENT für Tabelle `institutes`
--
ALTER TABLE `institutes`
  MODIFY `institute_ID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT für Tabelle `lecturers`
--
ALTER TABLE `lecturers`
  MODIFY `lecturer_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT für Tabelle `lecturers_institutes`
--
ALTER TABLE `lecturers_institutes`
  MODIFY `lecturers_institutes_ID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT für Tabelle `levels`
--
ALTER TABLE `levels`
  MODIFY `level_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT für Tabelle `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;
--
-- AUTO_INCREMENT für Tabelle `modules`
--
ALTER TABLE `modules`
  MODIFY `module_ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT für Tabelle `modules_levels`
--
ALTER TABLE `modules_levels`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT für Tabelle `moduletypes`
--
ALTER TABLE `moduletypes`
  MODIFY `module_type_ID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT für Tabelle `ratings`
--
ALTER TABLE `ratings`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
--
-- AUTO_INCREMENT für Tabelle `subjects`
--
ALTER TABLE `subjects`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT für Tabelle `subjects_lecturers`
--
ALTER TABLE `subjects_lecturers`
  MODIFY `subjects_lecturers_ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT für Tabelle `subjects_modules`
--
ALTER TABLE `subjects_modules`
  MODIFY `subjects_modules_ID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `user_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
