<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Cd extends CI_Controller {
	public function __construct()
	{
		parent::__construct();

		//$this->load->model('model_brand','brand');

		error_reporting(0);
		
	}
	
	function index(){
		
		cek_session_admin();
		
		$level = $this->session->level;
		$data['title'] = "List CD";
		$data['column'] = array("No","Barcode","Kode Lama","Kategori","Title","Title Series","Author 1","Author 2","Author3","Publisher","Language","Item ID","Page","Harga","Tahun Beli","Date Enter","Date Published","Action");
		$data['add'] = "master/home/view_home";
		$data['modal_header'] = "List CD";
//		$data['list'] = "master/home/home_list";
		$id = 2;

		$edit = "<a class='btn btn-success btn-xs' title='Tambah Data' id='openkegiatan'  href='#' data-book-id='1' data-toggle='modal' data-target='#showmodalkegiatan' data-header='Edit Data' data-href='".base_url("master/costcentre/view_costcentre/$id")."'><i class='fas fa-edit'></i> Edit</a>";
		//$view = "<a class='btn btn-primary btn-xs' href='$url'>Stock</a>";

		$delete = "<a class='btn btn-danger btn-xs' id='deletekegiatan'  href='#' data-id='tbcc/$id' data-href='".base_url("api/cms/delete")."'><i class='fas fa-trash'></i> Delete</a>";

		$action = $edit.'&nbsp;'.$delete;

		$list = "";
		for($i=1;$i<=10;$i++) {
			$list .= "<tr><td>$i</td><td>570-123121</td><td>XXA-123</td><td>X123</td><td>Setia sampai mana</td><td>Kisah yang tidak ada habisnya</td><td>Test123</td>
			<td>test author 2</td><td>test autor 3</td><td>Publisher 3</td><td>Indonesia</td><td>Item test</td><td>123</td><td>19.000</td><td>2012</td><td>2013</td><td>2013</td><td>$action</td></tr>";
		}

		$data['list'] = $list;
		if($level == 'admin' || $level == 'user' || $level == 'owner') {
			$this->template->load('administrator/template','administrator/mod_global/view_admin',$data);
		} else {
			redirect(base_url());
		}
		
	}

	function home_list() {
		/*
		$list[] = (object)array("username"=>"admin1","nama"=>"Admin 1","id"=>1);
		$list[] = (object)array("username"=>"admin2","nama"=>"Admin 2","id"=>2);
		*/
		$list = $this->admin->get_datatables();
		$data = array();
		$no = $_POST['start'];
		
		foreach ($list as $l) {
			$no++;
			$row = array();
			$row[] = $l->username;
			$row[] = $l->nama;
			$row[] = "";
            $row[] = "";

			$id = $l->id;

			$edit = "<a class='btn btn-success btn-xs' title='Tambah Data' id='openkegiatan'  href='#' data-book-id='1' data-toggle='modal' data-target='#showmodalkegiatan' data-header='Edit Data' data-href='".base_url("master/costcentre/view_costcentre/$id")."'><i class='fas fa-edit'></i> Edit</a>";
			//$view = "<a class='btn btn-primary btn-xs' href='$url'>Stock</a>";

			$delete = "<a class='btn btn-danger btn-xs' id='deletekegiatan'  href='#' data-id='tbcc/$id' data-href='".base_url("api/cms/delete")."'><i class='fas fa-trash'></i> Delete</a>";

			$action = $edit.'&nbsp;'.$delete;

			$row[] = $action;

			$data[] = $row;
		}

		$output = array(
						"draw" => $_POST['draw'],
						"recordsTotal" => $this->admin->count_all(),
						"recordsFiltered" => $this->admin->count_filtered(),
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
