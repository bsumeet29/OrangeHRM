<?php


class DetailedCompOffListConfigurationFactory extends ohrmListConfigurationFactory {
    
    protected static $listMode;
    protected static $loggedInEmpNumber;

    public function init() {
        sfContext::getInstance()->getConfiguration()->loadHelpers('OrangeDate');
        
        $header1 = new CompOffListHeader();
        $header4 = new CompOffListHeader();
        $header5 = new CompOffListHeader();
        $header6 = new CompOffListHeader();
        $header7 = new CompOffListHeader();
       // $header8 = new CompOffListHeader();
        $header1->populateFromArray(array(
            'name' => 'Date',
            'width' => '20%',
            'isSortable' => false,
            'elementType' => 'label',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array('getter' => 'getFormattedCompOffDateToView'),
        ));

     
        $header4->populateFromArray(array(
            'name' => 'Duration (Hours)',
            'width' => '8%',
            'isSortable' => false,
            'elementType' => 'label',
            'textAlignmentStyle' => 'right',
            'elementProperty' => array('getter' => 'getLengthHours'),//'hideIfCallback' => 'isNonWorkingDay'),
        ));

        $header5->populateFromArray(array(
            'name' => 'Status',
            'width' => '12%',
            'isSortable' => false,
            'elementType' => 'label',
            'filters' => array('CallbackCellFilter' => array('callback' => array('strtolower','ucwords')),
                               'I18nCellFilter' => array()
                              ),
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'getter' => array('getTextCompOffStatus'),
               // 'default' => __('Non Working Day'),
                'hasHiddenField' => true,
                'hiddenFieldName' => 'leave[{id}]',
                'hiddenFieldId' => 'leave-{id}',
                'hiddenFieldValueGetter' => 'getDetailedCompOffListRequestIdHolderValue',
                'hiddenFieldClass' => 'requestIdHolder',
                'placeholderGetters' => array(
                    'id' => 'getId',
                ),
            ),
        ));
        /* $header8->populateFromArray(array(
            'name' => 'Approval pending with',
            'width' => '18%',
            'isSortable' => false,
            'elementType' => 'link',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'labelGetter' => array('getEmployee','getSupervisorNames'),
                'placeholderGetters' => array('id' => 'getEmpNumber'),
                'urlPattern' => public_path('index.php/pim/viewEmployee/empNumber/{id}'),
            ),
        ));*/
        $header6->populateFromArray(array(
            'name' => 'Comments',
            'width' => '30%',
            'isSortable' => false,
            'elementType' => 'leaveComment',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'getter' => 'getCompoffComment',
                'idPattern' => 'hdnLeaveComment-{id}',
                'namePattern' => 'leaveComments[{id}]',
                'placeholderGetters' => array('id' => 'getId'),
                'hasHiddenField' => true,
                'hiddenFieldName' => 'leave[{id}]',
                'hiddenFieldId' => 'hdnLeave_{id}',
                'hiddenFieldValueGetter' => 'getId',
                //'hideIfCallback' => 'isNonWorkingDay',
            ),
        ));

       $compOffRequestService = new CompOffRequestService();
        $header7->populateFromArray(array(
            'name' => 'Actions',
            'width' => '10%',
            'isSortable' => false,
            'isExportable' => false,
            'elementType' => 'selectSingle',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'classPattern' => 'select_action quotaSelect',
                'defaultOption' => array('label' => 'Select Action', 'value' => ''),
                'hideIfEmpty' => true,
                'hideIfCallback' => 'isNonWorkingDay',
                'options' => array($compOffRequestService, 'getCompOffActions', array(self::RECORD, self::$loggedInEmpNumber)),
                'namePattern' => 'select_leave_action_{id}',
                'idPattern' => 'select_leave_action_{id}',
                'hasHiddenField' => true,
                'hiddenFieldName' => '{eimId}-{leaveTypeId}',
                'hiddenFieldId' => '{eimId}-{leaveTypeId}',
                'hiddenFieldValueGetter' => 'getDetailedCompOffListQuotaHolderValue',
                'hiddenFieldClass' => 'quotaHolder',
                'placeholderGetters' => array(
                    'id' => 'getId',
                    'eimId' => 'getEmpNumber',
                ),
                'hasHiddenField' => true,
                    'hiddenFieldName' =>'leaveId_{id}',
                    'hiddenFieldId' => 'leaveId',
                    'hiddenFieldValueGetter' => 'getId',
                    'hiddenFieldClass' => 'quotaHolder',
                    'placeholderGetters' => array(
                        'id' => 'getId',
                        'eimId' => 'getEmpNumber',
                        //'leaveTypeId' => 'getLeaveTypeId',
                ),
                
                
            ),
        ));

        $this->headers = array($header1,$header4,$header5,$header6,$header7);
    }
    
    public function getClassName() {
        return 'Leave';
    }

    public static function setListMode($listMode) {
        self::$listMode = $listMode;
    }
    
    public static function setLoggedInEmpNumber($empNumber) {
        self::$loggedInEmpNumber = $empNumber;
    }    
}
