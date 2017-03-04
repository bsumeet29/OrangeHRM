<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of deleteLeaveHistoryAction
 *
 * @author firoj
 */
class deleteWfhHistoryAction extends baseCoreLeaveAction {
   
    
  
      public function execute($request) {
    
        $form = new DefaultListForm();
        $form->bind($request->getParameter($form->getName()));
        $ids = $request->getParameter('chkSelectRow');
       
        if (count($ids) > 0) {
            try{
                if ($form->isValid()) {
                    $this->getWfhRequestService()->deleteWfhHistory($ids);
                     
                  
                    $this->getUser()->setFlash('success', __(TopLevelMessages::DELETE_SUCCESS));
                }
            }catch(Exception $e){
                $this->getUser()->setFlash('warning.nofade',$e->getMessage());
            }
        } else {
            $this->getUser()->setFlash('warning', __(TopLevelMessages::SELECT_RECORDS));
            
        }
        
        $this->redirect('leave/viewWfhList?reset=1');
    }
//    public function execute($request) {
//        print_r($request);
//       $this->redirect('leave/viewLeaveList');
//        
//    }    
}

?>
