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
                    <div class="card-body">
                    <?php
                    if(!empty($add)) { 
                        $modal = "openkegiatan";
                        $modal_target = "showmodalkegiatan";
                        if(!empty($show_modal)) {
                            $modal = "openkegiatan2";
                            $modal_target = "showmodalkegiatan2";
                        }
                        ?>
                    <a class="btn btn-primary btn-sm" title='Add Data' id='<?= $modal; ?>'  href="#" data-book-id='1' data-toggle='modal' data-target='#<?= $modal_target; ?>' data-header="<?= $modal_header; ?>" data-href='<?= base_url("$add"); ?>'>Add Data</a>
                    <?php } ?>
                    <?php
                    if(!empty($back)) { ?>
                    <a class="btn btn-danger btn-sm" href="<?= base_url($back) ?>">Back</a>
                    <?php } ?>
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
                                    <?php
                                    echo $list;
                                    ?>
                                    
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

$(document).ready(function() {
/*
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
            //$('.my_switch').bootstrapSwitch();
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
*/
});

</script>