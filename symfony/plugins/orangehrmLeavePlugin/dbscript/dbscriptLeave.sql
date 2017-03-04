/*

INSERT INTO `ohrm_leave_type` (`id`, `name`, `deleted`, `exclude_in_reports_if_no_entitlement`, `operational_country_id`) VALUES
(1, 'Emergency Leave', 0, 0, NULL),
(2, 'Planned Leave', 0, 0, NULL),
(3, 'Non Paid Leaves', 0, 0, NULL),
(4, 'Compoff', 0, 0, NULL);

ALTER TABLE `ohrm_leave` ADD `leave_request_date` DATE NOT NULL ;

*/

/* upto this shagufta has updata database on 247 on  9/1//2015  */
INSERT INTO `perennialhrm_mysql`.`ohrm_email` (`id`, `name`) VALUES (NULL, 'leave.conditional approve');

INSERT INTO `perennialhrm_mysql`.`ohrm_email_template` (`id`, `email_id`, `locale`, `performer_role`, `recipient_role`, `subject`, `body`) VALUES 
(NULL, '7', 'en_US', NULL, 'ess', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/ConditionallyApprove/leaveApprovalSubject.txt', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/ConditionallyApprove/leaveApprovalBody.txt'), 
(NULL, '7', 'en_US', NULL, 'subscriber', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/ConditionallyApprove/leaveApprovalSubscriberSubject.txt', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/ConditionallyApprove/leaveApprovalSubscriberBody.txt'),
(NULL, '7', 'en_US', NULL, 'supervisor', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/ConditionallyApprove/leaveConditionallyApproveSubject.txt', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/ConditionallyApprove/leaveConditionallyApproveBody.txt');

INSERT INTO `perennialhrm_mysql`.`ohrm_email_template` (`id`, `email_id`, `locale`, `performer_role`, `recipient_role`, `subject`, `body`)
VALUES (NULL, '1', 'en_US', NULL, 'ess', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/apply/leaveApplicationESSSubject.txt', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/apply/leaveApplicationESSBody.txt');
INSERT INTO `perennialhrm_mysql`.`ohrm_email_processor` (`id`, `email_id`, `class_name`) VALUES (NULL, '7', 'LeaveEmailProcessor');

INSERT INTO `perennialhrm_mysql`.`ohrm_workflow_state_machine` (`id`, `workflow`, `state`, `role`, `action`, `resulting_state`, `roles_to_notify`, `priority`) VALUES (NULL, '4', 'PENDING APPROVAL', 'ADMIN', 'CONDITIONAL APPROVE', 'CONDITIONAL APPROVED', 'ess,supervisor,subscriber', '0'), (NULL, '4', 'PENDING APPROVAL', 'SUPERVISOR', 'CONDITIONAL APPROVE', 'CONDITIONAL APPROVED', 'ess,supervisor,subscriber', '0');

INSERT INTO `perennialhrm_mysql`.`ohrm_workflow_state_machine` (`id`, `workflow`, `state`, `role`, `action`, `resulting_state`, `roles_to_notify`, `priority`) VALUES (NULL, '4', 'CONDITIONAL APPROVED', 'ADMIN', 'CANCEL', 'CANCELLED', 'ess,subscriber', '0'), (NULL, '4', 'CONDITIONAL APPROVED', 'SUPERVISOR', 'CANCEL', 'CANCELLED', 'ess,subscriber', '0');

INSERT INTO `perennialhrm_mysql`.`ohrm_workflow_state_machine` (`id`, `workflow`, `state`, `role`, `action`, `resulting_state`, `roles_to_notify`, `priority`) VALUES (NULL, '4', 'CONDITIONAL APPROVED', 'ADMIN', 'APPROVE', 'SCHEDULED', 'ess,subscriber', '0'), (NULL, '4', 'CONDITIONAL APPROVED', 'SUPERVISOR', 'APPROVE', 'SCHEDULED', 'ess,subscriber', '0');

INSERT INTO `perennialhrm_mysql`.`ohrm_leave_status` (`id`, `status`, `name`) VALUES (NULL, '6', 'CONDITIONAL APPROVED');

UPDATE `perennialhrm_mysql`.`ohrm_workflow_state_machine` SET `roles_to_notify` = 'ess,supervisor,subscriber,subordinate' WHERE `ohrm_workflow_state_machine`.`id` = 86;

INSERT INTO `perennialhrm_mysql`.`ohrm_email_template` (`id`, `email_id`, `locale`, `performer_role`, `recipient_role`, `subject`, `body`) VALUES (NULL, '1', 'en_US', NULL, 'subordinate', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/apply/leaveApplicationSubordinateubject.txt', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/apply/leaveApplicationSubordinateBody.txt');

/*
for conditionally approved leave user can cancel leave before approved leave from admin or lead
*/

INSERT INTO `perennialhrm_mysql`.`ohrm_workflow_state_machine` (`id`, `workflow`, `state`, `role`, `action`, `resulting_state`, `roles_to_notify`, `priority`) VALUES (NULL, '4', 'CONDITIONAL APPROVED', 'ESS', 'CANCEL', 'CANCELLED', 'supervisor,subscriber', '0');

/*
For leave conflict
*/
INSERT INTO `perennialhrm_mysql`.`ohrm_email` (`id`, `name`) VALUES (NULL, 'leave.conflict');

INSERT INTO `perennialhrm_mysql`.`ohrm_email_processor` (`id`, `email_id`, `class_name`) VALUES (NULL, '8', 'LeaveEmailProcessor');

INSERT INTO `perennialhrm_mysql`.`ohrm_email_template` (`id`, `email_id`, `locale`, `performer_role`, `recipient_role`, `subject`, `body`) VALUES (NULL, '8', 'en_US', NULL, 'supervisor', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/conflict/conflictLeaveDataSuperwiserSubject.txt', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/conflict/conflictLeaveDataSuperwiserBody.txt'), (NULL, '8', 'en_US', NULL, 'subordinate', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/conflict/conflictLeaveDataSubordinateSubject.txt', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/conflict/conflictLeaveDataSubordinateBody.txt'), (NULL, '8', 'en_US', NULL, 'ess', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/conflict/conflictLeaveDataESSSubject.txt', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/conflict/conflictLeaveDataESSBody.txt');

INSERT INTO `perennialhrm_mysql`.`ohrm_workflow_state_machine` (`id`, `workflow`, `state`, `role`, `action`, `resulting_state`, `roles_to_notify`, `priority`) VALUES (NULL, '4', 'INITIAL', 'ESS', 'CONFLICT', 'PENDING APPROVAL', 'ess,supervisor,subordinate', '0');

--- updting the menu item , to hide assign leave default value is 17
UPDATE `perennialhrm_mysql`.`ohrm_menu_item` SET `screen_id` = NULL WHERE `ohrm_menu_item`.`id` = 49;

/*
  send email notification of leave approve to subordinate 
  
 */
UPDATE `perennialhrm_mysql`.`ohrm_workflow_state_machine` SET `roles_to_notify` = 'ess,subscriber,subordinate,supervisor' WHERE `ohrm_workflow_state_machine`.`id` = 89;
UPDATE `perennialhrm_mysql`.`ohrm_workflow_state_machine` SET `roles_to_notify` = 'ess,subscriber,subordinate,supervisor' WHERE `ohrm_workflow_state_machine`.`id` = 90;
UPDATE `perennialhrm_mysql`.`ohrm_workflow_state_machine` SET `roles_to_notify` = 'ess,subscriber,subordinate,supervisor' WHERE `ohrm_workflow_state_machine`.`id` = 113;
UPDATE `perennialhrm_mysql`.`ohrm_workflow_state_machine` SET `roles_to_notify` = 'ess,subscriber,subordinate,supervisor' WHERE `ohrm_workflow_state_machine`.`id` = 114;
UPDATE `perennialhrm_mysql`.`ohrm_workflow_state_machine` SET `roles_to_notify` = 'ess,supervisor,subscriber,subordinate' WHERE `ohrm_workflow_state_machine`.`id` = 109;
UPDATE `perennialhrm_mysql`.`ohrm_workflow_state_machine` SET `roles_to_notify` = 'ess,supervisor,subscriber,subordinate' WHERE `ohrm_workflow_state_machine`.`id` = 110;
INSERT INTO `perennialhrm_mysql`.`ohrm_email_template` (`id`, `email_id`, `locale`, `performer_role`, `recipient_role`, `subject`, `body`) VALUES (NULL, '3', 'en_US', NULL, 'subordinate', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/approve/leaveApproveSubordinateubject.txt', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/approve/leaveApproveSubordinateBody.txt');
INSERT INTO `perennialhrm_mysql`.`ohrm_email_template` (`id`, `email_id`, `locale`, `performer_role`, `recipient_role`, `subject`, `body`) VALUES (NULL, '7', 'en_US', NULL, 'subordinate', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/ConditionallyApprove/leaveConditionalApproveSubordinateubject.txt', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/ConditionallyApprove/leaveConditionalApproveSubordinateBody.txt');
INSERT INTO `perennialhrm_mysql`.`ohrm_email_template` (`id`, `email_id`, `locale`, `performer_role`, `recipient_role`, `subject`, `body`) VALUES (NULL, '6', 'en_US', NULL, 'subordinate', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/change/leaveChangeSubordinateSubject.txt', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/change/leaveChangeSubordinateBody.txt');
INSERT INTO `perennialhrm_mysql`.`ohrm_email_template` (`id`, `email_id`, `locale`, `performer_role`, `recipient_role`, `subject`, `body`) VALUES (NULL, '3', 'en_US', NULL, 'supervisor', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/approve/leaveApprovalSuperwiserSubject.txt', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/approve/leaveApprovalSuperwiserBody.txt');
INSERT INTO `perennialhrm_mysql`.`ohrm_email_template` (`id`, `email_id`, `locale`, `performer_role`, `recipient_role`, `subject`, `body`) VALUES (NULL, '6', 'en_US', NULL, 'supervisor', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/change/leaveChangeSuperwiserSubject.txt', 'orangehrmLeavePlugin/modules/leave/templates/mail/en_US/change/leaveChangeSuperwiserBody.txt');

INSERT INTO `perennialhrm_mysql`.`ohrm_user_role_screen` (`id`, `user_role_id`, `screen_id`, `can_read`, `can_create`, `can_update`, `can_delete`) VALUES (NULL, '2', '17', '1', '1', '1', '0');
UPDATE `perennialhrm_mysql`.`ohrm_menu_item` SET `menu_title` = 'Comp Off' WHERE `ohrm_menu_item`.`id` = 49;
UPDATE `perennialhrm_mysql`.`ohrm_menu_item` SET `screen_id` ='17'  WHERE `ohrm_menu_item`.`id` = 49;
UPDATE `perennialhrm_mysql`.`ohrm_user_role_screen` SET `can_read` = '0', `can_create` = '0', `can_update` = '0' WHERE `ohrm_user_role_screen`.`id` = 22;

ALTER TABLE `ohrm_leave_tasks` ADD `comp_off_id` INT NULL DEFAULT NULL AFTER `leave_request_id`;
ALTER TABLE `ohrm_leave_tasks` ADD INDEX(`comp_off_id`);
ALTER TABLE `ohrm_leave_tasks` ADD FOREIGN KEY (`comp_off_id`) REFERENCES `perennialhrm_mysql`.`ohrm_comp_off`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;


ALTER TABLE `ohrm_leave_tasks` DROP FOREIGN KEY 
`ohrm_leave_tasks_ibfk_3`; ALTER TABLE `ohrm_leave_tasks` ADD 
CONSTRAINT `ohrm_leave_tasks_ibfk_3` FOREIGN KEY 
(`comp_off_id`) REFERENCES `perennialhrm_mysql`.`ohrm_comp_off`
(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

/*
up to this commited on 247 by ketki mam on 26/2/2015
*/

/* Startedworking on compoff modeule  4/2/2016  */

ALTER TABLE `ohrm_leave_request` ADD `contact_no` VARCHAR(256) NULL DEFAULT NULL COMMENT 'contact details during absence' AFTER `comments`;
ALTER TABLE `ohrm_leave_tasks` DROP FOREIGN KEY `ohrm_leave_tasks_ibfk_3`;
DROP TABLE ohrm_comp_off;

/* add ohrm_compoff_request table */

CREATE TABLE IF NOT EXISTS `ohrm_compoff_request` (
`id` int(11) NOT NULL,
  `date_applied` date NOT NULL,
  `emp_number` int(11) NOT NULL,
  `called_emp_number` int(11) DEFAULT NULL,
  `comments` varchar(256) DEFAULT NULL,
  `work_type` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE `ohrm_compoff_request`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `ohrm_compoff_request`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `ohrm_compoff_request` ADD INDEX(`emp_number`);
ALTER TABLE `ohrm_compoff_request` ADD FOREIGN KEY (`emp_number`) REFERENCES `perennial_hrm`.`hs_hr_employee`(`emp_number`) ON DELETE CASCADE ON UPDATE RESTRICT;
ALTER TABLE `ohrm_compoff_request` ADD INDEX(`called_emp_number`);
ALTER TABLE `ohrm_compoff_request` ADD FOREIGN KEY (`called_emp_number`) REFERENCES `perennial_hrm`.`hs_hr_employee`(`emp_number`) ON DELETE SET NULL ON UPDATE RESTRICT;



/*add ohrm_compoff table */


CREATE TABLE IF NOT EXISTS `ohrm_compoff` (
`id` int(11) NOT NULL,
  `date` date DEFAULT NULL,
  `length_hours` decimal(6,4) DEFAULT NULL,
  `length_days` decimal(6,4) DEFAULT NULL,
  `status` smallint(6) NOT NULL DEFAULT '0' COMMENT '0-pending 1-cancel 2-approve',
  `compoff_request_id` int(11) NOT NULL,
  `emp_number` int(11) NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `duration_type` int(4) NOT NULL DEFAULT '0',
  `leave_request_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE `ohrm_compoff`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `ohrm_compoff`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ohrm_compoff` ADD INDEX(`compoff_request_id`);
ALTER TABLE `ohrm_compoff` ADD INDEX(`emp_number`);
ALTER TABLE `ohrm_compoff` ADD FOREIGN KEY (`compoff_request_id`) REFERENCES `perennial_hrm`.`ohrm_compoff_request`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT; ALTER TABLE `ohrm_compoff` ADD FOREIGN KEY (`emp_number`) REFERENCES `perennial_hrm`.`hs_hr_employee`(`emp_number`) ON DELETE CASCADE ON UPDATE RESTRICT;




/* add ohrm_compoff_comment   */

CREATE TABLE IF NOT EXISTS `ohrm_compoff_comment` (
`id` int(11) NOT NULL,
  `compoff_id` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  `created_by_name` varchar(256) DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `created_by_emp_number` int(11) DEFAULT NULL,
  `comments` varchar(256) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE `ohrm_compoff_comment`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `ohrm_compoff_comment`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ohrm_compoff_comment` ADD INDEX(`compoff_id`);
ALTER TABLE `ohrm_compoff_comment` ADD INDEX(`created_by_id`);
ALTER TABLE `ohrm_compoff_comment` ADD INDEX(`created_by_emp_number`);
ALTER TABLE `ohrm_compoff_comment` ADD FOREIGN KEY (`compoff_id`) REFERENCES `perennial_hrm`.`ohrm_compoff`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
ALTER TABLE `ohrm_compoff_comment` ADD FOREIGN KEY (`created_by_id`) REFERENCES `perennial_hrm`.`ohrm_user`(`id`) ON DELETE SET NULL ON UPDATE RESTRICT; ALTER TABLE `ohrm_compoff_comment` ADD FOREIGN KEY (`created_by_emp_number`) REFERENCES `perennial_hrm`.`hs_hr_employee`(`emp_number`) ON DELETE CASCADE ON UPDATE RESTRICT;



/* add ohrm_compoff_tasks table  */

CREATE TABLE IF NOT EXISTS `ohrm_compoff_tasks` (
`id` int(11) NOT NULL,
  `compoff_request_id` int(11) NOT NULL,
  `task_name` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE `ohrm_compoff_tasks`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `ohrm_compoff_tasks`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ohrm_compoff_tasks` ADD INDEX(`compoff_request_id`);
ALTER TABLE `ohrm_compoff_tasks` ADD FOREIGN KEY (`compoff_request_id`) REFERENCES `perennial_hrm`.`ohrm_compoff_request`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;



/* add ohrm_compoff_request_comment table  */
CREATE TABLE IF NOT EXISTS `ohrm_compoff_request_comment` (
`id` int(11) NOT NULL,
  `compoff_request_id` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  `created_by_name` varchar(256) DEFAULT NULL,
  `created_by_id` int(11) DEFAULT NULL,
  `created_by_emp_number` int(11) DEFAULT NULL,
  `comments` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE `ohrm_compoff_request_comment`
 ADD PRIMARY KEY (`id`);

ALTER TABLE `ohrm_compoff_request_comment`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `ohrm_compoff_request_comment` ADD INDEX(`compoff_request_id`);
ALTER TABLE `ohrm_compoff_request_comment` ADD INDEX(`created_by_id`);
ALTER TABLE `ohrm_compoff_request_comment` ADD INDEX(`created_by_emp_number`);
ALTER TABLE `ohrm_compoff_request_comment` ADD FOREIGN KEY (`compoff_request_id`) REFERENCES `perennial_hrm`.`ohrm_compoff_request`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT; ALTER TABLE `ohrm_compoff_request_comment` ADD FOREIGN KEY (`created_by_id`) REFERENCES `perennial_hrm`.`ohrm_user`(`id`) ON DELETE SET NULL ON UPDATE RESTRICT; ALTER TABLE `ohrm_compoff_request_comment` ADD FOREIGN KEY (`created_by_emp_number`) REFERENCES `perennial_hrm`.`hs_hr_employee`(`emp_number`) ON DELETE CASCADE ON UPDATE RESTRICT;


ALTER TABLE `ohrm_compoff` CHANGE `leave_request_date` `compoff_request_date` DATE NULL DEFAULT NULL;

INSERT INTO `perennial_hrm`.`ohrm_screen` (`id`, `name`, `module_id`, `action_url`) VALUES (NULL, 'Compoff List', '4', 'viewCompoffList');
INSERT INTO `perennial_hrm`.`ohrm_menu_item` (`id`, `menu_title`, `screen_id`, `parent_id`, `level`, `order_hint`, `url_extras`, `status`) VALUES (NULL, 'Compoff List', '116', '41', '2', '600', '/reset/1', '1');
INSERT INTO `perennial_hrm`.`ohrm_user_role_screen` (`id`, `user_role_id`, `screen_id`, `can_read`, `can_create`, `can_update`, `can_delete`) VALUES (NULL, '1', '116', '1', '1', '1', '0'), (NULL, '3', '116', '1', '1', '1', '0');
UPDATE `perennial_hrm`.`ohrm_menu_item` SET `menu_title` = 'Apply Compoff' WHERE `ohrm_menu_item`.`id` = 49;
