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
class AddEmployeeForm extends sfForm {

    private $employeeService;
    private $userService;
    private $widgets = array();
    public $createUserAccount = 1;

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

    private function getUserService() {

        if (is_null($this->userService)) {
            $this->userService = new SystemUserService();
        }

        return $this->userService;
    }

    /**
     * Set EmployeeService
     * @param EmployeeService $employeeService
     */
    public function setEmployeeService(EmployeeService $employeeService) {
        $this->employeeService = $employeeService;
    }

    public function configure() {

        $status = array('Enabled' => __('Enabled'), 'Disabled' => __('Disabled'));
        $paymentMode=array(''=>__('Select'),'cheque'=>__('Cheque'),'hdfc'=>__('HDFC'));
        $idGenService = new IDGeneratorService();
        $idGenService->setEntity(new Employee());
        $empNumber = $idGenService->getNextID(false);
        $employeeId = str_pad($empNumber, 4, '0');
        
        $employeeId='P'.ltrim($employeeId, '0');

        $this->widgets = array(
            'firstName' => new sfWidgetFormInputText(array(), array("class" => "formInputText", "maxlength" => 20)),
            'middleName' => new sfWidgetFormInputText(array(), array("class" => "formInputText", "maxlength" => 20)),
            'lastName' => new sfWidgetFormInputText(array(), array("class" => "formInputText", "maxlength" => 20)),
            'mothersName' => new sfWidgetFormInputText(array(), array("class" => "formInputText", "maxlength" => 20)),
             
//            'email'=>new sfWidgetFormInputText(),
            'employeeId' => new sfWidgetFormInputText(array(), array("class" => "formInputText loginSection", "maxlength" => 10,)),
            'photofile' => new sfWidgetFormInputFileEditable(array('edit_mode' => false, 'with_delete' => false, 
                'file_src' => ''), array("class" => "duplexBox")),
            'chkLogin' => new sfWidgetFormInputCheckbox(array('value_attribute_value' => 1), array()),
            'user_name' => new sfWidgetFormInputText(array(), array("class" => "formInputText")),
            'user_password' => new sfWidgetFormInputText(array(), array("class" => "formInputText passwordRequired"
               )),
            're_password' => new sfWidgetFormInputText(array(), array("class" => "formInputText passwordRequired"
                )),
            'status' => new sfWidgetFormSelect(array('choices' => $status), array("class" => "formInputText")),            
            'empNumber' => new sfWidgetFormInputHidden(),
            'paymentMode' => new sfWidgetFormSelect(array('choices' => $paymentMode), array("class" => "formInputText"))
        );

        $this->widgets['empNumber']->setDefault($empNumber);
        $this->widgets['employeeId']->setDefault($employeeId);

        if ($this->getOption(('employeeId')) != "") {
            $this->widgets['employeeId']->setDefault($this->getOption(('employeeId')));
        }

        $this->widgets['firstName']->setDefault($this->getOption('firstName'));
        $this->widgets['middleName']->setDefault($this->getOption('middleName'));
        $this->widgets['lastName']->setDefault($this->getOption('lastName'));
        $this->widgets['mothersName']->setDefault($this->getOption('mothersName'));
//        $this->widgets['email']->setDefault($this->getOption('email'));
        $this->widgets['chkLogin']->setDefault($this->getOption('chkLogin'));
        $password=  uniqid(rand(4, 4));
        $this->widgets['user_name']->setDefault($this->getOption('user_name'));
        $this->widgets['user_password']->setDefault($password);
        $this->widgets['re_password']->setDefault($password);
        
        $selectedStatus = $this->getOption('status');
        if (empty($selectedStatus) || !isset($status[$selectedStatus])) {
            $selectedStatus = 'Enabled';
        }
       
        $this->widgets['status']->setDefault($selectedStatus);

        $this->setWidgets($this->widgets);

        $this->setValidators(array(
            'photofile' => new sfValidatorFile(array('max_size' => 1000000, 'required' => false)),
            'firstName' => new sfValidatorString(array('required' => true, 'max_length' => 30, 'trim' => true)),
            'empNumber' => new sfValidatorString(array('required' => false)),
            'lastName' => new sfValidatorString(array('required' => true, 'max_length' => 30, 'trim' => true)),
            'mothersName' => new sfValidatorString(array('required' => false)),
            'paymentMode'=>new sfValidatorString(array('required' => false)),
            'middleName' => new sfValidatorString(array('required' => false, 'max_length' => 30, 'trim' => true)),
            'employeeId' => new sfValidatorString(array('required' => false, 'max_length' => 10)),
            'chkLogin' => new sfValidatorString(array('required' => false)),
            'user_name' => new sfValidatorString(array('required' => true, 'trim' => true)),
            'user_password' => new sfValidatorString(array('required' => true,  'trim' => true)),
            're_password' => new sfValidatorString(array('required' => true,  'trim' => true)),
            'status' => new sfValidatorString(array('required' => false))
        ));

        $this->getWidgetSchema()->setLabels($this->getFormLabels());

        $formExtension = PluginFormMergeManager::instance();
        $formExtension->mergeForms($this, 'addEmployee', 'AddEmployeeForm');
        
        
        $customRowFormats[0] = "<li class=\"line nameContainer\"><label class=\"hasTopFieldHelp\">". __('Full Name') . "</label><ol class=\"fieldsInLine\"><li><div class=\"fieldDescription\"><em>*</em> ". __('First Name') . "</div>\n %field%%help%\n%hidden_fields%%error%</li>\n";
        $customRowFormats[1] = "<li><div class=\"fieldDescription\">". __('Middle Name') . "</div>\n %field%%help%\n%hidden_fields%%error%</li>\n";
        $customRowFormats[2] = "<li><div class=\"fieldDescription\"><em>*</em> ". __('Last Name') . "</div>\n %field%%help%\n%hidden_fields%%error%</li>\n</ol>\n</li>";
       
        $customRowFormats[6] = "<li class=\"loginSection\">%label%\n %field%%help%\n%hidden_fields%%error%</li>\n";
        $customRowFormats[7] = "<li class=\"\">%label%\n %field%%help%\n%hidden_fields%%error%</li>\n";
        $customRowFormats[8] = "<li class=\"loginSection\">%label%\n %field%%help%\n%hidden_fields%%error%</li>\n";
        $customRowFormats[9] = "<li class=\"loginSection\">%label%\n %field%%help%\n%hidden_fields%%error%</li>\n";
        $customRowFormats[5] = "<li><label class=\"\">". __('Photograph') . "</label><div class=\"\"></div>\n %field%%help%\n%hidden_fields%%error%</li>\n";
        sfWidgetFormSchemaFormatterCustomRowFormat::setCustomRowFormats($customRowFormats);
        $this->widgetSchema->setFormFormatterName('CustomRowFormat');
        
    }

    /**
     *
     * @return array
     */
    protected function getFormLabels() {
        $labels = array(
            'photofile' => __('Photograph'),
            'fullNameLabel' => __('Full Name'),
            'firstName' => false,
            'middleName' => false,
            'lastName' => false,
            'paymentMode'=>__('Payment Mode') . '<em> *</em>',
            'employeeId' => false,
            'chkLogin' => __('Create Login Details'),
            'user_name' => __('Email') . '<em> *</em>',
            'user_password' => __('Password') . '<em id="password_required"> *</em>',
            're_password' => __('Confirm Password') . '<em id="rePassword_required"> *</em>',
            'status' => __('Status') . '<em> *</em>'
        );

        return $labels;
    }
    
    public function getEmployee(){
        $posts = $this->getValues();
        
        $employee = new Employee();
        $employee->firstName = $posts['firstName'];
        $employee->lastName = $posts['lastName'];
        $employee->middleName = $posts['middleName'];
        $employee->employeeId = $posts['employeeId'];
        $employee->mothersName = $posts['mothersName'];
        $employee->emp_work_email=$posts['user_name'];
        $employee->payment_mode=$posts['paymentMode'];
        
        return $employee;
    }

    public function save() {

        $posts = $this->getValues();
        $file = $posts['photofile'];
        $employee = $this->getEmployee();

        $employeeService = $this->getEmployeeService();
        $employeeService->saveEmployee($employee);

        $empNumber = $employee->empNumber;

        //saving emp picture
        if (($file instanceof sfValidatedFile) && $file->getOriginalName() != "") {
            $empPicture = new EmpPicture();
            $empPicture->emp_number = $empNumber;
            $tempName = $file->getTempName();

            $empPicture->picture = file_get_contents($tempName);
            
            $empPicture->filename = $file->getOriginalName();
            $empPicture->file_type = $file->getType();
            $empPicture->size = $file->getSize();
            list($width, $height) = getimagesize($file->getTempName());
            $sizeArray = $this->pictureSizeAdjust($height, $width);
            $empPicture->width = $sizeArray['width'];
            $empPicture->height = $sizeArray['height'];
            $empPicture->save();
        }

        if ($this->createUserAccount) {
            $this->saveUser($empNumber);
        }

        //merge location dropdown
        $formExtension = PluginFormMergeManager::instance();
        $formExtension->saveMergeForms($this, 'addEmployee', 'AddEmployeeForm');

        return $empNumber;
    }

    private function saveUser($empNumber) {

        $posts = $this->getValues();
        
        if (trim($posts['user_name']) != "") {
            $userService = $this->getUserService();

            if (trim($posts['user_password']) != "" && $posts['user_password'] == $posts['re_password']) {
                $user = new SystemUser();
                $user->setDateEntered(date('Y-m-d H:i:s'));
                $user->setCreatedBy(sfContext::getInstance()->getUser()->getAttribute('user')->getUserId());
                $user->user_name = $posts['user_name'];
                $user->user_password = $posts['user_password'];
                $user->emp_number = $empNumber;
                $user->setStatus(($posts['status'] == 'Enabled') ? '1' : '0');
                $user->setUserRoleId(2);
                $userService->saveSystemUser($user, true);
                
                $to= $posts['user_name'];
                $subject="Welcome to Perennial Systems HRMS Portal";
                $mailMessage=" Dear ".$posts['firstName']." ".$posts['lastName'].",<br/><br/>";
		$mailMessage.="Greeting & Welcome to Perennial Systems HRMS Portal..!!!!!!  <br/><br/>";
                $mailMessage.="This portal provides you access to your Personal as well as Professional information. You can access the application through <br/><br/>";
                $mailMessage.='http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'].'/auth/login<br /><br />';
                $mailMessage.="Your login credentials are as follows: <br /><br />";
		$mailMessage .= "<b>User Name : </b> ". $posts['user_name']."<br/><br/>";
		$mailMessage .= "<b>Password  : </b> ".$posts['user_password']."<br/><br/>";
                
                $mailMessage.="If you have any trouble logging in or using the application, write to us at <a href='mailto:hr@perennialsys.com' target='_blank'>hr@perennialsys.com</a><br/>";
                $mailMessage .= 'We will revert to you and help you with your queries.
                                 <br/><br />We hope you have a great experience using the application.<br /><br />
                                 Note: This is an auto-generated mail. Please do not reply.<br /><br />
                                 Regards<br/>';
                $mailMessage .= 'HR Team';
                $emailService = new EmailService();
                $emailService->sendEmailNotificationByAction($to,$subject,$mailMessage);
            }
            
            $this->_handleLdapEnabledUser($posts, $empNumber);            
        }
    }

    private function pictureSizeAdjust($imgHeight, $imgWidth) {

        if ($imgHeight > 200 || $imgWidth > 200) {
            $newHeight = 0;
            $newWidth = 0;

            $propHeight = floor(($imgHeight / $imgWidth) * 200);
            $propWidth = floor(($imgWidth / $imgHeight) * 200);

            if ($propHeight <= 200) {
                $newHeight = $propHeight;
                $newWidth = 200;
            }

            if ($propWidth <= 200) {
                $newWidth = $propWidth;
                $newHeight = 200;
            }
        } else {
            if ($imgHeight <= 200)
                $newHeight = $imgHeight;

            if ($imgWidth <= 200)
                $newWidth = $imgWidth;
        }
        return array('width' => $newWidth, 'height' => $newHeight);
    }

    protected function _handleLdapEnabledUser($postedValues, $empNumber) {
        
        $sfUser = sfContext::getInstance()->getUser();
        
        $password           = $postedValues['user_password'];
        $confirmedPassword  = $postedValues['re_password'];
        $check1             = (empty($password) && empty($confirmedPassword))?true:false;
        $check2             = $sfUser->getAttribute('ldap.available');
        
        if ($check1 && $check2) {

            $user = new SystemUser();
            $user->setDateEntered(date('Y-m-d H:i:s'));
            $user->setCreatedBy($sfUser->getAttribute('user')->getUserId());
            $user->user_name = $postedValues['user_name'];
            $user->user_password = '';
            $user->emp_number = $empNumber;
            $user->setUserRoleId(2);
            $this->getUserService()->saveSystemUser($user, true);            
            
        }
        
    }    
}