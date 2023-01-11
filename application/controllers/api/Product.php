<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Product extends REST_Controller
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
		$this->load->model("model_product","product");
        $this->load->model("model_promobanner","promobanner");
        $this->load->helper('email');
        $this->load->model("model_cart","cart");
		$this->load->model("image_model");
        $this->load->model("page_model");
        $this->load->model("admin_model");
        $this->load->model("model_category","category");

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
                $categoryid = $this->input->post('category');

                $limit =  $this->input->post('limit');
                $name = $this->input->post("name");
                $page =  $this->input->post('page');
                
                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {

                            if(empty($page)) {
                                $page = 0;
                            } else {
                                $page = $page-1;
                            }
            
                            $page = $page*$limit;
                            
                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $post = array();
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();

                               
                                //$post = $this->admin_model->get_list_product($categoryid,$limit,$page)->result();
                                
                                
                                if(!empty($user)) {
                                    $cabang = $user->cabang;
                                    $post = $this->productbranch->get_list_product($cabang,$name,$categoryid,$limit,$page,"");
                                    foreach($post as $p) {
                                        $img = "";
                                        if(!empty($p->banner_image)) {
                                            $img = base_url() . $this->settings->info->upload_path_thumbs . "/" . $p->banner_image;
                                        }
                                        /*
                                        if(!empty($p->discount)) {
                                            $p_discount = $p->price-round(($p->price*$p->discount)/100);    
                                            $discount = (int)$p->discount;
                                            $p->price_old = "Rp ".number_format($p->price);
                                            $p->price = "Rp ".number_format($p_discount);
                                        } else {
                                            $p_discount = 0;
                                            $discount = 0;

                                            $p->price_old = 0;
                                            $p->price = "Rp ".number_format($p->price);
                                        }
                                        $p->discount = $discount;
                                        */
                                        
                                        $pp2 = $this->productbranch->get_price_min($p->product_master_id);

                                        $p->price = harga($pp2->price);
                                        $p->price_old = floatval($pp2->price_old);
                                        $p->discount = floatval($pp2->discount);



                                        $p->image = $img;
                                        $posts[] = $p;
                                    }
                                }                
                            } else {
                                $status = -1;
                                $msg = lang("error_85");

                            }
                            
                    } else {
                        $status = -3;
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
        $post = null;
        $data = null;
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

                                    //$this->productbranch->insert_visitor($user,1);
                                    if(!empty($p)) {
                                        $product_master_id = $p->product_master_id;
                                        $pimage = $this->product->get_product_images($product_master_id);
                                        $color = $this->productbranch->get_product_color($cabang,$product_master_id);
                                        $thickness = $this->productbranch->get_product_thickness($cabang,$product_master_id);
                                        $length = $this->productbranch->get_product_length($cabang,$product_master_id);

                                        $pp = $this->productbranch->get_price_min($product_master_id);

                                        $p->price = $pp->price;
                                        $p->price_old = floatval($pp->price_old);
                                        $p->discount = floatval($pp->discount);
                                        
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

                                        $p->wishlist = $this->productbranch->checkproductwishlist($user,$id);
                                        $post = $p;
    
                                    } else {
                                        $p = "";
                                        $status = -1;
                                        $msg = "Product Detail Not Found";
                                       $post = null;
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
    
    public function home_post()
    {
        $code = $this->input->post('cod3');
        $posts = array();
        $data = array();
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                $id = $this->input->post('id');
                
                    $warning = '';
                    $limit =  $this->input->post('limit');
                    $page =  $this->input->post('page');
                        // pastikan username dan password adalah berupa huruf atau angka.
                        if(!empty($token)) {
    
                            if(empty($page)) {
                                $page = 0;
                            } else {
                                $page = $page-1;
                            }
            
                            $page = $page*$limit;

                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $post = array();
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();
                                if(!empty($user)) {
                                    $cabang = $user->cabang;

                                    $banners = array();
                                    $featured = array();
                                    $best_seller = array();
                                    $promo_product = array();

                                    $bnr = $this->promobanner->list_promobanner($cabang)->result();

                                    foreach($bnr as $p3) {
                                        $img = "";
                                        if(!empty($p3->image)) {
                                            $img = base_url("uploads/banner/") . $p3->image;
                                        }
                                        $p3->image = $img;
                                        $banners[] = $p3;
                                    }
                                    

                                    $feat = $this->productbranch->get_list_product($cabang,"",$limit,$page,1);

                                    foreach($feat as $p) {
                                        $img = "";
                                        if(!empty($p->banner_image)) {
                                            $img = base_url() . $this->settings->info->upload_path_thumbs . "/" . $p->banner_image;
                                        }

                                        $pp = $this->productbranch->get_price_min($p->product_master_id);

                                        $p->price = harga($pp->price);
                                        $p->price_old = floatval($pp->price_old);
                                        $p->discount = floatval($pp->discount);
    
                                        //$p->price = "Rp ".number_format($p->price);
                                        $p->image = $img;
                                        $featured[] = $p;
                                    }

                                    $best = $this->productbranch->get_list_product($cabang,"",$limit,$page,2);

                                    foreach($best as $p2) {
                                        $img = "";
                                        if(!empty($p2->banner_image)) {
                                            $img = base_url() . $this->settings->info->upload_path_thumbs . "/" . $p->banner_image;
                                        }

                                        $pp2 = $this->productbranch->get_price_min($p2->product_master_id);

                                        $p2->price = harga($pp2->price);
                                        $p2->price_old = floatval($pp2->price_old);
                                        $p2->discount = floatval($pp2->discount);
    
                                        //$p2->price = "Rp ".number_format($p2->price);
                                        $p2->image = $img;
                                        $best_seller[] = $p2;
                                    }

                                    $list2 = $this->category->category_icon();
                                    foreach($list2 as $l) {
                                        

                                        $icon_image = "";
                                        if(!empty($l->icon_image)) {
                                            $icon_image = base_url("uploads/category_icon/thumbs/$l->icon_image");
                                        }
                                        $l->icon_image = $icon_image;
                                        
                                        $list[] = $l;
                                    }

                                    $spc = $this->promobanner->list_specialpromo($cabang)->result();

                                    foreach($spc as $p4) {
                                        $img4 = "";
                                        if(!empty($p4->image)) {
                                            $img4 = base_url("uploads/banner/") . $p4->image;
                                        }
                                        $p4->image = $img4;
                                        $promo_product[] = $p4;
                                    }

                                    $post = array("banner"=>$banners,"featured"=>$featured,"best_seller"=>$best_seller,"promo"=>$promo_product,
                                    "icon_category"=>$list);

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
    

    public function varian_post()
    {
        $code = $this->input->post('cod3');
        $posts = array();
        //$data = array();
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                $id = $this->input->post('id');
                
                    $warning = '';
                    $product_id =  $this->input->post('product_master_id');
                    $length =  $this->input->post('length_id');
                    $length_value = $this->input->post('length_value');
                    $thickness =  $this->input->post('thickness_id');
                    $color =  $this->input->post('color_id');
                    $qty = $this->input->post("qty");
                   // $data = array();
                    $data = null;
                    if(empty($qty)) {
                        $qty = 1;
                    }

                        // pastikan username dan password adalah berupa huruf atau angka.
                        if(!empty($token)) {
    
                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $post = array();
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();
                                $string_value = "";
                                if(!empty($user)) {
                                    $cabang = $user->cabang;

                                    $br = $this->productbranch->checkproductbranch($cabang,$product_id,$color,$length,$thickness);
                                   // var_dump($br);
                                    //echo $br;
                                    if(!empty($br->id)) {
                                        $price = round($br->price);
                                        $fprice = harga($price);
                                        $price_old = $br->price_old;
                                        $discount = $br->discount;



                                        if(!empty($length_value)) {
                                            $length = $length_value;
                                        } else {					
                                            $length = $this->cart->get_length($length);
                                        }
                                        
                                        $qty2 = $qty*$length;                                        
                                        $qty2 = round($qty2,2);
                                        $total = $price*$qty2;

                                        $ftotal = harga($total);
                                        /*
                                        if(empty($length)) {
                                            $total = $total*$length_value;
                                        }
                                        $ftotal = harga($total);
                                        */
                                        if(!empty($length)) {
                                            $string_value .= $length.' '.$br->length_satuan.' x ';
                                        }
                                        /*
                                        if(!empty($qty2)) {
                                            $string_value .= $qty2.' X ';
                                        }
                                        */
                                        if(!empty($qty)) {
                                            $string_value .= $qty.' x ';
                                        }

                                        $string_value .= $fprice.' x ';

                                        $sting_value = rtrim($string_value,' x ');
                                        
                                        $data = array("product_varian"=>$br->id,"format_price"=>$fprice,"price"=>$price,
                                        "format_total"=>$ftotal,"total"=>$total,"qty_fisik"=>$qty,"qty"=>$qty2,"price_old"=>$price_old,"discount"=>$discount,
                                    "formula_value"=>$sting_value);
                                    } else {
                                        $status = -1;
                                        $msg = "Product Varian Not Found";
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

    public function addcart_post()
    {
        $code = $this->input->post('cod3');
        $posts = array();
        $data = array();
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                $id = $this->input->post('id');
                $cart_id = 0;
                
                    $warning = '';
                    $product_id =  $this->input->post('product_id');
                    $length =  $this->input->post('length');
                    $thickness =  $this->input->post('thickness');
                    $product_varian =  $this->input->post('product_varian');
                    $qty = $this->input->post('qty');
                    $notes = $this->input->post('notes');
                    $length_value = $this->input->post('length_value');

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


                                    $b = $this->productbranch->getid_byproductvarian($cabang,$product_varian);
                                    //var_dump($b);
                                    if(!empty($id)) {
                                        $dd = $this->productbranch->check_product_master($product_id,$cabang);
                                        if(empty($dd)) {
                                            $ch = $this->cart->checkidcart($user->ID,$id);
                                            if($ch > 0) {
                                                $q = $this->cart->updatecart($id,$product_varian,$b,$qty,$notes,$user,$length_value,$product_id);
                                            }
                                            $cart_id = $id;
                                        } else {
                                            $q['status'] = -1;
                                            $q['message'] = "Product is not active";
                                            $q['cart_id'] = "";
                                        }
                                    } else {
                                            
                                        $dd = $this->productbranch->check_product_master($product_id,$cabang);
                                        if(empty($dd)) {
                                            $q = $this->cart->addcart($product_varian,$b,$qty,$notes,$user,$length_value,$product_id);
                                        } else {
                                            $q['status'] = -1;
                                            $q['message'] = "Product is not active";
                                            $q['cart_id'] = "";
                                        }

                                    }

                                    $status = $q['status'];
                                    $msg = $q['message'];
                                    $cart_id = $q['cart_id'];

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
                'cart_id' => $cart_id
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

    public function listwishlist_post()
    {
        $code = $this->input->post('cod3');
        $posts = array();
        $data = array();
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');

                $limit =  $this->input->post('limit');
                $name = $this->input->post("name");
                $page =  $this->input->post('page');
                
                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {

                            if(empty($page)) {
                                $page = 0;
                            } else {
                                $page = $page-1;
                            }
            
                            $page = $page*$limit;
                            
                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $post = array();
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();

                               
                                //$post = $this->admin_model->get_list_product($categoryid,$limit,$page)->result();
                                
                                
                                if(!empty($user)) {
                                    $cabang = $user->cabang;
                                    $post = $this->productbranch->get_list_productwishlist($user,$cabang,$name,$limit,$page,"");
                                    foreach($post as $p) {
                                        $img = "";
                                        if(!empty($p->banner_image)) {
                                            $img = base_url() . $this->settings->info->upload_path_thumbs . "/" . $p->banner_image;
                                        }
                                        if(!empty($p->discount)) {
                                            $p_discount = $p->price-round(($p->price*$p->discount)/100);    
                                            $discount = (int)$p->discount;
                                            $p->price_old = "Rp ".number_format($p->price);
                                            $p->price = "Rp ".number_format($p_discount);
                                        } else {
                                            $p_discount = 0;
                                            $discount = 0;

                                            $p->price_old = 0;
                                            $p->price = "Rp ".number_format($p->price);
                                        }
                                        $p->discount = $discount;
                                      



                                        $p->image = $img;
                                        $posts[] = $p;
                                    }
                                }                
                            } else {
                                $status = -1;
                                $msg = lang("error_85");

                            }
                            
                    } else {
                        $status = -3;
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

    public function updatewishlist_post()
    {
        $code = $this->input->post('cod3');
        $posts = array();
        $type = "";
        $data = array();
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');

                    $warning = '';
                    $product_id =  $this->input->post('product_id');

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

                                    $q = $this->productbranch->updatewishlist($user,$product_id);

                                    $status = $q['status'];
                                    $msg = $q['message'];
                                    $type = $q['type'];

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
            if(!empty($type)) {
                $message = [
                    'status' => $status,
                    'message' => $msg,
                    'type' => $type
                ];                
            } else {
                $message = [
                    'status' => $status,
                    'message' => $msg
                ];
            }
            /*
            $message = [
                'status' => $status,
                'message' => $msg
            ];
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

    
    public function productionupdate_post() {
        $orderid = $this->input->post("orderid");
        $lineid = $this->input->post("lineid");
       // echo base64_encode($lineid);

        if(!empty($lineid)) {
            $lineid = base64_decode($lineid);
        }

        $ada = $this->db->query("SELECT * FROM orders WHERE order_apps_id = '$orderid'")->row();
        $message = "";
        if(!empty($ada) && !empty($lineid)) {
            $od = $this->db->query("SELECT * FROM orders_detail WHERE id = $lineid AND orders_id = $ada->id")->row();
            if(!empty($od->id)) {
                $this->db->where('id',$lineid);
                $datadb = array("production_status"=>1,"production_qty"=>$od->qty_fisik,"preparation_qty"=>0);
                $this->db->update("orders_detail",$datadb);
                $message = "success";
            } else {
                $message = "Line id tidak ketemu";
            }
    
        } else {
            $message = "Nomor Order id tidak ada";
        }

        echo $message;
/*
        if($message['status']) {
            echo "success";
        } else {
            echo "error";
        }
  */      
         //$this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
    }
}
