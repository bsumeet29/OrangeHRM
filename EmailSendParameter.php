<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EmailSendParameter
 *
 * @author firoj
 */




class EmailSendParameter {
    
    private $conn;
    
    public function __construct() {
        include('dbconfigForsendMail.php');
        $conn =mysql_connect($host, $user, $pass);
        $this->conn=$conn;
        mysql_select_db($database,  $this->conn);
        if (mysql_errno($conn)) {
                 echo "Failed to connect to MySQL: " .  mysql_error($conn);
                    exit();
                  }
    }

     
    public function sendEmailOnAnniversary(){
        date_default_timezone_set("Asia/Kolkata");
        $today=Date('m-d');
        $query="SELECT * FROM hs_hr_employee
                             WHERE emp_marital_status='Married' and
                             SUBSTR(emp_anniversary_date,6)='$today'
                            ";
         //echo $sql;
        $anniversaryData=array();
        $result=  mysql_query($query);
        if(mysql_num_rows($result) > 0){
            while($row= mysql_fetch_array($result)){
                /* get email id of hr   */
                 $hrData=array();
                $sqlForHr='select emp_lastname,emp_firstname,emp_work_email from hs_hr_employee where emp_number IN 
                       (select e.erep_sup_emp_number from hs_hr_emp_reportto as e,ohrm_user as u where e.erep_sub_emp_number='.$row['emp_number'].' and u.emp_number=e.erep_sup_emp_number and u.user_role_id=1)'; 
                $result1=mysql_query($sqlForHr);
               if(mysql_num_rows($result1) >0){
                  while ($row1 = mysql_fetch_row($result1)) {
                      $hr=array('hrLastName'=>$row1[0],'hrFirstName'=>$row1[1],'hrEmailId'=>$row1[2]);
                        $hrData[]=$hr;
                  }
              }else{
                  echo "No HR \n";
              }
                /* end */
                  $anniversaryData[]=array(
                                        'lastName'=>$row['emp_lastname'],
                                        'firstName'=>$row['emp_firstname'],
                                        'middleName'=>$row['emp_middle_name'],
                                        'workEmailId'=>$row['emp_work_email'],
                                        'hrData'=>$hrData
                                        ) ;
            }
            
        }else{
            echo "Today is not anniversary date of any employee\n";
        }
        foreach ($anniversaryData as $data) {
            $cchrEmail=null;
            $subject="Marriage Anniversary Greetings !!!!!!!";
            $body ="Dear ".$data['firstName']." ".$data['lastName'].",";   
            $body .="<br /><br /> On behalf of Perennial Family, we would like to extend our heartiest congratulations to you and your better half on the occasion of your Wedding Anniversary. 
                       <br /><br /> We wish, you both celebrate many more years of happiness together. 
                       <br /><br /> Happy Marriage Anniversary !!!!!!
                        <br /><br />
                        Cheers<br />
                        HR Team
                     ";
            $to=$data['workEmailId'];
            if(isset($data['hrData'])){
            $hrData=$data['hrData'];
            foreach ($hrData as $hr)
            $cchrEmail[]=$hr['hrEmailId'];
            }
            $this->sendEmailToCandidate($to, $subject, $body,$cchrEmail);
        }
    } 
    
    public function sendEmailOnWorkAnniversary(){
        date_default_timezone_set("Asia/Kolkata");
        $today=Date('m-d');
        $checkToday=Date('Y-m-d');
        $query="SELECT * FROM hs_hr_employee
                             WHERE SUBSTR(joined_date,6)='$today'
                            ";
         //echo $sql;
        $empWorkAnniversayDayData=array();
        $result=  mysql_query($query);
        if(mysql_num_rows($result) > 0){
            while($row= mysql_fetch_array($result)){
                /* get email id of hr   */
                 $hrData=array();
                 $sqlForHr='select emp_lastname,emp_firstname,emp_work_email from hs_hr_employee where emp_number IN 
                       (select e.erep_sup_emp_number from hs_hr_emp_reportto as e,ohrm_user as u where e.erep_sub_emp_number='.$row['emp_number'].' and u.emp_number=e.erep_sup_emp_number and u.user_role_id=1)'; 
                $result1=mysql_query($sqlForHr);
               if(mysql_num_rows($result1) >0){
                  while ($row1 = mysql_fetch_row($result1)) {
                      $hr=array('hrLastName'=>$row1[0],'hrFirstName'=>$row1[1],'hrEmailId'=>$row1[2]);
                        $hrData[]=$hr;
                  }
              }else{
                  echo "No HR for work anniversary \n";
              }
                /* end */
                $d1 = new DateTime($row['joined_date']);
                $d2 = new DateTime($checkToday);
                
                  $empWorkAnniversayDayData[]=array(
                                        'lastName'=>$row['emp_lastname'],
                                        'firstName'=>$row['emp_firstname'],
                                        'middleName'=>$row['emp_middle_name'],
                                        'workEmailId'=>$row['emp_work_email'],
                                        'hrData'=>$hrData,
                                        'years'=>$d1->diff($d2)->y
                                        ) ;
            }
            
        }else{
            echo "Today is not work anniversary  of any employee\n";
        }
        
        foreach ($empWorkAnniversayDayData as $data) {
            $cchrEmail=null;
            $subject="Work Anniversary Greetings !!!!!!!";
            $body ="Dear ".$data['firstName']." ".$data['lastName'].",";   
            $body .="<br /><br />We extend our best wishes to you on your ".$data['years']." anniversary of service with Perennial Systems.<br />
                     We've always taken great pleasure to see your enthusiasm for work you do.   
                     <br /><br />Hoping that you will remain with us for many years to come !!!!.
                     <br /><br />
                     Cheers<br />
                     HR Team
                     ";
            $to=$data['workEmailId'];
            if(isset($data['hrData'])){
            $hrData=$data['hrData'];
            foreach ($hrData as $hr)
            $cchrEmail[]=$hr['hrEmailId'];
            }
            $this->sendEmailToCandidate($to, $subject, $body,$cchrEmail);
        }
        
        
    }

    public function sendEmailOnEmplBirthday(){
        date_default_timezone_set("Asia/Kolkata");
        $today=Date('m-d');
        $query="SELECT * FROM hs_hr_employee
                             WHERE SUBSTR(emp_birthday,6)='$today'
                            ";
         //echo $sql;
        $empBirthDayData=array();
        $result=  mysql_query($query);
        if(mysql_num_rows($result) > 0){
            while($row= mysql_fetch_array($result)){
                /* get email id of hr   */
                 $hrData=array();
                 $sqlForHr='select emp_lastname,emp_firstname,emp_work_email from hs_hr_employee where emp_number IN 
                       (select e.erep_sup_emp_number from hs_hr_emp_reportto as e,ohrm_user as u where e.erep_sub_emp_number='.$row['emp_number'].' and u.emp_number=e.erep_sup_emp_number and u.user_role_id=1)'; 
                $result1=mysql_query($sqlForHr);
               if(mysql_num_rows($result1) >0){
                  while ($row1 = mysql_fetch_row($result1)) {
                      $hr=array('hrLastName'=>$row1[0],'hrFirstName'=>$row1[1],'hrEmailId'=>$row1[2]);
                        $hrData[]=$hr;
                  }
              }else{
                  echo "No HR \n";
              }
                /* end */
                  $empBirthDayData[]=array(
                                        'lastName'=>$row['emp_lastname'],
                                        'firstName'=>$row['emp_firstname'],
                                        'middleName'=>$row['emp_middle_name'],
                                        'workEmailId'=>$row['emp_work_email'],
                                        'hrData'=>$hrData
                                        ) ;
            }
            
        }else{
            echo "Today is not birthday of any employee\n";
        }
        
        foreach ($empBirthDayData as $data) {
            $cchrEmail=null;
            $subject="Birthday Greetings !!!!!!!";
            $body ="Dear ".$data['firstName']." ".$data['lastName'].",";   
            $body .="<br /><br />On behalf of Perennial Family, we hope that you have a great year and accomplish all the fabulous goals you have set.<br />Wish you a Happy Birthday !!!!  
                      <br /><br />May you get the best of everything in life.
                        <br /><br />
                        Cheers<br />
                        HR Team
                     ";
            $to=$data['workEmailId'];
            if(isset($data['hrData'])){
            $hrData=$data['hrData'];
            foreach ($hrData as $hr)
            $cchrEmail[]=$hr['hrEmailId'];
            }
            $this->sendEmailToCandidate($to, $subject, $body,$cchrEmail);
        }
        
        
    }
    
    public function sendEmailOnEmpDependentBirthday(){
        date_default_timezone_set("Asia/Kolkata");
         $today=Date('m-d');
         $query="SELECT 
                    e.emp_number,e.emp_lastname,e.emp_firstname,e.emp_middle_name,e.emp_work_email,
                    d.ed_name,d.ed_relationship_type,d.ed_relationship
                    FROM hs_hr_employee as e, hs_hr_emp_dependents as d
                    WHERE
                      e.emp_number=d.emp_number and 
                      SUBSTR(d.ed_date_of_birth,6)='$today'
                                   ";
        // echo $query;
        $empDependentBirthDayData=array();
        $result=  mysql_query($query);
        if(mysql_num_rows($result) > 0){
            while($row= mysql_fetch_array($result)){
                if($row['ed_relationship_type']=='other'){
                    $relationship=$row['ed_relationship'];
                }else{
                    $relationship=$row['ed_relationship_type'];
                }
                /* get email id of hr   */
                 $hrData=array();
                 $sqlForHr='select emp_lastname,emp_firstname,emp_work_email from hs_hr_employee where emp_number IN 
                       (select e.erep_sup_emp_number from hs_hr_emp_reportto as e,ohrm_user as u where e.erep_sub_emp_number='.$row['emp_number'].' and u.emp_number=e.erep_sup_emp_number and u.user_role_id=1)'; 
                $result1=mysql_query($sqlForHr);
               if(mysql_num_rows($result1) >0){
                  while ($row1 = mysql_fetch_row($result1)) {
                      $hr=array('hrLastName'=>$row1[0],'hrFirstName'=>$row1[1],'hrEmailId'=>$row1[2]);
                        $hrData[]=$hr;
                  }
              }else{
                  echo "No HR \n";
              }
                /* end */
                  $empDependentBirthDayData[]=array(
                                        'lastName'=>$row['emp_lastname'],
                                        'firstName'=>$row['emp_firstname'],
                                        'middleName'=>$row['emp_middle_name'],
                                        'workEmailId'=>$row['emp_work_email'],
                                        'EmpDependentName'=>$row['ed_name'],
                                        'EmpDependentRelationship'=>$relationship,
                                        'hrData'=>$hrData
                                        );
            }
            
        }else{
            echo "Today is not birthday of any employee depedent\n";
        }
        
        foreach ($empDependentBirthDayData as $data) {
            $cchrEmail=null;
            $subject="Birthday Greetings !!!!!!!";
            $body ="Hi ".$data['firstName']." ".$data['lastName'].",";   
            $body .="<br /><br />On behalf of Perennial Family, we would like to extend our best wishes to your ".$data['EmpDependentRelationship']." ".ucfirst($data['EmpDependentName'])." on this day !!!!  
                      
                        <br /><br />
                        Cheers<br />
                        HR Team
                     ";
            $to=$data['workEmailId'];
            if(isset($data['hrData'])){
            $hrData=$data['hrData'];
            foreach ($hrData as $hr)
            $cchrEmail[]=$hr['hrEmailId'];
            }
            $this->sendEmailToCandidate($to, $subject, $body,$cchrEmail);
        }
        
    }
    
    // email reminde come to  lead and hr 2 week prior
    public function sendEmailOnCompletionProbation(){
        date_default_timezone_set("Asia/Kolkata");
        $today=Date('Y-m-d');
         $query="SELECT 
                    e.emp_number,e.emp_lastname,e.emp_firstname,e.emp_middle_name,
                    c.econ_extend_end_date
                    FROM hs_hr_employee as e,hs_hr_emp_contract_extend as c
                    WHERE
                      e.emp_number=c.emp_number and 
                      DATEDIFF(SUBSTR(c.econ_extend_end_date,1,10),'".$today."')=14";
         //echo $query;
         
        $empCompletionProbationData=array();
        $result=  mysql_query($query);
        if(mysql_num_rows($result) > 0){
            while($row= mysql_fetch_array($result)){
                
                $date=date('Y-m-d',strtotime($row['econ_extend_end_date']) );
                $probationCompleteDate = $date;
                $supervisorData=array();
                $sqlForSupervisor='select emp_lastname,emp_firstname,emp_work_email from hs_hr_employee where emp_number IN 
                       (select erep_sup_emp_number from hs_hr_emp_reportto where erep_sub_emp_number='.$row['emp_number'].')'; 
                $result1=mysql_query($sqlForSupervisor);
               if(mysql_num_rows($result1) >0){
                  while ($row1 = mysql_fetch_row($result1)) {
                      $supervisor=array('empSuperviosrLastName'=>$row1[0],'empSuperviosrFirstName'=>$row1[1],'supervisorEmailId'=>$row1[2]);
                        $supervisorData[]=$supervisor;
                  }
              }else{
                  echo "No Supervisor assigne\n";
              }
                $empCompletionProbationData[]=array(
                                        'lastName'=>$row['emp_lastname'],
                                        'firstName'=>$row['emp_firstname'],
                                        'middleName'=>$row['emp_middle_name'],
                                        'probationCompeleteDate'=>$probationCompleteDate,
                                        'empSupervisorData'=>$supervisorData
                                        );
            }
            
        }else{
            echo "Today is not probation complete day data of any employee\n ";
        }
        
        foreach ($empCompletionProbationData as $probationCompletion) {
            foreach ($probationCompletion['empSupervisorData'] as $supervisorOfEmp){
                $to=$supervisorOfEmp['supervisorEmailId'];
                $subject='Reminder: Completion of probationary period for '.$probationCompletion['firstName'];
                $body='Hi '.$supervisorOfEmp['empSuperviosrFirstName'].',<br /><br />';
                $body .="Reminder: For completion of probationary period for ".$probationCompletion['lastName']." ". $probationCompletion['firstName']." ".$probationCompletion['middleName'];
                $body .=" on ".$probationCompletion['probationCompeleteDate']."<br /><br />";
                $body .="";
                $body .="Regards<br />
                        HR Team";
               $this->sendEmailToCandidate($to, $subject, $body);
            }
        }
         
    }
    
    //email reminder to hr 2 week prior
    
    public function sendEmailOnDueDateForApprisal(){
        date_default_timezone_set("Asia/Kolkata");
        $today=Date('Y-m-d');
         $query="SELECT 
                    e.emp_number,e.emp_lastname,e.emp_firstname,e.emp_middle_name,
                    e.next_appraisal_date
                    FROM hs_hr_employee as e
                    WHERE DATEDIFF( e.next_appraisal_date,'".$today."')=14";
       // echo $query;
         
        $empnextApprisalDateData=array();
        $result=  mysql_query($query);
        if(mysql_num_rows($result) > 0){
            while($row= mysql_fetch_array($result)){
                
                $supervisorData=array();
                $sqlForSupervisor='select emp_lastname,emp_firstname,emp_work_email from hs_hr_employee where emp_number IN 
                       (select e.erep_sup_emp_number from hs_hr_emp_reportto as e,ohrm_user as u where e.erep_sub_emp_number='.$row['emp_number'].' and u.emp_number=e.erep_sup_emp_number and u.user_role_id=1)'; 
                $result1=mysql_query($sqlForSupervisor);
               if(mysql_num_rows($result1) >0){
                  while ($row1 = mysql_fetch_row($result1)) {
                      $supervisor=array('empSuperviosrLastName'=>$row1[0],'empSuperviosrFirstName'=>$row1[1],'supervisorEmailId'=>$row1[2]);
                        $supervisorData[]=$supervisor;
                  }
              }else{
                  echo "No Supervisor assigne\n";
              }
                $empnextApprisalDateData[]=array(
                                        'lastName'=>$row['emp_lastname'],
                                        'firstName'=>$row['emp_firstname'],
                                        'middleName'=>$row['emp_middle_name'],
                                        'nextApprisalDate'=>$row['next_appraisal_date'],
                                        'empSupervisorData'=>$supervisorData
                                        );
            }
            
        }else{
            echo "Today is not next apprisal  day data of any employee\n ";
        }
        
        foreach ($empnextApprisalDateData as $apprisalCompletion) {
            foreach ($apprisalCompletion['empSupervisorData'] as $supervisorOfEmp){
                $to=$supervisorOfEmp['supervisorEmailId'];
                $subject='Reminder: For next apprisal date of '.$apprisalCompletion['firstName'];
                $body='Hi '.$supervisorOfEmp['empSuperviosrFirstName'].',<br /><br />';
                $body .="Reminder: For next apprisal date of ".$apprisalCompletion['lastName']." ". $apprisalCompletion['firstName']." ".$apprisalCompletion['middleName'];
                $body .=" on ".$apprisalCompletion['nextApprisalDate']."<br /><br />";
                $body .="";
                $body .="Regards<br />
                        HR Team";
               $this->sendEmailToCandidate($to, $subject, $body);
            }
        }
    
    }
    
   
    public function sendEmailToCandidate($to,$subject,$body,$cc=NULL){
      $sql="select * from ohrm_email_configuration where id=1";
      $result=mysql_query($sql);
      if(mysql_num_rows($result) >0){
          while($row=mysql_fetch_row($result)){
              $mail_type=$row[1];
              $sentas=$row[2];
              $senmail_path=$row[3];
              $smtp_host=$row[4];
              $smtp_port=$row[5];
              $smtp_username=$row[6];
              $smtp_password=$row[7];
              $encryption_type=$row[9];
             }
          }else{
               echo "Email Configuration is not completed\n";
               return FALSE;
              }
         try{
              if($mail_type =='smtp'){
                // Create the Transport
                $transport = Swift_SmtpTransport::newInstance($smtp_host, $smtp_port,$encryption_type)
                                ->setUsername($smtp_username)
                                ->setPassword($smtp_password); 
                 }elseif ($mail_type =='sendmail') {
                     $transport = Swift_SendmailTransport::newInstance($senmail_path);
                    }
             // Create the Mailer using your created Transport
             $mailer = Swift_Mailer::newInstance($transport);
            // Create a message
            $message = Swift_Message::newInstance($subject)
                ->setFrom(array($sentas => 'Perennial Family'))
                ->setTo($to)
                ->setBody($body,'text/html');
           $message->setCc($cc) ;
            // Send the message
            $sendemaildata = @ $mailer->send($message,$failures);
            
         }catch (Exception $e){echo $e->getMessage();}
         return $sendemaildata;     
          
   }
   
  /*  public function sendEmailToPendingLeavenotification(){
     date_default_timezone_set("Asia/Kolkata");
     $today=date('Y-m-d');
     $sql="select l.id,l.date,l.emp_number,l.leave_request_date,e.emp_firstname,e.emp_lastname from ohrm_leave l,hs_hr_employee e
           where l.emp_number=e.emp_number and l.status='1' and DATEDIFF('".$today."',l.leave_request_date) > 2";
    $pendingLeaveData=array();
     $result=mysql_query($sql);
     if(mysql_num_rows($result) > 0){
         while ($row = mysql_fetch_row($result)) {
            $supervisorEmail=array();
            $data=array('leave_id'=>$row[0],'leave_applied_date'=>$row[1],'leave_request_date'=>$row[3],
                    'emp_name'=>$row[4].' '.$row[5]);

            $sqlForSupervisor='select emp_work_email from hs_hr_employee where emp_number IN 
                       (select erep_sup_emp_number from hs_hr_emp_reportto where erep_sub_emp_number='.$row[2].')'; 
            $result2=mysql_query($sqlForSupervisor);
              if(mysql_num_rows($result2) >0){
                  while ($row1 = mysql_fetch_row($result2)) {
                        $supervisorEmail[]=$row1[0];
                  }
              }else{
                  echo "No Supervisor assigned\n";
              }
              $data['supervisorEmail']=$supervisorEmail; 
              
              
            $pendingLeaveData[]=$data; 
         }
      }else{echo "No pending leave\n";}
      
     foreach ($pendingLeaveData as $value) {
         $subject="The leave needs to be looked at pending leaves of ".$value['emp_name'];
         $body="<html><body><p>Hi Team,<br /><br />".$value['emp_name'].
                    " has applied leave for the date ".$value['leave_applied_date']." on the date ".$value['leave_request_date'].
                    "<br />Please take an appropriate action on this leave<br /><br />
                        Thanks and regards,<br />
                        Admin<br /><br />This is auto generated mail for reminder</p></body></html>";
       $to=$value['supervisorEmail'];
       $this->sendEmailToCandidate($to, $subject, $body);
    
         
     }
     
  }*/ 
   public function sendEmailToPendingLeavenotificationDaily(){
       date_default_timezone_set("Asia/Kolkata");
     //$today=date('Y-m-d');
       $this->sendEmailToPendingLeavenotification("DATEDIFF(l.date,'".date('Y-m-d')."') > 2",2);
   }
   public function sendEmailToPendingLeavenotificationEveryFourHours(){
       date_default_timezone_set("Asia/Kolkata");
     //$today=date('Y-m-d');
       $this->sendEmailToPendingLeavenotification("DATEDIFF(l.date,'".date('Y-m-d')."') = 2",2);
   }
   public function sendEmailToPendingLeavenotificationHourly(){
       date_default_timezone_set("Asia/Kolkata");
     //$today=date('Y-m-d');
       $this->sendEmailToPendingLeavenotification("DATEDIFF(l.date,'".date('Y-m-d')."') = 1",null);
   }
  
   public function sendEmailToPendingLeavenotification($condition,$reportingType){
     
     $sql="select l.id,l.date,l.emp_number,l.leave_request_date,e.emp_firstname,e.emp_lastname from ohrm_leave l,hs_hr_employee e
           where l.emp_number=e.emp_number and l.status='1' and $condition ";
    $pendingLeaveData=array();
     $result=mysql_query($sql);
     if(mysql_num_rows($result) > 0){
         while ($row = mysql_fetch_row($result)) {
            $supervisorEmail=array();
            $data=array('leave_id'=>$row[0],'leave_applied_date'=>$row[1],'leave_request_date'=>$row[3],
                    'emp_name'=>$row[4].' '.$row[5]);
            if($reportingType==null){
            $sqlForSupervisor='select emp_work_email from hs_hr_employee where emp_number IN 
                       (select erep_sup_emp_number from hs_hr_emp_reportto where erep_sub_emp_number='.$row[2].')'; 
            }
            else{
                $sqlForSupervisor='select emp_work_email from hs_hr_employee where emp_number IN 
                       (select erep_sup_emp_number from hs_hr_emp_reportto where erep_sub_emp_number='.$row[2].$row[2].'and erep_reporting_mode='.$reportingType.')';
            }
            $result2=mysql_query($sqlForSupervisor);
              if(mysql_num_rows($result2) >0){
                  while ($row1 = mysql_fetch_row($result2)) {
                        $supervisorEmail[]=$row1[0];
                  }
              }else{
                  echo "No Supervisor assigned\n";
              }
              $data['supervisorEmail']=$supervisorEmail; 
              
              
            $pendingLeaveData[]=$data; 
         }
      }else{echo "No pending leave\n";}
      
     foreach ($pendingLeaveData as $value) {
         $subject="The leave needs to be looked at:- pending leaves of ".$value['emp_name'];
         $body="<html><body><p>Hi Team,<br /><br />".$value['emp_name'].
                    " has applied leave for the date ".$value['leave_applied_date']." on the date ".$value['leave_request_date'].
                    "<br />Please take an appropriate action on this leave<br /><br />
                        Thanks and regards,<br />
                        Admin<br /><br />This is auto generated mail for reminder</p></body></html>";
       $to=$value['supervisorEmail'];
       $this->sendEmailToCandidate($to, $subject, $body);
    
         
     }
     
  }
   public function sendEmailToCompoffExpirationnotification(){
     date_default_timezone_set("Asia/Kolkata");
     $today=date('Y-m-d');
    $sql="select c.id,c.date,c.emp_number,c.compoff_request_date,e.emp_firstname,e.emp_lastname from ohrm_compoff c,hs_hr_employee e
           where c.emp_number=e.emp_number and c.status='2' and DATEDIFF(c.date,'".$today."') = 1";
    $compoffExpirationData=array();
     $result=mysql_query($sql);
     if(mysql_num_rows($result) > 0){
         while ($row = mysql_fetch_row($result)) {
            $candidateEmail=array();
            $data=array('compoff_id'=>$row[0],'compoff_applied_date'=>$row[1],'compoff_request_date'=>$row[3],
                    'emp_name'=>$row[4].' '.$row[5]);

            $sqlCandidateEmail='select emp_work_email from hs_hr_employee where emp_number = '.$row[2]; 
            $result2=mysql_query($sqlCandidateEmail);
              if(mysql_num_rows($result2) >0){
                  while ($row1 = mysql_fetch_row($result2)) {
                        $candidateEmail[]=$row1[0];
                  }
              }else{
                 // echo "No Supervisor assigned\n";
              }
              $data['candidateEmail']=$candidateEmail; 
              
              
            $compoffExpirationData[]=$data; 
         }
      }else{echo "No expired compoff\n";
      
      }
      
     foreach ($compoffExpirationData as $value) {
         $subject="Your compoff is about to expire ".$value['emp_name'];
         $body="<html><body><p>Hi<br /><br />".$value['emp_name'].
                    " Your compoff will expire tommorrow on the date ".$value['compoff_applied_date'].
                    "<br />You can request for extension of date by today<br /><br />
                        Thanks and regards,<br />
                        Admin<br /><br />This is auto generated mail for reminder</p></body></html>";
       $to=$value['candidateEmail'];
       $this->sendEmailToCandidate($to, $subject, $body);
    
         
     }
     
  }
  public function sendEmailToPendingCompoffnotificationDaily(){
     date_default_timezone_set("Asia/Kolkata");        
    $this->sendEmailToPendingCompoffnotification("DATEDIFF(c.date,'".date('Y-m-d')."') > 2",2);
     
  }
   public function sendEmailToPendingCompoffnotificationEveryFourHours(){
     
     date_default_timezone_set("Asia/Kolkata");
     $this->sendEmailToPendingCompoffnotification("DATEDIFF(c.date,'".date('Y-m-d')."') = 2",2);
    
     
  }
  public function sendEmailToPendingCompoffnotificationHourly(){
      
      date_default_timezone_set("Asia/Kolkata");        
    $this->sendEmailToPendingCompoffnotification("DATEDIFF(c.date,'".date('Y-m-d')."') = 1",null);
  }
  public function sendEmailToPendingCompoffnotification($sqlcoditin,$reportingType){
      $sql="select c.id,c.date,c.emp_number,c.compoff_request_date,e.emp_firstname,e.emp_lastname from ohrm_compoff c,hs_hr_employee e
           where c.emp_number=e.emp_number and c.status='0' and $sqlcoditin";
      $pendingLeaveData=array();
     $result=mysql_query($sql);
     if(mysql_num_rows($result) > 0){
         while ($row = mysql_fetch_row($result)) {
            $supervisorEmail=array();
            $data=array('compoff_id'=>$row[0],'compoff_applied_date'=>$row[1],'compoff_request_date'=>$row[3],
                    'emp_name'=>$row[4].' '.$row[5]);
           if($reportingType==null){
            $sqlForSupervisor='select emp_work_email from hs_hr_employee where emp_number IN 
                       (select erep_sup_emp_number from hs_hr_emp_reportto where erep_sub_emp_number='.$row[2].')'; 
           }
           else{
               $sqlForSupervisor='select emp_work_email from hs_hr_employee where emp_number IN 
                       (select erep_sup_emp_number from hs_hr_emp_reportto where erep_sub_emp_number='.$row[2].'and erep_reporting_mode='.$reportingType.')';
           }
           $result2=mysql_query($sqlForSupervisor);
              if(mysql_num_rows($result2) >0){
                  while ($row1 = mysql_fetch_row($result2)) {
                        $supervisorEmail[]=$row1[0];
                  }
              }else{
                  echo "No Supervisor assigned\n";
              }
              $data['supervisorEmail']=$supervisorEmail; 
              
              
            $pendingLeaveData[]=$data; 
         }
      }else{echo "No pending compoff\n";}
      
     foreach ($pendingLeaveData as $value) {
         $subject="The compoff needs to be looked at pending compoff of ".$value['emp_name'];
         $body="<html><body><p>Hi Team,<br /><br />".$value['emp_name'].
                    " has applied for compoff for the date ".$value['compoff_applied_date']." on the date ".$value['compoff_request_date'].
                    "<br />Please take an appropriate action on this compoff<br /><br />
                        Thanks and regards,<br />
                        Admin<br /><br />This is auto generated mail for reminder</p></body></html>";
       $to=$value['supervisorEmail'];
       $this->sendEmailToCandidate($to, $subject, $body);
    
         
     }
  } 
}



?>
