<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Myprofile extends CI_Controller {    	
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect(config_item('admin_base_url').'home');
        }
        $this->load->helper('string');
        $this->load->library('form_validation');
        $this->load->model(config_item('admin_directory').'/myprofile_model');
    }        
    public function getUserProfile() {
        $data['MetaTitle'] = $this->lang->line('title_admin_myprofile').' | '.$this->lang->line('site_title');
        if($this->input->post('submitEditUser') == "Submit")
        {   
          $this->form_validation->set_rules('first_name', 'First Name', 'trim|required');
          $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required');                        
          $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');            
          //check form validation using codeigniter
          if ($this->form_validation->run())
          {
              $updateUserData = array(                  
                'first_name' =>$this->input->post('first_name'),
                'last_name' =>$this->input->post('last_name'),                  
                'email' =>$this->input->post('email'),                  
                'updated_date'=>date('Y-m-d H:i:s')
              );                                 
              $this->myprofile_model->updateUserModel($updateUserData,$this->input->post('user_id'));                 
              $this->session->set_flashdata('myProfileMSG', $this->lang->line('success_update'));                  
              redirect(config_item('admin_base_url')."myprofile/getUserProfile");                  
          }            
        }
        if($this->input->post('ChangePassword') == "Submit")
        {
            $data['selected_tab'] = "ChangePassword";
            $this->form_validation->set_rules('Newpass', 'New Password', 'trim|required|min_length[8]');
            $this->form_validation->set_rules('confirmPass', 'Confirm Password', 'trim|required|min_length[8]|matches[Newpass]');
            //check form validation using codeigniter
            if ($this->form_validation->run())
            {  
              $salt = '5&JDDlwz%Rwh!t2Yg-Igae@QxPzFTSId';
              $newEncryptPass  = md5($salt.$this->input->post('Newpass'));
              $updateUserPassData = array(
                'password' =>$newEncryptPass,
                'updated_date'=>date('Y-m-d H:i:s')
              );
              $this->myprofile_model->updateUserModel($updateUserPassData,$this->input->post('user_id'));                
              $this->session->set_flashdata('myProfileMSG', $this->lang->line('success_update'));
              redirect(config_item('admin_base_url')."myprofile/getUserProfile"); 
            }
        }        
        $user_id = ($this->session->userdata("adminID"))?$this->session->userdata("adminID"):$this->input->post('user_id');                        
        $data['editUserDetail'] = $this->myprofile_model->getEditUserDetail($user_id);
        $this->load->view(config_item('admin_directory').'/myprofile_edit',$data);
    }
    public function checkEmailExist()
    { 
      $chkEmail = $this->myprofile_model->CheckExists($this->input->post('email'),$this->input->post('user_id'));
      echo $chkEmail;
    }            
}