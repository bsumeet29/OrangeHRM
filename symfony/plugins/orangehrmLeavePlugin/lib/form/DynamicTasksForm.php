<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class DynamicTasksForm extends sfForm{
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
    public function configure($text) {

        $this->setWidgets($this->getFormWidgets($text));
        $this->setValidators($this->getFormValidators($text));

       // $this->setDefault('leaveBalance', '--');

        //$this->getValidatorSchema()->setPostValidator(new sfValidatorCallback(array('callback' => array($this, 'postValidation'))));

        $this->getWidgetSchema()->setNameFormat('leaveCnditions[%s]');
        $this->getWidgetSchema()->setLabels($this->getFormLabels());

    }
    protected function getFormWidgets($text){
        $widgets = array(
       $text => new ohrmWidgetEmployeeNameAutoFill(array('jsonList' => $this->getEmployeeListAsJson())),
       
     );
        return $widgets;
    }
    protected function getFormValidators($text){
    $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
    $validators = array(
       $text => new ohrmValidatorEmployeeNameAutoFill(),
    );
    return $validators;
    }
    protected function getFormLabels(){
       $labels = array('txtContactPerson' => __('Contact Person'),
           );
       return $labels;
    }
     public function getStylesheets() {
        $styleSheets = parent::getStylesheets();

        return $styleSheets;
    }
    
    public function getJavaScripts() {
        $javaScripts = parent::getJavaScripts();
        $javaScripts[] = plugin_web_path('orangehrmLeavePlugin', 'js/viewLeaveRequestSuccess.js');

        return $javaScripts;
    }
   /* public function postValidation($validator,$values){
        $reviewTimeStamp = strtotime($values['txtReviewvalDate']);
        $values['txtReviewvalDate'] = date('Y-m-d', $reviewTimeStamp);
        
        return $values;
    }*/
}
