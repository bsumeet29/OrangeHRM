<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of sampleAction
 *
 * @author firoj
 */
class sampleAction  extends sfAction{
    public function execute($request) {
        $obj=new getLeaveBalanceAjaxAction("2","74");
        print_r($obj->getResponse()) ;
    }    //put your code here
}

?>
