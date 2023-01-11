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

  <title>GKY Dashboard</title>

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="<?= base_url("asset"); ?>/plugins/fontawesome-free/css/all.min.css">
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
  <!-- Bootstrap Color Picker -->
  <link rel="stylesheet" href="<?= base_url("asset"); ?>/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css">
  <!-- Tempusdominus Bbootstrap 4 -->
  <link rel="stylesheet" href="<?= base_url("asset"); ?>/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
  <!-- Select2 -->
  <link rel="stylesheet" href="<?= base_url("asset"); ?>/plugins/select2/css/select2.min.css">
  <link rel="stylesheet" href="<?= base_url("asset"); ?>/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
  <!-- Bootstrap4 Duallistbox -->
  <link rel="stylesheet" href="<?= base_url("asset"); ?>/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css">
  <!-- summernote -->
  <link rel="stylesheet" href="<?= base_url("asset"); ?>/plugins/summernote/summernote-bs4.css">

 
  
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
  <!-- /.content -->
  <script src="<?= base_url("asset/plugins/summernote/summernote-bs4.min.js"); ?>"></script>
  
  <!-- Bootstrap Switch -->
<script src="<?= base_url("asset"); ?>/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
  



  <script src="<?= base_url("asset"); ?>/plugins/datatables/jquery.dataTables.min.js"></script>
  <script src="<?= base_url("asset"); ?>/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
  <script src="<?= base_url("asset"); ?>/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
  <script src="<?= base_url("asset"); ?>/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>

    <!-- Dropzone !-->
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('asset/plugins/dropzone/dropzone.min.css') ?>">
  <link rel="stylesheet" type="text/css" href="<?php echo base_url('asset/plugins/dropzone/basic.min.css') ?>">
  <script type="text/javascript" src="<?php echo base_url('asset/plugins/dropzone/dropzone.min.js') ?>"></script>

  <!-- Ekko Lightbox -->
  <script src="<?= base_url("asset"); ?>/plugins/ekko-lightbox/ekko-lightbox.min.js"></script>
  <style>
 
  .main-header {
      z-index: 100;
  }
  .modal { overflow: auto !important; }
  </style>


</head>
<body class="hold-transition sidebar-mini">
<div id='LoadingDulu'></div>
<div class="wrapper">
  <!-- Navbar -->
  <?php include 'header.php'; ?>
 
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?= base_url(); ?>" class="brand-link">
      <img src="<?= base_url('asset'); ?>/dist/img/AdminLTELogo.png" alt="CBM Logo" class="brand-image img-circle elevation-3"
           style="opacity: .8">
      <span class="brand-text font-weight-light">GKY</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
    
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="<?= base_url('asset'); ?>/dist/img/avatar.png" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block"><?= $this->session->username; ?></a>
        </div>
      </div>
    
      <!-- Sidebar Menu -->
      <?php include 'menu-admin.php'; ?>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Main content -->
   
    <?php echo $contents; ?>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  <footer class="main-footer">
    <strong>Copyright &copy; 2022 <a href="#">GKY Dashboard</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
    
    </div>
  </footer>

  <div class="modal" id="ModalGue" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
            <h4 class="modal-title"><span id="ModalHeader"></span></h4>
            <button class="close" type="button" id="modal-close2" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
<!--
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><i class='fa fa-times-circle'></i></button>
						<h4 class="modal-title" id="ModalHeader"></h4>
            !-->
					</div>
					<div class="modal-body" id="ModalContent"></div>
					<div class="modal-footer" id="ModalFooter"></div>
				</div>
			</div>
		</div>
		
		<script>
		$('#ModalGue').on('hide.bs.modal', function () {
		   setTimeout(function(){ 
		   		$('#ModalHeader, #ModalContent, #ModalFooter').html('');
		   }, 500);
		});
		</script>

</div>

<!-- Default Order Modal-->
<div class="modal fade" id="showmodalkegiatan" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
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
            <h4 class="modal-title"><span id="title-headermak"></span></h4>
            <button class="close" type="button" id="modal-close3" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          </div>
          <div class="modal-showmak container padding-top-1x padding-bottom-1x">
            <div id="loading-mak" class="text-center">
                  
            </div>
          </div>
        </div>
      </div>
    </div>   
<!-- ./wrapper -->


<script>

$(function () {
    $(document).on('click', '[data-toggle="lightbox"]', function(event) {
      event.preventDefault();
      $(this).ekkoLightbox({
        alwaysShowClose: true
      });
    });
/*
    $('.filter-container').filterizr({gutterPixels: 3});
    $('.btn[data-filter]').on('click', function() {
      $('.btn[data-filter]').removeClass('active');
      $(this).addClass('active');
    });
    */
})

</script>

<!-- REQUIRED SCRIPTS -->
<script>

$(document).on("click", "#deleteimages", function() {
  var r = confirm("Are you sure ?");
  var dataURL3 = $(this).attr('data-href');
  var id = $(this).attr('data-id');
  var table = $('#table2').DataTable();

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
 console.log(header);
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


$(document).on("click", "#opensj", function() {
 

 var dataURL3 = $(this).attr('data-href');
 var header = $(this).attr('data-header');

 //var concatsj = $("#concatsj").val();
 //console.log(concatsj);
 
 $('#title-headermak').html(header);

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

$(document).on("click", "#openmak", function() {
 

 var dataURL3 = $(this).attr('data-href');
 var header = $(this).attr('data-header');
 
 //console.log(dataURL3);
// console.log(header);
 $('#title-headermak').html(header);

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

$(document).on("click", "#nonaktifkegiatan", function() {
  var r = confirm("Are you sure ?");
  var dataURL3 = $(this).attr('data-href');
  var id = $(this).attr('data-id');
  var table = $('#table').DataTable();

  var dataString = 'id='+id;
  console.log(dataURL3);

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
            toastr.success(message);
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

$(document).on("click", "#deletekegiatan", function() {
  var r = confirm("Are you sure ?");
  var dataURL3 = $(this).attr('data-href');
  var user_id = $(this).attr('data-id');
  var table = $('#table').DataTable();

  var dataString = 'user_id='+user_id;

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
            toastr.success(message);
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


$(document).on("click", "#updatemodal", function() {
 
  var dataURL3 = $(this).attr('data-href');
  var id = $(this).attr('data-id');
  var table = $('#table').DataTable();

  var dataString = 'id='+id;
  $.ajax({
    type: "POST", // Method pengiriman data bisa dengan GET atau POST
    url: dataURL3, // Isi dengan url/path file php yang dituju
    data:dataString,// data yang akan dikirim ke file yang dituju
    success: function(response){ // Ketika proses pengiriman berhasil
      var message = response.message;
      if(response.status == 1) {
          // location.reload();
          table.ajax.reload();
          toastr.success(message);
          $("#modal-close").trigger("click");
      } else {
          toastr.error(message);
      }
    },
    error: function (xhr, ajaxOptions, thrownError) { // Ketika ada error
      alert(thrownError); // Munculkan alert error
    }
  });
  
});


$(document).ready(function(){ // Ketika halaman sudah siap (sudah selesai di load)


  $("#provinsi").change(function(){ // Ketika user mengganti atau memilih data provinsi
  //console.log("cari");
  $("#kota").hide(); // Sembunyikan dulu combobox kota nya
  $.ajax({
      type: "POST", // Method pengiriman data bisa dengan GET atau POST
      url: "<?= base_url("administrator/kota"); ?>", // Isi dengan url/path file php yang dituju
      data: {provinsi : $("#provinsi").val()}, // data yang akan dikirim ke file yang dituju
      dataType: "json",
      beforeSend: function(e) {
      if(e && e.overrideMimeType) {
          e.overrideMimeType("application/json;charset=UTF-8");
      }
      },
      success: function(response){ // Ketika proses pengiriman berhasil
      setTimeout(function(){
          
          $("#kota").html(response.data_kota).show();
      }, 100);
      },
      error: function (xhr, ajaxOptions, thrownError) { // Ketika ada error
      alert(thrownError); // Munculkan alert error
      }
  });
  });
  
  
  $("#kota").change(function(){ // Ketika user mengganti atau memilih data provinsi
    $("#kecamatan").hide(); // Sembunyikan dulu combobox kota nya
  
    
    $.ajax({
        type: "POST", // Method pengiriman data bisa dengan GET atau POST
        url: "<?= base_url("administrator/kecamatan"); ?>", // Isi dengan url/path file php yang dituju
        data: {kota : $("#kota").val()}, // data yang akan dikirim ke file yang dituju
        dataType: "json",
        beforeSend: function(e) {
        if(e && e.overrideMimeType) {
            e.overrideMimeType("application/json;charset=UTF-8");
        }
        },
        success: function(response){ // Ketika proses pengiriman berhasil
        setTimeout(function(){
          
            $("#kecamatan").html(response.data_kecamatan).show();
        }, 100);
        },
        error: function (xhr, ajaxOptions, thrownError) { // Ketika ada error
        alert(thrownError); // Munculkan alert error
        }
    });
  });

  $("#kecamatan").change(function(){ // Ketika user mengganti atau memilih data provinsi
    $("#kelurahan").hide(); // Sembunyikan dulu combobox kota nya
  
    
    $.ajax({
        type: "POST", // Method pengiriman data bisa dengan GET atau POST
        url: "<?= base_url("administrator/kelurahan"); ?>", // Isi dengan url/path file php yang dituju
        data: {kecamatan : $("#kecamatan").val()}, // data yang akan dikirim ke file yang dituju
        dataType: "json",
        beforeSend: function(e) {
        if(e && e.overrideMimeType) {
            e.overrideMimeType("application/json;charset=UTF-8");
        }
        },
        success: function(response){ // Ketika proses pengiriman berhasil
        setTimeout(function(){
          
            $("#kelurahan").html(response.data_kelurahan).show();
        }, 100);
        },
        error: function (xhr, ajaxOptions, thrownError) { // Ketika ada error
        alert(thrownError); // Munculkan alert error
        }
    });
  });
  
});

</script>
</body>
</html>