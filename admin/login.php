<?php require_once('../config.php') ?>
<!DOCTYPE html>
<html lang="en" class="" style="height: auto;">
 <?php require_once('inc/header.php') ?>
<body class="hold-transition login-page">             
  <script>
    start_loader()
  </script>
  <style>
    body{
      background-image: url("cover1.jpg");
      background-size:cover;
      background-repeat:no-repeat;
      backdrop-filter: contrast(1);
    }
    #page-title{
      font-size: 3.5em;
      color: #343a40 !important;
    }
  </style>
  <h1 class="text-center text-white px-4 py-5" id="page-title"><b>ZFS Inventory Management System</b></h1>
<div class="login-box">
  <!-- /.login-logo -->
  <div class="card card-teal my-2">
    <div class="card-body">
      <p class="login-box-msg">Please enter your credentials</p>
      <form id="login-frm" action="" method="post">
        <div class="input-group mb-3">
          <input type="text" class="form-control" name="username" autofocus placeholder="Username">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control"  name="password" placeholder="Password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block" align="center">Sign In</button>
          </div>
          <div class="col-4">
              <a href="forgot-password.html">I forgot my password</a>
          </div>
          <!-- /.col -->
      </div>
      </form>
      <!-- /.social-auth-links -->

      
      
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="<?= base_url ?>plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?= base_url ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?= base_url ?>dist/js/adminlte.min.js"></script>

<script>
  $(document).ready(function(){
    end_loader();
  })
</script>
</body>
</html>