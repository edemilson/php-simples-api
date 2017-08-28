/*
SQLyog Community v12.2.4 (32 bit)
MySQL - 5.7.19-0ubuntu0.16.04.1 : Database - easyapi
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`easyapi` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `easyapi`;

/*Table structure for table `cliente` */

DROP TABLE IF EXISTS `cliente`;

CREATE TABLE `cliente` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(45) DEFAULT NULL,
  `cidade` varchar(15) DEFAULT NULL,
  `idade` int(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

/*Data for the table `cliente` */

insert  into `cliente`(`id`,`nome`,`cidade`,`idade`) values 
(4,'Cachorro Loco','Garca',15),
(5,'Cachorrão','Garca',15);

/*Table structure for table `oauth_access_tokens` */

DROP TABLE IF EXISTS `oauth_access_tokens`;

CREATE TABLE `oauth_access_tokens` (
  `access_token` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` varchar(80) DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `scope` varchar(4000) DEFAULT NULL,
  PRIMARY KEY (`access_token`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `oauth_access_tokens` */

insert  into `oauth_access_tokens`(`access_token`,`client_id`,`user_id`,`expires`,`scope`) values 
('1419a69c49cf2c2d85d4281cc8e871a020b56295','testclient','edemilson','2017-08-28 12:43:54','app'),
('375969e4457e36e52cf1986ac57a7bbfe4ef3d7a','testclient',NULL,'2017-08-28 12:07:14',NULL),
('38f1ef214b6133d0e1e10582aa15c03d3b9b7a2b','testclient','edemilson','2017-08-28 12:30:46','app'),
('47e1022e5bc9f18ceb83f72edaae91cdf6320004','testclient','edemilson','2017-08-28 12:30:53','app'),
('5e59236c44479c8fe37bde0a1e366981ec8582fc','testclient','edemilson','2017-08-28 12:43:47','app'),
('61849dadac62c82a5d00558b395af57b3f8bcd2b','testclient',NULL,'2017-08-28 12:43:06',NULL),
('6d3fd709fffe6e89d426c7bce5c5142d2a343778','testclient','edemilson','2017-08-28 15:18:28','app'),
('6eed53108e9ea12bcf22fad2225a0c08d1d791e2','testclient','edemilson','2017-08-28 12:28:55','app'),
('90153330b9dfedb46325453e0375c2af6601db0d','testclient',NULL,'2017-08-28 12:06:39',NULL),
('90a6b79e3151bd0784854c5f26e6351b22140e4b','testclient','edemilson','2017-08-28 12:23:27','app'),
('9f51fd0603b9a69734c0643b800ee3326cf2e65d','testclient','edemilson','2017-08-28 12:28:45','app'),
('ad7b2a154f267b6a693ec15f530aa131f7d179cd','testclient','edemilson','2017-08-28 12:59:21','app'),
('b660f39f384f79dc49083938e3f79dfefd998c5a','testclient','edemilson','2017-08-28 12:42:11','app'),
('b696a5ca3685d9d41194bbbf2fae21a5569681df','testclient','edemilson','2017-08-28 12:31:33','app'),
('b9339c2202791e36f7c290f2e1cd9e22ef088331','testclient',NULL,'2017-08-28 12:10:27',NULL),
('c30b7a7bbc46bca8a42293bc78b1c4da196ed531','testclient','edemilson','2017-08-28 12:31:38','app'),
('d880ebfa5441e5a5a68a4a41ec95c0c2027cff6d','testclient','edemilson','2017-08-28 12:27:35','app'),
('d9304ab7cf1fe964b9aa2c8a1f338a6953c295ac','testclient','edemilson','2017-08-28 12:50:54','app'),
('e1d746dd07765d3426654d2f825e0ba19809ca81','testclient',NULL,'2017-08-28 12:43:33',NULL);

/*Table structure for table `oauth_authorization_codes` */

DROP TABLE IF EXISTS `oauth_authorization_codes`;

CREATE TABLE `oauth_authorization_codes` (
  `authorization_code` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` varchar(80) DEFAULT NULL,
  `redirect_uri` varchar(2000) DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `scope` varchar(4000) DEFAULT NULL,
  `id_token` varchar(1000) DEFAULT NULL,
  PRIMARY KEY (`authorization_code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `oauth_authorization_codes` */

/*Table structure for table `oauth_clients` */

DROP TABLE IF EXISTS `oauth_clients`;

CREATE TABLE `oauth_clients` (
  `client_id` varchar(80) NOT NULL,
  `client_secret` varchar(80) DEFAULT NULL,
  `redirect_uri` varchar(2000) DEFAULT NULL,
  `grant_types` varchar(80) DEFAULT NULL,
  `scope` varchar(4000) DEFAULT NULL,
  `user_id` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `oauth_clients` */

insert  into `oauth_clients`(`client_id`,`client_secret`,`redirect_uri`,`grant_types`,`scope`,`user_id`) values 
('testclient','testpass','http://fake/',NULL,NULL,NULL);

/*Table structure for table `oauth_jwt` */

DROP TABLE IF EXISTS `oauth_jwt`;

CREATE TABLE `oauth_jwt` (
  `client_id` varchar(80) NOT NULL,
  `subject` varchar(80) DEFAULT NULL,
  `public_key` varchar(2000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `oauth_jwt` */

/*Table structure for table `oauth_refresh_tokens` */

DROP TABLE IF EXISTS `oauth_refresh_tokens`;

CREATE TABLE `oauth_refresh_tokens` (
  `refresh_token` varchar(40) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `user_id` varchar(80) DEFAULT NULL,
  `expires` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `scope` varchar(4000) DEFAULT NULL,
  PRIMARY KEY (`refresh_token`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `oauth_refresh_tokens` */

insert  into `oauth_refresh_tokens`(`refresh_token`,`client_id`,`user_id`,`expires`,`scope`) values 
('6b63b8969d02c27be64e566b3fa4b750dd464006','testclient','edemilson','2017-09-11 11:59:21','app'),
('7f0ff26347ffcf99538d8fe991b516352e9ca484','testclient','edemilson','2017-09-11 14:18:28','app'),
('a75cc2264f9be1ccf7a6f47cb21948c8ded9bb98','testclient','edemilson','2017-09-11 11:42:11','app'),
('b4123f9fb099aee9b8d50577ad85cd525345d8ac','testclient','edemilson','2017-09-11 11:43:54','app'),
('cae691ad4f550e9114329bdaf81ee5b0c24dc149','testclient','edemilson','2017-09-11 11:50:54','app'),
('cdbcb0fde2a48054e8d29618be84ea99c3a47566','testclient','edemilson','2017-09-11 11:31:38','app');

/*Table structure for table `oauth_scopes` */

DROP TABLE IF EXISTS `oauth_scopes`;

CREATE TABLE `oauth_scopes` (
  `scope` varchar(80) NOT NULL,
  `is_default` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`scope`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `oauth_scopes` */

/*Table structure for table `oauth_users` */

DROP TABLE IF EXISTS `oauth_users`;

CREATE TABLE `oauth_users` (
  `username` varchar(80) DEFAULT NULL,
  `password` varchar(80) DEFAULT NULL,
  `first_name` varchar(80) DEFAULT NULL,
  `last_name` varchar(80) DEFAULT NULL,
  `email` varchar(80) DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT NULL,
  `scope` varchar(4000) DEFAULT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `oauth_users` */

insert  into `oauth_users`(`username`,`password`,`first_name`,`last_name`,`email`,`email_verified`,`scope`,`id`) values 
('edemilson','2e6f9b0d5885b6010f9167787445617f553a735f','Edemilson','Goncalves','edemilson@fulltime.com.br',1,'app',2);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
