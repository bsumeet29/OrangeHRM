<?php
require_once sfConfig::get('sf_root_dir').'/lib/vendor/symfony/lib/vendor/swiftmailer/swift_required.php';
exit();
$host="localhost:3306";
$user="root";
$pass="";
$database="perennialhrm_mysql";
$conn=  mysqli_connect($host, $user, $pass, $database);
  if (mysqli_connect_errno()) {
   echo "Failed to connect to MySQL: " . mysqli_connect_error();
   exit();
   }
   $emailData=sendEmail();    
   print_r($emailData);
   
 function sendEmail(){
     global $conn;
     $today=date('Y-m-d');
     $sql="select l.id,l.date,l.emp_number,l.leave_request_date,e.emp_firstname,e.emp_lastname from ohrm_leave l,hs_hr_employee e
           where l.emp_number=e.emp_number and status='1'and DATEDIFF('".$today."',l.leave_request_date) > 2";
     echo "<pre>";
     
     $result=mysqli_query($conn, $sql);
     if(mysqli_num_rows($result) > 0){
         while ($row = mysqli_fetch_row($result)) {
            $supervisorEmail=array();
            $data=array('leave_id'=>$row[0],'leave_applied_date'=>$row[1],'leave_request_date'=>$row[3],
                    'emp_name'=>$row[4].' '.$row[5]);

            $sqlForSupervisor='select emp_work_email from hs_hr_employee where emp_number IN 
                       (select erep_sup_emp_number from hs_hr_emp_reportto where erep_sub_emp_number='.$row[2].')'; 
         //    echo $sqlForSupervisor,"<br />";
            $result2=mysqli_query($conn, $sqlForSupervisor);
              if(mysqli_num_rows($result2) >0){
                  while ($row1 = mysqli_fetch_row($result2)) {
                        $supervisorEmail[]=$row1[0];
                  }
              }else{
                  echo "No Supervisor assigne";
              }
              $data['supervisorEmail']=$supervisorEmail; 
              //print_r($supervisorEmail);
              
            $pendingLeaveData[]=$data; 
         }
      }else{
         echo "NO pending Leave";
     }
     return $pendingLeaveData;
              
 }
?>
