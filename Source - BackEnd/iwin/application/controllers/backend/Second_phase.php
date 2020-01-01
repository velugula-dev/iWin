<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Second_phase extends CI_Controller {    
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect(config_item('admin_directory').'/home');
        }
    }
    public function index() {
        $arr['MetaTitle'] = $this->lang->line('title_admin_dashboard').' | '.$this->lang->line('site_title');        
        $this->load->view(config_item('admin_directory').'/second_phase',$arr);
    }
}