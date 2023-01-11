<style>
.label-info {
    background-color: #5bc0de;
}
.label {
    display: inline;
    padding: .2em .6em .3em;
    font-size: 75%;
    font-weight: 700;
    line-height: 1;
    color: #fff;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: .25em;
}

.bootstrap-tagsinput {
  width: 100% !important;
}
</style>


    <div class="row">
        <?php
            //echo form_builder($column);
            echo $html;
        ?>
        <!--
        <div class="col-sm-6" id="alamatkirim">

          <label style="text-decoration:underline;">Alamat Pengiriman</label><br>
          <div class="row">

              <div class="col-sm-3 mb-3">
                <label>Nama PT</label>
              </div>
              <div class="col-sm-9">
                <input type="text" name="" id="ap_nama_pt" readonly value="<?= $row->ap_nama_pt; ?>" class="form-control">
              </div>

              <div class="col-sm-3 mb-3">
                <label>Contact Person</label>
              </div>
              <div class="col-sm-9">
                <input type="text" name="" readonly id="ap_contact_person" value="<?= $row->ap_contact_person; ?>" class="form-control">
              </div>

              <div class="col-sm-3 mb-3">
                <label>Alamat</label>
              </div>
              <div class="col-sm-9">
                <textarea class="form-control" name="" readonly id="ap_alamat"><?= $row->ap_alamat; ?></textarea>
              </div>

          </div>
        </div>
        !-->

        <div class="col-sm-6" id="petikemas" style="<?= $petikemas_display; ?>">

          <label style="text-decoration:underline;">Identitas Peti Kemas</label><br>
          <div class="row">

            <div class="col-sm-6" >
                <div class="form-group">
                
                <table class="table table-bordered table-hover col-sm-3">
                  <thead><tr><td colspan="2">20 f</td></thead>
                  <tbody>
                    <?php 
                    if(empty($row->identitas_peti20f)) {
                      //echo "<tr><td><input type='text' name='20f[]'></td><td></td></tr>";
                    } else {
                      $identitas_20f = explode(",",$row->identitas_peti20f);
                      foreach($identitas_20f as $f2) {
                        echo "<tr><td><input type='text' readonly name='' value='$f2'></td><td><button class='btn btn-default'><i class='fa fa-times' style='color:red;'></i></button></td></tr>";
                      }
                    }
                    ?>
                      
                  </tbody>
                  
                </table>
                

                </div>
            </div>
            
            

            <div class="col-sm-6" >
                <div class="form-group">
                
                <table class="table table-bordered table-hover col-sm-3">
                  <thead><tr><td>40 f</td></thead>
                  <tbody>
                  <?php 
                    if(empty($row->identitas_peti40f)) {
                     // echo "<tr><td><input type='text' name='40f[]'></td><td></td></tr>";
                    } else {
                      $identitas_40f = explode(",",$row->identitas_peti40f);
                      foreach($identitas_40f as $f4) {
                        echo "<tr><td><input type='text' readonly name='' value='$f4'></td><td><button class='btn btn-default'><i class='fa fa-times' style='color:red;'></i></button></td></tr>";
                      }
                    }
                    ?>
                  </tbody>
                </table>

                </div>
            </div>

          </div>
        </div>

        <div class="col-sm-12">
            <div class="form-group">
            <label style="text-decoration:underline;">Kemasan</label>
            <div class="row">
                <div class="col-sm-3 mb-3">
                  <label>Jenis</label>
                </div>
                <div class="col-sm-9 row">
                  <input type="number" readonly value="<?= $row->berat_satuan; ?>" readonly name="" class="form-control col-sm-6">&nbsp;
                  <select name="" readonly class="form-control col-sm-5">
                    <?php
                    echo $opt_petikemas;
                    ?>
                  </select>
                </div>

                <div class="col-sm-3 mb-3">
                  <label>Gross Weight</label>
                </div>
                <div class="col-sm-9 row">
                  <input type="text" name="" readonly value="<?= $row->gross_wg; ?>" class="form-control col-sm-8">&nbsp;Kg
                </div>

                <div class="col-sm-3">
                  <label>Chargeable</label>
                </div>
                <div class="col-sm-9 row">
                  <input type="text" name="" readonly value="<?= $row->chargable; ?>" class="form-control col-sm-6">&nbsp;Kg
                </div>

            </div>
           
            </div>
        </div>

        <div class="col-sm-12">
            <div class="form-group">
            <label style="text-decoration:underline;">Biaya</label>
            <div class="row">
                <div class="col-sm-3 mb-3">
                  <label>CIF</label>
                </div>
                <div class="col-sm-9">
                  <input type="text" name="" id="cif" readonly  value="<?= harga_view($row->cif); ?>" class="form-control">
                </div>

                <div class="col-sm-3 mb-3">
                  <label>Kurs</label>
                </div>
                <div class="col-sm-9">
                  <input type="text" name="" id="kurs" readonly value="<?= harga_view($row->kurs); ?>" class="form-control">
                </div>

                <div class="col-sm-3 mb-3">
                  <label>Jumlah</label>
                </div>
                <div class="col-sm-9">
                  <input type="text" name="" readonly id="jumlah_biaya" value="<?= harga_view($row->jumlah_biaya); ?>" readonly class="form-control">
                </div>

            </div>
           
            </div>
        </div>

        <div class="col-sm-12">
            <div class="form-group">
            <label style="text-decoration:underline;">Pungutan</label>
            <div class="row">
                <div class="col-sm-3 mb-3">
                  <label>BM</label>
                </div>
                <div class="col-sm-9">
                  <input type="text" name="" id="bm" readonly value="<?= harga_view($row->bm); ?>" class="form-control">
                </div>

                <div class="col-sm-3 mb-3">
                  <label>Cukai</label>
                </div>
                <div class="col-sm-9">
                  <input type="text" name="" id="cukai" readonly value="<?= harga_view($row->cukai); ?>" class="form-control">
                </div>

                <div class="col-sm-3 mb-3">
                  <label>PPN</label>
                </div>
                <div class="col-sm-9">
                  <input type="text" name="" id="ppn" readonly value="<?= harga_view($row->ppn); ?>" class="form-control">
                </div>

                <div class="col-sm-3 mb-3">
                  <label>PPN Bm</label>
                </div>
                <div class="col-sm-9">
                  <input type="text" name="" id="ppn_bm" readonly value="<?= harga_view($row->ppn_bm); ?>" class="form-control">
                </div>

                <div class="col-sm-3 mb-3">
                  <label>Pph</label>
                </div>
                <div class="col-sm-9">
                  <input type="text" name="" id="pph" readonly value="<?= harga_view($row->pph); ?>" class="form-control">
                </div>

                <div class="col-sm-3 mb-3">
                  <label>Jumlah</label>
                </div>
                <div class="col-sm-9">
                  <input type="text" name="" readonly value="<?= harga_view($row->jumlah_pungutan); ?>" id="jumlah_pungutan" readonly class="form-control">
                </div>

            </div>
           
            </div>
        </div>

    </div>
    