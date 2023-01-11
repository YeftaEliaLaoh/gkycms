<form role="form" method="post" id="form">
    <input type="hidden" name="tbl" value="<?= $tbl; ?>">
    <input type="hidden" name="act" value="<?= $act; ?>">
    <input type="hidden" name="id" value="<?= $id; ?>">

    <div class="row">
        <div class="alert-submit"></div>
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
  //$("#provinsi").change(function(){ // Ketika user mengganti atau memilih data provinsi
 
$(function () {
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        })
})

function ubahJumlah() {
    var vol = $("input[name=qty]").val();
    var harga = $("input[name=harga]").val();

    var dataString = 'vol='+vol+'&harga='+harga;

    $.ajax({
        type:'POST',
        data:dataString,
        url:'<?= base_url("api/ubah_jumlah"); ?>',
        success:function(data) {
          var message = data.message;
          if(data.status == 1) {
           // location.reload();
           console.log(data.jumlah);
           console.log(data.jumlah_f);
            $("#total").val(data.jumlah_f);
            //$("#total_f").val(data.jumlah_f);
          } else {
            alert("Server Error");
            //$(".alert-output").html('<div class="alert alert-danger"><span class="alert-close" data-dismiss="alert"></span><i class="icon-alert-triangle"></i>&nbsp;&nbsp;<span class="text-medium">'+message+'</div>');
           // $("#daftar-submit").html('Daftar');
            //$('#daftar-submit').removeAttr('disabled');
          }
         
        }
    });
}


$("#product").change(function(){
    $("#showpegawai").hide(); // Sembunyikan dulu combobox kota nya	
		$.ajax({
			type: "POST", // Method pengiriman data bisa dengan GET atau POST
			url: "<?= base_url("sppd/showpegawai"); ?>", // Isi dengan url/path file php yang dituju
			data: {id : $("#type_pegawai").val()}, // data yang akan dikirim ke file yang dituju
			dataType: "json",
			beforeSend: function(e) {
				if(e && e.overrideMimeType) {
					e.overrideMimeType("application/json;charset=UTF-8");
				}
			},
			success: function(response){ // Ketika proses pengiriman berhasil
				setTimeout(function(){
                    console.log(response);
					$("#show_pegawai").html(response.data).show();
				}, 100);
			},
			error: function (xhr, ajaxOptions, thrownError) { // Ketika ada error
				alert(thrownError); // Munculkan alert error
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
                console.log(data);
                if(data.status == 1) {
                    location.reload();
                  // table.ajax.reload();
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
</script>