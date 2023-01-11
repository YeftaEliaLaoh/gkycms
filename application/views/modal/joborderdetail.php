
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
        <div class="col-sm-6" id="alamatkirim">

          <label style="text-decoration:underline;">Alamat Pengiriman</label><br>
          <div class="row">

              <div class="col-sm-3 mb-3">
                <label>Nama PT</label>
              </div>
              <div class="col-sm-9">
                <input type="text" name="ap_nama_pt" id="ap_nama_pt" value="<?= $row->ap_nama_pt; ?>" class="form-control">
              </div>

              <div class="col-sm-3 mb-3">
                <label>Contact Person</label>
              </div>
              <div class="col-sm-9">
                <input type="text" name="ap_contact_person" id="ap_contact_person" value="<?= $row->ap_contact_person; ?>" class="form-control">
              </div>

              <div class="col-sm-3 mb-3">
                <label>Alamat</label>
              </div>
              <div class="col-sm-9">
                <textarea class="form-control" name="ap_alamat" id="ap_alamat"><?= $row->ap_alamat; ?></textarea>
              </div>

          </div>
        </div>

        <div class="col-sm-6" id="petikemas" style="<?= $petikemas_display; ?>">

          <label style="text-decoration:underline;">Identitas Peti Kemas</label><br>
          <div class="row">

            <div class="col-sm-6" >
                <div class="form-group">
                
                <table class="table table-bordered table-hover col-sm-3" id="tbl1">
                  <thead><tr><td colspan="2">20 f</td></thead>
                  <tbody>
                    <?php 
                    if(empty($row->identitas_peti20f)) {
                      echo "<tr><td><input type='text' name='20f[]'></td><td></td></tr>";
                    } else {
                      $identitas_20f = explode(",",$row->identitas_peti20f);
                      foreach($identitas_20f as $f2) {
                        echo "<tr><td><input type='text' name='20f[]' value='$f2'></td><td></td></tr>";
                      }
                    }
                    ?>
                      
                  </tbody>
                  
                </table>
                <table>
                  <tr><td><a href="#" class="btn btn-primary" onclick="barisbaru(1);">+ Tambah 20 f</a></td></tr>
                </table>

                </div>
            </div>

            <div class="col-sm-6" >
                <div class="form-group">
                
                <table class="table table-bordered table-hover col-sm-3" id="tbl2">
                  <thead><tr><td>40 f</td></thead>
                  <tbody>
                  <?php 
                    if(empty($row->identitas_peti40f)) {
                      echo "<tr><td><input type='text' name='40f[]'></td><td></td></tr>";
                    } else {
                      $identitas_40f = explode(",",$row->identitas_peti40f);
                      foreach($identitas_40f as $f4) {
                        echo "<tr><td><input type='text' name='40f[]' value='$f4'></td><td></td></tr>";
                      }
                    }
                    ?>
                  </tbody>
                </table>
                <table>
                  <tr><td><a href="#" class="btn btn-primary" onclick="barisbaru(2);">+ Tambah 40 f</a></td></tr>
                </table>

                </div>
            </div>

          </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
            <label style="text-decoration:underline;">Kemasan</label>
            <div class="row">
                <div class="col-sm-3 mb-3">
                  <label>Jenis</label>
                </div>
                <div class="col-sm-9 row">
                  <input type="number" value="<?= $row->berat_satuan; ?>" name="berat_satuan" class="form-control col-sm-6">&nbsp;
                  <select name="jenis_peti" class="form-control col-sm-5">
                    <?php
                    echo $opt_petikemas;
                    ?>
                  </select>
                </div>

                <div class="col-sm-3 mb-3">
                  <label>Gross Weight</label>
                </div>
                <div class="col-sm-9 row">
                  <input type="text" name="gross_wg" value="<?= $row->gross_wg; ?>" class="form-control col-sm-6">&nbsp;Kg
                </div>

                <div class="col-sm-3">
                  <label>Chargeable</label>
                </div>
                <div class="col-sm-9 row">
                  <input type="text" name="chargable" value="<?= $row->chargable; ?>" class="form-control col-sm-6">&nbsp;Kg
                </div>

            </div>
           
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
            <label style="text-decoration:underline;">Biaya</label>
            <div class="row">
                <div class="col-sm-3 mb-3">
                  <label>CIF</label>
                </div>
                <div class="col-sm-9">
                  <input type="text" name="cif" id="cif" onkeyup="update_jumlah_biaya();" value="<?= harga_view($row->cif); ?>" class="form-control">
                </div>

                <div class="col-sm-3 mb-3">
                  <label>Kurs</label>
                </div>
                <div class="col-sm-9">
                  <input type="text" name="kurs" id="kurs" onkeyup="update_jumlah_biaya();" value="<?= harga_view($row->kurs); ?>" class="form-control">
                </div>

                <div class="col-sm-3 mb-3">
                  <label>Jumlah</label>
                </div>
                <div class="col-sm-9">
                  <input type="text" name="jumlah_biaya" id="jumlah_biaya" value="<?= harga_view($row->jumlah_biaya); ?>" readonly class="form-control">
                </div>

            </div>
           
            </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
            <label style="text-decoration:underline;">Pungutan</label>
            <div class="row">
                <div class="col-sm-3 mb-3">
                  <label>BM</label>
                </div>
                <div class="col-sm-9">
                  <input type="text" name="bm" id="bm" value="<?= harga_view($row->bm); ?>" onkeyup="update_jumlah_pungutan();" class="form-control">
                </div>

                <div class="col-sm-3 mb-3">
                  <label>Cukai</label>
                </div>
                <div class="col-sm-9">
                  <input type="text" name="cukai" id="cukai" value="<?= harga_view($row->cukai); ?>" onkeyup="update_jumlah_pungutan();" class="form-control">
                </div>

                <div class="col-sm-3 mb-3">
                  <label>PPN</label>
                </div>
                <div class="col-sm-9">
                  <input type="text" name="ppn" id="ppn" value="<?= harga_view($row->ppn); ?>" onkeyup="update_jumlah_pungutan();" class="form-control">
                </div>

                <div class="col-sm-3 mb-3">
                  <label>PPN Bm</label>
                </div>
                <div class="col-sm-9">
                  <input type="text" name="ppn_bm" id="ppn_bm" value="<?= harga_view($row->ppn_bm); ?>" onkeyup="update_jumlah_pungutan();" class="form-control">
                </div>

                <div class="col-sm-3 mb-3">
                  <label>Pph</label>
                </div>
                <div class="col-sm-9">
                  <input type="text" name="pph" id="pph" value="<?= harga_view($row->pph); ?>" onkeyup="update_jumlah_pungutan();" class="form-control">
                </div>

                <div class="col-sm-3 mb-3">
                  <label>Jumlah</label>
                </div>
                <div class="col-sm-9">
                  <input type="text" name="jumlah_pungutan" value="<?= harga_view($row->jumlah_pungutan); ?>" id="jumlah_pungutan" readonly class="form-control">
                </div>

            </div>
           
            </div>
        </div>
        
    </div>
    
</form>


<script>

  
	function update_jumlah_pungutan() {
    /*
		var bm_get = document.getElementById('bm').value;

		var rupiah = convert_thousand(bm_get);

		$("#bm").val(rupiah);

		var cukai_get = document.getElementById('cukai').value;

		var rupiah = convert_thousand(cukai_get);

		$("#cukai").val(rupiah);

		var ppn_get = document.getElementById('ppn').value;

		var rupiah = convert_thousand(ppn_get);
		
		$("#ppn").val(rupiah);
		
		var ppn_bm_get = document.getElementById('ppn_bm').value;

		var rupiah = convert_thousand(ppn_bm_get);

		$("#ppn_bm").val(rupiah);
		
		var pph_get = document.getElementById('pph').value;

		var rupiah = convert_thousand(pph_get);

	*/
  var bm = $("#bm").val();
  var cukai = $("#cukai").val();
  var ppn = $("#ppn").val();
  var ppn_bm = $("#ppn_bm").val();
  var pph = $("#pph").val();

  $("#bm").val(convert_thousand(bm));
  $("#cukai").val(convert_thousand(cukai));
  $("#ppn").val(convert_thousand(ppn));
  $("#ppn_bm").val(convert_thousand(ppn_bm));
  $("#pph").val(convert_thousand(pph));

  
  if(bm) {
    bm = parseFloat(bm.replace(/,/g,''));
  } else {
    bm = 0;
  }

  if(cukai) {
    cukai = parseFloat(cukai.replace(/,/g,''));
  } else {
    cukai = 0;
  }
  if(ppn) {
    ppn = parseFloat(ppn.replace(/,/g,''));
  } else {
    ppn = 0;
  }
  if(ppn_bm) {
    ppn_bm = parseFloat(ppn_bm.replace(/,/g,''));
  } else {
    ppn_bm = 0;
  }
  if(pph) {
    pph = parseFloat(pph.replace(/,/g,''));
  } else {
    pph = 0;
  }

    var total = bm+cukai+ppn+ppn_bm+pph;
    
		var totalToString = total.toString();

		var check_total = totalToString.includes(".");

		if(check_total == true) {
			split_total = totalToString.split('.');			
			
			var	number_string = split_total[0],
				sisa 	= number_string.length % 3,
				rupiah 	= number_string.substr(0, sisa),
				ribuan 	= number_string.substr(sisa).match(/\d{3}/g);
					
			if (ribuan) {
				separator = sisa ? ',' : '';
				rupiah += separator + ribuan.join(',');
				rupiah += '.' + split_total[1];
			}
		} else {
			var	number_string = totalToString,
				sisa 	= number_string.length % 3,
				rupiah 	= number_string.substr(0, sisa),
				ribuan 	= number_string.substr(sisa).match(/\d{3}/g);
					
			if (ribuan) {
				separator = sisa ? ',' : '';
				rupiah += separator + ribuan.join(',');
			}
		}
    
    $("#jumlah_pungutan").val(rupiah);
	}

  function update_jumlah_biaya() {
    var cif = $("#cif").val();
    var kurs = $("#kurs").val();

    var ccif = convert_thousand(cif);
    var ckurs = convert_thousand(kurs);

    $("#cif").val(ccif);
    $("#kurs").val(ckurs);

    var total = parseFloat(cif.replace(/,/g,'')) * parseFloat(kurs.replace(/,/g,''));

		var totalToString = total.toString();

		var check_total = totalToString.includes(".");

		if(check_total == true) {
			split_total = totalToString.split('.');			
			
			var	number_string = split_total[0],
				sisa 	= number_string.length % 3,
				rupiah 	= number_string.substr(0, sisa),
				ribuan 	= number_string.substr(sisa).match(/\d{3}/g);
					
			if (ribuan) {
				separator = sisa ? ',' : '';
				rupiah += separator + ribuan.join(',');
				rupiah += '.' + split_total[1];
			}
		} else {
			var	number_string = totalToString,
				sisa 	= number_string.length % 3,
				rupiah 	= number_string.substr(0, sisa),
				ribuan 	= number_string.substr(sisa).match(/\d{3}/g);
					
			if (ribuan) {
				separator = sisa ? ',' : '';
				rupiah += separator + ribuan.join(',');
			}
		}

    $("#jumlah_biaya").val(rupiah);
		//document.getElementById('jumlah_biaya').value = rupiah;

    //console.log(rupiah);
  }

  

  function barisbaru(val) {
    
    var Baris = "<tr>";
        Baris += "<td><input type='text' name='20f[]'></td>";
        Baris += "<td><button class='btn btn-default' id='HapusBaris'><i class='fa fa-times' style='color:red;'></i></button></td>";
        Baris += "</tr>";

    $('#tbl'+val+' tbody').append(Baris);

    $('#tbl'+val+' tbody tr').each(function(){
        $(this).find('td:nth-child(2) input').focus();
    });

  }

  
  $(document).on('click', '#HapusBaris', function(e){
    e.preventDefault();
    $(this).parent().parent().remove();

  });

  $(function () {
        $('.select2bs4').select2({
            theme: 'bootstrap4',
          //  tags: true
        });
        
        $('.select2bs42').select2({
            theme: 'bootstrap4',
            tags: true
        })

         //Colorpicker
        $('#my-colorpicker1').colorpicker()
  })

/*
$("#pabean").change(function(){
    //$("#no").val("haha");
    var pabean = $("#pabean").val();
    var angkutan = $("#angkutan").val();
    $.ajax({
      type:'POST',
      data:{pabean:pabean, angkutan:angkutan},
      url:"<?= base_url('api/cms/get_jo_no'); ?>",
      success:function(data) {
          var message = data.message;
          // console.log("haii");
          if(data.status == 1) {
              $("#no").val(data.jo);
              $("#sequence_order").val(data.sequence_order);
              $("#wilayah").html(data.wilayah).show();
          } else if(data.status == 0) {
            
          } else {
              toastr.error(message);        
          }
          
          
      }
    });
});
*/
$("#angkutan,#pabean").change(function(){
    //$("#no").val("haha");
    var pabean = $("#pabean").val();
    var angkutan = $("#angkutan").val();

    if(angkutan == 'udara') {
      $("#awb_div").show();
      $("#bl_div").hide();
      $("#petikemas").hide();
    } else if(angkutan == 'laut') {
      $("#bl_div").show();
      $("#awb_div").hide();
      $("#petikemas").show();
    } else {
      
    }
    
    $.ajax({
      type:'POST',
      data:{pabean:pabean, angkutan:angkutan},
      url:"<?= base_url('api/cms/get_jo_no'); ?>",
      success:function(data) {
          var message = data.message;
          // console.log("haii");
          console.log(data);
          if(data.status == 1) {
              $("#no").val(data.jo);
              $("#sequence_order").val(data.sequence_order);
              $("#idwilayah").html(data.wilayah).show();
              //$("#awbl_div").html(data.awbl);
          } else if(data.status == 0) {
            
          } else {
              toastr.error(message);        
          }

          
          
      }
    });
});
  
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

$("input[data-bootstrap-switch]").each(function(){
      $(this).bootstrapSwitch('state', $(this).prop('checked'));
    });
</script>

<script src="<?= base_url("asset") ?>/plugins/tags/bootstrap-tagsinput.min.js"></script>