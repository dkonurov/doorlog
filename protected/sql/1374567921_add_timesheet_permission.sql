SET NAMES UTF8;
SET AUTOCOMMIT=0;
START TRANSACTION;

INSERT INTO  `savage-db`.`permission` (
`id` ,
`key` ,
`name` ,
`permission_group_id` ,
`department_permission`
)
VALUES (
NULL ,  'watch_timesheet',  'Просмотр и загрузка табеля',  '2',  '0'
);

COMMIT;
