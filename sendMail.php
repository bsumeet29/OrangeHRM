<?php
//

//require_once('symfony\apps\orangehrm\lib\model\core\Service\EmailService.php');
//require_once ('symfony\lib\vendor\symfony\lib\config\sfConfig.class.php');
//require_once('symfony\lib\vendor\symfony\lib\vendor\swiftmailer\swift_required.php');

include('dbconfigForsendMail.php');
//$conn=  mysqli_connect($host, $user, $pass, $database);
$conn =mysql_connect($host, $user, $pass);
       mysql_select_db($database,$conn);
  if (mysql_errno($conn)) {
   echo "Failed to connect to MySQL: " .  mysql_error($conn);
   exit();
   }
   $emailData=  getSendEmailData();    
   //echo "The emai Data <pre>";
  // print_r($emailData);
   
   if($emailData !=NULL){
      
      $senmailData=sendMailToLeadAndHR($emailData);
      
    }
  if($senmailData !=null){
   // print_r($senmailData);
     echo  "$senmailData email send to Lead and HR";
   }  else {
       echo  "email not send to Lead and HR";
       
  }
   
  function sendMailToLeadAndHR($emailData){
      global $conn;
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
             echo "Email Configuration is not completed";
              return FALSE;
          }
          try{
              if($mail_type =='smtp'){
                // Create the Transport
                $transport = Swift_SmtpTransport::newInstance($smtp_host, $smtp_port,$encryption_type)
                     ->setUsername($smtp_username)
                     ->setPassword($smtp_password)
                         ; 
              }elseif ($mail_type =='sendmail') {
               $transport = Swift_SendmailTransport::newInstance($senmail_path);
            }
         // Create the Mailer using your created Transport
        $mailer = Swift_Mailer::newInstance($transport);
        // Create a message
        foreach ($emailData as $value) {
            $subject="The leave needs to be looked at pending leaves of ".$value['emp_name'];
            $messageBody="<html><body><p>Hi Team,<br /><br />".$value['emp_name'].
                    " has applied leave for the date ".$value['leave_applied_date']." on the date ".$value['leave_request_date'].
                    "<br />Please take an appropredate action on this leave<br /><br />
                        Thanks and regards,<br />
                        Admin<br /><br />This is auto generated mail for reminder</p></body></html>";
            $message = Swift_Message::newInstance($subject)
            ->setFrom(array($sentas => 'Perennial Family'))
            ->setTo($value['supervisorEmail'])
            ->setBody($messageBody,'text/html')
            ;
            // Send the message
            $sendemaildata = @ $mailer->send($message,$failures);
            }
        }  catch (Exception $e){
              echo $e->getMessage();
          }
          return $sendemaildata;
  }
  
  
  function getSendEmailData(){
     global $conn;
     date_default_timezone_set("Asia/Kolkata");
     $today=date('Y-m-d');
     $sql="select l.id,l.date,l.emp_number,l.leave_request_date,e.emp_firstname,e.emp_lastname from ohrm_leave l,hs_hr_employee e
           where l.emp_number=e.emp_number and l.status='1' and DATEDIFF('".$today."',l.leave_request_date) > 2";
    // echo "<pre>";
     //echo $sql;
     $pendingLeaveData=array();
    // $result=mysqli_query($conn, $sql);
      $result=mysql_query($sql);
     if(mysql_num_rows($result) > 0){
         while ($row = mysql_fetch_row($result)) {
            $supervisorEmail=array();
            $data=array('leave_id'=>$row[0],'leave_applied_date'=>$row[1],'leave_request_date'=>$row[3],
                    'emp_name'=>$row[4].' '.$row[5]);

            $sqlForSupervisor='select emp_work_email from hs_hr_employee where emp_number IN 
                       (select erep_sup_emp_number from hs_hr_emp_reportto where erep_sub_emp_number='.$row[2].')'; 
         //    echo $sqlForSupervisor,"<br />";
            $result2=mysql_query($sqlForSupervisor);
              if(mysql_num_rows($result2) >0){
                  while ($row1 = mysql_fetch_row($result2)) {
                        $supervisorEmail[]=$row1[0];
                  }
              }else{
                  echo "No Supervisor assigne\n";
              }
              $data['supervisorEmail']=$supervisorEmail; 
              //print_r($supervisorEmail);
              
            $pendingLeaveData[]=$data; 
         }
      }else{
         echo "No pending leave\n";
     }
     return $pendingLeaveData;
              
 }
?>
