<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of reportDownloadeAction
 *
 * @author firoj
 */
class reportDownloadeAction extends displayReportAction {
    
    public function execute($request) {
        
        $this->reportPermissions = $this->getDataGroupPermissions('pim_reports');

        if(!$this->reportPermissions->canRead()){
            return $this->renderText("You are not allowed to view this page!");
        }
        
        $this->getRequest()->setAttribute('skipRoundBorder', true);
        $this->setInitialActionDetails($request);

        $reportId = $request->getParameter("reportId");
       
        $reportableGeneratorService = new ReportGeneratorService();
        $reportableService = new ReportableService();
        $this->report = $reportableService->getReport($reportId);
        if (empty($this->report)) {
            return $this->renderText(__('Invalid Report Specified'));
        }

       if ($request->isMethod("get")) {
                $reportGeneratorService = new ReportGeneratorService();
//                $selectedRuntimeFilterFieldList = $reportGeneratorService->getSelectedRuntimeFilterFields($reportId);

                $selectedFilterFieldList = $reportableService->getSelectedFilterFields($reportId, false);
                
                $values = $this->setValues();

//                $linkedFilterFieldIdsAndFormValues = $reportGeneratorService->linkFilterFieldIdsToFormValues($selectedRuntimeFilterFieldList, $values);
//                $runtimeWhereClauseConditionArray = $reportGeneratorService->generateWhereClauseConditionArray($linkedFilterFieldIdsAndFormValues);

                $runtimeWhereClauseConditionArray = $reportGeneratorService->generateWhereClauseConditionArray($selectedFilterFieldList, $values);
                $sql = $reportGeneratorService->generateSql($reportId, $runtimeWhereClauseConditionArray);
          }
        
       try {
            $rawDataSet = $reportableGeneratorService->generateReportDataSet($reportId, $sql);
              
        } catch (Exception $e) {
            $this->getLoggerInstance()->error($e->getMessage(), $e);
            $this->getUser()->setFlash(displayMessageAction::MESSAGE_HEADING, __('Report could not be generated'), false);
            $this->getUser()->setFlash('error.nofade', __('Please run the report again.'), false);
            $this->forward('core', 'displayMessage');
        }
        
        $dataSet = self::escapeData($rawDataSet);
        
        $headerGroups = $reportableGeneratorService->getHeaderGroups($reportId);
        //header of csv file
       
        $headerofcsv="";
        $indexs=array();
        $headerGroupNameString='';
        $headerNameString='';
        foreach ( $headerGroups as $headerGroup) {
             $headerGroupName[]=$headerGroup->getName();
             $count=0;
             foreach ($headerGroup->getHeaders() as $header) {
                if($headerofcsv == ""){
                    $headerofcsv .=$header->getName();
                }else{
                    $headerofcsv .=','.$header->getName();
                }
                $headerNameString .="<th>".$header->getName()."</th>";  
                $properties=$header->getElementProperty();
                $indexs[]=$properties['getter'];
                $count++;
             }
             
             $headerGroupNameString .="<th colspan='$count'>".$headerGroup->getName()."</th>" ;
           }
          $headerofcsv .="\n";
          $table="<table border='1px solid black' style='width:100%;border-spacing:5px;'>
              <tr>$headerGroupNameString</tr>
              <tr>$headerNameString</tr>";
         
        
        if ($reportId == 3) {
            if (empty($dataSet[0]['employeeName']) && $dataSet[0]['totalduration'] == 0) {
                $dataSet = null;
            }
        }
        $dataRow='';
        $dataString1="";
        foreach ($dataSet as $data) {
            $employeeRowData ='';
            $dataString='';
            foreach ($indexs as $index) {
                if($employeeRowData == ''){
                   $employeeRowData .=$this->getdata($data[$index]); 
                }else{
                  $employeeRowData .=",".$this->getdata($data[$index]);  
                }
                $dataString .="<td style='text-align:center; padding: 0px;'>".$this->getdata1($data[$index])."</td>";
           }
           $employeeRowData .="\n";
           $dataRow .=$employeeRowData;
           $dataString1 .="<tr>".$dataString."</tr>";
        }
        $table .=$dataString1."</table>";
        $content=  $headerofcsv.$dataRow;
                $response = $this->getResponse();
		$response->setHttpHeader('Pragma', 'public');
		$response->setHttpHeader("Content-type", "application/vnd.ms-excel");
		$response->setHttpHeader("Content-Disposition", "attachment; filename=Report.xls");
		$response->setHttpHeader('Expires', '0');
		$response->setHttpHeader("Content-Length", strlen($table));
		$response->setContent($table);
                 
		return sfView::NONE;
    }

    public function setConfigurationFactory() {
        
    }

    public function setInitialActionDetails($request) {
        
    }

    public function setListHeaderPartial() {
        
    }

    public function setParametersForListComponent() {
        
    }

    public function setValues() {
        
    }   
     public function escapeData($data) {
       
        if (is_array($data)) {
            $escapedArray = array();
            foreach ($data as $key => $rawData) {
                $escapedArray[$key] = self::escapeData($rawData);
            }
            return $escapedArray;
        } else {
            return htmlspecialchars($data);
        } 
    }
    
    private function getdata($array){
         
        if(is_array($array)){
            $data='';
            foreach ($array as $value){
                
               $data .=($data =='')?(($value != '')?$value:' '):' '.(($value != '')?$value:' ');
                
            }
            return $data;
        }else{
            
            return ($array != '') ? str_replace(',', ' ', $array) :' ';
        }
    }
    
    private function getdata1($array){
         
        if(is_array($array)){
            $data='';
            foreach ($array as $value){
                
               $data .=($data =='')?(($value != '')?$value:' '):'<br />'.(($value != '')?$value:' ');
                
            }
            return $data;
        }else{
            
            return ($array != '') ? $array :' ';
        }
    }
    
}

?>
