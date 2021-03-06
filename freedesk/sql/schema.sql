-- MySQL dump 10.13  Distrib 5.5.24, for Linux (i686)
--
-- Host: localhost    Database: freedesk
-- ------------------------------------------------------
-- Server version	5.5.24

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `customer`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer` (
  `customerid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `firstname` varchar(254) NOT NULL,
  `lastname` varchar(254) NOT NULL,
  `email` varchar(254) NOT NULL,
  `username` varchar(254) NOT NULL,
  `password` varchar(254) NOT NULL,
  PRIMARY KEY (`customerid`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email` (
  `accountid` mediumint(9) NOT NULL AUTO_INCREMENT,
  `name` varchar(254) NOT NULL,
  `host` varchar(254) NOT NULL,
  `from` varchar(254) NOT NULL,
  `fromname` varchar(254) NOT NULL,
  `wordwrap` int(11) NOT NULL,
  `auth` int(11) NOT NULL,
  `username` varchar(254) NOT NULL,
  `password` varchar(254) NOT NULL,
  `smtpsec` varchar(128) NOT NULL,
  PRIMARY KEY (`accountid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `permgroup`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permgroup` (
  `permgroupid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `groupname` varchar(254) NOT NULL,
  PRIMARY KEY (`permgroupid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `permissions`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
  `permissionid` bigint(20) NOT NULL AUTO_INCREMENT,
  `permissiontype` varchar(16) NOT NULL,
  `permission` varchar(254) NOT NULL,
  `usergroupid` varchar(254) NOT NULL,
  `allowed` tinyint(4) NOT NULL,
  PRIMARY KEY (`permissionid`),
  KEY `permissiontype` (`permissiontype`,`permission`,`usergroupid`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `plugins`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `plugins` (
  `pluginid` bigint(20) NOT NULL AUTO_INCREMENT,
  `plugin` varchar(254) NOT NULL,
  `active` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`pluginid`),
  KEY `active` (`active`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `priority`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `priority` (
  `priorityid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `priorityname` varchar(128) NOT NULL,
  `resolutionsla` bigint(20) NOT NULL,
  `schedule` int(11) NOT NULL,
  PRIMARY KEY (`priorityid`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `request`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `request` (
  `requestid` bigint(20) NOT NULL AUTO_INCREMENT,
  `customer` bigint(20) NOT NULL,
  `assignteam` bigint(20) NOT NULL,
  `assignuser` varchar(254) NOT NULL,
  `class` int(11) NOT NULL,
  `openeddt` datetime NOT NULL,
  `status` int(11) NOT NULL,
  `priority` int(11) NOT NULL,
  `closeddt` datetime NOT NULL,
  PRIMARY KEY (`requestid`),
  KEY `customer` (`customer`,`assignteam`,`assignuser`,`status`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `requestclass`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `requestclass` (
  `classid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `classname` varchar(254) NOT NULL,
  `classclass` varchar(254) NOT NULL,
  PRIMARY KEY (`classid`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `session`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session` (
  `session_id` varchar(254) NOT NULL,
  `username` varchar(254) NOT NULL,
  `sessiontype` int(11) NOT NULL DEFAULT '-1',
  `created_dt` datetime NOT NULL,
  `updated_dt` datetime NOT NULL,
  `expires_dt` datetime NOT NULL,
  `realname` varchar(254) NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `status`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `status` (
  `status` int(11) NOT NULL,
  `description` varchar(254) NOT NULL,
  PRIMARY KEY (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sysconfig`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sysconfig` (
  `sc_option` varchar(254) NOT NULL,
  `sc_value` varchar(254) NOT NULL,
  PRIMARY KEY (`sc_option`),
  UNIQUE KEY `sc_option` (`sc_option`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `syslog`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `syslog` (
  `event_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_dt` datetime NOT NULL,
  `event` varchar(254) NOT NULL,
  `event_class` varchar(128) NOT NULL,
  `event_type` varchar(128) NOT NULL,
  `event_level` int(11) NOT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=InnoDB AUTO_INCREMENT=41253 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `team`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `team` (
  `teamid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `teamname` varchar(254) NOT NULL,
  PRIMARY KEY (`teamid`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `teamuserlink`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `teamuserlink` (
  `linkid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `teamid` bigint(20) unsigned NOT NULL,
  `username` varchar(254) NOT NULL,
  PRIMARY KEY (`linkid`),
  KEY `teamid` (`teamid`,`username`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `templates`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `templates` (
  `templateid` varchar(128) NOT NULL,
  `subject` text NOT NULL,
  `body` text NOT NULL,
  PRIMARY KEY (`templateid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `update`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `update` (
  `updateid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `requestid` bigint(20) unsigned NOT NULL,
  `update` text NOT NULL,
  `public` tinyint(4) NOT NULL,
  `updateby` varchar(254) NOT NULL,
  `updatedt` datetime NOT NULL,
  PRIMARY KEY (`updateid`),
  KEY `requestid` (`requestid`)
) ENGINE=InnoDB AUTO_INCREMENT=150 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `username` varchar(254) NOT NULL,
  `password` varchar(254) NOT NULL,
  `realname` varchar(254) NOT NULL,
  `email` varchar(254) NOT NULL,
  `permgroup` bigint(20) unsigned NOT NULL,
  `authtype` varchar(254) NOT NULL,
  `sparefield0` varchar(254) NOT NULL,
  `sparefield1` varchar(254) NOT NULL,
  `sparefield2` varchar(254) NOT NULL,
  `sparefield3` varchar(254) NOT NULL,
  `sparefield4` varchar(254) NOT NULL,
  `sparefield5` varchar(254) NOT NULL,
  `sparefield6` varchar(254) NOT NULL,
  `sparefield7` varchar(254) NOT NULL,
  `sparefield8` varchar(254) NOT NULL,
  `sparefield9` varchar(254) NOT NULL,
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `vis_country`
--

/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vis_country` (
  `country` char(2) NOT NULL DEFAULT '',
  `country_desc` varchar(120) NOT NULL DEFAULT '',
  `lat` float DEFAULT NULL,
  `long` float DEFAULT NULL,
  KEY `country_desc` (`country_desc`),
  KEY `country` (`country`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-08-22  1:31:09
