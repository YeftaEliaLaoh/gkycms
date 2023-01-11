
<!-- Main content -->

<div class="row">


<div class="col-12">
<div class="card-body">

            <table id="table2" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <td>Color</td>
                        <td>Length</td>
                        <td>Thickness</td>
                        <td>Price</td>
                        <td>Discount (%)</td>
                        <td>Status</td>
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

function update_price(product_id,product_varian,cabang,price) {
  $.ajax({
        type:'POST',
        data:{id:product_id,id_varian:product_varian,cabang:cabang,price:price},
        url:"<?= base_url('api/cms/update_price'); ?>",
        success:function(data) {
            var message = data.message;
            // console.log("haii");
            if(data.status == 1) {
                toastr.success('Berhasil di perbaharui');
            } else {
                toastr.error(message);
            }
            
            
        }
  });
}


function update_disc(product_id,product_varian,cabang,price) {
  $.ajax({
        type:'POST',
        data:{id:product_id,id_varian:product_varian,cabang:cabang,price:price},
        url:"<?= base_url('api/cms/update_disc'); ?>",
        success:function(data) {
            var message = data.message;
            // console.log("haii");
            if(data.status == 1) {
                toastr.success('Berhasil di perbaharui');
            } else {
                toastr.error(message);
            }
            
            
        }
  });
}

function change_status_varian(product_id,product_varian,cabang,status) {
 // console.log(product_id+" "+product_varian+" "+cabang);
  var price = $("#t-"+product_varian).val();
  $.ajax({
        type:'POST',
        data:{id:product_id,id_varian:product_varian,cabang:cabang,price:price},
        url:"<?= base_url('api/cms/change_status_varian'); ?>",
        success:function(data) {
            var message = data.message;
            var stt = data.stt;
            
            // console.log("haii");
            if(data.status == 1) {
                // location.reload();
                //table.ajax.reload();
                if(stt == 1) {
                  $("#t-"+product_varian).attr("readonly", false); 
                } else {
                  $("#t-"+product_varian).attr("readonly", true); 
                }
               
                toastr.success('Berhasil di perbaharui');
            } else {
                toastr.error(message);
            }
            
            
        }
  });
}

function discount(type,id) {
      var val = "#discount_percentage_"+id;
      var val2 = "#discount_price_"+id;


      if(type == 1) { //price
        $(val).hide();
        $(val).val("");
        $(val2).show();
      } else { //percentage
        $(val2).hide();
        $(val2).val("");
        $(val).show();
      }

  }

var table;
$(document).ready(function() {

    //datatables
    table = $('#table2').DataTable({ 

        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [1], //Initial no order.

        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": "<?php echo base_url("product/varian_branch_list/$product_id/$cabang");?>",
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
        "fnDrawCallback": function() {
            $('.my_switch').bootstrapSwitch();
        // $(this).bootstrapSwitch('state', $(this).prop('checked'));
        },

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