<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class System_option extends CI_Controller {	 
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect(config_item('admin_base_url').'home');
        }
        $this->load->model(config_item('admin_directory').'/systemoption_model');
    }
    public function view() {
        $data['MetaTitle'] = $this->lang->line('titleadmin_systemoptions').' | '.$this->lang->line('site_title');

        if($this->input->post('SubmitSystemSetting') == "Submit")
        {
                $systemOptionCount = count($_POST['option_value']);
                $systemOptionData = array();
                for ($nCount = 0; $nCount < $systemOptionCount; $nCount++) 
                {
                      $systemOptionData[] = array(
                          'system_option_id'  => $_POST['system_option_id'][$nCount],
                          'option_value'  => $_POST['option_value'][$nCount]
                      );
                }
                $this->systemoption_model->upateSystemOption($systemOptionData);
                $this->session->set_flashdata('SystemOptionMSG', $this->lang->line('system_option_save'));
        }
        $data['SystemOptionList'] = $this->systemoption_model->getSystemOptionList();
        $this->load->view(config_item('admin_directory').'/system_option',$data);
    }
}