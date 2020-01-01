<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class User_event extends CI_Controller { 
    public $module_name = "User Event";
    public $controller_name = "user_event";
    public $table_name = "user_screen_log";
    public $list_viewfile = "user_event";

    public function __construct() {
        parent::__construct();

        date_default_timezone_set('UTC');
        $this->load->library('form_validation');
        $this->load->model(config_item('admin_directory').'/user_event_model');        
    }
    public function view() {
        if (!$this->session->userdata('is_admin_login')) {
            redirect(config_item('admin_directory').'/home');
        }
        $question_id = ($this->uri->segment('4'))?$this->uri->segment('4'):$this->input->post('question_id');
        $data['userDetail'] = $this->common_model->getSingleRow('user_master','user_id',$this->encryption->decrypt(str_replace(array('-', '_', '~'), array('+', '/', '='),$this->uri->segment('4'))));

        $data['MetaTitle'] = $this->lang->line('title_admin_user_event').' | '.$this->lang->line('site_title');        
        $this->load->view(config_item('admin_directory').'/'.$this->list_viewfile,$data);
    }
    
    public function ajaxview() {
        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';
        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';
        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';
        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';
        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';
        
        $sortfields = array(1=>'api_name',2=>'created_date',3=>'user_screen_log_id');
        $sortFieldName = '';
        if(array_key_exists($sortCol, $sortfields))
        {
            $sortFieldName = $sortfields[$sortCol];
        }
        $user_id = $this->uri->segment("4");
        //Get Recored from model
        $UserEventData = $this->user_event_model->getUserEventList($user_id,$sortFieldName,$sortOrder,$displayStart,$displayLength);
        //echo "<pre> ";print_r($UserEventData);exit;
        $totalRecords = $UserEventData['total'];        
        $records = array();
        $records["aaData"] = array(); 
        $nCount = ($displayStart != '')?$displayStart+1:1;
        $getScreenName = getScreenName();

        $user_local_timezone = ($this->session->userdata('user_local_timezone'))?$this->session->userdata('user_local_timezone'):'America/New_York';                

        foreach ($UserEventData['data'] as $key => $userEventDetails) {
            $created_date = 'N/A';
            $use_date = 'N/A';
            if($userEventDetails->created_date)
            {
                $dt = new DateTime($userEventDetails->created_date, new DateTimeZone('UTC'));
                $dt->setTimezone(new DateTimeZone($user_local_timezone));
                $created_date = $dt->format('M d, Y h:i A');
            }

            if($userEventDetails->use_date)
            {
                $dt = new DateTime(date("Y-m-d",strtotime($userEventDetails->use_date))." ".date("H:i:s"), new DateTimeZone('UTC'));
                $dt->setTimezone(new DateTimeZone($user_local_timezone));
                $use_date = $dt->format('M d, Y');
            }

            $records["aaData"][] = array(
                $nCount,
                $getScreenName[$userEventDetails->api_name],                
                $created_date,
                $use_date,
                ''                
            );
            $nCount++;
        }        
        $records["sEcho"] = $sEcho;
        $records["iTotalRecords"] = $totalRecords;
        $records["iTotalDisplayRecords"] = $totalRecords;
        echo json_encode($records);
    }   
}