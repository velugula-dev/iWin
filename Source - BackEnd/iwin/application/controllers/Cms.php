<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cms extends CI_Controller {
    public function __construct() 
    {    	
        parent::__construct();
        $this->load->model('cms_model');
    }
	public function index()
	{
        $data['cmsData'] = $this->cms_model->get_cms_data($this->uri->segment('1'));
        $this->load->view('cms',$data);            
    }        
}