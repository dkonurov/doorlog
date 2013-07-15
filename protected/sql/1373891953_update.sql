SET NAMES UTF8;
SET AUTOCOMMIT=0;
START TRANSACTION;
ALTER TABLE `user` add column first_name varchar(100) NOT NULL AFTER `id`;
ALTER TABLE `user` add column second_name varchar(100) NOT NULL AFTER `first_name`;
ALTER TABLE `user` add column middle_name varchar(100) NOT NULL AFTER `second_name`;
COMMIT;
