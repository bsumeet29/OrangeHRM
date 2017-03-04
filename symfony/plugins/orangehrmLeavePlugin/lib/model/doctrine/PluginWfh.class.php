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
abstract class PluginWfh extends BaseWfh
{
    const WFH_STATUS_PENDING = 0;
    const WFH_STATUS_CANCELLED = 1;
    const WFH_STATUS_APPROVED = 2;
    /*
     * Changes by Rahul
     */
     const WFH_STATUS_WFH_WEEKEND = 3;
    const WFH_STATUS_WFH_HOLIDAY = 4;
     private static $nonWorkingDayStatuses = array(
        self::WFH_STATUS_WFH_WEEKEND,
        self::WFH_STATUS_WFH_HOLIDAY,
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
        self::WFH_STATUS_CANCELLED => 'Cancelled',
        self::WFH_STATUS_PENDING => 'Pending approval',
        self::WFH_STATUS_APPROVED => 'Scheduled',
              
    );
    private static $wfhStatusList;
    
    protected static function getWfhStatusList(){
        if(empty(self::$StatusList)){
            self::$wfhStatusList=array(
                0=>'Pending',
                1=>'Cancelled',
                2=>'Approved',
            );
        }
        return self::$wfhStatusList;
    }
    public function getTextWfhStatus() {
        
        $status = $this->getStatus();
        
        // check in user defined statuses
        $statusList = self::getWfhStatusList();
        if (array_key_exists($status, $statusList)) {            
            return $statusList[$status];
        }
                        
        if (array_key_exists($status, self::$wfhStatusText)) {            
            return self::$wfhStatusText[$status];
        }        
        
        return '';
    }
    public static function getTextForWfhStatus($status) {
        
        // check in user defined statuses
        $statusList = self::getWfhStatusList();
        if (array_key_exists($status, $statusList)) {            
            return $statusList[$status];
        }        
        
        if (array_key_exists($status, self::$wfhStatusText)) {            
            return self::$wfhStatusText[$status];
        }        

        return '';        
    }
     public static function getWfhStatusForText($status) {
        $statusList = self::getWfhStatusList();
        $statusInt = array_search($status, $statusList);
            
        if ($statusInt === false) {
            $statusInt = array_search($status, self::$wfhStatusText);
             
        }
       
        if ($statusInt === false) {
            
            return null;
        } else {
            
            return $statusInt;
        }
    }
     public static function getStatusTextList() {
        $statusList = self::getWfhStatusList();
        $workingStatuses = array();
        
        // filter out holidays
        foreach ($statusList as $key => $status) {
            if (!in_array($key, self::$nonWorkingDayStatuses)) {   //here changed !in_array to in_array to filter working days
                $workingStatuses[$key] = $status;
            }
        }
                        
        $wfhStatuses = array_map('strtolower', $workingStatuses);
        $wfhStatuses = array_map('ucwords', $wfhStatuses);
        return $wfhStatuses;
    }
    public static function getPendingWfhStatusList() {
        $pendingStatusList = array();
        $statusList = self::getWfhStatusList();
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
    public function getWfhDurationAsAString() {

        if ($this->getStartTime() != '00:00:00' || $this->getEndTime() != '00:00:00') {
            return "(" . (date("H:i", strtotime($this->getStartTime()))) . " - " . date("H:i", strtotime($this->getEndTime())) . ")";
        } else {
            return '';
        }
    }
    public function getFormattedWfhDateToView() {
        
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
        $wfhComments = $this-> getWfhComment() ;
        
        if (count($wfhComments) > 0) {
            $lastComment = $wfhComments->getLast();
            $latestComment = $lastComment->getComments();
        }
        
        return $latestComment;
    }
    public function getCommentsAsText() {
        $wfhComments = $this->getWfhComment();
        
        $allComments = '';
                
        // show last comment only
        if (count($wfhComments) > 0) {
            
            foreach ($wfhComments as $comment) {
                $created = new DateTime($comment->getCreated());
                $createdAt = set_datepicker_date_format($created->format('Y-m-d'));// . ' ' . $created->format('H:i');
                
                $formatComment = $createdAt . ' ' . $comment->getCreatedByName() . " " .
                        $comment->getComments();
                $allComments = $formatComment . " " . $allComments;
            }
        }
        
        return $allComments;
    }
    public function getDetailedWfhListQuotaHolderValue() {
        return "1";
    }

    public function getDetailedWfhListRequestIdHolderValue() {
        return "0";
    }
}