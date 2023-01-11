
<!-- Main content -->

<div class="row">


<div class="col-12">
<div class="card-body">
    
            <div class="dropzone">

                <div class="dz-message">

                <h3>Click here to add more images</h3>

                </div>

            </div>
            <table id="table2" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <td>Images</td>
                        <td>Action</td>
                    </tr>
                </thead>
                <tbody>
                <?php
                /*
                    $k = 0;
                    foreach($rimages as $m) {
                        $k++;
                        $img = base_url('uploads/products/thumbs/'.$m->image);
                        $id = $m->id;

                     //   $edit = "<a class='btn btn-success btn-xs' title='Tambah Data' id='openkegiatan'  href='#' data-book-id='1' data-toggle='modal' data-target='#showmodalkegiatan' data-header='Edit Data' data-href='".base_url("satuan/edit_satuan/$id")."'><i class='fas fa-edit'></i> Edit</a>";
                        $delete = "<a class='btn btn-danger btn-xs' id='deleteimage'  href='#' data-id='$id' data-href='".base_url("api/cms/delete_product_images")."'><i class='fas fa-trash'></i> Delete</a>";

                        $action = $edit.'&nbsp;'.$delete;
                        echo '<tr>
                                <td><div class="col-sm-2"><a href="'.$img.'" data-toggle="lightbox" data-title="img-'.$k.'" data-gallery="gallery">
                                <img src="'.$img.'" class="img-fluid mb-2" alt="image'.$k.'"/></div></td>
                                <td>'.$action.'</td>
                            </tr>';
                    }
                    */
                    ?>
                
                </tbody>
            </table>

        </div>
</div>


</div>

<style>
.dropzone {
    border: 2px dashed #0087F7;
}
</style>


<script type="text/javascript">

var table;
$(document).ready(function() {

    //datatables
    table = $('#table2').DataTable({ 

        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [1], //Initial no order.

        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": "<?php echo base_url("master/product/image_list/$product_id");?>",
            "type": "POST",
            "data": function ( data ) {
            }
        },

        //Set column definition initialisation properties.
        "columnDefs": [
        { 
            "targets": [ 0 ], //first column / numbering column
            "orderable": false, //set not orderable
        },
        ],

    });

    $('#btn-filter').click(function(){ //button filter event click
        table.ajax.reload();  //just reload table
    });
    $('#btn-reset').click(function(){ //button reset event click
        $('#form-filter')[0].reset();
        table.ajax.reload();  //just reload table
    });

});

</script>

<script type="text/javascript">


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


Dropzone.autoDiscover = false;



var foto_upload= new Dropzone(".dropzone",{

url: "<?php echo base_url('master/product/proses_upload_product/') ?>",

maxFilesize: 2,

method:"post",

acceptedFiles:"image/*",

paramName:"userfile",

dictInvalidFileType:"Type file ini tidak dizinkan",

addRemoveLinks:true,

});





//Event ketika Memulai mengupload

foto_upload.on("sending",function(a,b,c){

	a.token=Math.random();

	c.append("token_foto",a.token); //Menmpersiapkan token untuk masing masing foto

  c.append("product_id","<?= $product_id; ?>"); //Menmpersiapkan token untuk masing masing foto

  toastr.success('Berhasil disimpan');
  table.ajax.reload();

});





//Event ketika foto dihapus

foto_upload.on("removedfile",function(a){

	var token=a.token;

	$.ajax({

		type:"post",

		data:{token:token},

		url:"<?php echo base_url('master/product/remove_foto_product/') ?>",

		cache:false,

		dataType: 'json',

		success: function(){

			console.log("Foto terhapus");

		},

		error: function(){

			console.log("Error");



		}

	});

});



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