
/*remove pay grade */
DELETE FROM `perennialhrm_mysql`.`ohrm_filter_field` WHERE `ohrm_filter_field`.`filter_field_id` = 9

/* delete other id */
DELETE FROM `perennialhrm_mysql`.`ohrm_display_field` WHERE `ohrm_display_field`.`display_field_id` = 97

/*delete membership */

DELETE FROM `perennialhrm_mysql`.`ohrm_display_field` WHERE `ohrm_display_field`.`display_field_id` = 35;
DELETE FROM `perennialhrm_mysql`.`ohrm_display_field` WHERE `ohrm_display_field`.`display_field_id` = 36;
DELETE FROM `perennialhrm_mysql`.`ohrm_display_field` WHERE `ohrm_display_field`.`display_field_id` = 37;
DELETE FROM `perennialhrm_mysql`.`ohrm_display_field` WHERE `ohrm_display_field`.`display_field_id` = 38;
DELETE FROM `perennialhrm_mysql`.`ohrm_display_field` WHERE `ohrm_display_field`.`display_field_id` = 39;
DELETE FROM `perennialhrm_mysql`.`ohrm_display_field` WHERE `ohrm_display_field`.`display_field_id` = 40;
DELETE FROM `perennialhrm_mysql`.`ohrm_display_field` WHERE `ohrm_display_field`.`display_field_id` = 110;

/*upto this updated on 247 on 27/2/2015  */

DELETE FROM `perennialhrm_mysql`.`ohrm_filter_field` WHERE `ohrm_filter_field`.`filter_field_id` = 18;
UPDATE `perennialhrm_mysql`.`ohrm_display_field` SET `label` = 'Current Address' WHERE `ohrm_display_field`.`display_field_id` = 20;
UPDATE `perennialhrm_mysql`.`ohrm_display_field` SET `name` = 'hs_hr_emp_work_experience.last_appraisal_date', `label` = 'Last Appraisal Date', `field_alias` = 'expAppraisal', `element_property` = '<xml><getter>expAppraisal</getter></xml>' WHERE `ohrm_display_field`.`display_field_id` = 45;
INSERT INTO `perennialhrm_mysql`.`ohrm_display_field` (`display_field_id`, `report_group_id`, `name`, `label`, `field_alias`, `is_sortable`, `sort_order`, `sort_field`, `element_type`, `element_property`, `width`, `is_exportable`, `text_alignment_style`, `is_value_list`, `display_field_group_id`, `default_value`, `is_encrypted`, `is_meta`) VALUES (NULL, '3', 'hs_hr_emp_work_experience.ctc_previous', 'CTC (previous company)', 'ctc_previous', 'false', NULL, NULL, 'label', '<xml><getter>ctc_previous</getter></xml>', '200', '0', NULL, '1', '10', '---', '0', '0');
UPDATE `perennialhrm_mysql`.`ohrm_display_field` SET `label` = 'Payment Mode' WHERE `ohrm_display_field`.`display_field_id` = 66;
UPDATE `perennialhrm_mysql`.`ohrm_display_field` SET `label` = 'CTC ' WHERE `ohrm_display_field`.`display_field_id` = 67;
UPDATE `perennialhrm_mysql`.`ohrm_display_field` SET `label` = 'Account Number' WHERE `ohrm_display_field`.`display_field_id` = 71;
UPDATE `perennialhrm_mysql`.`ohrm_display_field` SET `label` = 'IFSC code' WHERE `ohrm_display_field`.`display_field_id` = 73;
DELETE FROM `perennialhrm_mysql`.`ohrm_display_field` WHERE `ohrm_display_field`.`display_field_id` = 65;
DELETE FROM `perennialhrm_mysql`.`ohrm_display_field` WHERE `ohrm_display_field`.`display_field_id` = 69;
DELETE FROM `perennialhrm_mysql`.`ohrm_display_field` WHERE `ohrm_display_field`.`display_field_id` = 70;
DELETE FROM `perennialhrm_mysql`.`ohrm_display_field` WHERE `ohrm_display_field`.`display_field_id` = 72;
DELETE FROM `perennialhrm_mysql`.`ohrm_display_field` WHERE `ohrm_display_field`.`display_field_id` = 74;
INSERT INTO `perennialhrm_mysql`.`ohrm_display_field` (`display_field_id`, `report_group_id`, `name`, `label`, `field_alias`, `is_sortable`, `sort_order`, `sort_field`, `element_type`, `element_property`, `width`, `is_exportable`, `text_alignment_style`, `is_value_list`, `display_field_group_id`, `default_value`, `is_encrypted`, `is_meta`) VALUES (NULL, '3', 'hs_hr_emp_directdebit.nominee_name', 'Nominee Name', 'nomineeName', 'false', NULL, NULL, 'label', '<xml><getter>nomineeName</getter></xml>', '200', '0', NULL, '1', '7', '---', '0', '0'), (NULL, '3', 'hs_hr_emp_directdebit.pf_number', 'PF Number', 'pfnumber', 'false', NULL, NULL, 'label', '<xml><getter>pfnumber</getter></xml>', '200', '0', NULL, '1', '7', '---', '0', '0');
INSERT INTO `perennialhrm_mysql`.`ohrm_display_field` (`display_field_id`, `report_group_id`, `name`, `label`, `field_alias`, `is_sortable`, `sort_order`, `sort_field`, `element_type`, `element_property`, `width`, `is_exportable`, `text_alignment_style`, `is_value_list`, `display_field_group_id`, `default_value`, `is_encrypted`, `is_meta`) VALUES (NULL, '3', 'hs_hr_emp_directdebit.nominee_name_pf', 'Nominee name (PF A/C) ', 'nomineeNamepf', 'false', NULL, NULL, 'label', '<xml><getter>nomineeNamepf</getter></xml>', '200', '0', NULL, '1', '7', '---', '0', '0'), (NULL, '3', 'hs_hr_emp_directdebit.branch_name', 'Branch Name', 'branchName', 'false', NULL, NULL, 'label', '<xml><getter>branchName</getter></xml>', '200', '0', NULL, '1', '7', '---', '0', '0');
DELETE FROM `perennialhrm_mysql`.`ohrm_display_field` WHERE `ohrm_display_field`.`display_field_id` = 68;
UPDATE `perennialhrm_mysql`.`ohrm_display_field` SET `element_type` = 'labelDate' WHERE `ohrm_display_field`.`display_field_id` = 45;
UPDATE `perennialhrm_mysql`.`ohrm_display_field` SET `name` = 'hs_hr_employee.next_appraisal_date', `label` = 'Next Appraisal Date', `field_alias` = 'nextAppraisalDate', `element_type` = 'labelDate', `element_property` = '<xml><getter>nextAppraisalDate</getter></xml>', `width` = '100' WHERE `ohrm_display_field`.`display_field_id` = 82;
UPDATE `perennialhrm_mysql`.`ohrm_display_field` SET `name` = 'CASE hs_hr_emp_passport.ep_passport_type_flg WHEN 1 THEN "Passport" WHEN 2 THEN "Driving License" WHEN 3 THEN "Adhar Card" WHEN 4 THEN "Election Identity Card" WHEN 5 THEN "PAN Card" END' WHERE `ohrm_display_field`.`display_field_id` = 95;
DELETE FROM `perennialhrm_mysql`.`ohrm_display_field` WHERE `ohrm_display_field`.`display_field_id` = 85;
DELETE FROM `perennialhrm_mysql`.`ohrm_display_field` WHERE `ohrm_display_field`.`display_field_id` = 87;
DELETE FROM `perennialhrm_mysql`.`ohrm_display_field` WHERE `ohrm_display_field`.`display_field_id` = 89;
DELETE FROM `perennialhrm_mysql`.`ohrm_display_field` WHERE `ohrm_display_field`.`display_field_id` = 90;
DELETE FROM `perennialhrm_mysql`.`ohrm_display_field` WHERE `ohrm_display_field`.`display_field_id` = 59;
DELETE FROM `perennialhrm_mysql`.`ohrm_display_field` WHERE `ohrm_display_field`.`display_field_id` = 60;
DELETE FROM `perennialhrm_mysql`.`ohrm_display_field` WHERE `ohrm_display_field`.`display_field_id` = 61;
DELETE FROM `perennialhrm_mysql`.`ohrm_display_field` WHERE `ohrm_display_field`.`display_field_id` = 119;
DELETE FROM `perennialhrm_mysql`.`ohrm_display_field` WHERE `ohrm_display_field`.`display_field_id` = 109;
DELETE FROM `perennialhrm_mysql`.`ohrm_display_field` WHERE `ohrm_display_field`.`display_field_id` = 18;
DELETE FROM `perennialhrm_mysql`.`ohrm_display_field` WHERE `ohrm_display_field`.`display_field_id` = 19;

INSERT INTO `perennialhrm_mysql`.`ohrm_display_field` (`display_field_id`, `report_group_id`, `name`, `label`, `field_alias`, `is_sortable`, `sort_order`, `sort_field`, `element_type`, `element_property`, `width`, `is_exportable`, `text_alignment_style`, `is_value_list`, `display_field_group_id`, `default_value`, `is_encrypted`, `is_meta`) VALUES (NULL, '3', 'CONCAT_WS(", ", NULLIF(hs_hr_employee.permanent_street1, ""), NULLIF(hs_hr_employee.permanent_street2, ""), NULLIF(hs_hr_employee.permanent_city_code, ""),NULLIF(hs_hr_employee.permanent_provin_code,""), NULLIF(hs_hr_employee.permanent_zipcode,""))', 'Permanent Address', 'paddress', 'false', NULL, NULL, 'label', '<xml><getter>paddress</getter></xml>', '200', '0', NULL, '0', '2', '---', '0', '0');
UPDATE `perennialhrm_mysql`.`ohrm_display_field` SET `name` = 'CONCAT_WS(", ", NULLIF(hs_hr_employee.permanent_street1, ""), NULLIF(hs_hr_employee.permanent_street2, ""), NULLIF(hs_hr_employee.permanent_city_code, ""),NULLIF(hs_hr_employee.permanent_provin_code,""), NULLIF(hs_hr_employee.permanent_zipcode,""), NULLIF(hs_hr_country.cou_name,""))' WHERE `ohrm_display_field`.`display_field_id` = 131;

/*
upto this updated on 247 by yogesh sir on 29/04/2015
*/