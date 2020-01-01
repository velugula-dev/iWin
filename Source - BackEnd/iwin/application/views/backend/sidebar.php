<div class="page-sidebar-wrapper">
    <div class="page-sidebar navbar-collapse collapse">        
        <ul class="page-sidebar-menu" data-auto-scroll="false" data-auto-speed="200">            
            <li class="sidebar-toggler-wrapper">                
                <div class="sidebar-toggler">
                </div>                
            </li>
            <li>&nbsp;</li>
            <li class="start <?php echo ($this->uri->segment(2)=='dashboard')?"active":""; ?>">
                <a href="<?php echo config_item('admin_base_url');?>dashboard">
                    <i class="fa fa-dashboard"></i>
                    <span class="title">Dashboard</span>
                    <span class="selected"></span>
                </a>
            </li>
            <li class="start <?php echo ($this->uri->segment(2)=='notification')?"active":""; ?>">
                <a href="<?php echo config_item('admin_base_url');?>notification/view">
                    <i class="fa fa-users"></i>
                    <span class="title">Notification Management</span>
                    <span class="selected"></span>
                </a>
            </li>
            <li class="start <?php echo ($this->uri->segment(2)=='feedback')?"active":""; ?>">
                <a href="<?php echo config_item('admin_base_url');?>feedback/view">
                    <i class="fa fa-users"></i>
                    <span class="title">Feedback Management</span>
                    <span class="selected"></span>
                </a>
            </li>
            <li class="start <?php echo ($this->uri->segment(2)=='user')?"active":""; ?>">
                <a href="<?php echo config_item('admin_base_url');?>user/view">
                    <i class="fa fa-users"></i>
                    <span class="title">Users Management</span>
                    <span class="selected"></span>
                </a>
            </li>
            <li class="start <?php echo ($this->uri->segment(2)=='question')?"active":""; ?>">
                <a href="<?php echo config_item('admin_base_url');?>question/view">
                    <i class="fa fa-question"></i>
                    <span class="title">Questions Management</span>
                    <span class="selected"></span>
                </a>
            </li>
            <li class="start <?php echo ($this->uri->segment(2)=='second_phase')?"active":""; ?>">
                <a href="<?php echo config_item('admin_base_url');?>second_phase">
                    <i class="fa fa-file-text"></i>
                    <span class="title">Advertisement</span>
                    <span class="selected"></span>
                </a>
            </li>
            <li class="start <?php echo ($this->uri->segment(2)=='second_phase')?"active":""; ?>">
                <a href="<?php echo config_item('admin_base_url');?>second_phase">
                    <i class="fa fa-pencil-square-o"></i>
                    <span class="title">Quiz History</span>
                    <span class="selected"></span>
                </a>
            </li>
            <li class="start <?php echo ($this->uri->segment(2)=='second_phase')?"active":""; ?>">
                <a href="<?php echo config_item('admin_base_url');?>second_phase">
                    <i class="fa fa-file"></i>
                    <span class="title">Reports</span>
                    <span class="selected"></span>
                </a>
            </li>
            <li class="start <?php echo ($this->uri->segment(2)=='cms')?"active":""; ?>">
                <a href="<?php echo config_item('admin_base_url');?>cms/view">
                    <i class="fa fa-pencil-square-o"></i>
                    <span class="title">CMS</span>
                    <span class="selected"></span>
                </a>
            </li>            
            <li class="start <?php echo ($this->uri->segment(2)=='system_option')?"active":""; ?>">
                <a href="<?php echo config_item('admin_base_url');?>system_option/view">
                    <i class="fa fa-pencil-square-o"></i>
                    <span class="title">System Option</span>
                    <span class="selected"></span>
                </a>
            </li>            
        </ul>        
    </div>
</div>