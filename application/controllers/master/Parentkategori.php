<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Parentkategori extends CI_Controller {
	public function __construct()
	{
		parent::__construct();

		//$this->load->model('model_brand','brand');

		error_reporting(0);
		
	}
	
	function index(){
		
		cek_session_admin();
		
		$level = $this->session->level;
		$data['title'] = "Parent Category";
		$data['column'] = array("No","Nama","Action");
		$data['add'] = "master/parentkategori/view_parent";
		$data['modal_header'] = "parent";
		$data['list'] = "master/parentkategori/parent_list";
		$id = 2;


		if($level == 'admin' || $level == 'user' || $level == 'owner') {
			$this->template->load('administrator/template','administrator/mod_global/view_global',$data);
		} else {
			redirect(base_url());
		}
		
	}

	function parent_list() {

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
		CURLOPT_URL => URL_API."admin/parent_category/list?perpage=$limit&page=$page",
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

//		var_dump($response);

		curl_close($curl);

		$data_list = json_decode($response);
		$dt = $data_list->data;
		//$dat = $dt->data;
		//$dt->total = 100;

		$data = array();
		//$no = $fr-1;
		$no = ($page-1)*15;
		foreach($dt as $l) {
			$no++;
			$row = array();
			$id = $l->id;

			$ll = json_encode($l);
			$bb = base64_encode($ll);

			$row[] = $no;
			$row[] = $l->name;

			$edit = "<a class='btn btn-success btn-xs' title='Edit Data' id='openkegiatan'  href='#' data-book-id='1' data-toggle='modal' data-target='#showmodalkegiatan' data-header='Edit Data' data-href='".base_url("master/parentkategori/view_parent/?data=$bb")."'><i class='fas fa-edit'></i> Edit</a>";
			$delete = "<a class='btn btn-danger btn-xs' id='deletekegiatan'  href='#' data-id='admin/parent_category/delete?id=$id' data-href='".base_url("api/cms/delete")."'><i class='fas fa-trash'></i> Delete</a>";

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


	function view_parent() {
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
			
			$data['tbl'] = "admin/parent_category/update";
			
		} else {
			$data['submit_name'] = "Save";
			$data['act'] = "save";
			$data['id'] = "";
			$data['row'] = "";
			$r = "";

			$data['tbl'] = "admin/parent_category/add";

		}
		$data['r'] = $r;
		$data['post'] = "crud_parentcategory";
		
		$username = $this->session->username;
		$user_id = $this->session->id;

		

//		$data['column'][] = array("nama"=>"Kode parent","type"=>"text","value"=>$r->code,"placeholder"=>"","pst_name"=>"kode","id"=>"kode","col"=>"12","required"=>"required");
		$data['column'][] = array("nama"=>"Nama Parent Kategori","type"=>"text","value"=>@$r->name,"placeholder"=>"","pst_name"=>"name","id"=>"name","col"=>"12","required"=>"required");

		


		$this->load->view('modal/global',$data);
	}



}
