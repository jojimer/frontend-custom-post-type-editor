<!-- Edit Report Modal Content -->
<div class="modal-header">
  <h5 class="modal-title font-weight-bold" id="editReportTitle">Edit Report</h5>
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<div class="modal-body">
    <?php include_once(FFRCRUD_PATH.'includes/template/edit_report_form.php'); ?>
</div>
<div class="modal-footer">
  <button id="submitUpdate" type="button" class="btn btn-primary">Update Report</button>
</div>