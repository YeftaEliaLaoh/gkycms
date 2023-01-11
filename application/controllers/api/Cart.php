<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Cart extends REST_Controller
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

        $this->load->model("user_model");
        
        $this->load->model("model_productbranch","productbranch");
        $this->load->model("model_promobanner","promobanner");
        $this->load->helper('email');
        $this->load->model("model_cart","cart");
        
        $this->load->model("register_model");

        date_default_timezone_set('Asia/Jakarta');
    }

    

    public function list_post()
    {
        $code = $this->input->post('cod3');
        $posts = array();
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
                                
                                if(!empty($user)) {
                                    $cabang = $user->cabang;
                                    $post = $this->cart->get_list_cart($user);
                                    $c = 0;
                                    foreach($post as $p) {
                                        $img = "";
                                        if(!empty($p->banner_image)) {
                                            $img = base_url() . $this->settings->info->upload_path_thumbs . "/" . $p->banner_image;
                                        }

                                        $t = $this->cart->check_product_status($p);
                                        $stat = 1;
                                        if(!empty($t['status'])) {
                                            $c += 1;
                                            $b[] = $t;
                                            $stat = 0;
                                            $type = $t['type'];
                                        }

                                        if(empty($p->length_id)) {
                                            $p->length_name = $p->length_value.' m';
                                        }
                                        $price = $p->price;
                                        $format_price = harga($price);

                                        $total = round($p->total);
                                        $format_total = harga($total);

                                        $qty = $p->qty;
                                        
                                        $p->format_price = $format_price;
                                        $p->format_total = $format_total;
                                        $p->image = $img;
                                        $p->status_active = $stat;
                                        
                                        $posts[] = $p;
                                    }

                                    if(!empty($c)) { // apabila ada product yg berubah
                                        $status = 2;
                                        $n = 0;
                                        if(!empty($type)) {
                                            $msg = "Sorry, there is an update in one or more product(s) in your cart. Please review your cart before proceeding";
                                            foreach($b as $k) {
                                                $n++;
                                                $product_name = $k['product_name'];
                                                $hrg = harga($k['price']);
                                                $msg .= "\n$n. $product_name, new price : $hrg";
                                            }
                                        } else {
                                            $msg = "Sorry, one or more product(s) in your cart is not available. Please review your cart before proceeding";
                                            foreach($b as $k) {
                                                $n++;
                                                $product_name = $k['product_name'];
                                                $msg .= "\n$n. $product_name";
                                            }
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

            if($status == -2) {
                $post = null;
            }

            $message = [
                'status' => $status,
                'message' => $msg,
                'data' => $post
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

    public function listcreateorder_post()
    {
        $code = $this->input->post('cod3');
        $cart = array();
        $data = array();
        $total_all = 0;
        $shipping_total = 0;
        $total = 0;
        $sub_total = 0;
        $address = null;
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');                
                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {
                            
                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $post = array();
                                $post2 = array();
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();
                                
                                if(!empty($user)) {
                                    $cabang = $user->cabang;

                                    
                                    $shipping_total = $this->cart->getongkir($user);

                                    $post = $this->cart->get_list_cartcreateorder($user);
                                    foreach($post as $p) {
                                        $img = "";
                                        if(!empty($p->banner_image)) {
                                            $img = base_url() . $this->settings->info->upload_path_thumbs . "/" . $p->banner_image;
                                        }
                                        $price = $p->price;
                                        $format_price = harga($price);

                                        if(empty($p->length_id)) {
                                            $p->length_name = $p->length_value.' m';
                                        }

                                        $total = round($p->total);
                                        $format_total = harga($total);
                                        

                                        $sub_total += $total;

                                        $qty = $p->qty;
                                        
                                        $p->format_price = $format_price;
                                        $p->format_total = $format_total;
                                        $p->image = $img;
                                        
                                        $cart[] = $p;
                                    }
                                    if(!empty($cart)) {

                                        $post2 = $this->cart->get_list_address($user->ID);
                                        foreach($post2 as $p2) {
                                            $p2->default_address = (int)$p2->default_address;
                                            $p2->selected = $p2->default_address;
                                            $address[] = $p2;
                                        }
    
                                        
                                        $total_all = $sub_total+$shipping_total;
                                    } else {
                                        $status = -1;
                                        $msg = "Please select Product on Cart";
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

            if($status == -1) {
                $message = [
                    'status' => $status,
                    'message' => $msg
                ];
            } else {
                $message = [
                    'status' => $status,
                    'message' => $msg,
                    'address' => $address,
                    'cart' => $cart,
                    'shipping_total'=>$shipping_total,
                    'total' => $sub_total,
                    'total_all'=>$total_all,
                    'format_total' => harga($sub_total),
                    'format_shipping_total' => harga($shipping_total),
                    'format_total_all' => harga($total_all),
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

    public function detail_post()
    {
        $code = $this->input->post('cod3');
        $posts = array();
        $data = array();
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                $id = $this->input->post('id');
                
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
                                    $p = $this->productbranch->get_product_detail($cabang,$id);
                                    $product_master_id = $p->product_master_id;
                                    $pimage = $this->product->get_product_images($product_master_id);
                                    $color = $this->productbranch->get_product_color($cabang,$product_master_id);
                                    $thickness = $this->productbranch->get_product_thickness($cabang,$product_master_id);
                                    $length = $this->productbranch->get_product_length($cabang,$product_master_id);
                                    
                                    $img = array();
                                    foreach($pimage  as $pi) {
                                        $url_img = "";
                                        if(!empty($pi->image)) {
                                            $url_img = base_url() . $this->settings->info->upload_path_thumbs . "/" . $pi->image;
                                        }
                                        $img[] = array("id"=>$pi->id,"image"=>$url_img);
                                    }
                                    $p->images = $img;
                                    $p->color = $color;
                                    $p->length = $length;
                                    $p->thickness = $thickness;

                                    $post = $p;

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
                $post = null;
            }

            $message = [
                'status' => $status,
                'message' => $msg,
                'data' => $post
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
    

    
    public function deletecart_post()   {
        $code = $this->input->post('cod3');
        $posts = array();
        $data = array();
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                $id = $this->input->post('id');
                
                    $warning = '';
                    $cart_id =  $this->input->post('cart_id');
                   
                        // pastikan username dan password adalah berupa huruf atau angka.
                        if(!empty($token)) {

                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $post = array();
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();
                                if(!empty($user)) {
                                    $cabang = $user->cabang;

                                    $q = $this->cart->deletecart($user->ID,$cart_id);
                                    
                                   // $q = $this->cart->addcart($product_varian,$b,$qty,$notes,$user,$length_value);
                                    $status = $q['status'];
                                    $msg = $q['message'];
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

    public function deletemultiplecart_post()   {
        $code = $this->input->post('cod3');
        $posts = array();
        $data = array();
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                $id = $this->input->post('id');
                
                    $warning = '';
                    $cart_id =  $this->input->post('cart_id');
                   
                        // pastikan username dan password adalah berupa huruf atau angka.
                        if(!empty($token)) {

                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $post = array();
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();
                                if(!empty($user)) {
                                    $cabang = $user->cabang;

                                    $q = $this->cart->deletemultiplecart($user->ID,$cart_id);
                                    $status = $q['status'];
                                    $msg = $q['message'];
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

    public function updateselect_post()   {
        $code = $this->input->post('cod3');
        $posts = array();
        $data = array();
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                $id = $this->input->post('id');
                
                    $warning = '';
                    $cart_id =  $this->input->post('cart_id');
                    $selected =  $this->input->post('selected');
                   
                        // pastikan username dan password adalah berupa huruf atau angka.
                        if(!empty($token)) {

                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $post = array();
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();
                                if(!empty($user)) {
                                    $cabang = $user->cabang;
                                    $ada = $this->cart->checkidcart($user->ID,$cart_id);
                                    if($ada) {
                                        $c = $this->cart->getidcart($user->ID,$cart_id);
                                        $t = $this->cart->check_product_status($c);
                                        if(!empty($t['status']) && !empty($selected)) { // apabila ada product yg berubah
                                            $status = 2;
                                            if(!empty($t['type'])) {
                                                $msg = "Sorry, there is an update in one or more product(s) in your cart. Please review your cart before proceeding";
                                                
                                                $product_name = $t['product_name'];
                                                $hrg = harga($t['price']);
                                                $msg .= " product_name, new price : $hrg";
                                                
                                            } else {
                                                $msg = "Sorry, one or more product(s) in your cart is not available. Please review your cart before proceeding";
                                                $product_name = $t['product_name'];
                                                $msg .= " ".$product_name;
                                                
                                            }
    
                                            $q = array("status"=>$status,"message"=>$msg);
                                        } else {
                                            $dd = $this->productbranch->check_product_master($c->product_id,$c->cabang);
                                            if(empty($dd)) {
                                                $q = $this->cart->updateselectedcart($user->ID,$cart_id,$selected);
                                            } else {
                                                $q = array("status"=>-1,"message"=>"Product is not active");
                                            }
                                        }

                                    } else {
                                        $q = array("status"=>-1,"message"=>"Cart ID Not Found");
                                    }

                                    
                                   // $q = $this->cart->addcart($product_varian,$b,$qty,$notes,$user,$length_value);
                                    $status = $q['status'];
                                    $msg = $q['message'];
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

    public function updateselectmultiple_post()   {
        $code = $this->input->post('cod3');
        $posts = array();
        $data = array();
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                $id = $this->input->post('id');
                
                    $warning = '';
                    $cart_id =  $this->input->post('cart_id');
                    $selected =  $this->input->post('selected');
                   
                        // pastikan username dan password adalah berupa huruf atau angka.
                        if(!empty($token)) {

                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $post = array();
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();
                                if(!empty($user)) {
                                    $cabang = $user->cabang;
                                    $carray = explode(",",$cart_id);
                                    $nn = 0;
                                    foreach($carray as $cc) {
                                       // echo $cc.'<br>';
                                      /// $cart_id = $cc;
                                       $ada = $this->cart->checkidcart($user->ID,$cc);
                                        if($ada) {
                                            $c = $this->cart->getidcart($user->ID,$cc);
                                            $t = $this->cart->check_product_status($c);
                                           // var_dump($t);
                                            if(!empty($t['status']) && !empty($selected)) { // apabila ada product yg berubah
                                                $status = 2;
                                                if(!empty($t['type'])) {
                                                    $msg = "Sorry, there is an update in one or more product(s) in your cart. Please review your cart before proceeding";
                                                    
                                                    $product_name = $t['product_name'];
                                                    $hrg = harga($t['price']);
                                                    $nn++;
                                                    $msg .= "\n$nn. $product_name, new price : $hrg";
                                                    
                                                } else {
                                                    $nn++;
                                                    $msg = "Sorry, one or more product(s) in your cart is not available. Please review your cart before proceeding";
                                                    $product_name = $t['product_name'];
                                                    $msg .= "\n$nn.".$product_name;
                                                    
                                                }
        
                                                $q = array("status"=>$status,"message"=>$msg);
                                            } else {
                                                $dd = $this->productbranch->check_product_master($c->product_id,$cabang);
                                                if(empty($dd)) {
                                                    $q = $this->cart->updateselectedcart($user->ID,$cc,$selected);
                                                } else {
                                                    $q = array("status"=>-1,"message"=>"Product is not active");
                                                }
                                            }

                                        } else {
                                            $q = array("status"=>-1,"message"=>"Cart ID Not Found");
                                        }

                                    }
                                    //$q = $this->cart->updateselectedmultiplecart($user->ID,$cart_id,$selected);
                                    /*
                                    $ada = $this->cart->checkidcart($user->ID,$cart_id);
                                    if($ada) {
                                        $q = $this->cart->updateselectedmultiplecart($user->ID,$cart_id,$selected);
                                    } else {
                                        $q = array("status"=>-1,"message"=>"Cart ID Not Found");
                                    }
                                    */

                                    
                                   // $q = $this->cart->addcart($product_varian,$b,$qty,$notes,$user,$length_value);
                                    $status = $q['status'];
                                    $msg = $q['message'];
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

    public function deleteaddress_post()   {
        $code = $this->input->post('cod3');
        $posts = array();
        $data = array();
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                $id = $this->input->post('id');
                
                    $warning = '';
                    $address_id =  $this->input->post('address_id');
                   
                        // pastikan username dan password adalah berupa huruf atau angka.
                        if(!empty($token)) {

                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $post = array();
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();
                                if(!empty($user)) {
                                    $cabang = $user->cabang;

                                    $q = $this->cart->deleteaddress($user->ID,$address_id);
                                    
                                   // $q = $this->cart->addcart($product_varian,$b,$qty,$notes,$user,$length_value);
                                    $status = $q['status'];
                                    $msg = $q['message'];
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

    public function checkvoucher_post() 
    {
        $code = $this->input->post('cod3');
        $posts = array();
        $data = null;
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                $voucher = $this->input->post('voucher');
                
                    $warning = '';
                   
                        // pastikan username dan password adalah berupa huruf atau angka.
                        if(!empty($token)) {

                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $post = array();
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();
                                if(!empty($user)) {
                                    $cabang = $user->cabang;

                                    $q = $this->cart->checkvoucher($user,$voucher);
                                    $status = $q['status'];
                                    $msg = $q['message'];
                                    $data = $q['data'];
                                    /*
                                    $status = 1;
                                    $msg = "oke $length_value $b->length_id";
                                    */

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
                $data = null;
            }

            $message = [
                'status' => $status,
                'message' => $msg,
                'data' => $data
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

    public function checkongkir_post() 
    {
        $code = $this->input->post('cod3');
        $posts = array();
        $data = "";
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
                                if(!empty($user)) {
                                    $cabang = $user->cabang;

                                    $q = $this->cart->checkongkir($user);
                                    $status = $q['status'];
                                    $msg = $q['message'];
                                    $data = $q['data'];
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
                $data = null;
            }

            $message = [
                'status' => $status,
                'message' => $msg,
                'data' => $data
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

    public function selectAddress_post() 
    {
        $code = $this->input->post('cod3');
        $posts = array();
        $data = array();
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                $id = $this->input->post('id');
                
                    $warning = '';
                   
                        // pastikan username dan password adalah berupa huruf atau angka.
                        if(!empty($token)) {

                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $post = array();
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();
                                if(!empty($user)) {
                                    $cabang = $user->cabang;

                                    $datadb = array("default_address"=>1);


                                    $q = $this->cart->selectAddress($user->ID,$datadb,$id);
                                    $status = $q['status'];
                                    $msg = $q['message'];
                                    /*
                                    $status = 1;
                                    $msg = "oke $length_value $b->length_id";
                                    */

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

    public function addupdateAddress_post()
    {
        $code = $this->input->post('cod3');
        $posts = array();
        $data = array();
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                $id = $this->input->post('id');
                
                    $warning = '';
                    $name =  $this->input->post('name');
                    $address =  $this->input->post('address');
                    $longitude =  $this->input->post('longitude');
                    $latitude =  $this->input->post('latitude');
                    $phone = $this->input->post('phone');
                    $province = $this->input->post('province');
                    $city = $this->input->post('city');
                    $subdistrict = $this->input->post('subdistrict');
                    $postcode = $this->input->post('postcode');
                    $title = $this->input->post('title');
                    $address_id = $this->input->post('address_id');
                    $address_from_maps = $this->input->post('address_from_maps');
                        // pastikan username dan password adalah berupa huruf atau angka.
                        if(!empty($token)) {

                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $post = array();
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();
                                if(!empty($user)) {
                                    $cabang = $user->cabang;

                                    $datadb = array("address"=>$address,"postcode"=>$postcode,"phone"=>$phone,"name_address"=>$name,
                                    "user_id"=>$user->ID,"longitude"=>$longitude,"latitude"=>$latitude,
                                    "province_id"=>$province,"city_id"=>$city,"subdistrict_id"=>$subdistrict,"title"=>$title,"address_from_maps"=>$address_from_maps);


                                    $q = $this->cart->addAddress($user->ID,$datadb,$address_id);
                                    $status = $q['status'];
                                    $msg = $q['message'];
                                    /*
                                    $status = 1;
                                    $msg = "oke $length_value $b->length_id";
                                    */

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

    function get_customer_detail($id) {

        

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://182.16.173.43/ApiEcommerce/public/api/detail_customer',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('accountnum' => $id),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $json = json_decode($response);
        //var_dump($json);
        foreach($json as $js) {
            if(!empty($js->ACCOUNTNUM)) {
                $name = $js->NAME;
                $accountnum = $js->ACCOUNTNUM;
                $salesid = $js->SALESRESPONSIBLE;
                $salesname = $js->SALESNAME;
                $hp = phonenumber($js->CELLULARPHONE);
                $phone = phonenumber($js->PHONE);
                $alamatpajak = $js->ALMPAJAK;
                $namapajak = $js->NAMAPAJAK;
                $alamattagihan = $js->ALAMATTAGIHAN;
                $vaccount = $js->VIRTUALACCOUNT;
                $vaccountmandiri = $js->VIRTUALACCOUNTMANDIRI;
                $cabang = $js->CABANGCBM;
                //$cabang = $this->model_cabang->check_cabang($cabang);
                $top_desc = $js->DESCRIPTION;
                $top = $js->TOP;
                $lokasi = $js->LOKASI;

                $a = $this->register_model->check_account_is_free($accountnum);
                if(!empty($a->ID)) {
                    $data = array("top"=>$top,"top_desc"=>$top_desc);

                    $q = $this->register_model->update_user($a->ID, $data);
                }

            }

        }

        return $top;

    }

    public function createorder_post()
    {
        $code = $this->input->post('cod3');
        $posts = array();
        $data = array();
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                    $warning = '';
                    $ongkir =  $this->input->post('ongkir_id');
                    $voucher =  $this->input->post('voucher_id');

                        // pastikan username dan password adalah berupa huruf atau angka.
                        if(!empty($token)) {

                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $post = array();
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();
                                if(!empty($user)) {
                                    $cabang = $user->cabang;
                                    
                                    //var_dump($q);
                                    $user->top = $this->get_customer_detail($user->username);
                                    $q = $this->cart->createorder($user,$ongkir,$voucher);

                                    $status = $q['status'];
                                    $msg = $q['message'];

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

    public function listaddress_post()
    {
        $code = $this->input->post('cod3');
        $posts = array();
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
                                
                                if(!empty($user)) {
                                    $cabang = $user->cabang;
                                    $post = $this->cart->get_list_address($user->ID);
                                    foreach($post as $p) {
                                        $p->default_address = (int)$p->default_address;
                                        
                                        $posts[] = $p;
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
                $post = null;
            }

            $message = [
                'status' => $status,
                'message' => $msg,
                'data' => $post
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
