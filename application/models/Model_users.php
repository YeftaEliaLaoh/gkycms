<?php 
class Model_users extends CI_model{
    var $table = 'cabang';
	var $column_order = array(null, 'nama','aktif'); //set column field database for datatable orderable
	var $column_search = array('nama','aktif'); //set column field database for datatable searchable 
	var $order = array('id' => 'desc'); // default order 

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		
		$i = 0;
		$wh = "";
		
		$s = $_POST['search']['value'];
		$wh .= " AND u.customer_name LIKE '%$s%'";


		if(isset($_POST['order'])) // here order processing
		{
			//$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
			$t = $_POST['order']['0']['column'];
			$u = $_POST['order']['0']['dir'];
			$wh .= "  ORDER BY $t $u ";
		} 
		else if(isset($this->order))
		{
			$wh .= "  ORDER BY u.id DESC ";
			//$order = $this->order;
			//$this->db->order_by(key($order), $order[key($order)]);
		}
		
		return "SELECT u.*,c.name as cabang_name FROM users u LEFT JOIN cabang c ON c.id=u.cabang WHERE 1=1 $wh";
	}

	public function get_datatables()
	{
		$a = $this->_get_datatables_query();
		if($_POST['length'] != -1)
		//$this->db->limit($_POST['length'], $_POST['start']);
		$l = $_POST['length'];
		$s = $_POST['start'];
		$a .= " LIMIT $s,$l";
		//$query = $this->db->get();
		$query = $this->db->query($a);
		return $query->result();
	}

	public function count_filtered()
	{
		$a = $this->_get_datatables_query();
		//$query = $this->db->get();
		$query = $this->db->query($a);
		return $query->num_rows();
	}

	public function count_all()
	{
		//$this->db->from($this->table);
		$a = $this->_get_datatables_query();
		$query = $this->db->query($a);
		return $query->num_rows();
		//return $this->db->count_all_results();
	}

    function cek_login($username,$password){
        $username = $this->db->escape_str($username);
        //$password = md5($password);
        $password = $this->common->encrypt($password);
        return $this->db->query("SELECT u.* FROM users u  where 
        (u.no_telp = '$username' OR u.email = '$username' OR u.username = '$username') AND u.password='".$password."'");
    }

    function getUserByEmail($email) {
        return $this->db->query("SELECT u.* FROM users u  where 
        (u.phone = '$email' OR u.email = '$email' OR u.username = '$email')");
    }

    function getAdminByEmail($email) {
        return $this->db->query("SELECT u.* FROM admin u  where 
        (u.no_telp = '$email' OR u.email = '$email' OR u.username = '$email')");
    }

    function getAdminById($id) {
        return $this->db->query("SELECT u.* FROM admin u  where u.id = $id");
    }

    function getAdminSales($id) {
        return $this->db->query("SELECT u.* FROM admin u  where u.sales_id = '$id'");
    }
    
	function users(){
		return $this->db->query("SELECT * FROM users");
    }

    function checkupdatePromo($user) {
        return $this->db->query("SELECT * FROM users WHERE ID = $user")->row();
    }

    function updatePromoUsers($cabang) {
        return $this->db->query("UPDATE users SET promo_update = 0 WHERE email IS NOT NULL AND cabang = $cabang");
    }

    function updatePromo_byuser($user,$status) {
        return $this->db->query("UPDATE users SET promo_update = $status WHERE ID = $user");
    }
    
    function add_user_menu($id,$type) {
        if($type == 1) {
            for($i=1;$i<=9;$i++) {
                $datadb = array("main_user_id"=>$id,"mainmenu_id"=>$i);
                $this->db->insert('user_menu',$datadb);
            }
        }
    }

	function users_tambah(){
        $datadb = array('username'=>$this->db->escape_str($this->input->post('a')),
                        'password'=>md5($this->input->post('b')),
                        'nama_lengkap'=>$this->db->escape_str($this->input->post('c')),
                        'level'=>$this->db->escape_str($this->input->post('level')),
                        'blokir'=>'N',
                        'id_session'=>md5($this->input->post('a')));
        $this->db->insert('users',$datadb);
    }

    function check_user($email,$phone) {
        $a = $this->db->query("SELECT * FROM users WHERE email = '$email' OR phone = '$phone'")->row();
        if(!empty($a->id)) {
            $ada = 1;
        } else {
            $ada = 0;
        }
        return $ada;
    }

    function register_user($data) {
        $email = $data['email'];
        $phone = $data['phone'];
        $ada = $this->check_user($email,$phone);
        $status = 1;
        if(empty($ada)) {
            //$msg = "Oke";
            $datadb = array('nama'=>$this->db->escape_str($data['instansi']),
            'nama_pic'=>$this->db->escape_str($data['nama']),
            'phone'=>$this->db->escape_str($data['phone']),
            'provinsi'=>$this->db->escape_str($data['provinsi']),
            'kota'=>$this->db->escape_str($data['kota']),
            'kecamatan'=>$this->db->escape_str($data['kecamatan']),
            'kelurahan'=>$this->db->escape_str($data['kelurahan']),
            'type'=>$this->db->escape_str($data['type']), 
            'penugasan'=>$this->db->escape_str($data['penugasan']), 
            'id_admin'=>$this->db->escape_str($data['id_admin']), 
            'email'=>$this->db->escape_str($data['email']),
            'user_created' => 1,'user_updated' => 1);
            $this->db->set('dt_created', 'NOW()', FALSE);
            $this->db->set('dt_updated', 'NOW()', FALSE);
            $this->db->insert('kelompok',$datadb);

            $id_kelompok = $this->db->insert_id();
        
            $datadb = array('username'=>$this->db->escape_str($data['instansi']),
                        'password'=>md5($data['passw']),
                        'show_password'=>$data['passw'],
                        'nama_lengkap'=>$this->db->escape_str($data['nama']),
                        'no_telp'=>$this->db->escape_str($data['phone']),
                        'level'=>'user','email'=>$this->db->escape_str($data['email']),
                        'blokir'=>'N',
                        'id_kelompok'=>$id_kelompok,
                        'user_created' => 1,'user_updated' => 1,
                        'id_session'=>md5($data['nama']));
            $this->db->set('dt_created', 'NOW()', FALSE);
            $this->db->set('dt_updated', 'NOW()', FALSE);
             $this->db->insert('users',$datadb);
             

             $url = base_url();
             if($data['type'] == 1) { $type = "Kelompok Masyarakat"; } else { $type = "Instansi"; }

            $subject = "Registrasi Berhasil di Swakelola Badan Restorasi Gambut";
            $nama = $data['nama'];
            $email = $data['email'];
           // $type = "Kelompok Masyarakat";

            $msg2 = "Dear $nama,
            <br><br>
            Terimakasih telah melakukan pendaftaran sebagai $type di Swakelola Badan Restorasi Gambut.<br>
            Selamat Anda berhasil lolos verifikasi, silahkan login ke link berikut : $url <br>
            <br>
            Regards,<br>
            <br>
            Admin Swakelola Badan Restorasi Gambut";  
            $a = sendEmailTemplate($subject,$msg2,$email,$nama);
            
            $msg = "Berhasil Register, mohon cek email anda untuk verifikasi data";
        } else {
            $status = -1;
            $msg = "Email atau No. Telpon anda sudah terdaftar";
        }
        $message = array("status"=>$status,"msg"=>$msg);
        return $message; 
    }

    function users_edit($id){
        return $this->db->query("SELECT u.* FROM users u where u.id='$id'");
    }

    function show_users($id){
        return $this->db->query("SELECT u.*,p.id as id_kelompok FROM users u LEFT JOIN kelompok p ON p.id=u.id_kelompok where u.id='$id'");
    }

    function users_update(){
        $config['upload_path'] = 'asset/avatar/';
        $config['allowed_types'] = 'gif|jpg|png|PNG|JPG|jpeg|JPEG';
        $config['max_size'] = '10000'; // kb
        $this->load->library('upload', $config);
        $this->upload->do_upload('e');
        $hasil=$this->upload->data();
        $nip = $this->input->post('nip');

        if ($hasil['file_name'] !=''){
            $datadb = array('foto'=>$hasil['file_name']);
            $this->db->where('nip',$nip);
            $this->db->update('pegawai',$datadb);
        }
        $id = $this->db->escape_str($this->input->post('id'));
        $password = $this->db->escape_str($this->input->post('password'));
        if (!empty($password)){
            $datadb = array('password'=>$password);
            $this->db->where('id',$id);
            $this->db->update('users',$datadb);
        }

        

        $email = $this->db->escape_str($this->input->post('email'));
        $nama = $this->db->escape_str($this->input->post('nama'));
        $tempat = $this->db->escape_str($this->input->post('tempat'));
        $tgl_lahir = tgl_simpan($this->db->escape_str($this->input->post('tgl_lahir')));
        $ktp = $this->db->escape_str($this->input->post('ktp'));
        $npwp = $this->db->escape_str($this->input->post('npwp'));
        $no_rek = $this->db->escape_str($this->input->post('no_rek'));
        $alamat = $this->db->escape_str($this->input->post('alamat'));
        $phone = $this->db->escape_str($this->input->post('phone'));

        $datadb = array('nama'=>$nama,
        'tempat'=>$tempat,
        'tgl_lahir'=>$tgl_lahir,
        'ktp'=>$ktp,
        'npwp'=>$npwp,
        'email'=>$email,
        'no_rek'=>$no_rek,
        'alamat'=>$alamat,
        'phone'=>$phone);

        $this->db->where('nip',$nip);
        $this->db->update('pegawai',$datadb);
        /*
        if (trim($this->input->post('b'))==''){
            $datadb = array('username'=>$this->db->escape_str($this->input->post('a')),
                            'nama_lengkap'=>$this->db->escape_str($this->input->post('c')),
                            'blokir'=>$this->db->escape_str($this->input->post('h')),
                            'level'=>$this->db->escape_str($this->input->post('level')),
                            'id_session'=>md5($this->input->post('a')));
            $this->db->where('username',$this->input->post('id'));
            $this->db->update('users',$datadb);
        }else{
            $datadb = array('username'=>$this->db->escape_str($this->input->post('a')),
                            'password'=>md5($this->input->post('b')),
                            'nama_lengkap'=>$this->db->escape_str($this->input->post('c')),
                            'blokir'=>$this->db->escape_str($this->input->post('h')),
                            'level'=>$this->db->escape_str($this->input->post('level')),
                            'id_session'=>md5($this->input->post('a')));
            $this->db->where('username',$this->input->post('id'));
            $this->db->update('users',$datadb);
        }
        */
    }

    function users_delete($id){
        return $this->db->query("DELETE FROM users where username='$id'");
    }

}