

---- below is already updated - R12--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

-- add blood group and mothers name @At revision: 10
ALTER TABLE `hs_hr_employee` ADD `blood_group` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL , ADD `mothers_name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL ;


-- job Category menu label changed to Departments 20 @At revision: 10  
UPDATE `ohrm_menu_item` SET `menu_title` = 'Departments' WHERE `ohrm_menu_item`.`id` = 10; 

UPDATE `ohrm_menu_item` SET `menu_title` = 'Designations' WHERE `ohrm_menu_item`.`id` = 7;

-- adding next appraisal 21/11/2014 @At revision: 10  
ALTER TABLE `hs_hr_employee` ADD `next_appraisal_date` DATE NULL ;


--adding PF number 24/11/2014 @At revision: 10  


ALTER TABLE `hs_hr_employee` ADD `pf_number` VARCHAR(25) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;


--updating the menu item 25/11/2014 Completed: At revision: 11  

UPDATE `ohrm_menu_item` SET `menu_title` = 'Mode Of Commutation' WHERE `ohrm_menu_item`.`id` = 21;

-- 25/11/2014 employee member table Completed: At revision: 11  


ALTER TABLE `hs_hr_emp_member_detail` ADD `type` VARCHAR(25) NULL , ADD `vehicle_type` VARCHAR(25) NULL , ADD `vehicle_number` VARCHAR(25) NULL ;