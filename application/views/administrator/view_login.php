<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>GKY Library</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="icon" type="image/png" sizes="16x16" href="<?= base_url("images/favicon_gsa.png"); ?>">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?= base_url("asset"); ?>/plugins/fontawesome-free/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="<?= base_url("asset"); ?>/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?= base_url("asset"); ?>/dist/css/adminlte.min.css">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
<body class="hold-transition login-page" style="background-image: url('<?php echo ("images/back_login8.jpg");?>'); height: 83vh" >
<div class="login-box">
  <div class="login-logo">
    <a href="#"><b>GKY</b> Dashboard</a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Sign in </p>
      <div class="alert-login"></div>

      <form action="#" method="post" id='form_login'>
        <div class="input-group mb-3">
          <input type="text" class="form-control" name="email" placeholder="Email" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="pass" class="form-control" placeholder="Password" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" name="remember" id="remember">
              <label for="remember">
                Remember Me
              </label>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" id="login-submit" class="btn btn-primary btn-block">Sign In</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

      <!-- /.social-auth-links -->

      <p class="mb-1">
        <!--
        <a href="forgot-password">I forgot my password</a>
        !-->
      </p>
      <!--
      <p class="mb-0">
        <a href="register.html" class="text-center">Register a new membership</a>
      </p>
      !-->
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="<?= base_url("asset"); ?>/plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?= base_url("asset"); ?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?= base_url("asset"); ?>/dist/js/adminlte.min.js"></script>

<script>

$('#form_login').submit(function(e) {
          e.preventDefault();
          var form = $(this);
          
          $("#login-submit").html('<i class="fa fa-spinner fa-spin"></i>Loading');
          $('#login-submit').attr('disabled',true);
          $.ajax({
          type:'POST',
          data:form.serialize(),
          url:"<?= base_url('api/admin/login'); ?>",
          success:function(data) {
            var message = data.message;
            if(data.status == 1) {
              location.reload();
              
            } else {
              $("#login-submit").html('Sign In');
              $('#login-submit').removeAttr('disabled');
              $(".alert-login").html('<div class="alert alert-danger alert-dismissible fade show text-center margin-bottom-1x"><span class="alert-close" data-dismiss="alert"></span><i class="icon-alert-triangle"></i>&nbsp;&nbsp;<span class="text-medium">'+message+'</div>');
            }
            
            
          }
          });
          
});
</script>

</body>
</html>
