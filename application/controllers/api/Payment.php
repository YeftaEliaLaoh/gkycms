<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Payment extends REST_Controller
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
        $this->load->helper('email');
        $this->load->model("model_payment","payment");
        $this->load->model("model_orders","orders");

        date_default_timezone_set('Asia/Jakarta');
    }

    

    public function invoice_list_post()
    {
        $code = $this->input->post('cod3');
        $post = array();
        $data = "";
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');  
                $type = $this->input->post("type");
                /*
                $date_from =  $this->input->post('date_from');  
                $date_to =  $this->input->post('date_to');
                $name =  $this->input->post('name');                  
                */
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
                                    $post = $this->payment->get_list_payment($user,$type);
                                    if(!empty($post)) {
                                        
                                        foreach($post as $p) {
                                            $total = $p->total;
                                            

                                            $p->format_total = harga($total);
                                            $p->invoice_pay_date_format = tgl_view($p->invoice_pay_date);
                                            $p->invoice_date_format = tgl_view($p->invoice_date);
                                            
                                            
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

    public function transactionpayment_list_post()
    {
        $code = $this->input->post('cod3');
        $post = array();
        $data = "";
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');  
                $type = $this->input->post("type");
                /*
                $date_from =  $this->input->post('date_from');  
                $date_to =  $this->input->post('date_to');
                $name =  $this->input->post('name');                  
                */
                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {
                        $gr_total = 0;
                            
                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();
                                
                                if(!empty($user)) {
                                    $cabang = $user->cabang;
                                    $post = $this->payment->get_list_purchase_all($user);
                                    if(!empty($post)) {
                                        
                                        foreach($post as $p) {
                                            $total = $p->total;

                                             $s = check_status_payment($p->status_payment);
                                            $p->status = $s['value'];
                                            $p->hex = $s['hex'];
                                            $p->format_total = harga($total);
                                            $p->purchase_total = harga($total);
                                            $p->invoice_pay_date_format = tgl_view($p->invoice_pay_date);
                                            $p->invoice_date_format = tgl_view($p->invoice_date);

                                            $p->payment = $this->payment->get_list_payment_id($p->id);
                                            
                                            $gr_total += $total;
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
                'data' => $posts,
                'grand_total'=>$gr_total
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

    public function bank_list_post()
    {
        $code = $this->input->post('cod3');
        $post = array();
        $data = "";
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');  
                /*
                $date_from =  $this->input->post('date_from');  
                $date_to =  $this->input->post('date_to');
                $name =  $this->input->post('name');                  
                */
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
                                    $post = $this->payment->get_list_bank();
                                    if(!empty($post)) {
                                        
                                        foreach($post as $p) {
                                           
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

    public function payinvoice_post()
    {
        $code = $this->input->post('cod3');
        $data = array();
        if(isset($code)) {
            if($code == $this->common->keycode()) {
                $token =  $this->input->post('token');
                $id = $this->input->post("id");
                $bank = $this->input->post("bank");
                $notes = $this->input->post("notes");

                    $warning = '';
                    // pastikan username dan password adalah berupa huruf atau angka.
                    if(!empty($token)) {

                            $check = $this->user_model->get_user_by_token($token);
                            if($check->num_rows() > 0) {
                                $post = array();
                                $status = 1;
                                $msg = "success";
                                $user = $check->row();
                                
                                $checkpayment = $this->payment->checkpayment($user,$id);
                                if(!empty($bank)) {
                                    $gb = $this->payment->getbankid($bank);
                                
                                    if($checkpayment->num_rows() > 0) {
                                        //$b = $this->payment->updatepayment($user,$id,$gb,1,$notes);
                                        $b = array();
                                        $total = 0;
                                        $orderid = "";
                                        $invoice_name = "";
                                        $status_n = 0;
                                        $msg_n = "";
                                        foreach($checkpayment->result() as $a) {
                                            
                                            $b = $this->payment->updatepayment($user,$a->id,$gb,1,$notes,$a);

                                            if($b['status'] == 1) {
                                                $this->orders->update_status_order($a->order_id,6);
                                                $total += $b['total'];
                                                $orderid .= $b['order_id'].',';
                                                $invoice_name .= $b['invoice_name'].',';
                                            } else {
                                                $status_n = 1;
                                                $msg_n = $b['message'];
                                            }

                                        }
                                        $b['order_id'] = rtrim($orderid,',');
                                        $b['invoice_name'] = rtrim($invoice_name,',');
                                        $b['total'] = $total;
                                        $b['format_total'] = harga($total);
                                        /*
                                        $status = $b['status'];
                                        $msg = $b['message'];
                                        */
                                        if($status_n == 1) {
                                            $status = -1;
                                            $msg = $msg_n;
                                        } else {
                                            $status = 1;
                                            $msg = "success";
    
                                        }
    
                                        //$gb = $this->payment->getbankid($bank);
    
                                    } else {
                                        $status = -1;
                                        $msg = "Payment not found";
                                    }
                                } else {
                                    $status = -1;
                                    $msg = "Please fill bank";
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
                
                $message = $b;
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
    

}
