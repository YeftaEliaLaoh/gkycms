<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Kategori extends CI_Controller {
	public function __construct()
	{
		parent::__construct();

		//$this->load->model('model_brand','brand');

		error_reporting(0);
		
	}
	
	function index(){
		
		cek_session_admin();
		
		$level = $this->session->level;
		$data['title'] = "Kategori";
		$data['column'] = array("No","Parent Kategori","Kode Kategori","Nama Kategori","Action");
		$data['add'] = "master/kategori/view_kategori";
		$data['modal_header'] = "Kategori";
		$data['list'] = "master/kategori/kategori_list";
		$data['parent'] = "master/parentkategori";
		$id = 2;


		if($level == 'admin' || $level == 'user' || $level == 'owner') {
			$this->template->load('administrator/template','administrator/mod_global/view_global',$data);
		} else {
			redirect(base_url());
		}
		
	}

	function testkategori() {
		$limit = 10;
		$start = $_POST['start'];
		$search = $_POST['search']['value'];

		$page = round($_POST['start']/$limit);
		if(empty($start)) {
			$page = 1;
		} else {
			$page = $page+1;
		}
		$token = $this->session->token;

		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => URL_API."admin/category/list?perpage=$limit&page=$page",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => array('perpage' => $limit,'page ' => $page),
		CURLOPT_HTTPHEADER => array(
			'token: '.$token
		),
		));

		$response = curl_exec($curl);

		curl_close($curl);

		var_dump($response);

		$data_list = json_decode($response);
		$dt = $data_list->data;
		$dat = $dt->data;
	}

	function kategori_list() {

		$token = $this->session->token;
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
		CURLOPT_URL => URL_API."admin/category/list?perpage=$limit&page=$page",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => array('perpage' => $limit,'page ' => $page,'like'=>$search),
		CURLOPT_HTTPHEADER => array(
			'token: '.$token
		),
		));

		$response = curl_exec($curl);

		//var_dump($response);

		curl_close($curl);

		$data_list = json_decode($response);
		$dt = $data_list->data;
		$dat = $dt->data;
		//$dt->total = 100;

		$data = array();
		//$no = $fr-1;
		$no = ($page-1)*15;
		foreach($dat as $l) {
			$no++;
			$row = array();
			$id = $l->id;

			$ll = json_encode($l);
			$bb = base64_encode($ll);

			$row[] = $no;
			$row[] = $l->parent_name;
			$row[] = $l->code;
			$row[] = $l->fullname;

			$edit = "<a class='btn btn-success btn-xs' title='Edit Data' id='openkegiatan'  href='#' data-book-id='1' data-toggle='modal' data-target='#showmodalkegiatan' data-header='Edit Data' data-href='".base_url("master/kategori/view_kategori/?data=$bb")."'><i class='fas fa-edit'></i> Edit</a>";
			$delete = "<a class='btn btn-danger btn-xs' id='deletekegiatan'  href='#' data-id='admin/category/delete?id=$id' data-href='".base_url("api/cms/delete")."'><i class='fas fa-trash'></i> Delete</a>";

			$action = $edit.'&nbsp;'.$delete;

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


	function view_kategori() {
		$data = array();
		//$id = $this->uri->segment(4);
		$id = $this->input->get("data");
		$token = $this->session->token;
		if(!empty($id)) {
			$data['submit_name'] = "Update";
			$data['act'] = "update";
			$jj = base64_decode($id);
			$r = json_decode($jj);
			//var_dump($r);
			$idd = $r->id;
			$data['id'] = $idd;
			
			$data['tbl'] = "admin/category/update";
			
		} else {
			$data['submit_name'] = "Save";
			$data['act'] = "save";
			$data['id'] = "";
			$data['row'] = "";
			$r = "";

			$data['tbl'] = "admin/category/add";

		}
		$data['r'] = $r;
		$data['post'] = "crud_category";
		
		$username = $this->session->username;
		$user_id = $this->session->id;

		//Parent
		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => URL_API."admin/parent_category/list",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_HTTPHEADER => array(
			"token: $token"
		),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		//echo $response;


		$data_list = json_decode($response);
		$opt5 = $data_list->data;

		$option5 = option_builder($opt5,$r->parent_id,0);

		$data['column'][] = array("nama"=>"Parent Kategori","type"=>"select2","value"=>"","placeholder"=>"","pst_name"=>"parent_id","id"=>"parent_id","col"=>"6","required"=>"required","option"=>$option5);
		$data['column'][] = array("nama"=>"Kode Kategori","type"=>"text","value"=>$r->code,"placeholder"=>"","pst_name"=>"kode","id"=>"kode","col"=>"6","required"=>"required");
		$data['column'][] = array("nama"=>"Nama Kategori","type"=>"text","value"=>@$r->fullname,"placeholder"=>"","pst_name"=>"fullname","id"=>"fullname","col"=>"12","required"=>"required");



		$this->load->view('modal/global',$data);
	}



}