<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Order extends REST_Controller
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
        $this->load->model("model_users","users");
        $this->load->model("model_promobanner","promobanner");
        $this->load->model("model_specialpromo","specialpromo");
        $this->load->helper('email');
        $this->load->model("model_cart","cart");
        $this->load->model("model_orders","order");

        date_default_timezone_set('Asia/Jakarta');
    }

    function test_notif_order_get() {
        $order_id = 67;
        $status_order = 1;
        $datadb2 = $this->db->query("SELECT o.*,u.device_id FROM orders o LEFT JOIN users u ON u.username=o.customer_id WHERE o.id = '$order_id'")->row_array();
        $a= send_notif_order($datadb2,$status_order);
        var_dump($a);
    }

    public function list_post()
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

                if(empty($limit)) {
                    $limit = 10;
                }

                if(empty($type)) {
                    $type = 1;
                }

                if(empty($page)) {
                    $page = 0;
                } else {
                    $page = $page-1;
                }

                $page = $page*$limit;

                if(!empty($period)) {
                    $date_to = date("Y-m-d");
                    $date_from = date('Y-m-d', strtotime("-$period days"));
                    //$a = date("Y-m-d", strtotime("$date_from -$period Days"));
                   
                }
                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {
                            
                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();
                                
                                if(!empty($user)) {
                                    $cabang = $user->cabang;
                                    $post = $this->cart->get_list_order($user,$date_from,$date_to,$name,$type,$status_apps,$page,$limit);
                                    if(!empty($post)) {
                                        
                                        foreach($post as $p) {
                                            $img = "";
                                            if(!empty($p->banner_image)) {
                                                $img = base_url() . $this->settings->info->upload_path_thumbs . "/" . $p->banner_image;
                                            }
                                            $total = round($p->total);
                                            $p->total = $total;
                                            $format_total = harga($total);
                                            /*
                                            if($p->status_order == 1) {
                                                $status_order = "On Process";
                                            } else {
                                                $status_order = "Pending";
                                            }
                                            */
                                            $status_order = cek_status_order_api($p->status_order,"",$user->top);

                                            $p->tracking_button = cek_tracking_order($p->status_order);

                                            $p->status_order_name = $status_order;

                                            $p->date_buy = tgl_view($p->dt_created);
                                            
                                            $p->format_total = $format_total;
                                            $img = "";
                                            if(!empty($p->banner_image)) {
                                                $img = base_url() . $this->settings->info->upload_path_thumbs . "/" . $p->banner_image;
                                            }
                                            $p->banner_image = $img;
                                            
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

    public function listsjdetail_post()
    {
        $code = $this->input->post('cod3');
        $sj_id = $this->input->post('sj_detail_id');
        $cart = array();
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
                                $post2 = array();
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();
                                
                                if(!empty($user)) {
                                    $cabang = $user->cabang;

                                    
                                    $post = $this->cart->get_sj_detail($sj_id);
                                    if(!empty($post)) {
                                        foreach($post['data'] as $p) {
                                            $img = "";
                                            if(!empty($p->banner_image)) {
                                                $img = base_url() . $this->settings->info->upload_path_thumbs . "/" . $p->banner_image;
                                            }
    
                                            $p->image = $img;
                                            
                                            $cart[] = $p;
                                        }
    
                                    } else {
                                        $status = -1;
                                        $msg = "No Data";
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
                    'sj_no' => $post['sj_no'],
                    'tgl_beli' => $post['tgl_beli'],
                    'status_sj'=>$post['status_sj'],
                    'status_tracking'=>$post['status_tracking'],
                    'order_id' => $post['order_id'],
                    'sj' => $cart
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

                                        $total = round($p->total);
                                        $shipping_total = $p->shipping_total;
                                        $total_all = $total+$shipping_total;
                                        $p->format_total_before = harga($total_all);

                                        $gr_total = $total_all;

                                        $discount = 0;

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

                                        $tracking = 0;
                                        if($p->status_order == 9) {
                                            $tracking = 1;
                                        }

                                        $p->total_all = $gr_total;
                                        $p->format_total = harga($total);
                                        $p->format_shipping_total = harga($shipping_total);
                                        $p->format_total_all = harga($gr_total);

                                        $p->format_date = tgl_indoo($p->dt_created);
                                        $p->format_tgl_np = tgl_indoo($p->tgl_np); 
                                        $p->tracking = $tracking;   
                                        $p->status_order_name = cek_status_order_api($p->status_order,"",$user->top);
                                        /*
                                        if(!empty($p->cancel_note)) {
                                            $p->status_order_name = cek_status_order_api($p->status_order).' \n '.$p->cancel_note.'';
                                        } else if(!empty($p->reason_reject)) {
                                            $p->status_order_name = cek_status_order_api($p->status_order).' \n '.$p->reason_reject.'';
                                        } else {
                                            $p->status_order_name = cek_status_order_api($p->status_order);
                                        }

                                        */

                                        $cl = $this->cart->check_cancel_button($p->status_order);

                                        /*
                                        if($p->status_order <= 1) {
                                            $cancel_button = 1;
                                            $cancel_str = "Cancel Order";
                                        }
                                        else if($p->status_order > 1 && $p->status_order <= 3) {
                                            $cancel_button = 1;
                                            $cancel_str = "Request Cancellation";
                                        }
                                        */
                                        
                                        
                                        $p->cancel_button = $cl['status'];
                                        $p->cancel_button_name = $cl['cancel_name'];
                                        


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

    public function updatesalesorder_post() {
        $so_id = $this->input->post("so_id");
        $userid = $this->input->post("userid");
        $draftorderid = $this->input->post("draftorderid");

//        $message = $this->order->update_salesorder($so_id);
            $message = $this->order->update_salesorder_new($so_id,$userid,$draftorderid);

        //var_dump($so_id);
        if($message['status'] == 1) {
            echo "success";
        } else {
            echo "error";
        }

         //$this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
    }

    public function cancelcontract_post() {
        $orderid = $this->input->post("orderid");
        $note = $this->input->post("note");

        //pengajuan pembatalan ke customer, update cancel_customer jadi 1
        //$message = $this->order->update_salesorder($so_id);

        //var_dump($so_id);

        if($message['status'] == 1) {
            echo "success";
        } else {
            echo "error";
        }

        // $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
    }

    public function verification_np_post() {
        $draftorderid = $this->input->post("draftorderid");
       // $so_id = $this->input->post("so_id");
        $nonp = $this->input->post("nonp");
        $userid = $this->input->post("userid");

        $now = date("Y-m-d H:i:s");

        $ada = $this->db->query("SELECT * FROM orders WHERE order_apps_id = '$draftorderid'")->row();
        $ada2 = $this->db->query("SELECT * FROM orders WHERE order_apps_id = '$draftorderid'")->row_array();
       

      //  $message = $this->order->crud_np($draftorderid,$detailorder,$userid);
        $stt = 2;      
        if($ada->status_order != 19) {
            $datanp = array("status_order"=>$stt,"np_id"=>$nonp,"np_status"=>1,"tgl_np"=>$now,"np_user_id"=>$userid);
            $message = $this->order->updateorder($datanp,$ada);
    
            $admins = $this->users->getAdminSales($ada->sales_id)->result();
    
            foreach($admins as $ad) {
                $deviceid = $ad->device_id;
                $ada2['device_id'] = $deviceid;
                $this->order->send_notif_orderadminnew($ada2,$stt);
    
            }
        } else {
            if($ada->status_order == 19) {
                $datanp = array("np_id"=>$nonp,"np_status"=>1,"tgl_np"=>$now,"np_user_id"=>$userid);
                $message = $this->order->updateorder($datanp,$ada);
                $this->cancelax($ada->order_apps_id,$ada->cancel_note);
            }

//            echo "success";
        }


        //var_dump($message);
        //var_dump($so_id);
        
        if($message['status'] == 1) {
            echo "success";
        } else {
            echo "error";
        }
        
         //$this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
    }

    public function cancelsj_post() {
        $draftorderid = $this->input->post("draftorderid");
        $userid = $this->input->post("userid");
        $nosj = $this->input->post("nosj");
        $reason = $this->input->post("reason");

        $now = date("Y-m-d H:i:s");

        $ada = $this->db->query("SELECT * FROM orders WHERE order_apps_id = '$draftorderid'")->row();

        $datadb = array("cancel"=>1,"reason_cancel"=>$reason,"tgl_cancel"=>$now);

        $message = $this->order->update_sj_cancel($datadb,$nosj);

        if($message['status'] == 1) {
            echo "success";
        } else {
            echo "error";
        }
    }

    public function newsj_post() {
        $draftorderid = $this->input->post("draftorderid");
        $userid = $this->input->post("userid");
        $nosj = $this->input->post("nosj");
        $item = $this->input->post("item");

        $now = date("Y-m-d H:i:s");

        $ada = $this->db->query("SELECT * FROM orders WHERE order_apps_id = '$draftorderid'")->row();
        $sjdate = date("Y-m-d");
        $datadb = array("draftorderid"=>$draftorderid,"userid"=>$userid,"nosj"=>$nosj,"item"=>$item);
        $datasj = array("sj_id"=>$nosj,"tgl_sj"=>$sjdate,"user_id"=>$userid,
									"draft_order_id"=>$draftorderid,"sj_items"=>$item,"orders_id"=>$ada->id);
        $message = $this->order->crud_salesordersj($datasj,$draftorderid);
        
        /*
        $datasalesdetail = array("sales_order_id"=>$so_id,"user_id"=>"admin","draft_order_id"=>$draftorderid,"userid_ax"=>$userid,
        "approve"=>0,"payment"=>$ada->top,"orders_id"=>$ada->id);
        $message = $this->order->crud_salesorderdetail($datasalesdetail,$draftorderid);
        
        $message = "";
        if(!empty($ada)) {
            $message['status'] = 1;
        } else {
            $message['status'] = -1;
        }
*/
        if($message['status'] == 1) {
            echo "success";
        } else {
            echo "error";
        }
        
    }

    public function newinvoice_post() {
        $draftorderid = $this->input->post("draftorderid");
        $userid = $this->input->post("userid");
        $soid = $this->input->post("soid");
        $invoiceid = $this->input->post("invoiceid");
        $invoiceamount = $this->input->post("invoiceamount");
        $duedate = $this->input->post("duedate");
        $userid = $this->input->post("userid");

        $now = date("Y-m-d H:i:s");

        $ada = $this->db->query("SELECT * FROM orders WHERE order_apps_id = '$draftorderid'")->row();
        $sjdate = date("Y-m-d");
        if(!empty($ada->id)) {
            /*
            $datadb = array("draftorderid"=>$draftorderid,"userid"=>$userid,"soid"=>$soid,"invoiceid"=>$invoiceid,"invoiceid"=>$invoiceid
            ,"invoiceamount"=>$invoiceamount);
            $message = $this->order->crud_invoice($datadb,$draftorderid);
            */
    
        } else {
            $message['status'] = -1;
        }
       
        if($message['status'] == 1) {
            echo "success";
        } else {
            echo "error";
        }
        
    }

    public function newsalesorder_post() {
        $draftorderid = $this->input->post("draftorderid");
        $so_id = $this->input->post("so_id");
        $userid = $this->input->post("userid");
        //$detailorder = $this->input->post("detailorder");

        $ada = $this->db->query("SELECT * FROM orders WHERE order_apps_id = '$draftorderid'")->row();
        $ada2 = $this->db->query("SELECT * FROM orders WHERE order_apps_id = '$draftorderid'")->row_array();
       // $message = $this->order->crud_salesorder($draftorderid,$detailorder,$userid);
        //$message = $this->order->crud_salesorder_new($draftorderid,$userid,$so_id);

        $datasalesdetail = array("sales_order_id"=>$so_id,"user_id"=>"admin","draft_order_id"=>$draftorderid,"userid_ax"=>$userid,
        "approve"=>0,"payment"=>$ada->top,"orders_id"=>$ada->id);
        $message = $this->order->crud_salesorderdetail($datasalesdetail,$draftorderid);

        $dataso = array("so_status"=>1,"status_order"=>3);
        $this->order->updateorder($dataso,$ada);

        $admins = $this->users->getAdminSales($ada->sales_id)->result();
        $stt = 3;
        foreach($admins as $ad) {
            $deviceid = $ad->device_id;
            $ada2['device_id'] = $deviceid;
            $this->order->send_notif_orderadminnew($ada2,$stt);
           //echo send_notif_orderadmin($ad,$draftorderid,$stt);

        }

        //var_dump($so_id);
        if($message['status']) {
            echo "success";
        } else {
            echo "error";
        }
         //$this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
    }

    function test_notifstaff_get() {
        $get = $this->input->get('type');
        $deviceid = $this->input->get('deviceid');
        //$deviceid = "ckmjit3WSxeNMyElUG7QtT:APA91bGrlrUriCmv1z1tIqjJaculxcWd9rDCBjmfn8IpAAF3xkc8vV2BFmqYo0z0QTXzD4JYjJ42Tnu26ITSbbGN70Q52H6fgRhHem92pIsHYf7z-ATFE5VC7zSsolhK5AOV-XHJqhHj";
        //$deviceid = "dtYG-sjcS7CyPOmTtz69S3:APA91bHO3j1oofUz8fKoEqHjzpiyNB9jO6w0eNaYY3Ey3xe0FySKxPle8t2UuxX-pvSo_wD2uw8dsx4m0LLdPgm_YgqS85WStMQIHRYYP4M0uqLgHYxexKHJvD5OlWlsXGf0jO53nNsJ";
        //$deviceid = "dtYG-sjcS7CyPOmTtz69S3:APA91bHO3j1oofUz8fKoEqHjzpiyNB9jO6w0eNaYY3Ey3xe0FySKxPle8t2UuxX-pvSo_wD2uw8dsx4m0LLdPgm_YgqS85WStMQIHRYYP4M0uqLgHYxexKHJvD5OlWlsXGf0jO53nNsJ";
//        $deviceid = "ckmjit3WSxeNMyElUG7QtT:APA91bGrlrUriCmv1z1tIqjJaculxcWd9rDCBjmfn8IpAAF3xkc8vV2BFmqYo0z0QTXzD4JYjJ42Tnu26ITSbbGN70Q52H6fgRhHem92pIsHYf7z-ATFE5VC7zSsolhK5AOV-XHJqhHj";
        $image_post = "";

        if($get == 1) { // Chat
            $title = "Chat Notification";
            $type = 1;

            $message = "Hello whatsup";
        } else if($get == 2) { // Status Order Update
            $title = "Order Notification";
            $type = 2;
            $message = "Pesanan anda dengan nomor DF/12/213 telah di Verifikasi";
        }  else if($get == 3) { // Perubahan TOP
            $title = "Payment Status Notification";
            $type = 3;
            $message = "Payment Status Anda mengalami perubahan menjadi CBD";
        }  else if($get == 4) { // Payment Notif
            $title = "Payment Notification";
            $type = 4;
            $message = "Mohon bayar Invoice anda nomor INV/123/123/123 agar dapat di proses";
        } else {
            $type = 5;
            $message = "Orderan Anda berhasil di Cancel oleh CBM";
            $title = "Cancel Order Notification";
        }

//        $statusdata = array();
/*
        if($type == 5) {
            $body = array(
                'registration_ids' => array($deviceid), // apabila lebih dari 1 devaccouniceid tapi content sama yg dikirim tinggal dibuat array
                'notification' => array('body' => $message, 'title' => $title),
                'data' => array("type"=>$type)
            );            
    
        } else {
            */
            if($type == 2) {
                $statusdata = array("statusPay"=>$message,"orderid"=>28);
            } else {
                $statusdata = array("statusPay"=>$message);
            }

            $body = array(
                'registration_ids' => array($deviceid), // apabila lebih dari 1 deviceid tapi content sama yg dikirim tinggal dibuat array
             //   'notification' => array('body' => $message, 'title' => $title,'image'=>$image_post),
               'data' => array("type"=>$type,"body"=>$message,"title"=>$title,"orderid"=>145),
               
            );


    
//        }

        $headers = array
                (
                    'Authorization: key=' . API_ACCESS_KEY,
                    'Content-Type: application/json'
                );
        #Send Reponse To FireBase Server	
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $body ) );
        $result = curl_exec($ch );

        curl_close( $ch );
        
        echo json_encode($body);
        var_dump($result);
    }

    function test_notifstaff3_get() {
        $status_order = 'pending';
        $sales_id = 'LI01';
        $qry = $this->db->query("SELECT u.device_id,u.id FROM admin u WHERE u.sales_id = '$sales_id'")->result_array();
        $order_apps_id = 'DF/2021/08/0064';
        $idd = 145;
        foreach($qry as $q) {
            echo $q['id'].'<br>';
            $datadb2['order_apps_id'] = $order_apps_id;
            $datadb2['device_id'] = $q['device_id'];
            $datadb2['id'] = $idd;
            echo send_notif_orderadmin($datadb2,$status_order);
        }

    }

    function test_notifchat_get() {
        $get = $this->input->get('type');
        $deviceid = $this->input->get('deviceid');
        //$deviceid = "ckmjit3WSxeNMyElUG7QtT:APA91bGrlrUriCmv1z1tIqjJaculxcWd9rDCBjmfn8IpAAF3xkc8vV2BFmqYo0z0QTXzD4JYjJ42Tnu26ITSbbGN70Q52H6fgRhHem92pIsHYf7z-ATFE5VC7zSsolhK5AOV-XHJqhHj";
        //$deviceid = "dtYG-sjcS7CyPOmTtz69S3:APA91bHO3j1oofUz8fKoEqHjzpiyNB9jO6w0eNaYY3Ey3xe0FySKxPle8t2UuxX-pvSo_wD2uw8dsx4m0LLdPgm_YgqS85WStMQIHRYYP4M0uqLgHYxexKHJvD5OlWlsXGf0jO53nNsJ";
        //$deviceid = "dtYG-sjcS7CyPOmTtz69S3:APA91bHO3j1oofUz8fKoEqHjzpiyNB9jO6w0eNaYY3Ey3xe0FySKxPle8t2UuxX-pvSo_wD2uw8dsx4m0LLdPgm_YgqS85WStMQIHRYYP4M0uqLgHYxexKHJvD5OlWlsXGf0jO53nNsJ";
//        $deviceid = "ckmjit3WSxeNMyElUG7QtT:APA91bGrlrUriCmv1z1tIqjJaculxcWd9rDCBjmfn8IpAAF3xkc8vV2BFmqYo0z0QTXzD4JYjJ42Tnu26ITSbbGN70Q52H6fgRhHem92pIsHYf7z-ATFE5VC7zSsolhK5AOV-XHJqhHj";
        $image_post = "";

        if($get == 1) { // Chat
            $title = "Chat Notification";
            $type = 1;

            $message = "Hello whatsup";
        } else if($get == 2) { // Status Order Update
            $title = "Order Notification";
            $type = 2;
            $message = "Pesanan anda dengan nomor DF/12/213 telah di Verifikasi";
        }  else if($get == 3) { // Perubahan TOP
            $title = "Payment Status Notification";
            $type = 3;
            $message = "Payment Status Anda mengalami perubahan menjadi CBD";
        }  else if($get == 4) { // Payment Notif
            $title = "Payment Notification";
            $type = 4;
            $message = "Mohon bayar Invoice anda nomor INV/123/123/123 agar dapat di proses";
        } else {
            $type = 5;
            $message = "Orderan Anda berhasil di Cancel oleh CBM";
            $title = "Cancel Order Notification";
        }

//        $statusdata = array();
/*
        if($type == 5) {
            $body = array(
                'registration_ids' => array($deviceid), // apabila lebih dari 1 devaccouniceid tapi content sama yg dikirim tinggal dibuat array
                'notification' => array('body' => $message, 'title' => $title),
                'data' => array("type"=>$type)
            );            
    
        } else {
            */
            if($type == 2) {
                $statusdata = array("statusPay"=>$message,"orderid"=>28);
            } else {
                $statusdata = array("statusPay"=>$message);
            }

            $body = array(
                'registration_ids' => array($deviceid), // apabila lebih dari 1 deviceid tapi content sama yg dikirim tinggal dibuat array
               // 'notification' => array('body' => $message, 'title' => $title,'image'=>$image_post),
               'data' => array("type"=>$type,"results"=>$statusdata),
            );


    
//        }

        $headers = array
                (
                    'Authorization: key=' . API_ACCESS_KEY,
                    'Content-Type: application/json'
                );
        #Send Reponse To FireBase Server	
        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $body ) );
        $result = curl_exec($ch );

        curl_close( $ch );
        
        echo json_encode($body);
        var_dump($result);
    }

    function test_notif_get() {
        $userid = "ALD0007";
        $ord = (object)array("user_id"=>$userid);
        $datadb = array("so_status"=>1);
        $s = send_notif($datadb,$ord);
        echo json_encode($s);
    }

    function test_get() {
        $serialize = 'a:1:{s:8:"original";a:1:{i:0;a:8:{s:11:"PenawaranID";s:15:"NP/03/0121/0001";s:11:"CustAccount";s:7:"CIP0126";s:16:"SalesResponsible";s:4:"AR05";s:12:"StsPenawaran";s:1:"3";s:11:"OngkosKirim";s:13:".000000000000";s:6:"Amount";s:21:"16930800.000000000000";s:12:"DraftOrderId";s:0:"";s:2:"so";a:2:{i:0;a:4:{s:7:"SalesId";s:12:"03/0121/0001";s:11:"SalesStatus";s:1:"3";s:7:"Payment";s:3:"30D";s:7:"Approve";s:1:"1";}i:1;a:4:{s:7:"SalesId";s:12:"03/0121/0043";s:11:"SalesStatus";s:1:"3";s:7:"Payment";s:3:"30D";s:7:"Approve";s:1:"1";}}}}}';
        $json = unserialize($serialize);
        //echo json_encode($d);
//        var_dump($json);
        foreach($json['original'] as $j) {
            $np_id = $j['PenawaranID'];
            //echo $np_id;
            if(!empty($j['so'])) {
                $so_array = $j['so'];
            
                foreach($so_array as $s) {
                    $salesid = $s['SalesId'];
                    $salesstat = $s['SalesStatus'];
                    $payment = $s['Payment'];
                    $approve = $s['Approve'];
    
                    echo $salesid.' '.$salesstat.' '.$payment.' '.$approve;
                    if(!empty($s['sj'])) {
                        $sj_array = $s['sj'];
                        foreach($sj_array as $sj) {
                            $sjid = $sj['SJID'];
                            $sjdate = $sj['SJDATE'];
                            $sjref = $sj['transRefId'];
                            $sjbatal = $sj['Batal'];
        
                            echo $sjid.' '.$sjdate.' '.$sjref.' '.$sjbatal;
                        }
                    }

                    if(!empty($s['invoice'])) {
                        $inv_array = $s['invoice'];
                        foreach($inv_array as $in) {
                            $invid = $in['InvoiceId'];
                            $invamt = $in['InvoiceAmount'];
        
                            echo $invid.' '.$invamt;
                        }
                    }
    
                }

            }

        }
        //print_r($d);
    }

    public function cancelflagorder_post() {
        $draftorderid = $this->input->post("draftorderid");
        $userid = $this->input->post("userid");
        $reason = $this->input->post("reason");

        $message = $this->order->crud_cancelflagorder($draftorderid,$userid,$reason);

        //var_dump($so_id);

        if($message['status'] == 1) {
            echo "success";
        } else {
            echo "error";
        }

        // $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
    }

    
    public function cancelorder_post() {
        $draftorderid = $this->input->post("draftorderid");
        $id = $this->input->post("id");
        

        $message = $this->order->crud_cancelorder($draftorderid);

        //var_dump($so_id);
        if($message['status'] == 1) {
            echo "success";
        } else {
            echo "error";
        }
        // $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
    }

    function cancelax($orderid,$note) {

            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://182.16.173.43/ApiEcommerce/public/api/cancel_contract',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('orderid' => $orderid,'note' => $note,'batal' => '0'),
            ));

            $response = curl_exec($curl);

            curl_close($curl);
            return $response;

    }

    public function cancelorderapps_post()
    {
        $code = $this->input->post('cod3');
        $post = array();
        $data = "";
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');  
                $order_id =  $this->input->post('order_id'); 
                $note = $this->input->post("note"); 
                $status_order = 0;
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {
                            
                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();
                                
                                if(!empty($user)) {
                                    $cabang = $user->cabang;
                                    $b = $this->order->crud_cancelorderapps($order_id,$note);
                                    $status = $b['status'];
                                    $msg = $b['message'];
                                    if($status == 1) {
                                        $this->cancelax($b['df'],$note);
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

    public function detailstatus_post()
    {
        $code = $this->input->post('cod3');
        $post = array();
        $data = "";
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');  
                $order_id =  $this->input->post('order_id');  
                $status_order = 0;
                $status_list_name = null;

                
                
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {
                            
                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();
                                
                                if(!empty($user)) {
                                    $cabang = $user->cabang;


                                    $pd = $this->order->get_order_id($order_id);
                                    $note = "";

                                    if(!empty($pd->cancel_note)) {
                                        $note = $pd->cancel_note;
                                    }
                                    if(!empty($pd->reason_reject)) {
                                        $note = $pd->reason_reject;
                                    }
                                    if(!empty($pd->id)) {
                                        if($pd->top == 'CBD') {
                                            $status_list_name['state_01'] = array("id"=>0,"name"=>"Menunggu Pembayaran");
                                            $status_list_name['state_02'] = array("id"=>1,"name"=>"Pesanan diproses");
                                            $status_list_name['state_03'] = array("id"=>2,"name"=>"Dalam Pengiriman");
                                            $status_list_name['state_04'] = array("id"=>3,"name"=>"Pesanan Tiba");
                                        } else {
                                            $status_list_name['state_01'] = array("id"=>0,"name"=>"Pending");
                                            $status_list_name['state_02'] = array("id"=>1,"name"=>"Pesanan diproses");
                                            $status_list_name['state_03'] = array("id"=>2,"name"=>"Dalam Pengiriman");
                                            $status_list_name['state_04'] = array("id"=>3,"name"=>"Pesanan Tiba");
                                        }
                                    }

                                    $post = $this->cart->get_detailstatus($user,$order_id);
                                    if(!empty($post)) {
                                        
                                        
                                        foreach($post as $p) {
                                            if(!empty($p->sj_id)) {
                                                $p->status_apps_name = updateString($p->status_apps_name,"no_resi",$p->sj_id);
                                            }
                                            $tgl = $p->dt_created;
                                            $p->format_date = tgl_indoo($tgl);
                                            $p->format_time = format_time($tgl);
                                            
                                            $status_order = $p->status_apps_id;

                                            if(!empty($note)) {
                                                if($status_order == 17 || $status_order == 10) {
                                                    $p->status_apps_name = $p->status_apps_name."\n$note";
                                                }
                                            }
                                           // $status_order = $p->status_apps_id-3;

                                            $posts[] = $p;
                                        }
                                        if($pd->top == 'CBD') {
                                            $status_order = $this->order->get_statusorder($status_order);
                                        } else {
                                            $status_order = $this->order->get_statusorder_top($status_order);
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
                'status_order' => $status_order,                
                'status_list_name' => $status_list_name,
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
    
    public function listnotification_post()
    {
        $code = $this->input->post('cod3');
        $post = array();
        $data = "";
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');       
                $type = $this->input->post('type');   
                
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
                            
                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();
                                
                                if(!empty($user)) {
                                    $cabang = $user->cabang;
                                    $post = $this->order->get_list_notification($user,$type,$page,$limit);
                                    if(!empty($post)) {
                                        
                                        foreach($post as $p) {

                                            $p->format_date = date('d F Y H:i A',  $p->timestamp);
                                            
                                            if($p->type == 2){
                                                $a = $this->specialpromo->specialpromo_edit($p->fromid)->row();
                                                $img = "";
                                                if(!empty($a->image)) {
                                                    $img = base_url() . "banner/" . $a->image;
                                                }
                                                $p->banner_image = $img;
                                                $p->message = $a->name;
                                                $p->sub_message = $a->description;
                                            } else {
                                                $p->banner_image = "";
                                                $p->sub_message = "";
                                            }
                                            
                                            
                                            
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

}
