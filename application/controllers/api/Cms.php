<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Cms extends REST_Controller
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
/*
        $this->load->model('model_users', 'users');
        $this->load->model('model_joborder', 'joborder');
        $this->load->model('model_billing', 'billing');
        $this->load->model('model_wilayah', 'wilayah');
*/
        date_default_timezone_set('Asia/Jakarta');
    }

    public function login_post()
    {
        
        $email =  $this->input->post('email');
        $pass = $this->input->post('pass');
        $status = -1;
        $msg = "problem";

        // $msg = $id.' '.$output.' '.$rincian;
        if (!empty($email) && !empty($pass)) {
            $ip      = $_SERVER['REMOTE_ADDR'];
            //$cek = $this->users->cek_login($email,$pass);
            $login = $this->users->getUserByEmail($email);
            $r = $login->row();
            $row = $login->row_array();
            $userid = $r->id;
            $email = $r->email;

            if (!empty($r->id)) {
                $status = 1;
                $msg = "success";

                $phpass = new PasswordHash(12, false);
                if (!$phpass->CheckPassword($pass, $r->password)) {
                    //$this->login_protect($email);
                    //$this->template->error(lang("error_29"));
                    $status = -1;
                    $msg = "Wrong password in pass";
                    // $msg = lang("error_29");
                }
                $this->session->set_userdata(array(
                    'username' => $row['username'], 'id' => $row['id'], 'level' => $row['level'],
                    'role' => $row['role'], 'user_pt' => $row['user_pt'], 'main_user_id' => $row['main_user_id']
                ));
            } else {
                $status = -1;
                $msg = "Wrong in db";
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

    function print_cek_post()
    {

        $id = $this->input->post("id");

        $sql = "SELECT jo.*, w.nama 'nama_wilayah', g.nama 'nama_gudang', p.nama 'nama_pelanggan', p.alamat 'alamat_pelanggan', 
		p.kontak 'up', ps.nama 'nama_pemasok', pk.nama 'nama_kemasan' FROM joborder jo INNER JOIN wilayah w ON jo.idwilayah = w.id 
		INNER JOIN gudang g on jo.idgudang = g.id INNER JOIN tbvendor p on jo.idpelanggan = p.kodevendor INNER JOIN pemasok ps on 
		jo.idpemasok = ps.id INNER JOIN peti_kemas pk on jo.jenis_peti = pk.id WHERE jo.id = '$id'";

        $r = $this->db->query($sql)->row_array();

        $url = "";
        $status = -1;
        $msg = "error";
        $data = "";

        $type = 1;

        if (!empty($r['id'])) {
            $status = 1;
            $msg = "success";
            if ($r['angkutan'] == 'laut') {
                $type = 2;
            } else {
                $url = base_url("joborder/print_preview?id=$id");
            }

            $opt_kontainer = "";
            $identitas_20f = explode(",", $r['identitas_peti20f']);
            foreach ($identitas_20f as $f2) {
                $opt_kontainer .= "<option value='$f2'>$f2</option>";
            }

            $identitas_40f = explode(",", $r['identitas_peti40f']);
            foreach ($identitas_40f as $f4) {
                $opt_kontainer .= "<option value='$f4'>$f4</option>";
            }


            $data = '
            
            <div style="width:500px;">
            <div class="container">
                    <div class="row">
                        <div class="col-sm-12 mb-3">
                            <div class="form-group">
                            <label>Nomor Kontainer</label>
                                <select class="form-control" name="print_kontainer" id="print_kontainer">
                                    ' . $opt_kontainer . '
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <button class="btn btn-sm btn-primary form-control" type="button" onclick="print_kontainer();">Print</button>
                            </div>
                        </div>
                    </div>
            </div>
            </div>';
        } else {
            $status = -1;
            $msg = "Tidak ada ID yg dipilih";
        }

        $message = [
            'status' => $status,
            'message' => $msg,
            'type' => $type,
            'url' => $url,
            'data' => $data
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code

    }

    function print_kontainer_cek_post()
    {

        $id = $this->input->post("id");
        $kontainer = $this->input->post("kontainer");

        $sql = "SELECT jo.*, w.nama 'nama_wilayah', g.nama 'nama_gudang', p.nama 'nama_pelanggan', p.alamat 'alamat_pelanggan', 
		p.kontak 'up', ps.nama 'nama_pemasok', pk.nama 'nama_kemasan' FROM joborder jo INNER JOIN wilayah w ON jo.idwilayah = w.id 
		INNER JOIN gudang g on jo.idgudang = g.id INNER JOIN tbvendor p on jo.idpelanggan = p.kodevendor INNER JOIN pemasok ps on 
		jo.idpemasok = ps.id INNER JOIN peti_kemas pk on jo.jenis_peti = pk.id WHERE jo.id = '$id'";

        $r = $this->db->query($sql)->row_array();

        $url = "";
        $status = -1;
        $msg = "error";
        $data = "";

        $type = 1;

        if (!empty($r['id'])) {
            $status = 1;
            $msg = "success";

            $url = base_url("joborder/print_preview?id=$id&kontainer=$kontainer");
        } else {
            $status = -1;
            $msg = "Tidak ada ID yg dipilih";
        }

        $message = [
            'status' => $status,
            'message' => $msg,
            'type' => $type,
            'url' => $url,
            'data' => $data
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code

    }

    public function updatetagihan_post()
    {
        $status = -1;
        $msg = "error";

        $post = $this->input->post();
        $datadb = array();
        $id = $post['id'];
        $total = $post['total'];

        if (!empty($id)) {
            $status = 1;
            $msg = "success";

            $datadb = array('total_tagihan' => $total);
            $this->db->where('id', $id);

            $this->db->update("tbbilling", $datadb);
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }

        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code

    }

    public function hapusdetailtagihan_post()
    {
        $status = -1;
        $msg = "error";

        $post = $this->input->post();
        $datadb = array();
        $id = $post['id'];
        $idd = $post['idd'];

        if (!empty($id)) {
            $status = 1;
            $msg = "success";

           $this->db->where("id", $id);
           
           if($this->db->delete("tb_detail_billing") ) {
            $b = $this->db->query("SELECT SUM(jumlah) as total FROM tb_detail_billing WHERE id_billing = '$idd'")->row();
            
            $pst = array("total_tagihan"=>$b->total);
            $this->db->where("id",$idd);
            $this->db->update("tbbilling",$pst);
            
           } else {
               $status = -1;
               $msg = "Gagal Hapus";
           }

            

        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }

        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code

    }

    public function delete_post()
    {
        $status = -1;
        $msg = "error";

        $post = $this->input->post();
        $datadb = array();
        $user_id = $post['user_id'];
      
        if (!empty($user_id)) {

            $token = $this->session->token;
            $kry = postcurltoken($user_id,"",$token);

            $k = json_decode($kry);
            //var_dump($k);
            //die;
            if($k->status == "success") {
                $status = 1;
                $msg = "success";
            } else {
                $status = -1;
                $err = $kry;
                $msg = "Error $k->status";
            }
/*
            $res = explode('/', $id);

            if (!empty($res[1])) {
                $this->db->where("id", $res[1])->delete($res[0]);
            }
            */
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }

        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code

    }

    public function nonaktif_post()
    {
        $status = -1;
        $msg = "error";

        $post = $this->input->post();
        $datadb = array();
        $id = $post['id'];

        if (!empty($id)) {

            $token = $this->session->token;
            $kry = postcurltoken($id,"",$token);
               
            $k = json_decode($kry);

            if($k->status == "success") {
                $status = 1;
                $msg = "success";
            } else {
                $status = -1;
                $err = $kry;
                $msg = "Error $id";
            }

        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }

        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code

    }

    public function pinjam_approve_post()
    {
        $status = -1;
        $msg = "error";

        $id = $this->input->get("id");
        $datadb = array();
        $token = $this->session->token;
//        $id = $post['id'];

        if (!empty($id)) {
          
            $url = URL_API."admin/rent/approve";
            $pst = array("booking_id"=>$id);

            $response = poststatus($url,$pst,$token);
            
            $b = json_decode($response);
            if($b->status == 'success') {
                $status = 1;
                $msg = "success";
            } else {
                $status = -1;
                $msg = "Error $url $response";
            }
            
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }

        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code

    }

    
    public function pinjam_cancel_post()
    {
        $status = -1;
        $msg = "error";

        $post = $this->input->post();
        $id = $this->input->get("id");
        $datadb = array();
        $token = $this->session->token;
//        $id = $post['id'];

        if (!empty($id)) {
          
            $url = URL_API."admin/rent/cancel";
            $pst = array("booking_id"=>$id); 
            
            $response = poststatus($url,$pst,$token);
            
            
            $b = json_decode($response);
            if($b->status == 'success') {
                $status = 1;
                $msg = "success";
            } else {
                $status = -1;
                $msg = $b->messsage;
            }
            
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }

        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code

    }

    
    public function pinjam_return_post()
    {
        $status = -1;
        $msg = "error";

        $post = $this->input->post();
        $id = $this->input->get("id");
        $datadb = array();
        $token = $this->session->token;
//        $id = $post['id'];

        if (!empty($id)) {
          
            $url = URL_API."admin/rent/return";
            $pst = array("rent_id"=>$id);

            $response = poststatus($url,$pst,$token);

            $b = json_decode($response);
            if($b->status == 'success') {
                $status = 1;
                $msg = "success";
            } else {
                $status = -1;
                $msg = $b->messsage;
            }
            
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }

        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code

    }


    public function notif_post()
    {
        $status = -1;
        $msg = "error";

        $post = $this->input->post();
        $id = $this->input->get("id");
        $user_id = $this->input->get("user_id");
        $datadb = array();
        $token = $this->session->token;
//        $id = $post['id'];

        if (!empty($id)) {
          
            $url = URL_API."admin/send_message";
            $pst = array("rent_id"=>$id,"user_id"=>$user_id,"type"=>"rent_alert");

            $response = poststatus($url,$pst,$token);

            $b = json_decode($response);
            if($b->status == 'success') {
                $status = 1;
                $msg = "success";
            } else {
                $status = -1;
                $msg = $b->messsage;
            }
            
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }

        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code

    }


    public function delete_admin_post()
    {
        $status = -1;
        $msg = "error";

        $post = $this->input->post();
        $id = $this->input->get("id");
        $datadb = array();
        $token = $this->session->token;
//        $id = $post['id'];

        if (!empty($id)) {
          
            $url = URL_API."admin/delete";
            $pst = array("id"=>$id);

            $response = poststatus($url,$pst,$token);

            $b = json_decode($response);
            if($b->status == 'success') {
                $status = 1;
                $msg = "success";
            } else {
                $status = -1;
                $msg = $b->messsage;
            }
            
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }

        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code

    }
    
    public function approve_post()
    {
        $status = -1;
        $msg = "error";

        $post = $this->input->post();
        $id = $this->input->get("id");
        $type = $this->input->get("type");
        $datadb = array();
        $token = $this->session->token;
//        $id = $post['id'];

        if (!empty($id)) {

            if($type == 'hamba') {
                $wh = "&type=hamba";
            } else {
                $wh = "";
            }
          
            $url = URL_API."admin/member/approve?id=$id$wh";
            $pst = array();

            $response = poststatus($url,$pst,$token);
            
            $b = json_decode($response);
            if($b->status == 'success') {
                $status = 1;
                $msg = "success";
            } else {
                $status = -1;
                $msg = $b->messsage;
            }
            
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }

        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code

    }

    
    public function reject_post()
    {
        $status = -1;
        $msg = "error";

        $post = $this->input->post();
        $id = $this->input->get("id");
        $datadb = array();
        $token = $this->session->token;
//        $id = $post['id'];

        if (!empty($id)) {
          
            $url = URL_API."admin/member/reject?id=$id";
            $pst = array();

            $response = postcurltoken($url,$pst,$token);
            
            $b = json_decode($response);
            if($b->status == 'success') {
                $status = 1;
                $msg = "success";
            } else {
                $status = -1;
                $msg = $b->messsage;
            }
            
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }

        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code

    }

    public function approve_extend_post()
    {
        $status = -1;
        $msg = "error";

        $post = $this->input->post();
        $id = $this->input->get("id");
        $datadb = array();
        $token = $this->session->token;
//        $id = $post['id'];

        if (!empty($id)) {
          
            $url = URL_API."admin/member/approve_extend_member";
            $pst = array("id"=>$id); 
            
            $response = poststatus($url,$pst,$token);
            
            
            $b = json_decode($response);
            if($b->status == 'success') {
                $status = 1;
                $msg = "success";
            } else {
                $status = -1;
                $msg = $b->messsage;
            }
            
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }

        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code

    }

    public function reject_extend_post()
    {
        $status = -1;
        $msg = "error";

        $post = $this->input->post();
        $id = $this->input->get("id");
        $datadb = array();
        $token = $this->session->token;
//        $id = $post['id'];

        if (!empty($id)) {
          
            $url = URL_API."admin/member/reject_extend_member";
            $pst = array("id"=>$id); 
            
            $response = poststatus($url,$pst,$token);
            
            
            $b = json_decode($response);
            if($b->status == 'success') {
                $status = 1;
                $msg = "success";
            } else {
                $status = -1;
                $msg = $b->messsage;
            }
            
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }

        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code

    }


    public function deletemasterproduct_post()
    {
        $status = -1;
        $msg = "error";

        $post = $this->input->post();
        $datadb = array();
        $id = $post['id'];

        if (!empty($id)) {
            $status = 1;
            $msg = "success";
            $datadb = array('status_deleted' => 1);
            $this->db->where('id', $id);

            $this->db->update("product_master", $datadb);
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }

        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code

    }

    public function deletemasterproductvarian_post()
    {
        $status = -1;
        $msg = "error";

        $post = $this->input->post();
        $datadb = array();
        $id = $post['id'];

        if (!empty($id)) {
            $status = 1;
            $msg = "success";
            $datadb = array('status_deleted' => 1);
            $this->db->where('id', $id);

            $this->db->update("product_master_varian", $datadb);
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }

        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code

    }

    public function delete_promo_banner_post()
    {
        $status = -1;
        $msg = "error";

        $post = $this->input->post();
        $datadb = array();
        $id = $post['id'];

        if (!empty($id)) {
            $status = 1;
            $msg = "success";

            $foto = $this->db->get_where('promo_banner', array('id' => $id));


            if ($foto->num_rows() > 0) {
                $hasil = $foto->row();

                $nama_foto = $hasil->image;


                if (file_exists($file = FCPATH . 'uploads/banner/' . $nama_foto)) {
                    unlink($file);
                    /*
                    $file2=FCPATH.'uploads/products/thumbs/'.$nama_foto;
                    unlink($file2);                    
                    */
                }
                $this->db->delete('promo_banner', array('id' => $id));
            }
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }

        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code

    }

    public function delete_special_promo_post()
    {
        $status = -1;
        $msg = "error";

        $post = $this->input->post();
        $datadb = array();
        $id = $post['id'];

        if (!empty($id)) {
            $status = 1;
            $msg = "success";

            $foto = $this->db->get_where('special_promo', array('id' => $id));


            if ($foto->num_rows() > 0) {
                $hasil = $foto->row();

                $nama_foto = $hasil->image;


                if (file_exists($file = FCPATH . 'uploads/banner/' . $nama_foto)) {
                    unlink($file);
                    /*
                    $file2=FCPATH.'uploads/products/thumbs/'.$nama_foto;
                    unlink($file2);                    
                    */
                }
                $this->db->delete('special_promo', array('id' => $id));
            }
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }

        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code

    }

    public function delete_product_images_post()
    {
        $status = -1;
        $msg = "error";

        $post = $this->input->post();
        $datadb = array();
        $id = $post['id'];

        if (!empty($id)) {
            $status = 1;
            $msg = "success";

            $foto = $this->db->get_where('product_master_image', array('id' => $id));


            if ($foto->num_rows() > 0) {
                $hasil = $foto->row();

                $nama_foto = $hasil->image;


                if (file_exists($file = FCPATH . 'uploads/products/photos/' . $nama_foto)) {
                    unlink($file);
                    $file2 = FCPATH . 'uploads/products/thumbs/' . $nama_foto;
                    unlink($file2);
                }
                $this->db->delete('product_master_image', array('id' => $id));
            }
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }

        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code

    }

    public function change_status_post()
    {
        $status = -1;
        $msg = "error";

        $post = $this->input->post();
        $datadb = array();
        $id = $post['id'];
        $cabang = $post['cabang'];

        if (!empty($id)) {
            $q = $this->product_branch->change_status($id, $cabang);

            $status = $q['status'];
            $msg = $q['message'];
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }

        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code

    }

    public function change_status_varian_post()
    {
        $status = -1;
        $msg = "error";
        $stt = 0;

        $post = $this->input->post();
        $datadb = array();
        $id = $post['id'];
        $id_varian = $post['id_varian'];
        $cabang = $post['cabang'];
        $price = $post['price'];

        if (!empty($id)) {

            $q = $this->product_branch->change_status_varian($id, $id_varian, $cabang, $price);

            $status = $q['status'];
            $msg = $q['message'];
            /*
            $status = 1;
            $msg = "adada";
            */
            $stt = $q['stt'];
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }

        $message = [
            'status' => $status,
            'message' => $msg,
            'stt' => $stt
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code

    }

    public function update_price_post()
    {
        $status = -1;
        $msg = "error";

        $post = $this->input->post();
        $datadb = array();
        $id = $post['id'];
        $id_varian = $post['id_varian'];
        $cabang = $post['cabang'];
        $price = $post['price'];

        if (!empty($id)) {

            $q = $this->product_branch->update_price($id, $id_varian, $cabang, $price);

            $status = $q['status'];
            $msg = $q['message'];
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }

        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code

    }

    public function update_qty_sj_post()
    {
        $status = -1;
        $msg = "error";

        $post = $this->input->post();
        $datadb = array();
        $order_id = $post['order_id'];
        $sj_detail_id = $post['sj_detail_id'];
        $detail_id = $post['detail_id'];
        $sj_detail_item_id = $post['sj_detail_item_id'];
        $qty = $post['qty'];

        if (!empty($order_id)) {

            $q = $this->orders->update_qty_sj($order_id, $sj_detail_id, $detail_id, $sj_detail_item_id, $qty);

            $status = $q['status'];
            $msg = $q['message'];
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }

        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code

    }

    public function update_status_sj_post()
    {
        $status = -1;
        $msg = "error";

        $post = $this->input->post();
        $datadb = array();
        $order_id = $post['order_id'];
        $sj_id = $post['sj_id'];
        $status_order = $post['status_order'];

        if (empty($status_order)) {
            $status_order = 6;
        }

        if (!empty($order_id)) {

            $q = $this->orders->update_status_sj($order_id, $sj_id, $status_order);

            $status = $q['status'];
            $msg = $q['message'];
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }

        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code

    }


    public function update_disc_post()
    {
        $status = -1;
        $msg = "error";

        $post = $this->input->post();
        $datadb = array();
        $id = $post['id'];
        $id_varian = $post['id_varian'];
        $cabang = $post['cabang'];
        $price = $post['price'];

        if (!empty($id)) {

            $q = $this->product_branch->update_disc($id, $id_varian, $cabang, $price);

            $status = $q['status'];
            $msg = $q['message'];
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }

        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code

    }

    public function get_jo_no_post()
    {
        $pabean = $this->input->post("pabean");
        $angkutan = $this->input->post("angkutan");
        $tanggal = $this->input->post("tanggal");

        if (!empty($tanggal)) {
            $tanggal = tgl_simpan($tanggal);
        }

        $status = -1;
        $msg = "error get job order number";
        $data = "";
        $seq = "";
        $opt_wilayah = "";
        if (!empty($pabean) && !empty($angkutan)) {
            $data = get_jo_number($pabean, $angkutan, $tanggal);
            if (!empty($data['jo'])) {
                $status = 1;
                $msg = "success";
                $jo = $data['jo'];
                $seq = $data['sequence_order'];

                $opt5 = $this->wilayah->wilayah_all($angkutan);
                $opt_wilayah = option_builder($opt5, "", 0);
            }
        } else {
            $status = 0;
            $msg = "";
        }

        $message = [
            'status' => $status,
            'message' => $msg,
            'jo' => $jo,
            'sequence_order' => $seq,
            'wilayah' => $opt_wilayah
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
    }

    public function get_billing_no_post()
    {
        $jenis = $this->input->post("jenis");
        $tanggal = $this->input->post("tanggal");

        $status = -1;
        $msg = "error get billing number";
        $data = "";
        $seq = "";
        $fp = 0;
        $generate = 0;
        if (!empty($jenis)) {
            $data = get_billing_number($jenis, $tanggal);
            if (!empty($data['billing_no'])) {
                $status = 1;
                $msg = "success";
                $billing_no = $data['billing_no'];
                $seq = $data['sequence_order'];
                $generate = $data['generate'];
                $fp = $data['fp'];
            }
        } else {
            $status = 0;
            $msg = "";
        }

        $message = [
            'status' => $status,
            'message' => $msg,
            'billing_no' => $billing_no,
            'sequence_order' => $seq,
            'generate' => $generate,
            'fp' => $fp
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
    }

    public function crud_global_post()
    {
        $status = -1;
        $msg = "error";

		$token = $this->session->token;

        $post = $this->input->post();
        $datadb = array();
        $tbl = $post['tbl'];
        $id = $post['id'];
        $act = $post['act'];

        unset($post['act']);
        unset($post['id']);
        unset($post['tbl']);

        if (!empty($act)) {
            $status = 1;
            $msg = "success";

            if ($act == 'save') {

                /*
                foreach ($post as $p => $k) {
                    if (strpos($p, 'tgl_') !== false) {
                        //echo $p.' '.tgl_simpan($k);
                        $this->db->set($p, tgl_simpan($k), TRUE);
                        unset($post[$p]);
                    }
                }
                */

              // $aa = json_encode($post);
               $kry = postcurltoken($tbl,$post,$token);
               
                $k = json_decode($kry);

                if($k->status == "success") {
                    $status = 1;
                    $msg = "success";
                } else {
                    $status = -1;
                    $err = $kry;
                    $msg = "Error";
                }

            } else if ($act == 'update') {
                $post['id'] = $id;
                $kry = postcurltoken($tbl,$post,$token);
               
                $k = json_decode($kry);

                if($k->status == "success") {
                    $status = 1;
                    $msg = "success";
                } else {
                    $status = -1;
                    $err = $kry;
                    $msg = "Error";
                }
                
            }
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }


        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code


    }

    
    public function crud_book_post()
    {
        $status = -1;
        $msg = "error";

		$token = $this->session->token;

        $post = $this->input->post();
        $datadb = array();
        $tbl = $post['tbl'];
        $id = $post['id'];
        $act = $post['act'];

        unset($post['act']);
        unset($post['id']);
        unset($post['tbl']);

        if (!empty($act)) {
            $status = 1;
            $msg = "success";
            $tempFile  = "hah";
            if(!empty($_FILES)) {
                $tempFile = $_FILES['d']['tmp_name'];

                unset($post['image']);


            }

            if ($act == 'save') {

                /*
                foreach ($post as $p => $k) {
                    if (strpos($p, 'tgl_') !== false) {
                        //echo $p.' '.tgl_simpan($k);
                        $this->db->set($p, tgl_simpan($k), TRUE);
                        unset($post[$p]);
                    }
                }
                */

              // $aa = json_encode($post);
               $kry = postcurltokenimage($tbl,$post,$token,$tempFile);
               
                $k = json_decode($kry);

                if($k->status == "success") {
                    $status = 1;
                    $msg = "success";
                } else {
                    $status = -1;
                    $err = $kry;
                    $msg = "Error";
                }

            } else if ($act == 'update') {
                $kry = postcurltokenimage($tbl,$post,$token,$tempFile);
               
                $k = json_decode($kry);

                if($k->status == "success") {
                    $status = 1;
                    $msg = "success";
                } else {
                    $status = -1;
                    $err = $kry;
                    //$msg = $tbl;
                    $msg = json_encode($kry);
                }
                
            }
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }


        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code


    }

    public function crud_fakturpajak_post()
    {
        $status = -1;
        $msg = "error";
        $msg_error = "waduh";
        $post = $this->input->post();
        $tbl = $post['tbl'];
        $stat = 0;
        $act = $post['act'];

        unset($post['act']);

        $str1 = $post['dr'];
        $str2 = $post['smp'];

        // $ar1 = explode(".", $str1);
        // $ar2 = explode(".", $str2);

        // $var1 = $ar1[1];
        // $var2 = $ar2[1];

        // $concat = $ar1[0];

        $data = array();
        for ($i = $str1; $i <= $str2; $i++) {
            array_push($data, array(
                'kode' => $i //$concat
            ));
        }
        if ($stat == 0) {
            if ($this->db->insert_batch('faktur_pajak', $data)) {
                $status = 1;
                $msg = "success";
            } else {
                $db_error = $this->db->error();

                $status = -1;
                $msg = $db_error['message'];
            }
        } else {
            $status = -1;
            $msg = $msg_error;
        }


        $message = [
            'status' => $status,
            'message' => $msg,
        ];
        $this->set_response($message, REST_Controller::HTTP_OK);
    }

    public function crud_joborder_post()
    {
        $status = -1;
        $msg = "error";
        $msg_error = "waduh";

        $stat = 0;

        $post = $this->input->post();
        $datadb = array();
        $tbl = $post['tbl'];
        $id = $post['id'];
        $act = $post['act'];

        unset($post['act']);
        unset($post['id']);
        unset($post['tbl']);

        if (!empty($act)) {
            /*
            $status = 1;
            $msg = "success";
*/
            if ($act == 'save') {

                if ($post['no']) {
                    $ada = $this->joborder->check_joborder($post['no']);
                    if (!empty($ada)) {
                        $pabean = $post["pabean"];
                        $angkutan = $post["angkutan"];
                        $nama_lengkap = $post["iduser"];

                        $status = -1;
                        $msg_error = "error get job order number";
                        $data = "";
                        $seq = "";
                        $opt_wilayah = "";
                        if (!empty($pabean) && !empty($angkutan)) {
                            $data = get_jo_number($pabean, $angkutan);
                            if (!empty($data['jo'])) {
                                $status = 1;
                                $msg = "success";
                                $jo = $data['jo'];
                                $seq = $data['sequence_order'];
                                $post['no'] = $jo;
                                $post['sequence_order'] = $seq;
                            }
                        } else {
                            $status = -1;
                            $msg = "Tidak ada nomor joborder";
                        }
                    }
                }

                if (!empty($post['user'])) {
                    $user = $post['user'];
                    $this->db->set('user_created', $user, TRUE);
                    $this->db->set('user_updated', $user, TRUE);

                    $this->db->set('dt_created', 'NOW()', FALSE);
                    $this->db->set('dt_updated', 'NOW()', FALSE);
                    unset($post['user']);
                }

                $identitas_peti_20f = "";
                $identitas_peti_40f = "";
                $post['peti_20f'] = "";
                if (!empty($post['20f'])) {
                    $k = 0;
                    foreach ($post['20f'] as $f2) {

                        if (!empty($f2)) {
                            $k++;
                            $identitas_peti_20f .= $f2 . ',';
                        }
                    }
                    $post['peti_20f'] = $k;
                }

                $post['peti_40f'] = "";
                if (!empty($post['40f'])) {
                    $k2 = 0;
                    foreach ($post['40f'] as $f4) {

                        if (!empty($f4)) {
                            $k2++;
                            $identitas_peti_40f .=  $f4 . ',';
                        }
                    }
                    $post['peti_40f'] = $k2;
                }

                unset($post['20f']);
                unset($post['40f']);

                if (!empty($identitas_peti_20f)) {
                    $identitas_peti_20f = rtrim($identitas_peti_20f, ",");
                }
                if (!empty($identitas_peti_40f)) {
                    $identitas_peti_40f = rtrim($identitas_peti_40f, ",");
                }
                $post['identitas_peti20f'] = $identitas_peti_20f;
                $post['identitas_peti40f'] = $identitas_peti_40f;


                // $nama = $this->session->nama_lengkap;
                // $nama = $post["iduser"];
                // $this -> db -> insert ($nama);

                $jenis_barang = $post["jenis_barang"];
                $this->joborder->checkjenisbarang($jenis_barang);

                $marks = array(
                    "gross_wg", "chargable", "cif", "kurs", "jumlah_biaya", "bm", "cukai", "ppn", "ppn_bm", "pph",
                    "jumlah_pungutan"
                );
                foreach ($post as $p => $k) {

                    if (strpos($p, 'tgl_') !== false) {
                        //echo $p.' '.tgl_simpan($k);
                        $this->db->set($p, tgl_simpan($k), TRUE);
                        unset($post[$p]);
                    }
                    if ($p == 'tanggal' || $p == 'notgldaftar') {
                        $this->db->set($p, tgl_simpan($k), TRUE);
                        unset($post[$p]);
                    }

                    if (in_array($p, $marks)) {
                        $this->db->set($p, harga_remove_rp($k), TRUE);
                        if (empty($k) && $p == 'jumlah_biaya') {
                            $stat += 1;
                            $msg_error = $p . ' belum diisi';
                        }
                        unset($post[$p]);
                    }
                }

                if ($stat == 0) {
                    if ($this->db->insert($tbl, $post)) {
                        $status = 1;
                        $msg = "success";
                        $id = $this->db->insert_id();
                    } else {
                        $db_error = $this->db->error();

                        $status = -1;
                        $msg = $db_error['message'];
                    }
                } else {
                    $status = -1;
                    $msg = $msg_error;
                }
            } else if ($act == 'update') {
                if (!empty($post['user'])) {
                    $user = $post['user'];
                    $this->db->set('user_updated', $user, TRUE);

                    $this->db->set('dt_updated', 'NOW()', FALSE);
                    unset($post['user']);
                }

                $nama_lengkap = $post["iduser"];

                $identitas_peti_20f = "";
                $identitas_peti_40f = "";
                $post['peti_20f'] = "";
                if (!empty($post['20f'])) {
                    $k = 0;
                    foreach ($post['20f'] as $f2) {

                        if (!empty($f2)) {
                            $k++;
                            $identitas_peti_20f .= $f2 . ',';
                        }
                    }
                    $post['peti_20f'] = $k;
                }

                $post['peti_40f'] = "";
                if (!empty($post['40f'])) {
                    $k2 = 0;
                    foreach ($post['40f'] as $f4) {

                        if (!empty($f4)) {
                            $k2++;
                            $identitas_peti_40f .=  $f4 . ',';
                        }
                    }
                    $post['peti_40f'] = $k2;
                }

                unset($post['20f']);
                unset($post['40f']);

                if (!empty($identitas_peti_20f)) {
                    $identitas_peti_20f = rtrim($identitas_peti_20f, ",");
                }
                if (!empty($identitas_peti_40f)) {
                    $identitas_peti_40f = rtrim($identitas_peti_40f, ",");
                }
                $post['identitas_peti20f'] = $identitas_peti_20f;
                $post['identitas_peti40f'] = $identitas_peti_40f;



                $jenis_barang = $post["jenis_barang"];
                $this->joborder->checkjenisbarang($jenis_barang);



                $marks = array(
                    "gross_wg", "chargable", "cif", "kurs", "jumlah_biaya", "bm", "cukai", "ppn", "ppn_bm", "pph",
                    "jumlah_pungutan"
                );
                foreach ($post as $p => $k) {
                    if (strpos($p, 'tgl_') !== false) {
                        //echo $p.' '.tgl_simpan($k);
                        $this->db->set($p, tgl_simpan($k), TRUE);
                        unset($post[$p]);
                    }
                    if ($p == 'tanggal' || $p == 'notgldaftar') {
                        $this->db->set($p, tgl_simpan($k), TRUE);
                        unset($post[$p]);
                    }

                    if (in_array($p, $marks)) {
                        $this->db->set($p, harga_remove_rp($k), TRUE);
                        unset($post[$p]);
                    }
                }


                $this->db->where('id', $id);
                if ($this->db->update($tbl, $post)) {
                    $status = 1;
                    $msg = "success";
                } else {
                    $db_error = $this->db->error();

                    $status = -1;
                    $msg = $db_error['message'];
                }
            }
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }


        $message = [
            'status' => $status,
            'message' => $msg,
            'id' => $id
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code


    }

    public function crud_billing_post()
    {
        $status = -1;
        $msg = "error";

        $post = $this->input->post();
        $datadb = array();
        $tbl = $post['tbl'];
        $id = $post['id'];
        $act = $post['act'];

        $jobordername = $post['no_joborder'];
        $id_joborder = $post['id_joborder'];

        unset($post['act']);
        unset($post['id']);
        unset($post['tbl']);

        if (!empty($act)) {
            $status = 1;
            $msg = "success";

            if ($act == 'save') {

                $stat = 0;
                $msg_error = "";
                $jenis = $post["jenis"];
                if ($post['no']) {
                    $ada = $this->billing->check_billing($post['no']);
                    if (!empty($ada)) {
                        $nama_lengkap = $post["iduser"];

                        $status = -1;
                        $msg_error = "error get billing number";
                        $data = "";
                        $seq = "";
                        if (!empty($jenis)) {
                            $tgl = $post['tanggal'];
                            $data = get_billing_number($jenis, $tgl);
                            if (!empty($data['billing_no'])) {
                                $status = 1;
                                $msg = "success";
                                $billing_no = $data['billing_no'];
                                $seq = $data['sequence_order'];
                                $post['no'] = $billing_no;
                                $post['sequence_order'] = $seq;
                            }
                        } else {
                            $status = 0;
                            $msg = "";
                        }
                    }
                }

                if (!empty($post['user'])) {
                    $user = $post['user'];
                    $this->db->set('user_created', $user, TRUE);
                    $this->db->set('user_updated', $user, TRUE);

                    $this->db->set('dt_created', 'NOW()', FALSE);
                    $this->db->set('dt_updated', 'NOW()', FALSE);
                    unset($post['user']);
                }



                // $msg_error =  $post["jenis"].' Mohon '.$post['no_fp'];

                /*
                $kode = $post['kode'];
                $jumlah = $post['jumlah'];
                $jenis_tagihan = $post['jenis_tagihan'];
                */
                $no = $post['no'];
                $no_array    = 0;
                $datadetail = array();
                foreach ($post['kode'] as $k2) {
                    if (!empty($k2)) {
                        $kode     = $post['kode'][$no_array];
                        $jumlah     = harga_remove_rp($post['jumlah'][$no_array]);
                        $jenis_tagihan     = $post['jenis_tagihan'][$no_array];

                        $datadetail[] = array("no_billing" => $no, "kd" => $kode, "jenis_tagihan" => $jenis_tagihan, "jumlah" => $jumlah);
                    }

                    $no_array++;
                }
                if ($post['tagihan']) {
                    if (!empty($datadetail)) {
                        $bb = 0;
                        $cc = 0;
                        $dd = 0;
                        $ee = 0;
                        foreach ($datadetail as $d3) {
                            if ($d3['kd'] == '211201001') {
                                $bb++;
                            }

                            if ($d3['kd'] == '411101001') {
                                $cc++;
                            }

                            if ($d3['kd'] == '411101002') {
                                $dd++;
                            }

                            if ($d3['kd'] == '611101042') {
                                $ee++;
                            }
                        }
                        if (empty($bb)) {
                            $stat = -1;
                            $msg_error = "Mohon Generate PPN";
                        }

                        if (!empty($cc)) {
                            $stat = -1;
                            $msg_error = "Mohon tidak isi Jasa Inklaring di detail tagihan";
                        }

                        if (!empty($dd)) {
                            $stat = -1;
                            $msg_error = "Mohon tidak isi Jasa Angkutan di detail tagihan";
                        }

                        if (!empty($ee)) {
                            $stat = -1;
                            $msg_error = "Mohon tidak isi PPN di detail tagihan";
                        }
                    } else {
                        $stat = -1;
                        $msg_error = "Mohon isi detail billing";
                    }
                }
                
                $no_fp2 = $post['no_fp2'];
                $no_fp1 = $post['no_fp'];
                if ($post["jenis"] == 'inklaring') {
                    if (empty($no_fp2)) {
                        $stat = -1;
                        $msg_error = "Mohon isi Nomor Faktur Pajak";
                    } else {
                        //$no_fp1 = $post['no_fp'];
                        $no_fp = $post['no_fp'] . '.' . $post['no_fp2'];
                        $this->db->set('no_fp', $no_fp, TRUE);
                    }
                    $check_jo = $this->db->query("SELECT * FROM tbbilling WHERE id_joborder = '$id_joborder' AND jenis='$jenis'")->row();
                    if ($check_jo->id) {
                        $stat = -1;
                        $msg_error = "Nomor Job Order sudah dipakai";
                    }
                }

                if ($post["jenis"] == 'jasangkut') {
                    if (empty($no_fp2)) {
                        $stat = -1;
                        $msg_error = "Mohon isi Nomor Faktur Pajak";
                    } else {
                        //$no_fp1 = $post['no_fp'];
                        $no_fp = $post['no_fp'] . '.' . $post['no_fp2'];
                        $this->db->set('no_fp', $no_fp, TRUE);
                    }
                    $check_jo2 = $this->db->query("SELECT * FROM tbbilling WHERE id_joborder = '$id_joborder' AND jenis='$jenis'")->row();
                    if ($check_jo2->id) {
                        $stat = -1;
                        $msg_error = "Nomor Job Order sudah dipakai";
                    }
                }

                unset($post['no_fp']);
                unset($post['no_fp2']);

                unset($post['kode']);
                unset($post['jumlah']);
                unset($post['jenis_tagihan']);
                unset($post['id_billing']);


                $tanggal = $post['tanggal'];
                $id_joborder = $post['id_joborder'];
                $marks = array("total_tagihan");
                $total_tagihan = harga_remove_rp($post['total_tagihan']);
                foreach ($post as $p => $k) {
                    if (strpos($p, 'tgl_') !== false) {
                        //echo $p.' '.tgl_simpan($k);
                        $this->db->set($p, tgl_simpan($k), TRUE);
                        unset($post[$p]);
                    }
                    if ($p == 'tanggal') {
                        $this->db->set($p, tgl_simpan($k), TRUE);
                        unset($post[$p]);
                    }

                    if (in_array($p, $marks)) {
                        $this->db->set($p, harga_remove_rp($k), TRUE);
                        unset($post[$p]);
                    }
                }


                if ($stat == 0) {
                    if ($this->db->insert($tbl, $post)) {
                        $status = 1;
                        $msg = "success";

                        $id = $this->db->insert_id();
                        foreach ($datadetail as $d) {
                            $datadetail2 = array("id_billing" => $id, "no_billing" => $d['no_billing'], "kd" => $d['kd'], "jenis_tagihan" => $d['jenis_tagihan'], "jumlah" => $d['jumlah']);
                            $this->db->insert("tb_detail_billing", $datadetail2);
                        }
                        if ($jenis == 'inklaring') {

                            $this->billing->update_fp($no_fp1, $no_fp2, $no, $tanggal);
                            $this->billing->insert_jurnal($no, $tanggal, $user, $total_tagihan, $id_joborder);
                        }
                        if ($jenis == 'jasangkut') {

                            $this->billing->update_fp($no_fp1, $no_fp2, $no, $tanggal);
                            $this->billing->insert_jurnal($no, $tanggal, $user, $total_tagihan, $id_joborder);
                        }
                    } else {
                        $db_error = $this->db->error();

                        $status = -1;
                        $msg = $db_error['message'];
                    }
                } else {
                    $status = -1;
                    $msg = $msg_error;
                }
            } else if ($act == 'update') {
                $stat = 0;
                $msg_error = "";

                if (!empty($post['user'])) {
                    $user = $post['user'];
                    $this->db->set('user_updated', $user, TRUE);

                    $this->db->set('dt_updated', 'NOW()', FALSE);
                    unset($post['user']);
                }

                $nama_lengkap = $post["iduser"];

                $no = $post['no'];
                $no_array    = 0;

                $no_fp2 = $post['no_fp2'];
                $no_fp1 = $post['no_fp'];

                if ($post["jenis"] == 'inklaring') {
                    if (empty($no_fp2)) {
                        $stat = -1;
                        $msg_error = "Mohon isi Nomor Faktur Pajak";
                    } else {
                        //$no_fp1 = $post['no_fp'];
                        $no_fp = $post['no_fp'] . '.' . $post['no_fp2'];
                        $this->db->set('no_fp', $no_fp, TRUE);
                    }
                }

                if ($post["jenis"] == 'jasangkut') {
                    if (empty($no_fp2)) {
                        $stat = -1;
                        $msg_error = "Mohon isi Nomor Faktur Pajak";
                    } else {
                        //$no_fp1 = $post['no_fp'];
                        $no_fp = $post['no_fp'] . '.' . $post['no_fp2'];
                        $this->db->set('no_fp', $no_fp, TRUE);
                    }
                }

                unset($post['no_fp']);
                unset($post['no_fp2']);
                
                //unset($post['jumlah']);
                //unset($post['jenis_tagihan']);
                


                $datadetail = array();
                foreach ($post['kode'] as $k2) {
                    if (!empty($k2)) {
                        $kode     = $post['kode'][$no_array];
                        $jumlah     = harga_remove_rp($post['jumlah'][$no_array]);
                        $jenis_tagihan     = $post['jenis_tagihan'][$no_array];
                        $id_detail     = $post['id_detail'][$no_array];

                        // $adb = $this->billing->checkdetailbilling($id_detail);
                        /* 
                        if(!empty($id_detail)) {
                            $datadetail = array("jumlah"=>$jumlah,"kd"=>$kode,"jenis_tagihan"=>$jenis_tagihan);
                            $this->db->where('id',$id_detail);
                            $this->db->update("tb_detail_billing",$datadetail);
                        } else {
                            $datadetail = array("id_billing"=>$id,"no_billing"=>$no,"kd"=>$kode,"jenis_tagihan"=>$jenis_tagihan,"jumlah"=>$jumlah);
                            $this->db->insert("tb_detail_billing",$datadetail);
                        }
                        */
                        $datadetail[] = array("id_billing" => $id, "no_billing" => $no, "kd" => $kode, "jenis_tagihan" => $jenis_tagihan, "jumlah" => $jumlah, "id_detail" => $id_detail);
                    }

                    $no_array++;
                }

                unset($post['kode']);
                unset($post['jumlah']);
                unset($post['jenis_tagihan']);
                unset($post['id_detail']);
                unset($post['id_billing']);

                $marks = array("total_tagihan");
                foreach ($post as $p => $k) {
                    if (strpos($p, 'tgl_') !== false) {
                        //echo $p.' '.tgl_simpan($k);
                        $this->db->set($p, tgl_simpan($k), TRUE);
                        unset($post[$p]);
                    }
                    if ($p == 'tanggal') {
                        $this->db->set($p, tgl_simpan($k), TRUE);
                        unset($post[$p]);
                    }

                    if (in_array($p, $marks)) {
                        $this->db->set($p, harga_remove_rp($k), TRUE);
                        unset($post[$p]);
                    }
                }

                if ($stat == 0) {
                    $this->db->where('id', $id);
                    if ($this->db->update($tbl, $post)) {
                        $status = 1;
                        $msg = "success";

                        foreach ($datadetail as $d) {

                            if (!empty($d['id_detail'])) {
                                $datadetail2 = array("kd" => $d['kd'], "jenis_tagihan" => $d['jenis_tagihan'], "jumlah" => $d['jumlah']);
                                $this->db->where('id', $d['id_detail']);
                                $this->db->update("tb_detail_billing", $datadetail2);
                            } else {
                                $datadetail2 = array(
                                    "id_billing" => $id, "no_billing" => $d['no_billing'],
                                    "kd" => $d['kd'], "jenis_tagihan" => $d['jenis_tagihan'], "jumlah" => $d['jumlah']
                                );
                                $this->db->insert("tb_detail_billing", $datadetail2);
                            }
                        }
                    } else {
                        $db_error = $this->db->error();

                        $status = -1;
                        $msg = $db_error['message'];
                    }
                } else {
                    $status = -1;
                    $msg = $msg_error;
                }
            }
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }


        $message = [
            'status' => $status,
            'message' => $msg,
            'id' => $id,
            'id_joborder' => $id_joborder,
            'joborder' => $jobordername
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code


    }

    public function crud_admin_post()
    {
        $status = -1;
        $msg = "error";

        $post = $this->input->post();
        $datadb = array();
        $tbl = $post['tbl'];
        $id = $post['id'];
        $act = $post['act'];

        unset($post['act']);
        unset($post['id']);
        unset($post['tbl']);

        if (!empty($act)) {
            $status = 1;
            $msg = "success";

            if ($act == 'save') {

                if (!empty($post['user'])) {
                    $user = $post['user'];
                    $this->db->set('user_created', $user, TRUE);
                    $this->db->set('user_updated', $user, TRUE);

                    $this->db->set('dt_created', 'NOW()', FALSE);
                    $this->db->set('dt_updated', 'NOW()', FALSE);
                    unset($post['user']);
                }

                if (!empty($post['password'])) {
                    if ($post['password'] == $post['confirm_password']) {
                        $pwd = $post['password'];
                        $pass = $this->common->encrypt($pwd);

                        // $this->db->set('show_password', $data['pass'], TRUE);

                        $this->db->set('password', $pass, TRUE);

                        unset($post['password']);
                        unset($post['confirm_password']);
                    } else {
                        $status = -1;
                        $msg = "Password doesnt match";
                    }
                }
                $err = "";
                if (!empty($post['email'])) {
                    $status = $this->admin->check_email($post['email']);
                    $err = "Email Already Exist";
                }

                if (!empty($post['username']) && $status == 1) {
                    $status = $this->admin->check_username($post['username']);
                    $err = "Username Already Exist";
                }

                if ($status == 1) {
                    foreach ($post as $p => $k) {
                        if (strpos($p, 'tgl_') !== false) {
                            //echo $p.' '.tgl_simpan($k);
                            $this->db->set($p, tgl_simpan($k), TRUE);
                            unset($post[$p]);
                        }
                    }

                    if ($this->db->insert($tbl, $post)) {
                        $status = 1;
                        $msg = "success";
                    } else {
                        $db_error = $this->db->error();

                        $status = -1;
                        $msg = $db_error['message'];
                    }
                } else {
                    $status = -1;
                    $msg = $err;
                }
            } else if ($act == 'update') {
                if (!empty($post['user'])) {
                    $user = $post['user'];
                    $this->db->set('user_updated', $user, TRUE);

                    $this->db->set('dt_updated', 'NOW()', FALSE);
                    unset($post['user']);
                }

                if (!empty($post['password'])) {
                    if ($post['password'] == $post['confirm_password']) {
                        $pwd = $post['password'];
                        $pass = $this->common->encrypt($pwd);

                        $this->db->set('password', $pass, TRUE);
                    } else {
                        $status = -1;
                        $msg = "Password doesnt match";
                    }
                }

                unset($post['password']);
                unset($post['confirm_password']);

                foreach ($post as $p => $k) {
                    if (strpos($p, 'tgl_') !== false) {
                        //echo $p.' '.tgl_simpan($k);
                        $this->db->set($p, tgl_simpan($k), TRUE);
                        unset($post[$p]);
                    }
                }


                $this->db->where('id', $id);
                if ($this->db->update($tbl, $post)) {
                    $status = 1;
                    $msg = "success";
                } else {
                    $db_error = $this->db->error();

                    $status = -1;
                    $msg = $db_error['message'];
                }
            }
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }


        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code


    }

    public function crud_np_post()
    {
        $status = -1;
        $msg = "error";

        $post = $this->input->post();
        $datadb = array();
        $tbl = $post['tbl'];
        $id = $post['id'];
        $act = $post['act'];

        unset($post['act']);
        unset($post['id']);
        unset($post['tbl']);

        if (!empty($act)) {
            $status = 1;
            $msg = "success";

            if ($act == 'save') {

                if (!empty($post['user'])) {
                    $user = $post['user'];
                    $this->db->set('user_created', $user, TRUE);
                    $this->db->set('user_updated', $user, TRUE);

                    $this->db->set('dt_created', 'NOW()', FALSE);
                    $this->db->set('dt_updated', 'NOW()', FALSE);
                    unset($post['user']);
                }


                foreach ($post as $p => $k) {
                    if (strpos($p, 'tgl_') !== false) {
                        //echo $p.' '.tgl_simpan($k);
                        $this->db->set($p, tgl_simpan($k), TRUE);
                        unset($post[$p]);
                    }
                }


                if ($this->db->insert($tbl, $post)) {
                    $status = 1;
                    $msg = "success";
                } else {
                    $db_error = $this->db->error();

                    $status = -1;
                    $msg = $db_error['message'];
                }
            } else if ($act == 'update') {
                if (!empty($post['user'])) {
                    $user = $post['user'];
                    $this->db->set('user_updated', $user, TRUE);

                    $this->db->set('dt_updated', 'NOW()', FALSE);
                    unset($post['user']);
                }


                foreach ($post as $p => $k) {
                    if (strpos($p, 'tgl_') !== false) {
                        //echo $p.' '.tgl_simpan($k);
                        $this->db->set($p, tgl_simpan($k), TRUE);
                        unset($post[$p]);
                    }
                }


                $this->db->where('id', $id);
                if ($this->db->update($tbl, $post)) {
                    $status = 1;
                    $msg = "success";

                    $s = $this->orders->insertorderdetailstatus($id);

                    $status = $s['status'];
                    $msg = $s['message'];
                } else {
                    $db_error = $this->db->error();

                    $status = -1;
                    $msg = $db_error['message'];
                }
            }
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }


        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code


    }

    function send_create_order($data, $items)
    {
        $curl = curl_init();
        $items_data = array();
        $netamount_total = 0;
        $amount_total = 0;
        foreach ($items as $t) {

            if (!empty($t->length_value)) {
                $length = $t->length_value;
            } else {
                $length = $this->cart->get_length($t->length_id);
            }
            $warna = "";
            if (!empty($t->color_name)) {
                $warna = $t->color_name;
            }
            $lineid = base64_encode($t->id);
            $price = $t->price;
            $qtyfisik = $t->qty_fisik;

            $netamount = $t->total;
            $price_old = $t->price_old;
            $amount = $t->amount;
            $discamount = $t->discount_price;


            $disc = $t->total - $amount;
            $items_data[] = array(
                "ITEMID" => $t->product_ax_id, "LINEID" => $lineid, "ITEMNAME" => $t->product_ax_name, "QTYFISIK" => $qtyfisik, "QTY" => $t->qty,
                "UKURAN" => $length,
                "WARNA" => $warna, "DRAFTORDERID" => $data->order_apps_id, "UNITPRICE" => $price, "NETAMOUNT" => $netamount, "AMOUNT" => $amount,
                "LINEPERCENT" => $t->discount, "DISCAMOUNT" => $discamount
            );
            /*
            $amount_total += $amount;
            $netamount_total += $netamount;
            */
        }

        $voucher_code = "";
        if (!empty($data->voucher_code)) {
            $voucher_code = $data->voucher_code;
        }
        $total = $data->total;
        //		$gr_total = $data->total-$data->voucher_discount_price;
        $netamount_total_voucher = $total - $data->voucher_discount_price;

        $array = array(
            "penawaranid" => "", "custaccount" => $data->customer_id, "sales" => $data->sales_id, "ongkos" => $data->shipping_total,
            "amount" => $total, "netamount" => $netamount_total_voucher, "draftorderid" => $data->order_apps_id, "invoiceid" => $data->invoice_id,
            "top" => $data->top, "voucher_code" => $voucher_code, "voucher_amount" => $data->voucher_discount_price, "items" => $items_data
        );

        $data_json = json_encode($array);

        curl_setopt_array($curl, array(
            CURLOPT_URL => AX_URL . 'insert_data',
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

        curl_close($curl);

        return $response;
        //echo $data_json;

    }


    function updateorderstatus_post()
    {
        $orderid = $this->input->post("orderid");
        $statusorder = $this->input->post("status");

        $status = -1;
        $msg = "error DB";
        $data = $this->cart->get_order($orderid);
        //$a = "Data success";
        if (!empty($data)) {
            $status = 1;
            $msg = "success";
            $this->orders->update_status_order($orderid, $statusorder);

            if ($statusorder == 15) {
                $userid = $this->session->id;
                if (empty($userid)) {
                    $userid = $this->input->post("userid");
                }


                $user = $this->users->getAdminById($userid)->row();
                // $user = $this->orders->get_customer_id($data->user_id);


                //  $this->orders->update_status_order($orderid,15);

                $aa = $this->orders->send_notif_orderadmin($user, $orderid, 15);
                $this->orders->create_order_detail_status($orderid, 15, $user);


                //$this->orders->send_notif_orderadmin($user,$orderid,$statusorder);
            }

            if ($statusorder == 1) {
                $userid = $this->session->id;
                /*
                $user = $this->users->getAdminById($userid)->row();
                $this->orders->send_notif_orderadmin($user,$orderid,$statusorder);
                */
                $user = $this->users->getAdminById($userid)->row();
                $items = $this->cart->get_items_order($orderid);
                $a = $this->cart->send_create_order($data, $items);
                //$a = "Data success";
                if ($a == '"Data Success"') {
                    $status = 1;
                    $msg = "success";

                    $user->user_id = $user->id;

                    $this->orders->create_order_detail_status($orderid, 1, $user);
                    $this->orders->update_status_order($orderid, 1);

                    //$aa = $this->orders->send_notif_orderadmin($user,$orderid,1);
                } else {
                    $status = -1;
                    $msg = "Failed send to AX $a";
                }
            }
        }

        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code

    }

    function createorderax_post()
    {
        $orderid = $this->input->post("orderid");

        $status = -1;
        $msg = "Error";
        $data = $this->cart->get_order($orderid);
        $items = $this->cart->get_items_order($orderid);
        //$a = $this->send_create_order($data,$items);
        $a = $this->cart->send_create_order($data, $items);
        //$a = "Data success";
        if ($a == '"Data Success"') {
            $status = 1;
            $msg = "success";
            $this->orders->update_status_order($orderid, 1);

            $d = $this->db->query("SELECT * FROM orders WHERE id = $orderid")->row();
            $user = (object)array("user_id" => $d->user_id);

            $this->orders->create_order_detail_status($orderid, 1, $user);
        }

        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code

    }

    public function crud_varian_post()
    {
        $status = -1;
        $msg = "error";

        $post = $this->input->post();
        $datadb = array();
        $tbl = $post['tbl'];
        $id = $post['id'];
        $act = $post['act'];

        unset($post['act']);
        unset($post['id']);
        unset($post['tbl']);

        if (!empty($act)) {
            $status = 1;
            $msg = "success";

            $thickness_id = implode($post['thickness_id'], ",");
            $length_id = implode($post['length_id'], ",");
            $color_id = $post['color_id'];
            $product_master_id = $post['product_master_id'];
            $width = $post['width'];
            $length = $post['length'];
            $height = $post['height'];
            $product_ax_id = $post['product_ax_id'];
            $product_ax_name = $post['product_ax_name'];
            $product_ax_amount = $post['product_ax_amount'];
            $color_array = $post['color_array'];
            //unset($post['thickness_id']); 
            // unset($post['length_id']);

            if ($act == 'save') {

                $a = $this->product->checkProduct($post['color_id'], $length_id, $thickness_id, $post['product_master_id']);

                if (!empty($a->id)) {
                    $status = -1;
                    $msg = "Kombinasi Varian sudah ada";
                } else {

                    if (!empty($post['user'])) {
                        $user = $post['user'];
                        $this->db->set('user_created', $user, TRUE);
                        $this->db->set('user_updated', $user, TRUE);

                        $this->db->set('dt_created', 'NOW()', FALSE);
                        $this->db->set('dt_updated', 'NOW()', FALSE);
                        unset($post['user']);
                    }
                    /*
                    foreach($post as $p => $k) {
                        if (strpos($p, 'tgl_') !== false) {
                            //echo $p.' '.tgl_simpan($k);
                            $this->db->set($p, tgl_simpan($k), TRUE);
                            unset($post[$p]);
                        }
                    }
                    */

                    if (isset($post['status'])) {
                        $this->db->set('status', 1, TRUE);
                    } else {
                        $this->db->set('status', 0, TRUE);
                    }

                    $post2 = array(
                        "color_id" => $color_id, "color_array" => $color_array, "thickness_id" => $thickness_id, "length_id" => $length_id, "product_master_id" => $product_master_id,
                        "width" => $width, "length" => $length, "height" => $height, "product_ax_id" => $product_ax_id, "product_ax_name" => $product_ax_name,
                        "product_ax_amount" => $product_ax_amount
                    );


                    if ($this->db->insert($tbl, $post2)) {
                        $status = 1;
                        $msg = "success";
                    } else {
                        $db_error = $this->db->error();

                        $status = -1;
                        $msg = $db_error['message'];
                    }
                }
            } else if ($act == 'update') {
                if (!empty($post['user'])) {
                    $user = $post['user'];
                    $this->db->set('user_updated', $user, TRUE);

                    $this->db->set('dt_updated', 'NOW()', FALSE);
                    unset($post['user']);
                }

                if (isset($post['status'])) {
                    $this->db->set('status', 1, TRUE);
                } else {
                    $this->db->set('status', 0, TRUE);
                }

                /*
                foreach($post as $p => $k) {
                    if (strpos($p, 'tgl_') !== false) {
                        //echo $p.' '.tgl_simpan($k);
                        $this->db->set($p, tgl_simpan($k), TRUE);
                        unset($post[$p]);
                    }
                }
                */

                $post2 = array(
                    "color_id" => $color_id, "thickness_id" => $thickness_id, "length_id" => $length_id, "product_master_id" => $product_master_id,
                    "width" => $width, "length" => $length, "height" => $height, "product_ax_id" => $product_ax_id, "product_ax_name" => $product_ax_name, "product_ax_amount" => $product_ax_amount
                );


                $this->db->where('id', $id);
                if ($this->db->update($tbl, $post2)) {
                    $status = 1;
                    $msg = "success";
                } else {
                    $db_error = $this->db->error();

                    $status = -1;
                    $msg = $db_error['message'];
                }
            }
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }


        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code


    }

    public function crud_product_post()
    {
        $status = -1;
        $msg = "error";

        $post = $this->input->post();
        $datadb = array();
        $tbl = $post['tbl'];
        $id = $post['id'];
        $act = $post['act'];

        unset($post['act']);
        unset($post['id']);
        unset($post['tbl']);


        if (!empty($act)) {
            $status = 1;
            $msg = "success";

            $sku = $post['sku'];
            $nama = $post['nama'];
            $users_id = $post['users_id'];

            $config['upload_path']   = FCPATH . 'uploads/product/';
            $config['allowed_types'] = 'gif|jpg|png|ico';
            $this->load->library('upload', $config);

            $this->load->library('image_lib');
            $saveFile = "";
            if (!empty($_FILES)) {
                $tempFile = $_FILES['image']['tmp_name'];
                $fileName = $_FILES['image']['name'];
                $fileName = str_replace(" ", "-", $_FILES['image']['name']);
                $fig = rand(1, 999999);
                if (!empty($fileName)) {
                    $saveFile = $fig . '_' . $fileName;
                }

                if (strpos($fileName, 'php') !== false) {
                } else {

                    $targetPath = "uploads/product/";

                    $targetFile = $targetPath . $saveFile;
                    move_uploaded_file($tempFile, $targetFile);
                }
            }
            unset($post['image']);


            if ($act == 'save') {

                $a = $this->ecommerce->checkProduct($users_id, $nama, $sku);

                if (!empty($a->id)) {
                    $status = -1;
                    $msg = "SKU atau nama barang sudah ada";
                } else {



                    if (!empty($post['user'])) {
                        $user = $post['user'];
                        $this->db->set('user_created', $user, TRUE);
                        $this->db->set('user_updated', $user, TRUE);

                        $this->db->set('dt_created', 'NOW()', FALSE);
                        $this->db->set('dt_updated', 'NOW()', FALSE);
                        unset($post['user']);
                    }

                    if (!empty($saveFile)) {
                        $this->db->set('image', $saveFile, TRUE);
                    }

                    if (!empty($post['password'])) {
                        $pass = md5($post['password']);
                        $this->db->set('password', $pass, TRUE);
                        unset($post['password']);
                    }


                    foreach ($post as $p => $k) {
                        if (strpos($p, 'tgl_') !== false) {
                            //echo $p.' '.tgl_simpan($k);
                            $this->db->set($p, tgl_simpan($k), TRUE);
                            unset($post[$p]);
                        }
                    }


                    $this->db->insert($tbl, $post);
                }
            } else if ($act == 'update') {
                if (!empty($post['user'])) {
                    $user = $post['user'];
                    $this->db->set('user_updated', $user, TRUE);

                    $this->db->set('dt_updated', 'NOW()', FALSE);
                    unset($post['user']);
                }

                if (!empty($saveFile)) {
                    $this->db->set('image', $saveFile, TRUE);
                }


                if (!empty($post['password'])) {
                    $pass = md5($post['password']);
                    $this->db->set('password', $pass, TRUE);
                    unset($post['password']);
                }

                foreach ($post as $p => $k) {
                    if (strpos($p, 'tgl_') !== false) {
                        //echo $p.' '.tgl_simpan($k);
                        $this->db->set($p, tgl_simpan($k), TRUE);
                        unset($post[$p]);
                    }
                }


                $this->db->where('id', $id);
                $this->db->update($tbl, $post);
            }
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }





        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code


    }

    public function crud_item_post()
    {
        $status = -1;
        $msg = "error";

        $post = $this->input->post();
        $datadb = array();
        $tbl = $post['tbl'];
        $id = $post['id'];
        $act = $post['act'];

        unset($post['act']);
        unset($post['id']);
        unset($post['tbl']);


        if (!empty($act)) {
            $status = -1;
            $msg = "error";

            $newprice = $post['price'];
            $users_id = $post['user'];

            $a = $this->orders->check_order_detail($id);
            if (!empty($a->id)) {
                $priceold = $a->price_old;
                $total = $priceold;
                $orderid = $a->orders_id;
                if (!empty($newprice)) {
                    $pcr = (($priceold - $newprice) / $priceold) * 100;
                    $newdisc = round($pcr, 2);
                    $total = $newprice * $a->qty;
                    $discount_price = $a->amount - $total;

                    $status = 1;
                    $msg = "success";

                    # Match attempt failed
                    $data_order = array("discount" => $newdisc, "price" => $newprice, "total" => $total, "discount_price" => $discount_price);
                    $this->orders->update_order_detail($data_order, $id);

                    $user = $this->orders->get_customer_id($a->user_id);

                    $pry = $this->cart->get_order_detail_id($orderid);
                    $gr_total = 0;
                    $total = 0;
                    foreach ($pry as $o) {
                        $total = $o->total;
                        $gr_total += $total;
                    }

                    $dataorder = array("total" => $gr_total);

                    $this->orders->update_order($dataorder, $orderid);

                    $cc = $this->db->query("SELECT * FROM orders WHERE id = $orderid")->row();

                    $total_new = $this->orders->get_total_disc_ship($gr_total, $cc);
                    $dataorder2 = array("total" => $total_new);

                    $this->orders->update_invoice($dataorder2, $orderid);

                    /*
                    if (have2dec($pcr)) {
                        $newprice = $priceold-(($priceold*$newdisc)/100);
                        # Successful match
                        $status = -1;
                        $msg = "Please use 2 digit after comma discount, Suggest Price $newprice ( $newdisc %)";
                    } else {
                        $status = 1;
                        $msg = "success";
                        
                        # Match attempt failed
                        $data_order = array("discount"=>$newdisc,"price"=>$newprice,"total"=>$total,"discount_price"=>$discount_price);
                        $this->orders->update_order_detail($data_order,$id);
                        
                    }
                    */
                }

                if ($status == 1) {
                    $order_id = $a->orders_id;
                    $pr = $this->cart->get_order_detail_id($order_id);
                    $total_all = 0;
                    foreach ($pr as $p) {
                        $total_all += $p->total;
                    }

                    if (!empty($total_all)) {
                        $dataorder = array("total" => $total_all);

                        $this->orders->update_order($dataorder, $order_id);
                    }
                }
            } else {
                $status = -1;
                $msg = "Not Found";
            }
            /*
            if(!empty($post['user'])) {
                $user = $post['user'];
                $this->db->set('user_updated', $user, TRUE);
                
                $this->db->set('dt_updated', 'NOW()', FALSE);
                unset($post['user']);
            }


            foreach($post as $p => $k) {
                if (strpos($p, 'tgl_') !== false) {
                    //echo $p.' '.tgl_simpan($k);
                    $this->db->set($p, tgl_simpan($k), TRUE);
                    unset($post[$p]);
                }
            }
            

            $this->db->where('id',$id);
            $this->db->update($tbl,$post);
            */
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }





        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code


    }

    public function crud_promobanner_post()
    {
        $status = -1;
        $msg = "error";

        $post = $this->input->post();
        $datadb = array();
        $tbl = $post['tbl'];
        $id = $post['id'];
        $act = $post['act'];

        unset($post['act']);
        unset($post['id']);
        unset($post['tbl']);
        $cabang = $post['cabang'];


        if (!empty($act)) {
            $status = 1;
            $msg = "success";

            $sku = $post['sku'];
            $nama = $post['nama'];
            $users_id = $post['users_id'];

            $config['upload_path']   = FCPATH . 'uploads/banner/';
            $config['allowed_types'] = 'gif|jpg|png|ico';
            $this->load->library('upload', $config);

            $this->load->library('image_lib');
            $saveFile = "";
            /*
            if (!empty ($_FILES)) {
                    $tempFile = $_FILES['image']['tmp_name'];
                    $fileName = $_FILES['image']['name'];
                    $fileName = str_replace(" ", "-", $_FILES['image']['name']);
                    $fig = rand(1, 999999);
                    if(!empty($fileName)) {
                        $saveFile = $fig . '_' . $fileName;
                    }

                    if (strpos($fileName,'php') !== false) {

                    }else{

                        $targetPath = "uploads/banner/";

                        $targetFile = $targetPath . $saveFile;
                        move_uploaded_file($tempFile, $targetFile);                        

                    }

                   
            }
            */
            $stat = 0;
            if (!empty($_FILES)) {
                $tempFile = $_FILES['image']['tmp_name'];
                $fileName = $_FILES['image']['name'];
                $fileName = str_replace(" ", "-", $_FILES['image']['name']);
                $fig = rand(1, 999999);
                if (!empty($fileName)) {
                    $saveFile = $fig . '_' . $fileName;
                }

                if (strpos($fileName, 'php') !== false) {
                    $stat = -1;
                } else {
                    $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                    $validate = array("JPG", "jpg", "PNG", "png", "JPEG", "jpeg");

                    if (in_array($ext, $validate)) {

                        $targetPath = "uploads/banner/";

                        $targetFile = $targetPath . $saveFile;
                        move_uploaded_file($tempFile, $targetFile);
                    } else {
                        $stat = -1;
                    }
                }
            }
            unset($post['image']);

            if (empty($stat)) {
                if ($act == 'save') {


                    if (!empty($post['user'])) {
                        $user = $post['user'];
                        $this->db->set('user_created', $user, TRUE);
                        $this->db->set('user_updated', $user, TRUE);

                        $this->db->set('dt_created', 'NOW()', FALSE);
                        $this->db->set('dt_updated', 'NOW()', FALSE);
                        unset($post['user']);
                    }

                    if (!empty($saveFile)) {
                        $this->db->set('image', $saveFile, TRUE);
                    }

                    if (!empty($post['password'])) {
                        $pass = md5($post['password']);
                        $this->db->set('password', $pass, TRUE);
                        unset($post['password']);
                    }


                    foreach ($post as $p => $k) {
                        if (strpos($p, 'tgl_') !== false) {
                            //echo $p.' '.tgl_simpan($k);
                            $this->db->set($p, tgl_simpan($k), TRUE);
                            unset($post[$p]);
                        }
                    }


                    if ($this->db->insert($tbl, $post)) {
                        $this->users->updatePromoUsers($cabang);
                        $status = 1;
                        $msg = "success";
                    } else {
                        $db_error = $this->db->error();

                        $status = -1;
                        $msg = $db_error['message'];
                    }
                } else if ($act == 'update') {
                    if (!empty($post['user'])) {
                        $user = $post['user'];
                        $this->db->set('user_updated', $user, TRUE);

                        $this->db->set('dt_updated', 'NOW()', FALSE);
                        unset($post['user']);
                    }

                    if (!empty($saveFile)) {
                        $this->db->set('image', $saveFile, TRUE);
                    }


                    if (!empty($post['password'])) {
                        $pass = md5($post['password']);
                        $this->db->set('password', $pass, TRUE);
                        unset($post['password']);
                    }

                    foreach ($post as $p => $k) {
                        if (strpos($p, 'tgl_') !== false) {
                            //echo $p.' '.tgl_simpan($k);
                            $this->db->set($p, tgl_simpan($k), TRUE);
                            unset($post[$p]);
                        }
                    }


                    $this->db->where('id', $id);
                    if ($this->db->update($tbl, $post)) {
                        $this->users->updatePromoUsers($cabang);

                        $status = 1;
                        $msg = "success";
                    } else {
                        $db_error = $this->db->error();

                        $status = -1;
                        $msg = $db_error['message'];
                    }
                }
            } else {
                $status = -1;
                $msg = "Gambar harus ext (jpg atau png) $ext";
            }
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }





        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code


    }


    public function crud_specialpromo_post()
    {
        $status = -1;
        $msg = "error";

        $post = $this->input->post();
        $datadb = array();
        $tbl = $post['tbl'];
        $id = $post['id'];
        $act = $post['act'];

        unset($post['act']);
        unset($post['id']);
        unset($post['tbl']);
        $cabang = $post['cabang'];


        if (!empty($act)) {
            $status = 1;
            $msg = "success";

            $sku = $post['sku'];
            $nama = $post['nama'];
            $users_id = $post['users_id'];

            $config['upload_path']   = FCPATH . 'uploads/banner/';
            $config['allowed_types'] = 'gif|jpg|png|ico';
            $this->load->library('upload', $config);

            $this->load->library('image_lib');
            $saveFile = "";
            $stat = 0;
            if (!empty($_FILES)) {
                $tempFile = $_FILES['image']['tmp_name'];
                $fileName = $_FILES['image']['name'];
                $fileName = str_replace(" ", "-", $_FILES['image']['name']);
                $fig = rand(1, 999999);
                if (!empty($fileName)) {
                    $saveFile = $fig . '_' . $fileName;
                }

                if (strpos($fileName, 'php') !== false) {
                    $stat = -1;
                } else {
                    $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                    $validate = array("JPG", "jpg", "PNG", "png", "JPEG", "jpeg");

                    if (in_array($ext, $validate)) {

                        $targetPath = "uploads/banner/";

                        $targetFile = $targetPath . $saveFile;
                        move_uploaded_file($tempFile, $targetFile);
                    } else {
                        $stat = -1;
                    }
                }
            }
            unset($post['image']);

            if (empty($stat)) {

                if ($act == 'save') {


                    if (!empty($post['user'])) {
                        $user = $post['user'];
                        $this->db->set('user_created', $user, TRUE);
                        $this->db->set('user_updated', $user, TRUE);

                        $this->db->set('dt_created', 'NOW()', FALSE);
                        $this->db->set('dt_updated', 'NOW()', FALSE);
                        unset($post['user']);
                    }

                    if (!empty($saveFile)) {
                        $this->db->set('image', $saveFile, TRUE);
                    }

                    if (!empty($post['password'])) {
                        $pass = md5($post['password']);
                        $this->db->set('password', $pass, TRUE);
                        unset($post['password']);
                    }


                    foreach ($post as $p => $k) {
                        if (strpos($p, 'tgl_') !== false) {
                            //echo $p.' '.tgl_simpan($k);
                            $this->db->set($p, tgl_simpan($k), TRUE);
                            unset($post[$p]);
                        }
                    }


                    if ($this->db->insert($tbl, $post)) {
                        $this->users->updatePromoUsers($cabang);
                        $status = 1;
                        $msg = "success";
                    } else {
                        $db_error = $this->db->error();

                        $status = -1;
                        $msg = $db_error['message'];
                    }
                } else if ($act == 'update') {
                    if (!empty($post['user'])) {
                        $user = $post['user'];
                        $this->db->set('user_updated', $user, TRUE);

                        $this->db->set('dt_updated', 'NOW()', FALSE);
                        unset($post['user']);
                    }

                    if (!empty($saveFile)) {
                        $this->db->set('image', $saveFile, TRUE);
                    }


                    if (!empty($post['password'])) {
                        $pass = md5($post['password']);
                        $this->db->set('password', $pass, TRUE);
                        unset($post['password']);
                    }

                    foreach ($post as $p => $k) {
                        if (strpos($p, 'tgl_') !== false) {
                            //echo $p.' '.tgl_simpan($k);
                            $this->db->set($p, tgl_simpan($k), TRUE);
                            unset($post[$p]);
                        }
                    }


                    $this->db->where('id', $id);
                    if ($this->db->update($tbl, $post)) {
                        $this->users->updatePromoUsers($cabang);

                        $status = 1;
                        $msg = "success";
                    } else {
                        $db_error = $this->db->error();

                        $status = -1;
                        $msg = $db_error['message'];
                    }
                }
            } else {
                $status = -1;
                $msg = "Gambar harus ext (jpg atau png) $ext";
            }
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }





        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code


    }

    public function crud_category_post()
    {
        $status = -1;
        $msg = "error";

		$token = $this->session->token;

        $post = $this->input->post();
        $datadb = array();
        $tbl = $post['tbl'];
        $id = $post['id'];
        $act = $post['act'];

        unset($post['act']);
        unset($post['id']);
        unset($post['tbl']);

        if (!empty($act)) {
            $status = 1;
            $msg = "success";

            if ($act == 'save') {

                /*
                foreach ($post as $p => $k) {
                    if (strpos($p, 'tgl_') !== false) {
                        //echo $p.' '.tgl_simpan($k);
                        $this->db->set($p, tgl_simpan($k), TRUE);
                        unset($post[$p]);
                    }
                }
                */

              // $aa = json_encode($post);
               $post = array("code"=>$post['kode'],"fullname"=>$post['fullname'],"parent_id"=>$post['parent_id']);
               $kry = postcurltoken($tbl,$post,$token);
               
                $k = json_decode($kry);

                if($k->status == "success") {
                    $status = 1;
                    $msg = "success";
                } else {
                    $status = -1;
                    $err = $kry;
                    $msg = "Error";
                }

            } else if ($act == 'update') {
               // $post['id'] = $id;
                $post = array("id"=>$id,"code"=>$post['kode'],"fullname"=>$post['fullname'],"parent_id"=>$post['parent_id']);
                $kry = postcurltoken($tbl,$post,$token);
               
                $k = json_decode($kry);

                if($k->status == "success") {
                    $status = 1;
                    $msg = "success";
                } else {
                    $status = -1;
                    $err = $kry;
                    $msg = "Error";
                }
                
            }
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }


        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code


    }


    public function crud_parentcategory_post()
    {
        $status = -1;
        $msg = "error";

		$token = $this->session->token;

        $post = $this->input->post();
        $datadb = array();
        $tbl = $post['tbl'];
        $id = $post['id'];
        $act = $post['act'];

        unset($post['act']);
        unset($post['id']);
        unset($post['tbl']);

        if (!empty($act)) {
            $status = 1;
            $msg = "success";

            if ($act == 'save') {

              // $aa = json_encode($post);
               $post = array("name"=>$post['name']);
               $kry = postcurltoken($tbl,$post,$token);
               
                $k = json_decode($kry);

                if($k->status == "success") {
                    $status = 1;
                    $msg = "success";
                } else {
                    $status = -1;
                    $err = $kry;
                    $msg = "Error";
                }

            } else if ($act == 'update') {
               // $post['id'] = $id;
                $post = array("id"=>$id,"name"=>$post['name']);
                $kry = postcurltoken($tbl,$post,$token);
               
                $k = json_decode($kry);

                if($k->status == "success") {
                    $status = 1;
                    $msg = "success";
                } else {
                    $status = -1;
                    $err = $kry;
                    $msg = "Error";
                }
                
            }
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }


        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code


    }

    public function crud_akun_post()
    {
        $status = -1;
        $msg = "error";

        $post = $this->input->post();
        $datadb = array();
        $tbl = $post['tbl'];
        $id = $post['id'];
        $act = $post['act'];

        unset($post['act']);
        unset($post['id']);
        unset($post['tbl']);


        if (!empty($act)) {
            $status = 1;
            $msg = "success";

            $config['upload_path']   = FCPATH . 'uploads/profile/';
            $config['allowed_types'] = 'gif|jpg|png|ico';
            $this->load->library('upload', $config);

            $this->load->library('image_lib');
            $saveFile = "";
            if (!empty($_FILES)) {
                $tempFile = $_FILES['avatar']['tmp_name'];
                $fileName = $_FILES['avatar']['name'];
                $fileName = str_replace(" ", "-", $_FILES['image']['name']);
                $fig = rand(1, 999999);
                if (!empty($fileName)) {
                    $saveFile = $fig . '_' . $fileName;
                }

                if (strpos($fileName, 'php') !== false) {
                } else {

                    $targetPath = "uploads/profile/";

                    $targetFile = $targetPath . $saveFile;
                    move_uploaded_file($tempFile, $targetFile);
                }
            }
            unset($post['avatar']);

            if ($act == 'update') {
                if (!empty($post['user'])) {
                    $user = $post['user'];
                    $this->db->set('user_updated', $user, TRUE);

                    $this->db->set('dt_updated', 'NOW()', FALSE);
                    unset($post['user']);
                }

                if (!empty($saveFile)) {
                    $this->db->set('avatar', $saveFile, TRUE);
                }


                if (!empty($post['password'])) {
                    $pass = md5($post['password']);
                    $this->db->set('password', $pass, TRUE);
                    unset($post['password']);
                }

                foreach ($post as $p => $k) {
                    if (strpos($p, 'tgl_') !== false) {
                        //echo $p.' '.tgl_simpan($k);
                        $this->db->set($p, tgl_simpan($k), TRUE);
                        unset($post[$p]);
                    }
                }


                $this->db->where('id', $id);
                $this->db->update($tbl, $post);
            }
        } else {
            $status = -1;
            $msg = "Error Missing Field";
        }





        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code


    }

    public function register_post()
    {
        $val = $this->input->post();
        //$status = -1;
        $a = $this->model_users->register_user($val);

        $message = [
            'status' => $a['status'],
            'message' => $a['msg']
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
    }

    public function crud_channel_post()
    {

        $data = $this->input->post();
        //$status = -1;

        $url = $data['url'];
        $channel = $data['channel'];
        $user = $data['user'];
        $users_id = $data['users_id'];
        /*
        $ada = "";
       
        if(empty($data['id'])) {
            $ada = $this->model_users->check_user($email,$phone);
            
        }
        $status = 1;
        $msg = "";
        */
        $ada = 0;
        $status = 1;

        if (empty($ada)) {

            $datadb = array(
                'url' => $url,
                'users_id' => $users_id, 'channel' => $channel
            );


            if (!empty($data['id'])) {
                $this->db->where('id', $data['id']);
                $this->db->set('user_updated', $user, TRUE);
                $this->db->set('dt_updated', 'NOW()', FALSE);

                $this->db->update('channel', $datadb);
            } else {
                $this->db->set('user_created', $user, TRUE);
                $this->db->set('user_updated', $user, TRUE);
                $this->db->set('dt_created', 'NOW()', FALSE);
                $this->db->set('dt_updated', 'NOW()', FALSE);
                $this->db->insert('channel', $datadb);
            }
            $msg = "Berhasil Register, mohon tunggu channel anda sedang di proses";
        } else {
            $status = -1;
            $msg = "Channel sudah terdaftar";
        }

        //$message = array("status"=>$status,"msg"=>$msg);

        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
    }
    /*
    public function crud_admin_post()
    {
        
        $data = $this->input->post();
        //$status = -1;
        
        $username = $this->session->username;

        //$id = $data['id'];

        $email = $data['email'];
        $phone = $data['no_telp'];
        $ada = "";
       
        if(empty($data['id'])) {
            $ada = $this->model_users->check_user($email,$phone);
            
        }
        $status = 1;
        $msg = "";
        
        if(empty($ada)) {
            $month = $data['month'];
            $tgl_mulai = tgl_simpan($data['tgl_mulai']);
            $tgl_expired = date ("Y-m-d", strtotime("+$month months", strtotime($tgl_mulai)));
            
            $datadb = array('username'=>$this->db->escape_str($data['nama_lengkap']),
            'nama_lengkap'=>$this->db->escape_str($data['nama_lengkap']),'tgl_mulai'=>$tgl_mulai,
            'tgl_expired'=>$tgl_expired,'month'=>$month,
            'no_telp'=>$this->db->escape_str($phone),
            'level'=>'owner','email'=>$this->db->escape_str($email),'role'=>'3',
            
            'id_session'=>md5($data['nama_lengkap']));
            //$this->db->insert('users',$datadb);
            
            if(!empty($data['pass'])) {
                
        		$pass = $this->common->encrypt($data['pass']);
                $this->db->set('password', $pass, TRUE);
                $this->db->set('show_password', $data['pass'], TRUE);
            }

            $user = $data['users_id'];
           
            
            
            if(!empty($data['id'])) {
                $this->db->where('id',$data['id']);
                $this->db->set('user_updated', $user, TRUE);
                $this->db->set('dt_updated', 'NOW()', FALSE);
                $this->db->update('users',$datadb);
            } else {
                $this->db->set('user_created', $user, TRUE);
                $this->db->set('user_updated', $user, TRUE);
                $this->db->set('dt_created', 'NOW()', FALSE);
                $this->db->set('dt_updated', 'NOW()', FALSE);
                $this->db->insert('users',$datadb);

                $id = $this->db->insert_id();
                $this->model_users->add_user_menu($id,1);                

            }
            $msg = "Berhasil Register, mohon cek email anda untuk verifikasi data";
        } else {
            $status = -1;
            $msg = "Email anda sudah terdaftar";
        }
        
        //$message = array("status"=>$status,"msg"=>$msg);

        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
    }
    */

    public function crud_tagihan_post()
    {
        $jenis = $this->input->post('jenis');
        $jumlah = $this->input->post('jumlah');
        $jenis_tagihan = $this->input->post('jenis_tagihan');
        $act = $this->input->post('act');
        $id = $this->input->post('id');

        $status = 1;
        $msg = "success";
        // $msg = $jenis.' '.$jumlah;

        $datetime = date('YmdHis');

        $old_tagihan =  $this->session->userdata('tagihan');

        if ($jenis == 'tagihan') {
            $jt = array("411101001", "611101047", "611101060", "711102060", "411101002");
            if (in_array($jenis_tagihan, $jt)) {
                $status = -1;
                $msg = "Mohon maaf biaya inklaring, angkutan dan ppn tidak bisa di billing tagihan";
            }
        }

        if ($jenis == 'jasangkut') {
            $jt = 411101002;
            if ($jenis_tagihan != $jt) {
                $status = -1;
                $msg = "Mohon Maaf harus jasa angkutan";
            }
        }

        if ($act == 'save') {
            if ($status == 1) {
                if (empty($old_tagihan)) {
                    $tagihan[] = array("jenis" => $jenis_tagihan, "jumlah" => $jumlah, "id" => $datetime);
                    $this->session->set_userdata('tagihan', $tagihan);
                } else {
                    $tagihan = array("jenis" => $jenis_tagihan, "jumlah" => $jumlah, "id" => $datetime);
                    array_push($old_tagihan, $tagihan);
                    $this->session->set_userdata('tagihan', $old_tagihan);
                }
            }
        } else if ($act == 'update') {
            $k = 0;
            $b = 0;
            foreach ($_SESSION['tagihan'] as $d) {
                // echo $d['id'][$id];
                $k++;
                if ($d['id'] == $id) {
                    $msg = "jenis $jenis_tagihan";
                    $b = $k;
                }
            }
            if (!empty($b)) {
                $b = $b - 1;
                $_SESSION['tagihan'][$b] = array("jenis" => $jenis_tagihan, "jumlah" => $jumlah, "id" => $id);
            }
        }


        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
    }


    public function crud_tagihan_update_post()
    {
        $jenis = $this->input->post('jenis');
        $jumlah = $this->input->post('jumlah');
        $jenis_tagihan = $this->input->post('jenis_tagihan');
        $act = $this->input->post('act');
        $id = $this->input->post('id');
        $id_billing = $this->input->post('id_billing');
        $id_billing_update = $this->input->post('id_billing_update');

        $status = 1;
        $msg = "success";
        // $msg = $jenis.' '.$jumlah;

        $datetime = date('YmdHis');

        $old_tagihan =  $this->session->userdata('tagihan');
        if(!empty($jumlah)) {
            $jumlah = harga_remove_rp($jumlah);
        }

        if ($jenis == 'tagihan') {
            $jt = array("411101001", "611101047", "611101060", "711102060", "411101002");
            if (in_array($jenis_tagihan, $jt)) {
                $status = -1;
                $msg = "Mohon maaf biaya inklaring, angkutam dan ppn tidak bisa di billing tagihan";
            }
        }

        if ($act == 'save') {
            if ($status == 1) {
                //$status = -1;
                //$msg = "$id_billing $jenis_tagihan $jumlah $act";
                
                $d = $this->db->query("SELECT * FROM tbbilling WHERE id = '$id_billing'")->row_array();
                $d2 = $this->db->query("SELECT * FROM tbakun WHERE kodeakun = '$jenis_tagihan'")->row_array();
                if(!empty($d['id'])) {
                    $datadetail2 = array("id_billing" => $id_billing, "no_billing" => $d['no'], "kd" => $d2['kodeakun'], "jenis_tagihan" => $d2['namaakun'], 
                    "jumlah" => $jumlah);

                    if ($this->db->insert("tb_detail_billing", $datadetail2)) {
                        $status = 1;
                        $msg = "success";

                        $b = $this->db->query("SELECT SUM(jumlah) as total FROM tb_detail_billing WHERE id_billing = '$id_billing'")->row();
                        $pst = array("total_tagihan"=>$b->total);
                        $this->db->where("id",$id_billing);
                        $this->db->update("tbbilling",$pst);
                    } else {
                        $db_error = $this->db->error();
    
                        $status = -1;
                        $msg = $db_error['message'];
                    }
                } else {
                    $status = -1;
                    $msg = "No data billing";
                }


            }
        } else if ($act == 'update') {

            $pst = array("jumlah"=>$jumlah);
            $this->db->where("id",$id);
            if($this->db->update("tb_detail_billing",$pst)) {
                $status = 1;
                $msg = "success";

                $b = $this->db->query("SELECT SUM(jumlah) as total FROM tb_detail_billing WHERE id_billing = '$id_billing'")->row();
                $pst = array("total_tagihan"=>$b->total);
                $this->db->where("id",$id_billing);
                $this->db->update("tbbilling",$pst);
            } else {
                $db_error = $this->db->error();

                $status = -1;
                $msg = $db_error['message'];
            }
        }


        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code
    }


    public function ubah_jumlah_post()
    {
        $vol =  $this->input->post('vol');
        $harga =  $this->input->post('harga');

        $status = 1;
        $msg = "success";
        if (!empty($vol) && !empty($harga)) {
            $jumlah = $vol * $harga;
            $jumlah_f = harga($jumlah);
        } else {
            $jumlah = 0;
            $jumlah_f = 0;
        }


        $message = [
            'status' => $status,
            'message' => $msg,
            'jumlah' => $jumlah,
            'jumlah_f' => $jumlah_f
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code


    }

    
    public function piutang_post()
    {
        /*
        $vol =  $this->input->post('vol');
        $harga =  $this->input->post('harga');
        */
        $d = $this->input->post();
        $js = json_encode($d);

        $status = -1;
        $msg = "success $js";
        

        $message = [
            'status' => $status,
            'message' => $msg
        ];
        $this->set_response($message, REST_Controller::HTTP_OK); // CREATED (201) being the HTTP response code


    }
}
