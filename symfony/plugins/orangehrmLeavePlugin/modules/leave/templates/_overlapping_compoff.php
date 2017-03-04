<?php
if (!empty($overlapCompoff) && count($overlapCompoff) > 0) {
    if ($workshiftLengthExceeded) {
        $heading = __('Workshift Length Exceeded Due To the Following Compoff Requests:');
    } else {
        $heading = count($overlapCompoff) == 1 ? __('Overlapping Compoff Request Found') : __('Overlapping Compoff Requests Found');
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
                foreach ($overlapCompoff as $compoff) {
                    $class = $oddRow ? 'odd' : 'even';
                    $oddRow = !$oddRow;
                    
                    $comments = $compoff->getCompoffRequest()->getComments() ;
                    
?>
                    <tr class="<?php echo $class; ?>">
                        <td><?php echo $compoff->getDate() ?></td>
                        <td><?php echo $compoff->getLengthHours() ?></td>
                        <td><?php if($compoff->getStatus()=='0'){echo __(ucwords('Pending'));} if($compoff->getStatus()=='2'){echo __(ucwords('Approved'));}?></td>
                        <td><?php echo $comments; ?></td>
                    </tr>
<?php } ?>

            </tbody>
        </table>
    </div>
</div>
<?php } ?>