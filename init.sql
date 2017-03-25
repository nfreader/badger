-- Create syntax for TABLE 'bg_ability'
CREATE TABLE `bg_ability` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `org` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create syntax for TABLE 'bg_app'
CREATE TABLE `bg_app` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `setting` varchar(32) DEFAULT NULL,
  `value` longtext,
  `autoload` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create syntax for TABLE 'bg_member_ability'
CREATE TABLE `bg_member_ability` (
  `member` int(11) NOT NULL,
  `ability` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create syntax for TABLE 'bg_org_members'
CREATE TABLE `bg_org_members` (
  `org` int(11) NOT NULL DEFAULT '0',
  `user` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`org`,`user`),
  UNIQUE KEY `org` (`org`,`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create syntax for TABLE 'bg_organization'
CREATE TABLE `bg_organization` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create syntax for TABLE 'bg_team'
CREATE TABLE `bg_team` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `org` int(11) NOT NULL,
  `name` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create syntax for TABLE 'bg_team_ability'
CREATE TABLE `bg_team_ability` (
  `team` int(11) NOT NULL,
  `ability` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create syntax for TABLE 'bg_user'
CREATE TABLE `bg_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) DEFAULT NULL,
  `password` varchar(256) DEFAULT NULL,
  `email` varchar(64) DEFAULT NULL,
  `rank` enum('SA','A','M') NOT NULL DEFAULT 'M',
  `name` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;