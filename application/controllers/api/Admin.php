<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Admin extends REST_Controller
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

        date_default_timezone_set('Asia/Jakarta');
    }

    public function login_post() {
        
        $email =  $this->input->post('email');
        $pass = $this->input->post('pass');
        $remember = $this->input->post('remember');
        // $nama = $this->input->post('nama_lengkap')
        $status = -1;
        $msg = "problem";
        
        if(!empty($email) && !empty($pass) ) { 
            $ip      = $_SERVER['REMOTE_ADDR'];
         
            $url = URL_API."admin/login";
//            $post = array("username"=>$email,"password"=>$pass);
            $post = array('username' => $email,'password' => $pass);
            $bry = postcurl($url,$post);
            
            $b = json_decode($bry);

            if(!empty($b->status)) {
                $status = 1;
                $msg = "success";

                $this->session->set_userdata(array('username'=>$email,'id'=>1,'level'=>'admin',"token"=>$b->token));
            } else {
                $status = -1;
                $msg = $b->message;
            }

        } else {
            $status = -1;
            $msg = "Missing Field";
        }

        
        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
        
    }

    public function forgot_post()
    {
        $details = array();        
        $email =  $this->input->post('email');
             
        $warning = '';
        // pastikan username dan password adalah berupa huruf atau angka.
        if(!empty($email)) {
           
                $status = 1;
                $msg = "success";

                $log = $this->login_model->getResetLog($_SERVER['REMOTE_ADDR']);
                if ($log->num_rows() > 0) {
                    $log = $log->row();
                    if ($log->timestamp+ 60*15 > time()) {
                        $status = -1;
                        $msg = lang("error_46");
                        /*
                        $this->template->error(
                            lang("error_46")
                        );
                        */
                    }
                }
        
                $this->login_model->addToResetLog($_SERVER['REMOTE_ADDR']);
        
                // Check for email
                $user = $this->login_model->getUserEmail($email);
                if ($user->num_rows() == 0) {

                    $status = -1;
                    $msg = lang("error_47");
                    /*
                    $this->template->error(
                        lang("error_47")
                    );
                    */
                }
                $user = $user->row();
        
                $token = rand(10000000,100000000000000000)
                . "HUFI9e9dvcwjecw8392klle@O(*388*&&£^^$$$";
        
                $token = sha1(md5($token));
        
                $this->login_model->updatetoken($user->id,$token);

                if(!isset($_COOKIE['language'])) {
                    // Get first language in list as default
                    $lang = $this->config->item("language");
                } else {
                    $lang = $this->common->nohtml($_COOKIE["language"]);
                }
                
                // Send Email
                $email_template = $this->home_model
                    ->get_email_template_hook("forgot_password", $lang);
                if($email_template->num_rows() == 0) {
                    $this->template->error(lang("error_48"));
                }
                $email_template = $email_template->row();
        
                $email_template->message = $this->common->replace_keywords(array(
                    "[NAME]" => $user->username,
                    "[SITE_URL]" => site_url(),
                    "[EMAIL_LINK]" => 
                        site_url("administrator/resetpw/" . $token . "/" . $user->id),
                    "[SITE_NAME]" =>  $this->settings->info->site_name
                    ),
                $email_template->message);
        
                $this->common->send_email($email_template->title,
                     $email_template->message, $email);
                
                $email_url = site_url("administrator/resetpw/" . $token . "/" . $user->id);

            
        } else {
            $status = -2;
            $msg = "Empty Email";
        }

        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code

        
    }

    public function list_order_old_post()
    {
        $code = $this->input->post('cod3');
        $post = array();
        $data = "";
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');  
                $date_from =  $this->input->post('date_from');  
                $date_to =  $this->input->post('date_to');
                $name =  $this->input->post('name');     
                $type = $this->input->post('type');             
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
                                    $post = $this->cart->get_list_order_admin($user,$date_from,$date_to,$name,$type);
                                    if(!empty($post)) {
                                        
                                        foreach($post as $p) {
                                            $img = "";
                                            if(!empty($p->banner_image)) {
                                                $img = base_url() . $this->settings->info->upload_path_thumbs . "/" . $p->banner_image;
                                            }
                                            $p->banner_image = $img;
                                            $total = $p->total;
                                            $format_total = harga($total);
                                           
                                            $status_order = cek_status_order_api($p->status_order,"");
                                            unset($p->detail_order);

                                            $p->status = $status_order;

                                            $p->date_buy = tgl_view($p->dt_created);
                                            
                                            $p->format_total = $format_total;
                                            
                                            $posts[] = $p;
                                        }
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

    public function detail_post()
    {
        $code = $this->input->post('cod3');
        $posts = array();
        //$data = array();
        $data = null;
        $posts = null;

        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                $order_id = $this->input->post('order_id');
                
                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {

                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $post = array();
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();
                                $length = array();
                                $thickness = array();
                                $color = array();
                                if(!empty($user)) {
                                    $cabang = $user->cabang;
                                    $p = $this->cart->get_detail_order($user,$order_id);
                                    
                                    
                                    if(!empty($p)) {

                                        $total = $p->total;
                                        $shipping_total = $p->shipping_total;
                                        $total_all = $total+$shipping_total;

                                        $tracking = 0;
                                        if($p->status_order == 2) {
                                            $tracking = 1;
                                        }

                                        $p->total_all = $total_all;
                                        $p->format_total = harga($total);
                                        $p->format_shipping_total = harga($shipping_total);
                                        $p->format_total_all = harga($total_all);
                                        $p->format_date = tgl_indoo($p->dt_created);
                                        $p->format_tgl_np = tgl_indoo($p->tgl_np); 
                                        $p->tracking = $tracking;       

                                        $items = $this->cart->get_detail_items($user,$order_id);
                                        $sj = $this->cart->get_detail_sj($user,$order_id);
                                        
                                        $p->items = $items;
                                        $p->sj = $sj;

                                        $posts = $p;

                                    } else {
                                        $posts = null;
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
        $posts = array();
        //$data = array();
        $data = null;
        $posts = null;

        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                $order_id = $this->input->post('order_id');
                
                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {

                            $check = $this->login_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $post = array();
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();
                                $length = array();
                                $thickness = array();
                                $color = array();
                                if(!empty($user)) {
                                    $cabang = $user->cabang;
                                    $p = $this->cart->get_detail_order_admin($user,$order_id);
                                    
                                    
                                    if(!empty($p)) {

                                        $total = $p->total;
                                        $shipping_total = $p->shipping_total;
                                        $total_all = $total+$shipping_total;
                                        

                                        $tracking = 0;
                                        if($p->status_order == 2) {
                                            $tracking = 1;
                                        }

                                        $cs = cek_status_order_api_new($p->status_order,"");
                                        $status_order_name = $cs['name'];
                                        $hex = $cs['hex'];
                                        //$p->total_all = $total_all;
                                        $gr_total = $total_all;

                                        $discount = "";

                                        if(!empty($p->voucher_discount)) {
                                            $perc = $p->voucher_discount;
                                            $a_perc = $total*$perc;
                                            $gr_total = $total-(round($a_perc/100))+$shipping_total;
                                            $discount = $perc.'%';
                                        //	$disc_perc = $vs->discount_percentage;
                                        }
                                        if(!empty($p->voucher_discount_price)) {
                                            $perc = $p->voucher_discount_price;
                                            $gr_total = $total-$perc+$shipping_total;
                                            $discount = harga($perc);				
                                        }
                            
                                        $p->voucher_discount = $discount;

                                        $format_total = harga($total);
                                        $format_shipping_total = harga($shipping_total);
                                        $format_total_all = harga($gr_total);

                                        $voucher_total = $discount;
                                        /*

                                        
                                        $p->format_date = tgl_indoo($p->dt_created);
                                        $p->format_tgl_np = tgl_indoo($p->tgl_np); 
                                        $p->tracking = $tracking;       

                                        $items = $this->cart->get_detail_items_admin($order_id);
                                        $sj = $this->cart->get_detail_sj_admin($order_id);
                                        
                                        $p->items = $items;
                                        $p->sj = $sj;
                                        */

                                        $items = $this->cart->get_detail_items_admin($order_id);
                                         
                                        $items = $items;

                                        $button_name = "Verify";

                                        $button_status = 1;
                                        if($p->status_order != 0) { // apabila status order nya 0 maka muncul button nya.
                                            $button_status = 0;
                                        }

                                        if($p->status_order == 15) {
                                            $button_status = 2;
                                            $button_name = "Edit";
                                        }


                                        $format_date = tgl_indoo($p->dt_created);
                                        $p = array("order_apps_id"=>$p->order_apps_id,"np_id"=>$p->np_id,"status_order_name"=>$status_order_name,"date_buy"=>$format_date,"customer_name"=>$p->customer_name,"format_total"=>$format_total,
                                    "format_total_all"=>$format_total_all,"format_shipping_total"=>$format_shipping_total,"phone_number"=>$p->phone,
                                    "voucher_total"=>$voucher_total,"payment_method_name"=>$p->top,"products"=>$p->products,"button_name"=>$button_name,"button_status"=>$button_status,
                                "items"=>$items);

                                        $posts = $p;

                                    } else {
                                        $posts = null;
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

    public function approve_order_post()
    {
        $code = $this->input->post('cod3');
        $post = array();
        $data = "";
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');  
                $orderid =  $this->input->post('order_id');  
                $status_order = 0;
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {
                            
                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();
                                
                                if(!empty($user)) {
                                    $data = $this->cart->get_order($orderid);
                                    $items = $this->cart->get_items_order($orderid);
                                    $a = $this->cart->send_create_order($data,$items);
                                    //$a = "Data success";
                                    if($a == '"Data Success"') {
                                        $status = 1;
                                        $msg = "success";
                                        $this->orders->update_status_order($orderid,1);
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

    public function confirm_order_post()
    {
        $code = $this->input->post('cod3');
        $post = array();
        $data = "";
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');  
                $orderid =  $this->input->post('order_id');  
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
                                        $items = $this->cart->get_items_order($orderid);
                                        $a = $this->cart->send_create_order($data,$items);
                                        //$a = "Data success";
                                        if($a == '"Data Success"') {
                                            $status = 1;
                                            $msg = "success";
                                            
                                            $user->user_id = $user->id;

                                            $this->orders->create_order_detail_status($orderid,1,$user);
                                            $this->orders->update_status_order($orderid,1);

                                            //$aa = $this->orders->send_notif_orderadmin($user,$orderid,1);
                                        } else {
                                            $status = -1;
                                            $msg = "Failed send to AX $a";
                                        }
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

                                        $this->orders->create_order_detail_status($orderid,17,$user);

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

    public function verify_order_post()
    {
        $code = $this->input->post('cod3');
        $post = array();
        $data = "";
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');  
                $orderid =  $this->input->post('order_id');
                $items = $this->input->post('items');  
                $status_order = 0;
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {
                            
                            $check = $this->login_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();

                                if(!empty($items)) {
                                    $obj = json_decode($items);
                                    $gr_total = 0;
                                    foreach($obj as $o) {
                                        $detailid = $o->id;
                                        $newprice = $o->new_price;
                                        $newdiscount = $o->new_discount;
                                        
                                        //echo $detailid.' '.$newprice.' '.$newdiscount;
                                        
                                            $a = $this->orders->check_order_detail($detailid);
                                            if(!empty($a->id)) {
                                                $priceold = $a->price_old;
                                                $total = $priceold;
                                                if(!empty($newprice)) {
                                                    $newdisc = round((($priceold-$newprice)/$priceold)*100,9);
                                                    $total = round($newprice*$a->qty, 0, PHP_ROUND_HALF_DOWN);
//                                                    $total = floor($newprice*$a->qty);
                                                    $discount_price = $a->amount-$total;
                                                    $data_order = array("discount"=>$newdisc,"price"=>$newprice,"total"=>$total,"discount_price"=>$discount_price);
                                                    $this->orders->update_order_detail($data_order,$detailid);
                                                }
                                                $gr_total += $total;
                                            }
                                        

                                    }
                                    
                                    $dataorder = array("total"=>$gr_total);
                                    
                                    $this->orders->update_order($dataorder,$orderid);
                                    
                                    $this->orders->update_status_order($orderid,15);

                                    $cc = $this->db->query("SELECT * FROM orders WHERE id = $orderid")->row();

                                    $total_new = $this->orders->get_total_disc_ship($gr_total,$cc);
                                    $dataorder2 = array("total"=>$total_new);

                                    $this->orders->update_invoice($dataorder2,$orderid);

                                    $user->user_id = $user->id;

                                    $this->orders->create_order_detail_status($orderid,15,$user);
                                    
                                    $aa = $this->orders->send_notif_orderadmin($user,$orderid,15);
                                   // var_dump($aa);
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

    public function change_statusorder_post()
    {
        $code = $this->input->post('cod3');
        $post = array();
        $data = "";
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');  
                $orderid =  $this->input->post('order_id');
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {
                            
                            $check = $this->login_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();

                                $this->orders->update_status_order($orderid,0);         
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
        $data_array = array();
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
                                    $posts[] = array("id"=>"all","text"=>"All","hex"=>"","total_orders"=>0);
                                    if(!empty($post)) {
                                        
                                        foreach($post as $p) {
                                            
                                            //$posts[] = $p;
                                            
                                            $name_value = $p->name;
                                            /*
                                            if(!empty($p->total_orders)) {
                                                $name_value .= " ( $p->total_orders )";
                                            }
                                            */
                                            $posts[] = array("id"=>$p->id,"text"=>$name_value,"hex"=>$p->hex,"total_orders"=>$p->total_orders);
                                        }
                                    } else {
                                        $posts = array();
                                    }
                                    $data_array = array("status"=>$posts,"date" => $date_stat_apps);
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
                $data_array = null;
            }

            $message = [
                'status' => $status,
                'message' => $msg,
                'data' => $data_array
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

    public function list_order_salesstaff_post()
    {
        $code = $this->input->post('cod3');
        $post = array();
        $data = "";
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');  
                $date_from =  $this->input->post('date_from');  
                $date_to =  $this->input->post('date_to');
               
                $name =  $this->input->post('name');     
                $type = $this->input->post('type');             
                $period = $this->input->post('period');
                $status_apps = $this->input->post('status_apps');

                $page = $this->input->post('page');             
                $limit = $this->input->post('limit');

                if(empty($page)) {
                    $page = 0;
                } else {
                    $page = $page-1;
                }

                $page = $page*$limit;

                if (isset($status_apps)) {
                    if($status_apps == "all") {
                        $status_apps = 0;
                    } else {
                        $status_apps = $status_apps;
                    }

                    if($status_apps == '') {
                        $status_apps = 0;
                    }

                } else {
                    $status_apps = 0;
                }

                //echo $status_apps;
                

                if(!empty($period)) {
                    $date_to = date("Y-m-d");
                    $date_from = date('Y-m-d', strtotime("-$period days"));
                    //$a = date("Y-m-d", strtotime("$date_from -$period Days"));
                   
                }
/*
                if(empty($date_from) && empty($date_to)) {
                    $date_to = date("Y-m-d");
                    $date_from = date('Y-m-d', strtotime("-60 days"));
                }
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
                                    $post = $this->cart->get_list_order_admin($user,$date_from,$date_to,$name,$status_apps,1,$page,$limit);
                                    if(!empty($post)) {
                                        
                                        foreach($post as $p) {
                                            $img = "";
                                            if(!empty($p->banner_image)) {
                                                $img = base_url() . $this->settings->info->upload_path_thumbs . "/" . $p->banner_image;
                                            }
                                            $total = $p->total;
                                            $format_total = harga($total);
                                            /*
                                            if($p->status_order == 1) {
                                                $status_order = "On Process";
                                            } else {
                                                $status_order = "Pending";
                                            }
                                            */
                                            /*
                                            

                                            $p->date_buy = tgl_view($p->dt_created);
                                            
                                            $p->format_total = $format_total;
                                            
                                            $p->banner_image = $img;
                                            
                                            $posts[] = $p;
                                            */
                                            $cs = cek_status_order_api_new($p->status_order,"");
                                            $status_order_name = $cs['name'];
                                            $hex = $cs['hex'];

                                            $img = "";
                                            if(!empty($p->banner_image)) {
                                                $img = base_url() . $this->settings->info->upload_path_thumbs . "/" . $p->banner_image;
                                            }

                                            $other_products = "1 product";
                                            if($p->total_product > 1) {
                                                $other_products = $p->total_product." products";
                                            }


                                            $date_buy = tgl_indoo($p->dt_created);
                                            $posts[] = array("id"=>$p->id,"date_buy"=>$date_buy,"order_apps_id"=>$p->order_apps_id,
                                            "format_total"=>$format_total,"customer_name"=>$p->customer_name,"status_order_name"=>$status_order_name,"status_order_hex"=>$hex,
                                        "banner_image"=>$img,"other_products"=>$other_products);
                                        }
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

    public function list_customer_salesstaff_post()
    {
        $code = $this->input->post('cod3');
        $post = array();
        $data = "";
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');                 
                $name =  $this->input->post('name');    

                $page = $this->input->post('page');             
                $limit = $this->input->post('limit');

                if(empty($page)) {
                    $page = 0;
                } else {
                    $page = $page-1;
                }

                $page = $page*$limit;

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
                                    $post = $this->orders->get_list_customer($user,$name,$page,$limit);
                                    if(!empty($post)) {
                                        
                                        foreach($post as $p) {
                                            $img = "";
                                           
                                            $total = $p->total_invoice;
                                            if(empty($total)) {
                                                $total = 0;
                                            }
                                            $id = $p->ID;
                                            $format_total = harga($total);
                                            
                                            $customer_name = $p->customer_name." ($p->username)";
                                            $posts[] = array("id"=>$id,"format_total"=>$format_total,"customer_name"=>$customer_name,
                                            "total"=>$total);
                                        }
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

    public function detail_customer_post()
    {
        $code = $this->input->post('cod3');
        $post = array();
        $data = "";
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token'); 
                $id = $this->input->post("id");
                $page = $this->input->post('page');             
                $limit = $this->input->post('limit');

                if(empty($page)) {
                    $page = 0;
                } else {
                    $page = $page-1;
                }

                $page = $page*$limit;

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
                                    $p = $this->orders->get_customer_detail($id);
                                    if(!empty($p->ID)) {
                                        if($p->top == 'CBD') {
                                            $transactions = $this->orders->get_customer_list_invoice($p->username,$page,$limit);

                                        } else {
                                            $transactions = $this->orders->get_customer_list_invoice_top($p->username,$page,$limit);

                                        }
                                        $posts = array("customer_name"=>$p->customer_name,"ID"=>$p->ID,"username"=>$p->username,"credit_limit"=>$p->credit_limit,
                                        "transaction_count"=>$p->transaction_count,"total_apps"=>$p->total_apps,"format_total_apps"=>harga($p->total_apps),"total_non_apps"=>$p->total_non_apps,
                                    "format_total_non_apps"=>harga($p->total_non_apps),"total_all"=>$p->total_all,"format_total_all"=>harga($p->total_all),"transactions"=>$transactions);
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

    public function list_order_salesadmin_post()
    {
        $code = $this->input->post('cod3');
        $post = array();
        $data = "";
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');  
                $date_from =  $this->input->post('date_from');  
                $date_to =  $this->input->post('date_to');
               
                $name =  $this->input->post('name');     
                $type = $this->input->post('type');             
                $period = $this->input->post('period');
                $status_apps = $this->input->post('status_apps');

                $page = $this->input->post('page');             
                $limit = $this->input->post('limit');

                if(empty($page)) {
                    $page = 0;
                } else {
                    $page = $page-1;
                }

                $page = $page*$limit;
                
                if (isset($status_apps)) {
                    if($status_apps == 'all') {
                        $status_apps = 15;
                    } else {
                        $status_apps = $status_apps;
                    }

                    if($status_apps == '') {
                        $status_apps = 15;
                    }

                } else {
                    $status_apps = 15;
                }
                

                if(!empty($period)) {
                    $date_to = date("Y-m-d");
                    $date_from = date('Y-m-d', strtotime("-$period days"));
                    //$a = date("Y-m-d", strtotime("$date_from -$period Days"));
                   
                }
                
/*
                if(empty($date_from) && empty($date_to)) {
                    $date_to = date("Y-m-d");
                    $date_from = date('Y-m-d', strtotime("-60 days"));
                }
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
                                    $post = $this->cart->get_list_order_admin($user,$date_from,$date_to,$name,$status_apps,2,$page,$limit);
                                    if(!empty($post)) {
                                        
                                        foreach($post as $p) {
                                            $img = "";
                                            if(!empty($p->banner_image)) {
                                                $img = base_url() . $this->settings->info->upload_path_thumbs . "/" . $p->banner_image;
                                            }
                                            $total = $p->total;
                                            
                                            $format_total = harga($total);
                                            
                                            $cs = cek_status_order_api_new($p->status_order,"");
                                            $status_order_name = $cs['name'];
                                            $hex = $cs['hex'];

                                            $img = "";
                                            if(!empty($p->banner_image)) {
                                                $img = base_url() . $this->settings->info->upload_path_thumbs . "/" . $p->banner_image;
                                            }

                                            $other_products = "1 product";
                                            if($p->total_product > 1) {
                                                $other_products = $p->total_product." products";
                                            }


                                            $date_buy = tgl_indoo($p->dt_created);
                                            $posts[] = array("id"=>$p->id,"date_buy"=>$date_buy,"order_apps_id"=>$p->order_apps_id,
                                            "format_total"=>$format_total,"customer_name"=>$p->customer_name,"status_order_name"=>$status_order_name,"status_order_hex"=>$hex,
                                        "banner_image"=>$img,"other_products"=>$other_products);
                                        }
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

    public function detail_order_salesadmin_post()
    {
        $code = $this->input->post('cod3');
        $posts = array();
        //$data = array();
        $data = null;
        $posts = null;

        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                $order_id = $this->input->post('order_id');
                
                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {

                            $check = $this->login_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $post = array();
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();
                                $length = array();
                                $thickness = array();
                                $color = array();
                                if(!empty($user)) {
                                    $cabang = $user->cabang;
                                    $p = $this->cart->get_detail_order_admin($user,$order_id);
                                    
                                    
                                    if(!empty($p)) {

                                        $total = $p->total;
                                        $shipping_total = $p->shipping_total;
                                        $total_all = $total+$shipping_total;
                                        

                                        $tracking = 0;
                                        if($p->status_order == 2) {
                                            $tracking = 1;
                                        }

                                        $cs = cek_status_order_api_new($p->status_order,"");
                                        $status_order_name = $cs['name'];
                                        $hex = $cs['hex'];
                                        //$p->total_all = $total_all;
                                        $gr_total = $total_all;

                                        if(!empty($p->voucher_discount)) {
                                            $perc = $p->voucher_discount;
                                            $a_perc = $total*$perc;
                                            $gr_total = $total-(round($a_perc/100))+$shipping_total;
                                            $discount = $perc.'%';
                                        //	$disc_perc = $vs->discount_percentage;
                                        }
                                        if(!empty($p->voucher_discount_price)) {
                                            $perc = $p->voucher_discount_price;
                                            $gr_total = $total-$perc+$shipping_total;
                                            $discount = harga($perc);				
                                        }
                            
                                        $p->voucher_discount = $discount;

                                        $format_total = harga($total);
                                        $format_shipping_total = harga($shipping_total);
                                        $format_total_all = harga($gr_total);

                                        $voucher_total = $discount;
                                       

                                        $items = $this->cart->get_detail_items_admin($order_id);
                                         
                                        $items = $items;

                                        $button_confirm_name = "Confirm";
                                        $button_reject_name = "Reject";

                                        $button_status = 0;
                                        if($p->status_order == 15) { // apabila status id 15 maka button nya muncul.
                                            $button_status = 1;
                                        }


                                        $format_date = tgl_indoo($p->dt_created);
                                        $p = array("id"=>$order_id,"order_apps_id"=>$p->order_apps_id,"np_id"=>$p->np_id,"status_order_name"=>$status_order_name,"date_buy"=>$format_date,"customer_name"=>$p->customer_name,"format_total"=>$format_total,
                                    "format_total_all"=>$format_total_all,"format_shipping_total"=>$format_shipping_total,"phone_number"=>$p->phone,
                                    "voucher_total"=>$voucher_total,"payment_method_name"=>$p->top,"products"=>$p->products,"button_confirm_name"=>$button_confirm_name
                                    ,"button_reject_name"=>$button_reject_name,"button_status"=>$button_status,
                                "items"=>$items);

                                        $posts = $p;

                                    } else {
                                        $posts = null;
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

}
