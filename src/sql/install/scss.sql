DELIMITER ;
CREATE TABLE IF NOT EXISTS `scss` (
  `filename` varchar(36) NOT NULL,
  `content` longtext NOT NULL,
  PRIMARY KEY (`filename`)
) ;