<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>OverUnderz - Reset your password</title>	
	<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />	
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
	<link href="<?php echo base_url()?>assets/style.css" rel="stylesheet" id="bootstrap-css">
</head>

<body>
    <div class="image-container set-full-height">
         <div class="logo-container">
            <div class="logo">
                <img src="<?php echo base_url()?>assets/backend/img/header_logo.png">
            </div>
        </div>
	    <!--   Big container   -->
	    <div class="container">
	        <div class="row">
		        <div class="col-sm-8 col-sm-offset-2">
		            <!-- Wizard container -->
		            <div class="wizard-container">		            	
		                <div class="card wizard-card content" data-color="red" id="wizard" style="padding: 10%;">
	                    	<form id="reset_form" class="login-form" action="" method="post">
					        <h3 class="form-title">Reset your password</h3>
					        <?php 
                            if($this->session->flashdata('success'))
                            {?>
                                <div class="alert alert-success">
                                    <strong>Success!</strong> <?php echo $this->session->flashdata('success');?>
                                </div>
                            <?php } ?>                            
					        <div class="form-group">
					            <label class="control-label visible-ie8 visible-ie9">Password</label>
					            <div class="input-icon">
					                <i class="fa fa-lock"></i>
					                <input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Password" name="password" id="password" value="">
					            </div>
					        </div>
					        <div class="form-group">
					            <label class="control-label visible-ie8 visible-ie9">Confirm Password</label>
					            <div class="input-icon">
					                <i class="fa fa-lock"></i>
					                <input class="form-control placeholder-no-fix" type="password" autocomplete="off" placeholder="Password" name="repassword" id="repassword" value="">
					            </div>
					        </div>
					        <div class="form-group">					            
					            <input type="submit" class="btn btn-danger pull-right" name="submit" value="Submit">
					        </div>
					    </form>
		                </div>
		            </div> <!-- wizard container -->
		        </div>
	    	</div> <!-- row -->
		</div> <!--  big container -->
	    <div class="footer">
	        <div class="container text-center">	             
			<?php echo date('Y');?> &copy; OverUnderz <i class="fa fa-heart heart"></i>
	        </div>
	    </div>
	</div>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
	<script type="text/javascript">
		$( "#reset_form" ).validate({
		  rules: {
		    password: "required",
		    repassword: {
		      equalTo: "#password"
		    }
		  }
		});
	</script>
</body>
</html>
