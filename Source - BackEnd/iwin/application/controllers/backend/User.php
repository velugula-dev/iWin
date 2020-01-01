<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class User extends CI_Controller { 
    public $module_name = "User Management";
    public $controller_name = "user";
    public $table_name = "user_master";
    public $list_viewfile = "user";

    public function __construct() {
        parent::__construct();

        date_default_timezone_set('UTC');
        $this->load->library('form_validation');
        $this->load->model(config_item('admin_directory').'/user_model');        
    }
    public function view() {
        if (!$this->session->userdata('is_admin_login')) {
            redirect(config_item('admin_directory').'/home');
        }
        $data['MetaTitle'] = $this->lang->line('title_admin_user_event').' | '.$this->lang->line('site_title');        
        $this->load->view(config_item('admin_directory').'/'.$this->list_viewfile,$data);
    }
    
    public function ajaxview() 
    {
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        
        $sortfields = array(1=>'first_name',2=>'email',3=>'last_login',4=>'created_date',5=>'last_login');
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        
        //Get Recored from model
        $UserData = $this->user_model->getUserList($sortFieldName,$sortOrder,$displayStart,$displayLength);
        $totalRecords = $UserData['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        $user_local_timezone = ($this->session->userdata('user_local_timezone'))?$this->session->userdata('user_local_timezone'):'America/New_York';                
        foreach ($UserData['data'] as $key => $userDetails) 
        {
            $last_login = 'N/A';
            $created_date = 'N/A';

            if($userDetails->last_login)
            {
                $dt = new DateTime($userDetails->last_login, new DateTimeZone('UTC'));
                $dt->setTimezone(new DateTimeZone($user_local_timezone));
                $last_login = $dt->format('M d, Y h:i A');

            }
            if($userDetails->created_date)
            {
                $dt = new DateTime($userDetails->created_date, new DateTimeZone('UTC'));
                $dt->setTimezone(new DateTimeZone($user_local_timezone));
                $created_date = $dt->format('M d, Y h:i A');
            }

            $records["aaData"][] = array(
                $nCount,
                $userDetails->first_name." ".$userDetails->last_name,                
                $userDetails->email,                
                $last_login,                
                $created_date,                
                '<a class="btn btn-sm danger-btn margin-bottom" href="'.config_item('admin_base_url').'user_event/view/'.str_replace(array('+', '/', '='), array('-', '_', '~'), $this->encryption->encrypt($userDetails->user_id)).'"><i class="fa fa-edit"></i> View Screens Tracking</a>'
            );
            $nCount++;
        }        
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }   
}