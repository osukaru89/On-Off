--
-- Deletion of all existing tables
--
DROP TABLE IF EXISTS `php_wol_textmails`;
DROP TABLE IF EXISTS `php_wol_statistics`;
DROP TABLE IF EXISTS `php_wol_invitations`;
DROP TABLE IF EXISTS `php_wol_scheduled_tasks`;
DROP TABLE IF EXISTS `php_wol_tasks`;
DROP TABLE IF EXISTS `php_wol_logger`;
DROP TABLE IF EXISTS `php_wol_events`;
DROP TABLE IF EXISTS `php_wol_hosts_hostgroups`;
DROP TABLE IF EXISTS `php_wol_hostgroups`;
DROP TABLE IF EXISTS `php_wol_permissions`;
DROP TABLE IF EXISTS `php_wol_actions`;
DROP TABLE IF EXISTS `php_wol_users_usergroups`;
DROP TABLE IF EXISTS `php_wol_usergroups`;
DROP TABLE IF EXISTS `php_wol_hostfarms_hosts`;
DROP TABLE IF EXISTS `php_wol_users_hostsfarms`;
DROP TABLE IF EXISTS `php_wol_hostsfarms`;
DROP TABLE IF EXISTS `php_wol_users_hosts`;
DROP TABLE IF EXISTS `php_wol_hosts`;
DROP TABLE IF EXISTS `php_wol_users_roles`;
DROP TABLE IF EXISTS `php_wol_roles`;
DROP TABLE IF EXISTS `php_wol_ldap_users`;
DROP TABLE IF EXISTS `php_wol_users`;

--
-- Table structure for table ``php_wol_users``
--
DROP TABLE IF EXISTS `php_wol_users`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `php_wol_users` (
  `id_user` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `login` varchar(40) NOT NULL,
  `pass` varchar(40) NOT NULL,
  `email` varchar(100) NOT NULL,
  `is_ldap_user` boolean NOT NULL DEFAULT FALSE,  
  `date_insert` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id_user`),
  UNIQUE KEY `login` (`login`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table ``php_wol_ldap_users``
--
DROP TABLE IF EXISTS `php_wol_ldap_users`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `php_wol_ldap_users` (
  `id_ldap_users` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_user` mediumint(8) UNSIGNED NOT NULL,  
  `ldap_uid` varchar(40) NOT NULL,
  PRIMARY KEY  (`id_ldap_users`),
  UNIQUE KEY `id_user` (`id_user`),
  FOREIGN KEY (`id_user`) REFERENCES `php_wol_users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  UNIQUE KEY `ldap_uid` (`ldap_uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table ``php_wol_roles``
--
DROP TABLE IF EXISTS `php_wol_roles`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `php_wol_roles` (
  `id_role` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` enum ('admin', 'user') NOT NULL,
  PRIMARY KEY  (`id_role`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

INSERT INTO php_wol_roles (name) VALUES ('user');
INSERT INTO php_wol_roles (name) VALUES ('admin');

--
-- Table structure for table ``php_wol_users_roles``
--
DROP TABLE IF EXISTS `php_wol_users_roles`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `php_wol_users_roles` (
  `id_user_role` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_role` mediumint(8) UNSIGNED NOT NULL,
  KEY `id_role` (`id_role`),
  FOREIGN KEY (`id_role`) REFERENCES `php_wol_roles` (`id_role`) ON DELETE CASCADE ON UPDATE CASCADE,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  KEY `id_user` (`id_user`),
  FOREIGN KEY (`id_user`) REFERENCES `php_wol_users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY  (`id_user_role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;


--
-- Table structure for table ``php_wol_hosts``
--
DROP TABLE IF EXISTS `php_wol_hosts`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `php_wol_hosts` (
  `id_host` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ip` varchar(16) DEFAULT NULL,
  `mac` varchar(12) NOT NULL,
  `name` varchar(40) NOT NULL,
  `status` mediumint(8) UNSIGNED NOT NULL DEFAULT 1,
  `owner_id` mediumint(8) UNSIGNED DEFAULT NULL,
  KEY `owner_id` (`owner_id`),
  FOREIGN KEY (`owner_id`) REFERENCES `php_wol_users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY  (`id_host`),
  UNIQUE KEY `mac` (`mac`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;


--
-- Table structure for table ``php_wol_users_hosts``
--
DROP TABLE IF EXISTS `php_wol_users_hosts`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `php_wol_users_hosts` (
  `id_user_hosts` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_host` mediumint(8) UNSIGNED NOT NULL,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `hostname_for_user` varchar(40) DEFAULT NULL,
  PRIMARY KEY  (`id_user_hosts`),
  KEY `id_host` (`id_host`),
  KEY `id_user` (`id_user`),
  FOREIGN KEY (`id_host`) REFERENCES `php_wol_hosts` (`id_host`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`id_user`) REFERENCES `php_wol_users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  UNIQUE INDEX (`id_host`, `id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;


--
-- Table structure for table ``php_wol_hostfarms``
--
DROP TABLE IF EXISTS `php_wol_hostsfarms`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `php_wol_hostsfarms` (
  `id_hostsfarm` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `hostfarm_name` varchar(40) NOT NULL,
  `see_all` boolean NOT NULL DEFAULT TRUE,
  PRIMARY KEY (`id_hostsfarm`),
  UNIQUE KEY `hostfarm_name` (`hostfarm_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;


--
-- Table structure for table ``php_wol_users_hostfarms``
--
DROP TABLE IF EXISTS `php_wol_users_hostsfarms`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `php_wol_users_hostsfarms` (
  `id_users_hostsfarm` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `id_hostsfarm` mediumint(8) UNSIGNED NOT NULL,
  PRIMARY KEY  (`id_users_hostsfarm`),
  KEY `id_user` (`id_user`),
  KEY `id_hostsfarm` (`id_hostsfarm`),
  FOREIGN KEY (`id_user`) REFERENCES `php_wol_users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`id_hostsfarm`) REFERENCES `php_wol_hostsfarms` (`id_hostsfarm`) ON DELETE CASCADE ON UPDATE CASCADE,
  UNIQUE INDEX (`id_hostsfarm`, `id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;


--
-- Table structure for table ``php_wol_hostfarms_hosts``
--
DROP TABLE IF EXISTS `php_wol_hostfarms_hosts`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `php_wol_hostfarms_hosts` (
  `id_hostsfarm_hosts` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_host` mediumint(8) UNSIGNED NOT NULL,
  `id_hostsfarm` mediumint(8) UNSIGNED NOT NULL,
  PRIMARY KEY  (`id_hostsfarm_hosts`),
  KEY `id_host` (`id_host`),
  KEY `id_hostsfarm` (`id_hostsfarm`),
  FOREIGN KEY (`id_host`) REFERENCES `php_wol_hosts` (`id_host`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`id_hostsfarm`) REFERENCES `php_wol_hostsfarms` (`id_hostsfarm`) ON DELETE CASCADE ON UPDATE CASCADE,
  UNIQUE INDEX (`id_hostsfarm`, `id_host`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table ``php_wol_usergroups``
--
DROP TABLE IF EXISTS `php_wol_usergroups`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `php_wol_usergroups` (
  `id_usergroup` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `level` mediumint(8) UNSIGNED NOT NULL,  
  PRIMARY KEY  (`id_usergroup`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table ``php_wol_users_usergroups``
--
DROP TABLE IF EXISTS `php_wol_users_usergroups`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `php_wol_users_usergroups` (
  `id_user_usergroup` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_user` mediumint(8) UNSIGNED NOT NULL,
  `id_usergroup` mediumint(8) UNSIGNED NOT NULL,
  PRIMARY KEY  (`id_user_usergroup`),
  FOREIGN KEY (`id_user`) REFERENCES `php_wol_users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`id_usergroup`) REFERENCES `php_wol_usergroups` (`id_usergroup`) ON DELETE CASCADE ON UPDATE CASCADE,
  UNIQUE INDEX (`id_user`, `id_usergroup`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table ``php_wol_hostgroups``
--
DROP TABLE IF EXISTS `php_wol_hostgroups`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `php_wol_hostgroups` (
  `id_hostgroup` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `level` mediumint(8) UNSIGNED NOT NULL, -- to define the 'level' needed to to something on the hostgroup
  PRIMARY KEY  (`id_hostgroup`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table ``php_wol_hosts_hostgroups``
--
DROP TABLE IF EXISTS `php_wol_hosts_hostgroups`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `php_wol_hosts_hostgroups` (
  `id_host_hostgroup` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_host` mediumint(8) UNSIGNED NOT NULL,
  `id_hostgroup` mediumint(8) UNSIGNED NOT NULL,
  PRIMARY KEY  (`id_host_hostgroup`),
  FOREIGN KEY (`id_host`) REFERENCES `php_wol_hosts` (`id_host`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`id_hostgroup`) REFERENCES `php_wol_hostgroups` (`id_hostgroup`) ON DELETE CASCADE ON UPDATE CASCADE,
  UNIQUE INDEX (`id_host`, `id_hostgroup`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table ``php_wol_events``
--
DROP TABLE IF EXISTS `php_wol_events`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `php_wol_events` (
  `id_event` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name_event` varchar(20),
  `can_be_emailed` boolean DEFAULT FALSE,
  PRIMARY KEY  (`id_event`),
  UNIQUE KEY `name_event` (`name_event`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- List of events that can happen :
--
INSERT INTO php_wol_events (name_event) VALUES ('connect');
INSERT INTO php_wol_events (name_event) VALUES ('wakeon');
INSERT INTO php_wol_events (name_event) VALUES ('turnoff');
INSERT INTO php_wol_events (name_event, can_be_emailed) VALUES ('addUser', TRUE);
INSERT INTO php_wol_events (name_event, can_be_emailed) VALUES ('changeEmail', TRUE);
INSERT INTO php_wol_events (name_event, can_be_emailed) VALUES ('changeRole', TRUE);
INSERT INTO php_wol_events (name_event, can_be_emailed) VALUES ('changePassword', TRUE);
INSERT INTO php_wol_events (name_event, can_be_emailed) VALUES ('addHostToUser', TRUE);
INSERT INTO php_wol_events (name_event, can_be_emailed) VALUES ('removeHostToUser', TRUE);
INSERT INTO php_wol_events (name_event) VALUES ('addHost');
INSERT INTO php_wol_events (name_event) VALUES ('changeIP');
INSERT INTO php_wol_events (name_event) VALUES ('changeOwner');
INSERT INTO php_wol_events (name_event) VALUES ('changeMac');
INSERT INTO php_wol_events (name_event, can_be_emailed) VALUES ('deleteUser', TRUE);
INSERT INTO php_wol_events (name_event, can_be_emailed) VALUES ('deleteHost', TRUE);
INSERT INTO php_wol_events (name_event) VALUES ('removeHostFromFarm');
INSERT INTO php_wol_events (name_event, can_be_emailed) VALUES ('removeUserFromFarm', TRUE);
INSERT INTO php_wol_events (name_event, can_be_emailed) VALUES ('addUserInFarm', TRUE);
INSERT INTO php_wol_events (name_event) VALUES ('addHostInFarm');
INSERT INTO php_wol_events (name_event, can_be_emailed) VALUES ('deleteFarm', TRUE);
INSERT INTO php_wol_events (name_event) VALUES ('addFarm');

INSERT INTO php_wol_events (name_event) VALUES ('cronAction');

INSERT INTO php_wol_events (name_event) VALUES ('schedule');

INSERT INTO php_wol_events (name_event, can_be_emailed) VALUES ('addUserToGroup', TRUE);
INSERT INTO php_wol_events (name_event, can_be_emailed) VALUES ('removeUserFromGroup', TRUE);
INSERT INTO php_wol_events (name_event) VALUES ('createUserGroup');
INSERT INTO php_wol_events (name_event, can_be_emailed) VALUES ('deleteUserGroup', TRUE);

INSERT INTO php_wol_events (name_event) VALUES ('addHostToGroup');
INSERT INTO php_wol_events (name_event) VALUES ('removeHostFromGroup');
INSERT INTO php_wol_events (name_event) VALUES ('createHostGroup');
INSERT INTO php_wol_events (name_event) VALUES ('deleteHostGroup');
INSERT INTO php_wol_events (name_event) VALUES ('changepermusergroup');
INSERT INTO php_wol_events (name_event) VALUES ('changepermhostgroup');

INSERT INTO php_wol_events (name_event, can_be_emailed) VALUES ('generatePass', TRUE);
--
-- Table structure for table ``php_wol_logger``
--
DROP TABLE IF EXISTS `php_wol_logger`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `php_wol_logger` (
  `id_log` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_event` mediumint(8) UNSIGNED NOT NULL,
  `date_event` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_author` mediumint(8) UNSIGNED,
  `info_event` varchar(200),
  `success` boolean NOT NULL,
  PRIMARY KEY  (`id_log`),
  FOREIGN KEY (`id_author`) REFERENCES `php_wol_users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`id_event`) REFERENCES `php_wol_events` (`id_event`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;


--
-- Table structure for table ``php_wol_tasks``
--
DROP TABLE IF EXISTS `php_wol_tasks`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `php_wol_tasks` (
  `id_task` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  PRIMARY KEY  (`id_task`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

INSERT INTO php_wol_tasks (name) VALUES ('wakeon');
INSERT INTO php_wol_tasks (name) VALUES ('turnoff');

--
-- Table structure for table ``php_wol_scheduled_tasks``
--
DROP TABLE IF EXISTS `php_wol_scheduled_tasks`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `php_wol_scheduled_tasks` (
  `id_scheduled_task` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `minute` varchar(10),
  `hour` varchar(10),
  `dayOfMonth` varchar(85),
  `month` varchar(50),
  `dayOfWeek` varchar(30),
  `id_task` mediumint(8) UNSIGNED NOT NULL,
  `typeOfSubject` enum ('host', 'group', 'farm') NOT NULL,
  `id_subject` mediumint(8) UNSIGNED NOT NULL, -- Warning : this is a reference on one of three tables ! It can reference host, farm or group.
  PRIMARY KEY  (`id_scheduled_task`),
  FOREIGN KEY (`id_task`) REFERENCES `php_wol_tasks` (`id_task`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table ``php_wol_scheduled_tasks``
--
DROP TABLE IF EXISTS `php_wol_invitations`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `php_wol_invitations` (
  `id_invitation` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name_of_people` varchar(40),
  `name_of_project` varchar(30),
  `hash` varchar(32) NOT NULL,
  `email` varchar(100) NOT NULL,
  `typeOfSubject` enum ('host', 'farm') NOT NULL,
  `id_subject` mediumint(8) UNSIGNED NOT NULL, -- Warning : this is a reference on one of two tables ! It can reference host or farm !
  `date_begin` date NOT NULL,
  `date_end` date NOT NULL,
  PRIMARY KEY  (`id_invitation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

DROP TABLE IF EXISTS `php_wol_statistics`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `php_wol_statistics` (
  `id_statistics` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_host` mediumint(8) UNSIGNED NOT NULL,
  `week_number` mediumint(8) UNSIGNED NOT NULL,
  `Mon` mediumint(8) UNSIGNED DEFAULT 0,
  `Tue` mediumint(8) UNSIGNED DEFAULT 0,
  `Wed` mediumint(8) UNSIGNED DEFAULT 0,
  `Thu` mediumint(8) UNSIGNED DEFAULT 0,
  `Fri` mediumint(8) UNSIGNED DEFAULT 0,
  `Sat` mediumint(8) UNSIGNED DEFAULT 0,
  `Sun` mediumint(8) UNSIGNED DEFAULT 0,
  FOREIGN KEY (`id_host`) REFERENCES `php_wol_hosts` (`id_host`) ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY  (`id_statistics`),
  UNIQUE INDEX (`id_host`, `week_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

DROP TABLE IF EXISTS `php_wol_textmails`;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE `php_wol_textmails` (
  `id_textmails` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_event` mediumint(8) UNSIGNED NOT NULL,  
  `lang` varchar(8) DEFAULT 'EN',
  `sender_mail` varchar(100) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `text` varchar(2000) NOT NULL,
  `token_list` varchar(200) NOT NULL,
  `isActive` boolean DEFAULT FALSE,
  FOREIGN KEY (`id_event`) REFERENCES `php_wol_events` (`id_event`) ON DELETE CASCADE ON UPDATE CASCADE,
  PRIMARY KEY  (`id_textmails`),
  UNIQUE INDEX (`id_event`, `lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
SET character_set_client = @saved_cs_client;

INSERT INTO php_wol_textmails (id_event, lang, sender_mail, subject, text, token_list, isActive) VALUES 
(
	(SELECT id_event FROM php_wol_events where name_event = 'addUser'),
	'EN',
	'',
	'[On/Off] You have been registered',
	'Hello, \n You can login to the application by clicking this link : %URL% \n Your login : %LOGIN% \n Your password : %PASSWORD% \n',
	'%URL% %LOGIN% %PASSWORD%',
	TRUE
);

INSERT INTO php_wol_textmails (id_event, lang, sender_mail, subject, text, token_list, isActive) VALUES 
(
	(SELECT id_event FROM php_wol_events where name_event = 'changeEmail'),
	'EN',
	'',
	'[On/Off] Your email has been changed',
	'Hello %LOGIN%, \n The admin of On/Off has changed your email address. \n Your old address was : %OLDMAIL% \n Your new address will be : %NEWMAIL%  \n If you think it is a mistake, please contact him ! \n',
	'%URL% %LOGIN% %OLDMAIL% %NEWMAIL%',
	TRUE
);

INSERT INTO php_wol_textmails (id_event, lang, sender_mail, subject, text, token_list, isActive) VALUES 
(
	(SELECT id_event FROM php_wol_events where name_event = 'changeRole'),
	'EN',
	'',
	'[On/Off] Your role changed',
	'Hello %LOGIN%, \n The admin of On/Off has changed your role. \n Your new role : " . %NEWROLE%',
	'%URL% %LOGIN% %NEWROLE%',
	TRUE
);

INSERT INTO php_wol_textmails (id_event, lang, sender_mail, subject, text, token_list, isActive) VALUES 
(
	(SELECT id_event FROM php_wol_events where name_event = 'changePassword'),
	'EN',
	'',
	'[On/Off] You have changed your password',
	'Hello %LOGIN%, \n You have successfully changed your password on On/Off application. \n You can now login with this password.',
	'%URL% %LOGIN%',
	TRUE
);

INSERT INTO php_wol_textmails (id_event, lang, sender_mail, subject, text, token_list, isActive) VALUES 
(
	(SELECT id_event FROM php_wol_events where name_event = 'addHostToUser'),
	'EN',
	'',
	'[On/Off] Host added',
	'Hello %LOGIN%, \n The admin of On/Off added the host %HOSTNAME% to your list of hosts. \n You can now power on and turn off it.',
	'%URL% %LOGIN% %HOSTNAME% %MAC%',
	FALSE
);

INSERT INTO php_wol_textmails (id_event, lang, sender_mail, subject, text, token_list, isActive) VALUES 
(
	(SELECT id_event FROM php_wol_events where name_event = 'removeHostToUser'),
	'EN',
	'',
	'[On/Off] Host removed',
	'Hello %LOGIN%, \n The admin of On/Off removed the host %HOSTNAME% from your list of hosts. \n You will not be able to see its status, power on and turn off it.',
	'%URL% %LOGIN% %HOSTNAME% %MAC%',
	FALSE
);

INSERT INTO php_wol_textmails (id_event, lang, sender_mail, subject, text, token_list, isActive) VALUES 
(
	(SELECT id_event FROM php_wol_events where name_event = 'deleteUser'),
	'EN',
	'',
	'[On/Off] Account deleted',
	'"Hello %LOGIN%, \n The admin of On/Off has deleted your account. \n If you think it is a mistake, you can contact him.',
	'%URL% %LOGIN%',
	TRUE
);

INSERT INTO php_wol_textmails (id_event, lang, sender_mail, subject, text, token_list, isActive) VALUES 
(
	(SELECT id_event FROM php_wol_events where name_event = 'deleteHost'),
	'EN',
	'',
	'[On/Off] Host deleted',
	'Hello %LOGIN%, \n The admin of On/Off has deleted the host %HOSTNAME%. \n This host was linked to you, but you will not be able to see his status, wake it on or turn it off.',
	'%URL% %LOGIN% %HOSTNAME%',
	FALSE
);

INSERT INTO php_wol_textmails (id_event, lang, sender_mail, subject, text, token_list, isActive) VALUES 
(
	(SELECT id_event FROM php_wol_events where name_event = 'removeUserFromFarm'),
	'EN',
	'',
	'[On/Off] You have been removed form a farm',
	'Hello %LOGIN%, \n The admin of On/Off has removed you from the farm %FARMNAME%. \n From now, you will not be able to see its hosts, their status, wake them on or turn them off.',
	'%URL% %LOGIN% %FARMNAME%',
	FALSE
);

INSERT INTO php_wol_textmails (id_event, lang, sender_mail, subject, text, token_list, isActive) VALUES 
(
	(SELECT id_event FROM php_wol_events where name_event = 'addUserInFarm'),
	'EN',
	'',
	'[On/Off] You have been added to a farm',
	'Hello %LOGIN%, \n The admin of On/Off has added you to the farm %FARMNAME%. \n From now, you will be able to see its hosts, their status, wake them on or turn them off.',
	'%URL% %LOGIN% %FARMNAME%',
	FALSE
);

INSERT INTO php_wol_textmails (id_event, lang, sender_mail, subject, text, token_list, isActive) VALUES 
(
	(SELECT id_event FROM php_wol_events where name_event = 'deleteFarm'),
	'EN',
	'',
	'[On/Off] A farm has been deleted',
	'Hello %LOGIN%, \n The admin of On/Off deleted the farm %FARMNAME%. \n From now, you will not be able to see its hosts, their status, wake them on or turn them off.',
	'%URL% %LOGIN% %FARMNAME%',
	FALSE
);

INSERT INTO php_wol_textmails (id_event, lang, sender_mail, subject, text, token_list, isActive) VALUES 
(
	(SELECT id_event FROM php_wol_events where name_event = 'addUserToGroup'),
	'EN',
	'',
	'[On/Off] You have been added to a group',
	'Hello %LOGIN%, \n The admin of On/Off has added you to the group %GROUPNAME%. \n From now, you will be able to see its hosts, their status, wake them on or turn them off.',
	'%URL% %LOGIN% %GROUPNAME%',
	FALSE
);

INSERT INTO php_wol_textmails (id_event, lang, sender_mail, subject, text, token_list, isActive) VALUES 
(
	(SELECT id_event FROM php_wol_events where name_event = 'removeUserFromGroup'),
	'EN',
	'',
	'[On/Off] You have been removed form a group',
	'Hello %LOGIN%, \n The admin of On/Off deleted the group %GROUPNAME%. \n From now, you will not be able to see its hosts, their status, wake them on or turn them off.',
	'%URL% %LOGIN% %GROUPNAME%',
	FALSE
);

INSERT INTO php_wol_textmails (id_event, lang, sender_mail, subject, text, token_list, isActive) VALUES 
(
	(SELECT id_event FROM php_wol_events where name_event = 'deleteUserGroup'),
	'EN',
	'',
	'[On/Off] A group has been deleted',
	'Hello %LOGIN%, \n The admin of On/Off deleted the group %GROUPNAME%. \n From now, you will not be able to see its hosts, their status, wake them on or turn them off.',
	'%URL% %LOGIN% %GROUPNAME%',
	FALSE
);

INSERT INTO php_wol_textmails (id_event, lang, sender_mail, subject, text, token_list, isActive) VALUES 
(
	(SELECT id_event FROM php_wol_events where name_event = 'generatePass'),
	'EN',
	'',
	'[On/Off] New pass generated',
	'Hello %LOGIN%, \n A new pass has been generated randomly. \n Your new pass is : %NEWPASS%',
	'%URL% %LOGIN% %NEWPASS%',
	TRUE
);
