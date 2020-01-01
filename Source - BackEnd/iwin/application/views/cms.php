<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title><?php echo ($cmsData->cms_title)?$cmsData->cms_title." | ":"";?>OverUnderz</title>	
	<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />	
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
	<link href="<?php echo base_url()?>assets/style.css" rel="stylesheet" id="bootstrap-css">
	<link rel="shortcut icon" href="<?php echo config_item('admin_assets_url');?>img/favicon.png"/>
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
		        <div class="col-sm-12 col-md-12">
		            <!-- Wizard container -->
		            <div class="wizard-container">
		                <div class="card wizard-card" data-color="red" id="wizard" style="padding: 0 20px 20px;">
		                	<?php
		                	if(!empty($cmsData))
		                	{?>
		                    	<div class="wizard-header">
		                        	<h3 class="wizard-title">
		                        		<?php echo $cmsData->cms_title;?>
		                        	</h3>									
		                    	</div>
		                    	<?php echo $cmsData->cms_contents;		                		
		                	}
		                	else
		                	{?>
		                    	<div class="wizard-header">
		                        	<h3 class="wizard-title">
		                        		No Record found
		                        	</h3>									
		                    	</div>
		                	<?php }?>
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
</body>

</html>
