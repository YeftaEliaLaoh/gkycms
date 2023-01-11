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
		$this->load->model("model_category");
		$this->load->model("user_model");
        $this->load->model("model_users");
        $this->load->model("model_cabang");
        $this->load->model("register_model");
        $this->load->helper('email');
		$this->load->model("image_model");
        $this->load->model("page_model");
        $this->load->model("model_color");
        $this->load->model("model_orders","orders");
        
        date_default_timezone_set('Asia/Jakarta');
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

    public function splash_post()
    {
        $code = $this->input->post('cod3');
        $posts = array();
        $data = array();
        $update = 0;
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
                                    $u = $this->model_users->checkupdatePromo($user->ID);
                                    $update = (int)$u->promo_update;
                                    if($u->promo_update == 0) {
                                        $this->model_users->updatePromo_byuser($user->ID,1);
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
                'update' => $update
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

    public function color_post()
    {
        $p = $this->input->post('color');
        if(!empty($p)) {
            $p = base64_decode($p);
        }
		//$type = $this->input->get('type');
        $type = 1;
        $color = $this->model_color->show_color($p)->result_array();
        $data = "";
        $idd = "";
        if(!empty($color)) {
            $data = "<option value=''>Choose Color</option>";
            foreach($color as $r) {
                $data .= "<option value='".$r['id']."'>".$r['name']."</option>";
                $idd .= $r['id'].',';
            }
            $idd = rtrim($idd,',');
        }
        $callback = array('data_color'=>$data,"color_id"=>$idd); // Masukan variabel html tadi ke dalam array $callback dengan index array : data_kota

        echo json_encode($callback);
    }

    public function get_warna_get() {
        $post = "";
        $page = $this->input->get("page");

        $url = "http://182.16.173.43/ApiEcommerce/public/api/list_warna";

        $p = post_url($url,$post); 
        $json = json_decode($p);
        foreach($json as $j) {
            $name = $j->color;
            $a = $this->model_color->check_color($name);
            if(empty($a) && !empty($name)) {
                $this->model_color->insert_color($name);
            }
        }

    }

    public function status_apps_post() {
        $code = $this->input->post('cod3');
        $token = $this->input->post('token');
        $post = array();
        $data = "";
        $status = -1;
        $msg = "Wrong code";
        $status_apps = array();
        $date_stat_apps = array();
        if(isset($code)) {
            
            if($code == $this->common->keycode()) {

                $status = 1;
                $msg = "success";

                if(!empty($token)) {
                            
                    $check = $this->user_model->get_user_by_token($token);
                    if($check->num_rows() > 0) {
                        $status = 1;
                        $msg = "success";
                        $user = $check->row();
                        
                        if(!empty($user)) {
                            $cabang = $user->cabang;
                            
                            $post = $this->orders->get_status_apps_user($user,1);
                            $posts[] = array("id"=>"100","text"=>"All","hex"=>"","total_orders"=>0);
                            if(!empty($post)) {
                                
                                foreach($post as $p) {
                                    
                                    //$posts[] = $p;
                                    
                                    $name_value = $p->name;
                                    if(!empty($p->total_orders)) {
                                        $name_value .= " ( $p->total_orders )";
                                    }
                                    if($p->id != 1) {
                                        $posts[] = array("id"=>$p->id,"text"=>$name_value,"hex"=>$p->hex,"total_orders"=>$p->total_orders);
                                    } else {
                                        $b = $this->db->query("SELECT COUNT(*) as total_orders FROM orders WHERE status_order IN (1,2,3) AND user_id = '$user->ID'")->row();
                                        if($user->top == 'CBD') {
                                            $name_value = $p->name;
                                        } else {
                                            $name_value = "Waiting Approval";
                                        }
                                        
                                        if(!empty($b->total_orders)) {
                                            $name_value .= " ( $b->total_orders )";
                                        }   
                                        $posts[] = array("id"=>$p->id,"text"=>$name_value,"hex"=>$p->hex,"total_orders"=>$b->total_orders);
                                    }

                                }
                            } else {
                                $posts = array();
                            }
                            //$data_array = array("status"=>$posts,"date" => $date_stat_apps);
                        }                
                    } else {
                        $status = -2;
                        $msg = lang("error_85");

                    }
                    
            } else {
                $status = -2;
                $msg = "Empty Token";
            }
/*
                $qry_stat = $this->db->query("SELECT * FROM status_apps")->result();
                $status_apps[] = array("id"=>"all","text"=>"All");
                foreach($qry_stat as $q) {

                    $status_apps[] = array("id"=>$q->id,"text"=>$q->name);
                }
                */

                $date_stat_apps[] = array("id"=>30,"text"=>"30 Days");
                $date_stat_apps[] = array("id"=>60,"text"=>"60 Days");
                $date_stat_apps[] = array("id"=>90,"text"=>"90 Days");

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
                'status_apps' => $posts,
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

    public function get_customer_post() {
        $data = $this->input->post("data");

        $js = json_decode($data,true);

        if(!empty($js[0]['ACCOUNTNUM'])) {
            $name = $js[0]['NAME'];
            $accountnum = $js[0]['ACCOUNTNUM'];
            $salesid = $js[0]['SALESRESPONSIBLE'];
            $salesname = $js[0]['SALESNAME'];
            $hp = phonenumber($js[0]['CELLULARPHONE']);
            $phone = phonenumber($js[0]['PHONE']);
            $alamatpajak = $js[0]['ALMPAJAK'];
            $namapajak = $js[0]['NAMAPAJAK'];
            $alamattagihan = $js[0]['ALAMATTAGIHAN'];
            $vaccount = $js[0]['VIRTUALACCOUNT'];
            $vaccountmandiri = $js[0]['VIRTUALACCOUNTMANDIRI'];
            $cabang = $js[0]['CABANGCBM'];
            $cabang = $this->model_cabang->check_cabang($cabang);
            $top_desc = $js[0]['DESCRIPTION'];
            $top = $js[0]['TOP'];
            $lokasi = $js[0]['LOKASI'];
            $credit = $js[0]['CREDIT LIMIT'];
            $prime = $js[0]['PRIME'];
            $apps = $js[0]['APPSACTIVED'];
         
            $pwd = 123456;

            $pass = $this->common->encrypt($pwd);
            $active = 1;

            $a = $this->register_model->check_account_is_free($accountnum);
            if(empty($a->ID)) { //register new
                $data = array("password"=>$pass,"username"=>$accountnum,"customer_name"=>$name,"cabang"=>$cabang,"accountnum"=>$accountnum,"salesid"=>$salesid,"salesname"=>$salesname,"vaccount"=>$vaccount,"vaccountmandiri"=>$vaccountmandiri,
                "alamatpajak"=>$alamatpajak,"namapajak"=>$namapajak,"address"=>$alamattagihan,"top"=>$top,"top_desc"=>$top_desc,"user_role" => 1, 
                "IP" => $_SERVER['REMOTE_ADDR'], "joined" => time(), "joined_date" => date("n-Y"),"lokasi"=>$lokasi,"phone"=>$phone,"hp"=>$hp,"credit_limit"=>$credit,"prime"=>$prime);
                $q = $this->register_model->add_user($data);
            } else { // update
                $data = array("customer_name"=>$name,"cabang"=>$cabang,"salesid"=>$salesid,"salesname"=>$salesname,"vaccount"=>$vaccount,"vaccountmandiri"=>$vaccountmandiri,
                "alamatpajak"=>$alamatpajak,"namapajak"=>$namapajak,"address"=>$alamattagihan,"top"=>$top,"top_desc"=>$top_desc,"lokasi"=>$lokasi,"phone"=>$phone,"hp"=>$hp,"prime"=>$prime);

                $q = $this->register_model->update_user($a->ID, $data);
            }

            echo "success";
          
         // echo $accountnum.' '.$prime.' '.$credit;
      } else {
          echo "error";
      }
       // $so_id = $this->input->post("so_id");
        //var_dump($data);
         //$this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
    }

    public function check_customer_get() {
        $post = "";
        $page = $this->input->get("page");
        for($i=1;$i<=$page;$i++) {
            $url = "http://182.16.173.43/ApiEcommerce/public/api/list_customer?page=$i";

            $p = post_url($url,$post);            
            $js = json_decode($p);
            $json = $js->data;
            var_dump($json);
            //echo $js->ACCOUNTNUM.'<Br>';
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
                    $cabang = $this->model_cabang->check_cabang($cabang);
                    $top_desc = $js->DESCRIPTION;
                    $top = $js->TOP;
                    $lokasi = $js->LOKASI;
        
                    $pwd = 123456;
        
                    $pass = $this->common->encrypt($pwd);
                    $active = 1;
        
                    $a = $this->register_model->check_account_is_free($accountnum);
                    if(empty($a->ID)) { //register new
                        $data = array("password"=>$pass,"username"=>$accountnum,"customer_name"=>$name,"cabang"=>$cabang,"accountnum"=>$accountnum,"salesid"=>$salesid,"salesname"=>$salesname,"vaccount"=>$vaccount,"vaccountmandiri"=>$vaccountmandiri,
                        "alamatpajak"=>$alamatpajak,"namapajak"=>$namapajak,"address"=>$alamattagihan,"top"=>$top,"top_desc"=>$top_desc,"user_role" => 1, 
                        "IP" => $_SERVER['REMOTE_ADDR'], "joined" => time(), "joined_date" => date("n-Y"),"lokasi"=>$lokasi,"phone"=>$phone,"hp"=>$hp);
                        $q = $this->register_model->add_user($data);
                    } else { // update
                        $data = array("customer_name"=>$name,"cabang"=>$cabang,"salesid"=>$salesid,"salesname"=>$salesname,"vaccount"=>$vaccount,"vaccountmandiri"=>$vaccountmandiri,
                        "alamatpajak"=>$alamatpajak,"namapajak"=>$namapajak,"address"=>$alamattagihan,"top"=>$top,"top_desc"=>$top_desc,"lokasi"=>$lokasi,"phone"=>$phone,"hp"=>$hp);
        
                        $q = $this->register_model->update_user($a->ID, $data);
                    }
        
                    //var_dump($q);
                }
            }
           

            //var_dump($js);
        }

    }

    public function check_customerdetail_get() {
        $curl = curl_init();
        $url = "http://182.16.173.43/ApiEcommerce/public/api/detail_customer";
        $post = array("accountnum"=>"HIJ0001");

            //$p = post_url($url,$post);  
            $data_json = json_encode($post);

            curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data_json,
            
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
            ));

            $response = curl_exec($curl);
		//var_dump($response);

            curl_close($curl);  

            $js = json_decode($response,true);
//			echo $js[0]['ACCOUNTNUM'].' '.$js[0]['CREDIT LIMIT'];
		     if(!empty($js[0]['ACCOUNTNUM'])) {
                    $name = $js[0]['NAME'];
                    $accountnum = $js[0]['ACCOUNTNUM'];
                    $salesid = $js[0]['SALESRESPONSIBLE'];
                    $salesname = $js[0]['SALESNAME'];
                    $hp = phonenumber($js[0]['CELLULARPHONE']);
                    $phone = phonenumber($js[0]['PHONE']);
                    $alamatpajak = $js[0]['ALMPAJAK'];
                    $namapajak = $js[0]['NAMAPAJAK'];
                    $alamattagihan = $js[0]['ALAMATTAGIHAN'];
                    $vaccount = $js[0]['VIRTUALACCOUNT'];
                    $vaccountmandiri = $js[0]['VIRTUALACCOUNTMANDIRI'];
                    $cabang = $js[0]['CABANGCBM'];
                    $cabang = $this->model_cabang->check_cabang($cabang);
                    $top_desc = $js[0]['DESCRIPTION'];
                    $top = $js[0]['TOP'];
                    $lokasi = $js[0]['LOKASI'];
				    $credit = $js[0]['CREDIT LIMIT'];
				    $prime = $js[0]['PRIME'];
				    $apps = $js[0]['APPSACTIVED'];
				 
				 $pwd = 123456;
        
                    $pass = $this->common->encrypt($pwd);
                    $active = 1;
        
                    $a = $this->register_model->check_account_is_free($accountnum);
                    if(empty($a->ID)) { //register new
                        $data = array("password"=>$pass,"username"=>$accountnum,"customer_name"=>$name,"cabang"=>$cabang,"accountnum"=>$accountnum,"salesid"=>$salesid,"salesname"=>$salesname,"vaccount"=>$vaccount,"vaccountmandiri"=>$vaccountmandiri,
                        "alamatpajak"=>$alamatpajak,"namapajak"=>$namapajak,"address"=>$alamattagihan,"top"=>$top,"top_desc"=>$top_desc,"user_role" => 1, 
                        "IP" => $_SERVER['REMOTE_ADDR'], "joined" => time(), "joined_date" => date("n-Y"),"lokasi"=>$lokasi,"phone"=>$phone,"hp"=>$hp,"credit_limit"=>$credit,"prime"=>$prime);
                        $q = $this->register_model->add_user($data);
                    } else { // update
                        $data = array("customer_name"=>$name,"cabang"=>$cabang,"salesid"=>$salesid,"salesname"=>$salesname,"vaccount"=>$vaccount,"vaccountmandiri"=>$vaccountmandiri,
                        "alamatpajak"=>$alamatpajak,"namapajak"=>$namapajak,"address"=>$alamattagihan,"top"=>$top,"top_desc"=>$top_desc,"lokasi"=>$lokasi,"phone"=>$phone,"hp"=>$hp,"prime"=>$prime);
        
                        $q = $this->register_model->update_user($a->ID, $data);
                    }
				  
				 // echo $accountnum.' '.$prime.' '.$credit;
			  }
        /*
                    
        
                    //var_dump($q);
					
                }
				*/
		//var_dump($response);
	//	echo $json[0]->ACCOUNTNUM;
//            $json = $js->data;
            //echo $js->ACCOUNTNUM.'<Br>';
		/*
            foreach($json as $js) {
          
				
            }
			*/

    }
    
    public function check_customerprime_get() {
        $url = "http://182.16.173.43/ApiEcommerce/public/api/list_customer";

            $p = post_url($url,$post);            
            $js = json_decode($p);
            $json = $js->data;
            var_dump($json);
            //echo $js->ACCOUNTNUM.'<Br>';
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
                    $cabang = $this->model_cabang->check_cabang($cabang);
                    $top_desc = $js->DESCRIPTION;
                    $top = $js->TOP;
                    $lokasi = $js->LOKASI;
        
                    $pwd = 123456;
        
                    $pass = $this->common->encrypt($pwd);
                    $active = 1;
        
                    $a = $this->register_model->check_account_is_free($accountnum);
                    if(empty($a->ID)) { //register new
                        $data = array("password"=>$pass,"username"=>$accountnum,"customer_name"=>$name,"cabang"=>$cabang,"accountnum"=>$accountnum,"salesid"=>$salesid,"salesname"=>$salesname,"vaccount"=>$vaccount,"vaccountmandiri"=>$vaccountmandiri,
                        "alamatpajak"=>$alamatpajak,"namapajak"=>$namapajak,"address"=>$alamattagihan,"top"=>$top,"top_desc"=>$top_desc,"user_role" => 1, 
                        "IP" => $_SERVER['REMOTE_ADDR'], "joined" => time(), "joined_date" => date("n-Y"),"lokasi"=>$lokasi,"phone"=>$phone,"hp"=>$hp);
                        $q = $this->register_model->add_user($data);
                    } else { // update
                        $data = array("customer_name"=>$name,"cabang"=>$cabang,"salesid"=>$salesid,"salesname"=>$salesname,"vaccount"=>$vaccount,"vaccountmandiri"=>$vaccountmandiri,
                        "alamatpajak"=>$alamatpajak,"namapajak"=>$namapajak,"address"=>$alamattagihan,"top"=>$top,"top_desc"=>$top_desc,"lokasi"=>$lokasi,"phone"=>$phone,"hp"=>$hp);
        
                        $q = $this->register_model->update_user($a->ID, $data);
                    }
        
                    var_dump($q);
                }
            }

    }

    public function listcategory_post() 
	{
		$code = $this->input->post('cod3');
        if(isset($code)) {
           
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                $name =  $this->input->post('name');
                $artist = $this->input->post('artist');
                    $warning = '';
                    $list = array();
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {
                        $status = 1;
                        $msg = "success";

                        $check = $this->user_model->get_user_by_token($token);
                        
                        $user = $check->row();
                        $member = array();
                        if(!empty($user)) {
                            $status = 1;

                            $msg = "success";

                            $page = intval($this->input->post("page"));
                            $limit = intval($this->input->post("limit"));
                          
                            
                            if(empty($page)) {
                                $page = 0;
                            } else {
                                $page = $page-1;
                            }

                            $page = $page*$limit;

                            $list2 = $this->model_category->category_view_all($name,$page,$limit);
                            foreach($list2 as $l) {
                                
                                $image = "";
                                if(!empty($l->image)) {
                                    $image = base_url("uploads/category/thumbs/$l->image");
                                }
                                $l->image = $image;
                                
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

    public function iconcategory_post() 
	{
		$code = $this->input->post('cod3');
        if(isset($code)) {
           
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                    $warning = '';
                    $list = array();
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {
                        $status = 1;
                        $msg = "success";

                        $check = $this->user_model->get_user_by_token($token);
                        
                        $user = $check->row();
                        $member = array();
                        if(!empty($user)) {
                            $status = 1;
                            $msg = "success";

                            $list2 = $this->model_category->category_all();
                            foreach($list2 as $l) {
                                
                                $image = "";
                                if(!empty($l->image)) {
                                    $image = base_url("uploads/category/thumbs/$l->image");
                                }
                                $l->image = $image;

                                $icon_image = "";
                                if(!empty($l->icon_image)) {
                                    $icon_image = base_url("uploads/category_icon/thumbs/$l->icon_image");
                                }
                                $l->icon_image = $icon_image;
                                
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

    
    

}
