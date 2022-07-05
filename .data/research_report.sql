-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Erstellungszeit: 05. Jul 2022 um 04:17
-- Server-Version: 10.4.10-MariaDB
-- PHP-Version: 8.1.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `research_report`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `activity`
--

DROP TABLE IF EXISTS `activity`;
CREATE TABLE IF NOT EXISTS `activity` (
  `activity_id` int(11) NOT NULL AUTO_INCREMENT,
  `user` varchar(6) NOT NULL,
  `category` varchar(100) NOT NULL,
  `title` mediumtext NOT NULL,
  `activity_count` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`activity_id`),
  KEY `user` (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `authors`
--

DROP TABLE IF EXISTS `authors`;
CREATE TABLE IF NOT EXISTS `authors` (
  `author_id` int(11) NOT NULL AUTO_INCREMENT,
  `publication_id` int(11) DEFAULT NULL,
  `poster_id` int(11) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `position` enum('first','last','middle') NOT NULL DEFAULT 'middle',
  `dsmz_affiliation` tinyint(1) NOT NULL DEFAULT 0,
  `user` varchar(6) DEFAULT NULL,
  PRIMARY KEY (`author_id`),
  KEY `poster_id` (`poster_id`),
  KEY `publication_id` (`publication_id`),
  KEY `user` (`user`)
) ENGINE=MyISAM AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `authors`
--

INSERT INTO `authors` (`author_id`, `publication_id`, `poster_id`, `last_name`, `first_name`, `position`, `dsmz_affiliation`, `user`) VALUES
(1, 2, NULL, 'Reimer', 'Lorenz Christian', 'first', 1, NULL),
(2, 2, NULL, 'Sardà Carbasse', 'Joaquim', 'middle', 1, NULL),
(3, 2, NULL, 'Koblitz', 'Julia', 'middle', 1, 'juk20'),
(4, 2, NULL, 'Ebeling', 'Christian', 'middle', 1, NULL),
(5, 2, NULL, 'Podstawka', 'Adam', 'middle', 1, NULL),
(6, 2, NULL, 'Overmann', 'Jörg', 'last', 1, NULL),
(7, 4, NULL, 'Koblitz', 'Julia', 'middle', 1, 'juk20'),
(8, 4, NULL, 'Dirks', 'Wilhelm G.', 'middle', 1, NULL),
(9, 4, NULL, 'Eberth', 'Sonja', 'middle', 1, NULL),
(10, 4, NULL, 'Nagel', 'Stefan', 'middle', 1, NULL),
(11, 4, NULL, 'Steenpass', 'Laura', 'middle', 1, NULL),
(12, 4, NULL, 'Pommerenke', 'Claudia', 'last', 1, 'cpo14'),
(13, 5, NULL, 'Spring', 'Stefan', 'middle', 1, NULL),
(14, 5, NULL, 'Rohde', 'Manfred', 'middle', 0, NULL),
(15, 5, NULL, 'Bunk', 'Boyke', 'middle', 1, 'bob10'),
(16, 5, NULL, 'Spröer', 'Cathrin', 'middle', 1, NULL),
(17, 5, NULL, 'Will', 'Sabine Eva', 'middle', 1, NULL),
(18, NULL, 1, 'Koblitz', 'Julia', 'first', 1, 'juk20'),
(19, NULL, 1, 'Reimer', 'Lorenz Christian', 'middle', 1, NULL),
(20, NULL, 1, 'Bunk', 'Boyke', 'middle', 1, 'bob10'),
(21, NULL, 1, 'Overmann', 'Jörg', 'last', 1, NULL),
(22, 6, NULL, 'Khodamoradi', 'Shadi', 'middle', 0, NULL),
(23, 6, NULL, 'Hahnke', 'Richard L.', 'middle', 0, NULL),
(24, 6, NULL, 'Mast', 'Yvonne', 'middle', 0, NULL),
(25, 6, NULL, 'Schumann', 'Peter', 'middle', 0, NULL),
(26, 6, NULL, 'Kämpfer', 'Peter', 'middle', 0, NULL),
(27, 6, NULL, 'Steinert', 'Michael', 'middle', 0, NULL),
(28, 6, NULL, 'Rückert', 'Christian', 'middle', 0, NULL),
(29, 6, NULL, 'Surup', 'Frank', 'middle', 0, NULL),
(30, 6, NULL, 'Rohde', 'Manfred', 'middle', 0, NULL),
(31, 6, NULL, 'Wink', 'Joachim', 'last', 0, NULL),
(32, NULL, 2, 'Koblitz', 'Julia', 'first', 0, 'juk20'),
(33, NULL, 2, 'Bunk', 'Boyke', 'middle', 0, 'bob10'),
(34, NULL, 2, 'Neumann-Schaal', 'Meina', 'middle', 0, 'men17'),
(35, NULL, 2, 'Pommerenke', 'Claudia', 'last', 0, 'cpo14');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `journal`
--

DROP TABLE IF EXISTS `journal`;
CREATE TABLE IF NOT EXISTS `journal` (
  `journal_id` int(11) NOT NULL AUTO_INCREMENT,
  `journal_name` mediumtext NOT NULL,
  `journal_abbr` varchar(255) DEFAULT NULL,
  `impact_factor` float(6,3) DEFAULT NULL,
  PRIMARY KEY (`journal_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `journal`
--

INSERT INTO `journal` (`journal_id`, `journal_name`, `journal_abbr`, `impact_factor`) VALUES
(1, 'Nucleic Acids Research', 'Nucleic Acids Research', NULL),
(2, 'F1000Research', 'F1000Research', NULL),
(3, 'Environmental Microbiology', 'Environmental Microbiology', NULL),
(4, 'Antonie van Leeuwenhoek', 'Antonie van Leeuwenhoek', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `poster`
--

DROP TABLE IF EXISTS `poster`;
CREATE TABLE IF NOT EXISTS `poster` (
  `poster_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` mediumtext NOT NULL,
  `conference` varchar(100) DEFAULT NULL,
  `date_start` date NOT NULL,
  `date_end` date DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `quartal` char(6) NOT NULL,
  PRIMARY KEY (`poster_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `poster`
--

INSERT INTO `poster` (`poster_id`, `title`, `conference`, `date_start`, `date_end`, `location`, `quartal`) VALUES
(1, 'BacMedia: a culture media database and its application for predicting cultivation conditions of so far uncultured microorganisms', 'VAAM 2022', '2021-10-29', '2021-10-29', 'online', '2022Q4');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `publication`
--

DROP TABLE IF EXISTS `publication`;
CREATE TABLE IF NOT EXISTS `publication` (
  `publication_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` mediumtext DEFAULT NULL COMMENT 'TI',
  `journal_id` int(11) DEFAULT NULL COMMENT 'JT',
  `year` smallint(6) DEFAULT NULL,
  `date_publication` date DEFAULT NULL COMMENT 'DP',
  `issue` varchar(20) DEFAULT NULL COMMENT 'IP',
  `pages` varchar(20) DEFAULT NULL COMMENT 'PG',
  `volume` int(11) DEFAULT NULL COMMENT 'VI',
  `doi` varchar(255) DEFAULT NULL COMMENT 'AID',
  `pubmed` int(11) DEFAULT NULL COMMENT 'PMID',
  `type` varchar(20) NOT NULL DEFAULT 'Article',
  `book_title` varchar(255) DEFAULT NULL COMMENT 'BTI',
  `open_access` tinyint(1) DEFAULT NULL,
  `epub` tinyint(1) NOT NULL DEFAULT 0,
  `quartal` char(6) DEFAULT NULL,
  PRIMARY KEY (`publication_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `publication`
--

INSERT INTO `publication` (`publication_id`, `title`, `journal_id`, `year`, `date_publication`, `issue`, `pages`, `volume`, `doi`, `pubmed`, `type`, `book_title`, `open_access`, `epub`, `quartal`) VALUES
(4, 'DSMZCellDive: Diving into high-throughput cell line data', 2, 2022, '2022-04-13', '', '420', 11, '10.12688/f1000research.111175.1', NULL, 'journal-article', '', 1, 1, '2022Q2'),
(2, 'Bac<i>Dive</i> in 2022: the knowledge base for standardized bacterial and archaeal data', 1, 2022, '2021-10-29', 'D1', 'D741-D746', 50, '10.1093/nar/gkab961', NULL, 'journal-article', '', 1, 0, '2022Q1'),
(5, 'New insights into the energy metabolism and taxonomy of            <i>Deferribacteres</i>            revealed by the characterization of a new isolate from a hypersaline microbial mat', 3, 2022, '2022-05-01', '5', '2543-2575', 24, '10.1111/1462-2920.15999', NULL, 'journal-article', '', 0, 0, '2022Q2'),
(6, 'Streptomonospora litoralis sp. nov., a halophilic thiopeptides producer isolated from sand collected at Cuxhaven beach', 4, 2021, '2021-10-01', '10', '1483-1496', 114, '10.1007/s10482-021-01609-4', NULL, 'journal-article', '', 0, 0, '2021Q4');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `quartal`
--

DROP TABLE IF EXISTS `quartal`;
CREATE TABLE IF NOT EXISTS `quartal` (
  `q_id` int(11) NOT NULL,
  `quartal` tinyint(1) NOT NULL,
  `year` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `scientist`
--

DROP TABLE IF EXISTS `scientist`;
CREATE TABLE IF NOT EXISTS `scientist` (
  `user` varchar(6) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `dept` varchar(100) NOT NULL,
  PRIMARY KEY (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `scientist`
--

INSERT INTO `scientist` (`user`, `first_name`, `last_name`, `dept`) VALUES
('juk20', 'Julia', 'Koblitz', 'Bioinf'),
('cpo14', 'Claudia', 'Pommerenke', 'MuTZ'),
('men17', 'Meina', 'Neumann-Schaal', 'Services'),
('bob10', 'Boyke', 'Bunk', 'Bioinf');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
