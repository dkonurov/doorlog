START TRANSACTION;
ALTER TABLE `users_statuses` add column time int(11); 
DELETE FROM `status`;
INSERT INTO `status` VALUES ( '1', 'Отгул', '8');
INSERT INTO `stasus` VALUES ( '2', 'Болел', '8');
INSERT INTO `stasus` VALUES ( '3', 'Командировка', '8');
INSERT INTO `stasus` VALUES ( '4', 'Из дома', '8');
INSERT INTO `stasus` VALUES ( '5', 'В другом офисе', '8');
INSERT INTO `stasus` VALUES ( '6', 'За свой счёт', '8');
COMMIT;

