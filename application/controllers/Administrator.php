<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Administrator extends CI_Controller {
	public function __construct()
	{
		parent::__construct();
		
		//$this->load->model('login_model',"login");
		// Load session library
		$this->load->library('session');

		error_reporting(0);
		
	}

	function index(){

		
		$session_set_value = $this->session->all_userdata();
		if (isset($session_set_value['remember_me']) && $session_set_value['remember_me'] == "1" && $this->session->level != '') {
			//$this->load->view('admin_page');
			redirect('home');
		} else {
			if ($this->session->level != ''){
				redirect('home');
			} else{
				$data['title'] = 'Administrator &rsaquo; Log In';
				$this->load->view('administrator/view_login',$data);
			}
		}
	}

	function forgot(){
		
		if ($this->session->level != ''){
			redirect('home');
		}else{
			$data['title'] = 'Administrator &rsaquo; Log In';
			$this->load->view('administrator/view_forgot',$data);
		}
	}

	public function resetpw($token,$userid) 
	{
		$userid = intval($userid);
		// Check
		$user = $this->login_admin_model->getResetUser($token, $userid)->row();
		if(!empty($user)) {
			$data['user'] = $user;
		} else {
			$data['user'] = "";
		}

		$this->load->view('administrator/view_recover',$data);
		/*
		var_dump($user);
		echo 'door';
*/		
//		$this->load->view('administrator/view_recover',$data);

	}

	public function resetpw_user($token,$userid) 
	{
		$userid = intval($userid);
		// Check
		$user = $this->login->getResetUser($token, $userid)->row();
		if(!empty($user)) {
			$data['user'] = $user;
			
			$data['token'] = $token;
		} else {
			$data['user'] = "";
			
			$data['token'] = "";
		}


	//	var_dump($user);

		$this->load->view('administrator/view_recover_user',$data);
		/*
		var_dump($user);
		echo 'door';
*/		
//		$this->load->view('administrator/view_recover',$data);

	}

	public function kota()
	{
		$p = $this->input->post('provinsi');
		//$type = $this->input->get('type');
		$type = 1;
		$kota = $this->model_utama->show_kota($p)->result_array();
		$data = "";
		if(!empty($kota)) {
			$data = "<option value=''>Pilih Kota</option>";
			foreach($kota as $r) {
				$data .= "<option value='".$r['id']."'>".$r['nama_kabkota']."</option>";
			}
		}
		$callback = array('data_kota'=>$data); // Masukan variabel html tadi ke dalam array $callback dengan index array : data_kota

		echo json_encode($callback);
	}

	public function kecamatan()
	{
		$p = $this->input->post('kota');
		//$type = $this->input->get('type');
		$type = 1;
		$kecamatan = $this->model_utama->show_kecamatan($p)->result_array();
		$data = "";
		if(!empty($kecamatan)) {
			$data = "<option value=''>Pilih Kecamatan</option>";
			foreach($kecamatan as $r) {
				$data .= "<option value='".$r['id']."'>".$r['nama_kecamatan']."</option>";
			}
		}
		$callback = array('data_kecamatan'=>$data); // Masukan variabel html tadi ke dalam array $callback dengan index array : data_kota

		echo json_encode($callback);
	}

	public function kelurahan()
	{
		$p = $this->input->post('kecamatan');
		//$type = $this->input->get('type');
		$type = 1;
		$kecamatan = $this->model_utama->show_kelurahan($p)->result_array();
		$data = "";
		if(!empty($kecamatan)) {
			$data = "<option value=''>Pilih Kelurahan / Desa</option>";
			foreach($kecamatan as $r) {
				$data .= "<option value='".$r['id']."'>".$r['nama_kelurahan']."</option>";
			}
		}
		$callback = array('data_kelurahan'=>$data); // Masukan variabel html tadi ke dalam array $callback dengan index array : data_kota

		echo json_encode($callback);
	}

	function logout(){
		$this->session->sess_destroy();
		redirect(base_url());
	}


}
