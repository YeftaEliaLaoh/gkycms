
<link rel="stylesheet" href="<?= base_url("asset"); ?>/plugins/tags/bootstrap-tagsinput.css">
<style>
.label-info {
    background-color: #5bc0de;
}
.label {
    display: inline;
    padding: .2em .6em .3em;
    font-size: 75%;
    font-weight: 700;
    line-height: 1;
    color: #fff;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: .25em;
}

.bootstrap-tagsinput {
  width: 100% !important;
}
</style>

<form role="form" method="post" id="form">
    <input type="hidden" name="tbl" value="<?= $tbl; ?>">
    <input type="hidden" name="act" value="<?= $act; ?>">
    <input type="hidden" name="id" value="<?= $id; ?>">

    <div class="row">
        <div class="alert-submit col-sm-12"></div>
        <?php
            echo form_builder($column);
        ?>
        <!--
        <div class="col-sm-6">
            <div class="form-group">
            <label>Nama</label>
            <input type="text" class="form-control" placeholder="Enter ...">
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
            <label>Text Disabled</label>
            <input type="text" class="form-control" placeholder="Enter ..." disabled="">
            </div>
        </div>
        !-->

        <div class="col-12 mb-2">
          <input type="submit" value="<?= $submit_name; ?>" id="btn-submit" class="btn btn-success float-right">
          <a href="#" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
        </div>
    </div>
    
</form>


<script>
  $(function () {
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        })

         //Colorpicker
        $('#my-colorpicker1').colorpicker()
             
  <?php
    if(!empty($id)) { 
        echo "checktype('$level');";

    }
    ?>
  })

  

 function checktype(val) {
    
    if(val != 'admin') {
        $("#div_cabang").show();
        
        if(val == 'salesadmin' || val == 'salesstaff') {
            $("#div_salesname").show();
            
        }
    } 
    else {
        $("#div_salesname").hide();
        $("#div_cabang").hide();
    }
 }
  
$('#form').submit(function(e) {
          e.preventDefault();
          var form = $(this);
          var table = $('#table').DataTable();
          
          $("#btn-submit").html('<i class="fa fa-spinner fa-spin"></i>Loading');
          $('#btn-submit').attr('disabled',true);
          $.ajax({
            type:'POST',
            data:form.serialize(),
            url:"<?= base_url('api/cms/'.$post.''); ?>",
            success:function(data) {
                var message = data.message;
               // console.log("haii");
                if(data.status == 1) {
                   // location.reload();
                   table.ajax.reload();
                   $('#modal-close4').trigger('click');
                   toastr.success('Berhasil di perbaharui');
                } else {
                    toastr.error(message);
                    $("#btn-submit").html('<?= $submit_name; ?>');
                    $('#btn-submit').removeAttr('disabled');
                    $(".alert-submit").html('<div class="alert alert-danger alert-dismissible fade show text-center margin-bottom-1x"><span class="alert-close" data-dismiss="alert"></span><i class="icon-alert-triangle"></i>&nbsp;&nbsp;<span class="text-medium">'+message+'</div>');
                }
                
                
            }
          });
          
});

$("input[data-bootstrap-switch]").each(function(){
      $(this).bootstrapSwitch('state', $(this).prop('checked'));
    });
</script>

<script src="<?= base_url("asset") ?>/plugins/tags/bootstrap-tagsinput.min.js"></script>