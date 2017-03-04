<?php

/**
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
 */

/**
 * Description of LeaveChangeMailProcessor
 *
 */
class LeaveChangeMailProcessor extends LeaveEmailProcessor {
    
    protected function _generateLeaveDetailsTable($data, $replacements) {

        $logger = $this->getLogger();
        $debugLogEnabled = $logger->isDebugEnabled();
        
        $requestType = isset($data['requestType']) ? $data['requestType'] : 'request';

        if ($debugLogEnabled) {
            $logger->debug("requestType = " . $requestType . ", days = " . count($data['days']));
        }
        
        // Show individual comments in table if there are any leave dates with comments
        $displayIndividualComments = false;
        if ($requestType == 'multiple' && count($data['days']) > 1) {
            
            foreach ($data['days'] as $leave) {
                $thisLeaveComment = $leave->getLatestCommentAsText();
                
                $this->getLogger()->debug("Leave Comment: " . $leaveComment);
                if (!empty($thisLeaveComment)) {
                    $displayIndividualComments = true;
                    break;
                }
            }
        }
        
        $details = '<html><body>
                    <style>
                    table, th, td {
                        border: 1px solid black;
                        border-collapse: collapse;
                    }
                    </style>
                    <br /><br />
                    
                    <table style="width:100%">
                      <tr>
                        <th>Date(s)</th>
                        <th>Duration (Hours)</th>';
         if ($displayIndividualComments) {
                $details .= "<th>Comments</th>";
            }
             $details .= "<th>Status</th><th>Leave type</th>";
         
         $leaveComment = '';
        
        if ($requestType == 'request') {
            $leaveComment = $data['request']->getCommentsAsText();
        } elseif ($requestType == 'single') {
            $leaveComment = $data['days'][0]->getCommentsAsText();
        }
     
          if(!empty($leaveComment)){
              $details .= "<th>Leave Comments</th>";  
          }
             
            $details .= "</tr>";
        $workFlows = $data['workFlow'];
        
        foreach ($data['changes'] as $workFlowId => $change) {
            
            if (isset($workFlows[$workFlowId])) {
                $action = ucwords(strtolower($workFlows[$workFlowId]->getAction()));
                $resultingState = ucwords(strtolower($workFlows[$workFlowId]->getResultingState()));
                
              // $details .= "Action: $action, Resulting State: $resultingState\n\n";
            }
            
            // Length of tab (4 spaces) : "    "

            
            $details .= "";
            if ($displayIndividualComments) {
                $details .= "";
            }        

            $details .= "";

            foreach ($change as $leave) {

                $leaveDate = set_datepicker_date_format($leave->getDate());
                $leaveDuration = round($leave->getLengthHours(), 2);

                if ($leaveDuration > 0) {

                    $leaveDuration = $this->_fromatDuration($leaveDuration);
                    $details .= "<tr><td><center>$leaveDate</center></td><td><center>$leaveDuration</center></td>";
                    if ($displayIndividualComments) {
                        $details .= "<td><center>" . $this->trimComment($leave->getLatestCommentAsText())."</center></td>";
                    }
                    $details .= "<td><center>$resultingState</center></td>";
                    $details .= "<td><center>".$replacements['leaveType']."</center></td></tr>";
                }

            }
            
            $details .= "";

        }
        
        
        $details .= "";

        
        if (!empty($leaveComment)) {
            $details .= "<td>$leaveComment</td>";
            $details .= "";
        }
    $details .='</table><br /><br />';
//    $details .='http://'.$_SERVER['SERVER_NAME'].$_SERVER['SCRIPT_NAME'].'/auth/login';
    $details .="<br /><br /></body></html>";   
     return $details;
     

    }
    
    public function getReplacements($data) {
        $data['request'] = $data['days'][0]->getLeaveRequest();
        $replacements = parent::getReplacements($data);
        return $replacements;

    }    
    
    protected function getSubscribers($emailName, $data) {        

        $workFlow = $data['workFlow'];
        $recipients = array();

        $logger = $this->getLogger();
        $debugLogEnabled = $logger->isDebugEnabled();
        
        if (is_array($workFlow)) {
            foreach ($workFlow as $item) {
                $action = strtolower($item->getAction());
                $eventRecipients = parent::getSubscribers('leave.' . $action, $data);

                if ($debugLogEnabled) {
                    $logger->debug('Recipient Count for leave.' . $action . ' = ' . count($eventRecipients));
                }

                // check if already there in recipients:
                foreach ($eventRecipients as $new) {
                    $found = false;

                    if ($debugLogEnabled) {
                        $logger->debug('Looking at recipient: ' . $new->getEmail());
                    }                
                    foreach ($recipients as $existing) {
                        if ($existing->getEmail() == $new->getEmail()) {
                            $found = true;
                            break;
                        }
                    }

                    if (!$found) {
                        $recipients[] = $new;

                        if ($debugLogEnabled) {
                            $logger->debug('Recipient not found, adding to list');
                        }                     
                    }
                }            
            }
        } else {
            $logger->warn('Only one workflow passed to leave.change mail notification');
        }
        
        if ($debugLogEnabled) {
            $logger->debug('Returning Total recipients for leave.change event = ' . count($recipients));
        }
        
        return $recipients;
    }    
    
}

