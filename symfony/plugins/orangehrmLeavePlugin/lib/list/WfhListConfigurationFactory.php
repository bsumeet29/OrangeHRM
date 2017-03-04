<?php

/*
 * Code by Rahul
 */
class WfhListConfigurationFactory extends ohrmListConfigurationFactory {
    
    protected static $listMode;
    protected static $loggedInEmpNumber;
    
    public function init() {
        sfContext::getInstance()->getConfiguration()->loadHelpers('OrangeDate');
        
        $header1 = new CompOffListHeader();
        $header2 = new CompOffListHeader();
        $header4 = new CompOffListHeader();
        $header5 = new CompOffListHeader();
        $header6 = new CompOffListHeader();
        $header7 = new CompOffListHeader();
        $header8 = new CompOffListHeader();
        $header9 = new CompOffListHeader();
        $header1->populateFromArray(array(
            'name' => 'Date',
            'width' => '24%',
            'isSortable' => false,
            'elementType' => 'link',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'labelGetter' => array('getWfhDateRange'),
                'placeholderGetters' => array('id' => 'getId'),
                'urlPattern' => public_path('index.php/leave/viewWfhRequest/id/{id}'),
            ),
        ));
$wfhRequestService = new WfhRequestService();
       $header2->populateFromArray(array(
            'name' => 'Employee Name',
            'width' => '18%',
            'isSortable' => false,
            'elementType' => 'link',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'labelGetter' => array('getEmployee','getFullName'),//$compOffRequestService,'getDetails'
                'placeholderGetters' => array('id' => 'getEmpNumber'),
                'urlPattern' => public_path('index.php/pim/viewEmployee/empNumber/{id}'),
            ),
        ));

        /*$header3->populateFromArray(array(
            'name' => 'Leave Type',
            'width' => '10%',
            'isSortable' => false,
            'elementType' => 'label',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array('getter' => array('getLeaveType', 'getDescriptiveLeaveTypeName')),
        )); */

        $header4->populateFromArray(array(
            'name' => 'Number of Days',
            'width' => '9%',
            'isSortable' => false,
            'elementType' => 'label',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array('getter' => 'getNumberOfDays'),
        ));

        /*$header8->populateFromArray(array(
            'name' => 'Leave Balance (Days)',
            'width' => '12%',
            'isSortable' => false,
            'elementType' => 'leaveListBalance',
            'textAlignmentStyle' => 'right',
        ));  */ 

        $header5->populateFromArray(array(
            'name' => 'Status',
            'width' => '12%',
            'isSortable' => false,
            'elementType' => 'link',
            'filters' => array('I18nCellFilter' => array()
                              ),
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'labelGetter' => array('getWfhBreakdown'),
                'placeholderGetters' => array('id' => 'getId'),
                'urlPattern' => public_path('index.php/leave/viewWfhRequest/id/{id}'),
               'hasHiddenField' => true,
                'hiddenFieldName' => 'compOffRequestStatus[{id}]',
                'hiddenFieldId' => 'hdnCompOffRequestStatus_{id}',
                'hiddenFieldValueGetter' => 'getCompOffStatusId',
            ),
        ));
        
         $header8->populateFromArray(array(
            'name' => 'Approval pending with',
            'width' => '18%',
            'isSortable' => false,
            'elementType' => 'link',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'labelGetter' => array('getEmployee','getSupervisorNames'),
                'placeholderGetters' => array('id' => 'getCalledEmpNumber'),
                'urlPattern' => public_path('index.php/pim/viewEmployee/empNumber/{id}'),
            ),
        ));
        $header6->populateFromArray(array(
            'name' => 'Comments',
            'width' => '17%',
            'isSortable' => false,
            'elementType' => 'leaveComment',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'getter' => 'getWfhRequestComment',
                'idPattern' => 'hdnLeaveComment-{id}',
                'namePattern' => 'leaveComments[{id}]',
                'placeholderGetters' => array('id' => 'getId'),
                'hasHiddenField' => true,
                'hiddenFieldName' => 'leaveRequest[{id}]',
                'hiddenFieldId' => 'hdnLeaveRequest_{id}',
                'hiddenFieldValueGetter' => 'getId',
            ),
        ));

        
        
        $header7->populateFromArray(array(
            'name' => 'Actions',
            'width' => '10%',
            'isSortable' => false,
            'isExportable' => false,
            'elementType' => 'compOffListAction',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'classPattern' => 'select_action quotaSelect',
                'defaultOption' => array('label' => 'Select Action', 'value' => ''),
                'hideIfEmpty' => true,
                'options' => array( $wfhRequestService, 'getWfhRequestActions', array(self::RECORD, self::$loggedInEmpNumber)),
                'namePattern' => 'select_leave_action_{id}',
                'idPattern' => 'select_leave_action_{id}',
                'hasHiddenField' => true,
                'hiddenFieldName' => '{eimId}-{leaveTypeId}',
                'hiddenFieldId' => '{eimId}-{leaveTypeId}',
                'hiddenFieldValueGetter' => 'getNumberOfDays',
                'hiddenFieldClass' => 'quotaHolder',
                'placeholderGetters' => array(
                    'id' => 'getId',
                    'eimId' => 'getEmpNumber'
                   // 'leaveTypeId' => 'getLeaveTypeId'
                ),
            ),
        ));
     /*   $header9->populateFromArray(array(
            'name' => 'Extension Request',
            'width' => '15%',
            'isSortable' => false,
            'elementType' => '',
            'textAlignmentStyle' => 'left',
        )); */
        $this->headers = array( $header1,$header2,  $header4,$header6,$header5,$header7);
    }
    
    public function getClassName() {
        return 'LeaveRequest';
    }
    
    public static function setListMode($listMode) {
        self::$listMode = $listMode;
    }
    
    public static function setLoggedInEmpNumber($empNumber) {
        self::$loggedInEmpNumber = $empNumber;
    }     
}

