
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

<form role="form" method="post" id="form" enctype="multipart/form-data">
    <input type="hidden" name="tbl" value="<?= $tbl; ?>">
    <input type="hidden" name="act" value="<?= $act; ?>">
    <input type="hidden" name="id" value="<?= $id; ?>">

    <div class="row">
        <div class="alert-submit col-sm-12"></div>
        
        <?php
            echo form_builder($column);
        ?>
        <div class="form-group col-sm-6">
          <label for="image">Image</label>
          <input type="file" class="form-control" name="d">
          <?php 
          if(!empty($r->image)) { 
            $url = $r->image;
            //$url = base_url("uploads/st/$r->st_image");
            echo "<img src='$url' width='100px'>"; 
          } ?>
        </div>

        <div class="col-12 mb-2">
          <input type="submit" value="<?= $submit_name; ?>" id="btn-submit" class="btn btn-primary float-right">
          <a href="#" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
        </div>
    </div>
    
</form>


<script>
  $(function () {
        $('.select2bs4').select2({
            theme: 'bootstrap4',
          //  tags: true
        })

        $('.select2bs42').select2({
            theme: 'bootstrap4',
            tags: true
        })

         //Colorpicker
        $('#my-colorpicker1').colorpicker()
  })
/*
  $('.js-data-example-ajax').select2({
    ajax: {
      url: '<?= base_url("master/book/showauthor"); ?>',
      dataType: 'json',

      // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
    }
  });
  */
  
$('#form').submit(function(e) {
          e.preventDefault();
          var form = $(this);
          var table = $('#table').DataTable();
          
          $("#btn-submit").html('<i class="fa fa-spinner fa-spin"></i>Loading');
          $('#btn-submit').attr('disabled',true);
          $.ajax({
            type:'POST',
            //data:form.serialize(),
            data: new FormData( this ),
            processData: false,
            contentType: false,
            url:"<?= base_url('api/cms/'.$post.''); ?>",
            success:function(data) {
                var message = data.message;
                console.log(data);
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

$( "#harga" ).keyup(function() {
  var kurs = $("#harga").val();
  var ckurs = convert_thousand(kurs);
  $("#harga").val(ckurs);
});

$( "#harga_jual" ).keyup(function() {
  var kurs = $("#harga_jual").val();
  var ckurs = convert_thousand(kurs);
  $("#harga_jual").val(ckurs);
});

$( "#limitpiutang" ).keyup(function() {
  var kurs = $("#limitpiutang").val();
  var ckurs = convert_thousand(kurs);
  $("#limitpiutang").val(ckurs);
});

$("input[data-bootstrap-switch]").each(function(){
      $(this).bootstrapSwitch('state', $(this).prop('checked'));
    });
</script>

<script src="<?= base_url("asset") ?>/plugins/tags/bootstrap-tagsinput.min.js"></script>