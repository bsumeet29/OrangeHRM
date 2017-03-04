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
class EmployeeContactDetailsForm extends sfForm {

    private $countryService;
    private $employeeService;
    public $fullName;
    public $empNumber;
    private $employee;

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

        $this->contactDetailsPermission = $this->getOption('contactDetailsPermission');

        
        $this->empNumber = $this->getOption('empNumber');
        $this->employee = $this->getEmployeeService()->getEmployee($this->empNumber);
//        echo "<pre>";
//        print_r( $this->employee);
//        die;
        $this->fullName = $this->employee->getFullName();

        $widgets = array('empNumber' => new sfWidgetFormInputHidden(array(), array('value' => $this->employee->empNumber)));
        $validators = array('empNumber' => new sfValidatorString(array('required' => true)));

        if ($this->contactDetailsPermission->canRead()) {

            $contactDetailsWidgets = $this->getContactDetailsWidgets();
            $contactDetailsValidators = $this->getContactDetailsValidators();

            if (!$this->contactDetailsPermission->canUpdate()) {
                foreach ($contactDetailsWidgets as $widgetName => $widget) {
                    $widget->setAttribute('disabled', 'disabled');
                }
            }
            $widgets = array_merge($widgets, $contactDetailsWidgets);
            $validators = array_merge($validators, $contactDetailsValidators);
        }

        $this->setWidgets($widgets);
        $this->setValidators($validators);

        $this->widgetSchema->setNameFormat('contact[%s]');

        // set up your post validator method
        $this->validatorSchema->setPostValidator(
                new sfValidatorCallback(array(
                    'callback' => array($this, 'postValidation')
                ))
        );
    }

    public function getContactDetailsWidgets() {
        $countries = $this->getCountryList();
        
        $states = $this->getStatesList();
        $widgets = array();
        
        //creating widgets current address
        $widgets['current_address'] = new ohrmWidgetDiv();
        $widgets['country'] = new sfWidgetFormSelect(array('choices' => $countries));
        $widgets['state'] = new sfWidgetFormSelect(array('choices' => $states));
        $widgets['street1'] = new sfWidgetFormInput();
        $widgets['street2'] = new sfWidgetFormInput();
        $widgets['city'] = new sfWidgetFormInput();
        $widgets['province'] = new sfWidgetFormInput();
        $widgets['emp_zipcode'] = new sfWidgetFormInput();
        $widgets['emp_hm_telephone'] = new sfWidgetFormInput();
        $widgets['emp_mobile'] = new sfWidgetFormInput();
        $widgets['emp_work_telephone'] = new sfWidgetFormInput();
        $widgets['emp_work_email'] = new sfWidgetFormInput();
        $widgets['emp_oth_email'] = new sfWidgetFormInput();
        
        //creating permanent address
        $widgets['permanent_address'] = new ohrmWidgetDiv();
        $widgets['checkbox']=new sfWidgetFormInputCheckbox();
        $widgets['permanent_country'] = new sfWidgetFormSelect(array('choices' => $countries));
        $widgets['permanent_state'] = new sfWidgetFormSelect(array('choices' => $states));
        $widgets['permanent_street1'] = new sfWidgetFormInput();
        $widgets['permanent_street2'] = new sfWidgetFormInput();
        $widgets['permanent_city'] = new sfWidgetFormInput();
        $widgets['permanent_province'] = new sfWidgetFormInput();
        $widgets['permanent_emp_zipcode'] = new sfWidgetFormInput();
        

        //setting the default values
        $widgets['permanent_address']->setDefault("Permanent Address");
        $widgets['current_address']->setDefault("Current Address");
        if($this->employee->country){
           $widgets['country']->setDefault($this->employee->country); 
        }else{
            $widgets['country']->setDefault('IN');
        }
        
        $widgets['state']->setDefault($this->employee->province);
        $widgets['street1']->setDefault($this->employee->street1);
        $widgets['street2']->setDefault($this->employee->street2);
        $widgets['city']->setDefault($this->employee->city);
        $widgets['province']->setDefault($this->employee->province);
        $widgets['emp_zipcode']->setDefault($this->employee->emp_zipcode);
        if($this->employee->permanent_coun_code){
         $widgets['permanent_country']->setDefault($this->employee->permanent_coun_code);
        }  else {
           $widgets['permanent_country']->setDefault('IN'); 
        }
        if($this->employee->c_p_same_address){
          $widgets['checkbox']->setAttribute('checked', 'checked');
        }
       
        $widgets['permanent_street1']->setDefault($this->employee->permanent_street1);
        $widgets['permanent_street2']->setDefault($this->employee->permanent_street2);
        $widgets['permanent_city'] ->setDefault($this->employee->permanent_city_code);
        $widgets['permanent_province'] ->setDefault($this->employee->permanent_provin_code);
        $widgets['permanent_emp_zipcode'] ->setDefault($this->employee->permanent_zipcode);
        
        $widgets['emp_hm_telephone']->setDefault($this->employee->emp_hm_telephone);
        $widgets['emp_mobile']->setDefault($this->employee->emp_mobile);
        $widgets['emp_work_telephone']->setDefault($this->employee->emp_work_telephone);
        $widgets['emp_work_email']->setDefault($this->employee->emp_work_email);
        $widgets['emp_oth_email']->setDefault($this->employee->emp_oth_email);
        
        return $widgets;
    }

    public function getContactDetailsValidators() {
        $validators = array(
            'country' => new sfValidatorString(array('required' => false, 'trim' =>true)),
            'state' =>  new sfValidatorString(array('required' => false, 'trim' =>true)),
            'street1' => new sfValidatorString(array('required' => false,'trim'=>true)),
            'street2' => new sfValidatorString(array('required' => false,'trim'=>true)),
            'city' => new sfValidatorString(array('required' => false,'trim'=>true)),
            'province' => new sfValidatorString(array('required' => false,'trim'=>true)),
            'emp_zipcode' => new sfValidatorString(array('required' => false,'trim'=>true)),
            'checkbox' => new sfValidatorPass(array('required' => false)),
            'permanent_country' => new sfValidatorString(array('required' => false, 'trim' =>true)),
            'permanent_state' =>  new sfValidatorString(array('required' => false, 'trim' =>true)),
            'permanent_street1' => new sfValidatorString(array('required' => false,'trim'=>true)),
            'permanent_street2' => new sfValidatorString(array('required' => false,'trim'=>true)),
            'permanent_city' => new sfValidatorString(array('required' => false,'trim'=>true)),
            'permanent_province' => new sfValidatorString(array('required' => false,'trim'=>true)),
            'permanent_emp_zipcode' => new sfValidatorString(array('required' => false,'trim'=>true)),
            
            'emp_hm_telephone' => new sfValidatorString(array('required' => false,'max_length' =>21,'trim'=>true)),
            'emp_mobile' => new sfValidatorString(array('required' => false,'min_length'=>10,'max_length'=>10,'trim'=>true)),
            'emp_work_telephone' => new sfValidatorString(array('required' => false,'max_length'=>21,'trim'=>true)),
            'emp_work_email' => new sfValidatorEmail(array('required' => false)),
            'emp_oth_email' => new sfValidatorEmail(array('required' => false)),
        );
        return $validators;
    }

    public function postValidation($validator, $values) {

        $emails = $this->getEmailList();

        $errorList = array();
        $emailList = array();
        foreach ($emails as $email) {
            if ($email['empNo'] == $this->empNumber) {
                continue;
            }
            if ($email['workEmail']) {
                $emailList[] = $email['workEmail'];
            }
            if ($email['othEmail']) {
                $emailList[] = $email['othEmail'];
            }
        }
        if ($values['emp_work_email'] != "" && $values['emp_oth_email'] != "") {
            if ($values['emp_work_email'] == $values['emp_oth_email']) {
                $errorList['emp_oth_email'] = new sfValidatorError($validator, __("This email already exists"));
            }
        }
        if (in_array($values['emp_work_email'], $emailList)) {
            $errorList['emp_work_email'] = new sfValidatorError($validator, __("This email already exists"));
        }
        if (in_array($values['emp_oth_email'], $emailList)) {
            $errorList['emp_oth_email'] = new sfValidatorError($validator, __("This email already exists"));
        }
        if (count($errorList) > 0) {

            throw new sfValidatorErrorSchema($validator, $errorList);
        }
        return $values;
    }

    /**
     * Get Employee object
     */
    public function getEmployee() {
        
        $employee = $this->getEmployeeService()->getEmployee($this->getValue('empNumber'));
        
        $employee->street1 = $this->getValue('street1');
        $employee->street2 = $this->getValue('street2');
        $employee->city = $this->getValue('city');
        $employee->country = $this->getValue('country');

        $province = $this->getValue('province');
        if ($employee->country == "US") {
            $province = $this->getValue('state');
        }

        $employee->province = $province;
        $employee->emp_zipcode = $this->getValue('emp_zipcode');
        if($this->getValue('checkbox') == 'on'){
            $employee->c_p_same_address=1;
        }else{
             $employee->c_p_same_address=0;
        }
        $employee->permanent_street1 = $this->getValue('permanent_street1');
        $employee->permanent_street2 = $this->getValue('permanent_street2');
        $employee->permanent_city_code = $this->getValue('permanent_city');
        $employee->permanent_coun_code = $this->getValue('permanent_country');
        $employee->permanent_provin_code =  $this->getValue('permanent_province');
        $employee->permanent_zipcode = $this->getValue('permanent_emp_zipcode');
        
        
        $employee->emp_hm_telephone = $this->getValue('emp_hm_telephone');
        $employee->emp_mobile = $this->getValue('emp_mobile');
        $employee->emp_work_telephone = $this->getValue('emp_work_telephone');
        $employee->emp_work_email = $this->getValue('emp_work_email');
        $employee->emp_oth_email = $this->getValue('emp_oth_email');
        

        return $employee;
        
    }

    /**
     * Returns Country Service
     * @returns CountryService
     */
    public function getCountryService() {
        if (is_null($this->countryService)) {
            $this->countryService = new CountryService();
        }
        return $this->countryService;
    }

    /**
     * Returns Country List
     * @return array
     */
    private function getCountryList() {
        $list = array(0 => "-- " . __('Select') . " --");
        $countries = $this->getCountryService()->getCountryList();
        foreach ($countries as $country) {
            $list[$country->cou_code] = $country->cou_name;
        }
        return $list;
    }

    /**
     * Returns States List
     * @return array
     */
    private function getStatesList() {
        $list = array("" => "-- " . __('Select') . " --");
        $states = $this->getCountryService()->getProvinceList();
        foreach ($states as $state) {
            $list[$state->province_code] = $state->province_name;
        }
        return $list;
    }

    /**
     * Returns email List
     * @return array
     */
    public function getEmailList() {
        $list = array();
        $emailList = $this->getEmployeeService()->getEmailList();
        foreach ($emailList as $k => $email) {
            $list[] = array('empNo' => $email['empNumber'], 'workEmail' => $email['emp_work_email'], 'othEmail' => $email['emp_oth_email']);
        }
        return $list;
    }

}

?>