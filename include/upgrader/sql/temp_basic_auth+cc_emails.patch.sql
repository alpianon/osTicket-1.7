/**
 * @version v1.7
 *
 * @schema d5339caebcfbdcca7d57be5b17f804ee
 */



CREATE TABLE IF NOT EXISTS `ost_ticket_cc_emails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `opted_out` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `ost_client_passwords` (
  `email` varchar(150) NOT NULL,
  `password` varchar(32) NOT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
