/*
SQLyog Ultimate v11.31 (32 bit)
MySQL - 10.1.21-MariaDB : Database - wtc
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `blocks` */

DROP TABLE IF EXISTS `blocks`;

CREATE TABLE `blocks` (
  `number` int(10) unsigned NOT NULL,
  `timestamp` bigint(20) DEFAULT NULL,
  `hash` varchar(64) DEFAULT NULL,
  `miner` varchar(50) DEFAULT NULL,
  `data` text,
  `data_readable` binary(1) DEFAULT NULL,
  `nonce` varchar(100) DEFAULT NULL,
  `size` int(10) DEFAULT NULL,
  `checked` tinyint(1) DEFAULT '0',
  `comment` text,
  PRIMARY KEY (`number`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `pools` */

DROP TABLE IF EXISTS `pools`;

CREATE TABLE `pools` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` timestamp NULL DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `miner` varchar(50) DEFAULT NULL,
  `expression` varchar(100) DEFAULT NULL,
  `color` varchar(10) DEFAULT NULL,
  `comment` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
