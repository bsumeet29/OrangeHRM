<?php

class employeeListAjaxAction extends sfAction {
    
    public function execute($request) {
         sfConfig::set('sf_web_debug', false);
        sfConfig::set('sf_debug', false);
        $jsonList = array();
        $jsonList = $this->getEmployeeListAsJson();
//        $response = $this->getResponse();
//        $response->setHttpHeader('Expires','0');
//        $response->setHttpHeader("Cache-Control","must-revalidate,post-check = 0,pre-check = 0");
//        $response->setHttpHeader("Cache-Control","private",false);
        echo (json_encode($jsonList));
        
        return sfView::NONE;
        
    }
    
     private function getEmployeeListAsJson() {

        $jsonArray = array();

        $properties = array("empNumber", "firstName", "middleName", "lastName", 'termination_id');

        $requiredPermissions = array(
            BasicUserRoleManager::PERMISSION_TYPE_ACTION => array('assign_leave'));
/*if (!empty($requiredPermissions)) {
            $requiredPermissions = json_decode($requiredPermissions);
        }*/
        $employeeList = UserRoleManagerFactory::getUserRoleManager()
                ->getAccessibleEntityProperties('Employee', $properties, null, null, array(), array(), $requiredPermissions);
        $obj=new EmployeeDao();
       
        $employeeList=$obj->getEmployeeList();
        $employeeUnique = array();
        foreach ($employeeList as $employee) {
            $terminationId = $employee['termination_id'];
            $empNumber = $employee['empNumber'];
            if (!isset($employeeUnique[$empNumber]) && empty($terminationId) && $empNumber != $this->getEmployeeNumber()) {
                $name = trim(trim($employee['firstName'] . ' ' . $employee['middleName'], ' ') . ' ' . $employee['lastName']);

                //$employeeUnique[$empNumber['id']] = $name['name'];
                //$jsonArray[$name['name']] = $name['id'] ;
                array_push($jsonArray,array('name' => $name,
                    'id' => $empNumber));
            //    $jsonArray = array('name' => $name, 'id' => $empNumber);
            }
        }

       // $jsonString = json_encode($jsonArray);
       // return $jsonString;
   //$jsonArray = json_encode($jsonArray);
        return $jsonArray;
    }
    
    private function getEmployeeNumber() {
        return $_SESSION['empID'];
    }
}