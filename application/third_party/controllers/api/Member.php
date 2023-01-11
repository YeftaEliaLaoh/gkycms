<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Member extends REST_Controller
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
        $this->load->model("admin_model");
		$this->load->model("home_model");
        $this->load->model("register_model");
        $this->load->helper('email');
        $this->load->model("feed_model");
		$this->load->model("image_model");
		$this->load->model("page_model");
    }

    function checkGambar($site_url,$val,$path)
    {
        $banner_img = "";
        if(!empty($val)) {
            $url = $site_url.$path.$val;
            
            $banner_img = $url;
        } 
        return $banner_img;
    }

    public function login_post()
    {
        $code = $this->input->post('cod3');
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $email =  $this->input->post('email');
                $pass = $this->input->post('pass');
                
                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($email) && !empty($pass)) {
                        if (!ctype_alnum($pass)){
                            $status = -3;
                            $msg = lang("error_28");
                        
                        }
                        else{
                            $status = 1;
                            $msg = "success";

                            $login = $this->login_model->getUserByEmail($email);
                            if ($login->num_rows() == 0) {
                                $login = $this->login_model->getUserByUsername($email);
                                if($login->num_rows() == 0) {
                                    $this->login_protect($email);
                                    //$this->template->error(lang("error_29"));
                                    $status = -1;
                                    $msg = lang("error_29");
                                }
                            } else {
                                $r = $login->row();
                                $userid = $r->ID;
                                $email = $r->email;

                                if($this->settings->info->secure_login) {
                                    // Generate a token
                                    $token = rand(1,100000) . $email;
                                    $token = md5(sha1($token));
                        
                                    // Store it
                                    $this->login_model->updateUserToken($userid, $token);
                                } else {
                                    if(empty($r->token)) {
                                        // Generate a token
                                        $token = rand(1,100000) . $email;
                                        $token = md5(sha1($token));
                        
                                        // Store it
                                        $this->login_model->updateUserToken($userid, $token);
                                    } else {
                                        if($r->online_timestamp + (3600*24*30*2) < time() ) {
                                                // Generate a token
                                            $token = rand(1,100000) . $email;
                                            $token = md5(sha1($token));
                        
                                            // Store it
                                            $this->login_model->updateUserToken($userid, $token);
                                        } else {
                                            $token = $r->token;
                                        }
                                    }
                                }

                                $details = array("email"=>$email,"userid"=>$userid,"token"=>$token,"username"=>$r->username,"first_name"=>$r->first_name,"last_name"=>$r->last_name,
                            "avatar"=>base_url("uploads/".$r->avatar),"themes"=>$r->themes_color);
                            }

                            $phpass = new PasswordHash(12, false);
                            if (!$phpass->CheckPassword($pass, $r->password)) {
                                $this->login_protect($email);
                                //$this->template->error(lang("error_29"));
                                $status = -1;
                                $msg = lang("error_29");
                            }
                            
                            if($this->settings->info->login_protect) {
                                // Check user for 5 login attempts
                                $s = $this->login_model->get_login_attempts($_SERVER['REMOTE_ADDR'], 
                                                $email, (15*60));
                                if($s->num_rows() > 0) {
                                    $s = $s->row();
                                    if($s->count >=5) {
                                        $status = -1;
                                        $msg = lang("error_68");
                                    }
                                }
                            }
                    
                            if($this->settings->info->activate_account) 
                            {
                                if(!$r->active) {
                                    $status = -1;
                                    $msg = lang("error_72") . "<a href='".
                                    site_url("register/send_activation_code/" . $r->ID . "/" .
                                     urlencode($r->email)).
                                    "'>".lang("error_73") ."</a> " . lang("error_74");
                                }
                            }
                    
                            

                        }
                    } else {
                        $status = -2;
                        $msg = "Empty Email Or Password";
                    }
                   
            }
            else {
                $msg = "Wrong Code";
                $status = -2;

            }
            
            if($status == 1) {
                $message = [
                    'status' => $status,
                    'message' => $msg,
                    'details' => $details
                ];
            } else {
                $message = [
                    'status' => $status,
                    'message' => $msg
                ];
            }
            
            $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
        }
        else {
            $this->response([
                'status' => FALSE,
                'message' => 'Wrong key'
                    ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
        
    }

    private function login_protect($email) 
	{
		if($this->settings->info->login_protect) {
			// Add Count
			$s = $this->login_model
				->get_login_attempts($_SERVER['REMOTE_ADDR'], 
					$email, (15*60));
			if($s->num_rows() > 0) {
				$s = $s->row();
				$this->login_model->update_login_attempt($s->ID, array(
					"count" => $s->count+1
					)
				);
			} else {
				$this->login_model->add_login_attempt(array(
					"IP" => $_SERVER['REMOTE_ADDR'],
					"username" => $email,
					"count" => 1,
					"timestamp" => time()
					)
				);
			}
		}
	}
    
    
    public function register_post()
    {
        $code = $this->input->post('cod3');
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $email = $this->input->post("email", true);
                $first_name = $this->common->nohtml(
                    $this->input->post("first_name", true));
                $last_name = $this->common->nohtml(
                    $this->input->post("last_name", true));
                $pass = $this->common->nohtml(
                    $this->input->post("password", true));
                $pass2 = $this->common->nohtml(
                    $this->input->post("password2", true));
                $username = $this->common->nohtml(
                    $this->input->post("username", true));


                    $status = 1;
                    $msg = "";
                
                    if (strlen($username) < 3) { 
                        $status = -1; 
                        $msg = lang("error_31");
                    }

                    if (!preg_match("/^[a-z0-9_]+$/i", $username)) {
                        $status = -1; 
                        $msg = lang("error_15");
                    }
        
                    if (!$this->register_model->check_username_is_free($username)) {
                        $status = -1; 
                        $msg = lang("error_16");
                    }
        
        
                    if ($pass != $pass2) {
                        $status = -1; 
                        $msg = lang("error_22");
                    } 
        
                    if (strlen($pass) <= 5) {
                        $status = -1; 
                        $msg = lang("error_17");
                    }
        
                    if (strlen($first_name) > 25) {
                        $status = -1; 
                        $msg = lang("error_56");
                    }
                    if (strlen($last_name) > 30) {
                        $status = -1; 
                        $msg = lang("error_57");
                    }
        
                    if (empty($first_name) || empty($last_name)) {
                        $status = -1; 
                        $msg = lang("error_58");
                    }
        
                    if (empty($email)) {
                        $status = -1; 
                        $msg = lang("error_18");
                    }
        
                    if (!valid_email($email)) {
                        $status = -1; 
                        $msg = lang("error_19");
                    }
        
                    if (!$this->register_model->checkEmailIsFree($email)) {
                        $status = -1; 
                        $msg = lang("error_20");
                    }
        
                    if ($status == 1) {
        
                        $pass = $this->common->encrypt($pass);
                        $active = 1;
                        $activate_code = "";
                        $success =  lang("success_20");
                        if($this->settings->info->activate_account) {
                            $active = 0;
                            $activate_code = md5(rand(1,10000000000) . "fhsf" . rand(1,100000));
                            $success = lang("success_33");
        
                            if(!isset($_COOKIE['language'])) {
                                // Get first language in list as default
                                $lang = $this->config->item("language");
                            } else {
                                $lang = $this->common->nohtml($_COOKIE["language"]);
                            }
        
                            // Send Email
                            $email_template = $this->home_model
                                ->get_email_template_hook("email_activation", $lang);
                            if($email_template->num_rows() == 0) {
                                $this->template->error(lang("error_48"));
                            }
                            $email_template = $email_template->row();
        
                            $email_template->message = $this->common->replace_keywords(array(
                                "[NAME]" => $username,
                                "[SITE_URL]" => site_url(),
                                "[EMAIL_LINK]" => 
                                    site_url("register/activate_account/" . $activate_code . 
                                        "/" . $username),
                                "[SITE_NAME]" =>  $this->settings->info->site_name
                                ),
                            $email_template->message);
        
                            $this->common->send_email($email_template->title,
                                 $email_template->message, $email);
                        }
        
                        $userid = $this->register_model->add_user(array(
                            "username" => $username,
                            "email" => $email,
                            "first_name" => $first_name,
                            "last_name" => $last_name,
                            "password" => $pass,
                            "user_role" => $this->settings->info->default_user_role,
                            "IP" => $_SERVER['REMOTE_ADDR'],
                            "joined" => time(),
                            "joined_date" => date("n-Y"),
                            "active" => $active,
                            "activate_code" => $activate_code,
                            )
                        );
        
                        // Check for any default user groups
                        $default_groups = $this->user_model->get_default_groups();
                        foreach($default_groups->result() as $r) {
                            $this->user_model->add_user_to_group($userid, $r->ID);
                        }
                    }
                   
            }
            else {
                $msg = "Wrong Code";
                $status = -2;

            }
            
            if($status == 1) {
                $login = $this->login_model->getUserByEmail($email);
                $r = $login->row();
                $userid = $r->ID;
                $email = $r->email;
                $token = rand(1,100000) . $email;
                $token = md5(sha1($token));

                // Store it
                $this->login_model->updateUserToken($userid, $token);
            
                $details = array("email"=>$email,"userid"=>$userid,"token"=>$r->token,"username"=>$r->username,"first_name"=>$r->first_name,"last_name"=>$r->last_name,
            "avatar"=>base_url("uploads/".$r->avatar));
                $message = [
                    'status' => $status,
                    'message' => $msg,
                    'details' => $details
                ];
            } else {
                $message = [
                    'status' => $status,
                    'message' => $msg
                ];
            }
           
            $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
        }
        else {
            $this->response([
                'status' => FALSE,
                'message' => 'Wrong key'
                    ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
        
    }

    public function provinces_get()
    {
        $status = 1;
        $prov = $this->home_model->provinces("")->result();
        $details = array();
        foreach($prov as $p) {
            $details[] = array("id"=>$p->id,"name"=>$p->name);
        }
        $message = [
            'status' => $status,
            'details' => $details
        ];    
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
        
    }

    public function themes_get()
    {
        $status = 1;
        $prov = $this->home_model->themes("")->result();
        $details = array();
        foreach($prov as $p) {
            $details[] = array("id"=>$p->id,"name"=>$p->name,"color"=>$p->color);
        }
        $message = [
            'status' => $status,
            'details' => $details
        ];    
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
        
    }

    public function city_get()
    {
        $prov_id = $this->input->get("id");
        $status = 1;
        $city = $this->home_model->city($prov_id)->result();
        $details = array();
        foreach($city as $p) {
            $details[] = array("id"=>$p->id,"name"=>$p->name);
        }
        $message = [
            'status' => $status,
            'details' => $details
        ];    
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
        
    }

    public function subdistrict_get()
    {
        $prov_id = $this->input->get("id");
        $status = 1;
        $city = $this->home_model->subdistrict($prov_id)->result();
        $details = array();
        foreach($city as $p) {
            $details[] = array("id"=>$p->id,"name"=>$p->name);
        }
        $message = [
            'status' => $status,
            'details' => $details
        ];    
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
        
    }

    public function profile_post()
    {
        $code = $this->input->post('cod3');
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                
                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {

                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $status = 1;
                                $msg = "success";
                                $a = $check->row();
                                $ch = $this->user_model->get_profile($a->ID);
                                $c = $ch->row();
                                $c->avatar = base_url("uploads/".$c->avatar);
                                $details = $c;
                                /*
                                $details = array("first_name"=>$c->first_name,"last_name"=>$c->last_name,"email"=>$c->email,"aboutme"=>$c->aboutme,
                                "username"=>$c->username,"avatar"=>base_url("uploads/".$c->avatar),"address"=>$c->address_1,"province"=>$c->province_name,"city"=>$c->city_name,
                                "subdistrict"=>$c->subdistrict_name,"province_id"=>$c->province,"city_id"=>$c->city,
                                "subdistrict_id"=>$c->subdistrict,"zipcode"=>$c->zipcode,"themes"=>$c->themes,"susbcribers"=>$c->subscribers);
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
            
            if($status == 1) {
                $message = [
                    'status' => $status,
                    'message' => $msg,
                    'details' => $details
                ];
            } else {
                $message = [
                    'status' => $status,
                    'message' => $msg
                ];
            }
            
            $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
        }
        else {
            $this->response([
                'status' => FALSE,
                'message' => 'Wrong key'
                    ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
        
    }

    public function edit_profile_post()
    {
        $code = $this->input->post('cod3');
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                
                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {

                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $status = 1;
                                $msg = "success";
                                $a = $check->row();
                                
                                $email = $this->input->post("email", true);
                                $first_name = $this->common->nohtml(
                                    $this->input->post("first_name", true));
                                $last_name = $this->common->nohtml(
                                    $this->input->post("last_name", true));
                                $aboutme = $this->common->nohtml(
                                    $this->input->post("aboutme", true));
                                $address = $this->common->nohtml(
                                    $this->input->post("address", true));
                                $province = $this->common->nohtml($this->input->post("province", true));
                                $city = $this->common->nohtml($this->input->post("city", true));
                                $subdistrict = $this->common->nohtml($this->input->post("subdistrict", true));
                                $zipcode = $this->common->nohtml($this->input->post("zipcode", true));
                                $themes = $this->common->nohtml($this->input->post("themes", true));

                                $profile_view = intval($this->input->post("profile_view"));
                                $posts_view = intval($this->input->post("posts_view"));
                                $post_profile = intval($this->input->post("post_profile"));
                                $allow_friends = intval($this->input->post("allow_friends"));
                                $allow_pages = intval($this->input->post("allow_pages"));
                                $chat_option = intval($this->input->post("chat_option"));
                                $tag_user = intval($this->input->post("tag_user"));
                                

                                    if (strlen($first_name) > 25) {
                                        $status = -1; 
                                        $msg = lang("error_56");
                                    }
                                    if (strlen($last_name) > 30) {
                                        $status = -1; 
                                        $msg = lang("error_57");
                                    }
                        
                                    if (empty($first_name) || empty($last_name)) {
                                        $status = -1; 
                                        $msg = lang("error_58");
                                    }

                        
                                    if ($status == 1) {
                                        $this->user_model->update_user($a->ID, array(
                                            "first_name" => $first_name, 
                                            "last_name" => $last_name,
                                            "aboutme" => $aboutme,
                                            "address_1" => $address,
                                            "city" => $city,
                                            "province" => $province,
                                            "subdistrict" => $subdistrict,
                                            "zipcode" => $zipcode,
                                            "themes" => $themes,
                                            "profile_view" => $profile_view,
                                            "posts_view" => $posts_view,
                                            "post_profile" => $post_profile,
                                            "allow_friends" => $allow_friends,
                                            "allow_pages" => $allow_pages,
                                            "chat_option" => $chat_option,
                                            "tag_user" => $tag_user
                                            )
                                        );
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
            
            if($status == 1) {
                $message = [
                    'status' => $status,
                    'message' => $msg
                ];
            } else {
                $message = [
                    'status' => $status,
                    'message' => $msg
                ];
            }
            
            $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
        }
        else {
            $this->response([
                'status' => FALSE,
                'message' => 'Wrong key'
                    ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
        
    }

    public function addpost_post()
    {
        $code = $this->input->post('cod3');
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                $type =  $this->input->post('type');
                $categoryid =  $this->input->post('category_id');
                
                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {

                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $status = 1;
                                $msg = "success";
                                $a = $check->row();
                                
                                $content = $this->common->nohtml($this->input->post("content"));
                                $image_url = $this->common->nohtml($this->input->post("image_url"));
                                $youtube_url = $this->common->nohtml($this->input->post("youtube_url"));

                                $targetid = intval($this->input->post("targetid"));
                                $target_type = $this->common->nohtml($this->input->post("target_type"));

                                $with_users = ($this->input->post("with_users"));
                                $post_as = $this->common->nohtml($this->input->post("post_as"));


                                $c = $this->common->get_user_tag_usernames($content);
                                $content = $c['content'];
                                $tagged_users = $c['users'];

                                $location = $this->common->nohtml($this->input->post("location"));

                                $users = array();
                                $user_flag = 0;
                                if(is_array($with_users)) {
                                    foreach($with_users as $username) {
                                        $username = $this->common->nohtml($username);
                                        $user = $this->user_model->get_user_by_username($username);
                                        if($user->num_rows() > 0) {
                                            $user_flag = 1;
                                            $user = $user->row();
                                            $users[] = $user;
                                        }
                                    }
                                }

                                if($target_type == "page_profile") {
                                    // Validate page
                                    $page = $this->page_model->get_page($targetid);
                                    if($page->num_rows() == 0) {
                                        $status = -1;
                                        $msg = lang("error_94");
                                        //$this->template->jsonError(lang("error_94"));
                                    }

                                }


                                $fileid = 0;
                                if(!empty($image_url)) {

                                    if($target_type == "page_profile") {
                                        // Check for default feed album
                                        $album = $this->image_model->get_page_feed_album($targetid);
                                        if($album->num_rows() == 0) {
                                            // Create
                                            $albumid = $this->image_model->add_album(array(
                                                "pageid" => $targetid,
                                                "feed_album" => 1,
                                                "name" => lang("ctn_646"),
                                                "description" => lang("ctn_647"),
                                                "timestamp" => time()
                                                )
                                            );
                                        } else {
                                            $album = $album->row();
                                            $albumid = $album->ID;
                                        }
                                    } else {
                                        // Check for default feed album
                                        $album = $this->image_model->get_user_feed_album($a->ID);
                                        if($album->num_rows() == 0) {
                                            // Create
                                            $albumid = $this->image_model->add_album(array(
                                                "userid" => $a->ID,
                                                "feed_album" => 1,
                                                "name" => lang("ctn_646"),
                                                "description" => lang("ctn_648"),
                                                "timestamp" => time()
                                                )
                                            );
                                        } else {
                                            $album = $album->row();
                                            $albumid = $album->ID;
                                        }
                                    }

                                    $fileid = $this->feed_model->add_image(array(
                                        "file_url" => $image_url,
                                        "userid" => $a->ID,
                                        "timestamp" => time(),
                                        "albumid" => $albumid
                                        )
                                    );
                                    // Update album count
                                    $this->image_model->increase_album_count($albumid);

                                } elseif(isset($_FILES['image_file']['size']) && $_FILES['image_file']['size'] > 0) {
                                    $this->load->library("upload");
                                    // Upload image
                                    $this->upload->initialize(array(
                                    "upload_path" => $this->settings->info->upload_path,
                                    "overwrite" => FALSE,
                                    "max_filename" => 300,
                                    "encrypt_name" => TRUE,
                                    "remove_spaces" => TRUE,
                                    "allowed_types" => "png|gif|jpeg|jpg",
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

                                    if($target_type == "page_profile") {
                                        // Check for default feed album
                                        $album = $this->image_model->get_page_feed_album($targetid);
                                        if($album->num_rows() == 0) {
                                            // Create
                                            $albumid = $this->image_model->add_album(array(
                                                "pageid" => $targetid,
                                                "feed_album" => 1,
                                                "name" => lang("ctn_646"),
                                                "description" => lang("ctn_647"),
                                                "timestamp" => time()
                                                )
                                            );
                                        } else {
                                            $album = $album->row();
                                            $albumid = $album->ID;
                                        }
                                    } else {
                                        // Check for default feed album
                                        $album = $this->image_model->get_user_feed_album($a->ID);
                                        if($album->num_rows() == 0) {
                                            // Create
                                            $albumid = $this->image_model->add_album(array(
                                                "userid" => $a->ID,
                                                "feed_album" => 1,
                                                "name" => lang("ctn_646"),
                                                "description" => lang("ctn_648"),
                                                "timestamp" => time()
                                                )
                                            );
                                        } else {
                                            $album = $album->row();
                                            $albumid = $album->ID;
                                        }
                                    }


                                    $fileid = $this->feed_model->add_image(array(
                                        "file_name" => $data['file_name'],
                                        "file_type" => $data['file_type'],
                                        "extension" => $data['file_ext'],
                                        "file_size" => $data['file_size'],
                                        "userid" => $a->ID,
                                        "timestamp" => time(),
                                        "albumid" => $albumid
                                        )
                                    );
                                    // Update album count
                                    $this->image_model->increase_album_count($albumid);
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
                                    $videoid = $this->feed_model->add_video(array(
                                        "youtube_id" => $youtube_id,
                                        "userid" => $a->ID,
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
                                    "allowed_types" => "avi|mp4|webm|ogv|ogg|3gp|flv",
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

                                    $videoid = $this->feed_model->add_video(array(
                                        "file_name" => $data['file_name'],
                                        "file_type" => $data['file_type'],
                                        "extension" => $data['file_ext'],
                                        "file_size" => $data['file_size'],
                                        "userid" => $a->ID,
                                        "timestamp" => time()
                                        )
                                    );
                                }


                                if(empty($content) && $fileid == 0 && $videoid == 0) {
                                    //$this->template->jsonError(lang("error_98"));
                                    $status = -1;
                                    $msg = lang("error_98");
                                }

                                $site_flag = 0;
                                $url_matches = array();
                                preg_match_all('/[a-zA-Z]+:\/\/[0-9a-zA-Z;.\/\-?:@=_#&%~,+$]+/', 
                                    $content, $url_matches);

                                if(isset($url_matches[0])) {
                                    $url_matches = $url_matches[0];
                                }

                                // Hashtags
                                $hashtags = $this->common->get_hashtags($content);
                                
                                foreach($hashtags[0] as $r) {
                                    $r = trim($r);
                                    $tag = substr($r, 1, strlen($r));
                                    // Check it exists
                                    $tagi = $this->feed_model->get_hashtag($tag);
                                    if($tagi->num_rows() == 0) {
                                        $this->feed_model->add_hashtag(array(
                                            "hashtag" => $tag,
                                            "count" => 1
                                            )
                                        );
                                    } else {
                                        $tagi = $tagi->row();
                                        $this->feed_model->increment_hashtag($tagi->ID);
                                    }
                                }

                                // Get urls in post
                                $sites = array();
                                foreach($url_matches as $k=>$v) {
                                    $s = $this->common->get_url_details($v);
                                    
                                    if(is_array($s)) {
                                        $sites[] = $s;
                                        $site_flag = 1;
                                        // Replace url in content
                                        $content = str_replace($v, "", $content);
                                    }
                                }

                                if($target_type == "user_profile") {
                                    // Validate user
                                    $user = $this->user_model->get_user_by_id($targetid);
                                    if($user->num_rows() == 0) {
                                        //$this->template->jsonError(lang("error_85"));
                                        $status = -1;
                                        $msg = lang("error_85");
                                    }
                                    $user = $user->row();

                                    // Check the user's permissions
                                    $flags = $this->common->check_friend($a->ID, $user->ID);
                                    if( ($user->post_profile && ($a->ID == $user->ID 
                                        || $flags['friend_flag'])) || !$user->post_profile) {

                                    } else {
                                        //$this->template->jsonError(lang("error_99"));
                                        $status = -1;
                                        $msg = lang("error_99");
                                    }

                                    
                                    $postid = $this->feed_model->add_post(array(
                                        "userid" => $targetid,
                                        "content" => $content,
                                        "timestamp" => time(),
                                        "imageid" => $fileid,
                                        "videoid" => $videoid,
                                        "location" => $location,
                                        "user_flag" => $user_flag,
                                        "profile_userid" => $a->ID,
                                        "site_flag" => $site_flag,
                                        "type" => $type,
                                        "categoryid" => $categoryid
                                        )
                                    );
                                } elseif($target_type == "page_profile") {
                                    // Validate page

                                    $page = $this->page_model->get_page($targetid);
                                    if($page->num_rows() == 0) {
                                        //$this->template->jsonError(lang("error_94"));
                                        $status = -1;
                                        $msg = lang("error_94");
                                    }

                                    $page = $page->row();

                                    // Get page member
                                    $member = $this->page_model->get_page_user($page->ID, $a->ID);
                                    if($member->num_rows() == 0) {
                                        $member = null;
                                    } else {
                                        $member = $member->row();
                                    }

                                    if(!$this->common->has_permissions(array("admin", "page_admin"), $this->user)) {
                                        if($post_as == "user") {
                                            // fine
                                            if($page->posting_status == 1 && $member == null) {
                                               // $this->template->jsonError(lang("error_100"));
                                                $status = -1;
                                                $msg = lang("error_94");
                                            } elseif($page->posting_status == 0 && ($member == null || !$member->roleid)) {
                                                //$this->template->jsonError(lang("error_100"));
                                                $status = -1;
                                                $msg = lang("error_100");
                                            }

                                            $this->user_model->increase_posts($a->ID);
                                        } elseif($post_as == "page") {
                                            // check they are admin of page
                                            if(!isset($member->roleid)) {
                                                $status = -1;
                                                $msg = lang("error_100");
                                                //$this->template->jsonError(lang("error_100"));
                                            } elseif($member->roleid != 1) {
                                                //$this->template->jsonError(lang("error_100"));
                                                $status = -1;
                                                $msg = lang("error_100");
                                            }
                                            
                                        } else {
                                            //$this->template->jsonError(lang("error_100"));
                                            $status = -1;
                                            $msg = lang("error_100");
                                        }
                                    }
                                    
                                    $postid = $this->feed_model->add_post(array(
                                        "userid" => $a->ID,
                                        "pageid" => $targetid,
                                        "content" => $content,
                                        "timestamp" => time(),
                                        "imageid" => $fileid,
                                        "videoid" => $videoid,
                                        "location" => $location,
                                        "user_flag" => $user_flag,
                                        "hide_profile" => 1, // stops it showing up in feed and profile page,
                                        "post_as" => $post_as,
                                        "site_flag" => $site_flag,
                                        "type" => $type,
                                        "categoryid" => $categoryid
                                        )
                                    );
                                } else {
                                    $this->user_model->increase_posts($a->ID);
                                    $postid = $this->feed_model->add_post(array(
                                        "userid" => $a->ID,
                                        "content" => $content,
                                        "timestamp" => time(),
                                        "imageid" => $fileid,
                                        "videoid" => $videoid,
                                        "location" => $location,
                                        "user_flag" => $user_flag,
                                        "site_flag" => $site_flag,
                                        "type" => $type,
                                        "categoryid" => $categoryid
                                        )
                                    );
                                }

                                $this->feed_model->add_feed_subscriber(array(
                                    "postid" => $postid,
                                    "userid" => $a->ID
                                    )
                                );

                                foreach($sites as $site) 
                                {
                                    $this->feed_model->add_feed_site(array(
                                        "url" => $site['url'],
                                        "title" => $site['title'],
                                        "description" => $site['description'],
                                        "image" => $site['image'],
                                        "postid" => $postid
                                        )
                                    );
                                }

                                foreach($tagged_users as $user) {
                                    // Notification
                                    $this->feed_model->add_tagged_users(array(
                                        "userid" => $user->ID,
                                        "postid" => $postid
                                        )
                                    );
                                    $this->user_model->increment_field($user->ID, "noti_count", 1);
                                    $this->user_model->add_notification(array(
                                        "userid" => $user->ID,
                                        "url" => "home/index/3?postid=" . $postid,
                                        "timestamp" => time(),
                                        "message" => $a->first_name . " " . $a->last_name . lang("ctn_649"),
                                        "status" => 0,
                                        "fromid" => $a->ID,
                                        "username" => $user->username,
                                        "email" => $user->email,
                                        "email_notification" => $user->email_notification
                                        )
                                    );

                                    $this->feed_model->add_feed_subscriber(array(
                                        "postid" => $postid,
                                        "userid" => $user->ID
                                        )
                                    );
                                }

                                foreach($users as $user) {
                                    $this->feed_model->add_feed_users(array(
                                        "userid" => $user->ID,
                                        "postid" => $postid
                                        )
                                    );

                                    // Check user is not already added to subscriber feed
                                    $sub = $this->feed_model->get_feed_subscriber($postid, $user->ID);
                                    if($sub->num_rows() == 0) {
                                        $this->feed_model->add_feed_subscriber(array(
                                            "postid" => $postid,
                                            "userid" => $user->ID
                                            )
                                        );
                                    }

                                    // Notification
                                    $this->user_model->increment_field($user->ID, "noti_count", 1);
                                    $this->user_model->add_notification(array(
                                        "userid" => $user->ID,
                                        "url" => "home/index/3?postid=" . $postid,
                                        "timestamp" => time(),
                                        "message" => $a->first_name . " " . $a->last_name . " " . lang("ctn_650"),
                                        "status" => 0,
                                        "fromid" => $a->ID,
                                        "username" => $user->username,
                                        "email" => $user->email,
                                        "email_notification" => $user->email_notification
                                        )
                                    );
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
            
            if($status == 1) {
                /*
                $message = [
                    'status' => $status,
                    'message' => $msg,
                    'details' => $details
                ];
                */
                $message = [
                    'status' => $status,
                    'message' => $msg
                ];
            } else {
                $message = [
                    'status' => $status,
                    'message' => $msg
                ];
            }
            
            $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
        }
        else {
            $this->response([
                'status' => FALSE,
                'message' => 'Wrong key'
                    ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
        
    }

    public function editpost_post()
    {
        $code = $this->input->post('cod3');
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                $type =  $this->input->post('type');
                $categoryid =  $this->input->post('category_id');
                
                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {

                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();
                                
                                $id = intval($this->input->post("id"));
                               
                                $post = $this->feed_model->get_post($id, $user->ID);
                                if($post->num_rows() == 0) {
                                    $msg = lang("error_105");
                                }
                                $post = $post->row();

                                if($post->pageid > 0 && $post->post_as == "page") {
                                    // Anyone who is admin of page can modify the post
                                    $member = $this->page_model->get_page_user($post->pageid, $user->ID);
                                    if($member->num_rows() == 0) {
                                        if(!$this->common->has_permissions(array("admin", "post_admin"), $this->user)) {
                                            $msg = lang("error_109");
                                        }
                                    } else {
                                        $member = $member->row();
                                        if($member->roleid != 1) {
                                            if(!$this->common->has_permissions(array("admin", "post_admin"), $this->user)) {
                                                $msg = lang("error_109");
                                            }
                                        }
                                    }

                                } else {
                                    if($post->userid != $user->ID) {
                                        if(!$this->common->has_permissions(array("admin", "post_admin"), $this->user)) {
                                            $msg = lang("error_109");
                                        }
                                    }
                                }

                                $content = $this->common->nohtml($this->input->post("content"));
                                $location = $this->common->nohtml($this->input->post("location"));
                                $image_url = $this->common->nohtml($this->input->post("image_url"));
                                $youtube_url = $this->common->nohtml($this->input->post("youtube_url"));
                                $with_users = ($this->input->post("with_users"));

                                $c = $this->common->get_user_tag_usernames($content);
                                $content = $c['content'];
                                $tagged_users = $c['users'];

                                $users = array();
                                $user_flag = 0;
                                if(is_array($with_users)) {
                                    foreach($with_users as $username) {
                                        $username = $this->common->nohtml($username);
                                        $user = $this->user_model->get_user_by_username($username);
                                        if($user->num_rows() > 0) {
                                            $user_flag = 1;
                                            $user = $user->row();
                                            $users[] = $user;
                                        }
                                    }
                                }

                                $fileid = $post->imageid;
                                if(!empty($image_url)) {
                                    $fileid = $this->feed_model->add_image(array(
                                        "file_url" => $image_url,
                                        "userid" => $user->ID,
                                        "timestamp" => time()
                                        )
                                    );

                                } elseif(isset($_FILES['image_file']['size']) && $_FILES['image_file']['size'] > 0) {
                                    $this->load->library("upload");
                                    // Upload image
                                    $this->upload->initialize(array(
                                    "upload_path" => $this->settings->info->upload_path,
                                    "overwrite" => FALSE,
                                    "max_filename" => 300,
                                    "encrypt_name" => TRUE,
                                    "remove_spaces" => TRUE,
                                    "allowed_types" => "png|gif|jpeg|jpg",
                                    "max_size" => $this->settings->info->file_size,
                                        )
                                    );

                                    if ( ! $this->upload->do_upload('image_file'))
                                    {
                                            $error = array('error' => $this->upload->display_errors());

                                            $msg = lang("error_95") . "<br /><br />" .
                                                $this->upload->display_errors();
                                    }

                                    $data = $this->upload->data();

                                    $fileid = $this->feed_model->add_image(array(
                                        "file_name" => $data['file_name'],
                                        "file_type" => $data['file_type'],
                                        "extension" => $data['file_ext'],
                                        "file_size" => $data['file_size'],
                                        "userid" => $user->ID,
                                        "timestamp" => time()
                                        )
                                    );
                                }

                                // Video
                                $videoid=0;
                                if(!empty($youtube_url)) {
                                    $matches = array();
                                    preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $youtube_url, $matches);
                                    if(!isset($matches[0]) || empty($matches[0])) {
                                        $msg = lang("error_96");
                                    }
                                    $youtube_id = $matches[0];
                                    // Add
                                    $videoid = $this->feed_model->add_video(array(
                                        "youtube_id" => $youtube_id,
                                        "userid" => $user->ID,
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
                                    "allowed_types" => "avi|mp4|webm|ogv|ogg|3gp|flv",
                                    "max_size" => $this->settings->info->file_size,
                                        )
                                    );

                                    if ( ! $this->upload->do_upload('video_file'))
                                    {
                                            $error = array('error' => $this->upload->display_errors());

                                            $msg = lang("error_97") . "<br /><br />" .
                                                $this->upload->display_errors() . "<br />" . mime_content_type($_FILES['video_file']['tmp_name']);
                                    }

                                    $data = $this->upload->data();

                                    $videoid = $this->feed_model->add_video(array(
                                        "file_name" => $data['file_name'],
                                        "file_type" => $data['file_type'],
                                        "extension" => $data['file_ext'],
                                        "file_size" => $data['file_size'],
                                        "userid" => $user->ID,
                                        "timestamp" => time()
                                        )
                                    );
                                }

                                if(empty($content) && $fileid == 0 && $videoid == 0) $this->template->jsonError(lang("error_98"));

                                $this->feed_model->update_post($id, array(
                                    "content" => $content,
                                    "location" => $location,
                                    "imageid" => $fileid,
                                    "videoid" => $videoid,
                                    "user_flag" => $user_flag
                                    )
                                );

                                foreach($tagged_users as $user) {
                                    // Check the user wasn't already tagged
                                    $tag = $this->feed_model->get_feed_tag($id, $user->ID);
                                    if($tag->num_rows() == 0) {

                                        // Notification
                                        $this->feed_model->add_tagged_users(array(
                                            "userid" => $user->ID,
                                            "postid" => $id
                                            )
                                        );
                                        $this->user_model->increment_field($user->ID, "noti_count", 1);
                                        $this->user_model->add_notification(array(
                                            "userid" => $user->ID,
                                            "url" => "home/index/3?postid=" . $id,
                                            "timestamp" => time(),
                                            "message" => $user->first_name . " " . $user->last_name . " " . lang("ctn_649"),
                                            "status" => 0,
                                            "fromid" => $user->ID,
                                            "username" => $user->username,
                                            "email" => $user->email,
                                            "email_notification" => $user->email_notification
                                            )
                                        );

                                        // Check user is not already added to subscriber feed
                                        $sub = $this->feed_model->get_feed_subscriber($id, $user->ID);
                                        if($sub->num_rows() == 0) {
                                            $this->feed_model->add_feed_subscriber(array(
                                                "postid" => $id,
                                                "userid" => $user->ID
                                                )
                                            );
                                        }

                                    }

                                }

                                // Delete feed users
                                $this->feed_model->delete_feed_users($id);

                                $users = array_unique($users);

                                foreach($users as $user) {
                                    $this->feed_model->add_feed_users(array(
                                        "userid" => $user->ID,
                                        "postid" => $id
                                        )
                                    );

                                    // Check user is not already added to subscriber feed
                                    $sub = $this->feed_model->get_feed_subscriber($id, $user->ID);
                                    if($sub->num_rows() == 0) {
                                        $this->feed_model->add_feed_subscriber(array(
                                            "postid" => $id,
                                            "userid" => $user->ID
                                            )
                                        );
                                    }

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
            
            if($status == 1) {
                /*
                $message = [
                    'status' => $status,
                    'message' => $msg,
                    'details' => $details
                ];
                */
                $message = [
                    'status' => $status,
                    'message' => $msg
                ];
            } else {
                $message = [
                    'status' => $status,
                    'message' => $msg
                ];
            }
            
            $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
        }
        else {
            $this->response([
                'status' => FALSE,
                'message' => 'Wrong key'
                    ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
        
    }

    public function deletepost_post()
    {
        $code = $this->input->post('cod3');
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                
                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {

                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $status = 1;
                                $msg = "success";
                                $id = intval($this->input->post("id"));
                                $a = $check->row();
                                $post = $this->feed_model->get_post($id,$a->ID);
                                if($post->num_rows() == 0) {
                                    $this->template->jsonError(lang("error_105"));
                                }
                                $post = $post->row();

                                $this->user_model->decrease_posts($post->userid);

                                $this->feed_model->delete_post($id);
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

    public function loadfeedmember_post()
    {
        $code = $this->input->post('cod3');
        $posts = array();
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                
                    $warning = '';
                    $username = $this->input->post('username');
                    $page = $this->input->post('page');
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {

                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $post = array();
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();

                                $user2 = $this->user_model->get_user_by_username($username);
                                $user2 = $user2->row();
                                

                                
                                $flags = $this->common->check_friend($user->ID, $user2->ID);

                                if($user2->posts_view == 1 && $user2->ID != $user->ID) {
                                    // Only let's friends view profile.
                                    if(!$flags['friend_flag']) {
                                        $posts = array();
                                        exit();
                                    }
                                }



                                $post = $this->feed_model->get_user_posts_only($user2->ID, $page)->result();
                                foreach($post as $p) {
                                    if(!empty($p->image_file_name)) {
                                        $p->image_file_name = base_url() . $this->settings->info->upload_path_relative . "/" . $p->image_file_name;
                                    }
                                    if(!empty($p->video_file_name)) {
                                        $p->video_file_name = base_url() . $this->settings->info->upload_path_relative . "/" . $p->video_file_name;
                                    }
                                    
                                    $p->avatar = base_url() . $this->settings->info->upload_path_relative . "/" . $p->avatar;
                                    /*
                                    $posts[] = array("ID"=>$p->ID,"content"=>$p->content,"timestamp"=>$p->timestamp,"userid"=>$p->userid,"likes"=>$p->likes,
                                "comments"=>$p->comments,"location"=>$p->location,"image_file_name"=>$p->image_file_name,"image_file_url"=>$p->image_file_url,"imageid"=>$p->imageid,
                            "video_file_name"=>$p->video_file_name,"username"=>$p->username,"first_name"=>$p->first_name,"last_name"=>$p->last_name,"avatar"=>$p->avatar);
                            */
                                    $posts[] = $p;
                                }

                                if($user->ID != $user2->ID) {
                                    // Invite
                                    $this->page_model->add_page_invite(array(
                                        "userid" => $user->ID,
                                        "pageid" => $user2->ID,
                                        "timestamp" => time(),
                                        "fromid" => $user->ID
                                        )
                                    );
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
                'post' => $posts
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

    public function logvisitor_post()
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

                                $posts = $this->page_model->get_log_visitor($user->ID)->result();
                                
                
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
                'post' => $posts
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

    public function loadfeed_post()
    {
        $code = $this->input->post('cod3');
        $posts = array();
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                
                    $warning = '';
                    $username = $this->input->post('username');
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {

                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $post = array();
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();

                                $page = intval($this->input->get("page"));
		                        $post = $this->feed_model->get_home_feed_api($user, $page)->result();
                                //$post = $post->result();
                                foreach($post as $r) {
                                    $r->avatar = base_url() . $this->settings->info->upload_path_relative . "/" . $r->avatar;
                                    if(!empty($r->image_file_name)) {
                                        $r->image_file_name = base_url() . $this->settings->info->upload_path_relative . "/" . $r->image_file_name;
                                    }
                                    if(!empty($r->video_file_name)) {
                                        $r->video_file_name = base_url() . $this->settings->info->upload_path_relative . "/" . $r->video_file_name;
                                    }
                                    
                                    $posts[] = $r;
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
                'post' => $posts
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

    public function like_post()
    {
        $code = $this->input->post('cod3');
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

                                $post = $this->feed_model->get_post($id,$user->ID);
                                if($post->num_rows() == 0) {
                                    $msg =  lang("error_105");
                                }

                                $post = $post->row();

                                //$this->check_post_permission($post);
                                // Check user hasn't already liked the post
                                $like = $this->feed_model->get_post_like($id, $user->ID);
                                if($like->num_rows() > 0) {
                                    // Unlike
                                    $like  = $like->row();
                                    $likes = $post->likes - 1;
                                    $this->feed_model->update_post($id, array(
                                        "likes" => $likes
                                        )
                                    );

                                    $this->feed_model->delete_like_post($like->ID);

                                    $data = array(
                                        "likes" => $likes,
                                        "like_status" => false
                                    );
                                } else {
                                    $likes = $post->likes + 1;
                                    $this->feed_model->update_post($id, array(
                                        "likes" => $likes
                                        )
                                    );

                                    $this->feed_model->add_post_like(array(
                                        "userid" => $user->ID,
                                        "postid" => $id,
                                        "timestamp" => time()
                                        )
                                    );

                                    $data = array(
                                        "likes" => $likes,
                                        "like_status" => true
                                    );

                                    if($post->userid > 0) {
                                        $this->user_model->increment_field($post->userid, "noti_count", 1);
                                        $this->user_model->add_notification(array(
                                            "userid" => $post->userid,
                                            "url" => "home/index/3?postid=" . $post->ID,
                                            "timestamp" => time(),
                                            "message" => $user->first_name . " " . $user->last_name . " " . lang("ctn_651"),
                                            "status" => 0,
                                            "fromid" => $user->ID,
                                            "username" => $post->username,
                                            "email" => $post->email,
                                            "email_notification" => $post->email_notification
                                            )
                                        );
                                    }

                                }
                                //$post = $post->result();
                                
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
                'post' => $data
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

    public function get_feedcommentsreplies_post() 
	{
        $code = $this->input->post('cod3');
        $com = array();
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

                                
                               // $id = intval($id);
                                $comment = $this->feed_model->get_comment($id);
                                if($comment->num_rows() == 0) {
                                    $status = -1;
                                    $msg = lang("error_108");
                                }
                                $comment = $comment->row();

                                $post = $this->feed_model->get_post($comment->postid,$user->ID);
                                if($post->num_rows() == 0) {
                                    $status = -1;
                                    $msg = lang("error_105");
                                } 

                                $post = $post->row();

                                $replies = $this->feed_model->get_comment_replies($id, $user->ID, 0);

                                $com = array();
                                if($replies->num_rows() == 0) {
                                    $status = -1;
                                    $msg = lang("error_105");
                                } else {
                                    if(!empty($replies->result())) {
                                        foreach($replies->result() as $r) {
                                            $r->avatar = base_url() . $this->settings->info->upload_path_relative . "/" . $r->avatar;
                                            if(!empty($r->replies)) {
                                                $repl = $r->replies;
                                            } else {
                                                $repl = "";
                                            }
                                            $com[] = array("ID"=>$r->ID,"timestamp"=>$r->timestamp,"comment"=>$r->comment,"userid"=>$r->userid,"likes"=>$r->likes,"replies"=>$repl,
                                        "username"=>$r->username,"first_name"=>$r->first_name,"last_name"=>$r->last_name,"online_timestamp"=>$r->online_timestamp,"avatar"=>$r->avatar);
                                        }
                                    }
                                    
                                }
                                
                                
                                
                                //$com = array_reverse($com);
                        

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
                'post' => $com
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

    public function get_feedcomments_post() 
	{
        $code = $this->input->post('cod3');
        $com = array();
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

                                $post = $this->feed_model->get_post($id,$user->ID);
                                if($post->num_rows() == 0) {
                                    $status = -1;
                                    $msg = lang("error_105");
                                    $com = array();
                                } else {
                                    $post = $post->row();
                        
                                    $page = 0;
                            
                                    $comments = $this->feed_model->get_feed_comments($id, $user->ID, $page);
                                    $com = array();
                                    foreach($comments->result() as $r) {
                                       // $com[] = $r;
                                       if(!empty($r->replies)) {
                                           $repl = $r->replies;
                                       } else {
                                           $repl = "";
                                       }
                                       $r->avatar = base_url() . $this->settings->info->upload_path_relative . "/" . $r->avatar;
                                        $com[] = array("ID"=>$r->ID,"timestamp"=>$r->timestamp,"comment"=>$r->comment,"userid"=>$r->userid,"likes"=>$r->likes,"replies"=>$repl,
                                    "username"=>$r->username,"first_name"=>$r->first_name,"last_name"=>$r->last_name,"online_timestamp"=>$r->online_timestamp,"avatar"=>$r->avatar);
                                    }
                                }
                               
                        
                                //$com = array_reverse($com);
                        

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
                'post' => $com
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

    public function get_subscribers_post() 
	{
        $code = $this->input->post('cod3');
        $list = array();
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

                                $lists = $this->user_model->get_subscribers($user->ID)->result();
                                foreach($lists as $l) {
                                    $l->avatar = base_url() . $this->settings->info->upload_path_relative . "/" . $l->avatar;
                                    $list[] = $l;
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
                'list' => $list
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

    public function get_subscribepost_post()
    {
        $code = $this->input->post('cod3');
        $data = array();
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

                                $post = $this->feed_model->get_user_saved_post($user->ID)->result();
                                foreach($post as $p) {
                                    $data[] = $p;
                                }
                                
                                //$post = $post->result();
                                
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
                'post' => $data
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

    public function deleteaccount_post()
    {
        $code = $this->input->post('cod3');
        $data = "failed";
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
                        
                                $this->user_model->delete_user($user->ID);
                                // Delete user from user group
                                $this->admin_model->delete_user_from_all_groups($user->ID);

                                $this->user_model->delete_user_feed_like($user->ID);
                                $this->user_model->delete_user_feed_item($user->ID);
                                $this->user_model->delete_user_subscriber($user->ID);
                                $this->user_model->delete_chat($user->ID);
                                $this->user_model->delete_user_comment($user->ID);
                                $this->user_model->delete_user_friendreq($user->ID);
                                $this->user_model->delete_user_friend($user->ID);
                                
                                //$post = $post->result();
                                
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
    
    public function subscribepost_post()
    {
        $code = $this->input->post('cod3');
        $data = "failed";
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


                                $saved = $this->feed_model->get_user_save_post($id, $user->ID);
                                if($saved->num_rows() == 0) {
                                    // Add
                                    $this->feed_model->add_saved_post(array(
                                        "userid" => $user->ID,
                                        "postid" => $id
                                        )
                                    );
                                    $data = "Save Post";
                                } else {
                                    $saved = $saved->row();
                                    $this->feed_model->delete_saved_post($saved->ID);
                                    $data = "Delete Post";
                                }
                                
                                
                                //$post = $post->result();
                                
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
                'type' => $data
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
    
    
    public function subscribe_post()
    {
        $code = $this->input->post('cod3');
        $data = "failed";
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


                                $p = $this->user_model->get_user_by_id($id)->row();
                                if(empty($p)) {
                                    $status = -1;
                                    $msg = "Wrong Username";
                                } else {
                                    $subs = $this->feed_model->get_user_subscriber($id, $user->ID);
                                
                                    if($subs->num_rows() > 0) {
                                        // Unsubscribe
                                        $subs  = $subs->row();
                                        $this->feed_model->decrease_subs($id);

                                        $this->feed_model->delete_user_subscriber($subs->ID);
                                        $data = "Unsubscribe";

                                    } else {
                                        // Subscribe
                                        $this->feed_model->increase_subs($id);

                                        $this->feed_model->add_user_subscriber(array(
                                            "userid" => $user->ID,
                                            "friendid" => $id,
                                            "timestamp" => time()
                                        ));


                                        if($p->ID > 0) {
                                            $this->user_model->increment_field($p->ID, "noti_count", 1);
                                            $this->user_model->add_notification(array(
                                                "userid" => $p->ID,
                                                "url" => "",
                                                "timestamp" => time(),
                                                "message" => $user->first_name . " " . $user->last_name . " " . "has subscribes you!",
                                                "status" => 0,
                                                "fromid" => $user->ID,
                                                "username" => $p->username,
                                                "email" => $p->email,
                                                "email_notification" => $p->email_notification
                                                )
                                            );
                                        }

                                        $data = "Subscribe";

                                    }

                                }
                                
                                
                                //$post = $post->result();
                                
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
                'type' => $data
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

    public function share_post()
    {
        $code = $this->input->post('cod3');
        $data = "failed";
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                $id =  $this->input->post('id');
                $content_s = $this->common->nohtml($this->input->post("content"));
                
                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {

                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();
                                
                                $p = $this->feed_model->get_feed_item($id)->row();
                                if(empty($p)) {
                                    $status = -1;
                                    $msg = "Wrong Feed";
                                } else {
                                    $check = $this->feed_model->check_feed_item($id,$user->ID)->row();
                                    if(!empty($check)) {
                                        $status = -1;
                                        $msg = "Duplicate Share Feed";
                                    } else {
                                        $postid = $this->feed_model->add_post(array(
                                            "userid" => $user->ID,
                                            "content" => $p->content,
                                            "share_comment" => $content_s,
                                            "timestamp" => time(),
                                            "imageid" => $p->imageid,
                                            "videoid" => $p->videoid,
                                            "location" => $p->location,
                                            "user_flag" => $p->user_flag,
                                            "profile_userid" => $p->profile_userid,
                                            "site_flag" => $p->site_flag,
                                            "type" => $p->type,
                                            "categoryid" => $p->categoryid,
                                            "share_pageid" => $id
                                            )
                                        );
                                        $this->feed_model->increase_shares($id);
                                    }
                                    
                                }
                               
                                //$post = $post->result();
                                
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

    public function postcommentreply_post()
    {
        $code = $this->input->post('cod3');
        $com = array();
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

                                $com = $this->feed_model->get_comment($id);
                                if($com->num_rows() == 0) {
                                    $status = -1;
                                    $msg = lang("error_107"). $id;
                                } else {
                                    $com = $com->row();

                                    $post = $this->feed_model->get_post($com->postid,$user->ID);
                                    if($post->num_rows() == 0) {
                                        $msg = lang("error_107");
                                    }
                                    $post = $post->row();

                                // $this->check_post_permission($post);

                                    $comment = $this->common->nohtml($this->input->post("comment"));

                                    if(empty($comment)) $msg = lang("error_106");

                                    $c = $this->common->get_user_tag_usernames($comment);
                                    $comment = $c['content'];
                                    $tagged_users = $c['users'];


                                    $hide_prev = intval($this->input->get("hide_prev"));

                                    $page = intval($this->input->post("page"));

                                    $replyid = $this->feed_model->add_comment(array(
                                            "commentid" => $com->ID,
                                            "userid" => $user->ID,
                                            "comment" => $comment,
                                            "timestamp" => time()
                                            )
                                        );
                            
                                    $reply_count = $com->replies+1;
                                    $this->feed_model->update_comment($id, array(
                                        "replies" => $reply_count
                                        )
                                    );
                            
                                    $this->feed_model->increment_post($com->postid);
                                    $comments = $com->comments+1;

                                    if($com->userid>0) {
                                        $this->user_model->increment_field($com->userid, "noti_count", 1);
                                        $this->user_model->add_notification(array(
                                            "userid" => $com->userid,
                                            "url" => "home/index/3?postid=" . $post->ID . "&commentid=" . $com->ID . "&replyid=" . $replyid,
                                            "timestamp" => time(),
                                            "message" => $user->first_name . " " . $user->last_name . " " . lang("ctn_654"),
                                            "status" => 0,
                                            "fromid" => $user->ID,
                                            "username" => $com->username,
                                            "email" => $com->email,
                                            "email_notification" => $com->email_notification
                                            )
                                        );
                                    }
                            
                                    $replies = $this->feed_model->get_comment_replies($id, $user->ID, 0);
                                    $coms = array();
                                    foreach($replies->result() as $r) {
                                        $coms[] = $r;
                                    }
                            
                                    $coms = array_reverse($coms);
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
                'post' => $com
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

    public function comment_post()
    {
        $code = $this->input->post('cod3');
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

                                $post = $this->feed_model->get_post($id,$user->ID);
                                if($post->num_rows() == 0) {
                                    $msg =  lang("error_105");
                                }
                                $post = $post->row();

                               // $this->check_post_permission($post);

                               $comment = $this->common->nohtml($this->input->post("comment"));

                               if(empty($comment)) $msg = lang("error_106");

                               $c = $this->common->get_user_tag_usernames($comment);
                               $comment = $c['content'];
                               $tagged_users = $c['users'];


                               $hide_prev = intval($this->input->get("hide_prev"));

                               $page = intval($this->input->post("page"));

                               $commentid = $this->feed_model->add_comment(array(
                                   "postid" => $post->ID,
                                   "userid" => $user->ID,
                                   "comment" => $comment,
                                   "timestamp" => time()
                                   )
                               );

                               $comments_count = $post->comments+1;
                               $this->feed_model->update_post($id, array(
                                   "comments" => $comments_count
                                   )
                               );

                               foreach($tagged_users as $user2) {
                               
                                   $this->user_model->increment_field($user2->ID, "noti_count", 1);
                                   $this->user_model->add_notification(array(
                                       "userid" => $user2->ID,
                                       "url" => "home/index/3?postid=" . $post->ID . "&commentid=". $commentid,
                                       "timestamp" => time(),
                                       "message" => $user->first_name . " " . $user->last_name . " " . lang("ctn_652"),
                                       "status" => 0,
                                       "fromid" => $user->ID,
                                       "username" => $user2->username,
                                       "email" => $user2->email,
                                       "email_notification" => $user2->email_notification
                                       )
                                   );
                               }

                               // Check user is not already added to subscriber feed
                               $sub = $this->feed_model->get_feed_subscriber($post->ID, $user->ID);
                               if($sub->num_rows() == 0) {
                                   $this->feed_model->add_feed_subscriber(array(
                                       "postid" => $post->ID,
                                       "userid" => $user->ID
                                       )
                                   );
                               }

                               // get subscribers
                               $subs = $this->feed_model->get_feed_subscribers($id);
                               foreach($subs->result() as $user2) {
                                   if($user2->ID != $user->ID) {
                                       $this->user_model->increment_field($user2->ID, "noti_count", 1);
                                       $this->user_model->add_notification(array(
                                           "userid" => $user2->ID,
                                           "url" => "home/index/3?postid=" . $id . "&commentid=" . $commentid,
                                           "timestamp" => time(),
                                           "message" => $user->first_name . " " . $user->last_name . " " . lang("ctn_653"),
                                           "status" => 0,
                                           "fromid" => $user->ID,
                                           "username" => $user2->username,
                                           "email" => $user2->email,
                                           "email_notification" => $user2->email_notification
                                           )
                                       );
                                   }
                               }

                               $comments = $this->feed_model->get_feed_comments($id, $user->ID, $page);
                               $com = array();
                               foreach($comments->result() as $r) {
                                   $com[] = $r;
                               }

                               $com = array_reverse($com);
                                
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
                'post' => $com
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

    public function notification_post()
    {
        $code = $this->input->post('cod3');
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

                                $posts = $this->user_model->get_notifications($user->ID)->result();
                                //$post = $post->result();
                                
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
                'post' => $posts
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

    public function listfriends_post() 
	{
		$code = $this->input->post('cod3');
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                
                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {

                            $check = $this->user_model->get_user_by_token($token);
                            $member = array();
                            if($check->num_rows() > 0) {
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();
                                $limit = 10000;
                                $list = $this->user_model->get_user_friends($user->ID, $limit)->result();
                                foreach($list as $l) {
                                    $l->avatar = base_url() . $this->settings->info->upload_path_relative . "/" . $l->avatar;
                                    $data[] = array("username"=>$l->username,"first_name"=>$l->first_name,"last_name"=>$l->last_name,"online_timestamp"=>$l->online_timestamp
                                ,"friendid"=>$l->friendid,"avatar"=>$l->avatar);
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
                'list' => $data
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

    public function searchpost_post() 
	{
        $code = $this->input->post('cod3');
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                
                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {

                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();

                                $query = $this->common->nohtml($this->input->post("query"));

                                $search = array();
                                
                                if(!empty($query)) {
                                    $feed = $this->feed_model->get_home_feed_search($user->ID,$query);
                                    if($feed->num_rows() == 0) {
                                    } else {
                                        foreach($feed->result() as $s) {
                                           
                                           // $s->url = site_url("profile/" . $r->username);
                                           // $array[] = $s;
                                           $search[] = array("content"=>$s->content,"username"=>$s->username,"avatar"=>$s->p_avatar,"type"=>$s->type);
                                        }
                                    }
                                    // Search pages
                                    
                                    
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
                'search' => $search
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


    public function search_post() 
	{
        $code = $this->input->post('cod3');
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                
                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {

                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();

                                $query = $this->common->nohtml($this->input->post("query"));

                                $search = array();
                                
                                if(!empty($query)) {
                                    $usernames = $this->user_model->get_user_by_name($query);
                                    if($usernames->num_rows() == 0) {
                                    } else {
                                        foreach($usernames->result() as $r) {
                                            $s = new STDClass;
                                            $s->label = $r->first_name ." " . $r->last_name;
                                            $s->type = "user";
                                            $s->value = $r->username;
                                            $s->avatar = base_url() . $this->settings->info->upload_path_relative . "/" . $r->avatar;
                                           // $s->url = site_url("profile/" . $r->username);
                                           // $array[] = $s;
                                           $search[] = array("full_name"=>$s->label,"username"=>$s->value,"avatar"=>$s->avatar,"type"=>$s->type);
                                        }
                                    }
                                    // Search pages
                                    
                                    $pages = $this->page_model->get_pages_by_name($query);
                                    if($pages->num_rows() == 0) {
                                    } else {
                                        foreach($pages->result() as $r) {
                                            if(!empty($r->slug)) {
                                                $slug = $r->slug;
                                            } else {
                                                $slug = $r->ID;
                                            }
                                            $s = new STDClass;
                                            $s->label = $r->name;
                                            $s->type = "page";
                                            $s->value = $r->name;
                                            $s->avatar = base_url() . $this->settings->info->upload_path_relative . "/" . $r->profile_avatar;
                                            //$s->url = site_url("pages/view/" . $slug);
                                            //$array[] = $s;
                                            $search[] = array("full_name"=>$s->label,"username"=>$s->value,"avatar"=>$s->avatar,"type"=>$s->type);
                                        }
                                    }
                                    
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
                'search' => $search
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
    

    public function member_profile_post() 
	{
        $code = $this->input->post('cod3');
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                
                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {

                            $check = $this->user_model->get_user_by_token($token);
                            $member = array();
                            if($check->num_rows() > 0) {
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();
                                $username = $this->input->post("username");
                                $username = $this->common->nohtml($username);

                                if(empty($username)) {
                                    $status = -1;
                                    $msg = lang("error_51");
                                }
                                
                                $role = $this->user_model->get_user_role($user->user_role);
                                if($role->num_rows() == 0) {
                                    $role = lang("ctn_46");
                                } else {
                                    $role = $role->row();
                                    $rolename = $role->name;
                                }
                        
                                if($role->banned) $this->template->error(lang("error_53"));
                        
                        
                                $groups = $this->user_model->get_user_groups($user->ID);
                                $fields = $this->user_model->get_custom_fields_answers(array(
                                    "profile" => 1), $user->ID);
                        
                                // Update profile views
                                $this->user_model->increase_profile_views($user->ID);
                        
                                $user_data = $this->user_model->get_user_data($user->ID);
                                if($user_data->num_rows() == 0) {
                                    $user_data = null;
                                } else {
                                    $user_data = $user_data->row();
                                }
                        
                                // check user is friend
                                $flags = $this->check_friend($user->ID, $user->ID);
                        
                                if($user->profile_view == 1 && $user->ID != $this->user->info->ID) {
                                    // Only let's friends view profile.
                                    if(!$flags['friend_flag']) {
                        
                                        $user->profile_header = "empty.png";
                                        $user->avatar = "default.png";
                                        /*
                        
                                        $this->template->loadContent("profile/empty.php", array(
                                            "user" => $user,
                                            "friend_flag" => $flags['friend_flag'],
                                            "request_flag" => $flags['request_flag'],
                                            ), 1
                                        );
                                        */
                                    }
                                }
                        
                                $relationship_user = null;
                                if($user->relationship_userid > 0) {
                                    $usern = $this->user_model->get_user_by_id($user->relationship_userid);
                                    if($usern->num_rows() > 0) {
                                        $usern = $usern->row();
                                        $relationship_user = $usern;
                                    }
                                }
                                
                        
                                $friends = $this->user_model->get_user_friends_sample($user->ID);
                                $albums = $this->image_model->get_user_albums_sample($user->ID);

                                $member = array(
                                    "user" => $user,
                                    "groups" => $groups,
                                    "rolename" => $role,
                                    "fields" => $fields,
                                    "user_data" => $user_data,
                                    "friend_flag" => $flags['friend_flag'],
                                    "request_flag" => $flags['request_flag'],
                                    "friends" => $friends,
                                    "albums" => $albums,
                                    "post_count" => 0,
                                    "relationship_user" => $relationship_user
                                );
                        
                        /*
                                $this->template->loadContent("profile/index.php", array(
                                    "user" => $user,
                                    "groups" => $groups,
                                    "rolename" => $role,
                                    "fields" => $fields,
                                    "user_data" => $user_data,
                                    "friend_flag" => $flags['friend_flag'],
                                    "request_flag" => $flags['request_flag'],
                                    "friends" => $friends,
                                    "albums" => $albums,
                                    "post_count" => 0,
                                    "relationship_user" => $relationship_user
                                    )
                                );
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
                'message' => $msg,
                'member' => $member
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
    
    private function check_friend($userid, $friendid) 
	{
		// check user is friend
		$friend_flag = 0;
		$request_flag = 0;
		$friend = $this->user_model->get_user_friend($userid, $friendid);
		if($friend->num_rows() > 0) {
			// Friends
			$friend_flag = 1;
		} else {
			// Check for a request
			$request = $this->user_model->check_friend_request($userid, $friendid);
			if($request->num_rows() > 0) {
				// Request sent
				$request_flag = 1;
			}
		}

		return array("friend_flag" => $friend_flag, "request_flag" => $request_flag);
    }
    
    public function addfriend_post() 
	{
		$code = $this->input->post('cod3');
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                $userid = $this->input->post('userid');
                
                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {

                            $check = $this->user_model->get_user_by_token($token);
                            $member = array();
                            if($check->num_rows() > 0) {
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();

                                $userid = intval($userid);
                                $user2 = $this->user_model->get_user_by_id($userid);
                                if($user2->num_rows() == 0) {
                                   $status = -1;
                                   $msg = lang("error_85");
                                }
                                $user2 = $user2->row();

                                if($user->ID == $user2->ID) {
                                    $status = -1;
                                    $msg = lang("error_141");
                                }


                                // Check they're not already friends
                                $friend = $this->user_model->get_user_friend($user->ID, $user2->ID);
                                if($friend->num_rows() > 0) {
                                    $status = -1;
                                    $msg = lang("error_142");
                                }

                                // Check user hasn't already sent a request
                                $request = $this->user_model->check_friend_request($user->ID, $user2->ID);
                                if($request->num_rows() > 0) {
                                    $status = -1;
                                    $msg = lang("error_143");
                                }

                                if($status == 1) {
                                    // Send request
                                    $this->user_model->add_friend_request(array(
                                        "userid" => $user->ID,
                                        "friendid" => $user2->ID,
                                        "timestamp" => time()
                                        )
                                    );

                                    // Notification
                                    $this->user_model->increment_field($user2->ID, "noti_count", 1);
                                    $this->user_model->add_notification(array(
                                        "userid" => $user2->ID,
                                        "url" => "user_settings/friend_requests",
                                        "timestamp" => time(),
                                        "message" => $user->first_name . " " . $user->last_name . " " . lang("ctn_660"),
                                        "status" => 0,
                                        "fromid" => $user->ID,
                                        "username" => $user2->username,
                                        "email" => $user2->email,
                                        "email_notification" => $user2->email_notification
                                        )
                                    );
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
    

    public function friendrequest_post() 
	{
		$code = $this->input->post('cod3');
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                
                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {

                            $check = $this->user_model->get_user_by_token($token);
                            $member = array();
                            if($check->num_rows() > 0) {
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();

                                $list = $this->user_model->get_friend_requests($user->ID)->result();


                                
                                
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
                'list' => $list
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
    
    public function friendacceptreject_post() 
	{
		$code = $this->input->post('cod3');
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                $id = $this->input->post('id');
                $type = $this->input->post('type');
                
                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {

                            $check = $this->user_model->get_user_by_token($token);
                            $member = array();
                            if($check->num_rows() > 0) {
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();
                                
                                $request = $this->user_model->get_friend_request($id, $user->ID);
                                if($request->num_rows() == 0) {
                                    $status = -1;
                                    $msg = lang("error_147");
                                }
                                $request = $request->row();
                                
                                if($status == 1) {
                                    if($type == 0) {
                                        // Reject
                                        $this->user_model->delete_friend_request($id);
                                        // Notification
                                        $this->user_model->increment_field($request->userid, "noti_count", 1);
                                        $this->user_model->add_notification(array(
                                            "userid" => $request->userid,
                                            "url" => "home/index",
                                            "timestamp" => time(),
                                            "message" => $user->first_name . " " . $user->last_name . " " . lang("ctn_663"),
                                            "status" => 0,
                                            "fromid" => $user->ID,
                                            "username" => $request->username,
                                            "email" => $request->email,
                                            "email_notification" => $request->email_notification
                                            )
                                        );
                                        $this->session->set_flashdata("globalmsg", lang("success_82"));
                                    } elseif($type == 1) {
                                        // Accept
                                        $this->user_model->delete_friend_request($id);
                                        // Notification
                                        $this->user_model->increment_field($request->userid, "noti_count", 1);
                                        $this->user_model->add_notification(array(
                                            "userid" => $request->userid,
                                            "url" => "home/index",
                                            "timestamp" => time(),
                                            "message" => $user->first_name . " " . $user->last_name . " " . lang("ctn_664"),
                                            "status" => 0,
                                            "fromid" => $user->ID,
                                            "username" => $request->username,
                                            "email" => $request->email,
                                            "email_notification" => $request->email_notification
                                            )
                                        );
                            
                                        $this->user_model->add_friend(array(
                                            "userid" => $request->userid,
                                            "friendid" => $user->ID,
                                            "timestamp" => time()
                                            )
                                        );
                            
                                        $this->user_model->add_friend(array(
                                            "friendid" => $request->userid,
                                            "userid" => $user->ID,
                                            "timestamp" => time()
                                            )
                                        );
                            
                                        // Now update the user's serialized friends list so we can get
                                        // the wall posts of all friends
                                        $friends = unserialize($user->friends);
                            
                                        $friends[] = $request->userid;
                            
                                        $this->user_model->update_user($user->ID, array(
                                            "friends" => serialize($friends)
                                            )
                                        );
                            
                                        // Update other user
                                        $user = $this->user_model->get_user_by_id($request->userid);
                                        if($user->num_rows() > 0) {
                                            $user = $user->row();
                                            $friends = unserialize($user->friends);
                            
                                            $friends[] = $user->ID;
                            
                                            $this->user_model->update_user($user->ID, array(
                                                "friends" => serialize($friends)
                                                )
                                            );
                                        }
                            
                            
                                       // $this->session->set_flashdata("globalmsg", lang("success_83"));
                                    }
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
    
    public function addalbum_post() 
	{
		$code = $this->input->post('cod3');
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                $name =  $this->input->post('name');
                $description =  $this->input->post('description');
                
                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {
                        $status = 1;
                        $msg = "success";

                        $check = $this->user_model->get_user_by_token($token);
                        $member = array();
                        if($check->num_rows() > 0) {
                            $status = 1;
                            $msg = "success";
                            $user = $check->row();

                            $this->image_model->add_album(array(
                                "userid" => $user->ID,
                                "name" => $name,
                                "description" => $description,
                                "timestamp" => time()
                                )
                            );
                            
                            
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

    public function listalbum_post() 
	{
		$code = $this->input->post('cod3');
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                
                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {
                        $status = 1;
                        $msg = "success";

                        $check = $this->user_model->get_user_by_token($token);
                        $member = array();
                        if($check->num_rows() > 0) {
                            $status = 1;
                            $msg = "success";
                            $user = $check->row();

                            $list = $this->image_model->get_user_albums_all($user->ID)->result();
                            
                            
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
                'list' => $list
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

    public function listphotobyalbum_post() 
	{
		$code = $this->input->post('cod3');
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                $id_album =  $this->input->post('id_album');
                
                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {
                        $status = 1;
                        $msg = "success";

                        $check = $this->user_model->get_user_by_token($token);
                        $member = array();
                        if($check->num_rows() > 0) {
                            $status = 1;
                            $msg = "success";
                            $user = $check->row();

                            $album = $this->image_model->get_user_album($id_album);

                            $album = $album->row();

                            $list = $this->image_model->get_album_images($album->ID, 0)->result();
                            
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
                'list' => $list
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
