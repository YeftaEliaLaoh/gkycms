<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Pinjam extends CI_Controller {
	public function __construct()
	{
		parent::__construct();

		//$this->load->model('model_brand','brand');

		error_reporting(0);
		
	}
	/*
	function index(){
		
		cek_session_admin();
		
		$level = $this->session->level;
		$data['title'] = "Transaksi Peminjaman";
		$data['column'] = array("ID Receipt","ID Member","Nama","Barcode Buku","Code Buku","Nama Buku","Tanggal Peminjaman","Sesi","Konfirmasi Peminjaman");
		$data['add'] = "master/home/view_home";
		$data['modal_header'] = "List CD";
//		$data['list'] = "master/home/home_list";
		$id = 2;

		$edit = "<a class='btn btn-success btn-xs' title='Tambah Data' id='openkegiatan'  href='#' data-book-id='1' data-toggle='modal' data-target='#showmodalkegiatan' data-header='Edit Data' data-href='".base_url("master/costcentre/view_costcentre/$id")."'><i class='fas fa-edit'></i> Approve</a>";
		//$view = "<a class='btn btn-primary btn-xs' href='$url'>Stock</a>";

		$delete = "<a class='btn btn-danger btn-xs' id='deletekegiatan'  href='#' data-id='tbcc/$id' data-href='".base_url("api/cms/delete")."'><i class='fas fa-trash'></i> Reject</a>";

		$action = $edit.'&nbsp;'.$delete;

		$list = "";
		for($i=1;$i<=10;$i++) {
			$list .= "<tr><td>111231231</td><td>GKY12312</td><td>Steven Hart</td><td>43123131</td><td>ISBN1231321</td><td>Buku 1</td><td>21/01/2022</td><td>Sesi $i</td><td>$action</td></tr>";
		}

		$data['list'] = $list;
		if($level == 'admin' || $level == 'user' || $level == 'owner') {
			$this->template->load('administrator/template','administrator/mod_global/view_admin',$data);
		} else {
			redirect(base_url());
		}
		
	}
	*/

	function index(){
		
		cek_session_admin();
		
		$level = $this->session->level;
		$data['title'] = "List Pinjam";
		$data['column'] = array("ID Receipt","ID Member","Nama","Barcode Buku","Code Buku","Nama Buku","Tanggal Peminjaman","Tanggal Pengembalian","Sesi","Konfirmasi Peminjaman");
		$data['add'] = "";
		$data['modal_header'] = "List Peminjaman";
		$data['list'] = "transaksi/pinjam/pinjam_list";
		$id = 2;


		if($level == 'admin' || $level == 'user' || $level == 'owner') {
			$this->template->load('administrator/template','administrator/mod_global/view_global',$data);
		} else {
			redirect(base_url());
		}
		
	}

	function approve(){
		
		cek_session_admin();
		
		$level = $this->session->level;
		$data['title'] = "List Pinjam";
		$data['column'] = array("ID Receipt","ID Member","Nama","Barcode Buku","Code Buku","Nama Buku","Tanggal Peminjaman","Sesi","Konfirmasi Peminjaman");
		$data['add'] = "";
		$data['modal_header'] = "List Peminjaman";
		$data['list'] = "transaksi/pinjam/approve_list";
		$id = 2;


		if($level == 'admin' || $level == 'user' || $level == 'owner') {
			$this->template->load('administrator/template','administrator/mod_global/view_global',$data);
		} else {
			redirect(base_url());
		}
		
	}

	function pinjam_list() {

		$token = $this->session->token;
		//$token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwidXNlcm5hbWUiOiJhdHV5cGVydGFtYSIsInJvbGUiOiJhZG1pbiIsImNyZWF0ZWRfZGF0ZSI6MTY0NDMxMzQ1MSwiZXhwaXJlZF9kYXRlIjoxNjUzMzEzNDUxfQ.2FCaSx2IY5ZYHu0X8QVayaX6u-MEk98Cq3xsoqbdQ-0";
		$curl = curl_init();
		$limit = 10;
		$start = $_POST['start'];
		$search = $_POST['search']['value'];

		$page = round($_POST['start']/$limit);
		if(empty($start)) {
			$page = 1;
		} else {
			$page = $page+1;
		}
		
		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => URL_API."admin/rent/list",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => array('order' => "desc","like"=>$search),
		CURLOPT_HTTPHEADER => array(
			'token: '.$token
		),
		));

		$response = curl_exec($curl);

		curl_close($curl);

		$data_list = json_decode($response);
		$dt = $data_list->data;
		$dat = $dt->data;
	//	$dat = $dt->data;
		//$curr = $dt->current_page;
		//$data_list->total = 100;
		//$dt->total = 100;
		$data = array();
		//$no = $fr-1;
		$no = ($page-1)*15;
		foreach($dat as $l) {
			$no++;
			$row = array();
			$id = $l->rent_id;
			$user_id = $l->user_id;


			$ll = json_encode($l);
			$bb = base64_encode($ll);

			//$data['column'] = array("ID Receipt","ID Member","Nama","Barcode Buku","Code Buku","Nama Buku","Tanggal Peminjaman","Sesi","Konfirmasi Peminjaman");

			$row[] = $l->booking_id;
			$row[] = $l->code;
			$row[] = $l->nama_anggota;
			$row[] = $l->barcode;
			$row[] = $l->code;
			$row[] = $l->title;

			$row[] = tgl_view($l->rent_date);
			$row[] = tgl_view($l->returndate);
			$row[] = $l->name;

			//$approve = "&nbsp;<a class='btn btn-info btn-xs' id='deletekegiatan'  href='#' data-id='$id' data-href='".base_url("api/cms/pinjam_approve?id=$id")."'>Approve</a>";
			//$cancel = "&nbsp;<a class='btn btn-danger btn-xs' id='deletekegiatan'  href='#' data-id='$id' data-href='".base_url("api/cms/pinjam_cancel?id=$id")."'>Cancel</a>";
			$return = "&nbsp;<a class='btn btn-warning btn-xs' id='deletekegiatan'  href='#' data-id='$id' data-href='".base_url("api/cms/pinjam_return?id=$id")."'>Return</a>";
			$approve = "";
			$cancel = "";
			//$notif = "&nbsp;<a class='btn btn-info btn-xs' id='deletekegiatan'  href='#' data-id='$id' data-href='".base_url("api/cms/notif?user_id=$user_id&id=$id")."'>Notif</a>";

			//$action = "<div style='width:120px;float:left;'>".$return.$notif."</div>";
			$action = "<div style='width:120px;float:left;'>".$return."</div>";

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

	
	function approve_list() {

		$token = $this->session->token;
		//$token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6MSwidXNlcm5hbWUiOiJhdHV5cGVydGFtYSIsInJvbGUiOiJhZG1pbiIsImNyZWF0ZWRfZGF0ZSI6MTY0NDMxMzQ1MSwiZXhwaXJlZF9kYXRlIjoxNjUzMzEzNDUxfQ.2FCaSx2IY5ZYHu0X8QVayaX6u-MEk98Cq3xsoqbdQ-0";
		$curl = curl_init();
		$limit = 10;
		$start = $_POST['start'];
		$search = $_POST['search']['value'];

		$page = round($_POST['start']/$limit);
		if(empty($start)) {
			$page = 1;
		} else {
			$page = $page+1;
		}
		
		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => URL_API."admin/rent/list_approval?perpage=$limit&page=$page",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => array('order' => "desc","like"=>$search),
		CURLOPT_HTTPHEADER => array(
			'token: '.$token
		),
		));

		$response = curl_exec($curl);

		curl_close($curl);

		$data_list = json_decode($response);
		$dt = $data_list->data;
		$dat = $dt->data;
	//	$dat = $dt->data;
		//$curr = $dt->current_page;
		//$data_list->total = 100;
		//$dt->total = 100;
		$data = array();
		//$no = $fr-1;
		$no = ($page-1)*15;
		foreach($dat as $l) {
			$no++;
			$row = array();
			$id = $l->booking_id;


			$ll = json_encode($l);
			$bb = base64_encode($ll);

			//$data['column'] = array("ID Receipt","ID Member","Nama","Barcode Buku","Code Buku","Nama Buku","Tanggal Peminjaman","Sesi","Konfirmasi Peminjaman");

			$row[] = $l->booking_id;
			$row[] = $l->code;
			$row[] = $l->nama_anggota;
			$row[] = $l->barcode;
			$row[] = $l->code;
			$row[] = $l->title;

			$row[] = tgl_view($l->tanggal_peminjaman);
			$row[] = $l->name;

			$approve = "&nbsp;<a class='btn btn-info btn-xs' id='deletekegiatan'  href='#' data-id='$id' data-href='".base_url("api/cms/pinjam_approve?id=$id")."'>Approve</a>";
			$cancel = "&nbsp;<a class='btn btn-danger btn-xs' id='deletekegiatan'  href='#' data-id='$id' data-href='".base_url("api/cms/pinjam_cancel?id=$id")."'>Cancel</a>";
			//$return = "&nbsp;<a class='btn btn-warning btn-xs' id='deletekegiatan'  href='#' data-id='$id' data-href='".base_url("api/cms/pinjam_return?id=$id")."'>Return</a>";
			$return = "";
			
			$action = $approve.$cancel.$return;

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




}
