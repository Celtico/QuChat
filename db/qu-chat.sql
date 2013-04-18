CREATE TABLE IF NOT EXISTS `qu-chat` (
  `id_chat` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `id_user_parent` int(11) DEFAULT NULL,
  `id_resource` int(11) DEFAULT NULL,
  `id_resource_parent` int(11) DEFAULT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `name` text COLLATE utf8_unicode_ci,
  `name_parent` text COLLATE utf8_unicode_ci,
  `message` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id_chat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;