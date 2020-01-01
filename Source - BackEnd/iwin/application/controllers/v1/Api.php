<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';
class Api extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
        $this->load->model('api_model');                
        $this->load->library('form_validation');
        define('perPage', 10);
    }
    public function registration_post()
    {
        if($this->input->post('social_media_id') !="" && $this->input->post('social_media_type') != "")
        {
            if($this->input->post('social_media_type') == "FB")
            {
                $checkRecord = $this->api_model->getRecord('user_master', 'social_media_fb_id',$this->input->post('social_media_id'));
            }
            else
            {
                $checkRecord = $this->api_model->getRecord('user_master', 'social_media_gp_id',$this->input->post('social_media_id'));                
            }

            $checkEmailRecord = $this->api_model->getRecord('user_master', 'email',$this->input->post('email'));                
            if(!empty($checkEmailRecord) && empty($checkRecord))
            {
                $updateUser = array(
                    'first_name'=>($this->input->post('first_name'))?$this->input->post('first_name'):'',
                    'last_name'=>($this->input->post('last_name'))?$this->input->post('last_name'):'',
                    'gender'=>($this->input->post('gender'))?$this->input->post('gender'):'',
                    'email'=>($this->input->post('email'))?strtolower(trim($this->input->post('email'))):'',
                    'phone'=>($this->post('phone'))?$this->post('phone'):'',
                    'push_notification_token'=>($this->post('push_notification_token'))?$this->post('push_notification_token'):'',
                    'user_type'=>'User',
                    'status'=>1,
                    'device_type'=>($this->post('device_type'))?$this->post('device_type'):null,
                    'updated_date'=>date("Y-m-d H:i:s"),
                    'updated_by'=>$checkEmailRecord->user_id
                );
                if($this->input->post('social_media_type') =="FB")
                {
                    $updateUser['social_media_fb_id'] = $this->input->post('social_media_id');
                }
                else
                {
                    $updateUser['social_media_gp_id'] = $this->input->post('social_media_id');                    
                }


                if($this->input->post('birth_date') !="" && $this->input->post('birth_date') >0)
                {
                    $updateUser['birth_date'] = $this->input->post('birth_date');
                }
                if ($this->post('profile_pic')!="")
                {
                    $url = $this->post('profile_pic');
                    
                    /* Extract the filename */
                    $filename = time().".png";
                    /* Save file wherever you want */
                    $imageData = @file_get_contents($url);
                    if(!empty($imageData))
                    {
                        @file_put_contents('./uploads/user_images/'.$filename, $imageData);
                        $updateUser['profile_pic'] = $filename;                        
                    }
                } 

                $this->api_model->updateUser('user_master', $updateUser,'user_id',$checkEmailRecord->user_id);
                $userData = $this->api_model->getRecord('user_master', 'user_id',$checkEmailRecord->user_id);

                $profile_pic_full_url = "";
                if($userData->profile_pic!="")
                {
                    $profile_pic_full_url = base_url().'uploads/user_images/'.$userData->profile_pic;
                }

                $this->api_model->updateUser('user_master',array('last_login'=>date("Y-m-d H:i:s")),'user_id',$checkEmailRecord->user_id);                    
                $this->response(['user_id' => $checkEmailRecord->user_id,'profile_pic' =>$profile_pic_full_url ,'status' => 1,'message' => $this->lang->line('registration_success')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else if(empty($checkRecord))
            {
                $addUser = array(
                    'first_name'=>($this->input->post('first_name'))?$this->input->post('first_name'):'',
                    'last_name'=>($this->input->post('last_name'))?$this->input->post('last_name'):'',
                    'gender'=>($this->input->post('gender'))?$this->input->post('gender'):'',
                    'email'=>($this->input->post('email'))?strtolower(trim($this->input->post('email'))):'',
                    'password'=>'',
                    'phone'=>($this->post('phone'))?$this->post('phone'):'',
                    'push_notification_token'=>($this->post('push_notification_token'))?$this->post('push_notification_token'):'',
                    'user_type'=>'User',
                    'status'=>1,
                    'device_type'=>($this->post('device_type'))?$this->post('device_type'):null,
                    'created_date'=>date("Y-m-d H:i:s")
                );
                if($this->input->post('social_media_type') =="FB")
                {
                    $addUser['social_media_fb_id'] = $this->input->post('social_media_id');
                }
                else
                {
                    $addUser['social_media_gp_id'] = $this->input->post('social_media_id');                    
                }


                if($this->input->post('birth_date') !="" && $this->input->post('birth_date') >0)
                {
                    $addUser['birth_date'] = $this->input->post('birth_date');
                }
                if ($this->post('profile_pic')!="")
                {
                    $url = $this->post('profile_pic');
                    
                    /* Extract the filename */
                    $filename = time().".png";
                    /* Save file wherever you want */
                    file_put_contents('./uploads/user_images/'.$filename, file_get_contents($url));
                    $addUser['profile_pic'] = $filename;
                } 

                $user_id = $this->api_model->addRecord('user_master', $addUser);
                $this->api_model->updateUser('user_master', array('created_by'=>$user_id),'user_id',$user_id);
                if($user_id)
                {
                    $userData = $this->api_model->getRecord('user_master', 'user_id',$user_id);

                    $profile_pic_full_url = "";
                    if($userData->profile_pic!="")
                    {
                        $profile_pic_full_url = base_url().'uploads/user_images/'.$userData->profile_pic;
                    }

                    $this->api_model->updateUser('user_master',array('last_login'=>date("Y-m-d H:i:s")),'user_id',$user_id);                    
                    $this->response(['user_id' => $user_id,'profile_pic' =>$profile_pic_full_url ,'status' => 1,'message' => $this->lang->line('registration_success')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response([
                        'status' => 0,
                        'message' => $this->lang->line('registration_fail')
                    ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }                            
            }
            else
            {
                $this->response([
                    'status' => 0,
                    'message' => $this->lang->line('user_exist')
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code            
            }
        }
        else
        {
            if($this->input->post('email') !="" && $this->input->post('password') != "")
            {
                //$checkEmailPassword = $this->api_model->getRecordMultipleWhere('user_master', array('email'=>$this->input->post('email'),'password'=>""));
                $checkRecord = $this->api_model->getRecord('user_master', 'email',$this->input->post('email'));
                
                if(empty($checkRecord))
                {
                    $salt = '5&JDDlwz%Rwh!t2Yg-Igae@QxPzFTSId';
                    $addUser = array(
                        'first_name'=>($this->input->post('first_name'))?$this->input->post('first_name'):'',
                        'last_name'=>($this->input->post('last_name'))?$this->input->post('last_name'):'',
                        'gender'=>($this->input->post('gender'))?$this->input->post('gender'):'',
                        'email'=>($this->input->post('email'))?strtolower(trim($this->input->post('email'))):'',
                        'password'=>md5($salt.$this->input->post('password')),
                        'phone'=>($this->post('phone'))?$this->post('phone'):'',
                        'push_notification_token'=>($this->post('push_notification_token'))?$this->post('push_notification_token'):'',
                        'user_type'=>'User',
                        'status'=>1,
                        'device_type'=>($this->post('device_type'))?$this->post('device_type'):null,
                        'created_date'=>date("Y-m-d H:i:s")
                    );
                    if($this->input->post('birth_date') !="" && $this->input->post('birth_date') >0)
                    {
                        $addUser['birth_date'] = $this->input->post('birth_date');
                    }
                    
                    if (!empty($_FILES['profile_pic']['name']))
                    {
                        if (!empty($_FILES['profile_pic']['name']))
                        {
                            $config['upload_path']          = "./uploads/user_images";
                            $config['allowed_types']        = 'gif|jpg|png|jpeg';

                            if (!@is_dir($config['upload_path'])) {
                                @mkdir($config['upload_path'], 0777, TRUE);
                            }
                                
                            $this->load->library('upload', $config);

                            if ( $this->upload->do_upload('profile_pic'))
                            {
                                $imgdata = $this->upload->data();   
                                $addUser['profile_pic'] = $imgdata['file_name'];
                            }
                            else
                            {
                                $error = 'invalid image';
                                $this->response([
                                    'status' => 0,
                                    'message' => $this->lang->line('upload_image_invalid')
                                ], REST_Controller::HTTP_NOT_FOUND);
                            }
                        }
                    } 
                    if($error == "") 
                    {
                        $user_id = $this->api_model->addRecord('user_master', $addUser);
                        $this->api_model->updateUser('user_master', array('created_by'=>$user_id),'user_id',$user_id);
                        if($user_id)
                        {
                            $userData = $this->api_model->getRecord('user_master', 'user_id',$user_id);
                            $profile_pic_full_url = "";
                            if($userData->profile_pic!="")
                            {
                                $profile_pic_full_url = base_url().'uploads/user_images/'.$userData->profile_pic;
                            }

                            $this->api_model->updateUser('user_master',array('last_login'=>date("Y-m-d H:i:s")),'user_id',$user_id);                    
                            $this->response(['user_id' => $user_id,'profile_pic' =>$profile_pic_full_url ,'status' => 1,'message' => $this->lang->line('registration_success')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                        }
                        else
                        {
                            $this->response([
                                'status' => 0,
                                'message' => $this->lang->line('registration_fail')
                            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                        }                            
                    }
                } 
                else
                {
                        $this->response([
                            'status' => 0,
                            'message' => $this->lang->line('email_exist')
                        ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code            
                }
            }
            else
            {
                $this->response([
                    'status' => 0,
                    'message' => $this->lang->line('registration_fail')
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code                        
            }            
        }
    }
    public function login_post()
    {
        if($this->input->post('social_media_id') !="")
        {
            if($this->input->post('social_media_type') == "FB")
            {
                $login = $this->api_model->getRecord('user_master', 'social_media_fb_id',$this->input->post('social_media_id'));
            }
            else
            {
                $login = $this->api_model->getRecord('user_master', 'social_media_gp_id',$this->input->post('social_media_id'));                
            }

            if ($login)
            {
                if($login->profile_pic!="")
                {
                    $login->profile_pic = base_url().'uploads/user_images/'.$login->profile_pic;
                }
                if($this->input->post('push_notification_token'))
                {
                    $this->api_model->updateUser('user_master',array('push_notification_token'=>$this->input->post('push_notification_token')),'user_id',$login->user_id);                    
                }
                $this->api_model->updateUser('user_master',array('last_login'=>date("Y-m-d H:i:s")),'user_id',$login->user_id);                    
                unset($login->password);
                $this->response(['login' => $login, 'status' => 1,'message' => $this->lang->line('login_success')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $checkEmailRecord = $this->api_model->getRecord('user_master', 'email',$this->input->post('email'));                
                if(!empty($checkEmailRecord) && empty($checkRecord))
                {
                    $updateUser = array(
                        'push_notification_token'=>($this->post('push_notification_token'))?$this->post('push_notification_token'):'',
                        'updated_date'=>date("Y-m-d H:i:s"),
                        'updated_by'=>$checkEmailRecord->user_id
                    );
                    if($this->input->post('social_media_type') =="FB")
                    {
                        $updateUser['social_media_fb_id'] = $this->input->post('social_media_id');
                    }
                    else
                    {
                        $updateUser['social_media_gp_id'] = $this->input->post('social_media_id');                    
                    }

                    $this->api_model->updateUser('user_master', $updateUser,'user_id',$checkEmailRecord->user_id);
                    $userData = $this->api_model->getRecord('user_master', 'user_id',$checkEmailRecord->user_id);

                    if($userData->profile_pic!="")
                    {
                        $userData->profile_pic = base_url().'uploads/user_images/'.$userData->profile_pic;
                    }
                    if($this->input->post('push_notification_token'))
                    {
                        $this->api_model->updateUser('user_master',array('push_notification_token'=>$this->input->post('push_notification_token')),'user_id',$userData->user_id);                    
                    }
                    $this->api_model->updateUser('user_master',array('last_login'=>date("Y-m-d H:i:s")),'user_id',$login->user_id);                    
                    unset($userData->password);
                    $this->response(['login' => $userData, 'status' => 1,'message' => $this->lang->line('login_success')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
                else
                {
                    $this->response([
                        'status' => 0,
                        'message' => $this->lang->line('invalid_user')
                    ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
                }        
            }
        }
        else
        {
            $login = $this->api_model->getLogin(strtolower(trim($this->input->post('email'))), $this->input->post('password'));        

            if($login->profile_pic!="")
            {
                $login->profile_pic = base_url().'uploads/user_images/'.$login->profile_pic;
            }

            if ($login)
            {
                if($this->input->post('push_notification_token'))
                {
                    $this->api_model->updateUser('user_master',array('push_notification_token'=>$this->input->post('push_notification_token')),'user_id',$login->user_id);                    
                }
                $this->api_model->updateUser('user_master',array('last_login'=>date("Y-m-d H:i:s")),'user_id',$login->user_id);                    
                unset($login->password);

                $this->response(['login' => $login, 'status' => 1,'message' => $this->lang->line('login_success')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response([
                    'status' => 0,
                    'message' => $this->lang->line('login_fail')
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }        
        }
    }
    public function forgotpassword_post()
    {
        $checkRecord = $this->api_model->getRecordMultipleWhere('user_master', array('email'=>strtolower(trim($this->input->post('email'))),'status'=>1));
        if(!empty($checkRecord))
        {
            $activecode = substr(md5(uniqid(mt_rand(), true)) , 0, 8);
            // email message body
            $forgot_password_emailBody = '<html>
            <link href="https://fonts.googleapis.com/css?family=PT+Sans:400,400italic,700,700italic" rel="stylesheet" type="text/css">
            <body topmargin="0" leftmargin="0" marginheight="0" marginwidth="0" style="-webkit-font-smoothing: antialiased;width:100% !important;background:#fff;-webkit-text-size-adjust:none;font-family: PT Sans, sans-serif;">
            <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#fff"><tr>
            <td padding-bottom:30px; border:1px solid #066bd3; width:100%; height:100%;" >
            <table width="533" cellpadding="0" cellspacing="0" border="0" align="center" class="table">
            <tr>
            <td width="533">        
              <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table">
            <tr>
            <td width="100%" class="logocell">                
            <img src="'.base_url().'assets/backend/img/header_logo.png" alt="OverUnderz App Logo" style="-ms-interpolation-mode:bicubic; width:220px; padding-bottom:10px;"><br>              
            </td>
            </tr>
            </table>
            <table width="533" cellpadding="25" cellspacing="0" border="0">
            <tr>
            <td style="border: 1px solid #066bd3; padding:40px 30px; box-shadow:0 2px 3px #cccccc; border-radius: 4px; background-color:#fff;">
            <p style="font-size:14px; color:#343434; margin-bottom:10px;">Hi '.$checkRecord->first_name.',</p>              
            <p style="font-size:14px; color:#343434; margin-bottom:10px;">We received a request to reset the password associated with this email address.</p>
            <p style="font-size:14px; color:#343434; margin-bottom:10px;">Please reset your password: <a href="'.base_url().'user/reset/'.$activecode.'">'.base_url().'user/reset/'.$activecode.'</a></p>
            <p style="font-size:14px; color:#343434; margin-bottom:10px;">If clicking the link above does not work, copy and paste the URL in a new browser window instead.</p>
            <br>
            <p style="font-size:14px; color:#343434; margin-bottom:10px;">Best Regards, <br /> OverUnderz Team</p></td>                         
            </tr>
            <tr>
                <td align="center" style="background:#00243c; color:#fff; padding:20px 0px;">
                    Copyright '.date('Y')." ".$this->lang->line('site_title').'  All rights reserved.
                </td>
            </tr>
            </table>            
            </table>
            </body>
            </html>';          
            //echo $forgot_password_emailBody;exit;
            $this->load->library('email');  
            $config['charset'] = 'iso-8859-1';  
            $config['wordwrap'] = TRUE;  
            $config['mailtype'] = 'html';  
            $this->email->initialize($config);  
            $this->email->from('support@overunderz.com', 'OverUnderz');  
            $this->email->to(trim($this->input->post('email')));  
            $this->email->subject('OverUnderz Application: New Password');  
            $this->email->message($forgot_password_emailBody);  

            if($this->email->send())
            {
                $data = array('verification_code'=>$activecode);
                $this->api_model->updateUser('user_master',$data,'user_id',$checkRecord->user_id);
            }

            //$this->api_model->updateUser('user_master',array('password'=>$enc_pass),'user_id',$checkRecord->user_id);
            $this->response(['status' => 1,'message' => $this->lang->line('fp_email_send')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        else
        {
                $this->response([
                    'status' => 0,
                    'message' => $this->lang->line('invalid_email')
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code            
        }
    }
    public function save_lat_lang_post()
    {
        if($this->input->post('user_id') !="")
        {
            $LatLongArrayData = array(
                'latitude'=>($this->input->post('latitude'))?$this->input->post('latitude'):null,
                'longitude'=>($this->input->post('longitude'))?$this->input->post('longitude'):null
            );
            $this->api_model->updateUser('user_master',$LatLongArrayData,'user_id',$this->input->post('user_id'));

            $this->response([
                'status' => 1,
                'message' => $this->lang->line('lat_long_update_success')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        else
        {
            $this->response([
                'status' => 0,
                'message' => $this->lang->line('invalid_user')
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    public function get_profile_data_post()
    {
        if($this->input->post('user_id') !="")
        {
            //save user screen record
            $this->api_model->addRecord('user_screen_log',array('user_id'=>$this->post('user_id'),'api_name'=>'get_profile_data','created_date'=>date('Y-m-d H:i:s')));
            $userData = $this->api_model->getRecord('user_master','user_id',$this->input->post('user_id'));     
            if ($userData)
            {
                unset($userData->password);
                $this->response([
                    'UserData'=>$userData,
                    'status' => 1,
                    'message' => $this->lang->line('user_data_found')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                $this->response([
                    'status' => 0,
                    'message' => $this->lang->line('user_data_not_found')
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }                    
        }
        else
        {
            $this->response([
                'status' => 0,
                'message' => $this->lang->line('user_data_not_found')
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code            
        }
    }
    public function save_profile_data_post()
    {
        if($this->input->post('user_id') != "")
        {
            $this->form_validation->set_rules('first_name', 'First Name', 'trim|required');
            $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required');
            if ($this->form_validation->run())
            {
                $editProfileData = array(
                    'first_name'=>$this->input->post('first_name'),
                    'last_name'=>$this->input->post('last_name'),
                    'latitude'=>($this->input->post('latitude'))?$this->input->post('latitude'):null,
                    'longitude'=>($this->input->post('longitude'))?$this->input->post('longitude'):null,
                    'updated_date'=>date('Y-m-d'),
                    'updated_by'=>$this->input->post('user_id')        
                );

                if($this->input->post('email')!="")
                {
                    $editProfileData['email'] = $this->input->post('email');
                }
                if($this->input->post('latitude')!="")
                {
                    $editProfileData['latitude'] = $this->input->post('latitude');
                }
                if($this->input->post('longitude')!="")
                {
                    $editProfileData['longitude'] = $this->input->post('longitude');
                }
                if (!empty($_FILES['profile_pic']['name']))
                {
                    if (!empty($_FILES['profile_pic']['name']))
                    {
                        $config['upload_path']          = "./uploads/user_images";
                        $config['allowed_types']        = 'gif|jpg|png|jpeg';

                        if (!@is_dir($config['upload_path'])) {
                            @mkdir($config['upload_path'], 0777, TRUE);
                        }
                            
                        $this->load->library('upload', $config);

                        if ( $this->upload->do_upload('profile_pic'))
                        {
                            $imgdata = $this->upload->data();   
                            $editProfileData['profile_pic'] = $imgdata['file_name'];
                        }
                        else
                        {
                            $error = 'invalid image';
                            $this->response([
                                'status' => 0,
                                'message' => $this->lang->line('upload_image_invalid')
                            ], REST_Controller::HTTP_NOT_FOUND);
                        }
                    }
                } 
                if($error == "") 
                {
                    $this->api_model->updateUser('user_master', $editProfileData,'user_id',$this->input->post('user_id'));
                    $userData = $this->api_model->getRecord('user_master', 'user_id',$this->input->post('user_id'));

                    $profile_pic_full_url = "";
                    if($userData->profile_pic!="")
                    {
                        $profile_pic_full_url = base_url().'uploads/user_images/'.$userData->profile_pic;
                    }

                    $this->response(['user_id' => $this->input->post('user_id'),'profile_pic' =>$profile_pic_full_url ,'status' => 1,'message' => $this->lang->line('profile_update_success')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
                }
            }     
            else
            {
                $this->response([
                    'user_data'=>$this->api_model->getRecord('user_master','user_id',$this->input->post('user_id')),        
                    'status' => 0,
                    'message' => validation_errors()
                ], REST_Controller::HTTP_NOT_FOUND);
            }   
        }
        else
        {
            $this->response([
                'status' => 0,
                'message' => $this->lang->line('invalid_user')
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }    
    public function delete_user_post()
    {
        if($this->input->post('user_id')!="")
        {
            $this->api_model->delete_user('user_address','user_id',$this->input->post('user_id'));
            $this->api_model->delete_user('user_answer_master','user_id',$this->input->post('user_id'));
            $this->api_model->delete_user('user_master','user_id',$this->input->post('user_id'));
            $this->response([
                'status' => 1,
                'message' => 'User delete successfully!'
            ], REST_Controller::HTTP_OK);
        }
        else
        {
            $this->response([
                'status' => 0,
                'message' => $this->lang->line('invalid_user')
            ], REST_Controller::HTTP_NOT_FOUND);            
        }
    }
    public function delete_user_answer_post()
    {
        if($this->input->post('user_id')!="")
        {
            $this->api_model->delete_user('user_answer_master','user_id',$this->input->post('user_id'));
            $this->response([
                'status' => 1,
                'message' => 'User ANS delete successfully!'
            ], REST_Controller::HTTP_OK);
        }
        else
        {
            $this->response([
                'status' => 0,
                'message' => 'invalid user'
            ], REST_Controller::HTTP_NOT_FOUND);            
        }
    }
    public function update_token_post()
    {
        if($this->input->post('user_id')!="")
        {
            $this->api_model->updateUser('user_master',array('push_notification_token'=>$this->input->post('push_notification_token')),'user_id',$this->input->post('user_id'));
            $this->response([
                'status' => 1,
                'message' => $this->lang->line('update_token_success')
            ], REST_Controller::HTTP_OK);
        }
        else
        {
            $this->response([
                'status' => 0,
                'message' => $this->lang->line('invalid_user')
            ], REST_Controller::HTTP_NOT_FOUND);            
        }
    }
    public function change_password_post()
    {
        if($this->input->post('user_id')!="")
        {
            $this->form_validation->set_rules('old_password', 'Old Password', 'trim|required');
            $this->form_validation->set_rules('new_password', 'New Password', 'trim|required');
            if ($this->form_validation->run())
            {
                $chckOldPassword = $this->api_model->check_old_assword($this->input->post('user_id'),$this->input->post('old_password'));
                if(!empty($chckOldPassword))
                {
                    $salt = '5&JDDlwz%Rwh!t2Yg-Igae@QxPzFTSId';
                    $enc_pass  = md5($salt.$this->input->post('new_password'));

                    $this->api_model->updateUser('user_master',array('password'=>$enc_pass),'user_id',$this->input->post('user_id'));
                    $this->response([
                        'status' => 1,
                        'message' => $this->lang->line('change_password_success')
                    ], REST_Controller::HTTP_OK);                    
                }
                else
                {
                    $this->response([
                        'status' => 0,
                        'message' => $this->lang->line('invalid_old_password')
                    ], REST_Controller::HTTP_NOT_FOUND);                    
                }
            }
            else
            {
                $this->response([
                    'status' => 0,
                    'message' => validation_errors()
                ], REST_Controller::HTTP_NOT_FOUND);
            }
        }
        else
        {
            $this->response([
                'status' => 0,
                'message' => $this->lang->line('invalid_user')
            ], REST_Controller::HTTP_NOT_FOUND);            
        }
    }
    public function get_quiz_questions_list_post()
    {
        $exam_date = ($this->post('exam_date'))?$this->post('exam_date'):date('Y-m-d');

        //save user screen record
        $this->api_model->addRecord('user_screen_log',array('user_id'=>$this->post('user_id'),'api_name'=>'get_quiz_questions_list','use_date'=>$exam_date,'created_date'=>date('Y-m-d H:i:s')));

        if($exam_date == date('Y-m-d'))
        {
            $isEditableQuestion = 1;
        }
        else
        {
            $isEditableQuestion = 0;
        }

        $questionsCount = $this->api_model->getQuizQuestionsCount($exam_date);        
        $questionsList = $this->api_model->getQuizQuestionsLimit($this->post('user_id'),$exam_date);        
        $question_answer_userans = array();
        if(!empty($questionsList))
        {
            $current_quiz_question = "";
            foreach ($questionsList as $key => $ques_ans_detail) 
            {
                if (!isset($question_answer_userans[$ques_ans_detail["question_id"]])) 
                {
                    if($current_quiz_question == "" && $ques_ans_detail["user_selected_answer"] =="")
                    {
                        $current_quiz_question = $ques_ans_detail["question_id"];   
                    }
                    $question_answer_userans[$ques_ans_detail["question_id"]] = array('question_id'=>$ques_ans_detail["question_id"],'question_type'=>$ques_ans_detail["question_type"],'question_name'=>$ques_ans_detail["question_name"],'question_date'=>$ques_ans_detail["question_date"],'question_detail'=>$ques_ans_detail["question_detail"],'userAnswers'=>$ques_ans_detail["user_selected_answer"]);
                    $question_answer_userans[$ques_ans_detail["question_id"]]['options'] = array();
                }
                //array_push($question_answer_userans[$ques_ans_detail["question_id"]]['options'], $ques_ans_detail);            
                array_push($question_answer_userans[$ques_ans_detail["question_id"]]['options'], array('question_answer_id'=>$ques_ans_detail['question_answer_id'],'answer'=>$ques_ans_detail['answer'],'is_correct_answer'=>$ques_ans_detail['is_correct_answer']));            
            }

            $newFinalQuesAnsArray = array();
            foreach ($question_answer_userans as $key => $value) 
            {
                array_push($newFinalQuesAnsArray, $value);
            }
            if($current_quiz_question!="")
            {
                $this->api_model->updateUser('user_master',array('current_quiz_question'=>$current_quiz_question,'current_quiz_question_date'=>date("Y-m-d H:i:s")),'user_id',$this->post('user_id'));
            }
            //get time for ads display setting
            $timeForAds = 3;
            $timeForAdsData = $this->api_model->getRecord('system_option','option_slug','ads_display');
            if(!empty($timeForAdsData))
            {
                $timeForAds = ($timeForAdsData->option_value !="" && $timeForAdsData->option_value >0)?$timeForAdsData->option_value:3;
            }
            $isAnsAdded = 0;
            $AnsAddedData = $this->api_model->getRecord('quiz_master','quiz_date',$exam_date);
            if(!empty($AnsAddedData))
            {
                if($AnsAddedData->display_answer ==1)
                {
                    $isAnsAdded = 1;
                    $userResult = $this->api_model->getUserResultData($exam_date,$this->post('user_id'));
                    if(!empty($userResult))
                    {
                        foreach ($newFinalQuesAnsArray as $key => $value) 
                        {
                            if(isset($userResult[$value['question_id']]))
                            {
                                $newFinalQuesAnsArray[$key]["isUserAnsCorrect"] = $userResult[$value['question_id']];
                            }
                            else
                            {
                                $newFinalQuesAnsArray[$key]["isUserAnsCorrect"] = 0;
                            }
                        }
                    }
                }
            }
            if($isAnsAdded == 1)
            {
                $isEditableQuestion = 0;
            }
            $this->response([
                'QuizQuestionList'=>$newFinalQuesAnsArray,
                'questionsCount'=>$questionsCount,
                'timeForAds'=>$timeForAds,
                'todayDate'=>date('M d, Y'),
                'examDate'=>date('M d, Y',strtotime($exam_date)),
                'isEditableQuiz'=>$isEditableQuestion,
                'isAnsAdded'=>$isAnsAdded,
                'status' => 1,
                'message' => $this->lang->line('quiz_question_found')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        else
        {
            $this->response([
                'status' => 0,
                'message' => $this->lang->line('quiz_question_not_found'),
                'todayDate'=>date('M d, Y'),
                'examDate'=>date('M d, Y',strtotime($exam_date))                
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }    
    public function save_user_answer_post()
    {
        $exam_date = ($this->post('exam_date'))?$this->post('exam_date'):date('Y-m-d');

        //if($this->post('user_id') !="" && $this->post('question_id') !="" && $exam_date == date('Y-m-d'))
        if($this->post('user_id') !="" && $this->post('question_id') !="")
        {
            $userAnswerData[] = array(
                'user_id'=>$this->post('user_id'),
                'question_id'=>$this->post('question_id'),
                'question_answer_id'=>($this->post('question_answer_id'))?$this->post('question_answer_id'):null,
                'latitude'=>($this->post('latitude'))?$this->post('latitude'):0,
                'longitude'=>($this->post('longitude'))?$this->post('longitude'):0,
                'created_date'=>date("Y-m-d H:i:s")
            );
            $user_answer_id = $this->api_model->deleteInsertRecord('user_answer_master',$userAnswerData,array('user_id'=>$this->post('user_id'),'question_id'=>$this->post('question_id')));                                    
            if($this->post('current_quiz_question'))
            {
                $this->api_model->updateUser('user_master',array('current_quiz_question'=>$this->post('current_quiz_question'),'current_quiz_question_date'=>date("Y-m-d H:i:s")),'user_id',$this->post('user_id'));                                
            }
            else
            {
                $this->api_model->updateUser('user_master',array('current_quiz_question'=>null,'current_quiz_question_date'=>null),'user_id',$this->post('user_id'));                                                
            }

            $this->response([
                'status' => 1,
                'message' => $this->lang->line('user_quiz_ans_save')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code            
        }        
        else
        {
            $this->response([
                'status' => 0,
                'message' => $this->lang->line('user_quiz_ans_not_save')
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }    
    public function get_user_quiz_history_post()
    {
        if($this->post('user_id') !="")
        {
            //save user screen record
            $this->api_model->addRecord('user_screen_log',array('user_id'=>$this->post('user_id'),'api_name'=>'get_user_quiz_history','created_date'=>date('Y-m-d H:i:s')));
            //$display_per_page = ($this->post('display_per_page') >0)?$this->post('display_per_page'):perPage;
            $userQuizHistoryCount = $this->api_model->getUserQuizHistoryCount($this->post('user_id'));
            
            //$userQuizHistoryData = $this->api_model->userQuizHistoryData($this->post('user_id'),$display_per_page,$this->post('page_no'));                
            $userQuizHistoryData = $this->api_model->userQuizHistoryData($this->post('user_id'));                
            if(!empty($userQuizHistoryData))
            {
                $this->response([
                    'user_quiz_history_count'=>$userQuizHistoryCount,
                    'user_quiz_history_data'=>$userQuizHistoryData,
                    'status' => 1,
                    'message' => $this->lang->line('user_quiz_history_found')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code                            
            }
            else
            {
                $this->response([
                    'user_quiz_history_count'=>$userQuizHistoryCount,
                    'user_quiz_history_data'=>$userQuizHistoryData,
                    'status' => 1,
                    'message' => $this->lang->line('user_quiz_history_not_found')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code                                            
            }
        }        
        else
        {
            $this->response([
                'status' => 0,
                'message' => $this->lang->line('invalid_user')
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }    
    public function get_quiz_result_post()
    {
        if($this->post('exam_date') !="")
        {
            //save user screen record
            if($this->post('user_id'))
            {
                $this->api_model->addRecord('user_screen_log',array('user_id'=>$this->post('user_id'),'api_name'=>'get_quiz_result','use_date'=>$this->post('exam_date'),'created_date'=>date('Y-m-d H:i:s')));                
            }
    
            $display_per_page = ($this->post('display_per_page') >0)?$this->post('display_per_page'):perPage;

            $QuizResultCount = $this->api_model->getQuizResultCount($this->post('exam_date'));
            $QuizResultData = $this->api_model->getQuizResultData($this->post('exam_date'),$display_per_page,$this->post('page_no'));                
            
            if(!empty($QuizResultData))
            {
                $this->response([
                    'quiz_result_count'=>$QuizResultCount,
                    'quiz_result_data'=>$QuizResultData,
                    'status' => 1,
                    'message' => $this->lang->line('user_quiz_result_found')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code                            
            }
            else
            {
                $this->response([
                    'user_quiz_history_count'=>$QuizResultCount,
                    'quiz_result_data'=>$QuizResultData,
                    'status' => 1,
                    'message' => $this->lang->line('user_quiz_result_not_found')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code                                            
            }
        }        
        else
        {
            $this->response([
                'status' => 0,
                'message' => $this->lang->line('invalid_exam_date')
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    public function get_cms_data_post()
    {
        if($this->input->post("cms_slug"))
        {
            $cmsData = $this->api_model->getRecord('cms','cms_slug',$this->input->post("cms_slug"));
        }
        else
        {
            $cmsData = $this->api_model->getAllRecord('cms','*');            
        }
        if(!empty($cmsData))
        {
            $this->response([
                'cms_data'=>$cmsData,
                'status' => 1,
                'message' => $this->lang->line('content_found')
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
        else
        {
            $this->response([
                'status' => 0,
                'message' => $this->lang->line('content_not_found')
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }
    /*public function get_mvp_data_post()
    {
        $settingsData = $this->api_model->getAllRecord('settings','*');
        if(!empty($settingsData))
        {
            $this->response([
                'mvp_data'=>$settingsData,
                'status' => 1,
                'message' => $this->lang->line('mvp_content_found')
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
        else
        {
            $this->response([
                'status' => 0,
                'message' => $this->lang->line('mvp_content_not_found')
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }*/
    public function logout_post()
    {
        if($this->post('push_notification_token')!="" && $this->post('user_id') !="")
        {
            //$push_notification_token = $this->post('push_notification_token');
            $user_id = $this->post('user_id');        

            $userData = $this->api_model->getRecordMultipleWhere('user_master',array('user_id'=>$user_id));
            if($userData)
            {
                $data = array('push_notification_token'=>"");
                $this->api_model->updateUser('user_master',$data,'user_id',$user_id);
                $this->response(['status' => 1,'message' => $this->lang->line('user_logout_success')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            } 
            else 
            {
                $this->response([
                    'status' => 0,
                    'message' => $this->lang->line('invalide_email_deviceid')
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
            }            
        }
        else
        {
            $this->response([
                'status' => 0,
                'message' => $this->lang->line('invalide_email_deviceid')
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code            
        }
    }
//Feedback API
    public function feedback_msg_questions_list_post()
    {
        $feedback_question_date = ($this->post('feedback_question_date'))?$this->post('feedback_question_date'):date('Y-m-d');

        //save user screen record
        $this->api_model->addRecord('user_screen_log',array('user_id'=>$this->post('user_id'),'api_name'=>'feedback_msg_questions_list','use_date'=>$feedback_question_date,'created_date'=>date('Y-m-d H:i:s')));


        $checkFeedbackRecord = 0;
        if($this->post('user_id') !="")
        {
            $checkFeedbackRecord = $this->api_model->checkFeedbackAnsGiven($this->post('user_id'),$feedback_question_date);
        }

        $editFeedbackAnsStatus = 1;

        $allowDays = $this->api_model->getRecord('system_option','option_slug','feedback_edit_days');
        $feedback_allow_date = $feedback_question_date;

        if($allowDays->option_value !="" && $allowDays->option_value >0)
        {
            $feedback_allow_date = date('Y-m-d', strtotime($feedback_question_date. " + $allowDays->option_value days"));
        }

        //if($checkFeedbackRecord >0 && $feedback_question_date != date('Y-m-d'))
        if($feedback_allow_date < date('Y-m-d'))
        {
            $editFeedbackAnsStatus = 0;
        }
        $feedback_msg = $this->api_model->getFeedbackMSG();
        $feedback_questions_list = $this->api_model->getFeedbackQuestionList($this->post('user_id'),$feedback_question_date);        
        if(!empty($feedback_questions_list) || !empty($feedback_msg))
        {
            $this->response([
                    'editFeedbackAnsStatus'=>$editFeedbackAnsStatus,
                    'FeedbackMSG'=>$feedback_msg,
                    'feedback_questions_list'=>$feedback_questions_list,
                    'todayDate'=>date('M d, Y',strtotime($feedback_question_date)),
                    'status' => 1,
                    'message' => $this->lang->line('feedback_question_found')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        else
        {
            $this->response([
                'status' => 0,
                'message' => $this->lang->line('feedback_question_not_found')
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code
        }
    }      
    public function save_user_feedback_answer_post()
    {
        $feedback_question_date = ($this->post('feedback_question_date'))?$this->post('feedback_question_date'):date('Y-m-d');

        $allowDays = $this->api_model->getRecord('system_option','option_slug','feedback_edit_days');
        $feedback_allow_date = $feedback_question_date;
        if($allowDays->option_value !="" && $allowDays->option_value >0)
        {
            $feedback_allow_date = date('Y-m-d', strtotime($feedback_question_date. " + $allowDays->option_value days"));
        }

        if($this->post('user_id') !="" && $_POST['feedback_answers'] !="" && $feedback_allow_date >= date('Y-m-d'))
        {
            $userAnsArray = json_decode($_POST['feedback_answers'], true);
            $save_user_feedback_ans = array();
            if(!empty($userAnsArray))
            {
                foreach ($userAnsArray as $key => $value) 
                {
                    $questionData = $this->api_model->getRecord('feedback_question_master','feedback_question_id',$key);
                    if(!empty($questionData))
                    {
                        $SaveData = $this->api_model->getRecordMultipleWhere('feedback_user_answer',array('user_id'=>$this->post('user_id'),'feedback_question_id'=>$questionData->feedback_question_id,'feedback_question_date'=>$feedback_question_date));
                        if(empty($SaveData))
                        {
                            $save_user_feedback_ans[] = array(
                                'user_id'=>$this->post('user_id'),
                                'feedback_question_id'=>$questionData->feedback_question_id,
                                'feedback_question'=>$questionData->feedback_question,
                                'feedback_question_type'=>$questionData->feedback_question_type,
                                'feedback_user_answer'=>$value,
                                'feedback_question_date'=>$feedback_question_date,
                                'created_date'=>date('Y-m-d')
                            );                        
                        }
                        else
                        {
                            $update_user_feedback_ans[] = array(
                                'feedback_user_answer_id'=>$SaveData->feedback_user_answer_id,
                                'feedback_question_id'=>$questionData->feedback_question_id,
                                'feedback_question'=>$questionData->feedback_question,
                                'feedback_question_type'=>$questionData->feedback_question_type,
                                'feedback_user_answer'=>$value,
                                'feedback_question_date'=>$feedback_question_date
                            );                        
                        }
                    }
                }
                if(!empty($save_user_feedback_ans))
                {
                    $this->common_model->insertBatch('feedback_user_answer',$save_user_feedback_ans);                
                }
                if(!empty($update_user_feedback_ans))
                {
                    $this->common_model->updateBatch('feedback_user_answer',$update_user_feedback_ans,'feedback_user_answer_id');                
                }
                $this->response([
                    'status' => 1,
                    'message' => $this->lang->line('user_quiz_ans_save')], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code            
            }
            else
            {
                $this->response([
                    'status' => 0,
                    'message' => $this->lang->line('user_quiz_ans_not_save')
                ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code            
            }
        }
        else
        {
            $this->response([
                'status' => 0,
                'message' => $this->lang->line('user_quiz_ans_not_save')
            ], REST_Controller::HTTP_OK); // NOT_FOUND (404) being the HTTP response code            
        }
    }
}
