<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Driver extends REST_Controller
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
        //$shop_id = "9352293";

        $this->load->model('model_users','users');
        $this->load->model('login_admin_model','login_model');
        $this->load->model('home_model');
        $this->load->model('model_cart','cart');
        $this->load->model('model_orders','orders');

        date_default_timezone_set('Asia/Jakarta');
    }



    public function update_status_delivering_post()
    {
        $code = $this->input->post('cod3');
        $post = array();
        $data = "";
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');  
                $id =  $this->input->post('id');  
                $status_order = 0;
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {
                            
                            $check = $this->login_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();
                                
                                if(!empty($user)) {
                                    $now = date("Y-m-d H:i:s");

                                    $b = $this->orders->get_order_id_from_do($id);
                                    $datadb = array("status_order"=>1,"delivery_timestamp"=>$now,"sj_id"=>$b->sj_id,"orders_id"=>$b->orders_id);
                                    $this->orders->update_status_delivery_order_detail($user,$id,$datadb);

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

            if($status == -2) {
                $posts = null;
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

    public function update_status_finished_post()
    {
        $code = $this->input->post('cod3');
        $post = array();
        $data = "";
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');  
                $id =  $this->input->post('id');  
                $status_order = 0;
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {
                            
                            $check = $this->login_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();
                                
                                if(!empty($user)) {
                                    $now = date("Y-m-d H:i:s");

                                    $b = $this->orders->get_order_id_from_do($id);
                                    $datadb = array("status_order"=>2,"finished_timestamp"=>$now,"sj_id"=>$b->sj_id,"orders_id"=>$b->orders_id);
                                    $this->orders->update_status_delivery_order_detail($user,$id,$datadb);

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

            if($status == -2) {
                $posts = null;
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

    public function reject_order_post()
    {
        $code = $this->input->post('cod3');
        $post = array();
        $data = "";
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');  
                $orderid =  $this->input->post('order_id'); 
                $reason =  $this->input->post('reason'); 
                $status_order = 0;
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {
                            
                            $check = $this->login_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();
                                
                                if(!empty($user)) {
                                    $data = $this->cart->get_order($orderid);
                                    if($data->status_order == 15) {
                                       // $items = $this->cart->get_items_order($orderid);
                                       // $a = $this->cart->send_create_order($data,$items);
                                        //$a = "Data success";
                                        $status = 1;
                                        $msg = "success";
                                        $this->orders->update_status_order_reject($orderid,17,$reason,$user);
                                    } else {
                                        $status = -1;
                                        $msg = "Order can not update before Verify by Sales Staff";
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

            if($status == -2) {
                $posts = null;
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


    public function status_apps_post()
    {
        $code = $this->input->post('cod3');
        $post = array();
        $date_stat_apps = array();
        $posts = array();
        $data = "";
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {

                        $date_stat_apps[] = array("id"=>30,"text"=>"30 Days");
                        $date_stat_apps[] = array("id"=>60,"text"=>"60 Days");
                        $date_stat_apps[] = array("id"=>90,"text"=>"90 Days");
                            
                            $check = $this->login_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();
                                
                                if(!empty($user)) {
                                    $cabang = $user->cabang;
                                    
                                    $post = $this->orders->get_status_apps_user($user,2);
                                    $posts[] = array("id"=>"1","text"=>"Pending","hex"=>"#cccccc");
                                    $posts[] = array("id"=>"2","text"=>"Delivering","hex"=>"#cccccc");
                                    $posts[] = array("id"=>"3","text"=>"Delivered","hex"=>"#cccccc");
                                    
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

            if($status == -2) {
                $posts = null;
            }

            $message = [
                'status' => $status,
                'message' => $msg,
                'data' => $posts,
                'date' => $date_stat_apps
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

    public function list_order_post()
    {
        $code = $this->input->post('cod3');
        $post = array();
        $data = "";
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token'); 
                $status_apps = $this->input->post('status_apps');
                $name =  $this->input->post('name');   
                $date = "2021-07-23";
                /* 
                $date_from =  $this->input->post('date_from');  
                $date_to =  $this->input->post('date_to');
               
                  
                $type = $this->input->post('type');             
                $period = $this->input->post('period');

                */
                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {
                            
                            $check = $this->login_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();
                                
                                if(!empty($user)) {
                                    $cabang = $user->cabang;
                                    $post = $this->orders->get_list_order_driver($user,$cabang,$status_apps,$date,$name);
                                    //var_dump($post);
                                    if(!empty($post)) {
                                        //$posts = $post;
                                        $posts = $post;
                                    } else {
                                        $posts = array();
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

            if($status == -2) {
                $posts = null;
            }

            $message = [
                'status' => $status,
                'message' => $msg,
                'data' => $posts
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


    public function detail_order_post()
    {
        $code = $this->input->post('cod3');
        $post = array();
        $data = "";
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token'); 
                $id = $this->input->post("id");


                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {
                            
                            $check = $this->login_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();
                                
                                if(!empty($user)) {
                                    $cabang = $user->cabang;
                                    $p = $this->orders->get_order_driver_detail($id);
                                    $posts = $p;
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

            if($status == -2) {
                $posts = null;
            }

            $message = [
                'status' => $status,
                'message' => $msg,
                'data' => $posts
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
