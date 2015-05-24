-- phpMyAdmin SQL Dump
-- version 3.1.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generato il: 08 mag, 2015 at 04:35 PM
-- Versione MySQL: 5.1.30
-- Versione PHP: 5.2.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `randaps`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `photos`
--

CREATE TABLE IF NOT EXISTS `photos` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `File` varchar(50) NOT NULL,
  `DateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Social` tinyint(1) NOT NULL,
  `eMail` varchar(100) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dump dei dati per la tabella `photos`
--


-- --------------------------------------------------------

--
-- Struttura della tabella `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `Tag` varchar(100) NOT NULL,
  `Value` text NOT NULL,
  PRIMARY KEY (`Tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dump dei dati per la tabella `settings`
--

INSERT INTO `settings` (`Tag`, `Value`) VALUES
('email_customobject', 'Foto da RandA PhotoSharing!'),
('email_object', '#photofb #TW'),
('email_body', '<p style="text-align: center;"><span style="font-size: 14pt; color: #ff0000;"><strong>Grazie per aver utilizzato RandA Photo Sharing!</strong></span></p>'),
('logo_x', '750'),
('logo_y', '640'),
('overlay_x', '10'),
('overlay_y', '420'),
('photo_cloud', '0'),
('photo_email', '0'),
('photo_save', '1'),
('photo_social', '0'),
('result_x', '110'),
('result_y', '555'),
('sender_email', ''),
('standby', '0'),
('text_email', 'Vuoi ricevere la foto per eMail assieme a fantastiche offerte?'),
('text_end', 'Premi OK per concludere'),
('text_measurement', 'Misurazione in corso: mani sui poli!'),
('text_no', 'No'),
('text_photo', 'Cheese!'),
('text_wait', 'Attendi! Sto elaborando...'),
('text_preview', 'Seleziona gli effetti e premi OK per confermare'),
('text_social', 'Vuoi caricare la foto sui nostri social networks?'),
('text_start', 'Premi OK per iniziare!'),
('text_yes', 'Si'),
('theme', 'maker');

-- --------------------------------------------------------

--
-- Struttura della tabella `themes`
--

CREATE TABLE IF NOT EXISTS `themes` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Value` varchar(50) NOT NULL,
  `Description` varchar(50) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Dump dei dati per la tabella `themes`
--

INSERT INTO `themes` (`ID`, `Value`, `Description`) VALUES
(7, 'maker', 'Maker');

-- --------------------------------------------------------

--
-- Struttura della tabella `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `User` varchar(50) NOT NULL,
  `Password` varchar(50) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dump dei dati per la tabella `users`
--

INSERT INTO `users` (`ID`, `User`, `Password`) VALUES
(1, 'admin', 'afb750403bd78be6a56ea521647c4692');
