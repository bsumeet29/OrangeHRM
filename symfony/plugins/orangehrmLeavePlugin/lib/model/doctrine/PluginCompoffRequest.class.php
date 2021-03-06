<?php

/**
 * PluginCompoffRequest
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
/* 
 * changes by rahul 
 */
abstract class PluginCompoffRequest extends BaseCompoffRequest
{
     private $compOff = null;
    private $compOffCount = null;
    private $numberOfDays = null;
    private $compOffDuration = null;
    private $statusCounter = array();
   // private $workShiftHoursPerDay = null;
    const COMPOFF_STATUS_DIFFER=-2;
    public function getNumberOfDays() {
        $this->_fetchCompOff();
        return number_format($this->numberOfDays, 2);
    }
    private function getStatusCounter() {
        return $this->statusCounter;
    }
   public function getEmployeeService() {
        if (is_null($this->employeeService)) {
            $empService = new EmployeeService();
            $empService->setEmployeeDao(new EmployeeDao());
            $this->employeeService = $empService;
        }
        return $this->employeeService;
    }
public function getCompOffDuration() {

        if ($this->compOffCount == 1) {
            $startTime = $this->compOff[0]->getStartTime();
            $endTime = $this->compOff[0]->getEndTime();

            if ((!empty($startTime) && !empty($endTime)) && ("{$startTime} {$endTime}" != '00:00:00 00:00:00')) {
                return "{$startTime} to {$endTime}";
            } else {
                $totalDuration = $this->compOff[0]->getLengthHours();
                if (!empty($totalDuration)) {
                    return number_format($totalDuration, 2) . ' hours';
                } else {
                    return number_format($this->_getWorkShiftHoursPerDay(), 2) . ' hours';
                }
            }
        } else {
            return number_format($this->compOffDuration, 2) . ' hours';
        }
    }
    public function getcompOffBreakdown() {
       $this->_fetchCompOff();
         $statusStrings = array();

        foreach ($this->statusCounter as $status => $count) {
           
            if (!is_null($status)) {
                $statusStrings[] = __(ucwords(strtolower(CompOff::getTextForCompOffStatus($status)))) . "(" . number_format($count, 2) . ")";
            }
        }
 
      return implode(', ', $statusStrings); 
}
   
 
  public function getNameDetails(){
     $compOff = $this->_fetchCompOff();
     $empNumber = $this->getEmployee()->getFullName();
      return $empNumber;
      }
private function _fetchCompOff() {
        if (is_null($this->compOff)) {
            $this->compOff = $this->getCompOff();
            $this->_parseCompOff();
        }
    }
    public function getCompOffDateRange() { //To be changed to getCompOffDateRange here as well as in compOffrequestConfigurationfactory

        $this->_fetchCompOff();
        $compOffCount = count($this->compOff);
        
        if ($compOffCount == 1) {
            return $this->compOff[0]->getFormattedCompOffDateToView();
        } else {
            $firstDate = $this->compOff[0]->getDate();
            $lastDate = $this->compOff[$compOffCount - 1]->getDate();
            
            if (strtotime($firstDate) > strtotime($lastDate)) {
                $startDate = $lastDate;
                $endDate = $firstDate;
            } else {
                $startDate = $firstDate;
                $endDate = $lastDate;                
            }
            return sprintf('%s %s %s', set_datepicker_date_format($startDate), __('to'), set_datepicker_date_format($endDate));
        }
    }
    
    public function gettestname(){
       $fullName = $this->getEmployeeFullName();
      return $fullName;
    }

        private function _parseCompOff() {
        $this->numberOfDays = 0.0;
        $this->compOffDuration = 0.0;

        // Counting leave
        $this->compOffCount = $this->compOff->count();

        $this->statusCounter = array();

        foreach ($this->compOff as $compOff) {
            // Calculating number of days and duration
            $dayLength = (float) $compOff->getLengthDays();

            //this got changed to fix sf-3019087,3044234 $hourLength = $dayLength * $this->_getWorkShiftHoursPerDay();
            $hourLength = (float) $compOff->getLengthHours();
            if ($dayLength >= 1) {
                $hourLength = $dayLength * (float) $compOff->getLengthHours();
            }

            if ($hourLength == 0.0) {
                $hourLength = (float) $compOff->getLengthHours();
            }

            $this->compOffDuration += $hourLength;

            //if($hourLength > 0) {
            $this->numberOfDays += $dayLength;
            //}
            
            if (!$compOff->isNonWorkingDay()) { //changed from !
                
                // Populating leave breakdown
                
                $status = $compOff->getStatus();
                $statusDayLength = ($dayLength != 0) ? $dayLength : 1;
                if ($hourLength > 0) {
                    if (array_key_exists($status, $this->statusCounter)) {
                        $this->statusCounter[$status]+= $statusDayLength;
                    } else {
                        $this->statusCounter[$status] = $statusDayLength;
                    }
                }
            }
        }

        //is there any use of this block ?
        /* if ($this->numberOfDays == 1.0) {
          $this->numberOfDays = $this->leave[0]->getLengthDays();
          } */

    }
    public function getCompOffStatusId() {
        
                 $this->_fetchCompOff();
        if ($this->isStatusDiffer()) {
            return self::COMPOFF_STATUS_DIFFER;
        } else {
            reset($this->statusCounter);
            $firstKey = key($this->statusCounter);
            return $firstKey;
        }
    }
     public function isStatusDiffer() {

        if (count($this->getStatusCounter()) > 1) {
            return true;
        } else {
            return false;
        }
    }
    public function getSupervisorName()
    {
        $SupervisorName = $this->getEmployee()->getSupervisorNames();
       // $empNagme = $emp->getData();
        return $SupervisorName;
    }
    public function getEmployeeFullName(){
         $fullName=$this->getEmployee()->getFullName();
         return $fullName;
    }
     public function getCompOffItems() {

        $compOffRequestDao = new CompOffRequestDao();
        return $compOffRequestDao->fetchCompOff($this->getId());
    }
     public function getLatestCommentAsText() {
        $latestComment = '';
        $compOffComments = $this->getCompoffRequestComment();
        
        if (count($compOffComments) > 0) {
            $lastComment = $compOffComments->getLast();
            $latestComment = $lastComment->getComments();
        }
        
        return $latestComment;
    
     }
      public function getCommentsAsText() {
        $compOffComments = $this->getCompoffRequestComment();
        
        $allComments = '';
                
        // show last comment only
        if (count($compOffComments) > 0) {
            
            foreach ($compOffComments as $comment) {
                $created = new DateTime($comment->getCreated());
                $createdAt = set_datepicker_date_format($created->format('Y-m-d'));// . ' ' . $created->format('H:i');
                
                $formatComment = "(" . $createdAt . ' - ' . $comment->getCreatedByName() . ")  " .
                        $comment->getComments();
                $allComments = $formatComment . "<br />" . $allComments;
            }
        }
        
        return $allComments;
    }
    
   
        
   
   


   
}