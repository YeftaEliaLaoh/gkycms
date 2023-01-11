<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container">
    <div class="row mb-2">
        <div class="col-sm-6">
        <h1 class="m-0 text-dark"><?= $title; ?></h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active"><?= $title; ?></li>
        </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<input type="hidden" name="orderid" id="orderid" value="<?= $order_id; ?>">
<!-- Main content -->
<div class="content">
    <section class="content">
    <div class="invoice p-3 mb-3">
              <!-- title row -->
              <div class="row">
              <div class="alert-submit col-sm-12"></div>
                <div class="col-12">
                <?php 
                 // var_dump($data);
                  ?>
                  <h4>
                    <i class="fas fa-globe"></i> Draft Order ID : <?= $data->order_apps_id; ?>
                    <small class="float-right">Date: <?= $data->format_date; ?></small>
                  </h4>
                </div>
                <!-- /.col -->
              </div>
              <!-- info row -->
              <div class="row invoice-info">
                <div class="col-sm-4 invoice-col">
                  Customer Name
                  <address>
                    <strong><?= $data->customer_name; ?></strong><br>
                    <?= $data->complete_address; ?>
                  </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                  Nomor NP
                  <address>
                    <strong><?= $data->np_id; ?></strong>
                    <?php
                    /*
                    if(empty($data->np_id)) { ?>
                    <button type="button" title='Tambah Data' id='openkegiatan'  href='#' data-book-id='1' data-toggle='modal' data-target='#showmodalkegiatan' data-header='Create NP' data-href='<?= base_url("orders/createnp/$data->order_id"); ?>' class="btn btn-success">
                        Create NP
                    </button>                  
                    <?php }
                    */ ?>
                  </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                  Order Status
                  <address>
                    <strong><?= cek_status_order($data->status_order,""); ?></strong>
                  </address>
                  <?php
                  if(!empty($d->reason_reject)) {
                    echo "<b>$d->reason_reject</b>";
                  }
                  if(!empty($d->cancel_note)) {
                    echo "<b>$d->cancel_note</b>";
                  }
                  ?>
                    <!--
                  <b>Sales Name : <?= $data->sales_name; ?></b><br>
                  <b>Nomor Kontrak :</b> <?= $data->np_id; ?><br>
                  <b>Shipping Total:</b> <?= $data->format_shipping_total; ?><br>
                  <b>Total : <?= $data->format_total; ?></b> <br>
                  <b>Account:</b> 968-34567
                  !-->
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->

              <!-- Table row -->
              <div class="row">
                <div class="col-12 table-responsive">
                  <h4>Product Details</h4>
                  <table class="table table-striped">
                    <thead>
                    <?php
                    if(empty($data->np_id)) { ?>
                    <tr>
                      <th>Product</th>
                      <th>Ordered Qty</th>
                      <th>Notes</th>
                      <th>Price</th>
                      <th>Discount</th>
                      <th>Price After Disc</th>
                      <th>Subtotal</th>                      
                      <th>Action</th>
                    </tr>
                    
                    </thead>
                    <tbody>
                    <?php
                    foreach($data->items as $t) {
                        $spec = null;
                        $item_id = $t->id;

                        $action = "";
                        if($status_order == 0 || $status_order == 15) {
                        $edit = "<a class='btn btn-success btn-xs' title='Tambah Data' id='openkegiatan'  href='#' data-book-id='1' data-toggle='modal' data-target='#showmodalkegiatan' data-header='Edit Data' data-href='".base_url("orders/view_item/$item_id")."'><i class='fas fa-edit'></i> Edit</a>";
//                        $edit = "<a class='btn btn-success btn-xs' title='Tambah Data'  href='".base_url("orders/view_items/$item_id")."' ><i class='fas fa-edit'></i> Edit</a>";
                       // $delete = "<a class='btn btn-danger btn-xs' id='deletekegiatan'  href='#' data-id='$item_id' data-href='".base_url("cms/delete_orders_detail")."'><i class='fas fa-trash'></i> Delete</a>";

                        $action = $edit.'&nbsp;'.$delete;
                        }

                        echo "<tr>
                        <td>$t->product_name</td>
                        <td>$t->ordered_qty</td>
                        <td>$t->notes</td>
                        <td>$t->format_price_old</td>
                        <td>$t->discount</td>
                        <td>$t->format_price</td>
                        <td>$t->format_total</td>
                        <td>$action</td>
                      </tr>";
                    }
                    ?>
                    </tbody>
                    </table>
                </div>
                    
                    <div class="col-12 mb-2">
                    <?php
                    if($status_order == 0 || $status_order == 15 || $status_order == 12) {?>
                      <input type="submit" value="Approve" id="btn-submit-approve" class="btn btn-success float-right">
                      <?php 
                      
                      if($status_order == 0 && $status_order != 15) { ?>
                      &nbsp;&nbsp;<input type="button" value="Verify" id="btn-submit-verif" class="btn btn-warning">
                      <?php }                       
                      /*
                      ?>                      
                      <?php if($status_order != 12) { ?>
                      &nbsp;&nbsp;<input type="button" value="Confirm" id="btn-submit-confirm" class="btn btn-primary">
                      <?php } 
                      */?>
                    <?php } ?>
                    </div>  
                    <?php
                    } else { ?>
                    <tr>
                      <th>Product</th>
                      <th>Ordered Qty</th>
                      <th>Notes</th>
                      <th>Price</th>
                      <th>Discount</th>
                      <th>Price After Disc</th>
                      <th>Subtotal</th>                      
                      <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                   foreach($data->items as $t) {
                    $spec = null;
                    $item_id = $t->id;

                    $action = "";
                    if($status_order == 0 || $status_order == 15) {
                    $edit = "<a class='btn btn-success btn-xs' title='Tambah Data' id='openkegiatan'  href='#' data-book-id='1' data-toggle='modal' data-target='#showmodalkegiatan' data-header='Edit Data' data-href='".base_url("orders/view_item/$item_id")."'><i class='fas fa-edit'></i> Edit</a>";
//                        $edit = "<a class='btn btn-success btn-xs' title='Tambah Data'  href='".base_url("orders/view_items/$item_id")."' ><i class='fas fa-edit'></i> Edit</a>";
                   // $delete = "<a class='btn btn-danger btn-xs' id='deletekegiatan'  href='#' data-id='$item_id' data-href='".base_url("cms/delete_orders_detail")."'><i class='fas fa-trash'></i> Delete</a>";

                    $action = $edit.'&nbsp;'.$delete;
                    }

                    echo "<tr>
                    <td>$t->product_name</td>
                    <td>$t->ordered_qty</td>
                    <td>$t->notes</td>
                    <td>$t->format_price_old</td>
                    <td>$t->discount</td>
                    <td>$t->format_price</td>
                    <td>$t->format_total</td>
                    <td>$action</td>
                  </tr>";
                  }
                    ?>
                    </tbody>
                    </table>
                </div>
                    <?php } ?>
                  
                <!-- /.col -->
                

                
              </div>
              
              <!-- /.row -->

              <div class="row">
                <!-- accepted payments column -->
                <div class="col-6">
                  <p class="lead"><b>Payment Methods: <?= $data->payment_method_name; ?></b></p>                 
                </div>
                <!-- /.col -->
                <div class="col-6">
                  <!--
                  <p class="lead">Amount Due 2/22/2014</p>
                  !-->
                  <div class="table-responsive">
                    <table class="table">
                      <tbody><tr>
                        <th style="width:50%">Subtotal:</th>
                        <td><?= $data->format_total; ?></td>
                      </tr>
                      
                      <tr>
                        <th>Shipping:</th>
                        <td><?= $data->format_shipping_total; ?></td>
                      </tr>
                      <?php
                      if(!empty($data->voucher_discount)) { ?>
                      <tr>
                        <th>Voucher Discount:</th>
                        <td>- (<?= $data->voucher_discount; ?>)</td>
                      </tr>
                      <?php
                      }
                      ?>
                      <tr>
                        <th>Total:</th>
                        <td><?= $data->format_total_all; ?></td>
                      </tr>
                    </tbody></table>
                  </div>
                </div>
                <!-- /.col -->
              </div>
              <!-- /.row -->

              <!-- this row will not appear when printing -->
              <div class="row no-print">
                <?php
                if(!empty($data->np_id)) { ?>
               
                <div class="col-12 table-responsive">
                  <h4>Delivery Order</h4>
                  <?php
                  if($data->status_order != 7) { ?> 
                  <button type="button" title='Tambah Data' id='openkegiatan'  href='#' data-book-id='1' data-toggle='modal' data-target='#showmodalkegiatan' data-header='Create SJ' data-href='<?= base_url("orders/createsj/$data->order_id"); ?>' class="btn btn-success">
                    Create SJ
                  </button><br><br> 
                  <?php } ?> 
                  <table class="table table-striped">
                    <thead>
                    <tr>
                      <th>Nomor SJ</th>
                      <th>Tanggal SJ</th>
                      <th>Items</th>
                      <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach($data->sj as $j) {
                        $date = tgl_indoo($j->tgl_sj);
                        $status = cek_status_order($j->status_sj,$j->sj_id);
//                        $status_order = updateString($status,$j->sj_id,"no_resi");
                        $id = $j->id;
                        $view = "";
                        if($status_order == 0 || $status_order == 15) {
                          $view = "<a class='btn btn-success btn-xs' title='Items' id='openmak'  href='#' data-book-id='1' data-toggle='modal' data-target='#showmodalmak' data-header='Items' data-href='".base_url("orders/items/$data->order_id/$id")."'><i class='fas fa-edit'></i> Items</a>";
                        }

                        echo "<tr>
                        <td>$j->sj_id</td>
                        <td>$date</td>
                        <td>$view</td>
                        <td>$status</td>
                      </tr>";
                    }
                    ?>
                    </tbody>
                  </table>
                </div>

                <?php } ?>
              </div>
              <!--
              <div class="row no-print">
                <div class="col-12">
                  <a href="invoice-print.html" target="_blank" class="btn btn-default"><i class="fas fa-print"></i> Print</a>
                  <button type="button" class="btn btn-success float-right"><i class="far fa-credit-card"></i> Submit
                    Payment
                  </button>
                  <button type="button" class="btn btn-primary float-right" style="margin-right: 5px;">
                    <i class="fas fa-download"></i> Generate PDF
                  </button>
                </div>
              </div>
              !-->
              <div class="row no-print">
                <div class="col-12">
                  
                  <button type="button" onclick="location.href='<?= base_url('orders'); ?>'" class="btn btn-danger float-right" style="margin-right: 5px;">
                    Back to List Order
                  </button>
                </div>
              </div>
            </div>
    </section>
<!-- /.row -->
</div><!-- /.container-fluid -->
<!-- /.content -->

<script type="text/javascript">

$('#btn-submit-approve').click(function(e) {
          e.preventDefault();
          var orderid = $("#orderid").val();

          $("#btn-submit-approve").html('<i class="fa fa-spinner fa-spin"></i>Loading');
          $('#btn-submit-approve').attr('disabled',true);
          $.ajax({
            type:'POST',
            data: { orderid : orderid},
            url:"<?= base_url('api/cms/createorderax'); ?>",
            success:function(data) {
                var message = data.message;
                //console.log(message);
                //console.log(orderid);
                //console.log(message);
                
                if(data.status == 1) {
                    location.reload();
                   toastr.success('Berhasil di perbaharui');
                } else {
                    toastr.error(message);
                    $("#btn-submit-approve").html('Approve');
                    $('#btn-submit-approve').removeAttr('disabled');
                    $(".alert-submit").html('<div class="alert alert-danger alert-dismissible fade show text-center margin-bottom-1x"><span class="alert-close" data-dismiss="alert"></span><i class="icon-alert-triangle"></i>&nbsp;&nbsp;<span class="text-medium">'+message+'</div>');
                }
                
                
                
            }
          });
          
});

$('#btn-submit-confirm').click(function(e) {
          e.preventDefault();
          var orderid = $("#orderid").val();

          $("#btn-submit-confirm").html('<i class="fa fa-spinner fa-spin"></i>Loading');
          $('#btn-submit-confirm').attr('disabled',true);
          $.ajax({
            type:'POST',
            data: { orderid : orderid, status : 12},
            url:"<?= base_url('api/cms/updateorderstatus'); ?>",
            success:function(data) {
                var message = data.message;
                //console.log(message);
                //console.log(orderid);
                //console.log(message);
                
                if(data.status == 1) {
                    location.reload();
                   toastr.success('Berhasil di perbaharui');
                } else {
                    toastr.error(message);
                    $("#btn-submit-confirm").html('Confirm');
                    $('#btn-submit-confirm').removeAttr('disabled');
                    $(".alert-submit").html('<div class="alert alert-danger alert-dismissible fade show text-center margin-bottom-1x"><span class="alert-close" data-dismiss="alert"></span><i class="icon-alert-triangle"></i>&nbsp;&nbsp;<span class="text-medium">'+message+'</div>');
                }
                
                
                
            }
          });
          
});

$('#btn-submit-verif').click(function(e) {
          e.preventDefault();
          var orderid = $("#orderid").val();

          $("#btn-submit-verif").html('<i class="fa fa-spinner fa-spin"></i>Loading');
          $('#btn-submit-verif').attr('disabled',true);
          $.ajax({
            type:'POST',
            data: { orderid : orderid,status : 15},
            url:"<?= base_url('api/cms/updateorderstatus'); ?>",
            success:function(data) {
                var message = data.message;
                //console.log(message);
                //console.log(orderid);
                //console.log(message);
                
                if(data.status == 1) {
                    location.reload();
                   toastr.success('Berhasil di perbaharui');
                } else {
                    toastr.error(message);
                    $("#btn-submit-verif").html('Verify');
                    $('#btn-submit-verif').removeAttr('disabled');
                    $(".alert-submit").html('<div class="alert alert-danger alert-dismissible fade show text-center margin-bottom-1x"><span class="alert-close" data-dismiss="alert"></span><i class="icon-alert-triangle"></i>&nbsp;&nbsp;<span class="text-medium">'+message+'</div>');
                }
                
                
                
            }
          });
          
});

function setproduct(id,itemname,amount) {
   //console.log(id); 
   console.log(itemname);
   $("#product_ax_id").val(id);
   $("#product_ax_name").val(itemname);
   $("#product_ax_amount").val(amount);
   $( "#modal-close3" ).trigger( "click");
}


var table;
<?= $js; ?>
function change_status(id,cabang,status) {
    //console.log(id+" "+cabang+" "+status);
    $.ajax({
        type:'POST',
        data:{id:id,cabang:cabang},
        url:"<?= base_url('api/cms/change_status'); ?>",
        success:function(data) {
            var message = data.message;
            // console.log("haii");
            if(data.status == 1) {
                // location.reload();
                //table.ajax.reload();
               
                toastr.success('Berhasil di perbaharui');
            } else {
                toastr.error(message);
            }
            
            
        }
    });
}
$(document).ready(function() {

    //datatables
    table = $('#table').DataTable({ 

        "processing": true, //Feature control the processing indicator.
        "serverSide": true, //Feature control DataTables' server-side processing mode.
        "order": [1], //Initial no order.

        // Load data for the table's content from an Ajax source
        "ajax": {
            "url": "<?php echo site_url($list);?>",
            "type": "POST",
            "data": function ( data ) {
            }
        },
        "fnDrawCallback": function() {
            $('.my_switch').bootstrapSwitch();
        // $(this).bootstrapSwitch('state', $(this).prop('checked'));
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