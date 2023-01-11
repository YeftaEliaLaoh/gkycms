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
                            if (!empty($add)) {
                                $modal = "openkegiatan";
                                $modal_target = "showmodalkegiatan";
                                if (!empty($show_modal)) {
                                    $modal = "openkegiatan2";
                                    $modal_target = "showmodalkegiatan2";
                                }
                            ?>
                                <a class="btn btn-success btn-sm" title='Add Data' id='<?= $modal; ?>' href="#" data-book-id='1' data-toggle='modal' data-target='#<?= $modal_target; ?>' data-header="<?= $modal_header; ?>" data-href='<?= base_url("$add"); ?>'>Add Data</a>
                            <?php } ?>
                            <?php
                            if (!empty($back)) { ?>
                                <a class="btn btn-danger btn-sm" href="<?= base_url($back) ?>">Back</a>
                            <?php } ?>
                            
                            <div class="table-responsive">
                                <table id="table-1" class="table table-striped table-white">
                                    <thead>
                                        <tr>
                                            <td colspan="1" align="center">No</td>
                                            <td colspan="1" align="center">Kode Barang</td>
                                            <td colspan="1" align="center">Nama Barang</td>
                                            <td colspan="1" align="center">Jenis</td>
                                            <td colspan="1" align="center">Merk</td>
                                            <td colspan="1" align="center">Satuan</td>
                                            <td colspan="1" align="center">Harga Jual Dasar</td>
                                            <?php
                                            foreach ($harga as $d) {
                                            ?>
                                                <td colspan="1" align="center"><?php echo $d->nama ?></td>
                                            <?php } ?> 

                                            <td colspan="1" align="center">Action</td>
                                                                                       
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                        $no = 1;
                                        foreach ($data as $d) {
                                        ?>
                                            <tr>
                                                <td><?php echo $no++ ?></td>
                                                <td><?php echo $d->barang_id ?></td>
                                                <td><?php echo $d->nama_barang ?></td>
                                                <td><?php echo $d->namakategori ?></td>
                                                <td><?php echo $d->merk ?></td>
                                                <td><?php echo $d->satuan ?></td>
                                                <td><?php echo harga($d->harga_jual) ?></td>
                                                <?php
                                                foreach ($harga_to as $da) {
                                                    if ($d->barang_id == $da->barang_id) {
                                                ?>
                                                        <td><?php echo harga($da->harga) ?></td>
                                                <?php
                                                    }
                                                } ?>
                                                <?php
                                                foreach ($harga_modern as $da) {
                                                    if ($d->barang_id == $da->barang_id) {
                                                ?>
                                                        <td><?php echo harga($da->harga) ?></td>
                                                <?php
                                                    }
                                                } ?>
                                                <?php
                                                foreach ($harga_kanvas as $da) {
                                                    if ($d->barang_id == $da->barang_id) {
                                                ?>
                                                        <td><?php echo harga($da->harga) ?></td>
                                                <?php
                                                    }
                                                } ?>
                                                <?php
                                                foreach ($harga_pinyuh as $da) {
                                                    if ($d->barang_id == $da->barang_id) {
                                                ?>
                                                        <td><?php echo harga($da->harga) ?></td>
                                                <?php
                                                    }
                                                } ?>
                                                <?php
                                                foreach ($harga_freeline as $da) {
                                                    if ($d->barang_id == $da->barang_id) {
                                                ?>
                                                        <td><?php echo harga($da->harga) ?></td>
                                                <?php
                                                    }
                                                } ?>
                                                <?php
                                                foreach ($harga_6 as $da) {
                                                    if ($d->barang_id == $da->barang_id) {
                                                ?>
                                                        <td><?php echo harga($da->harga) ?></td>
                                                <?php
                                                    }
                                                } ?>
                                                <td><a class="btn btn-success btn-xs" title="Tambah Data" id="openkegiatan"  href="#" data-book-id="1" data-toggle="modal" data-target="#showmodalkegiatan" data-header="Edit Data" data-href="<?php echo base_url('master/setharga/view_setharga')?>/<?php echo $d->barang_id ?>"><i class='fas fa-edit'></i> Edit</a></td>
                                            </tr>
                                        <?php } ?>
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
    
    function setproduct(id, itemname, amount) {
        //console.log(id); 
        console.log(itemname);
        $("#product_ax_id").val(id);
        $("#product_ax_name").val(itemname);
        $("#product_ax_amount").val(amount);
        $("#modal-close3").trigger("click");
    }

var table;
<?= $js; ?>
function change_status(id, cabang, status) {
        //console.log(id+" "+cabang+" "+status);
        $.ajax({
            type: 'POST',
            data: {
                id: id,
                cabang: cabang
            },
            url: "<?= base_url('api/cms/change_status'); ?>",
            success: function(data) {
                var message = data.message;
                // console.log("haii");
                if (data.status == 1) {
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

});

</script>