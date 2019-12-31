<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Cms extends CI_Controller {	 
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('is_admin_login')) {
            redirect(config_item('admin_base_url').'home');
        }
        $this->load->model(config_item('admin_directory').'/cms_model');
    }
    public function view() {
        $data['MetaTitle'] = $this->lang->line('titleadmin_cms').' | '.$this->lang->line('site_title');

        if($this->input->post('SubmitCMS') == "Submit")
        {
                $cmsCount = count($_POST['cms_id']);
                $cmsData = array();
                for ($nCount = 0; $nCount < $cmsCount; $nCount++) 
                {
                      $cmsData[] = array(
                          'cms_id'  => $_POST['cms_id'][$nCount],
                          'cms_contents'  => $_POST['cms_contents'][$nCount]
                      );
                }
                $this->cms_model->upateSystemOption($cmsData);
                $this->session->set_flashdata('cmsMSG', $this->lang->line('success_update'));
        }
        $data['cmsList'] = $this->cms_model->cmsList();
        $this->load->view(config_item('admin_directory').'/cms',$data);
    }
}