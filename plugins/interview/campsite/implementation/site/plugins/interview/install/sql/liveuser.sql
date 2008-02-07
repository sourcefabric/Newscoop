-- Setup additional rights

INSERT INTO `liveuser_rights`
SET right_id = (SELECT (id + 1) FROM `liveuser_rights_right_id_seq`),
    area_id = 0,
    right_define_name = 'plugin_interview_notify',
    has_implied = 1 ;

UPDATE `liveuser_rights_right_id_seq`
SET id = (id + 1);


INSERT INTO `liveuser_rights`
SET right_id = (SELECT (id + 1) FROM `liveuser_rights_right_id_seq`),
    area_id = 0,
    right_define_name = 'plugin_interview_guest',
    has_implied = 1 ;

UPDATE `liveuser_rights_right_id_seq`
SET id = (id + 1);

INSERT INTO `liveuser_rights`
SET right_id = (SELECT (id + 1) FROM `liveuser_rights_right_id_seq`),
    area_id = 0,
    right_define_name = 'plugin_interview_moderator',
    has_implied = 1 ;

UPDATE `liveuser_rights_right_id_seq`
SET id = (id + 1);


INSERT INTO `liveuser_rights`
SET right_id = (SELECT (id + 1) FROM `liveuser_rights_right_id_seq`),
    area_id = 0,
    right_define_name = 'plugin_interview_admin',
    has_implied = 1 ;

UPDATE `liveuser_rights_right_id_seq`
SET id = (id + 1);

