SET NAMES UTF8;
SET AUTOCOMMIT=0;
START TRANSACTION;
ALTER TABLE `status` add column type_id int(11) NOT NULL AFTER `id`;
COMMIT;
