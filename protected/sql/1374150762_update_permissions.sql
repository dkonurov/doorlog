SET NAMES UTF8;
SET AUTOCOMMIT=0;
START TRANSACTION;
UPDATE `permission` SET `name` = 'Просмотр отчетов по посещаемости' WHERE `id` =19; 
UPDATE `permission` SET `permission_group_id` = '3' WHERE `id` =18;

DELETE FROM `permission` WHERE `permission_group_id` = 8;
DELETE FROM `permission_group` WHERE `id` = 8;

INSERT INTO `permission` 
(`key`, `name`, `permission_group_id`)
VALUES 
('weekend_edit', 'Выходные дни', 2);

COMMIT;

