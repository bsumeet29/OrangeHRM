<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CompOffDao
 *
 * @author firoj
 */
class CompOffDao extends BaseDao {
    //put your code here
    
     public function saveCompOff(CompOff $compOff){
        try {
            $compOff->save();
            return $compOff;
         } catch (Exception $e) {
            throw new DaoException($e->getMessage());
            }
      }
}

?>
