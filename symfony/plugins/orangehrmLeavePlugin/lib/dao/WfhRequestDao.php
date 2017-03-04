<?php

/*
 *
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 *
 */

class WfhRequestDao extends BaseDao {

    private static $doneMarkingOfExpiredCompoff = false;
    private $employeeService;

    /**
     * Save leave request 
     * 
     * @param LeaveRequest $leaveRequest Leave request object
     * @param Array $leaveList Array of leave objects linked to the leave request
     * @param Array $entitlements Array of entitlements to be modified 
     * @return boolean
     */
    public function saveWfhRequest(WfhRequest $wfhRequest, $wfhs) {
        
        $conn = Doctrine_Manager::connection();
        $conn->beginTransaction();        
        
        try {
            
            $wfhRequest->save();
            
            foreach ($wfhs as $wfh) {
                $wfh->setWfhRequestId($wfhRequest->getId());
                $wfh->save();
              
            }

            $conn->commit();
            return $wfhRequest;
        } catch (Exception $e) {
            $conn->rollback();
            throw new DaoException($e->getMessage());
        }
    }

    public function saveWfhRequestComment($wfhRequestId, $comment, $createdBy, $loggedInUserId, $loggedInEmpNumber) {
        try {
            $wfhRequestComment = new WfhRequestComment();
            $wfhRequestComment->setWfhRequestId($wfhRequestId);
            $wfhRequestComment->setCreated(date('Y-m-d H:i:s'));
            $wfhRequestComment->setCreatedByName($createdBy);
            $wfhRequestComment->setCreatedById($loggedInUserId);
            $wfhRequestComment->setCreatedByEmpNumber($loggedInEmpNumber);
            $wfhRequestComment->setComments($comment);  
            $wfhRequestComment->save();
            return $wfhRequestComment;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
    public function searchSubordinateLeave(LeaveRequest $leaveRequest,$contactPersons){
        $conflictLeaveData=array();
        $leaveDates=$leaveRequest->getLeaveDates();
             foreach ($contactPersons as $person){
                 $contact_person_id=$person['empId'];
                 $contact_person_name=$person['empName'];
                 $dataForDateRange=$this->getLeaveByEmpId($contact_person_id ,$leaveDates);
                if(!empty($dataForDateRange)){
                    $conflictDate=array();
                    foreach ($dataForDateRange as $dataForDate){
                      $conflictDate[]=$dataForDate['date'];
                    }
                   $conflictLeaveData[]=array('empId'=>$contact_person_id,'task'=>$person['task'],'empName'=>$contact_person_name,'conflictedDate'=>$conflictDate);
                }
               
           }
           if(!empty($conflictLeaveData)){
               return $conflictLeaveData;
           }else{
               return null;
           }
    }

    public function saveWfhComment($wfhId, $comment, $createdBy, $loggedInUserId, $loggedInEmpNumber) {
        try {
            $WfhComment = new WfhComment();
            $WfhComment->setWfhId($wfhId);
            $WfhComment->setCreated(date('Y-m-d H:i:s'));
            $WfhComment->setCreatedByName($createdBy);
            $WfhComment->setCreatedById($loggedInUserId);
            $WfhComment->setCreatedByEmpNumber($loggedInEmpNumber);
            $WfhComment->setComments($comment);
            
            $WfhComment->save();
            return $WfhComment;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
    
    public function getWfhRequestComments($wfhRequestId) {
        try {
            $q = Doctrine_Query::create()
                    ->from('WfhRequestComment w')
                    ->andWhere("w.wfh_request_id = ?", $wfhRequestId);


            $comments = $q->execute();

            return $comments;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function getWfhComments($wfhId) {
        try {
            $q = Doctrine_Query::create()
                    ->from('WfhComment w')
                    ->andWhere("w.wfh_id = ?", $wfhId);


            $comments = $q->execute();

            return $comments;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }    
    
    public function saveWfh(Wfh $wfh) {
        try {
            $wfh->save();
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function changeWfhStatus(Wfh $wfh,$currentstate,$newstate) {
//        $conn = Doctrine_Manager::connection();
//        $conn->beginTransaction();
    //    $pdo = Doctrine_Manager::getInstance()->getCurrentConnection()->getDbh();
       // $userdata=array();
        $id=$wfh->getId();
       /* $e_id=$wfh->getLeaveEntitlementId();
        //echo"<pre>";print_r($compoff);exit;
        //echo"ID :".$e_id;exit;
        $days= $wfh->getLengthDays();
        $empNo=$wfh->getEmpNumber();
     $creditedDate= $fromDate =   strtotime(date('Y-m-d'), true);
      
       $toDate=strtotime('+30 days',$fromDate);
        if($fromDate<'2016-03-31'&&$toDate>'2016-03-31'){
            $date =  strtotime(('Y-03-31'),true);
            $toDate = strtotime($date,true);
            //$toDate = date('Y-m-d',$toDate);
        }*/
       
        try { 

          /*  if($newstate==2){

              /*  $q = Doctrine_Query::create()
                            ->update('LeaveEntitlement l')
                            ->set('l.no_of_days=?','l.no_of_days + ?', $days)
                            ->where('l.emp_number = ?', $empNo)
                            ->andWhere('l.leave_type_id = ?',4);

                  
                    $row=$q->execute();
               if(!($row)){  
                   $user = sfContext::getInstance()->getUser();
                    $loggedInUserId = $user->getAttribute('auth.userId');
                    $loggedInEmpNumber = $user->getAttribute('auth.empNumber');
                    $entitlement = new LeaveEntitlement();
                     if (!empty($loggedInEmpNumber)) {
                            $employee = $this->getEmployeeService()->getEmployee($loggedInEmpNumber);
                            $createdBy = $employee->getFullName();
                        } else {
                            $createdBy = $user->getAttribute('auth.firstName');
                        }
                    $entitlement->setEmpNumber($empNo);
                        $entitlement->setLeaveTypeId(4);

                        $entitlement->setCreditedDate(date('Y-m-d'));
                        $entitlement->setCreatedById($loggedInUserId);
                        $entitlement->setCreatedByName($createdBy);

                        $entitlement->setEntitlementType(1);
                        $entitlement->setDeleted(0);

                        $entitlement->setNoOfDays($days);
                        $entitlement->setFromDate(date('Y-m-d'));
                         $entitlement->setToDate(date('Y-m-d', strtotime("+30 days")));
                        
                        $entitlement->save();

                $entitlementId = $entitlement->getId();
              // echo"<pre>";print_r($entitlementId);exit;
               //}
                $q = Doctrine_Query::create()
                            ->update('CompOff c')
                            ->set('c.status=?',2)
                            ->set('c.leave_entitlement_id=?',$entitlementId)
                            ->where('c.id = ?', $id);
                $result=$q->execute();
             $userdata= array(
                 'empNo'=>$empNo,
                 'newstate'=>$newstate
             );
             return $userdata;
               
            }
            elseif($currentstate==2&&$newstate==1){
               
                $q = Doctrine_Query::create()
                            ->update('LeaveEntitlement l')
                            ->set('l.deleted=?',1)
                            ->where('l.id = ?', $e_id);
                 $row=$q->execute();
                  
                 $q = Doctrine_Query::create()
                            ->update('CompOff c')
                            ->set('c.status=?',1)
                            ->where('c.id = ?', $id);
                $result=$q->execute();
             $userdata= array(
                 'empNo'=>$empNo,
                 'newstate'=>$newstate
             );
             return $userdata;   
            }
            else{ */
                //echo"ID :".$e_id;exit;
                
                 $q = Doctrine_Query::create()
                            ->update('Wfh c')
                            ->set('c.status=?',$newstate)
                            ->where('c.id = ?', $id);
                $result=$q->execute();
             $userdata= array(
                 'empNo'=>$empNo,
                 'newstate'=>$newstate
             );
             return $userdata; 
            //}
        } catch (DaoException $e) {
            $conn->rollback();
            throw new DaoException($e->getMessage(), 0, $e);
        }       
    }

    /**
     * Modify Overlap leave request
     * @param LeaveRequest $leaveRequest
     * @return boolean
     */
    public function xmodifyOverlapLeaveRequest(LeaveRequest $leaveRequest, $leaveList, $leavePeriod = null) {
        try {
            $nextLeavePeriod = false;
            $nextLeaveRequest = false;
            if ($leavePeriod == null)
                $leavePeriod = Doctrine :: getTable('LeavePeriod')->find($leaveRequest->getLeavePeriodId());

            foreach ($leaveList as $leave) {

                if ($leave->getLeaveDate() > $leavePeriod->getEndDate()) {
                    if (!($nextLeavePeriod instanceof LeavePeriod)) {

                        $leavePeriodService = new LeavePeriodService();
                        $leavePeriodService->setLeavePeriodDao(new LeavePeriodDao());

                        $nextLeavePeriod = $leavePeriodService->createNextLeavePeriod($leave->getLeaveDate());

                        $nextLeaveRequest = new LeaveRequest();
                        $idGenService = new IDGeneratorService();
                        $idGenService->setEntity($leaveRequest);
                        $nextLeaveRequest->setLeaveRequestId($idGenService->getNextID());

                        $nextLeaveRequest->setLeaveTypeId($leaveRequest->getLeaveTypeId());
                        $nextLeaveRequest->setDateApplied($leaveRequest->getDateApplied());
                        $nextLeaveRequest->setLeavePeriodId($nextLeavePeriod->getLeavePeriodId());
                        $nextLeaveRequest->setLeaveTypeName($leaveRequest->getLeaveTypeName());
                        $nextLeaveRequest->setEmpNumber($leaveRequest->getEmpNumber());
                        $nextLeaveRequest->setLeaveComments($leaveRequest->getLeaveComments());

                        $nextLeaveRequest->save();
                    }

                    $q = Doctrine_Query::create()
                            ->update('Leave l')
                            ->set('l.leave_request_id=', $nextLeaveRequest->getLeaveRequestId())
                            ->where('l.leave_id = ?', $leave->getLeaveId());


                    $q->execute();
                }
            }
            return true;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Get Overlapping Compoff
     * @param String $compoffStartDate
     * @param String $compoffEndDate
     * @param int $empId
     * @param String $startDayStartTime
     * @param String $startDayEndTime
     * @param String $endDayStartTime
     * @param String $endDayEndTime
     * @return Array of Leave objects
     * @throws DaoException
     */
    public function getOverlappingWfh($wfhStartDate, $wfhEndDate, $empId, 
            $startDayStartTime = null, $startDayEndTime = null, $allDaysPartial = false, $endDayStartTime = null, $endDayEndTime = null) {

        try {
            
            $startDayStartTime = $this->addSeconds($startDayStartTime);
            $startDayEndTime = $this->addSeconds($startDayEndTime);
            $endDayStartTime = $this->addSeconds($endDayStartTime);
            $endDayEndTime = $this->addSeconds($endDayEndTime);
            
            $q = Doctrine_Query::create()
                    ->from('Wfh w');

            $q->andWhere('w.emp_number = ?' , $empId);
            $q->andWhereNotIn('w.status', array(Wfh::WFH_STATUS_CANCELLED));

            if ($wfhStartDate == $wfhEndDate) {

                if (is_null($startDayStartTime)) {
                    $startDayStartTime = '00:00:00';
                }
                
                if (is_null($endDayStartTime)) {
                    $endDayStartTime = '00:00:00';
                }
                
                if (is_null($startDayEndTime)) {
                    $startDayStartTime = '23:59:00';
                }
                
                if (is_null($endDayEndTime)) {
                    $endDayEndTime = '23:59:00';
                }
                
                $startDateAndTime = $wfhStartDate . " " . $startDayStartTime;
                $endDateAndTime = $wfhEndDate . " " . $startDayEndTime;
                
                $orParams = array();
                $or [] = "(? <= CONCAT(`date`,' ',start_time) AND CONCAT(`date`,' ',end_time) <= ?)";
                $orParams[] = $startDateAndTime;
                $orParams[] = $endDateAndTime;
                $or [] = "(CONCAT(`date`,' ',start_time) <= ? AND ? <= CONCAT(`date`,' ',end_time))";
                $orParams[] = $startDateAndTime;
                $orParams[] = $endDateAndTime;
                $or [] = "(? < CONCAT(`date`,' ',start_time) AND CONCAT(`date`,' ',start_time) < ?)";
                $orParams[] = $startDateAndTime;
                $orParams[] = $endDateAndTime;
                $or [] = "(? < CONCAT(`date`,' ',end_time) AND CONCAT(`date`,' ',end_time) < ?)";
                $orParams[] = $startDateAndTime;
                $orParams[] = $endDateAndTime;
                $or [] = "(? = CONCAT(`date`,' ',end_time) AND CONCAT(`date`,' ',end_time) = ?)";
                $orParams[] = $startDateAndTime;
                $orParams[] = $endDateAndTime;
                $or [] = "((`date` = ?) AND ((start_time = '00:00:00' AND end_time='00:00:00') OR (start_time IS NULL AND end_time IS NULL)))";
                $orParams[] = $wfhEndDate;

                $orString = implode(" OR ", $or);
                $orString = "(" . $orString . ")";
                $q->andWhere($orString, $orParams);                
            } else {
                
                // first get all overlapping leave, disregarding time periods          
                $q->andWhere("( `date` <= ? AND `date` >= ?)", array($wfhEndDate, $wfhStartDate));
                                
            
                if ($allDaysPartial) {
                    // will overlap with full days or if time period overlaps
                    $q->andWhere("(start_time = '00:00:00' AND end_time='00:00:00') OR (start_time IS NULL AND end_time IS NULL) " . 
                            "OR  ((? < end_time) AND (? > start_time))",
                            array($startDayStartTime, $startDayEndTime));                 
                    
                } else { 
                    
                    // Start Day condition                    
                    if (!is_null($startDayStartTime) && !is_null($startDayEndTime)) {
                        $q->andWhere("`date` <> ? " . 
                                "OR  (? < end_time AND ? > start_time) " .
                                "OR (start_time = '00:00:00' AND end_time='00:00:00') " .
                                "OR (start_time IS NULL AND end_time IS NULL)",
                                array($wfhStartDate, $startDayStartTime, $startDayEndTime));  
                    }
                    
                    // End Day condition                    
                    if (!is_null($endDayStartTime) && !is_null($endDayEndTime)) {
                        $q->andWhere("(`date` <> ?) " . 
                                "OR  ((? < end_time) AND (? > start_time)) " .
                                "OR (start_time = '00:00:00' AND end_time='00:00:00') " .
                                "OR (start_time IS NULL AND end_time IS NULL)",
                                array($wfhEndDate, $endDayStartTime, $endDayEndTime));   
                    }


                }
            }

            $wfhListArray = $q->execute();
            return $wfhListArray;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
    
    /**
     *
     * @param type $employeeId
     * @param type $date
     * @return type 
     */
    public function getTotalLeaveDuration($employeeId, $date) {

        $this->_markApprovedLeaveAsTaken();

        $leaveStatusNotConsider = array(Leave::LEAVE_STATUS_LEAVE_CANCELLED, Leave::LEAVE_STATUS_LEAVE_REJECTED, Leave::LEAVE_STATUS_LEAVE_WEEKEND, Leave::LEAVE_STATUS_LEAVE_HOLIDAY);

        $q = Doctrine_Query::create()
                ->select('SUM(length_hours) as total_duration')
                ->from('Leave')
                ->where("emp_number =?", $employeeId)
                ->andWhereNotIn("status ", $leaveStatusNotConsider)
                ->andWhere("date =?", $date);
        $duration = $q->fetchOne();

        return $duration->getTotalDuration();
    }

    /**
     * Count leave records in the Leave table
     * @return integer $count
     */
    public function xgetLeaveRecordCount() {
        try {

            $q = Doctrine_Query::create()
                    ->from('Leave');
            $count = $q->count();
            return $count;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param $empId
     * @param $leaveTypeId
     * @return int
     */
    public function xgetNumOfLeave($empId, $leaveTypeId) {
        try {


            $q = Doctrine_Query::create()
                    ->addSelect('sum(leave_length_days) as daysLength')
                    ->from('Leave l')
                    ->andWhere("l.employee_id = ?", $empId)
                    ->andWhere("l.leave_type_id = ?", $leaveTypeId);


            $record = $q->fetchOne();

            return $record['daysLength'];
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     *
     * @param $empId
     * @param $leaveTypeId
     * @return int
     */
    public function xgetNumOfAvaliableLeave($empId, $leaveTypeId, $leavePeriodId = null) {
        try {


            $q = Doctrine_Query::create()
                    ->addSelect('sum(leave_length_days) as daysLength')
                    ->from('Leave l')
                    ->andWhere("l.employee_id = ?", $empId)
                    ->andWhere("l.leave_type_id = ?", $leaveTypeId)
                    ->andWhereNotIn('l.leave_status', array(Leave::LEAVE_STATUS_LEAVE_CANCELLED, Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL, Leave::LEAVE_STATUS_LEAVE_REJECTED));

            if ($leavePeriodId) {
                $q->leftJoin('l.LeaveRequest lr');
                $q->andWhere('lr.leave_period_id = ?', $leavePeriodId);
            }

            $record = $q->fetchOne();

            return $record['daysLength'];
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * 
     * @param LeavePeriod $leavePeriod
     * @return unknown_type
     */
    public function xgetLeavePeriodOverlapLeaves(LeavePeriod $leavePeriod) {
        try {
            $q = Doctrine_Query::create()
                    ->from('Leave l')
                    ->andWhere('l.leave_date > ?', $leavePeriod->getEndDate())
                    ->groupBy('l.leave_request_id');

            $leaveList = $q->execute();
            return $leaveList;
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Search Leave Requests.
     * 
     * Valid Search Parameter values
     *    * 'noOfRecordsPerPage' (int) - Number of records per page. If not available, 
     *                                   sfConfig::get('app_items_per_page') will be used.
     *    * 'dateRange' (DateRange)    -
     *    * 'statuses' (array)
     *    * 'employeeFilter' (array)   - Filter by given employees. If an empty array(), does not match any employees.
     *    * 'leavePeriod'
     *    * 'leaveType'
     *    * 'cmbWithTerminated'
     *    * 'subUnit'                  - Only return leave requests for employees in given subunit 
     *                                   (or subunit below that in the org structure).
     *    * 'locations' (array)        - Only return leave requests for employees in given locations.
     *    * 'employeeName' (string)    - Match employee name (Wildcard match against full name).
     * 
     * @param ParameterObject $searchParameters Search Parameters
     * @param int $page $status Page Number
     * @param bool $isCSVPDFExport If true, returns all results (ignores paging) as an array
     * @param bool $isMyLeaveList If true, ignores setting to skip terminated employees.
     * @param bool $prefetchComments If true, will prefetch leave comments for faster access.
     * 
     * @return array Returns results and record count in the following format:
     *               array('list' => results, 'meta' => array('record_count' => count)
     * 
     *               If $isCSVPDFExport is true, returns just an array of results.
     */
    public function searchWfhRequests($searchParameters, $page = 1, $isCSVPDFExport = false, $isMyLeaveList = false,
            $prefetchLeave = false, $prefetchComments = false) {
        
       //$this->_markExpiredCompoff();
        
        $limit = !is_null($searchParameters->getParameter('noOfRecordsPerPage')) ? $searchParameters->getParameter('noOfRecordsPerPage') : sfConfig::get('app_items_per_page');
        $offset = ($page > 0) ? (($page - 1) * $limit) : 0;
      
        $list = array();

        $select = 'lr.*, em.firstName, em.lastName, em.middleName, em.termination_id';
        
        if ($prefetchComments) {
            $select .= ', lc.*';
        }
        if ($prefetchLeave) {
            $select .= ', l.*';
        }
        
        $q = Doctrine_Query::create()
                ->select($select)
                ->from('WfhRequest lr')
                ->leftJoin('lr.Wfh l')
                ->leftJoin('lr.Employee em');

        if ($prefetchComments) {
            $q->leftJoin('lr.WfhRequestComment lc');
        }
        
        $dateRange = $searchParameters->getParameter('dateRange', new DateRange());
        $statuses = $searchParameters->getParameter('statuses');
        $employeeFilter = $searchParameters->getParameter('employeeFilter');
        //$leavePeriod = $searchParameters->getParameter('leavePeriod');
        //$leaveType = $searchParameters->getParameter('leaveType');
        //$leaveTypeId = $searchParameters->getParameter('leaveTypeId');
        $includeTerminatedEmployees = $searchParameters->getParameter('cmbWithTerminated');
         $departmentId = $searchParameters->getParameter('eeo_category');
        $subUnit = $searchParameters->getParameter('subUnit');
        //$locations = $searchParameters->getParameter('locations');
        $employeeName = $searchParameters->getParameter('employeeName');

        $fromDate = $dateRange->getFromDate();
        $toDate = $dateRange->getToDate();

       /* if (!empty($fromDate)) {
            $q->andWhere("l.date >= ?",$fromDate);
        }

        if (!empty($toDate)) {
            $q->andWhere("l.date <= ?",$toDate);
        }
        */
        if (!empty($statuses)) {
            $q->whereIn("l.status", $statuses);
        }

        if (!empty($employeeFilter)) {
            if (is_numeric($employeeFilter) && $employeeFilter > 0) {
                $q->andWhere('lr.emp_number = ?', (int) $employeeFilter);
            } elseif ($employeeFilter instanceof Employee) {
                $q->andWhere('lr.emp_number = ?', $employeeFilter->getEmpNumber());
            } elseif (is_array($employeeFilter)) {
                $empNumbers = array();
                foreach ($employeeFilter as $employee) {
                    $empNumbers[] = ($employee instanceof Employee) ? $employee->getEmpNumber() : $employee;
                }
                
                // Here, ->whereIn() is very slow when employee number count is very high (around 5000).
                // this seems to be due to the time taken by Doctrine to replace the 5000 question marks in the query.
                // Therefore, replaced with manually built IN clause.
                // Note: $empNumbers is not based on user input and therefore is safe to use in the query.
                $q->andWhere('lr.emp_number IN (' . implode(',', $empNumbers) . ')');
            }
        } else {
            // empty array does not match any results.
            if (is_array($employeeFilter)) {
                $q->andWhere('lr.emp_number = ?', -1);
            }
        }

//        if (trim($fromDate) == "" && trim($toDate) == "" && !empty($leavePeriod)) {
//            $leavePeriodId = ($leavePeriod instanceof LeavePeriod) ? $leavePeriod->getLeavePeriodId() : $leavePeriod;
//            $q->andWhere('lr.leave_period_id = ?', (int) $leavePeriodId);
//        }

//        if (!empty($leaveType)) {
//            $leaveTypeId = ($leaveType instanceof LeaveType) ? $leaveType->getLeaveTypeId() : $leaveType;
//            $q->andWhere('lr.leave_type_id = ?', $leaveTypeId);
//        }
//        if (!empty($leaveTypeId)) {
//            $q->andWhere('lr.leave_type_id = ?', $leaveTypeId);
//        }

        if ($isMyLeaveList) {
            $includeTerminatedEmployees = true;
        }

        // Search by employee name
        if (!empty($employeeName)) {
            $employeeName = str_replace(' (' . __('Past Employee') . ')', '', $employeeName);
            // Replace multiple spaces in string with wildcards
            $employeeName = preg_replace('!\s+!', '%', $employeeName);

            // Surround with wildcard character
            $employeeName = '%' . $employeeName . '%';

            $q->andWhere('CONCAT_WS(\' \', em.emp_firstname, em.emp_middle_name, em.emp_lastname) LIKE ?', $employeeName);
        }

        if (!empty($subUnit)) {

            // Get given subunit's descendents as well.
            $subUnitIds = array($subUnit);
            $subUnitObj = Doctrine::getTable('Subunit')->find($subUnit);

            if (!empty($subUnitObj)) {
                $descendents = $subUnitObj->getNode()->getDescendants();
                foreach ($descendents as $descendent) {
                    $subUnitIds[] = $descendent->id;
                }
            }

            $q->andWhereIn('em.work_station', $subUnitIds);
        }
        if (!empty($departmentId)) {
            $departmentIds = array($departmentId);
            $subUnitObj = Doctrine::getTable('JobCategory')->find($departmentId);
           
            $q->andWhereIn('em.eeo_cat_code', $departmentId);
        }

        if (empty($includeTerminatedEmployees)) {
            $q->andWhere("em.termination_id IS NULL");
        }

        if (!empty($locations)) {
            $q->leftJoin('em.locations loc');
            $q->andWhereIn('loc.id', $locations);
        }

        $count = $q->count();
        
        $q->orderBy('l.date DESC, em.emp_lastname ASC, em.emp_firstname ASC');        

        if ($isCSVPDFExport) {
            $limit = $count;
            $offset = 0;
        }
        $q->offset($offset);
        $q->limit($limit);
       
        $list = $q->execute();
//echo"<pre>";var_dump($list);exit;
        return $isCSVPDFExport ? $list : array('list' => $list, 'meta' => array('record_count' => $count));
    }
  /*
   * delete leave
   * 
   */
  public function deleteWfhHistory($ids) {
      try {
            $q = Doctrine_Query::create()
                    ->delete('WfhRequest')
                    ->whereIn('id', $ids);
            $q->execute();
            
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
  }
    /**
     *
     * @param int $leaveRequestId
     * @return array
     */
    public function fetchWfh($wfhRequestId) {

        $q = Doctrine_Query::create()
                ->select('*')
                ->from('Wfh c')
                ->where('c.wfh_request_id = ?', $wfhRequestId);

        return $q->execute();
    }

    /**
     *
     * @param int $leaveId
     * @return array
     */
    public function readCompOff($compOffId) {

        $q = Doctrine_Query::create()
                ->select('*')
                ->from('CompOff c')
                ->where('c.id = ?', $compOffId);

        return $q->fetchOne();
    }

    public function fetchWfhRequest($wfhRequestId) {
       //$this->_markExpiredCompoff();

        $q = Doctrine_Query::create()
                ->select('*')
                ->from('WfhRequest cr')
                ->where('id = ?', $wfhRequestId);
       
        return $q->fetchOne();
    }
    public function getLeaveByEmpId($emp_number,$leaveDates){
        $q = Doctrine_Query::create()
                ->select('*')
                ->from('Leave l')
                ->where('l.emp_number = ?', $emp_number)
                ->whereNotIn('l.status',array(-1,0,4,5))
                ->whereIn('l.date', $leaveDates);
        
         return $q->fetchArray();
        
    }

    public function getWfhById($wfh) {
       // $this->_markExpiredCompoff();

        $q = Doctrine_Query::create()
                ->select('*')
                ->from('Wfh c')
                ->where('c.id = ?', $wfh);

        return $q->fetchOne();
    }

    public function getScheduledLeavesSum($employeeId, $leaveTypeId, $leavePeriodId) {
        $this->_markApprovedLeaveAsTaken();

        try {

            $q = Doctrine_Query::create()
                    ->select('SUM(lea.length_days) as scheduledSum')
                    ->from('Leave lea')
                    //->leftJoin('lea.LeaveRequest lr')
                    ->where("lea.emp_number = ?", $employeeId)
                    ->andWhere("lea.leave_type_id = ?", $leaveTypeId)
                    ->andWhere("lea.status = ?", Leave::LEAVE_STATUS_LEAVE_APPROVED)
            //->andWhere("lr.leave_period_id = $leavePeriodId")
            ;

            $record = $q->fetchOne();

            return $record['scheduledSum'];
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    public function getTakenLeaveSum($employeeId, $leaveTypeId, $leavePeriodId) {

        $this->_markApprovedLeaveAsTaken();

        $q = Doctrine_Query::create()
                ->select('SUM(lea.length_days) as scheduledSum')
                ->from('Leave lea')
                ->where("lea.emp_number = ?", $employeeId)
                ->andWhere("lea.leave_type_id = ?", $leaveTypeId)
                ->andWhere("lea.status = ?", Leave::LEAVE_STATUS_LEAVE_TAKEN)

        ;

        $record = $q->fetchOne();

        return $record['scheduledSum'];
    }

    public function markApprovedLeaveAsTaken() {
        $this->_markApprovedLeaveAsTaken();
    }
    public function markExpiredCompoff(){

        $this->_markExpiredCompoff();
    }

    private function _markExpiredCompoff() {
        if (self::$doneMarkingOfExpiredCompoff) {

            return;
        } else {

            $date = date('Y-m-d H:i:s');
            $conn = Doctrine_Manager::connection()->getDbh();
//            $query = "SELECT l.id from ohrm_leave_entitlement l WHERE l.to_date < ? ";
//            $statement = $conn->prepare($query);
//            $result = $statement->execute($date);
         //   echo"<pre>";print_r($result);exit;
              $q = Doctrine_Query::create()
                        ->select('l.id')
                        ->from('LeaveEntitlement l')
                        ->where('l.to_date < ?',$date);
            $result = $q->execute();
            if ($result) {
                
                $ids = array();
                foreach($result as $id)
                {
                    $ids[] = $id;
                }
                if (count($ids) > 0) {
                    $q = Doctrine_Query::create()
                            ->update('LeaveEntitlement l')
                            ->set('l.deleted', 1)
                            ->whereIn('l.id', $ids);
                    $q->execute();

                }
            }
            self::$doneMarkingOfExpiredCompoff = true;
        }
    }

    public function getLeaveRequestSearchResultAsArray($searchParameters) {
        $this->_markApprovedLeaveAsTaken();

        $q = $this->getSearchBaseQuery($searchParameters);

        $q->select('lr.date_applied, lt.name, lr.comments, sum(l.length_hours) leave_length_hours_total, sum(l.length_days) as total_leave_length_days,em.firstName, em.middleName, em.lastName' .
                        ', sum(IF(l.status = 2, l.length_days, 0)) as scheduled, ' .
                        ', sum(IF(l.status = 0, l.length_days, 0)) as cancelled, ' .
                        ', sum(IF(l.status = 3, l.length_days, 0)) as taken, ' .
                        ', sum(IF(l.status = -1, l.length_days, 0)) as rejected, ' .
                        ', sum(IF(l.status = 1, l.length_days, 0)) as pending_approval, ' .
                        'concat(l.status)')
                ->groupBy('lr.id');

        return $q->execute(array(), Doctrine::HYDRATE_SCALAR);
    }

    public function getDetailedLeaveRequestSearchResultAsArray($searchParameters) {

        $this->_markApprovedLeaveAsTaken();

        $q = $this->getSearchBaseQuery($searchParameters);

        $q->select('lr.date_applied,l.date, lt.name, l.length_hours, ' .
                'l.status,l.comments, em.firstName, em.middleName, em.lastName ');

        return $q->execute(array(), Doctrine::HYDRATE_SCALAR);
    }

    protected function getSearchBaseQuery($searchParameters) {


        $q = Doctrine_Query::create()
                ->from('LeaveRequest lr')
                ->leftJoin('lr.LeaveType lt')
                ->leftJoin('lr.Leave l')
                ->leftJoin('lr.Employee em');

        $dateRange = $searchParameters->getParameter('dateRange', new DateRange());
        $statuses = $searchParameters->getParameter('statuses');
        $employeeFilter = $searchParameters->getParameter('employeeFilter');
        $leavePeriod = $searchParameters->getParameter('leavePeriod');
        $leaveType = $searchParameters->getParameter('leaveType');
        $leaveTypeId = $searchParameters->getParameter('leaveTypeId');
        $includeTerminatedEmployees = $searchParameters->getParameter('cmbWithTerminated');
        $subUnit = $searchParameters->getParameter('subUnit');
        $locations = $searchParameters->getParameter('locations');
        $employeeName = $searchParameters->getParameter('employeeName');

        $fromDate = $dateRange->getFromDate();
        $toDate = $dateRange->getToDate();

        if (!empty($fromDate)) {
            $q->andWhere("l.date >= ?",$fromDate);
        }

        if (!empty($toDate)) {
            $q->andWhere("l.date <= ?",$toDate);
        }

        if (!empty($statuses)) {
            $q->whereIn("l.status", $statuses);
        }

        if (!empty($employeeFilter)) {
            if (is_numeric($employeeFilter) && $employeeFilter > 0) {
                $q->andWhere('lr.empNumber = ?', (int) $employeeFilter);
            } elseif ($employeeFilter instanceof Employee) {
                $q->andWhere('lr.empNumber = ?', $employeeFilter->getEmpNumber());
            } elseif (is_array($employeeFilter)) {
                $empNumbers = array();
                foreach ($employeeFilter as $employee) {
                    $empNumbers[] = ($employee instanceof Employee) ? $employee->getEmpNumber() : $employee;
                }
                $q->whereIn('lr.empNumber', $empNumbers);
            }
        } else {
            // empty array does not match any results.
            if (is_array($employeeFilter)) {
                $q->andWhere('lr.empNumber = ?', -1);
            }
        }

//        if (trim($fromDate) == "" && trim($toDate) == "" && !empty($leavePeriod)) {
//            $leavePeriodId = ($leavePeriod instanceof LeavePeriod) ? $leavePeriod->getLeavePeriodId() : $leavePeriod;
//            $q->andWhere('lr.leave_period_id = ?', (int) $leavePeriodId);
//        }

        if (!empty($leaveType)) {
            $leaveTypeId = ($leaveType instanceof LeaveType) ? $leaveType->getLeaveTypeId() : $leaveType;
            $q->andWhere('lr.leave_type_id = ?', $leaveTypeId);
        }
        if (!empty($leaveTypeId)) {
            $q->andWhere('lr.leave_type_id = ?', $leaveTypeId);
        }

        // Search by employee name
        if (!empty($employeeName)) {
            $employeeName = str_replace(' (' . __('Past Employee') . ')', '', $employeeName);
            // Replace multiple spaces in string with wildcards
            $employeeName = preg_replace('!\s+!', '%', $employeeName);

            // Surround with wildcard character
            $employeeName = '%' . $employeeName . '%';

            $q->andWhere('CONCAT_WS(\' \', em.emp_firstname, em.emp_middle_name, em.emp_lastname) LIKE ?', $employeeName);
        }

        if (!empty($subUnit)) {

            // Get given subunit's descendents as well.
            $subUnitIds = array($subUnit);
            $subUnitObj = Doctrine::getTable('Subunit')->find($subUnit);

            if (!empty($subUnitObj)) {
                $descendents = $subUnitObj->getNode()->getDescendants();
                foreach ($descendents as $descendent) {
                    $subUnitIds[] = $descendent->id;
                }
            }

            $q->andWhereIn('em.work_station', $subUnitIds);
        }

        if (empty($includeTerminatedEmployees)) {
            $q->andWhere("em.termination_id IS NULL");
        }

        if (!empty($locations)) {
            $q->leftJoin('em.locations loc');
            $q->andWhereIn('loc.id', $locations);
        }

        $q->orderBy('l.date DESC, em.emp_lastname ASC, em.emp_firstname ASC');

        return $q;
    }
    
    protected function addSeconds($timeValue) {
        if (is_string($timeValue) && substr_count($timeValue, ':') == 1) {
            $timeValue .= ':00';
        }
        
        return $timeValue;
    } 
    public function getEmployeeService(){
         if (!($this->employeeService instanceof EmployeeService)) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
    }

}
