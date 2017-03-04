<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of getLatestEmployeeIdAjaxAction
 *
 * @author firoj
 */
class getLatestEmployeeIdAjaxAction extends sfAction{
    private $employeeService;
    public function getEmployeeService() {
        if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
            $this->employeeService->setEmployeeDao(new EmployeeDao());
        }
        return $this->employeeService;
    }

    public function setEmployeeService($employeeService) {
        $this->employeeService = $employeeService;
    }

        
    public function execute($request) {
       
        $mode=$request->getParameter('paymentMode');
        $employeeList=$this->getEmployeeService()->searchEmployee('payment_mode', $mode);
        $emplyoeeIds=array();
        foreach ($employeeList as $employee) {
             $emplyoeeIds[]= ltrim($employee->getEmployeeId(), 'P');
        }
        if(!empty($emplyoeeIds) && is_array($emplyoeeIds)){
            $latestNumber= max($emplyoeeIds)+1;
        }else{
            
            $latestNumber= ($mode == 'hdfc')? 1:935;
        }
        echo str_pad($latestNumber, 3, "0", STR_PAD_LEFT);
        
        exit();
        
        
    }    


}

?>
