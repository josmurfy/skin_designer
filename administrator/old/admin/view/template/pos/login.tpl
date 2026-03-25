<?php echo $header; ?>
<link type="text/css" href="view/stylesheet/pos.css" rel="stylesheet" media="screen" />
<body class="back">
<!-- header start here-->
	<header>
		<!-- header container start here-->
		<div class="container">
			<div class="row">
				<div class="col-sm-12 col-md-12 col-xs-12">
					<!-- logo start here-->
					<div id="logo">
						<a href="#">
							<img src="../image/catalog/poslogo.png" class="img-responsive" alt="logo" title="logo" />
						</a>
					</div>
					<!-- logo end here-->
				</div>
			</div>
		</div>
		<!-- header container end here -->
	</header>
<!-- header end here -->

<div id="log-in">
<div id="content">
  <div class="container-fluid"><br />
    <br />
    <div class="row">
      <div class="col-sm-offset-3 col-sm-6">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1 class="panel-title"><?php echo $text_login; ?></h1>
          </div>
          <div class="panel-body">
            <?php if ($success) { ?>
            <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
              <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            <?php } ?>
            <?php if ($error_warning) { ?>
            <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
              <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            <?php } ?>
            <form action="<?php echo $posaction; ?>" method="post" enctype="multipart/form-data">
              <div class="form-group">
               
                <div class="input-group">
				<span class="input-group-addon"><i class="fa fa-user"></i></span>
                  <input type="text" name="username" value="<?php echo $username; ?>" placeholder="<?php echo $entry_username; ?>" id="input-username" class="form-control" />
                </div>
              </div>
              <div class="form-group">
               
                <div class="input-group"><span class="input-group-addon"><i class="fa fa-lock"></i></span>
                  <input type="password" name="password" value="<?php echo $password; ?>" placeholder="<?php echo $entry_password; ?>" id="input-password" class="form-control" />
                  <input type="hidden" name="poslogin" value="poslogin" id="input-poslogin" class="form-control" />
                </div>
                <?php if ($forgotten) { ?>
                <span class="help-block"><a href="<?php echo $forgotten; ?>"><?php echo $text_forgotten; ?></a></span>
                <?php } ?>
              </div>
            
			<div class="col-md-12 col-sm-12 col-xs-12">
			<button type="submit" class="btn btn-primary"><i class="fa fa-sign-in"></i> </button>
			</div>
		 
              <?php if ($redirect) { ?>
              <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
              <?php } ?>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</div>
</body>