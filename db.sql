CREATE SCHEMA IF NOT EXISTS `bookStorage`
  DEFAULT CHARACTER SET utf8;
USE `bookStorage`;

-- -----------------------------------------------------
-- Table `bookStorage`.`Books`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `bookStorage`.`Books` (
  `id`               INT         NOT NULL AUTO_INCREMENT,
  `name`             VARCHAR(45) NULL,
  `author`           VARCHAR(45) NULL,
  `genre`            VARCHAR(45) NULL,
  `publication_date` DATE        NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB;