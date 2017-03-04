<?php
include 'EmailSendParameter.php';

$time = date('i');
$timeInHours = date('H');
//echo "time".$timeInHours;exit;
$sendEmailParameterObject=new EmailSendParameter();

if($timeInHours == 00){
$sendEmailParameterObject->sendEmailOnWorkAnniversary();
$sendEmailParameterObject->sendEmailOnAnniversary();
$sendEmailParameterObject->sendEmailOnEmplBirthday();
$sendEmailParameterObject->sendEmailOnEmpDependentBirthday();
$sendEmailParameterObject->sendEmailOnCompletionProbation();
$sendEmailParameterObject->sendEmailOnDueDateForApprisal();
//$sendEmailParameterObject->sendEmailToPendingLeavenotification();
$sendEmailParameterObject->sendEmailToPendingCompoffnotificationDaily();
$sendEmailParameterObject->sendEmailToCompoffExpirationnotification();
$sendEmailParameterObject->sendEmailToPendingLeavenotificationDaily();
}
if($time == 00){
    $sendEmailParameterObject->sendEmailToPendingCompoffnotificationHourly();
    $sendEmailParameterObject->sendEmailToPendingLeavenotificationHourly();

}
if($timeInHours == (00||04||08||12||16||20)){
    $sendEmailParameterObject->sendEmailToPendingCompoffnotificationEveryFourHours();
    $sendEmailParameterObject->sendEmailToPendingLeavenotificationEveryFourHours();

}
?>
