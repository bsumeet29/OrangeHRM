<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
class CompoffExtensionForm extends sfForm{
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

        $this->getWidgetSchema()->setNameFormat('compoffExtension[%s]');
        $this->getWidgetSchema()->setLabels($this->getFormLabels());

    }
    protected function getFormWidgets(){
         $mindate = date('d-m-Y');
         $maxdate = date('d-m-Y', strtotime('+1 year'));
        $widgets = array(
       'txtExtensionDate' => new ohrmWidgetDatePicker(array(), array('id' => 'leave_txtExtensionDate','minDate' => $mindate,'maxDate' => $maxdate)),
       'txtComments' => new sfWidgetFormTextarea(array(), array('id' => 'comments','rows' => '3', 'cols' => '30')),
     );
        return $widgets;
    }
    protected function getFormValidators(){
    $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
    $validators = array(
        'txtExtensionDate' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => true),
                    array('invalid' => 'Date format should be ' . $inputDatePattern)),
         'txtComments' => new sfValidatorString(array('required' => true, 'trim' => true, 'max_length' => 1000)),
    );
    return $validators;
    }
    protected function getFormLabels(){
        $requiredMarker = ' <em>*</em>';
       $labels = array('txtExtensionDate' => __('Date for Extension').$requiredMarker,
              'txtComments' => __('Comments').$requiredMarker, 
           );
       return $labels;
    }
     public function getStylesheets() {
        $styleSheets = parent::getStylesheets();

        return $styleSheets;
    }
    
    public function getJavaScripts() {
        $javaScripts = parent::getJavaScripts();
       // $javaScripts[] = plugin_web_path('orangehrmLeavePlugin', 'js/viewLeaveListSuccess.js');

        return $javaScripts;
    }
    public function postValidation($validator,$values){
        $reviewTimeStamp = strtotime($values['txtExtensionDate']);
        $values['txtExtensionDate'] = date('Y-m-d', $reviewTimeStamp);
        
        return $values;
    }
}
