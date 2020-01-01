<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');
class Home extends CI_Controller {   
  public function __construct() {
    parent::__construct();        
    $this->load->library('form_validation');
    $this->load->model(config_item('admin_directory').'/home_model');    
  }
  public function index() {
    if ($this->session->userdata('is_admin_login')) {
      redirect(config_item('admin_base_url').'dashboard');
    } else {
      $this->load->view(config_item('admin_directory').'/login');
    }
  }  
  public function do_login() {
    if ($this->session->userdata('is_admin_login')) {     
      redirect(config_item('admin_base_url').'dashboard');
    } else {
      if($this->session->userdata('UserID') != "")
      {
        $this->session->set_flashdata('loginError', 'Merchant already login in this browser');
        redirect(config_item('admin_base_url').'home'); exit;                
      }
      else
      {          
        $user = $this->input->post('username');
        $password = $this->input->post('password');
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');
        if ($this->form_validation->run() == FALSE) {
          $this->load->view(config_item('admin_directory').'/login');
        } 
        else 
        {
          $salt = '5&JDDlwz%Rwh!t2Yg-Igae@QxPzFTSId';
          $enc_pass  = md5($salt.$password); 
          $this->db->where('email',$user);
          $this->db->where('password',$enc_pass);
          $this->db->where('user_type','Admin');
          $val = $this->db->get('user_master')->first_row();         
          if($val->Status!='0' && $val->email==$user) 
          {
            $this->session->set_userdata(
              array(
                'adminID' => $val->user_id,
                'adminFirstname' => $val->first_name,                            
                'adminLastname' => $val->last_name,                            
                'adminemail' => $val->email,                            
                'is_admin_login' => true,
                'UserType' => $val->user_type,
                'user_local_timezone'=>($this->input->post('user_local_timezone') != "")?$this->input->post('user_local_timezone'):'America/New_York'
              )
            );
              // remember ME
            $cookie_name = "adminAuth";
            if($this->input->post('rememberMe')==1){                    
                  $this->input->set_cookie($cookie_name, 'usr='.$user.'&hash='.$password, 60*60*24*5); // 5 days
                } else {
                  delete_cookie($cookie_name);
                }                
                redirect(config_item('admin_base_url').'dashboard');
              } 
              else if($val->Status=='0' && $val->email==$user)
              {                
                $data['loginError'] = $this->lang->line('login_deactivate');
                $this->load->view(config_item('admin_directory').'/login', $data);
              } 
              else 
              {
                $data['loginError'] = $this->lang->line('login_error');
                $this->load->view(config_item('admin_directory').'/login', $data);
              }
            }
          }
        }
      }

      public function forgotpassword(){
    // when click submit button
        if($this->input->post('Submit')=="Submit"){ 
          $this->form_validation->set_rules('email_address', 'Email', 'trim|required|valid_email');          
          if($this->form_validation->run()){
            $checkEx = $this->home_model->checkemailExist($this->input->post('email_address'));          
            if(!empty($checkEx))
            {
              // confirmation link
              $verificationCode = random_string('alnum', 20).$checkEx->user_id.random_string('alnum', 5);
              $confirmationLink = config_item('admin_base_url').'home/newpassword/'.$verificationCode;               
              // email message body
              $forgot_password_emailBody = '<html>
              <link href="https://fonts.googleapis.com/css?family=PT+Sans:400,400italic,700,700italic" rel="stylesheet" type="text/css">
              <body topmargin="0" leftmargin="0" marginheight="0" marginwidth="0" style="-webkit-font-smoothing: antialiased;width:100% !important;background:#fff;-webkit-text-size-adjust:none;font-family: PT Sans, sans-serif;">
              <table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#fff"><tr>
              <td padding-bottom:30px; border:1px solid #deecf3; width:100%; height:100%;" >
              <table width="533" cellpadding="0" cellspacing="0" border="0" align="center" class="table">
              <tr>
              <td width="533">        
              <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table">
              <tr>
              <td width="100%" class="logocell">                
              <img src="'.config_item('admin_assets_url').'img/logo-email-template.png" alt="'.$this->lang->line('site_title').' Logo" style="-ms-interpolation-mode:bicubic; width:220px; padding-bottom:10px;"><br>              
              </td>
              </tr>
              </table>
              <table width="533" cellpadding="25" cellspacing="0" border="0">
              <tr>
              <td style="border: 1px solid #E6CD66; padding:40px 30px; box-shadow:0 2px 3px #cccccc; border-radius: 4px; background-color:#fff;">
              <p style="font-size:14px; color:#343434; margin-bottom:10px;">Hi '.$checkEx->first_name.' '.$checkEx->last_name.',</p>              
              <p style="font-size:14px; color:#343434; margin-bottom:10px;">We received a request to reset the password associated with this email address. If you made this request, please follow the instructions below.
              Click the link below to reset your password using our secure server:</p>
              <a class="account-style" style="color:#064D81; font-size:14px; text-decoration:none; margin-top: 2px; display: inline-block;" href="'.$confirmationLink.'" target="blank">'.$confirmationLink.'</a>
              <p style="font-size:14px; color:#343434; margin-bottom:0px;">If you did not request to have your password reset you can safely ignore this email. Rest assured your account is safe.</p>';
              "</td>                         
              </tr>
              </table>            
              </table>
              </body>
              </html>";          

              $subject = sprintf($this->lang->line('forgotpassword_email_subject'),$this->lang->line('site_title'));          
              $this->load->library('email');  
              $config['charset'] = "utf-8";
              $config['mailtype'] = "html";
              $config['newline'] = "\r\n";      
              $this->email->initialize($config);  
              $this->email->from('noreply@overunderz.com', 'OverUnderz Quiz engine');  
              $this->email->to($this->input->post('email_address'));  
              $this->email->subject($subject);  
              $this->email->message($forgot_password_emailBody);            
              $this->email->send();          
              $this->home_model->updateVerificationCode($this->input->post('email_address'),$verificationCode,$checkEx->user_id);
              redirect(config_item('admin_base_url').'home/forgotpasswordsent');
              exit();
            }else{
          // if not exist then
              $this->session->set_flashdata('emailNotExist', $this->lang->line('email_not_exist'));
              redirect(config_item('admin_base_url').'home/forgotpassword'); 
            }
          }
        }
        
                
        $arr['MetaTitle'] = $this->lang->line('title_merchant_fogottPass').' | '.$this->lang->line('site_title');
        $arr['captchaData'] = $data;
        $this->load->view(config_item('admin_directory').'/forgot_password',$arr);  
      }

  // verify (unique code) when reach from mail
      public function newPassword($verification_code=NULL,$whichone = NULL) {  
    // when click submit button
        if($this->input->post('Submit')=="Submit"){
          $this->form_validation->set_rules('password', 'password', 'trim|required|min_length[8]');  
          $this->form_validation->set_rules('confirm_pass', 'Confirm Password', 'trim|required|min_length[8]|matches[password]');   
          $salt = '5&JDDlwz%Rwh!t2Yg-Igae@QxPzFTSId';
          $enc_pass  = md5($salt.$this->input->post('password'));
          if ($this->form_validation->run())
          {
            $changePswData = array(        
             'password'  => $enc_pass
           );
          $this->home_model->updateData($changePswData,'user_master',$this->input->post('verification_code')); // data,table,where
          $this->session->set_flashdata('PasswordChange', $this->lang->line('success_password_change'));        
          redirect(config_item('admin_base_url'));
          exit();
        }
      }
      $chkverify = $this->home_model->forgotEmailVerify($verification_code);

      if(!empty($chkverify)){
        $arr['verification_code'] = $verification_code;
        $arr['MetaTitle'] = $this->lang->line('title_merchant_password_assist').' | '.$this->lang->line('site_title');
        $this->load->view(config_item('admin_directory').'/new_password',$arr); 
      }else{
        $this->session->set_flashdata('verifyerr', $this->lang->line('invalid_url_verify'));
        redirect(config_item('admin_base_url').'home/not_found'); 
      }
    }
    public function forgotpasswordsent(){
      $this->load->view(config_item('admin_directory').'/forgot_passwordsent'); 
    }  
    public function logout() {
      $this->session->unset_userdata('adminID');
      $this->session->unset_userdata('adminFirstname');
      $this->session->unset_userdata('adminLastname');
      $this->session->unset_userdata('adminemail');
      $this->session->unset_userdata('is_admin_login');  
      $this->session->unset_userdata('UserType');  
      $this->session->sess_destroy();
      $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate, no-transform, max-age=0, post-check=0, pre-check=0");
      $this->output->set_header("Pragma: no-cache");
      redirect(config_item('admin_base_url').'home', 'refresh');
    }
    public function not_found()
    {
      $this->load->view('error_404');
    }
}