<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Admin extends CI_Controller {
	public function __construct()
	{
		parent::__construct();

		//$this->load->model('model_brand','brand');

		error_reporting(0);
		
	}
/*
	function index(){
		
		cek_session_admin();
		$data['title'] = "Dashboard";
		$level = $this->session->level;
		
		$data['option_store'] = array();
        $this->template->load('administrator/template','administrator/view_home',$data);
        
	}
	*/
	
	function index(){
		
		cek_session_admin();
		
		$level = $this->session->level;
		$data['title'] = "Admin GKY";
		$data['column'] = array("Username","Action");
		$data['add'] = "admin/view_admin";
		$data['modal_header'] = "List Admin";
		$data['list'] = "admin/admin_list";
/*
		$id = 2;

		$edit = "<a class='btn btn-success btn-xs' title='Tambah Data' id='openkegiatan'  href='#' data-book-id='1' data-toggle='modal' data-target='#showmodalkegiatan' data-header='Edit Data' data-href='".base_url("master/costcentre/view_costcentre/$id")."'><i class='fas fa-edit'></i> Edit</a>";
		//$view = "<a class='btn btn-primary btn-xs' href='$url'>Stock</a>";

		$delete = "<a class='btn btn-danger btn-xs' id='deletekegiatan'  href='#' data-id='tbcc/$id' data-href='".base_url("api/cms/delete")."'><i class='fas fa-trash'></i> Delete</a>";

		$action = $edit.'&nbsp;'.$delete;

		$list = "<tr><td>Testing123</td><td>Admin1</td><td></td><td></td><td>$action</td></tr>
		<tr><td>Testing123</td><td>Admin2</td><td></td><td></td><td>$action</td></tr>
		<tr><td>Testing123</td><td>Admin3</td><td></td><td></td><td>$action</td></tr>";
		$data['list'] = $list;
		*/
		if($level == 'admin' || $level == 'user' || $level == 'owner') {
			$this->template->load('administrator/template','administrator/mod_global/view_global',$data);
		} else {
			redirect(base_url());
		}
		
	}

	function admin_list() {
		/*
		$list[] = (object)array("username"=>"admin1","nama"=>"Admin 1","id"=>1);
		$list[] = (object)array("username"=>"admin2","nama"=>"Admin 2","id"=>2);
		*/

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
		CURLOPT_URL => URL_API."admin/list",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_HTTPHEADER => array(
			'token: '.$token
		),
		));

		$response = curl_exec($curl);

		curl_close($curl);

		$data_list = json_decode($response);
		$dat = $data_list->data;

		foreach($dat as $l) {
			$no++;
			$row = array();
			$row[] = $l->username;

			$id = $l->id;
			$ll = json_encode($l);
			$bb = base64_encode($ll);

			$edit = "<a class='btn btn-success btn-xs' title='Tambah Data' id='openkegiatan'  href='#' data-book-id='1' data-toggle='modal' data-target='#showmodalkegiatan' data-header='Edit Data' data-href='".base_url("admin/view_admin?data=$bb")."'><i class='fas fa-edit'></i> Edit</a>";
			//$view = "<a class='btn btn-primary btn-xs' href='$url'>Stock</a>";

			$delete = "<a class='btn btn-danger btn-xs' id='deletekegiatan'  href='#' data-id='$id' data-href='".base_url("api/cms/delete_admin?id=$id")."'><i class='fas fa-trash'></i> Delete</a>";

			$action = $edit.'&nbsp;'.$delete;

			$row[] = $action;

			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $no,
						"recordsFiltered" => $no,
						"data" => $data,
				);
		//output to json format
		echo json_encode($output);
	}



	function view_admin() {
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

			//$idd = $r->book_id;
			$data['id'] = $r->id;
			
			$data['tbl'] = "admin/edit";
			
		} else {
			$data['submit_name'] = "Save";
			$data['act'] = "save";
			$data['id'] = "";
			$data['row'] = "";
			$r = "";

			$data['tbl'] = "admin/registration";

		}
		$data['post'] = "crud_global";
		
		$username = $this->session->username;
		$user_id = $this->session->id;

		$data['column'][] = array("nama"=>"Username","type"=>"text","value"=>$r->username,"placeholder"=>"","pst_name"=>"username","id"=>"username","col"=>"6","required"=>"required");
		$data['column'][] = array("nama"=>"Password","type"=>"password","value"=>"","placeholder"=>"","pst_name"=>"password","id"=>"password","col"=>"6","required"=>"required");
		$data['column'][] = array("nama"=>"Confirm Password","type"=>"password","value"=>"","placeholder"=>"","pst_name"=>"password_confirmation","id"=>"password_confirmation","col"=>"6","required"=>"required");

		$this->load->view('modal/global',$data);
	}



}
