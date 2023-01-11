<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Master extends REST_Controller
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

    
    public function getcategory_post() 
	{
		$code = $this->input->post('cod3');
        if(isset($code)) {
           
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                $type =  $this->input->post('type');
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

                            $list = $this->page_model->get_postthread_category($type)->result();
                            
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

    public function listcategory_post() 
	{
		$code = $this->input->post('cod3');
        if(isset($code)) {
            $post = array();
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                $type =  $this->input->post('type');
                $categoryid =  $this->input->post('category_id');
                
                $page = intval($this->input->post("page"));
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

                            $list = $this->page_model->list_postthread_category($user->ID, $page,$categoryid)->result();
                            
                            foreach($list as $l) {
                                $image_file_name = $this->checkGambar(base_url(),$l->image_file_name,"uploads/");
                                $avatar = $this->checkGambar(base_url(),$l->avatar,"uploads/");
                                $datetime = date('d F Y H:i:s',$l->timestamp);
                                
                                $post[] = array("ID"=>$l->ID,"content"=>$l->content,"userid"=>$l->userid,"username"=>$l->username,"first_name"=>$l->first_name,"last_name"=>$l->last_name,
                                "avatar"=>$avatar,"timestamp"=>$l->timestamp,"likes"=>$l->likes,"comments"=>$l->comments,"datetime"=>$datetime,"image_file_name"=>$image_file_name,"image_file_url"=>$l->image_file_url,"video_file_name"=>$l->video_file_name);
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
                'post' => $post
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
