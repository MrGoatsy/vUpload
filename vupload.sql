-- Adminer 4.8.0 MySQL 5.5.5-10.3.25-MariaDB-0ubuntu0.20.04.1 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `commentlikes`;
CREATE TABLE `commentlikes` (
  `comment_like_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `u_id` bigint(20) NOT NULL,
  `v_id` bigint(20) NOT NULL,
  `lord` tinyint(1) NOT NULL,
  PRIMARY KEY (`comment_like_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `c_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `u_id` bigint(20) NOT NULL,
  `v_id` bigint(20) NOT NULL,
  `comment` text DEFAULT NULL,
  `c_date` datetime NOT NULL DEFAULT current_timestamp(),
  `c_hidden` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`c_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `history`;
CREATE TABLE `history` (
  `h_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `u_id` bigint(20) DEFAULT NULL,
  `v_id` bigint(20) NOT NULL,
  `v_loggedOut` varchar(255) DEFAULT NULL,
  `lord` tinyint(1) DEFAULT NULL,
  `h_date` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`h_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `m_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `u_id_sender` bigint(20) NOT NULL,
  `u_id_recipient` bigint(20) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `content` varchar(10000) NOT NULL,
  `messageDate` datetime NOT NULL,
  `messageRead` tinyint(1) DEFAULT 0,
  `sender_archived` tinyint(1) DEFAULT 0,
  `recipient_archived` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`m_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `processingqueue`;
CREATE TABLE `processingqueue` (
  `pr_id` int(10) NOT NULL AUTO_INCREMENT,
  `v_id` bigint(20) NOT NULL,
  `pr_busy` tinyint(1) DEFAULT 0,
  `pr_current` float NOT NULL,
  PRIMARY KEY (`pr_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `ranks`;
CREATE TABLE `ranks` (
  `r_id` int(10) NOT NULL AUTO_INCREMENT,
  `rankName` varchar(255) NOT NULL,
  `rankValue` int(10) DEFAULT NULL,
  PRIMARY KEY (`r_id`),
  UNIQUE KEY `rankValue` (`rankValue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `ranks` (`r_id`, `rankName`, `rankValue`) VALUES
(1,	'Admin',	999),
(2,	'Moderator',	950),
(3,	'Premium',	100),
(4,	'User',	1),
(5,	'Banned',	0)
ON DUPLICATE KEY UPDATE `r_id` = VALUES(`r_id`), `rankName` = VALUES(`rankName`), `rankValue` = VALUES(`rankValue`);

DROP TABLE IF EXISTS `reports`;
CREATE TABLE `reports` (
  `rp_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `wn_id` int(11) NOT NULL,
  `v_id` bigint(20) NOT NULL,
  `u_reporter_id` bigint(20) NOT NULL,
  `u_reported_id` bigint(20) NOT NULL,
  `reportDate` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`rp_id`),
  KEY `v_id` (`v_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `subscriptions`;
CREATE TABLE `subscriptions` (
  `sub_id` int(11) NOT NULL AUTO_INCREMENT,
  `u_makerId` bigint(20) DEFAULT NULL,
  `u_followerId` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`sub_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `u_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `joindate` datetime NOT NULL DEFAULT current_timestamp(),
  `website` varchar(255) DEFAULT NULL,
  `u_desc` varchar(10000) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT 'default.png',
  `email` varchar(255) NOT NULL,
  `email_code` varchar(32) NOT NULL,
  `rank` int(10) DEFAULT -1,
  `u_ip` varchar(255) NOT NULL,
  PRIMARY KEY (`u_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `videos`;
CREATE TABLE `videos` (
  `v_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `u_id` bigint(20) NOT NULL,
  `v_uploadtime` datetime NOT NULL DEFAULT current_timestamp(),
  `v_fileName` varchar(500) DEFAULT NULL,
  `v_extension` varchar(5) NOT NULL,
  `v_title` varchar(500) DEFAULT NULL,
  `v_desc` varchar(10000) DEFAULT NULL,
  `v_tags` varchar(500) DEFAULT NULL,
  `v_thumbnail` varchar(255) DEFAULT NULL,
  `v_hidden` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`v_id`),
  FULLTEXT KEY `v_title` (`v_title`,`v_desc`,`v_tags`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `videoservers`;
CREATE TABLE `videoservers` (
  `vs_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `v_id` bigint(20) NOT NULL,
  `v_server` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`vs_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `warningnames`;
CREATE TABLE `warningnames` (
  `wn_id` int(10) NOT NULL AUTO_INCREMENT,
  `warningInfo` varchar(255) DEFAULT NULL,
  `warningPoints` int(10) DEFAULT NULL,
  PRIMARY KEY (`wn_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `warningnames` (`wn_id`, `warningInfo`, `warningPoints`) VALUES
(1,	'Spam or misleading content',	20),
(2,	'Violent or repulsive content',	50),
(3,	'Hateful or abusive content ',	20),
(4,	'Harmful or dangerous acts',	20),
(5,	'Child abuse',	100),
(6,	'Promotes terrorism',	100)
ON DUPLICATE KEY UPDATE `wn_id` = VALUES(`wn_id`), `warningInfo` = VALUES(`warningInfo`), `warningPoints` = VALUES(`warningPoints`);

DROP TABLE IF EXISTS `warnings`;
CREATE TABLE `warnings` (
  `w_id` int(10) NOT NULL AUTO_INCREMENT,
  `wn_id` int(11) NOT NULL,
  `u_reporter_id` int(11) NOT NULL,
  `u_reported_id` bigint(20) NOT NULL,
  `warningDate` datetime NOT NULL DEFAULT current_timestamp(),
  `archived` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`w_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- 2021-02-24 00:32:42
