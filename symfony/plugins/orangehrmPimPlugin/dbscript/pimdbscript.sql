/*
set permison for admin only and supervisor to add work experiance 
*/
UPDATE `perennialhrm_mysql`.`ohrm_user_role_data_group` SET `can_create` = '0', `can_update` = '0', `can_delete` = '0' WHERE `ohrm_user_role_data_group`.`id` = 71;
ALTER TABLE `hs_hr_employee` CHANGE `emp_other_id` `emp_anniversary_date` DATE NULL DEFAULT NULL;
ALTER TABLE `hs_hr_employee` ADD `payment_mode` VARCHAR(25) NOT NULL AFTER `next_appraisal_date`;
ALTER TABLE `hs_hr_employee` CHANGE `payment_mode` `payment_mode` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;

ALTER TABLE `hs_hr_employee` ADD `permanent_street1` VARCHAR(100) NOT NULL AFTER `emp_zipcode`, ADD `permanent_street2` VARCHAR(100) NOT NULL AFTER `permanent_street1`, ADD `permanent_city_code` VARCHAR(100) NOT NULL AFTER `permanent_street2`, ADD `permanent_coun_code` VARCHAR(100) NOT NULL AFTER `permanent_city_code`, ADD `permanent_provin_code` VARCHAR(100) NOT NULL AFTER `permanent_coun_code`, ADD `permanent_zipcode` VARCHAR(20) NOT NULL AFTER `permanent_provin_code`;

ALTER TABLE `hs_hr_employee` CHANGE `permanent_street1` `permanent_street1` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `permanent_street2` `permanent_street2` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `permanent_city_code` `permanent_city_code` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `permanent_coun_code` `permanent_coun_code` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `permanent_provin_code` `permanent_provin_code` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `permanent_zipcode` `permanent_zipcode` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;

ALTER TABLE `hs_hr_emp_dependents` CHANGE `ed_relationship_type` `ed_relationship_type` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

ALTER TABLE `hs_hr_emp_dependents` CHANGE `ed_relationship_type` `ed_relationship_type` ENUM('Child','Father','Mother','Husband','Wife','Sister','Brother','other') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

ALTER TABLE `hs_hr_emp_basicsalary` DROP FOREIGN KEY `hs_hr_emp_basicsalary_ibfk_1`; ALTER TABLE `hs_hr_emp_basicsalary` DROP FOREIGN KEY `hs_hr_emp_basicsalary_ibfk_2`; ALTER TABLE `hs_hr_emp_basicsalary` DROP FOREIGN KEY `hs_hr_emp_basicsalary_ibfk_4`;

ALTER TABLE `hs_hr_emp_directdebit` ADD `nominee_name` VARCHAR(500) NULL AFTER `dd_account`, ADD `pf_number` VARCHAR(100) NULL AFTER `nominee_name`, ADD `nominee_name_pf` VARCHAR(500) NULL AFTER `pf_number`, ADD `branch_name` VARCHAR(500) NULL AFTER `nominee_name_pf`;

ALTER TABLE `hs_hr_emp_directdebit` CHANGE `dd_amount` `dd_amount` DECIMAL(11,2) NULL, CHANGE `dd_account_type` `dd_account_type` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'CHECKING, SAVINGS', CHANGE `dd_transaction_type` `dd_transaction_type` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT 'BLANK, PERC, FLAT, FLATMINUS';

ALTER TABLE `hs_hr_emp_basicsalary` CHANGE `currency_id` `currency_id` VARCHAR(6) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '';

ALTER TABLE `hs_hr_emp_directdebit` CHANGE `dd_routing_num` `dd_routing_num` INT(20) NOT NULL;

ALTER TABLE `hs_hr_emp_work_experience` ADD `last_appraisal_date` DATE NULL AFTER `eexp_to_date`, ADD `ctc_previous` DECIMAL(10) NULL AFTER `last_appraisal_date`, ADD `relevant_experience` INT(5) NULL AFTER `ctc_previous`, ADD `total_experience` INT(5) NULL AFTER `relevant_experience`;

ALTER TABLE `hs_hr_emp_work_experience` CHANGE `relevant_experience` `relevant_experience` DECIMAL(5) NULL DEFAULT NULL, CHANGE `total_experience` `total_experience` DECIMAL(5) NULL DEFAULT NULL;

ALTER TABLE `hs_hr_emp_work_experience` CHANGE `ctc_previous` `ctc_previous` DECIMAL(10,3) NULL DEFAULT NULL, CHANGE `relevant_experience` `relevant_experience` DECIMAL(5,3) NULL DEFAULT NULL, CHANGE `total_experience` `total_experience` DECIMAL(5,3) NULL DEFAULT NULL;

/*
upto this commited on 247 by ketki mam on 26/2/2015
*/

/*
  add current address and perment address same
*/
ALTER TABLE `hs_hr_employee` ADD `c_p_same_address` INT(2) NOT NULL DEFAULT '0' AFTER `emp_zipcode`;
/*Add IFCS code as alphanumbr*/
ALTER TABLE `hs_hr_emp_directdebit` CHANGE `dd_routing_num` `dd_routing_num` VARCHAR(100) NULL;

/*add element in report */
INSERT INTO `perennialhrm_mysql`.`ohrm_display_field` (`display_field_id`, `report_group_id`, `name`, `label`, `field_alias`, `is_sortable`, `sort_order`, `sort_field`, `element_type`, `element_property`, `width`, `is_exportable`, `text_alignment_style`, `is_value_list`, `display_field_group_id`, `default_value`, `is_encrypted`, `is_meta`) VALUES (NULL, '3', 'hs_hr_employee.emp_anniversary_date', 'Wedding Anniversary Date', 'empWeddingDate', 'false', NULL, NULL, 'labelDate', '<xml><getter>empWeddingDate</getter></xml>', '100', '0', NULL, '0', '1', '---', '0', '0');

INSERT INTO `perennialhrm_mysql`.`ohrm_display_field` (`display_field_id`, `report_group_id`, `name`, `label`, `field_alias`, `is_sortable`, `sort_order`, `sort_field`, `element_type`, `element_property`, `width`, `is_exportable`, `text_alignment_style`, `is_value_list`, `display_field_group_id`, `default_value`, `is_encrypted`, `is_meta`) VALUES 
(NULL, '3', 'hs_hr_employee.mothers_name', 'Employee Mother Name', 'employeeMothername', 'false', NULL, NULL, 'label', '<xml><getter>employeeMothername</getter></xml>', '200', '0', NULL, '0', '1', '---', '0', '0'), 
(NULL, '3', 'hs_hr_employee.blood_group', 'Employee Blood Group', 'employeeBloodGroup', 'false', NULL, NULL, 'label', '<xml><getter>employeeBloodGroup</getter></xml>', '200', '0', NULL, '0', '1', '---', '0', '0');

/*
 upto this commited on 247 by yogesh sir on 28/4/2015
*/