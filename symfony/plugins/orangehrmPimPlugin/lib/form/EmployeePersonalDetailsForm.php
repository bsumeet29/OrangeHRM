<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */

class EmployeePersonalDetailsForm extends BaseForm {

    private $nationalityService;
    private $employeeService;
    private $readOnlyWidgetNames = array();
    private $gender;
    private $employee;
    public $fullName;

    /**
     * Get NationalityService
     * @returns NationalityService
     */
    public function getNationalityService() {
        if (is_null($this->nationalityService)) {
            $this->nationalityService = new NationalityService();
        }
        return $this->nationalityService;
    }

    /**
     * Set NationalityService
     * @param NationalityService $nationalityService
     */
    public function setNationalityService(NationalityService $nationalityService) {
        $this->nationalityService = $nationalityService;
    }

    /**
     * Get EmployeeService
     * @returns EmployeeService
     */
    public function getEmployeeService() {
        if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
            $this->employeeService->setEmployeeDao(new EmployeeDao());
        }
        return $this->employeeService;
    }

    /**
     * Set EmployeeService
     * @param EmployeeService $employeeService
     */
    public function setEmployeeService(EmployeeService $employeeService) {
        $this->employeeService = $employeeService;
    }

    public function configure() {

        $this->personalInformationPermission = $this->getOption('personalInformationPermission');
        $this->canEditSensitiveInformation = $this->getOption('canEditSensitiveInformation');
        
        $empNumber = $this->getOption('empNumber');

         
        $this->employee = $this->getEmployeeService()->getEmployee($empNumber);
        
        $this->gender = ($this->employee->emp_gender != "") ? $this->employee->emp_gender : "";
        $this->fullName = $this->employee->getFullName();
        

        $widgets = array('txtEmpID' => new sfWidgetFormInputHidden(array(), array('value' => $this->employee->empNumber)));
        $validators = array('txtEmpID' => new sfValidatorString(array('required' => true)));
      
        if ($this->personalInformationPermission->canRead()) {

            $personalInfoWidgets = $this->getPersonalInfoWidgets();
            $personalInfoValidators = $this->getPersonalInfoValidators();
          
            if (!$this->personalInformationPermission->canUpdate()) {
                foreach ($personalInfoWidgets as $widgetName => $widget) {
                    $widget->setAttribute('disabled', 'disabled');
                    $this->readOnlyWidgetNames[] = $widgetName;
                }
            }
           
            $widgets = array_merge($widgets, $personalInfoWidgets);
            $validators = array_merge($validators, $personalInfoValidators);

            $sensitiveInfoWidgets = $this->getSensitiveInfoWidgets();
            $sensitiveInfoValidators = $this->getSensitiveInfoValidators();
            
            if (!$this->canEditSensitiveInformation) {
                foreach ($sensitiveInfoWidgets as $widgetName => $widget) {
                    $widget->setAttribute('disabled', 'disabled');
                    $this->readOnlyWidgetNames[] = $widgetName;
                }
            }
            
            $widgets = array_merge($widgets, $sensitiveInfoWidgets);
            $validators = array_merge($validators, $sensitiveInfoValidators);
        }

        $this->setWidgets($widgets);
        $this->setValidators($validators);

        $this->widgetSchema->setNameFormat('personal[%s]');
    }

    public function getReadOnlyWidgetNames() {
        return $this->readOnlyWidgetNames;
    }

    private function getNationalityList() {
        $nationalityService = $this->getNationalityService();
        $nationalities = $nationalityService->getNationalityList();
        $list = array(0 => "-- " . __('Select') . " --");

        foreach ($nationalities as $nationality) {
            $list[$nationality->getId()] = $nationality->getName();
        }
        return $list;
    }

    private function getPersonalInfoWidgets() {
        $today=date('d-m-Y');
   
        $widgets = array(
            'txtEmpLastName' => new sfWidgetFormInputText(),
            'txtEmpFirstName' => new sfWidgetFormInputText(),
            'txtEmpMiddleName' => new sfWidgetFormInputText(),
            'txtMothersName' => new sfWidgetFormInputText(),
            'cmbBloodGroup' => new sfWidgetFormSelect(array('choices' => array('' => "-- " . __('Select') . " --", 'A+' => __('A+'), 'A-' => __('A-'), 'AB+' => __('AB+'), 'AB-' => __('AB-'), 'B+' => __('B+'), 'B-' => __('B-'), 'O+' => __('O+'), 'O-' => __('O-')))),
            'txtPfNumber'  => new sfWidgetFormInputText(),
            'txtEmpNickName' => new sfWidgetFormInputText(),
            'optGender' => new sfWidgetFormChoice(array('expanded' => true, 'choices' => array(1 => __("Male"), 2 => __("Female")))),
            'cmbNation' => new sfWidgetFormSelect(array('choices' => $this->getNationalityList())),
            
            'cmbMarital' => new sfWidgetFormSelect(array('choices' => array('' => "-- " . __('Select') . " --", 'Single' => __('Single'), 'Married' => __('Married'), 'Other' => __('Other')))),
            'txtAnniversaryDate' =>  new ohrmWidgetDatePicker(array(), array('id' => 'personal_txtAnniversaryDate','maxDate'=>$today)),
            'chkSmokeFlag' => new sfWidgetFormInputCheckbox(),
            'txtLicExpDate' => new ohrmWidgetDatePicker(array(), array('id' => 'personal_txtLicExpDate','minDate'=>$today)),
            'txtMilitarySer' => new sfWidgetFormInputText(),
            'DOB' => new ohrmWidgetDatePicker(array(), array('id' => 'personal_DOB','maxDate'=>$today)),
            'txtLicenNo' => new sfWidgetFormInputText()
        );
   
        //setting default values
        
        $widgets['txtEmpLastName']->setAttribute('value', $this->employee->lastName);
        $widgets['txtEmpFirstName']->setAttribute('value', $this->employee->firstName);
        $widgets['txtEmpMiddleName']->setAttribute('value', $this->employee->middleName);
        $widgets['txtMothersName']->setAttribute('value', $this->employee->mothersName);
        
        $widgets['cmbBloodGroup']->setDefault($this->employee->bloodGroup);
        $widgets['txtPfNumber']->setAttribute('value', $this->employee->pfNumber);
        $widgets['txtEmpNickName']->setAttribute('value', $this->employee->nickName);
        $widgets['optGender']->setDefault($this->gender);
        //setting the default selected nation code
        $widgets['cmbNation']->setDefault($this->employee->nation_code);
        
        //setting default marital status
        $widgets['cmbMarital']->setDefault($this->employee->emp_marital_status);
        
        $widgets['txtAnniversaryDate']->setAttribute('value',  set_datepicker_date_format($this->employee->anniversaryDate));

        if ($this->employee->smoker) {
            $widgets['chkSmokeFlag']->setAttribute('checked', 'checked');
        }

        $widgets['chkSmokeFlag']->setAttribute('value', 1);
        $widgets['txtLicExpDate']->setAttribute('value', set_datepicker_date_format($this->employee->emp_dri_lice_exp_date));
        $widgets['txtMilitarySer']->setAttribute('value', $this->employee->militaryService);
        $widgets['DOB']->setAttribute('value', set_datepicker_date_format($this->employee->emp_birthday));
        $widgets['txtLicenNo']->setAttribute('value', $this->employee->licenseNo);
       
        return $widgets;
    }

    private function getPersonalInfoValidators() {
        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
        

        //setting server side validators
        $validators = array(
            //'txtEmployeeId' => new sfValidatorString(array('required' => true,'trim'=>true)),
        'txtEmpLastName' => new sfValidatorString(array('required' => true, 'max_length' => 30, 'trim' => true),
                                array('required' => 'Last Name Empty!', 'max_length' => 'Last Name Length exceeded 30 characters')),
        'txtEmpFirstName' => new sfValidatorString(array('required' => true, 'max_length' => 30, 'trim' => true),
                                array('required' => 'First Name Empty!', 'max_length' => 'First Name Length exceeded 30 characters')),
        'txtEmpMiddleName' => new sfValidatorString(array('required' => false, 'max_length' => 30, 'trim' => true), array('max_length' => 'Middle Name Length exceeded 30 characters')),
        'txtMothersName' => new sfValidatorString(array('required' => false,'trim'=>true)),
        'cmbBloodGroup' => new sfValidatorChoice(array('required' => false,
                'choices' => array('A+','A-','B+','B-','AB+','AB-','O+','O-'),
                'multiple' => false)),
         'txtPfNumber'   => new sfValidatorString(array('required' => false,'trim'=>true,'max_length' => 30)),
         'txtEmpNickName' => new sfValidatorString(array('required' => false, 'trim' => true)),
         'optGender' => new sfValidatorChoice(array('required' => false,
                'choices' => array(Employee::GENDER_MALE, Employee::GENDER_FEMALE),
                'multiple' => false)),
         'cmbNation' => new sfValidatorChoice(array('required' => false, 'choices' => array_keys($this->getNationalityList()))),
         'txtAnniversaryDate' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false), array('invalid' => "Date format should be" . $inputDatePattern)),
         'cmbMarital' => new sfValidatorString(array('required' => false)),
         'chkSmokeFlag' => new sfValidatorString(array('required' => false)),
         'txtLicExpDate' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false), array('invalid' => "Date format should be $inputDatePattern")),
         'txtMilitarySer' => new sfValidatorString(array('required' => false)),
         'DOB' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false), array('invalid' => "Date format should be" . $inputDatePattern)),
         'txtLicenNo' => new sfValidatorString(array('required' => false, 'max_length' => 30,'trim'=>true), array('max_length' => 'License No length exceeded 30 characters'))
        );

        return $validators;
    }

    private function getSensitiveInfoWidgets() {

        $widgets = array(
            'txtEmployeeId' => new sfWidgetFormInputText(),
            'txtNICNo' => new sfWidgetFormInputText(),
            'txtSINNo' => new sfWidgetFormInputText(),
            );


        $widgets['txtEmployeeId']->setAttribute('value', $this->employee->employeeId);
        $widgets['txtNICNo']->setAttribute('value', $this->employee->ssn);
        $widgets['txtSINNo']->setAttribute('value', $this->employee->sin);
       

        return $widgets;
    }

    private function getSensitiveInfoValidators() {
       

        $validators = array(
            'txtEmployeeId' => new sfValidatorString(array('required' => false,'trim'=>true)),
            'txtNICNo' => new sfValidatorString(array('required' => false)),
            'txtSINNo' => new sfValidatorString(array('required' => false, 'max_length' => 30), array('max_length' => 'First Name Length exceeded 30 characters')),
            
            );

        return $validators;
    }

    /**
     * Get Employee object with values filled using form values
     */
    public function getEmployee() {

        $employee = $this->employee;

        if ($this->personalInformationPermission->canUpdate()) {

            $employee->firstName = $this->getValue('txtEmpFirstName');
            $employee->middleName = $this->getValue('txtEmpMiddleName');
            $employee->lastName = $this->getValue('txtEmpLastName');
            $employee->nickName = $this->getValue('txtEmpNickName');
            $employee->mothersName = $this->getValue('txtMothersName');
            $employee->bloodGroup = $this->getValue('cmbBloodGroup');
            $employee->pfNumber = $this->getValue('txtPfNumber');
            $nation = $this->getValue('cmbNation');
            $employee->nation_code = ($nation != '0') ? $nation : null;
            $employee->anniversaryDate = $this->getValue('txtAnniversaryDate');
            $employee->emp_marital_status = $this->getValue('cmbMarital');
            $smoker = $this->getValue('chkSmokeFlag');
            $employee->smoker = !empty($smoker) ? $smoker : 0;
            $gender = $this->getValue('optGender');
            if (!empty($gender)) {
                $employee->emp_gender = $gender;
            }
            $employee->emp_dri_lice_exp_date = $this->getValue('txtLicExpDate');
            $employee->militaryService = $this->getValue('txtMilitarySer');
            $employee->emp_birthday = $this->getValue('DOB');
            $employee->licenseNo = $this->getValue('txtLicenNo');
        }

        if ($this->canEditSensitiveInformation) {
            $employee->employeeId = $this->getValue('txtEmployeeId');
            $employee->ssn = $this->getValue('txtNICNo');
            $employee->sin = $this->getValue('txtSINNo');
            
        }

        return $employee;
    }

}

