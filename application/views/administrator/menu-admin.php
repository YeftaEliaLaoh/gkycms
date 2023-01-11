<?php
$uri = $this->uri->segment(1);
/*
$c = getUser($this->session->id);


if(!empty($c->avatar)) {
  $avatar = $c->avatar;
}
$name = $c->nama_lengkap;
*/
$avatar = "default.jpg";
$level = $this->session->level;
$name = "Admin";
?>
<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <!-- Add icons to the links using the .nav-icon class
          with font-awesome or any other icon font library -->
  
    <li class="nav-item">
      <a href="<?= base_url('home'); ?>" class="nav-link <?php if($uri == 'home') { echo 'active'; } ?>">
        <i class="nav-icon fas fa-home"></i>
        <p>
          Home
        </p>
      </a>
    </li>
    
    <li class="nav-item">
      <a href="<?= base_url('admin'); ?>" class="nav-link <?php if($uri == 'admin') { echo 'active'; } ?>">
        <i class="nav-icon fas fa-user"></i>
        <p>
          Admin
        </p>
      </a>
    </li>
    
    <li class="nav-item has-treeview <?php if($uri == 'master') { echo 'menu-open'; } ?>">
      <a href="#" class="nav-link <?php if($uri == 'master') { echo 'active'; } ?>">
        <i class="nav-icon fas fa-database"></i>
        <p>
          Master 
          <i class="fas fa-angle-left right"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <!--
        <li class="nav-item">
          <a href="<?= base_url("master/parentkategori"); ?>" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Parent Kategori</p>
          </a>
        </li>
        !-->
        

        <li class="nav-item">
          <a href="<?= base_url("master/jemaat"); ?>" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Jemaat</p>
          </a>
        </li>

        
        <li class="nav-item has-treeview <?php if($uri == 'master') { echo 'menu-open'; } ?>" onclick='location.href="<?=  base_url("master/book"); ?>"'>
          <a href="<?= base_url("master/book"); ?>" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Judul</p>
          </a>
          <ul class="nav nav-treeview">
            
            <li class="nav-item">
              <a href="<?= base_url("master/items"); ?>" class="nav-link">
                <i class="far nav-icon">></i>
                <p>Item</p>
              </a>
            </li>

          </ul>
        </li>

        <li class="nav-item">
          <a href="<?= base_url("master/author"); ?>" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Author</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="<?= base_url("master/publisher"); ?>" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Publisher</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="<?= base_url("master/bahasa"); ?>" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Bahasa</p>
          </a>
        </li>
        
       <li class="nav-item">
          <a href="<?= base_url("master/kategori"); ?>" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Kategori</p>
          </a>
        </li>
        
<!--
        <li class="nav-item">
          <a href="<?= base_url("master/book"); ?>" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Buku</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= base_url("master/cd"); ?>" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>CD</p>
          </a>
        </li>
!-->

      </ul>
    </li>

    
    <li class="nav-item has-treeview <?php if($uri == 'member') { echo 'menu-open'; } ?>">
      <a href="#" class="nav-link <?php if($uri == 'member') { echo 'active'; } ?>">
        <i class="nav-icon fas fa-user-cog"></i>
        <p>
          Member
          <i class="fas fa-angle-left right"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a href="<?= base_url("member/data"); ?>" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Data Member</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= base_url("member/aktivasi"); ?>" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Aktivasi Member</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="<?= base_url("member/extend"); ?>" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Extend Member</p>
          </a>
        </li>


      </ul>
    </li>
    
    
    <li class="nav-item has-treeview <?php if($uri == 'transaksi') { echo 'menu-open'; } ?>">
      <a href="#" class="nav-link <?php if($uri == 'transaksi') { echo 'active'; } ?>">
        <i class="nav-icon fas fa-inbox"></i>
        <p>
          Transaksi
          <i class="fas fa-angle-left right"></i>
        </p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a href="<?= base_url("transaksi/pinjam/approve"); ?>" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Peminjaman Approval</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= base_url("transaksi/pinjam"); ?>" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Peminjaman</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="<?= base_url("transaksi/kembali"); ?>" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Pengembalian</p>
          </a>
        </li>


      </ul>
    </li>
    
    <li class="nav-item">
      <a href="<?= base_url('settings'); ?>" class="nav-link <?php if($uri == 'settings') { echo 'active'; } ?>">
        <i class="nav-icon fas fa-cog"></i>
        <p>
          Settings
        </p>
      </a>
    </li>


    <li class="nav-item">
      <a href="<?= base_url('administrator/logout'); ?>" class="nav-link">
        <i class="nav-icon fas fa-sign-out-alt"></i>
        <p>
          Logout
        </p>
      </a>
    </li>
