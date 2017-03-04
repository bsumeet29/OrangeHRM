<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class LeaveConditionsForm extends sfForm{
     protected $configService;

    public function getConfigService() {
        
        if (!$this->configService instanceof ConfigService) {
            $this->configService = new ConfigService();
        }
        
        return $this->configService;        
    }

    public function setConfigService($configService) {
        $this->configService = $configService;
    }  
    public function configure() {

        $this->setWidgets($this->getFormWidgets());
        $this->setValidators($this->getFormValidators());

       // $this->setDefault('leaveBalance', '--');

        $this->getValidatorSchema()->setPostValidator(new sfValidatorCallback(array('callback' => array($this, 'postValidation'))));

        $this->getWidgetSchema()->setNameFormat('leaveCnditions[%s]');
        $this->getWidgetSchema()->setLabels($this->getFormLabels());

    }
    protected function getFormWidgets(){
        $mindate = date('d-m-Y');
        $widgets = array(
       'txtReviewvalDate' => new ohrmWidgetDatePicker(array(), array('id' => 'leave_txtReviewDate','minDate' => $mindate,'maxDate' => '')),
       'txtConditions' => new sfWidgetFormTextarea(array(), array('id' => 'conditions','rows' => '3', 'cols' => '30')),
     );
        return $widgets;
    }
    protected function getFormValidators(){
    $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
    $validators = array(
        'txtReviewvalDate' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => true),
                    array('invalid' => 'Date format should be ' . $inputDatePattern)),
         'txtConditions' => new sfValidatorString(array('required' => false, 'trim' => true, 'max_length' => 1000)),
    );
    return $validators;
    }
    protected function getFormLabels(){
       $labels = array('txtReviewvalDate' => __('Date for Reviewval'),
              'txtConditions' => __('Conditions'), 
           );
       return $labels;
    }
     public function getStylesheets() {
        $styleSheets = parent::getStylesheets();

        return $styleSheets;
    }
    
    public function getJavaScripts() {
        $javaScripts = parent::getJavaScripts();
        $javaScripts[] = plugin_web_path('orangehrmLeavePlugin', 'js/viewLeaveListSuccess.js');

        return $javaScripts;
    }
    public function postValidation($validator,$values){
        $reviewTimeStamp = strtotime($values['txtReviewvalDate']);
        $values['txtReviewvalDate'] = date('Y-m-d', $reviewTimeStamp);
        
        return $values;
    }
}
