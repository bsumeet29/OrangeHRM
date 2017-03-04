<?php

/**
 * PluginCompOff
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class PluginCompOff extends BaseCompOff
{
    const COMPOFF_STATUS_PENDING = 0;
    const COMPOFF_STATUS_CANCELLED = 1;
    const COMPOFF_STATUS_APPROVED = 2;
    /*
     * Changes by Rahul
     */
     const COMPOFF_STATUS_COMPOFF_WEEKEND = 3;
    const COMPOFF_STATUS_COMPOFF_HOLIDAY = 4;
     private static $nonWorkingDayStatuses = array(
        self::COMPOFF_STATUS_COMPOFF_WEEKEND,
        self::COMPOFF_STATUS_COMPOFF_HOLIDAY,
    );
//    const COMP_OFF_STATUS_COMP_OFF_TYPE_DELETED_TEXT = 'COMP-OFF TYPE DELETED';
//    
//    const COMP_OFF_STATUS_COMP_OFF_PENDING_APPROVAL_TEXT = 'Pending Approval';
//    
    const PENDING_APPROVAL_STATUS_PREFIX = 'Pending';
    
    const DURATION_TYPE_FULL_DAY = 0;
    const DURATION_TYPE_HALF_DAY_AM = 1;
    const DURATION_TYPE_HALF_DAY_PM = 2;
    const DURATION_TYPE_SPECIFY_TIME = 3;
     
          private static $compOffStatusText = array(
        self::COMPOFF_STATUS_CANCELLED => 'Cancelled',
        self::COMPOFF_STATUS_PENDING => 'Pending approval',
        self::COMPOFF_STATUS_APPROVED => 'Scheduled',
              
    );
    private static $compOffStatusList;
    
    protected static function getCompOffStatusList(){
        if(empty(self::$compOffStatusList)){
            self::$compOffStatusList=array(
                0=>'Pending',
                1=>'Cancelled',
                2=>'Approved',
            );
        }
        return self::$compOffStatusList;
    }
    public function getTextCompOffStatus() {
        
        $status = $this->getStatus();
        
        // check in user defined statuses
        $statusList = self::getCompOffStatusList();
        if (array_key_exists($status, $statusList)) {            
            return $statusList[$status];
        }
                        
        if (array_key_exists($status, self::$compOffStatusText)) {            
            return self::$compOffStatusText[$status];
        }        
        
        return '';
    }
    public static function getTextForCompOffStatus($status) {
        
        // check in user defined statuses
        $statusList = self::getCompOffStatusList();
        if (array_key_exists($status, $statusList)) {            
            return $statusList[$status];
        }        
        
        if (array_key_exists($status, self::$compOffStatusText)) {            
            return self::$compOffStatusText[$status];
        }        

        return '';        
    }
     public static function getCompOffStatusForText($status) {
        $statusList = self::getCompOffStatusList();
        $statusInt = array_search($status, $statusList);
            
        if ($statusInt === false) {
            $statusInt = array_search($status, self::$compOffStatusText);
             
        }
       
        if ($statusInt === false) {
            
            return null;
        } else {
            
            return $statusInt;
        }
    }
     public static function getStatusTextList() {
        $statusList = self::getCompOffStatusList();
        $workingStatuses = array();
        
        // filter out holidays
        foreach ($statusList as $key => $status) {
            if (in_array($key, self::$nonWorkingDayStatuses)) {   //here changed !in_array to in_array to filter working days
                $workingStatuses[$key] = $status;
            }
        }
                        
        $compOffStatuses = array_map('strtolower', $workingStatuses);
        $compOffStatuses = array_map('ucwords', $compOffStatuses);
        return $compOffStatuses;
    }
    public static function getPendingCompOffStatusList() {
        $pendingStatusList = array();
        $statusList = self::getCompOffStatusList();
        //var_dump($statusList);exit;
        foreach($statusList as $key => $status) {
            if (0 === strpos($status, self::PENDING_APPROVAL_STATUS_PREFIX)) {
                $pendingStatusList[$key] = $status;
            }
        }
        return $pendingStatusList;
    }
    
     public function isNonWorkingDay() {
        if (($this->getLengthHours() == 0.00) && in_array($this->getStatus(), self::$nonWorkingDayStatuses)) {
            return true;
        }
        return false;
    }
    public function getNumberOfDays() {
        return $this->getCompoffRequest()->getNumberOfDays();
    }
    public function getCompOffDurationAsAString() {

        if ($this->getStartTime() != '00:00:00' || $this->getEndTime() != '00:00:00') {
            return "(" . (date("H:i", strtotime($this->getStartTime()))) . " - " . date("H:i", strtotime($this->getEndTime())) . ")";
        } else {
            return '';
        }
    }
    public function getFormattedCompOffDateToView() {
        
        $date = set_datepicker_date_format($this->getDate());
        // check if partial leave
        $durationType = $this->getDurationType();
        
        if ($durationType != self::DURATION_TYPE_FULL_DAY) {
            $time = date('H:i', strtotime($this->getStartTime())) . ' - ' . date('H:i', strtotime($this->getEndTime()));
            $date .= ' (' . $time . ')';
            
            if (($durationType == self::DURATION_TYPE_HALF_DAY_AM) || 
                    ($durationType == self::DURATION_TYPE_HALF_DAY_PM)) {
                $date .= ' ' . __('Half Day');
            }
        }
        return $date;
    }
    public function getLatestCommentAsText() {
        $latestComment = '';
        $compOffComments = $this-> getCompoffComment() ;
        
        if (count($compOffComments) > 0) {
            $lastComment = $compOffComments->getLast();
            $latestComment = $lastComment->getComments();
        }
        
        return $latestComment;
    }
    public function getCommentsAsText() {
        $compOffComments = $this->getCompoffComment();
        
        $allComments = '';
                
        // show last comment only
        if (count($compOffComments) > 0) {
            
            foreach ($compOffComments as $comment) {
                $created = new DateTime($comment->getCreated());
                $createdAt = set_datepicker_date_format($created->format('Y-m-d'));// . ' ' . $created->format('H:i');
                
                $formatComment = $createdAt . ' ' . $comment->getCreatedByName() . " " .
                        $comment->getComments();
                $allComments = $formatComment . " " . $allComments;
            }
        }
        
        return $allComments;
    }
    public function getDetailedCompOffListQuotaHolderValue() {
        return "1";
    }

    public function getDetailedCompOffListRequestIdHolderValue() {
        return "0";
    }
}