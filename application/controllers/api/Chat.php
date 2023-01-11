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

        $this->load->model("chat_model");
        $this->load->model("user_model");
		$this->load->model("model_productbranch","productbranch");

        date_default_timezone_set('Asia/Jakarta');
    }

    public function send_chat_post()
    {
        $code = $this->input->post('cod3');
        $data = array();
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                $product_id = $this->input->post('product_id');
                if(empty($product_id)) {
                    $product_id = 0;
                }
                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {

                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $post = array();
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();

                                $c = $this->chat_model->get_user_chats($user->ID);
                                if(!empty($c->id)) {
                                    $chatid = $c->id;
                                } else {
                                    $chatid = $this->chat_model->insertuserchat($user);
                                }

                                
                                $message = $this->common->nohtml($this->input->post("message"));

                                $fileid = 0;
                                if(isset($_FILES['image_file']['size']) && $_FILES['image_file']['size'] > 0) {
                                    $this->load->library("upload");
                                    // Upload image
                                    $this->upload->initialize(array(
                                    "upload_path" => FCPATH.'uploads/chat/images',
                                    "overwrite" => FALSE,
                                    "max_filename" => 10000,
                                    "encrypt_name" => TRUE,
                                    "remove_spaces" => TRUE,
                                    "allowed_types" => "png|gif|jpeg|jpg|JPG|GIF|PNG",
                                    "max_size" => $this->settings->info->file_size,
                                        )
                                    );

                                    if ( ! $this->upload->do_upload('image_file'))
                                    {
                                            $error = array('error' => $this->upload->display_errors());
                                            /*
                                            $this->template->jsonError(lang("error_95") . "<br /><br />" .
                                                $this->upload->display_errors());
                                                */
                                            $status = -1;
                                            $msg = lang("error_95") . "<br /><br />" .
                                            $this->upload->display_errors();
                                    }

                                    $data = $this->upload->data();


                                    $fileid = $this->chat_model->add_image(array(
                                        "file_name" => $data['file_name'],
                                        "file_type" => $data['file_type'],
                                        "extension" => $data['file_ext'],
                                        "file_size" => $data['file_size'], 
                                        "user_id" => $user->ID,
                                        "timestamp" => time()
                                        )
                                    );
                                    // Update album count
                                    //$this->image_model->increase_album_count($albumid);
                                }

                                
                                // Video
                                $videoid=0;
                                if(!empty($youtube_url)) {
                                    $matches = array();
                                    preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $youtube_url, $matches);
                                    if(!isset($matches[0]) || empty($matches[0])) {
                                       // $this->template->jsonError(lang("error_96"));
                                        $status = -1;
                                        $msg = lang("error_96");
                                    }
                                    $youtube_id = $matches[0];
                                    // Add
                                    $videoid = $this->chat_model->add_video(array(
                                        "youtube_id" => $youtube_id,
                                        "user_id" => $user->ID,
                                        "timestamp" => time()
                                        )
                                    );
                                } elseif(isset($_FILES['video_file']['size']) && $_FILES['video_file']['size'] > 0) {
                                    $this->load->library("upload");
                                    // Upload image
                                    $this->upload->initialize(array(
                                    "upload_path" => FCPATH.'uploads/chat/videos',
                                    "overwrite" => FALSE,
                                    "max_filename" => 300,
                                    "encrypt_name" => TRUE,
                                    "remove_spaces" => TRUE,
                                    "allowed_types" => "avi|mp4|webm|ogv|ogg|3gp|flv|MP4|AVI",
                                    "max_size" => $this->settings->info->file_size,
                                        )
                                    );

                                    if ( ! $this->upload->do_upload('video_file'))
                                    {
                                            $error = array('error' => $this->upload->display_errors());
                                            $status = -1;
                                            $msg = lang("error_97") . "<br /><br />" .
                                            $this->upload->display_errors() . "<br />" . mime_content_type($_FILES['video_file']['tmp_name']);
                                            /*
                                            $this->template->jsonError(lang("error_97") . "<br /><br />" .
                                                $this->upload->display_errors() . "<br />" . mime_content_type($_FILES['video_file']['tmp_name']));
                                                */
                                    }

                                    $data = $this->upload->data();

                                    $videoid = $this->chat_model->add_video(array(
                                        "file_name" => $data['file_name'],
                                        "file_type" => $data['file_type'],
                                        "extension" => $data['file_ext'],
                                        "file_size" => $data['file_size'],
                                        "user_id" => $user->ID,
                                        "timestamp" => time()
                                        )
                                    );
                                }

                                if(empty($message) &&  $fileid == 0 && $videoid == 0) {
                                    $status = -1;
                                    $msg = lang("error_106");
                                }

                                if(!empty($user->ID)) {
                                   // Get message
                                   $replyid = $this->chat_model->add_chat_message($user,array(
                                    "chat_id" => $chatid,
                                    "from_id" => $user->ID,
                                    "message" => $message,
                                    "fileid"=>$fileid,
                                    "videoid"=>$videoid,
                                    "read"=>1,
                                    "product_id"=>$product_id
                                    )
                                );

                                $this->chat_model->update_chat($chatid, array(
                                    "last_reply_userid" => $user->ID,
                                    "last_message" => $message
                                    )
                                );


                                $this->user_model->add_notification(array(
                                    "userid" => 0,
                                    "url" => $chatid,
                                    "timestamp" => time(),
                                    "message" => "New Message From $user->customer_name",
                                    "status" => 0,
                                    "fromid" => $user->ID,
                                    "username" => $user->username,
                                    "email" => "",
                                    "email_notification" => ""
                                    )
                                );
                                } else {
                                    $status = -1;
                                    $msg = "Message not send";
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

    public function chat_notif_post()
    {
        $code = $this->input->post('cod3');
        $data = array();
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                $count_notif = 0;
                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {

                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $post = array();
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();

                                $c = $this->chat_model->get_user_chats($user->ID);
                                if(!empty($c->id)) {
                                    $chatid = $c->id;
                                    $count_notif = $this->chat_model->get_chat_notif($chatid);
    
                                } else {
                                    $status = -1;
                                    $msg = "Wrong token";
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
                'notif'=>$count_notif,
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

    public function send_chat_admin_post()
    {
        $code = $this->input->post('cod3');
        $data = array();
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                $id = $this->input->post("id");
             
                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {

                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $post = array();
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();

                                $c = $this->chat_model->get_user_chats($id);
                                if(!empty($c)) {
                                    $chatid = $c->id;
                                } else {
                                    $chatid = $this->chat_model->insertuserchatbyadmin($id);
                                }

                                
                                $message = $this->common->nohtml($this->input->post("message"));

                                $fileid = 0;
                                if(isset($_FILES['image_file']['size']) && $_FILES['image_file']['size'] > 0) {
                                    $this->load->library("upload");
                                    // Upload image
                                    $this->upload->initialize(array(
                                    "upload_path" => $this->settings->info->upload_path,
                                    "overwrite" => FALSE,
                                    "max_filename" => 10000,
                                    "encrypt_name" => TRUE,
                                    "remove_spaces" => TRUE,
                                    "allowed_types" => "png|gif|jpeg|jpg|JPG|GIF|PNG",
                                    "max_size" => $this->settings->info->file_size,
                                        )
                                    );

                                    if ( ! $this->upload->do_upload('image_file'))
                                    {
                                            $error = array('error' => $this->upload->display_errors());
                                            /*
                                            $this->template->jsonError(lang("error_95") . "<br /><br />" .
                                                $this->upload->display_errors());
                                                */
                                            $status = -1;
                                            $msg = lang("error_95") . "<br /><br />" .
                                            $this->upload->display_errors();
                                    }

                                    $data = $this->upload->data();


                                    $fileid = $this->chat_model->add_image(array(
                                        "file_name" => $data['file_name'],
                                        "file_type" => $data['file_type'],
                                        "extension" => $data['file_ext'],
                                        "file_size" => $data['file_size'], 
                                        "user_id" => $user->ID,
                                        "timestamp" => time()
                                        )
                                    );
                                    // Update album count
                                    //$this->image_model->increase_album_count($albumid);
                                }

                                
                                // Video
                                $videoid=0;
                                if(!empty($youtube_url)) {
                                    $matches = array();
                                    preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $youtube_url, $matches);
                                    if(!isset($matches[0]) || empty($matches[0])) {
                                       // $this->template->jsonError(lang("error_96"));
                                        $status = -1;
                                        $msg = lang("error_96");
                                    }
                                    $youtube_id = $matches[0];
                                    // Add
                                    $videoid = $this->chat_model->add_video(array(
                                        "youtube_id" => $youtube_id,
                                        "user_id" => 0,
                                        "timestamp" => time()
                                        )
                                    );
                                } elseif(isset($_FILES['video_file']['size']) && $_FILES['video_file']['size'] > 0) {
                                    $this->load->library("upload");
                                    // Upload image
                                    $this->upload->initialize(array(
                                    "upload_path" => $this->settings->info->upload_path,
                                    "overwrite" => FALSE,
                                    "max_filename" => 300,
                                    "encrypt_name" => TRUE,
                                    "remove_spaces" => TRUE,
                                    "allowed_types" => "avi|mp4|webm|ogv|ogg|3gp|flv|MP4|AVI",
                                    "max_size" => $this->settings->info->file_size,
                                        )
                                    );

                                    if ( ! $this->upload->do_upload('video_file'))
                                    {
                                            $error = array('error' => $this->upload->display_errors());
                                            $status = -1;
                                            $msg = lang("error_97") . "<br /><br />" .
                                            $this->upload->display_errors() . "<br />" . mime_content_type($_FILES['video_file']['tmp_name']);
                                            /*
                                            $this->template->jsonError(lang("error_97") . "<br /><br />" .
                                                $this->upload->display_errors() . "<br />" . mime_content_type($_FILES['video_file']['tmp_name']));
                                                */
                                    }

                                    $data = $this->upload->data();

                                    $videoid = $this->chat_model->add_video(array(
                                        "file_name" => $data['file_name'],
                                        "file_type" => $data['file_type'],
                                        "extension" => $data['file_ext'],
                                        "file_size" => $data['file_size'],
                                        "user_id" => 0,
                                        "timestamp" => time()
                                        )
                                    );
                                }

                                if(empty($message) &&  $fileid == 0 && $videoid == 0) {
                                    $status = -1;
                                    $msg = lang("error_106");
                                }

                                if(!empty($user->ID)) {
                                   // Get message
                                   $replyid = $this->chat_model->add_chat_message($user,array(
                                    "chat_id" => $chatid,
                                    "from_id" => 0,
                                    "message" => $message,
                                    "fileid"=>$fileid,
                                    "videoid"=>$videoid
                                    )
                                );

                                $this->chat_model->update_chat($chatid, array(
                                    "last_reply_userid" => 0,
                                    "last_message" => $message
                                    )
                                );

/*
                                $this->user_model->add_notification(array(
                                    "userid" => 0,
                                    "url" => $chatid,
                                    "timestamp" => time(),
                                    "message" => "New Message From CS",
                                    "status" => 0,
                                    "fromid" => 0,
                                    "username" => $user->username,
                                    "email" => "",
                                    "email_notification" => ""
                                    )
                                );
                                */
                                } else {
                                    $status = -1;
                                    $msg = "Message not send";
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

    public function detail_chat_post()
    {
        $code = $this->input->post('cod3');
        $data = array();
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');

                $limit = $this->input->post("limit");
                $page = $this->input->post("page");
                if(empty($page)) {
                    $page = 0;
                } else {
                    $page = $page-1;
                }

                $page = $page*$limit;
                
                
                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {

                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $post = array();
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();

                              
                                    $c = $this->chat_model->get_user_chats($user->ID);
                                    if(!empty($c)) {
                                        $cabang = $user->cabang;
                                        
                                        $chatid = $c->id;

                                        $this->chat_model->updateuserread($chatid);

                                        $messages = $this->chat_model->get_chat_messages($chatid, $limit,$page,$user);
                                        foreach($messages as $r) {
                                            $r->format_date = date('d F Y H:i A', strtotime($r->dt_created));
                                            //$r->avatar = base_url() . $this->settings->info->upload_path_relative . "/" . $r->avatar;
                                            $r->format_time = $this->common->get_time_string_simple($this->common->convert_simple_time(strtotime($r->dt_created)));
                                            $r->chatid = $chatid;
                                            if(!empty($r->image_file_name)) {
                                                $r->image_file_name = base_url('uploads') . "/chat/images/" . $r->image_file_name;
                                            } else {
                                                $r->image_file_name = "";
                                            }
                                            $product = null;
                                            if(!empty($r->product_id)) {
                                                $p = $this->productbranch->get_product_detail($cabang,$r->product_id);
                                                $product_master_id = $p->product_master_id;
                                                $pp = $this->productbranch->get_price_min($product_master_id);

                                                $product_price = $pp->price_old;
                                                $product_discount_price = $pp->price;
                                                $product_image = "";
                                                if(!empty($p->banner_image)) {
                                                    $product_image = base_url() . $this->settings->info->upload_path_thumbs . "/" . $p->banner_image;
                                                }

                                                $product = array("product_name"=>$p->name,"product_img_url"=>$product_image,"product_id"=>$p->id,
                                            "product_price"=>harga($product_price),"product_discount_price"=>harga($product_discount_price));
                                            }
                                            $r->product = $product;
    
                                            if(!empty($r->video_file_name)) {
                                                $r->video_file_name = base_url('uploads') .  "/chat/videos/" . $r->video_file_name;
                                            } else {
                                                $r->video_file_name = "";
                                            }
    
                                            $data[] = $r;
                                        }
                                    } else {
                                        $data = null;
                                    }

                                   
                                   
                                //} End not friend
                
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


}
