
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
        
        <div id="accordion" class="col-sm-12">

          <div class="card card-success">
            <div class="card-header" data-toggle="collapse" style="cursor:pointer;" data-parent="#accordion" href="#collapseThree">
              <h4 class="card-title">
                <a class="" aria-expanded="true">
                  Detail Job Order <span id="span-joborder"></span>
                </a>
              </h4>

              <!-- /.card-tools -->
            </div>
            <div id="collapseThree" class="panel-collapse collapse show" style="">
              <div class="card-body" id="joborderdetail">
                
              </div>
            </div>
          </div>

          
        </div>

        <div class="col-sm-3" id="fp_div" style="display:none;">
            <div class="form-group">
            <label>No Faktur</label>
            <input type="text" class="form-control">
            </div>
        </div>

        <div class="col-sm-12">
            <div class="form-group">
            <label>Detail Tagihan</label>
              <div class="col-sm-6 mb-3">
                <button type="button" class="btn btn-sm btn-primary" onclick="tambah_tagihan(1);">Tambah</button>&nbsp;
                <button type="button" class="btn btn-sm btn-primary" onclick="generate_tagihan(1);" style="display:none;" id="generate">Generate</button>&nbsp;
                <button type="button" class="btn btn-sm btn-primary" onclick="generate_materai(1);">Materai</button>

              </div>
              <table class="table table-bordered table-hover " id="tbl1">
                  <thead>
                    <tr>
                      <th>Kd</th>
                      <th>Jenis Tagihan</th>
                      <th>Jumlah</th>
                      <th>Action</th>
                    </tr>
                    
                  </thead>
                  <tbody></tbody>
                  <tfoot>
                    <tr><td colspan="2">Total : </td><td><input type='text' readonly id="total_price" value="<?php echo harga_view($row->total_tagihan); ?>" name="total_tagihan"></td><td></td></tr>
                  </tfoot>
              </table>

            </div>
        </div>


        <div class="col-12 mb-2">
          <input type="submit" value="<?= $submit_name; ?>" id="btn-submit" class="btn btn-success float-right">
          <a href="#" data-dismiss="modal" class="btn btn-secondary">Cancel</a>
        </div>
    </div>
    
</form>

<script>

$(document).ready(function() {
  
  selectRefresh();
  <?php
  /*
  if(!empty($id)) {
    echo 'setproduct("'.$row->id_joborder.'");';
    if($row->jenis == 'ump' || $row->jenis == 'inklaring') {
      echo '$("#generate").show();';
    }

    if($row->jenis == 'inklaring') {
      echo '$("#fp_div").show();';
    }
    echo 'loaddetailtagihan("'.$row->no.'")';
  }
  */
  ?>
});

function loaddetailtagihan(id) {
  var val = 1;
  $.ajax({
      type:'POST',
      data:{id : id},
      url:"<?= base_url('billing/showdetailtagihan'); ?>",
      success:function(response) {
        
          $('#tbl'+val+' tbody').append(response);
          $('#tbl'+val+' tbody tr').each(function(){
              $(this).find('td:nth-child(2) input').focus();
          });

          selectRefresh();


        
      }
    });
}

function change_format(val,id) {
  $("#jumlah_"+id).val(convert_thousand(val));
  
  sum_input();
}

function selectRefresh() {
  $('.select2bs').select2({
    theme: 'bootstrap4'
  });
}

function generate_tagihan(val) {
  var id = $("#id_joborder").val();
  var total_price = $("#total_price").val();
  console.log(total_price);

  if(id) {
    var Nomor = $('#tbl'+val+' tbody tr').length + 1;
    $.ajax({
        type:'POST',
        data:{no : Nomor,id:id,total:total_price},
        url:"<?= base_url('billing/generatetagihan'); ?>",
        success:function(response) {
          //console.log(response);
            $('#tbl'+val+' tbody').append(response);
            $('#tbl'+val+' tbody tr').each(function(){
                $(this).find('td:nth-child(2) input').focus();
            });

            selectRefresh();


          
        }
      });
      setTimeout(function(){
        sum_input();
      }, 1000);
  } else {
    var message = "Job Order belum dipilih";
    $(".alert-submit").html('<div class="alert alert-danger alert-dismissible fade show text-center margin-bottom-1x"><span class="alert-close" data-dismiss="alert"></span><i class="icon-alert-triangle"></i>&nbsp;&nbsp;<span class="text-medium">'+message+'</div>');
  }

}

function tambah_tagihan(val) {
    
    var Nomor = $('#tbl'+val+' tbody tr').length + 1;
    $.ajax({
      type:'POST',
      data:{no : Nomor},
      url:"<?= base_url('billing/tambahtagihan'); ?>",
      success:function(response) {
        
          $('#tbl'+val+' tbody').append(response);
          $('#tbl'+val+' tbody tr').each(function(){
              $(this).find('td:nth-child(2) input').focus();
          });

          selectRefresh();


        
      }
    });
    
    sum_input();

    
}

function sum_input() {
   var sum = 0;
  $('.jumlah').each(function(){
    var b = this.value.split(",").join("");
      sum += parseFloat(b);
  });
  var sumb = sum.toString();

  var a = convert_thousand(sumb);
  $("#total_price").val(a);
}

function sum_input_update() {
   var sum = 0;
  $('.jumlah').each(function(){
    var b = this.value.split(",").join("");
      sum += parseFloat(b);
  });
  var sumb = sum.toString();

  var a = convert_thousand(sumb);
  $("#total_price").val(a);
  <?php if(!empty($id)) { ?>
  $.ajax({
      type:'POST',
      data:{id : <?= $id; ?>,total:sumb},
      url:"<?= base_url('api/cms/updatetagihan'); ?>",
      success:function(response) {
        console.log(response);
      }
  });
<?php } ?>
}

function generate_materai(val) {
    
    var Nomor = $('#tbl'+val+' tbody tr').length + 1;
    $.ajax({
      type:'POST',
      data:{no : Nomor},
      url:"<?= base_url('billing/generatematerai'); ?>",
      success:function(response) {
        
          $('#tbl'+val+' tbody').append(response);
          $('#tbl'+val+' tbody tr').each(function(){
              $(this).find('td:nth-child(2) input').focus();
          });

          selectRefresh();
          sum_input();

        
      }
    });

    
}

$(document).on('click', '#HapusBaris', function(e){
    e.preventDefault();
    $(this).parent().parent().remove();
    sum_input();
});

$(document).on('click', '#HapusBarisupdate', function(e){
    e.preventDefault();
    $(this).parent().parent().remove();
    //sum_input();
});

function hapusdetailtagihan(id) {
  //console.log(no+" "+id);
  $.ajax({
      type:'POST',
      data:{id : id},
      url:"<?= base_url('api/cms/hapusdetailtagihan'); ?>",
      success:function(response) {
        sum_input_update();
      }
    });
}

function getkode(val,no) {
  $("#kode_"+no).val(val);
  
  var a = $( "#jenis_tagihan_"+no+" option:selected" ).text();
  $("#jenis_tagihan2_"+no).val(a);
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
                   $('#modal-close').trigger('click');
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

$("#jenis").change(function(){
    //$("#no").val("haha");
    var jenis = $("#jenis").val();

    
    $.ajax({
      type:'POST',
      data:{jenis:jenis},
      url:"<?= base_url('api/cms/get_billing_no'); ?>",
      success:function(data) {
          var message = data.message;
          // console.log("haii");
         // console.log(data);
          if(data.status == 1) {
          
              $("#no").val(data.billing_no);
              $("#sequence_order").val(data.sequence_order);
              if(data.generate == 1) {
                $("#generate").show();
              } else {
                $("#generate").hide();
              }

              if(data.fp == 1) {
                $("#fp_div").show();
              } else {
                $("#fp_div").hide();
              }
              
              //$("#awbl_div").html(data.awbl);
          } else if(data.status == 0) {
            
          } else {
              toastr.error(message);        
          }

          
          
      }
    });
});

function setproduct(id) {
   //console.log(id); 
//   console.log(itemname);
   //$("#accordion").hide();
   $.ajax({
    type: "POST", // Method pengiriman data bisa dengan GET atau POST
    url: "<?= base_url("joborder/showjoborder"); ?>", // Isi dengan url/path file php yang dituju
    data: {id : id}, // data yang akan dikirim ke file yang dituju
    dataType: "json",
    beforeSend: function(e) {
        if(e && e.overrideMimeType) {
        e.overrideMimeType("application/json;charset=UTF-8");
        }
    },
    success: function(response){ // Ketika proses pengiriman berhasil
    /*
        setTimeout(function(){
        console.log(color)
        // lalu munculkan kembali combobox kotanya
        $("#color_id").html(response.data_color).show();
        $("#color_array").val(response.color_id);
        console.log(response.color_id);
        }, 100);
        */
        
       // $("#accordion").show();
        $("#span-joborder").html(response.no_jo);
        $("#no_joborder").val(response.no_jo);
        $("#id_joborder").val(response.id_joborder);
        $("#joborderdetail").html(response.html).show();
        
        console.log(response);
    },
    error: function (xhr, ajaxOptions, thrownError) { // Ketika ada error
        alert(thrownError); // Munculkan alert error
    }
    });
//   $("#product_ax_amount").val(amount);
   $( "#modal-close2" ).trigger( "click");
}

$("input[data-bootstrap-switch]").each(function(){
      $(this).bootstrapSwitch('state', $(this).prop('checked'));
    });


</script>

<script src="<?= base_url("asset") ?>/plugins/tags/bootstrap-tagsinput.min.js"></script>