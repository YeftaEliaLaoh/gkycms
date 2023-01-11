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
                     <div class="row">    

                        <div class="col-sm-3">
                            <div class="form-group">
                            <label>Pabean</label>
                                <select name="pabean_search" id="pabean_search" class="form-control">
                                    <?php echo $opt_pabean; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                            <label>Angkutan</label>
                                <select name="angkutan_search" id="angkutan_search" class="form-control">
                                    <?php echo $opt_angkutan; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                            <label>Tahun</label>
                                <select name="tahun_search" id="tahun_search" class="form-control" >
                                    <?php echo $opt_tahun; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <input type="button" value="Filter" id="btn-filter" class="btn btn-info mt-3">
                                
                            </div>
                        </div>
                        <!--
                        <div class="col-12 mb-2">
                            <input type="button" value="Filter" id="btn-filter" class="btn btn-info float-right ml-3">
                            
                            
                        </div>
                        !-->
                        
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="table2" class="table table-bordered table-hover">
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
        </div><!-- /.container-fluid -->
    </section>
<!-- /.row -->
</div><!-- /.container-fluid -->
<!-- /.content -->

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
            "url": "<?php echo site_url($list);?>",
            "type": "POST",
            "data": function(params) {
                params.angkutan = $('#angkutan_search').val();
                params.tahun = $('#tahun_search').val();
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