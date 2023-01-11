<?php
$uri = $this->uri->segment(1);
/*
$c = getUser($this->session->id);
$avatar = "default.jpg";
if(!empty($c->avatar)) {
  $avatar = $c->avatar;
}
$name = $c->nama_lengkap;
*/
?>
<div class="collapse navbar-collapse order-3" id="navbarCollapse">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a href="<?= base_url(); ?>" class="nav-link">Dashboard</a>
    </li>
    
    <li class="nav-item dropdown">
      <a id="dropdownSubMenu1" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link dropdown-toggle">Master</a>
      <ul aria-labelledby="dropdownSubMenu1" class="dropdown-menu border-0 shadow">
        <li class="dropdown-submenu dropdown-hover">
          <a id="dropdownSubMenu2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-item dropdown-toggle">Books</a>
          <ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow">
            <li><a href="<?= base_url("master/category"); ?>" class="dropdown-item">Books Category</a></li>
            <li><a href="<?= base_url("master/author"); ?>" class="dropdown-item">Author</a></li>
            <li><a href="<?= base_url("master/publisher"); ?>" class="dropdown-item">Publisher</a></li>
            <li><a href="<?= base_url("master/books"); ?>" class="dropdown-item">Books</a></li>
          </ul>
        </li>

        <li class="dropdown-submenu dropdown-hover">
          <a id="dropdownSubMenu2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
           class="dropdown-item dropdown-toggle">Member</a>
          <ul aria-labelledby="dropdownSubMenu2" class="dropdown-menu border-0 shadow">
            <li><a href="<?= base_url("master/kebaktian"); ?>" class="dropdown-item">Kebaktian</a></li>
            <li><a href="<?= base_url("master/kelas"); ?>" class="dropdown-item">Kelas</a></li>
            <li><a href="<?= base_url("master/membertype"); ?>" class="dropdown-item">Member Type</a></li>
            <li><a href="<?= base_url("master/membertype"); ?>" class="dropdown-item">List Member</a></li>
          </ul>
        </li>

      </ul>
    </li>


    <li class="nav-item">
      <a href="<?= base_url("administrator/logout"); ?>" class="nav-link">Logout</a>
    </li>
    
  </ul>

  <!-- <span class="navbar-text">
        Hallo
  </span> -->

</div>