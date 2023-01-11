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

<!-- Main content -->
<div class="content">
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">List <?= $title; ?></h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="col-sm-12">
                        <div class="card m-3">
                        <div class="card-header bg-primary border-0">
                            <h3 class="card-title">
                            <i class="fas fa-search mr-1"></i>
                            Filter
                            </h3>
                            <!-- card tools -->
                            <div class="card-tools">
                            
                            <button type="button"
                                    class="btn btn-primary btn-sm"
                                    data-card-widget="collapse"
                                    data-toggle="tooltip"
                                    title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            </div>
                            <!-- /.card-tools -->
                        </div>

                        <div class="card-body">
                            <form role="form">

                                <div class="row">
                                    
                                    <div class="col-sm-4">
                                    <!-- text input -->
                                    <div class="form-group">
                                        <label>Periode</label>
                                        
                                        <input type="hidden" name="pt" id="pt" value="<?= $pt; ?>">
                                        
                                        <input type="text" class="form-control float-right" id="periode">
                                        <input type="hidden" name="startdate" value="" id="startdate">
                                            <input type="hidden" name="enddate" value="" id="enddate">
                                    </div>
                                    </div>
                                    <script>
                                    $(function () {
                                        $('#periode').on('apply.daterangepicker', function(ev, picker) {
                                           
                                           var startdate = picker.startDate.format('YYYY-MM-DD');
                                           var enddate = picker.endDate.format('YYYY-MM-DD');
                                           $("#startdate").val(startdate);
                                           $("#enddate").val(enddate);

                                       });
                                        $('#periode').daterangepicker({
                                            locale: {
                                                format:  'DD/MM/YYYY'
                                            }
                                        });
                                    })
                                    </script>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label>Marketplace</label>
                                            <select name="channel" id="channel" class="form-control">
                                                <option value="">All</option>
                                                <option value="tokopedia">Tokopedia</option>
                                                <option value="shopee">Shopee</option>
                                                <option value="lazada">Lazada</option>
                                                <option value="blibli">Blibli</option>
                                                <option value="jdid">JD.ID</option>
                                                <option value="bukalapak">Bukalapak</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label>Order Status</label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="">All</option>
                                                <option value="1">New</option>
                                                <option value="2">Picking</option>
                                                <option value="3">Packing</option>
                                                <option value="4">Shipped</option>
                                                <option value="5">Delivered</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label>Store</label>
                                            <!--
                                            <input type="text" class="form-control" placeholder="Enter ...">
                                            !-->
                                            <select name="store" id="store" class="form-control">
                                                <?php echo $option_store; ?>
                                            </select> 
                                        </div>
                                    </div>

                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label>Brand</label>
                                            <select name="brand" id="brand" class="form-control">
                                                <?php echo $option_brand; ?>
                                            </select> 
                                        </div>
                                    </div>

                                </div>

                                <div class="col-12 mb-2">
                                    <input type="submit" value="Filter" id="btn-submit" class="btn btn-info float-right ml-3">
                                    <a class="btn btn-success btn-md float-right" href="<?= base_url("export_transaksi"); ?>" target="_blank">Download</a>
                                    
                                </div>

                                </form>
                            </div>

                            </div>        
                    </div>
                    <!-- End Search Form !-->
                    <div class="card-body">
                        <!--
                        <div class="col-sm-4">
                            <select name="channel" id="channel" class="form-control">
                               <option value="">All</option>
                               <option value="tokopedia">Tokopedia</option>
                               <option value="shopee">Shopee</option>
                               <option value="lazada">Lazada</option>
                               <option value="blibli">Blibli</option>
                            </select>
                        </div>
                        !-->
                        <div class="col-12 mb-2">
                           
                            
                        </div>
                        
                    <?php
                    if(!empty($add)) { ?>
                    <a class="btn btn-success btn-sm" title='Tambah Data' id='openkegiatan'  href="#" data-book-id='1' data-toggle='modal' data-target='#showmodalkegiatan' data-header="<?= $modal_header; ?>" data-href='<?= base_url("$add"); ?>'>Tambah Data</a>
                    <?php } ?>
                    <a href="#" onclick="refresh();" id="refresh-submit" class="btn btn-info btn-sm">Refresh</a>
                        <div class="table-responsive">
                            <table id="table" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                    <?php
                                        echo showth($column);
                                    ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
<!-- /.row -->
</div><!-- /.container-fluid -->
<!-- /.content -->

<script type="text/javascript">

var table;

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
            "data": function(params) {
                params.brand = $('#brand').val();
                params.channel = $('#channel').val();
                params.startdate = $('#startdate').val();
                params.enddate = $('#enddate').val();
                params.status = $('#status').val();
                params.pt = $('#pt').val();
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
    
    $('#channel').change( function() {
        table.ajax.reload();
      });

      $('#status').change( function() {
        table.ajax.reload();
      });

      $('#periode').change( function() {
        table.ajax.reload();
      });

      $('#brand').change( function() {
        table.ajax.reload();
      });

    $('#btn-filter').click(function(){ //button filter event click
        table.ajax.reload();  //just reload table
    });
    $('#btn-reset').click(function(){ //button reset event click
        $('#form-filter')[0].reset();
        table.ajax.reload();  //just reload table
    });

});

function refresh() {
    $("#refresh-submit").html('<i class="fa fa-spinner fa-spin"></i>Loading');
    $('#refresh-submit').attr('disabled',true);

    $.ajax({
        type:'POST',
        data:form.serialize(),
        url:"<?= base_url('api/'.$refresh.''); ?>",
        success:function(data) {
            var message = data.message;
            console.log("haii");
            if(data.status == 1) {
                // location.reload();
                table.ajax.reload();
                toastr.success('Berhasil di perbaharui');
            } else {
                toastr.error(message);
                $("#refresh-submit").html('Refresh');
                $('#refresh-submit').removeAttr('disabled');
                $(".alert-submit").html('<div class="alert alert-danger alert-dismissible fade show text-center margin-bottom-1x"><span class="alert-close" data-dismiss="alert"></span><i class="icon-alert-triangle"></i>&nbsp;&nbsp;<span class="text-medium">'+message+'</div>');
            }
            
            
        }
    });
}

</script>