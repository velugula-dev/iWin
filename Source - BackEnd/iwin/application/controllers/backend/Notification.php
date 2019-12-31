<?php

if (!defined('BASEPATH'))

    exit('No direct script access allowed');

class Notification extends CI_Controller { 

    public $module_name = "Notification";

    public $controller_name = "notification";

    public $table_name = "notifications";

    public $addedit_viewfile = "notification_add";

    public $list_viewfile = "notification";



    public function __construct() {

        parent::__construct();



        $this->load->library('form_validation');

        $this->load->model(config_item('admin_directory').'/notification_model');        

    }

    public function view() {

        if (!$this->session->userdata('is_admin_login')) {

            redirect(config_item('admin_directory').'/home');

        }

        $data['MetaTitle'] = $this->lang->line('title_admin_notification').' | '.$this->lang->line('site_title');        

        $this->load->view(config_item('admin_directory').'/'.$this->list_viewfile,$data);

    }

    public function add() {

        if (!$this->session->userdata('is_admin_login')) {

            redirect(config_item('admin_directory').'/home');

        }

        $data['MetaTitle'] = $this->lang->line('title_admin_notification_add').' | '.$this->lang->line('site_title');

        if($this->input->post('submitNotification') == "Submit")

        {

            $this->form_validation->set_rules('notification_type', 'Notification Type', 'trim|required');

            $this->form_validation->set_rules('Users[]', 'Users', 'trim|required');

            $this->form_validation->set_rules('notification_contents', 'Notification Type', 'trim|required');

            $this->form_validation->set_rules('notification_date', 'Notification Message', 'trim|required');



            if ($this->form_validation->run())

            {                
                if($this->input->post('notification_type') == "winnersAnnouncement")
                {

                    $user_local_timezone = ($this->session->userdata('user_local_timezone'))?$this->session->userdata('user_local_timezone'):'America/New_York';                
                    date_default_timezone_set($user_local_timezone);
                    $dt1 = new DateTime($this->input->post('notification_date')." ".date("H:i:s"), new DateTimeZone($user_local_timezone));
                    $dt1->setTimezone(new DateTimeZone('UTC'));
                    $current_date = $dt1->format('Y-m-d');
                    $notification_date = $dt1->format('Y-m-d');
                    $notification_date_time = $dt1->format('Y-m-d H:i:s');
                    date_default_timezone_set('UTC');
                    $checkCorrectAns = $this->notification_model->getCheckQuizResultData($current_date);
                    if(!empty($checkCorrectAns) && $checkCorrectAns->points >0)
                    {

                        $addNotificationData = array(                   

                            'notification_type'=>$this->input->post('notification_type'),

                            'notification_contents'=>$this->input->post('notification_contents'),

                            'notification_date'=>$notification_date,

                            'created_by'=>$this->session->userdata("adminID"),

                            'created_date'=>date("Y-m-d H:i:s")

                        );                                            

                        



                        $winnerList = array();

                        


                            //$current_date = $this->input->post('notification_date');

                            $getTodaysQuestion = $this->notification_model->getTodaysQuestion($current_date);

                            $todayQeustionAns = array();

                            if(!empty($getTodaysQuestion))

                            {

                                $todayQeustionAns = $this->notification_model->getTodaysQuestionAns($current_date);        

                            }



                            if($todayQeustionAns == count($getTodaysQuestion))

                            {

                                $this->db->insert('quiz_master',array('quiz_date'=>$current_date,'display_answer'=>1));

                                $this->db->where('option_slug','notification_send_count');

                                $winnerCount = $this->db->get('system_option')->first_row();

                                $AttemptUserData = $this->notification_model->getQuizResultData($current_date,$winnerCount->option_value,1);        
                                if(!empty($AttemptUserData))
                                {
                                    foreach ($AttemptUserData as $key => $value) 
                                    {
                                        if($value['points']>0)
                                        {
                                            $winnerList[] = $value;                                        
                                        }
                                    }                                    
                                }
                            }
                        $notification_id = $this->notification_model->addData('notifications',$addNotificationData);

                                     

                        // START Push Notification

                       /* $DeviceIds = $this->notification_model->getUserDevices($_POST['Users']);                



                        $registrationIds = array_column($DeviceIds, 'push_notification_token');



                        $return = array_chunk($registrationIds,800);                



                        foreach ($return as $key => $registrationId) {

                            #prep the bundle

                            $fields = array();            

                            if(is_array($registrationId) && count($registrationId) > 1){

                                $fields['registration_ids'] = $registrationId; // multiple user to send push notification

                            }else{

                                $fields['to'] = $registrationId[0]; // only one user to send push notification

                            }



                            $fields['notification']['body'] = $this->input->post('notification_contents');

                            $fields['notification']['sound'] = 'default';

                            $fields['data'] = array ('notification_type'=>$this->input->post('notification_type'),'notification_date'=>$notification_date,'notification_date_time'=>$notification_date_time);



                            $headers = array (

                                        'Authorization: key=' . FCM_KEY,

                                        'Content-Type: application/json'

                            );

                            #Send Reponse To FireBase Server    

                            $ch = curl_init();

                            curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );

                            curl_setopt( $ch,CURLOPT_POST, true );

                            curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );

                            curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );

                            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );

                            curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );

                            $result = curl_exec($ch );

                            curl_close( $ch );

                        }*/


                        if(!empty($winnerList))

                        {

                            // START Push Notification

                            $DeviceIds = $this->notification_model->getUserDevices(array_column($winnerList, 'user_id'));                

                            $registrationIds = array_column($DeviceIds, 'push_notification_token');

                            $return = array_chunk($registrationIds,800);                



                            $this->db->where('option_slug','winner_text_cronjob');

                            $cronText = $this->db->get('system_option')->first_row();



                            foreach ($return as $key => $registrationId) 

                            {

                                #prep the bundle

                                $fields = array();            

                                if(is_array($registrationId) && count($registrationId) > 1){

                                    $fields['registration_ids'] = $registrationId; // multiple user to send push notification

                                }else{

                                    $fields['to'] = $registrationId[0]; // only one user to send push notification

                                }



                                $fields['notification']['body'] = $cronText->option_value;

                                $fields['notification']['sound'] = 'default';

                                $fields['data'] = array ('notification_type'=>'winnersAnnouncement','notification_date'=>$notification_date,'notification_date_time'=>$notification_date_time);



                                $headers = array (

                                            'Authorization: key=' . FCM_KEY,

                                            'Content-Type: application/json'

                                );

                                #Send Reponse To FireBase Server    

                                $ch = curl_init();

                                curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );

                                curl_setopt( $ch,CURLOPT_POST, true );

                                curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );

                                curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );

                                curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );

                                curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );

                                $result = curl_exec($ch );

                                curl_close( $ch );

                            }  

                        }     


                        date_default_timezone_set('America/New_York');

                        $this->session->set_flashdata('NotificationMSG', $this->lang->line('success_add'));

                        redirect(config_item('admin_base_url').$this->controller_name."/view"); 
                    }
                    else
                    {
                        $data['Error'] = 'Notification Send failed! All participants all Answer got it wrong';
                    }
                }
                else
                {

                    $user_local_timezone = ($this->session->userdata('user_local_timezone'))?$this->session->userdata('user_local_timezone'):'America/New_York';                
                    date_default_timezone_set($user_local_timezone);
                    $dt1 = new DateTime($this->input->post('notification_date')." ".date("H:i:s"), new DateTimeZone($user_local_timezone));
                    $dt1->setTimezone(new DateTimeZone('UTC'));
                    $current_date = $dt1->format('Y-m-d');
                    $notification_date = $dt1->format('Y-m-d');
                    $notification_date_time = $dt1->format('Y-m-d H:i:s');
                    date_default_timezone_set('UTC');


                    $addNotificationData = array(                   

                        'notification_type'=>$this->input->post('notification_type'),

                        'notification_contents'=>$this->input->post('notification_contents'),

                        'notification_date'=>$notification_date,

                        'created_by'=>$this->session->userdata("adminID"),

                        'created_date'=>date("Y-m-d H:i:s")

                    );                                            

                    $notification_id = $this->notification_model->addData('notifications',$addNotificationData);



                        $winnerList = array();

                    if($this->input->post('notification_type') == "answersPosted")

                    {


                        //$current_date = $this->input->post('notification_date');

                        $getTodaysQuestion = $this->notification_model->getTodaysQuestion($current_date);

                        $todayQeustionAns = array();

                        if(!empty($getTodaysQuestion))

                        {

                            $todayQeustionAns = $this->notification_model->getTodaysQuestionAns($current_date);        

                        }



                        if($todayQeustionAns == count($getTodaysQuestion))

                        {

                            $this->db->insert('quiz_master',array('quiz_date'=>$current_date,'display_answer'=>1));

                            $this->db->where('option_slug','notification_send_count');

                            $winnerCount = $this->db->get('system_option')->first_row();

                            $AttemptUserData = $this->notification_model->getQuizResultData($current_date,$winnerCount->option_value,1);        
                            if(!empty($AttemptUserData))
                            {
                                foreach ($AttemptUserData as $key => $value) 
                                {
                                    if($value['points']>0)
                                    {
                                        $winnerList[] = $value;                                        
                                    }
                                }                                    
                            }

                        }

                    }

                                 

                    // START Push Notification

                    $DeviceIds = $this->notification_model->getUserDevices($_POST['Users']);                



                    $registrationIds = array_column($DeviceIds, 'push_notification_token');



                    $return = array_chunk($registrationIds,800);                



                    foreach ($return as $key => $registrationId) {

                        #prep the bundle

                        $fields = array();            

                        if(is_array($registrationId) && count($registrationId) > 1){

                            $fields['registration_ids'] = $registrationId; // multiple user to send push notification

                        }else{

                            $fields['to'] = $registrationId[0]; // only one user to send push notification

                        }

                        /*if($this->input->post('NotificationDescription')){

                            $fields['notification']['body'] = $this->input->post('NotificationDescription');    

                        }*/  



                        $fields['notification']['body'] = $this->input->post('notification_contents');

                        $fields['notification']['sound'] = 'default';

                        $fields['data'] = array ('notification_type'=>$this->input->post('notification_type'),'notification_date'=>$notification_date,'notification_date_time'=>$notification_date_time);


                        $headers = array (

                                    'Authorization: key=' . FCM_KEY,

                                    'Content-Type: application/json'

                        );

                        #Send Reponse To FireBase Server    

                        $ch = curl_init();

                        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );

                        curl_setopt( $ch,CURLOPT_POST, true );

                        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );

                        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );

                        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );

                        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );

                        $result = curl_exec($ch );

                        curl_close( $ch );

                    }

                    // END Push Notification


                    $checkCorrectAns = $this->notification_model->getCheckQuizResultData($current_date);

                    if(!empty($winnerList) && !empty($checkCorrectAns) && $checkCorrectAns->points >0)

                    {

                        // START Push Notification

                        $DeviceIds = $this->notification_model->getUserDevices(array_column($winnerList, 'user_id'));                

                        $registrationIds = array_column($DeviceIds, 'push_notification_token');

                        $return = array_chunk($registrationIds,800);                



                        $this->db->where('option_slug','winner_text_cronjob');

                        $cronText = $this->db->get('system_option')->first_row();



                        foreach ($return as $key => $registrationId) 

                        {

                            #prep the bundle

                            $fields = array();            

                            if(is_array($registrationId) && count($registrationId) > 1){

                                $fields['registration_ids'] = $registrationId; // multiple user to send push notification

                            }else{

                                $fields['to'] = $registrationId[0]; // only one user to send push notification

                            }



                            $fields['notification']['body'] = $cronText->option_value;

                            $fields['notification']['sound'] = 'default';

                            $fields['data'] = array ('notification_type'=>'winnersAnnouncement','notification_date'=>$notification_date,'notification_date_time'=>$notification_date_time);



                            $headers = array (

                                        'Authorization: key=' . FCM_KEY,

                                        'Content-Type: application/json'

                            );

                            #Send Reponse To FireBase Server    

                            $ch = curl_init();

                            curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );

                            curl_setopt( $ch,CURLOPT_POST, true );

                            curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );

                            curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );

                            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );

                            curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );

                            $result = curl_exec($ch );

                            curl_close( $ch );

                        }  

                    }     


                    date_default_timezone_set('America/New_York');

                    $this->session->set_flashdata('NotificationMSG', $this->lang->line('success_add'));

                    redirect(config_item('admin_base_url').$this->controller_name."/view");                     
                }
            }

        }

        $data['users'] = $this->notification_model->getUserList();

        $this->load->view(config_item('admin_directory').'/'.$this->addedit_viewfile,$data);

    }

    

    public function ajaxview() {

        $displayLength = ($this->input->post('iDisplayLength') != '')?intval($this->input->post('iDisplayLength')):'';

        $displayStart = ($this->input->post('iDisplayStart') != '')?intval($this->input->post('iDisplayStart')):'';

        $sEcho = ($this->input->post('sEcho'))?intval($this->input->post('sEcho')):'';

        $sortCol = ($this->input->post('iSortCol_0'))?intval($this->input->post('iSortCol_0')):'';

        $sortOrder = ($this->input->post('sSortDir_0'))?$this->input->post('sSortDir_0'):'ASC';

        

        $sortfields = array(1=>'notification_type',2=>'notification_contents',3=>'notification_date');

        $sortFieldName = '';

        if(array_key_exists($sortCol, $sortfields))

        {

            $sortFieldName = $sortfields[$sortCol];

        }

        

        //Get Recored from model

        $NotificationData = $this->notification_model->getNotificationList($sortFieldName,$sortOrder,$displayStart,$displayLength);

        //echo "<pre> ";print_r($NotificationData);exit;

        $totalRecords = $NotificationData['total'];        

        $records = array();

        $records["aaData"] = array(); 

        $nCount = ($displayStart != '')?$displayStart+1:1;

        date_default_timezone_set('UTC');
        $user_local_timezone = ($this->session->userdata('user_local_timezone'))?$this->session->userdata('user_local_timezone'):'America/New_York';                

        foreach ($NotificationData['data'] as $key => $notificationDetails) {

            $dt = new DateTime($notificationDetails->notification_date." ".date("H:i:s"), new DateTimeZone('UTC'));

            $dt->setTimezone(new DateTimeZone($user_local_timezone));

            $notification_date = $dt->format('Y-m-d');
           
            $records["aaData"][] = array(

                $nCount,

                $notificationDetails->notification_type,                

                $notificationDetails->notification_contents,                

                $notification_date,                

                '<button onclick="deleteNotification('.$notificationDetails->notification_id.')"  title="Click here for Delete" class="delete btn btn-sm danger-btn margin-bottom"><i class="fa fa-times"></i> Delete</button>'

            );

            $nCount++;

        }        
        date_default_timezone_set('America/New_York');

        $records["sEcho"] = $sEcho;

        $records["iTotalRecords"] = $totalRecords;

        $records["iTotalDisplayRecords"] = $totalRecords;

        echo json_encode($records);

    }

    public function ajaxdeleteNotification() {

        $notification_id = ($this->input->post('notification_id') != '')?$this->input->post('notification_id'):'';

        if($notification_id != ''){

            $this->notification_model->deleteRecord($notification_id);

        }

    }

    public function new_question_added()

    {

      // START Push Notification
    date_default_timezone_set('UTC');

      $getTodaysQuestion = $this->notification_model->getTodaysQuestion(date("Y-m-d"));

      if(!empty($getTodaysQuestion))

      {

        $DeviceIds = $this->notification_model->getUserDevices(array('6'));                



        $registrationIds = array_column($DeviceIds, 'push_notification_token');

        $return = array_chunk($registrationIds,800);                

        

        $this->db->where('option_slug','question_added_cron_notification');

        $cronText = $this->db->get('system_option')->first_row();



        foreach ($return as $key => $registrationId) {

            #prep the bundle

            $fields = array();            

            if(is_array($registrationId) && count($registrationId) > 1){

                $fields['registration_ids'] = $registrationId; // multiple user to send push notification

            }else{

                $fields['to'] = $registrationId[0]; // only one user to send push notification

            }



            $fields['notification']['body'] = $cronText->option_value;

            $fields['notification']['sound'] = 'default';

            $fields['data'] = array ('notification_type'=>'questionsPosted','notification_date'=>date("Y-m-d"),'notification_date_time'=>date("Y-m-d H:i:s"));

            date_default_timezone_set('America/New_York');


            $headers = array (

                        'Authorization: key=' . FCM_KEY,

                        'Content-Type: application/json'

            );

            #Send Reponse To FireBase Server    

            $ch = curl_init();

            curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );

            curl_setopt( $ch,CURLOPT_POST, true );

            curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );

            curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );

            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );

            curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );

            $result = curl_exec($ch );

            curl_close( $ch );

        } 

      }               

    }

    public function answer_posted_cron()

    {

      // START Push Notification
    date_default_timezone_set('UTC');

      $current_date = date("Y-m-d");

      $getTodaysQuestion = $this->notification_model->getTodaysQuestion($current_date);

      $todayQeustionAns = array();

      if(!empty($getTodaysQuestion))

      {

        $todayQeustionAns = $this->notification_model->getTodaysQuestionAns($current_date);        

      }



      if($todayQeustionAns == count($getTodaysQuestion))

      {

        $this->db->insert('quiz_master',array('quiz_date'=>$current_date,'display_answer'=>1));



        $DeviceIds = $this->notification_model->getUserDevices(array('6'));                



        $registrationIds = array_column($DeviceIds, 'push_notification_token');

        $return = array_chunk($registrationIds,800);                

        

        $this->db->where('option_slug','answer_added_cron_notification');

        $cronText = $this->db->get('system_option')->first_row();



        foreach ($return as $key => $registrationId) {

            #prep the bundle

            $fields = array();            

            if(is_array($registrationId) && count($registrationId) > 1){

                $fields['registration_ids'] = $registrationId; // multiple user to send push notification

            }else{

                $fields['to'] = $registrationId[0]; // only one user to send push notification

            }



            $fields['notification']['body'] = $cronText->option_value;

            $fields['notification']['sound'] = 'default';

            $fields['data'] = array ('notification_type'=>'answersPosted','notification_date'=>$current_date,'notification_date_time'=>date("Y-m-d H:i:s"));


            date_default_timezone_set('America/New_York');

            $headers = array (

                        'Authorization: key=' . FCM_KEY,

                        'Content-Type: application/json'

            );

            #Send Reponse To FireBase Server    

            $ch = curl_init();

            curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );

            curl_setopt( $ch,CURLOPT_POST, true );

            curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );

            curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );

            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );

            curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );

            $result = curl_exec($ch );

            curl_close( $ch );

        }   

      }             

    }

    public function send_winner_cron()

    {
        date_default_timezone_set('UTC');

      $getTodaysQuestion = $this->notification_model->getTodaysQuestion(date("Y-m-d"));

      $todayQeustionAns = array();

      $winnerList = array();

      if(!empty($getTodaysQuestion))

      {

        $todayQeustionAns = $this->notification_model->getTodaysQuestionAns(date("Y-m-d"));        

      }



      if($todayQeustionAns == count($getTodaysQuestion))

      {

        $this->db->where('option_slug','notification_send_count');

        $winnerCount = $this->db->get('system_option')->first_row();

        $winnerList = $this->notification_model->getQuizResultData(date("Y-m-d"),$winnerCount->option_value,1);        

      }



      if(!empty($winnerList))

      {

        // START Push Notification

        $DeviceIds = $this->notification_model->getUserDevices(array_column($winnerList, 'user_id'));                

        $registrationIds = array_column($DeviceIds, 'push_notification_token');

        $return = array_chunk($registrationIds,800);                

        

        $this->db->where('option_slug','winner_text_cronjob');

        $cronText = $this->db->get('system_option')->first_row();



        foreach ($return as $key => $registrationId) {

            #prep the bundle

            $fields = array();            

            if(is_array($registrationId) && count($registrationId) > 1){

                $fields['registration_ids'] = $registrationId; // multiple user to send push notification

            }else{

                $fields['to'] = $registrationId[0]; // only one user to send push notification

            }



            $fields['notification']['body'] = $cronText->option_value;

            $fields['notification']['sound'] = 'default';

            $fields['data'] = array ('notification_type'=>'winnersAnnouncement','notification_date'=>date("Y-m-d"),'notification_date_time'=>date("Y-m-d H:i:s"));


            date_default_timezone_set('America/New_York');

            $headers = array (

                        'Authorization: key=' . FCM_KEY,

                        'Content-Type: application/json'

            );

            #Send Reponse To FireBase Server    

            $ch = curl_init();

            curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );

            curl_setopt( $ch,CURLOPT_POST, true );

            curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );

            curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );

            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );

            curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );

            $result = curl_exec($ch );

            curl_close( $ch );

        }  

      }       

    }    

}