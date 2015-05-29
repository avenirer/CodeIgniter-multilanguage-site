-- Adminer 4.1.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE DATABASE `ciapp` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `ciapp`;

CREATE TABLE `banned` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `deleted_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `banned` (`id`, `ip`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`, `deleted_by`) VALUES
(2,	'100.10.25.40',	'2015-05-19 16:37:54',	'0000-00-00 00:00:00',	'0000-00-00 00:00:00',	1,	0,	0),
(3,	'91.220.13.30',	'2015-05-19 16:38:04',	'0000-00-00 00:00:00',	'0000-00-00 00:00:00',	1,	0,	0);

CREATE TABLE `ci_sessions` (
  `id` varchar(40) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `timestamp` int(10) unsigned NOT NULL DEFAULT '0',
  `data` blob NOT NULL,
  PRIMARY KEY (`id`,`ip_address`),
  KEY `ci_sessions_timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `contents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_type` varchar(100) NOT NULL,
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0',
  `featured_image` varchar(255) NOT NULL,
  `order` int(4) unsigned NOT NULL,
  `published` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `published_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `deleted_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE `content_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_id` int(11) NOT NULL,
  `language_slug` varchar(5) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `short_title` varchar(255) DEFAULT NULL,
  `teaser` mediumtext NOT NULL,
  `content` longtext,
  `page_title` varchar(100) DEFAULT NULL,
  `page_description` varchar(170) DEFAULT NULL,
  `page_keywords` varchar(255) DEFAULT NULL,
  `rake` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `deleted_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page_id` (`content_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE `dictionary` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `word` varchar(255) NOT NULL,
  `language_slug` varchar(10) NOT NULL,
  `noise` tinyint(1) NOT NULL DEFAULT '0',
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `word` (`word`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `groups` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `groups` (`id`, `name`, `description`) VALUES
(1,	'admin',	'Administrators'),
(2,	'members',	'Members');

CREATE TABLE `images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_type` varchar(100) NOT NULL,
  `content_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `file` varchar(255) NOT NULL,
  `width` int(5) NOT NULL DEFAULT '0',
  `height` int(5) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `deleted_by` int(11) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `keyphrases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_type` varchar(255) NOT NULL,
  `content_id` int(10) unsigned NOT NULL,
  `phrase_id` int(10) unsigned NOT NULL,
  `language_slug` varchar(10) NOT NULL,
  `score` decimal(10,2) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `keywords` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_type` varchar(255) NOT NULL,
  `content_id` int(10) unsigned NOT NULL,
  `language_slug` varchar(10) NOT NULL,
  `word_id` int(10) unsigned NOT NULL,
  `appearances` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language_name` varchar(100) NOT NULL,
  `slug` varchar(10) NOT NULL,
  `language_directory` varchar(100) NOT NULL,
  `language_code` varchar(20) DEFAULT NULL,
  `default` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `languages` (`id`, `language_name`, `slug`, `language_directory`, `language_code`, `default`) VALUES
(1,	'Română',	'ro',	'romanian',	'ro_RO',	1),
(8,	'English',	'en',	'english',	'en_US',	0);

CREATE TABLE `login_attempts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(15) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `deleted_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `menus` (`id`, `title`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`, `deleted_by`) VALUES
(2,	'top-menu',	'2015-05-04 12:25:23',	NULL,	NULL,	1,	0,	0);

CREATE TABLE `menu_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) unsigned NOT NULL,
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0',
  `order` int(4) unsigned NOT NULL,
  `styling` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `deleted_by` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `menu_items` (`id`, `menu_id`, `parent_id`, `order`, `styling`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`, `deleted_by`) VALUES
(3,	2,	0,	0,	NULL,	'2015-05-05 09:53:14',	'2015-05-05 14:29:05',	NULL,	1,	1,	0),
(4,	2,	0,	0,	NULL,	'2015-05-05 09:53:40',	'2015-05-05 10:45:37',	NULL,	1,	1,	0),
(5,	2,	4,	0,	NULL,	'2015-05-05 14:05:29',	'2015-05-05 14:05:29',	NULL,	1,	1,	0);

CREATE TABLE `menu_item_translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `language_slug` varchar(5) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `absolute_path` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `deleted_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `menu_item_translations` (`id`, `item_id`, `language_slug`, `title`, `url`, `absolute_path`, `created_at`, `updated_at`, `deleted_at`, `created_by`, `updated_by`, `deleted_by`) VALUES
(8,	3,	'en',	'First item',	'#',	0,	'2015-05-05 10:45:28',	NULL,	NULL,	1,	0,	0),
(6,	3,	'ro',	'Primul item',	'#',	1,	'2015-05-05 09:53:14',	'2015-05-05 14:29:05',	NULL,	1,	1,	0),
(7,	4,	'ro',	'Al doilea item',	'#',	0,	'2015-05-05 09:53:40',	'2015-05-05 10:45:14',	NULL,	1,	1,	0),
(9,	4,	'en',	'Second item',	'#',	0,	'2015-05-05 10:45:37',	NULL,	NULL,	1,	0,	0),
(10,	5,	'ro',	'Un alt item',	'#',	0,	'2015-05-05 14:05:29',	NULL,	NULL,	1,	0,	0);

CREATE TABLE `phrases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phrase` varchar(255) NOT NULL,
  `language_slug` varchar(10) NOT NULL,
  `last_check` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



CREATE TABLE `slugs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_type` varchar(150) NOT NULL,
  `content_id` int(11) NOT NULL,
  `translation_id` int(11) NOT NULL,
  `language_slug` varchar(5) NOT NULL,
  `url` varchar(255) NOT NULL,
  `redirect` int(11) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `updated_by` int(11) NOT NULL,
  `deleted_by` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `url` (`url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(15) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `salt` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `activation_code` varchar(40) DEFAULT NULL,
  `forgotten_password_code` varchar(40) DEFAULT NULL,
  `forgotten_password_time` int(11) unsigned DEFAULT NULL,
  `remember_code` varchar(40) DEFAULT NULL,
  `created_on` int(11) unsigned NOT NULL,
  `last_login` int(11) unsigned DEFAULT NULL,
  `active` tinyint(1) unsigned DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `users` (`id`, `ip_address`, `username`, `password`, `salt`, `email`, `activation_code`, `forgotten_password_code`, `forgotten_password_time`, `remember_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `company`, `phone`) VALUES
(1,	'127.0.0.1',	'administrator',	'$2y$08$G0h47xFzvBDD3DjwWD13XeCfwGuZgqtSodh5ARhDJLLWPRv0jSgfG',	'',	'admin@admin.com',	'',	NULL,	NULL,	NULL,	1268889823,	1432619830,	1,	'Admin',	'istrator',	'ADMIN',	'0');

CREATE TABLE `users_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `group_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_users_groups` (`user_id`,`group_id`),
  KEY `fk_users_groups_users1_idx` (`user_id`),
  KEY `fk_users_groups_groups1_idx` (`group_id`),
  CONSTRAINT `fk_users_groups_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `users_groups_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `users_groups` (`id`, `user_id`, `group_id`) VALUES
(3,	1,	1);

CREATE TABLE `website` (
  `title` varchar(255) NOT NULL,
  `page_title` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `admin_email` varchar(200) NOT NULL,
  `contact_email` varchar(200) NOT NULL,
  `modified_by` varchar(200) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `website` (`title`, `page_title`, `status`, `admin_email`, `contact_email`, `modified_by`) VALUES
('CI site',	'CI site',	1,	'avenir.ro@gmail.com',	'avenir.ro@gmail.com',	'1');

-- 2015-05-26 13:22:33
