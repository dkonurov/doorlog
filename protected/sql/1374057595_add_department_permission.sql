SET NAMES UTF8;
SET AUTOCOMMIT=0;
START TRANSACTION;

ALTER TABLE `permission` ADD `department_permission` INT( 11 ) NOT NULL 

UPDATE `savage-db`.`permission` SET `department_permission` = '1' WHERE `permission`.`id` = 18;

UPDATE `savage-db`.`permission` SET `department_permission` = '1' WHERE `permission`.`id` = 19;

UPDATE `savage-db`.`permission` SET `department_permission` = '1' WHERE `permission`.`id` = 25;


CREATE TABLE `savage-db`.`user_department_permission` (
`user_id` INT( 11 ) UNSIGNED NOT NULL ,
`department_id` INT( 11 ) UNSIGNED NOT NULL ,
`permission_id` INT( 11 ) UNSIGNED NOT NULL ,
PRIMARY KEY ( `user_id` , `department_id` , `permission_id` )
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

ALTER TABLE `user_department_permission` ADD FOREIGN KEY ( `user_id` ) REFERENCES `savage-db`.`user` (
`id`
) ON DELETE CASCADE ON UPDATE CASCADE ;

ALTER TABLE `user_department_permission` ADD FOREIGN KEY ( `department_id` ) REFERENCES `savage-db`.`department` (
`id`
) ON DELETE CASCADE ON UPDATE CASCADE ;

ALTER TABLE `user_department_permission` ADD FOREIGN KEY ( `permission_id` ) REFERENCES `savage-db`.`permission` (
`id`
) ON DELETE CASCADE ON UPDATE CASCADE ;


ALTER TABLE  `department` DROP  `chief_id`;

COMMIT;
