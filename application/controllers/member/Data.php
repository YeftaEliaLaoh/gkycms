<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Data extends CI_Controller {
	public function __construct()
	{
		parent::__construct();

		//$this->load->model('model_brand','brand');

		error_reporting(0);
		
	}
	
	function index(){
		
		cek_session_admin();
		
		$level = $this->session->level;
		$data['title'] = "Data Member";
		$data['column'] = array("No Anggota Jemaat","ID Member Jemaat","Nama","Tanggal Lahir","Alamat Rumah","Kode Pos","No Telepon","Kelas","Status","Tgl Aktivasi Member","Tgl Expired Member","Kebaktian","Action");
	//	$data['add'] = "master/home/view_home";
		$data['modal_header'] = "List Member";
		$data['list'] = "member/data/data_list";
		$id = 2;

		$edit = "<a class='btn btn-success btn-xs' title='Tambah Data' id='openkegiatan'  href='#' data-book-id='1' data-toggle='modal' data-target='#showmodalkegiatan' data-header='Edit Data' data-href='".base_url("master/costcentre/view_costcentre/$id")."'><i class='fas fa-edit'></i> Edit</a>";
		//$view = "<a class='btn btn-primary btn-xs' href='$url'>Stock</a>";

		$delete = "<a class='btn btn-danger btn-xs' id='deletekegiatan'  href='#' data-id='tbcc/$id' data-href='".base_url("api/cms/delete")."'><i class='fas fa-trash'></i> Delete</a>";

		$action = $edit.'&nbsp;'.$delete;
/*
		$list = "";
		for($i=1;$i<=10;$i++) {
			$list .= "<tr><td>GKY1234</td><td>570-123121</td><td>Steven Hart</td><td>21 Januari 1982</td><td>Jl. Test1 412 123</td><td>11243</td><td>5123131</td><td>1B</td><td>Active</td><td>$action</td></tr>";
		}

		$data['list'] = $list;
		*/
		if($level == 'admin' || $level == 'user' || $level == 'owner') {
			$this->template->load('administrator/template','administrator/mod_global/view_global',$data);
		} else {
			redirect(base_url());
		}
		
	}

	function data_list() {

		$token = $this->session->token;
		$curl = curl_init();
		$limit = 20;
		$start = $_POST['start'];
		$search = $_POST['search']['value'];

	//		$page = $_POST['length']/$limit;
		if(empty($start)) {
			$page = 1;
		} else {
			$page = $start+1;
		}
		
		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => URL_API."admin/member?perpage=$limit&page=$page",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => array("like"=>$search),
		CURLOPT_HTTPHEADER => array(
			'token: '.$token
		),
		));

		$response = curl_exec($curl);

		curl_close($curl);

		$data_list = json_decode($response);
		$dt = $data_list->data;
		$dat = $dt->data;

		$data = array();

		foreach($dat as $l) {
			$no++;
			$row = array();
			$id = $l->id;
			$user_id = trim($l->user_id);

			$row[] = $l->nomor_anggota;
			$row[] = $l->id_member;
			$row[] = $l->nama_anggota;
			$row[] = tgl_view($l->tanggal_lahir);
			$row[] = $l->alamat;
			$row[] = $l->kode_pos;
			$row[] = $l->phone_number;
			$row[] = $l->kelas;
			$row[] = $l->status;
			$row[] = tgl_view($l->memberstart);
			$row[] = tgl_view($l->memberend);
			$row[] = $l->kebaktian;

			$ll = json_encode($l);
			$bb = base64_encode($ll);

			$delete = "<a class='btn btn-danger btn-xs' id='deletekegiatan' href='#' data-id='admin/member/delete_user?user_id=$user_id' data-href='".base_url("api/cms/delete")."'><i class='fas fa-trash'></i> Delete</a>";
			$nonaktif = "<a class='btn btn-warning btn-xs' id='nonaktifkegiatan' href='#' data-id='admin/member/delete?id=$id' data-href='".base_url("api/cms/nonaktif")."'><i class='fas fa-edit'></i> Non Aktif</a>";

			$action = "<div style='width:150px;float:left;'>".$delete.'&nbsp;'.$nonaktif."</div>";

			$row[] = $action;

			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $dt->total,
						"recordsFiltered" => $dt->total,
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}


	function view_home() {
		$data = array();
		$id = $this->uri->segment(4);
		if(!empty($id)) {
			$data['submit_name'] = "Update";
			$data['act'] = "update";
			
			$data['id'] = $id;
			$r = $this->home->home_edit($id)->row();
			$data['row'] = $r;

			$getkode = $r->kodecc;
			
		} else {
			$data['submit_name'] = "Save";
			$data['act'] = "save";
			$data['id'] = "";
			$data['row'] = "";
			$r = "";

			$getkode = getkode_cc();
		}
		$data['post'] = "crud_global";
		$data['tbl'] = "tbcc";
		
		$username = $this->session->username;
		$user_id = $this->session->id;

		$opt2 = $this->costcentre->kategori_all();
		$opt_kategori = option_builder($opt2,$r->kategori,0);

		$data['column'][] = array("nama"=>"Kode Cost Centre","type"=>"readonly","value"=>$getkode,"placeholder"=>"","pst_name"=>"kodecc","id"=>"kodecc","col"=>"6");
		$data['column'][] = array("nama"=>"Nama Departemen","type"=>"text","value"=>@$r->namadep,"placeholder"=>"Input Nama Depart","pst_name"=>"namadep","id"=>"namadep","col"=>"6","required"=>"required");
        $data['column'][] = array("nama"=>"Kode Biaya","type"=>"text","value"=>@$r->kodebiaya,"placeholder"=>"Input Kode Biaya","pst_name"=>"kodebiaya","id"=>"kodebiaya","col"=>"6","required"=>"required");
        $data['column'][] = array("nama"=>"Kategori","type"=>"option","value"=>"","placeholder"=>"","pst_name"=>"kategori","id"=>"kategori","col"=>"6","option"=>$opt_kategori);
        $data['column'][] = array("nama"=>"Keterangan","type"=>"textarea","value"=>@$r->ket,"placeholder"=>"","pst_name"=>"ket","id"=>"ket","col"=>"12");
		$data['column'][] = array("nama"=>"user","type"=>"addon","value"=>"$username","placeholder"=>"","pst_name"=>"user","id"=>"user","col"=>"3");
        
		$this->load->view('modal/global',$data);
	}



}
