<script>

$(document).ready(function() {


    <?php
    if(empty($id)) { ?>
        $("#pabean").val('impor');
        $("#angkutan").val('udara');

        get_no_jo();
        $("#tanggal").on("change.datetimepicker", ({date}) => {
        //    var dob = $("#tanggal").find("input").val();
            //console.log(dob);
            get_no_jo();
        })
        
    <?php
    } else { ?>
    $("#pabean").val('<?= $pabean; ?>');
    $("#angkutan").val('<?= $angkutan; ?>');
    $("#btn-submit").html('<?= $submit_name; ?>');
    <?php
    }
    ?>

    $('.select2bs4').select2({
        theme: 'bootstrap4',
        //  tags: true
    });
    
    $('.select2bs42').select2({
        theme: 'bootstrap4',
        tags: true
    })

});

</script>
<input type="hidden" name="tbl" value="<?= $tbl; ?>">
<input type="hidden" name="act" value="<?= $act; ?>">
<input type="hidden" name="id" id="id" value="<?= $id; ?>">


<div class="row">
    <div class="alert-submit col-sm-12"></div>
    <div class="row col-sm-6">
    <?php
        echo form_builder($column);
    ?>
    
        <div class="col-sm-12" id="petikemas" style="<?= $petikemas_display; ?>">

            <label style="text-decoration:underline;">Identitas Peti Kemas</label><br>
            <div class="row">

                <div class="col-sm-6" >
                    <div class="form-group">
                    
                        <table class="table table-bordered table-hover col-sm-3" id="tbl1">
                            <thead><tr><td colspan="2">20 f</td></thead>
                            <tbody>
                            <?php 
                            if(empty($row->identitas_peti20f)) {
                                echo "<tr><td><input type='text' name='20f[]'></td><td></td></tr>";
                            } else {
                                $identitas_20f = explode(",",$row->identitas_peti20f);
                                foreach($identitas_20f as $f2) {
                                echo "<tr><td><input type='text' name='20f[]' value='$f2'></td><td><button class='btn btn-default' id='HapusBaris'><i class='fa fa-times' style='color:red;'></i></button></td></tr>";
                                }
                            }
                            ?>
                                
                            </tbody>
                            
                        </table>
                        <table>
                            <tr><td><button type="button" class="btn btn-primary" onclick="barisbaru(1);">+ Tambah 20 f</button></td></tr>
                        </table>

                    </div>
                </div>

                <div class="col-sm-6" >
                    <div class="form-group">
                    
                        <table class="table table-bordered table-hover col-sm-3" id="tbl2">
                            <thead><tr><td>40 f</td></thead>
                            <tbody>
                            <?php 
                            if(empty($row->identitas_peti40f)) {
                                echo "<tr><td><input type='text' name='40f[]'></td><td></td></tr>";
                            } else {
                                $identitas_40f = explode(",",$row->identitas_peti40f);
                                foreach($identitas_40f as $f4) {
                                echo "<tr><td><input type='text' name='40f[]' value='$f4'></td><td><button class='btn btn-default' id='HapusBaris'><i class='fa fa-times' style='color:red;'></i></button></td></tr>";
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                        <table>
                            <tr><td><button type="button" class="btn btn-primary" onclick="barisbaru(2);">+ Tambah 40 f</button></td></tr>
                        </table>

                    </div>
                </div>

            </div>
        </div>


    </div>



    <div class="row col-sm-6">

            <div class="col-sm-6" id="alamatkirim">

        <label style="text-decoration:underline;">Alamat Pengiriman</label><br>
        <div class="row">

            <div class="col-sm-3 mb-3">
            <label>Nama PT</label>
            </div>
            <div class="col-sm-9">
            <input type="text" name="ap_nama_pt" id="ap_nama_pt" value="<?= $row->ap_nama_pt; ?>" class="form-control">
            </div>

            <div class="col-sm-3 mb-3">
            <label>Contact Person</label>
            </div>
            <div class="col-sm-9">
            <input type="text" name="ap_contact_person" id="ap_contact_person" value="<?= $row->ap_contact_person; ?>" class="form-control">
            </div>

            <div class="col-sm-3 mb-3">
            <label>Alamat</label>
            </div>
            <div class="col-sm-9">
            <textarea class="form-control" name="ap_alamat" id="ap_alamat"><?= $row->ap_alamat; ?></textarea>
            </div>

        </div>
        </div>


        <div class="col-sm-6">
        <div class="form-group">
            <label style="text-decoration:underline;">Kemasan</label>
            <div class="row">
                <div class="col-sm-3 mb-3">
                    <label>Jenis</label>
                </div>
                <div class="col-sm-9 row">
                    <input type="number" value="<?= $row->berat_satuan; ?>" name="berat_satuan" class="form-control col-sm-6">&nbsp;
                    <select name="jenis_peti" class="form-control col-sm-5">
                    <?php
                    echo $opt_petikemas;
                    ?>
                    </select>
                </div>

                <div class="col-sm-3 mb-3">
                    <label>Gross Weight</label>
                </div>
                <div class="col-sm-9 row">
                    <input type="text" name="gross_wg" value="<?= $row->gross_wg; ?>" class="form-control col-sm-6">&nbsp;Kg
                </div>

                <div class="col-sm-4">
                    <label>Chargeable</label>
                </div>
                <div class="col-sm-8 row">
                    <input type="text" name="chargable" value="<?= $row->chargable; ?>" class="form-control col-sm-10">&nbsp;Kg
                </div>

            </div>

        </div>
        </div>

        <div class="col-sm-6">
        <div class="form-group">
        <label style="text-decoration:underline;">Biaya</label>
        <div class="row">
            <div class="col-sm-3 mb-3">
                <label>CIF</label>
            </div>
            <div class="col-sm-9">
                <input type="text" name="cif" id="cif" onkeyup="update_jumlah_biaya();" value="<?= harga_view($row->cif); ?>" required class="form-control">
            </div>

            <div class="col-sm-3 mb-3">
                <label>Kurs</label>
            </div>
            <div class="col-sm-9">
                <input type="text" name="kurs" id="kurs" onkeyup="update_jumlah_biaya();" value="<?= harga_view($row->kurs); ?>" required class="form-control">
            </div>

            <div class="col-sm-3 mb-3">
                <label>Jumlah</label>
            </div>
            <div class="col-sm-9">
                <input type="text" name="jumlah_biaya" id="jumlah_biaya" value="<?= harga_view($row->jumlah_biaya); ?>"  required readonly class="form-control">
            </div>

        </div>
        
        </div>
        </div>

        <div class="col-sm-6">
        <div class="form-group">
        <label style="text-decoration:underline;">Pungutan</label>
        <div class="row">
            <div class="col-sm-3 mb-3">
                <label>BM</label>
            </div>
            <div class="col-sm-9">
                <input type="text" name="bm" id="bm" value="<?= harga_view($row->bm); ?>" onkeyup="update_jumlah_pungutan();" class="form-control">
            </div>

            <div class="col-sm-3 mb-3">
                <label>Cukai</label>
            </div>
            <div class="col-sm-9">
                <input type="text" name="cukai" id="cukai" value="<?= harga_view($row->cukai); ?>" onkeyup="update_jumlah_pungutan();" class="form-control">
            </div>

            <div class="col-sm-3 mb-3">
                <label>PPN</label>
            </div>
            <div class="col-sm-9">
                <input type="text" name="ppn" id="ppn" value="<?= harga_view($row->ppn); ?>" onkeyup="update_jumlah_pungutan();" class="form-control">
            </div>

            <div class="col-sm-3 mb-3">
                <label>PPN Bm</label>
            </div>
            <div class="col-sm-9">
                <input type="text" name="ppn_bm" id="ppn_bm" value="<?= harga_view($row->ppn_bm); ?>" onkeyup="update_jumlah_pungutan();" class="form-control">
            </div>

            <div class="col-sm-3 mb-3">
                <label>Pph</label>
            </div>
            <div class="col-sm-9">
                <input type="text" name="pph" id="pph" value="<?= harga_view($row->pph); ?>" onkeyup="update_jumlah_pungutan();" class="form-control">
            </div>

            <div class="col-sm-3 mb-3">
                <label>Jumlah</label>
            </div>
            <div class="col-sm-9">
                <input type="text" name="jumlah_pungutan" value="<?= harga_view($row->jumlah_pungutan); ?>" required id="jumlah_pungutan" readonly class="form-control">
            </div>

            <div class="col-sm-3 mb-3">
                <label></label>
            </div>
            <div class="col-sm-9">
                <input type="hidden" name="iduser" id="iduser" value="<?php echo $this->session->nama_lengkap;?>" readonly class="form-control">
            </div>

        </div>
        
        </div>
        </div>
    </div>


</div>