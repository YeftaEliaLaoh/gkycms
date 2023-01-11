<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">

  <title>GYK</title>

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="<?= base_url("asset"); ?>/plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?= base_url("asset"); ?>/dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <!-- Toastr -->
  <link rel="stylesheet" href="<?= base_url("asset"); ?>/plugins/toastr/toastr.min.css">
  <!-- daterange picker -->
  <link rel="stylesheet" href="<?= base_url("asset"); ?>/plugins/daterangepicker/daterangepicker.css">
  <!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="<?= base_url("asset"); ?>/plugins/icheck-bootstrap/icheck-bootstrap.min.css">

  <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.1.7/css/fixedHeader.dataTables.min.css">
  <!-- Bootstrap Color Picker -->
  <link rel="stylesheet" href="<?= base_url("asset"); ?>/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href="<?= base_url("asset"); ?>/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="<?= base_url("asset"); ?>/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="<?= base_url("asset"); ?>/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <!-- Bootstrap4 Duallistbox -->
  <link rel="stylesheet" href="<?= base_url("asset"); ?>/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">

  <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url("images/favicon_gsa.png"); ?>">

  
  <!-- REQUIRED SCRIPTS -->

  <!-- jQuery -->
  <script src="<?= base_url("asset"); ?>/plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="<?= base_url("asset"); ?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="<?= base_url("asset"); ?>/dist/js/adminlte.min.js"></script>
  <!-- Toastr -->
  <script src="<?= base_url("asset"); ?>/plugins/toastr/toastr.min.js"></script>

  <!-- Select2 -->
  <script src="<?= base_url("asset"); ?>/plugins/select2/js/select2.full.min.js"></script>
  <!-- Bootstrap4 Duallistbox -->
  <script src="<?= base_url("asset"); ?>/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
  <!-- InputMask -->
  <script src="<?= base_url("asset"); ?>/plugins/moment/moment.min.js"></script>
  <script src="<?= base_url("asset"); ?>/plugins/inputmask/min/jquery.inputmask.bundle.min.js"></script>
  <!-- date-range-picker -->
  <script src="<?= base_url("asset"); ?>/plugins/daterangepicker/daterangepicker.js"></script>
  <!-- bootstrap color picker -->
  <script src="<?= base_url("asset"); ?>/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
  <!-- Tempusdominus Bootstrap 4 -->
  <script src="<?= base_url("asset"); ?>/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>

  <!-- Thousand Separator !-->
  <script src="<?= base_url("asset"); ?>/easy-number-separator.js"></script>

  <script src="<?= base_url("asset"); ?>/plugins/datatables/jquery.dataTables.min.js"></script>
  <script src="<?= base_url("asset"); ?>/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
  <script src="<?= base_url("asset"); ?>/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
  <script src="<?= base_url("asset"); ?>/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
  <script src="https://cdn.datatables.net/fixedheader/3.1.7/js/dataTables.fixedHeader.min.js"></script>


</head>
<body class="hold-transition layout-top-nav">
<div class="wrapper">

  

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand-md navbar-light navbar-white navbar-sticky navbar-stuck">
    <div class="container">
      <a href="<?= base_url(); ?>" class="navbar-brand">
        <span class="brand-text font-weight-light">GYK</span>
      </a>
      
      <button class="navbar-toggler order-1" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <?php include 'menu-admin.php'; ?>

      <!-- Right navbar links -->
      <?php //include 'notif-header.php'; ?>

    </div>
    <span class="navbar-text text-primary">
        Halo,  <?php echo $this->session->username;?>
      </span>
  </nav>
  <!-- /.navbar -->

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- <img src="<?php echo base_url("../images/b6.jpg");?>" class="img-fluid"> -->
    <div class="bg-image" style="background-image: url('<?php echo ("images/b6.jpg");?>'); height: 83vh" >
    <!-- <div class="bg-gradient"> -->

        <?php echo $contents; ?>

    </div>
  </div>
  <!-- /.content-wrapper -->


  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    
    <!-- Default to the left -->
    <!--
    <strong>Copyright &copy; 2020 <a href="https://deratech.id">Deratech</a>.</strong> All rights reserved.
    !-->
  </footer>
</div>
<style>
div.sticky {
  position: -webkit-sticky;
  position: sticky;
  top: 0;
  z-index:5;
}

.modal { overflow: auto !important; }
</style>
 <!-- Default Order Modal-->
 <div class="modal fade" id="showmodalkegiatan" data-backdrop="static" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title"><span id="title-header3"></span></h4>
            <button class="close" type="button" id="modal-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-showorder container padding-top-1x padding-bottom-1x">
            <div id="loading-order" class="text-center">
                  
            </div>
          </div>
        </div>
      </div>
    </div>
  
<div id="dialog" title="Cari Data" style="display:none;">
  <p></p>
</div>


<div id="dialog_tambah" title="Data" style="display:none;">
  <p></p>
</div>
  
  <div class="modal fade" id="showmodalkegiatan2" data-backdrop="static" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title"><span id="title-header4"></span></h4>
            <button class="close" type="button" id="modal-close4" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-showorder4 container padding-top-1x padding-bottom-1x">
            <div id="loading-order4" class="text-center">
                  
            </div>
          </div>
        </div>
      </div>
    </div>

  <!-- Default MAK Modal-->
  <div class="modal fade" id="showmodalmak" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title"><span id="title-header4"></span></h4>
            <button class="close" type="button" id="modal-close2" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-showmak container padding-top-1x padding-bottom-1x">
            <div id="loading-mak" class="text-center">
                  
            </div>
          </div>
        </div>
      </div>
    </div>   
<!-- ./wrapper -->


  <!-- Default MAK Modal-->
  <!--
  <div class="modal fade" id="showmodalkegiatan2" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title"><span id="title-header4"></span></h4>
            <button class="close" type="button" id="modal-close4" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-showorder4 container padding-top-1x padding-bottom-1x">
            <div id="loading-order4" class="text-center">
                  
            </div>
          </div>
        </div>
      </div>
    </div>   
  !-->
<!-- ./wrapper -->

<script>
  
$(document).on("click", "#openkegiatan", function() {
 

 var dataURL3 = $(this).attr('data-href');
 var header = $(this).attr('data-header');
 //console.log(dataURL3);
 $('#title-header3').html(header);

 $("#loading-order").show(); // Tampilkan loadingnya
 $.ajax({
   type: "POST", // Method pengiriman data bisa dengan GET atau POST
   url: dataURL3, // Isi dengan url/path file php yang dituju
   data: {}, // data yang akan dikirim ke file yang dituju
   success: function(response){ // Ketika proses pengiriman berhasil
     setTimeout(function(){
       $("#loading-order").hide(); // Sembunyikan loadingnya
       var modal3 = $('.modal-showorder');
       // set isi dari combobox kota
       // lalu munculkan kembali combobox kotanya
       $(modal3).html(response).show();
     }, 100);
   },
   error: function (xhr, ajaxOptions, thrownError) { // Ketika ada error
     alert(thrownError); // Munculkan alert error
   }
 });

});


$(document).on("click", "#openkegiatan2", function() {
 

 var dataURL3 = $(this).attr('data-href');
 var header = $(this).attr('data-header');
 //console.log(dataURL3);
 $('#title-header4').html(header);

 $("#loading-order4").show(); // Tampilkan loadingnya
 $.ajax({
   type: "POST", // Method pengiriman data bisa dengan GET atau POST
   url: dataURL3, // Isi dengan url/path file php yang dituju
   data: {}, // data yang akan dikirim ke file yang dituju
   success: function(response){ // Ketika proses pengiriman berhasil
     setTimeout(function(){
       $("#loading-order4").hide(); // Sembunyikan loadingnya
       var modal3 = $('.modal-showorder4');
       // set isi dari combobox kota
       // lalu munculkan kembali combobox kotanya
       $(modal3).html(response).show();
     }, 100);
   },
   error: function (xhr, ajaxOptions, thrownError) { // Ketika ada error
     alert(thrownError); // Munculkan alert error
   }
 });

});

$(document).on("click", "#openmak", function() {
 

 var dataURL3 = $(this).attr('data-href');
 var header = $(this).attr('data-header');
 //console.log(dataURL3);
 $('#title-header4').html(header);

 $("#loading-mak").show(); // Tampilkan loadingnya
 $.ajax({
   type: "POST", // Method pengiriman data bisa dengan GET atau POST
   url: dataURL3, // Isi dengan url/path file php yang dituju
   data: {}, // data yang akan dikirim ke file yang dituju
   success: function(response){ // Ketika proses pengiriman berhasil
     setTimeout(function(){
       $("#loading-mak").hide(); // Sembunyikan loadingnya
       var modal3 = $('.modal-showmak');
       // set isi dari combobox kota
       // lalu munculkan kembali combobox kotanya
       $(modal3).html(response).show();
     }, 100);
   },
   error: function (xhr, ajaxOptions, thrownError) { // Ketika ada error
     alert(thrownError); // Munculkan alert error
   }
 });

});


$(document).on("click", "#deletekegiatan", function() {
  var r = confirm("Are you sure ?");
  var dataURL3 = $(this).attr('data-href');
  var id = $(this).attr('data-id');
  var table = $('#table').DataTable();

  var dataString = 'id='+id;
  if (r == true) {
    //txt = "You pressed OK!";
    $.ajax({
      type: "POST", // Method pengiriman data bisa dengan GET atau POST
      url: dataURL3, // Isi dengan url/path file php yang dituju
      data:dataString,// data yang akan dikirim ke file yang dituju
      success: function(response){ // Ketika proses pengiriman berhasil
        var message = response.message;
        if(response.status == 1) {
            // location.reload();
            table.ajax.reload();
            toastr.success('Berhasil dihapus');
        } else {
            toastr.error(message);
        }
      },
      error: function (xhr, ajaxOptions, thrownError) { // Ketika ada error
        alert(thrownError); // Munculkan alert error
      }
    });
  }
});

$(document).on("click", "#deletekegiatanreload", function() {
  var r = confirm("Are you sure ?");
  var dataURL3 = $(this).attr('data-href');
  var id = $(this).attr('data-id');
  var table = $('#table').DataTable();

  var dataString = 'id='+id;
  if (r == true) {
    //txt = "You pressed OK!";
    $.ajax({
      type: "POST", // Method pengiriman data bisa dengan GET atau POST
      url: dataURL3, // Isi dengan url/path file php yang dituju
      data:dataString,// data yang akan dikirim ke file yang dituju
      success: function(response){ // Ketika proses pengiriman berhasil
        var message = response.message;
        if(response.status == 1) {
            toastr.success('Berhasil dihapus');
            location.reload();            
        } else {
            toastr.error(message);
        }
      },
      error: function (xhr, ajaxOptions, thrownError) { // Ketika ada error
        alert(thrownError); // Munculkan alert error
      }
    });
  }
});


function convert_thousand(number_get) {
		var number = number_get.replace(/,/g,'');

		var check_number = number.toString().includes(".");

		if(check_number == true) {
			split_number = number.toString().split('.');			
			
			if(split_number[0].length > 3) {
				var	number_string = split_number[0],
					sisa 	= number_string.length % 3,
					rupiah 	= number_string.substr(0, sisa),
					ribuan 	= number_string.substr(sisa).match(/\d{3}/g);
						
				if (ribuan) {
					separator = sisa ? ',' : '';
					rupiah += separator + ribuan.join(',');
					rupiah += '.' + split_number[1];
				}
			} else {
				rupiah = number_get;
			}
		} else {
			var	number_string = number.toString(),
				sisa 	= number_string.length % 3,
				rupiah 	= number_string.substr(0, sisa),
				ribuan 	= number_string.substr(sisa).match(/\d{3}/g);
					
			if (ribuan) {
				separator = sisa ? ',' : '';
				rupiah += separator + ribuan.join(',');
			}
		}

		return rupiah;
	}


</script>
<script src="<?= base_url(); ?>asset/highcharts.js"></script>

<SCRIPT language="javascript">
	function addRow(tableID) {

		var table = document.getElementById(tableID);

		var rowCount = table.rows.length;
		var row = table.insertRow(rowCount);

		var colCount = table.rows[0].cells.length;
		
		if(tableID == 'dataTable4') {
			for(var i=0; i<colCount; i++) {

				var newcell = row.insertCell(i);

				//newcell.innerHTML = table.rows[1].cells[i].innerHTML;
				//alert(newcell.childNodes);
				if(i == 2) {
					newcell.innerHTML = table.rows[1].cells[i].innerHTML.replace("warna[1][]","warna["+rowCount+"][]");
					//newcell.className = "chosen span3";
					//newcell.childNodes[0].name = "warna[" + rowCount+"][]";
					//alert(newcell.childNodes[0].name);
				} 
				else if(i == 3) {
					newcell.innerHTML = table.rows[1].cells[i].innerHTML.replace("ukuran[1][]","ukuran["+rowCount+"][]");
					//newcell.innerHTML = table.rows[1].cells[i].innerHTML;
					//newcell.childNodes[0].name = "ukuran[" + rowCount+"][]";
				} 
				else {
					newcell.innerHTML = table.rows[1].cells[i].innerHTML;
					switch(newcell.childNodes[0].type) {
						case "text":
								newcell.childNodes[0].value = "";
								
								break;
						case "checkbox":
								newcell.childNodes[0].checked = false;
								break;
						case "select-one":
								newcell.childNodes[0].selectedIndex = 0;
								
								break;
					}
				}
			}
		} else {
			for(var i=0; i<colCount; i++) {

				var newcell = row.insertCell(i);

				newcell.innerHTML = table.rows[1].cells[i].innerHTML;
				//alert(newcell.childNodes);
				switch(newcell.childNodes[0].type) {
					case "text":
							newcell.childNodes[0].value = "";
							break;
					case "checkbox":
							newcell.childNodes[0].checked = false;
							break;
					case "select-one":
							newcell.childNodes[0].selectedIndex = 0;
							
							break;
				}
			}
		}
	}

	function deleteRow(tableID,tbl) {
		try {
		var table = document.getElementById(tableID);
		var rowCount = table.rows.length;

		for(var i=0; i<rowCount; i++) {
			var row = table.rows[i];
			var chkbox = row.cells[0].childNodes[0];
			if(null != chkbox && true == chkbox.checked) {
        cval = chkbox.value;
       // alert(cval);
        
        if(cval != null) {
          idd = tbl+"/"+chkbox.value;
          //alert(idd);
          $.ajax({
            type:'POST',
            data: {id:idd},
            url:"<?= base_url('api/delete'); ?>",
            success:function(data) {
              console.log("terhapus");
                
            }
          });
        }
        
        
				//var dataString = 'id='+idd;

				if(rowCount <= 1) {
					alert("Cannot delete all the rows.");
					break;
				}
				table.deleteRow(i);
				rowCount--;
				i--;
			}


		}
		}catch(e) {
			alert(e);
		}
	}
	

</SCRIPT>
</body>
</html>
