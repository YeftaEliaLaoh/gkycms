<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Chat extends REST_Controller
{

    private $allowed_img_types;

    function __construct()
    {
        parent::__construct();
        $this->methods['all_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['one_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['set_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['productDel_delete']['limit'] = 50; // 50 requests per hour per user/key
        $this->methods['convert_get']['limit'] = 500; // 500 requests per hour per user/key
       // $this->load->model(array('Api_model', 'admin/Products_model'));
        $this->allowed_img_types = $this->config->item('allowed_img_types');

        $this->load->model("login_model");
		$this->load->model("user_model");
		$this->load->model("home_model");
        $this->load->model("register_model");
        $this->load->helper('email');
        $this->load->model("feed_model");
		$this->load->model("image_model");
        $this->load->model("page_model");
        $this->load->model("chat_model");
    }

    
    public function list_chat_post()
    {
        $code = $this->input->post('cod3');
        $posts = array();
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                
                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {

                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $post = array();
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();
                                
                                $friends = $this->user_model->get_user_friends($user->ID, 10)->result();
                                foreach($friends as $r) {
                                    $r->avatar = base_url() . $this->settings->info->upload_path_relative . "/" . $r->avatar;
                                    $f_timestamp = $this->common->get_time_string_simple($this->common->convert_simple_time($r->online_timestamp));
                                    $data[] = array("ID"=>$r->friendid,"username"=>$r->username,"first_name"=>$r->first_name,"last_name"=>$r->last_name,
                                    "online_timestamp"=>$r->online_timestamp,"format_time"=>$f_timestamp,"avatar"=>$r->avatar);
                                }
                
                            } else {
                                $status = -2;
                                $msg = lang("error_85");

                            }
                            
                    } else {
                        $status = -2;
                        $msg = "Empty Token";
                    }
                   
                   
            }
            else {
                $msg = "Wrong Code";
                $status = -2;

            }

            $message = [
                'status' => $status,
                'message' => $msg,
                'chat' => $data
            ];
            /*
            
            if($status == 1) {
                
            } else {
                $message = [
                    'status' => $status,
                    'message' => $msg
                ];
            }
            */
            
            $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
        }
        else {
            $this->response([
                'status' => FALSE,
                'message' => 'Wrong key'
                    ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
        
    }

    public function detail_chat_post()
    {
        $code = $this->input->post('cod3');
        $data = array();
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                $id =  $this->input->post('id');
                
                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {

                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $post = array();
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();

                                $friendid = intval($id);
                                $chats = $this->chat_model->get_user_chats($user->ID);

                                $chatid = 0;
                                foreach($chats->result() as $r) {
                                    $user_count = $this->chat_model->get_user_count($r->chatid);
                                    if($user_count == 2) {
                                        // Look for friend
                                        $friend = $this->chat_model->get_chat_user($r->chatid, $friendid);
                                        if($friend->num_rows() > 0) {
                                            $chatid = $r->chatid;
                                            break;
                                        }
                                    }
                                }

                                $cht = $friend->result();
                                foreach($cht as $c) {
                                    $chatid = $c->chatid;
                                    
                                }

                                $limit = 1000;

                                $messages = $this->chat_model->get_chat_messages($chatid, $limit);
                                foreach($messages->result() as $r) {
                                    $r->avatar = base_url() . $this->settings->info->upload_path_relative . "/" . $r->avatar;
                                    $r->format_time = $this->common->get_time_string_simple($this->common->convert_simple_time($r->online_timestamp));
                                    $data[] = $r;
                                }

                
                            } else {
                                $status = -2;
                                $msg = lang("error_85");

                            }
                            
                    } else {
                        $status = -2;
                        $msg = "Empty Token";
                    }
                   
                   
            }
            else {
                $msg = "Wrong Code";
                $status = -2;

            }

            $message = [
                'status' => $status,
                'message' => $msg,
                'chat' => $data
            ];
            
            $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
        }
        else {
            $this->response([
                'status' => FALSE,
                'message' => 'Wrong key'
                    ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
        
    }

    public function send_chat_post()
    {
        $code = $this->input->post('cod3');
        $data = array();
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                $id =  $this->input->post('id');
                
                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {

                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $post = array();
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();

                                $friendid = intval($id);
                                $chats = $this->chat_model->get_user_chats($user->ID);

                                $chatid = 0;
                                foreach($chats->result() as $r) {
                                    $user_count = $this->chat_model->get_user_count($r->chatid);
                                    if($user_count == 2) {
                                        // Look for friend
                                        $friend = $this->chat_model->get_chat_user($r->chatid, $friendid);
                                        if($friend->num_rows() > 0) {
                                            $chatid = $r->chatid;
                                            break;
                                        }
                                    }
                                }

                                $chat = $this->chat_model->get_live_chat($chatid);
                                if($chat->num_rows() == 0) {
                                    $this->template->jsonError(lang("error_86"));
                                }
                                $chat = $chat->row();

                                // Check user is a member
                                $member = $this->chat_model->get_chat_user($chatid, $user->ID);
                                if($member->num_rows() == 0) {
                                    $msg = lang("error_87");
                                }
                                $member = $member->row();

                                $message = $this->common->nohtml($this->input->post("message"));
                               

                                if(empty($message)) {
                                    $msg = lang("error_88");
                                }

                                $replyid = $this->chat_model->add_chat_message(array(
                                    "chatid" => $chatid,
                                    "userid" => $user->ID,
                                    "message" => $message,
                                    "timestamp" => time()
                                    )
                                );

                                // Update all chat users of unread message
                                $this->chat_model->update_chat_users($chatid, array(
                                    "unread" => 1
                                    )
                                );

                                $this->chat_model->update_chat($chatid, array(
                                    "last_replyid" => $replyid,
                                    "last_reply_timestamp" => time(),
                                    "last_reply_userid" => $this->user->info->ID,
                                    "posts" => $chat->posts + 1
                                    )
                                );
/*
                                $limit = 1000;

                                $messages = $this->chat_model->get_chat_messages($chatid, $limit);
                                foreach($messages->result() as $r) {
                                    $r->avatar = base_url() . $this->settings->info->upload_path_relative . "/" . $r->avatar;
                                    $r->format_time = $this->common->get_time_string_simple($this->common->convert_simple_time($r->online_timestamp));
                                    $data[] = $r;
                                }
*/
                
                            } else {
                                $status = -2;
                                $msg = lang("error_85");

                            }
                            
                    } else {
                        $status = -2;
                        $msg = "Empty Token";
                    }
                   
                   
            }
            else {
                $msg = "Wrong Code";
                $status = -2;

            }

            $message = [
                'status' => $status,
                'message' => $msg
            ];
            
            $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
        }
        else {
            $this->response([
                'status' => FALSE,
                'message' => 'Wrong key'
                    ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
        
    }
    

}
