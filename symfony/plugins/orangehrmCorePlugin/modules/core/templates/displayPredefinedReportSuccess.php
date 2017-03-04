
<?php

if ($reportPermissions->canRead()) {
    
    include_component('core', 'ohrmList', $parmetersForListComponent);
    
?>
   <input type="button" class="reset" id="btnBack" name="btnBack" value="Back" style="margin-left: 21px;margin-bottom: 30px;">
   
   <a title="<?php echo __("Download"); ?>" target="_blank" class="download" 
      href="<?php echo url_for('core/reportDownloade?reportId='.$reportId);?>">
       <input type="button" value="<?php echo __('Download Report') ?>" style="margin-left: 21px;margin-bottom: 30px;" />
   </a>
      
  
<?php } ?>



<script type="text/javascript">
  $('#btnBack').click(function() {
            location.href = "<?php echo url_for('core/viewDefinedPredefinedReports/reportGroup/3/reportType/PIM_DEFINED') ?>";
        });
  </script>

