<?php 
    echo "<div class='col-md-12'>
              <div class='box box-info'>
                <div class='box-header with-border'>
                  <h3 class='box-title'>Edit Data User</h3>
                </div>
              <div class='box-body'>";
              $attributes = array('class'=>'form-horizontal','role'=>'form');
              echo form_open_multipart('administrator/edit_manajemenuser',$attributes); 
          echo "<div class='col-md-12'>";
                  ?>
                  <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs pull-right">
                      <li ><a href="#tab_3-2" data-toggle="tab" aria-expanded="true">Change Password</a></li>  
                      <li class=""><a href="#tab_2-2" data-toggle="tab" aria-expanded="false">Change Avatar</a></li>
                      <li class="active"><a href="#tab_1-1" data-toggle="tab" aria-expanded="false">Personal Info</a></li>
            
                      
                      
                      <li class="pull-left header"><i class="fa fa-th"></i> Profile Account</li>
                    </ul>
                    <input type='hidden' name="nip" value="<?= $rows['nip'] ?>">
                    <input type='hidden' name="id" value="<?= $rows['id'] ?>">
                    <input type='hidden' name="username" value="<?= $rows['username'] ?>">
                    <div class="tab-content">
                      <div class="tab-pane active" id="tab_1-1">
                        <div class="form-group">
                          <label for="nama">Nama Lengkap</label>
                          <input type="text" name="nama" class="form-control" value="<?= $rows['nama'] ?>" id="nama" placeholder="Nama lengkap">
                        </div>
                        <div class="form-group">
                          <label for="tempat">Tempat Lahir</label>
                          <input type="text" name="tempat" class="form-control" value="<?= $rows['tempat'] ?>" id="tempat" placeholder="Nama lengkap">
                        </div>
                        <div class="form-group">
                          <label for="tgllahir">Tanggal Lahir</label>
                          <input type="text" name="tgl_lahir" class="form-control" value="<?= tgl_view($rows['tgl_lahir']) ?>" id="datepicker">
                        </div>
                        <div class="form-group">
                          <label for="ktp">No. KTP</label>
                          <input type="text" name="ktp" class="form-control" value="<?= $rows['ktp'] ?>" id="ktp">
                        </div>
                        <div class="form-group">
                          <label for="npwp">No. NPWP</label>
                          <input type="text" name="npwp" class="form-control" value="<?= $rows['npwp'] ?>" id="npwp">
                        </div>
                        <div class="form-group">
                          <label for="norek">No. Rek DKI</label>
                          <input type="text" name="no_rek" class="form-control" value="<?= $rows['no_rek'] ?>" id="norek" >
                        </div>
                        <div class="form-group">
                          <label for="alamat">Alamat</label>
                          <textarea name="alamat" class="form-control" id="alamat"><?= $rows['alamat'] ?></textarea>
                        </div>
                        <div class="form-group">
                          <label for="notelp">No. Telepon / HP</label>
                          <input type="text" name="phone" class="form-control" value="<?= $rows['phone'] ?>" id="notelp">
                        </div>

                      </div>
                      <!-- /.tab-pane -->
                      <div class="tab-pane" id="tab_2-2">
                        <div class="form-group">
                          <input type="file" name="e" id="files">
                         <? if ($rows['foto'] != ''){ echo "<i style='color:red'>Lihat Gambar Saat ini : </i><a target='_BLANK' href='".base_url()."asset/avatar/$rows[foto]'>$rows[foto]</a>"; }  ?>                         
                          <span>Upload file berekstensi .jpeg, .png, .gif</span>
                        </div>
                      </div>
                      <!-- /.tab-pane -->
                      <div class="tab-pane" id="tab_3-2">
                        <div class='alert alert-warning'><b>Username</b> tidak bisa diubah, dan Apabila password tidak diubah, dikosongkan saja...</div>
                        <div class="form-group">
                          <label for="email">Email</label>
                          <input type="email" name="email" value="<?= $rows['email'] ?>" class="form-control" id="email">
                        </div>
                        <div class="form-group">
                          <label for="npwp">Username</label>
                          <input type="text" class="form-control" value="<?= $rows['username'] ?>" id="username" readonly>
                        </div>
                        <div class="form-group">
                          <label for="password">Password</label>
                          <input type="password" name="password" class="form-control" id="password" >
                        </div>
                      </div>
                      <!-- /.tab-pane -->
                    </div>
                    <!-- /.tab-content -->
                  </div>

                  <?php
                  /*
                  <table class='table table-condensed table-bordered'>
                  <tbody>
                    <input type='hidden' name='id' value='$rows[username]'>
                    <tr><th width='120px' scope='row'>Username</th>   <td><input type='text' class='form-control' name='a' value='$rows[username]' readonly='on'></td></tr>
                    <tr><th scope='row'>Password</th>                 <td><input type='password' class='form-control' name='b'></td></tr>
                    <tr><th scope='row'>Nama Lengkap</th>             <td><input type='text' class='form-control' name='c' value='$rows[nama_lengkap]'></td></tr>
                    <tr><th scope='row'>Level</th>                   <td>"; if ($rows['level']=='admin'){ echo "<input type='radio' name='level' value='admin' checked> Admin &nbsp; <input type='radio' name='level' value='user'> User"; }else{ echo "<input type='radio' name='level' value='admin'> Admin &nbsp; <input type='radio' name='level' value='user' checked> User"; } echo "</td></tr>
                    <tr><th scope='row'>Blokir</th>                   <td>"; if ($rows['blokir']=='Y'){ echo "<input type='radio' name='h' value='Y' checked> Ya &nbsp; <input type='radio' name='h' value='N'> Tidak"; }else{ echo "<input type='radio' name='h' value='Y'> Ya &nbsp; <input type='radio' name='h' value='N' checked> Tidak"; } echo "</td></tr>
                  </tbody>
                  </table>
                  */
                  echo "
                </div>
              </div>
                <div class='box-footer'>
                    <button type='submit' name='submit' class='btn btn-info'>Update</button>
                    <a href='#' onclick='history.go(-1);'><button type='button' class='btn btn-default pull-right'>Cancel</button></a>
                    
                  </div>
            </div>";
            ?>
            