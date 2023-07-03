<style>
  .user-img {
    position: absolute;
    height: 27px;
    width: 27px;
    object-fit: cover;
    left: -7%;
    top: -12%;
  }

  .btn-rounded {
    border-radius: 50px;
  }
</style>

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-light shadow text-sm">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="<?php echo base_url ?>" class="nav-link">ZFS Inventory Management System -
        <?php if ($_settings->userdata('type') == 1) : ?>
          Admin
        <?php elseif ($_settings->userdata('type') == 2) : ?>
          Manager
        <?php elseif ($_settings->userdata('type') == 3) : ?>
          Staff
        <?php endif; ?>
      </a>
    </li>
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
    <li class="nav-item">
      <div class="btn-group nav-link">
        <button type="button" class="btn btn-rounded badge badge-light dropdown-toggle dropdown-icon" data-toggle="dropdown">
          <span>
            <?php
            $avatar = $_settings->userdata('avatar');
            $imageData = base64_encode($avatar);
            $imageType = $_settings->userdata('avatar_type');
            $dataURI = 'data:' . $imageType . ';base64,' . $imageData;
            ?>
            <img src="<?php echo $dataURI; ?>" class="img-circle elevation-2 user-img" alt="User Image">
          </span> &nbsp;
          <span class="ml-3"><?php echo $_settings->userdata('username'); ?></span> &nbsp;
          <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div class="dropdown-menu" role="menu">
          <a class="dropdown-item" href="<?php echo base_url.'admin/?page=user' ?>">
            <span class="fa fa-user"></span> My Account
          </a>
          <div class="dropdown-divider"></div>
          <a class="dropdown-item" href="<?php echo base_url.'/classes/Login.php?f=logout' ?>">
            <span class="fas fa-sign-out-alt"></span> Logout
          </a>
        </div>
      </div>
    </li>
  </ul>
</nav>
<!-- /.navbar -->
