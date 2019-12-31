<?php $this->load->view(config_item('admin_directory').'/header');?>
<div class="page-container">
<?php $this->load->view(config_item('admin_directory').'/sidebar');?>
    <div class="page-content-wrapper">
        <div class="page-content admin-dashboard">          
            <div class="row">
                <div class="col-md-12">
                    <h3 class="page-title">Second Phase</h3>
                </div>
            </div>  
            <div class="row">
                <div class="col-md-12">
                    <h2 style="color: #066bd3;">we will do it in Second Phase</h2>
                </div>
            </div>                                        
        </div>            
    </div>
</div>
</div>
<script src="<?php echo config_item('admin_assets_url');?>scripts/metronic.js" type="text/javascript"></script>
<script src="<?php echo config_item('admin_assets_url');?>scripts/layout.js" type="text/javascript"></script>
<script>
jQuery(document).ready(function() {    
   Metronic.init();
   Layout.init(); // init layout   
});
</script>
<?php $this->load->view(config_item('admin_directory').'/footer');?>