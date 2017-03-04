----------------------------------------------
-- Adding freilds for Leave task and conatct person
----------------------------------------------
ALTER TABLE `ohrm_leave_request_comment` 
ADD `task` VARCHAR(255) NULL COMMENT 'Employee pending tasks during leave period' , 
ADD `alternate_contact_person` VARCHAR(100) NULL COMMENT 'alterante contact person for tasks' ;

/*
ALTER TABLE `ohrm_leave_request_comment` CHANGE `ulternate_contact_person` `alternate_contact_person` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'ulterante contact person for tasks';
*/