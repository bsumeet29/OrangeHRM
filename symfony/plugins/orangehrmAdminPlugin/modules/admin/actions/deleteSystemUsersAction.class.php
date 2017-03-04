<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of deleteProjectAction
 *
 * @author orangehrm
 */
class deleteSystemUsersAction extends sfAction {

	private $systemUserService ;
        
        public function getSystemUserService() {
            $this->systemUserService = new SystemUserService();
            return $this->systemUserService;
        }
 public function getEmployeeService() {
        if(is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
            $this->employeeService->setEmployeeDao(new EmployeeDao());
        }
        return $this->employeeService;
    }

        
	/**
	 *
	 * @param <type> $request
	 */
	public function execute($request) {
                $form = new DefaultListForm();
                $form->bind($request->getParameter($form->getName()));
		$toBeDeletedUserIds = $request->getParameter('chkSelectRow');
                

		if (!empty($toBeDeletedUserIds)) {
                    if ($form->isValid()) {
                        
                        $accessibleIds = $this->getContext()->getUserRoleManager()->getAccessibleEntityIds('SystemUser');
                        
                        $delete = true;
                        foreach ($toBeDeletedUserIds as $id) {
                            if (!in_array($id, $accessibleIds)) {
                                $delete = false;
                                break;
                            }
                        }
			if (!$delete) {
                            $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
                        }
                        $empArray = array();
                        $ids = $this->getSystemUserService()->getEmpNumbers($toBeDeletedUserIds);
                        foreach ($ids as $id){
                            $empArray[] = $id['emp_number'];
                        }
                        $employeeService = $this->getEmployeeService();
                        foreach($empArray as $emp){
                    $empList[] = $employeeService->getEmployee($emp);
                }
                        
                        $count = $employeeService->deleteEmployees($empArray);
//                        $this->getSystemUserService()->deleteSystemUsers($ids);
                        if (count($count) == count($ids)) {
                    $emailService = new EmailService();
                    foreach ($empList as $employee) {
                        $subject="Your perennial systems HRMS portal account has been deleted"; 
                        $mailMessage = "Hi &nbsp;".$employee->getFirstName().",<br/><br/>";
                        $mailMessage.="Admin has deleted your perennial systems HRMS portal account. If you have any queries please contact Admin.";
                        $mailMessage .= '<br/>';
                        $mailMessage .= '<br/>Best regards,<br/>';
                        $mailMessage .= '<br/>Admin';
                        $emailService->sendEmailNotificationByAction($employee->getEmpWorkEmail(),$subject,$mailMessage);

                    }
                
                        $this->getUser()->setFlash('success', __(TopLevelMessages::DELETE_SUCCESS));
                    }
			
		}else{
                    $this->getUser()->setFlash('warning', __(TopLevelMessages::SELECT_RECORDS));
                }

		$this->redirect('admin/viewSystemUsers');
	}

}
}
?>
