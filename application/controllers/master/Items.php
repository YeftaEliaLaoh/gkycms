<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Items extends CI_Controller {
	public function __construct()
	{
		parent::__construct();

		//$this->load->model('model_brand','brand');

		error_reporting(0);
		
	}
	
	function index(){
		
		cek_session_admin();
//		echo $this->session->token;
		
		$level = $this->session->level;
		$data['title'] = "Master Item";
		$data['column'] = array("No","Barcode","Kode Lama","Kategori","Lokasi Gereja","Title","Title Series","Author 1","Author 2","Author3","Publisher","Language","Item ID","Page","Harga","Tahun Beli","Data Enter","Date Published","Status Buku","Action");
		$data['add'] = "master/items/view_items";
		$data['modal_header'] = "Master Item";
		$data['list'] = "master/items/item_list";
		$id = 2;

		$edit = "<a class='btn btn-success btn-xs' title='Tambah Data' id='openkegiatan'  href='#' data-book-id='1' data-toggle='modal' data-target='#showmodalkegiatan' data-header='Edit Data' data-href='".base_url("master/costcentre/view_costcentre/$id")."'><i class='fas fa-edit'></i> Edit</a>";
		//$view = "<a class='btn btn-primary btn-xs' href='$url'>Stock</a>";

		$delete = "<a class='btn btn-danger btn-xs' id='deletekegiatan'  href='#' data-id='tbcc/$id' data-href='".base_url("api/cms/delete")."'><i class='fas fa-trash'></i> Delete</a>";

		$action = $edit.'&nbsp;'.$delete;
/*
		$list = "";
		for($i=1;$i<=10;$i++) {
			$list .= "<tr><td>$i</td><td>570-123121</td><td>XXA-123</td><td>X123</td><td>Setia sampai mana</td><td>Kisah yang tidak ada habisnya</td><td>Test123</td>
			<td>test author 2</td><td>test autor 3</td><td>Publisher 3</td><td>Indonesia</td><td>Item test</td><td>123</td><td>19.000</td><td>2012</td><td>2013</td><td>2013</td><td>$action</td></tr>";
		}

		$data['list'] = $list;
		*/

		if($level == 'admin' || $level == 'user' || $level == 'owner') {
			$this->template->load('administrator/template','administrator/mod_global/view_global',$data);
		} else {
			redirect(base_url());
		}
		
	}

	function item_list() {

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
		CURLOPT_URL => URL_API."admin/items?perpage=$limit&page=$page",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => array('perpage' => $limit,'page ' => $page,"like"=>$search),
		CURLOPT_HTTPHEADER => array(
			'token: '.$token
		),
		));

		$response = curl_exec($curl);

		curl_close($curl);

		$data_list = json_decode($response);
		$dt = $data_list->data;
		$dat = $dt->data;
		$curr = $dt->current_page;
		//$data_list->total = 100;

		$data = array();
		//$no = $fr-1;
		$no = ($page-1)*15;
		foreach($dat as $l) {
			$no++;
			$row = array();
			$id = $l->id;

			$au = $l->author;
			$aut = "";
			$author1 = "";
			$author2 = "";
			$author3 = "";

			if(!empty($au)) {
				foreach($au as $ac) {
					$aut .= $ac->name.';';
				}
				
				$au = explode(";",$aut);
				$author1 = $au[0];
				$author2 = $au[1];
				$author3 = $au[2];
				
	
			}

			$ll = json_encode($l);
			$bb = base64_encode($ll);

			if($l->isrent == "false") {
				$isrent = "Tidak Dipinjam";
			} else {
				$isrent = "Dipinjam";
			}

			$generatebarcode = "";
			if(!empty($l->barcode)) {
				$url_barcode = base_url("master/items/generatebarcode/$l->barcode");
				$generatebarcode = "<a class='btn btn-success btn-xs' target='_blank' href='$url_barcode'>Generate</a>";
			}

			$row[] = $no;
			$row[] = $l->barcode.$generatebarcode;
			$row[] = $l->kodelama;
			$row[] = $l->category_name;
			$row[] = $l->location_name;
			$row[] = $l->title;
			$row[] = $l->titleseries;
			$row[] = $author1;
			$row[] = $author2;
			$row[] = $author3;
			$row[] = $l->book_publisher;
			$row[] = $l->book_language;
			$row[] = $l->book_id;
			$row[] = "";
			$row[] = "";
			$row[] = $l->yearpublish;
			$row[] = "";
			$row[] = "";
			$row[] = $isrent;

			$edit = "<a class='btn btn-success btn-xs' title='Edit Data' id='openkegiatan'  href='#' data-book-id='1' data-toggle='modal' data-target='#showmodalkegiatan' data-header='Edit Data' data-href='".base_url("master/items/view_items/?data=$bb")."'><i class='fas fa-edit'></i> Edit</a>";
			$delete = "<a class='btn btn-danger btn-xs' id='deletekegiatan'  href='#' data-id='admin/items/delete?id=$id' data-href='".base_url("api/cms/delete")."'><i class='fas fa-trash'></i> Delete</a>";

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


	function view_items() {
		$data = array();
		//echo $this->session->token;
		//$id = $this->uri->segment(4);
		$id = $this->input->get("data");

//		echo $id;
		$token = $this->session->token;
		if(!empty($id)) {
			//var_dump("tes1");
			$data['submit_name'] = "Update";
			$data['act'] = "update";
			$jj = base64_decode($id);
			$r = json_decode($jj);
//			var_dump($r);
			$idd = $r->id;
			$data['id'] = $idd;
			
			$data['tbl'] = "admin/items/update?id=$idd";	

			$data['column'][] = array("nama"=>"Barcode","type"=>"number","display_none"=>1,"value"=>$r->barcode,"placeholder"=>"","pst_name"=>"barcode","id"=>"barcode","col"=>"6","required"=>"");
			$data['column'][] = array("nama"=>"Kode Lama","type"=>"text","display_none"=>1,"value"=>@$r->kodelama,"placeholder"=>"","pst_name"=>"kodelama","id"=>"kodelama","col"=>"6","required"=>"");
			$data['column'][] = array("nama"=>"Title","type"=>"text","display_none"=>1,"value"=>@$r->book_id,"placeholder"=>"","pst_name"=>"idbook","id"=>"idbook","col"=>"6","required"=>"");
			
		} else {

			$data['submit_name'] = "Save";
			$data['act'] = "save";
			$data['id'] = "";
			$data['row'] = "";
			$r = "";

			$data['tbl'] = "admin/items/add";

			
			$curl2 = curl_init();

			curl_setopt_array($curl2, array(
			CURLOPT_URL => URL_API.'admin/books_for_option',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			//CURLOPT_POSTFIELDS => array('perpage' => 1000,'page ' => 1),
			CURLOPT_HTTPHEADER => array(
				"token: $token"
			),
			));

			$response2 = curl_exec($curl2);
			//var_dump($response2);

			curl_close($curl2);

//			var_dump($response2);

			$data_list2 = json_decode($response2);
			$ot = $data_list2->data;
			//$opt6 = $ot->data;
			$option6 = option_builder3($ot,$r->book_id,0);

			$data['column'][] = array("nama"=>"Title","type"=>"select2","value"=>"","placeholder"=>"","pst_name"=>"idbook","id"=>"idbook","col"=>"6","required"=>"required","option"=>$option6);
		
			$data['column'][] = array("nama"=>"Qty","type"=>"text","value"=>@$r->qty,"placeholder"=>"","pst_name"=>"qty","id"=>"qty","col"=>"6","required"=>"required");

		}
		$data['post'] = "crud_global";
		
		$username = $this->session->username;
		$user_id = $this->session->id;

		//Location

		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => URL_API."admin/location/list",
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

		//echo $r->idlocation.' '.$r->idbook;
		//var_dump($r);

		$data_list = json_decode($response);
		$opt5 = $data_list->data;

		$option5 = option_builder4($opt5,$r->location_name,0);

		


		$data['column'][] = array("nama"=>"Lokasi Gereja","type"=>"select2","value"=>"","placeholder"=>"","pst_name"=>"idlocation","id"=>"idlocation","col"=>"12","required"=>"required","option"=>$option5);

        //$data['column'][] = array("nama"=>"ID Location","type"=>"text","value"=>@$r->idlocation,"placeholder"=>"","pst_name"=>"idlocation","id"=>"idlocation","col"=>"6","required"=>"required");


		$this->load->view('modal/global',$data);
	}

	private function set_barcode($code)
	{
		// Load library
		$this->load->library('zend');
		// Load in folder Zend
		$this->zend->load('Zend/Barcode');
		// Generate barcode
		Zend_Barcode::render('code128', 'image', array('text'=>$code), array());
	}

	function generatebarcode($val) {
		$this->set_barcode($val);
	}



}
