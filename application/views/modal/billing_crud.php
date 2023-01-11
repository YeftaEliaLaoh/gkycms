<script>

$(document).ready(function() {

    $('.select2bs4').select2({
        theme: 'bootstrap4',
        //  tags: true
    });
    
    $('.select2bs42').select2({
        theme: 'bootstrap4',
        tags: true
    })

    $("#tanggal").on("change.datetimepicker", ({date}) => {
        var dob = $("#tanggal").find("input").val();
        //console.log(dob);
        var id = $("#id").val();
        if(id == '') {
            get_billing();
        }
    })

    var id = $("#id").val();
    if(id != '') {
        var idjoborder = $("#id_joborder").val();
        var joborder = $("#no_joborder").val();

        //console.log(idjoborder+" hell "+joborder);
        setproduct(idjoborder,joborder);
        
        $("#btn-submit").html('Update');
    }

});

</script>

<input type="hidden" name="tbl" value="<?= $tbl; ?>">
<input type="hidden" name="act" value="<?= $act; ?>">
<input type="hidden" name="id" id="id" value="<?= $id; ?>">
<input type="hidden" name="id_billing" id="id_billing" value="<?= $id_billing; ?>">


<div class="row">
    <div class="alert-submit col-sm-12"></div>
    <div style="float:left;width:30%;">
        

        <div class="row">
            <?php
                echo form_builder($column);
            ?>
            <div id="detail_joborder" class="col-sm-12 mt-3"></div>
        </div>
    </div>  
    <div class="row col-sm-8" style="width: 70%; box-sizing: border-box; float: left;">


            <div class="col-sm-12">
                <div class="row">
                    <div class="col-sm-6" id="fp_div" <?= $fp_hide; ?>>
                        
                        <!-- <div class="alert alert-success" role="alert">
                        <h5 class="alert-heading">Kode Faktur</h5>
                        <hr>
                        <p class="mb-0">01 : PPN dipungut, 07 : PPN tidak dipungut</p>
                        <p class="mb-0">010/070 : Normal, 011/071 : Pengganti</p>
                        </div> -->

                        

                        <div class="form-group">
                        <label>No Faktur</label>
                            <div class="row">
                                <?php
                                if(!empty($row->no_fp)) {
                                    $fp = explode(".",$row->no_fp);
                                    $fp1 = $fp[0];
                                    $fp2 = $fp[1].'.'.$fp[2];
                                }

                                ?>
                                <input type="text" name="no_fp" readonly id="no_fp1" value="<?= $fp1; ?>" class="form-control col-2"> . 
                                
                                <input type="text" name="no_fp2" id="no_fp2" value="<?= $fp2; ?>" readonly class="form-control col-4" />

                                <div class="col-3">
                                    
                                    <i class="fa fa-pen pointer" onclick="add_kode_fp()"></i>

                                    <i class="fa fa-magic pointer" onclick="add_status_fp()"></i>

                                    <i class="fa fa-paper-plane pointer" onclick="generate_no_fp()"></i>

                                    <i class="fa fa-trash pointer" onclick="clear_fp()" id="btn-clear-nofp"></i>
                                </div>

                            </div>
                        </div>

                    </div>

                    <div class="col-sm-3" >
                        <div class="form-group">
                        <label>Termin</label>
                        <input type="number" value="<?= $termin; ?>" name="termin" class="form-control">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                <label>Detail Tagihan</label>
                <div class="col-sm-6 mb-3">
                    <button type="button" class="btn btn-sm btn-primary" onclick="tambah_tagihan(1);">Tambah</button>&nbsp;
                    <button type="button" class="btn btn-sm btn-primary" onclick="generate_tagihan(1);" <?= $generate_hide; ?> id="generate">Generate</button>&nbsp;
                    <button type="button" class="btn btn-sm btn-primary" onclick="generate_materai(1);">Materai</button>

                </div>
                <table class="table table-bordered table-hover " id="tbl1">
                    <thead>
                        <tr>
                        <th>Kd</th>
                        <th>Jenis Tagihan</th>
                        <th>Jumlah</th>
                        <th>Action</th>
                        </tr>
                        
                    </thead>
                    <tbody id="tbl_tagihan">
                        
                        <!--
                        <div id="tagihan_list"></div>
                        !-->
                        <?php 
                            if(!empty($row->id)) {
                                $detail = $this->db->query("SELECT * FROM tb_detail_billing WHERE id_billing = $row->id")->result();
                                $no = 0;
                                if(!empty($detail)) {        
                                    foreach($detail as $d) {
                                        $no++;
                                        $opt2 = $this->billing->akun_all();
                                        $opt_jenis = option_builder($opt2,$d->kd,0);
                                        $harga = harga_view($d->jumlah);
                                        $kd = $d->kd;
                                        $id_detail = $d->id;
                                        //echo "<tr><td><input type='text' name='20f[]'></td><td></td></tr>";
                                        /*
                                        echo "<tr>
                                        <td style='width:15%;'><input type='hidden' name='kode[]' value='$kd' class='col-12' id='kode_$no' readonly></td>
                                        <input type='hidden' name='id_detail[]' value='$d->id'>
                                        <td><select onchange='getkode(this.value,".'"'.$no.'"'.");' class='col-12 select2bs' id='jenis_tagihan_$no'>$opt_jenis</select>
                                        <input type='hidden' id='jenis_tagihan2_$no' value='$d->jenis_tagihan' name='jenis_tagihan[]'></td>
                                        <td><input type='text' name='jumlah[]' value='$harga' class='col-12 jumlah' id='jumlah_$no' onkeyup='change_format(this.value,".'"'.$no.'"'.");'></td>
                                        <td><button class='btn btn-default' type='button' id='HapusBarisupdate' onclick='hapusdetailtagihan($id_detail)'><i class='fa fa-times' style='color:red;'></i></button></td>
                                        </tr>";
                                        */

                                        echo "<tr>
                                        <td style='width:15%;'>$kd<input type='hidden' name='kode[]' value='$kd' class='col-12' id='kode_$no' readonly></td>
                                        <input type='hidden' name='id_detail[]' value='$d->id'>
                                        <td>$d->jenis_tagihan
                                        <input type='hidden' id='jenis_tagihan2_$no' value='$d->jenis_tagihan' name='jenis_tagihan[]'></td>
                                        <td>$harga<input type='hidden' name='jumlah[]' value='$harga' class='col-12 jumlah' id='jumlah_$no' onkeyup='change_format(this.value,".'"'.$no.'"'.");'></td>
                                        <td><button type='button' class='btn btn-sm btn-primary' onclick='view_edit_tagihan($id_detail);'>Edit</button>&nbsp<button class='btn btn-default' type='button' onclick='hapusdetailtagihan($id_detail)'><i class='fa fa-times' style='color:red;'></i></button></td>
                                        </tr>";
                                    }
                                }
                            }


                        ?>
                    </tbody>
                    <tfoot>
                        <tr><td colspan="2">Total : </td><td><input type='text' readonly id="total_price" value="<?php echo harga_view($row->total_tagihan); ?>" name="total_tagihan"></td><td></td></tr>
                    </tfoot>
                </table>

                </div>

                <div class="col-sm-3 mb-3">
                <label></label>
                </div>
                <div class="col-sm-5">
                    <input type="hidden" name="iduser" id="iduser" value="<?php echo $this->session->nama_lengkap;?>" readonly class="form-control">
                </div>

            </div>
            
        </div>


</div>