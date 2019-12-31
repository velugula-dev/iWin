<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
    public function __construct() 
    {    	
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('form_validation');
        $this->load->model('user_model');
    }
	public function index()
	{
		redirect('home');
        exit;
    }        
    public function activate()
	{
		if($this->uri->segment('3') != "")
		{
			$this->user_model->updateData($this->uri->segment('3'));
		}
		$this->load->view('user_activate',$data);
	}
	public function reset()
	{
        $getData = $this->user_model->check_verfication_code($this->uri->segment('3'));
		if(!empty($getData))
        {
            if($this->input->post('submit') == "Submit")
            {           
                $this->form_validation->set_rules('password', 'Password', 'trim|required');
                //check form validation using codeigniter
                if ($this->form_validation->run())
                {
                    $salt = '5&JDDlwz%Rwh!t2Yg-Igae@QxPzFTSId';
                    $passUser = array(                    
                        'password'=>md5($salt.$this->input->post('password')),
                        'verification_code' => ""
                    );
                    $this->session->set_flashdata('success', "Password updated successfully");
                    $this->user_model->resetPassword($this->uri->segment('3'),$passUser);
                }
            }
            $this->load->view('user_reset',$data);            
        }
        else
        {
            $this->load->view('user_activate_fail');            
        }
	}	
}