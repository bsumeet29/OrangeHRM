<?php
if (!empty($overlapWfh) && count($overlapWfh) > 0) {
    if ($workshiftLengthExceeded) {
        $heading = __('Workshift Length Exceeded Due To the Following WFH Requests:');
    } else {
        $heading = count($overlapWfh) == 1 ? __('Overlapping WFH Request Found') : __('Overlapping WFH Requests Found');
    }
    ?>
<div class="box single">
    <div class="head"><h1><?php echo $heading; ?></h1></div>
    <div class="inner">
        <table border="0" cellspacing="0" cellpadding="0" class="table">
            <thead>
                <tr>
                    <th width="200px"><?php echo __("Date") ?></th>
                    <th width="100px"><?php echo __("No of Hours") ?></th>
                    <th width="200px"><?php echo __("Status") ?></th>
                    <th width="150px"><?php echo __("Comments") ?></th>
                </tr>
            </thead>
            <tbody>

<?php
                $oddRow = true;
                foreach ($overlapWfh as $wfh) {
                    $class = $oddRow ? 'odd' : 'even';
                    $oddRow = !$oddRow;
                    
                    $comments = $wfh->getWfhRequest()->getComments() ;
                    
?>
                    <tr class="<?php echo $class; ?>">
                        <td><?php echo $wfh->getDate() ?></td>
                        <td><?php echo $wfh->getLengthHours() ?></td>
                        <td><?php if($wfh->getStatus()=='0'){echo __(ucwords('Pending'));} if($wfh->getStatus()=='2'){echo __(ucwords('Approved'));}?></td>
                        <td><?php echo $comments; ?></td>
                    </tr>
<?php } ?>

            </tbody>
        </table>
    </div>
</div>
<?php } ?>