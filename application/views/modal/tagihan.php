
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
<form role="form" method="post" id="form-tagihan">
    <input type="hidden" name="tbl" value="<?= $tbl; ?>">
    <input type="hidden" name="act" id="act" value="<?= $act; ?>">
    <input type="hidden" name="id" id="id_tagihan" value="<?= $id; ?>">
    <input type="hidden" name="id_billing_update" id="id_billing_update" value="<?= $id_billing; ?>">

    <div class="row">
        <div class="alert-submit"></div>
        <?php
            //echo form_builder($column);
        ?>

        <div class="col-12">
            <div class="form-group">
            <label>Jenis Tagihan</label>
            <select name="jenis_tagihan" class="form-control select2bs4" id="jenis_tagihan">
                <?php 
                foreach($jenis as $j) {
                    $sel = "";
                    if($row['jenis'] == $j->id) {
                        $sel = 'selected="selected"';
                    }
                    echo "<option value='$j->id' $sel>$j->name</option>";
                }
                ?>
            </select>
            </div>
        </div>

        
        <div class="col-12">
            <div class="form-group">
            <label>Jumlah</label>
            <input type='text' name='jumlah_tagihan' value="<?= $row['jumlah']; ?>" id="jumlah_tagihan" class='form-control' onkeyup='change_format_tagihan(this.value);'>
            </div>
        </div>
        

        <div class="col-12 mt-2 mb-2">
            <input type="button" value="<?= $submit_name; ?>" onclick="tagihan_submit();" id="btn-submit-tagihan" class="btn btn-success col-12">
        </div>
    </div>
    
</form>

<script>
$(function () {
        $('.select2bs4').select2({
            theme: 'bootstrap4',
            allowClear: true
        })
})

//$('#form-tagihan').submit(function(e) {
<?php
if(!empty($id_billing)) {
    ?>
function tagihan_submit() {       
    
    var jenis_tagihan = $("#jenis_tagihan").val();
    var jenis = $("#jenis").val();
    var jumlah = $("#jumlah_tagihan").val();
    var act = $("#act").val();
    var id_tagihan = $("#id_tagihan").val();
    var id_billing = $("#id_billing_update").val();

    console.log(id_tagihan+" "+jenis_tagihan+" "+jenis+" "+jumlah+" "+act+" "+id_billing);

    $("#btn-submit-tagihan").html('<i class="fa fa-spinner fa-spin"></i>Loading');
    $('#btn-submit-tagihan').attr('disabled',true);
    $.ajax({
    type:'POST',
    data:{jumlah:jumlah,jenis:jenis,jenis_tagihan:jenis_tagihan,act:act,id:id_tagihan,id_billing:id_billing},
    url:"<?= base_url('api/cms/'.$post.''); ?>",
    success:function(data) {
        console.log(data);
        
        var message = data.message;
        console.log(message);
        // console.log("haii");

        if(data.status == 1) {
            // location.reload();
            toastr.success('Berhasil di perbaharui');
            showcontent(id_billing);
                        
            $("#btn-submit-tagihan").html('<?= $submit_name; ?>');
            $('#btn-submit-tagihan').removeAttr('disabled');
        /*
            $('#modal-close').trigger('click');
        
          
            tagihan_list();

            */
        } else {
            toastr.error(message);
            $("#btn-submit-tagihan").html('<?= $submit_name; ?>');
            $('#btn-submit-tagihan').removeAttr('disabled');
            $(".alert-submit-tagihan").html('<div class="alert alert-danger alert-dismissible fade show text-center margin-bottom-1x"><span class="alert-close" data-dismiss="alert"></span><i class="icon-alert-triangle"></i>&nbsp;&nbsp;<span class="text-medium">'+message+'</div>');
        }
        

    }
    });
         
}
<?php } else { ?>


    function tagihan_submit() {       
    
    var jenis_tagihan = $("#jenis_tagihan").val();
    var jenis = $("#jenis").val();
    var jumlah = $("#jumlah_tagihan").val();
    var act = $("#act").val();
    var id_tagihan = $("#id_tagihan").val();


    $("#btn-submit-tagihan").html('<i class="fa fa-spinner fa-spin"></i>Loading');
    $('#btn-submit-tagihan').attr('disabled',true);
    $.ajax({
    type:'POST',
    data:{jumlah:jumlah,jenis:jenis,jenis_tagihan:jenis_tagihan,act:act,id:id_tagihan},
    url:"<?= base_url('api/cms/'.$post.''); ?>",
    success:function(data) {
      //  console.log(data);
        
        var message = data.message;
        console.log(message);
        // console.log("haii");

        if(data.status == 1) {
            // location.reload();
        
            $('#modal-close').trigger('click');
            toastr.success('Berhasil di perbaharui');

            tagihan_list();
            if(act == 'save') {
                reset_tagihan();
            }

            
            $("#btn-submit-tagihan").html('<?= $submit_name; ?>');
            $('#btn-submit-tagihan').removeAttr('disabled');
        } else {
            toastr.error(message);
            $("#btn-submit-tagihan").html('<?= $submit_name; ?>');
            $('#btn-submit-tagihan').removeAttr('disabled');
            $(".alert-submit-tagihan").html('<div class="alert alert-danger alert-dismissible fade show text-center margin-bottom-1x"><span class="alert-close" data-dismiss="alert"></span><i class="icon-alert-triangle"></i>&nbsp;&nbsp;<span class="text-medium">'+message+'</div>');
        }
        

    }
    });
         
}
<?php } ?>
$("input[data-bootstrap-switch]").each(function(){
    $(this).bootstrapSwitch('state', $(this).prop('checked'));
    });
</script>

<script src="<?= base_url("asset") ?>/plugins/tags/bootstrap-tagsinput.min.js"></script>