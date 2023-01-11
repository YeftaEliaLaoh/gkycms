<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Book extends CI_Controller {
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
		$data['title'] = "List Buku";
		$data['column'] = array("No","Barcode","Kode Lama","Kategori","Title","Title Series","Author 1","Author 2","Author3","Publisher","Language","Item ID","Page","Harga","Tahun Beli","Date Enter","Date Published","Action");
		$data['add'] = "master/home/view_home";
		$data['modal_header'] = "List Buku";
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

	*/
	
	function index(){
		
		cek_session_admin();
		
		$level = $this->session->level;
		$data['title'] = "Master Buku";
		$data['column'] = array("No","Book ID","Kategori","Title","Title Series","Author 1","Author 2","Author3","Publisher","Language","Tahun Beli",
		"Description","Image","Action");

		$data['add'] = "master/book/view_book";
		$data['show_modal'] = 1;
		$data['modal_header'] = "Master Buku";
		$data['list'] = "master/book/book_list";
		$id = 2;


		if($level == 'admin' || $level == 'user' || $level == 'owner') {
			$this->template->load('administrator/template','administrator/mod_global/view_global',$data);
		} else {
			redirect(base_url());
		}
		
	}

	function book_list() {

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
		CURLOPT_URL => URL_API."admin/books?perpage=$limit&page=$page",
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
			$id = $l->book_id;

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

			$row[] = $no;
			$row[] = $l->book_id;
			$row[] = $l->category_name;
			$row[] = $l->title;
			$row[] = $l->titleseries;
			$row[] = $author1;
			$row[] = $author2;
			$row[] = $author3;
			$row[] = $l->book_publisher;
			$row[] = $l->book_language;
			$row[] = $l->yearpublish;

			$edit = "<a class='btn btn-success btn-xs' title='Edit Data' id='openkegiatan2'  href='#' data-book-id='1' data-toggle='modal' data-target='#showmodalkegiatan2' data-header='Edit Data' data-href='".base_url("master/book/view_book/?data=$bb")."'><i class='fas fa-edit'></i> Edit</a>";
			$delete = "<a class='btn btn-danger btn-xs' id='deletekegiatan'  href='#' data-id='admin/books/delete?id=$id' data-href='".base_url("api/cms/delete")."'><i class='fas fa-trash'></i> Delete</a>";

			$action = $edit.'&nbsp;'.$delete;

			$img = "";
			if(!empty($l->image)) {
				$img = "<a href='$l->image' target='_blank'><img src='$l->image' width='200px'></a>";
			}

			$row[] = $l->description;
			$row[] = $img;

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

	function showauthor() {
		$token = $this->session->token;
		$q = $this->input->get("q");
		/*
		$json[] = array("id"=>1,"text"=>"test $q");
		$json[] = array("id"=>2,"text"=>"test 2");
		*/

		$curl = curl_init();
/*
		curl_setopt_array($curl, array(
		CURLOPT_URL => URL_API."admin/author/list",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => array('like' => $q),
		CURLOPT_HTTPHEADER => array(
			"token: $token"
		),
		));
*/
		curl_setopt_array($curl, array(
			CURLOPT_URL => URL_API.'admin/author/list_dropdown',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => array('like' => $q),
			CURLOPT_HTTPHEADER => array(
			"token: $token"
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		//echo $response;


		$data_list = json_decode($response);
		$json_arr = $data_list->data;
//		$json_arr = $dlist->data;

		foreach($json_arr as $r) {
			$json[] = array("id"=>$r->id,"text"=>$r->name);
		}

		$result = array("results"=>$json);

		echo json_encode($result);
	}

	function showpublisher() {
		$token = $this->session->token;
		$q = $this->input->get("q");
		/*
		$json[] = array("id"=>1,"text"=>"test $q");
		$json[] = array("id"=>2,"text"=>"test 2");
		*/

		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => URL_API."admin/publisher/list",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => array('like' => $q),
		CURLOPT_HTTPHEADER => array(
			"token: $token"
		),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		//echo $response;


		$data_list = json_decode($response);
		$dlist = $data_list->data;
		$json_arr = $dlist->data;

		foreach($json_arr as $r) {
			$json[] = array("id"=>$r->id,"text"=>$r->name);
		}

		$result = array("results"=>$json);

		echo json_encode($result);
	}

	
	function showcategory() {
		$token = $this->session->token;
		$q = $this->input->get("q");
		
		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => URL_API."admin/category/list",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => array('like' => $q),
		CURLOPT_HTTPHEADER => array(
			"token: $token"
		),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		//echo $response;


		$data_list = json_decode($response);
		$dlist = $data_list->data;
		$json_arr = $dlist->data;

		foreach($json_arr as $r) {
			$json[] = array("id"=>$r->id,"text"=>$r->code.' - '.$r->fullname);
		}

		$result = array("results"=>$json);

		echo json_encode($result);
	}

	function view_book() {
		$data = array();
		//$id = $this->uri->segment(4);
		$id = $this->input->get("data");
		$token = $this->session->token;
		if(!empty($id)) {
			$data['submit_name'] = "Update";
			$data['act'] = "update";
			$jj = base64_decode($id);
			$r = json_decode($jj);

			if(!empty($r->author)) {
				$n = 0;
				foreach($r->author as $au) {
					$n++;
					if($n == 1) {
						$author1 = $au->name;
						$author1_id = $au->id;
					}
					if($n == 2) {
						$author2 = $au->name;
						$author2_id = $au->id;
					}
					if($n == 3) {
						$author3 = $au->name;
						$author3_id = $au->id;
					}
				}
			}

//			var_dump($r);
			$idd = $r->book_id;
			$data['id'] = $idd;
			
			$data['tbl'] = "admin/books/update?id=$idd";
			
		} else {
			$data['submit_name'] = "Save";
			$data['act'] = "save";
			$data['id'] = "";
			$data['row'] = "";
			$r = "";

			$data['tbl'] = "admin/books/add";

		}
		$data['r'] = $r;
		$data['post'] = "crud_book";
		
		$username = $this->session->username;
		$user_id = $this->session->id;

		/*
		$opt4 = $this->inventory->barang_all();
		
		$option4 = option_builder($opt4,$r->barang_id,0);
		
		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => URL_API."admin/category/list",
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
		$dl = $data_list->data;
		$opt4 = $dl->data;

		$option4 = option_builder_book($opt4,$r->category_name,0);
*/
		//LAnguage
		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => URL_API."admin/language/list",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => array('page' => "1"),
		CURLOPT_HTTPHEADER => array(
			"token: $token"
		),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		//echo $response;


		$data_l = json_decode($response);
		$data_list = $data_l->data;
		$opt5 = $data_list->data;

		$option5 = option_builder4($opt5,$r->book_language,0);

		//Publisher
		/*
		$curl = curl_init();

		curl_setopt_array($curl, array(
		CURLOPT_URL => URL_API."admin/publisher/list",
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => array('like' => ""),
		CURLOPT_HTTPHEADER => array(
			"token: $token"
		),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		//echo $response;


		$data_list = json_decode($response);
		$dtl = $data_list->data;

		$opt6 = $dtl->data;

		$option6 = option_builder4($opt6,$r->book_publisher,0);
		*/
		$publisher = "";
		if(!empty($r->book_publisher)) {
			$publisher = $r->book_publisher;
		}

		$categoryname = "";
		if(!empty($r->category_name)) {
			$categoryname = $r->category_name;
		}
		
		//var_dump($dt);

		$data['column'][] = array("nama"=>"Title","type"=>"text","value"=>$r->title,"placeholder"=>"","pst_name"=>"title","id"=>"title","col"=>"6","required"=>"required");
		$data['column'][] = array("nama"=>"Title Series","type"=>"text","value"=>@$r->titleseries,"placeholder"=>"","pst_name"=>"titleseries","id"=>"titleseries","col"=>"6","required"=>"required");
        $data['column'][] = array("nama"=>"Year Publish","type"=>"text","value"=>@$r->yearpublish,"placeholder"=>"","pst_name"=>"yearpublish","id"=>"yearpublish","col"=>"6","required"=>"required");
        $data['column'][] = array("nama"=>"Language","type"=>"select2","value"=>"","placeholder"=>"","pst_name"=>"idlanguage","id"=>"idlanguage","col"=>"6","required"=>"required","option"=>$option5);
       // $data['column'][] = array("nama"=>"ID Publisher","type"=>"text","value"=>@$r->idpublisher,"placeholder"=>"","pst_name"=>"idpublisher","id"=>"idpublisher","col"=>"6","required"=>"required");
        //$data['column'][] = array("nama"=>"ID Book Category","type"=>"text","value"=>@$r->idbookcategory,"placeholder"=>"","pst_name"=>"idbookcategory","id"=>"idbookcategory","col"=>"6","required"=>"required");
		$data['column'][] = array("nama"=>"Publisher","type"=>"select2remotepublisher","value"=>$publisher,"value_id"=>$r->idpublisher,"placeholder"=>"","pst_name"=>"idpublisher","id"=>"idpublisher","col"=>"6","required"=>"required");
		$data['column'][] = array("nama"=>"Book Category","type"=>"select2remotecategory","value"=>$categoryname,"value_id"=>$r->idbookcategory,"placeholder"=>"","pst_name"=>"idbookcategory","id"=>"idbookcategory","col"=>"6","required"=>"required");
//		$data['column'][] = array("nama"=>"Book Category","type"=>"select2","value"=>"","placeholder"=>"","pst_name"=>"idbookcategory","id"=>"idbookcategory","col"=>"6","required"=>"required","option"=>$option4);

		$data['column'][] = array("nama"=>"Author 1","type"=>"select2remoteauthor","value"=>$author1,"value_id"=>$author1_id,"placeholder"=>"","pst_name"=>"author1","id"=>"author1","col"=>"6","required"=>"");
		$data['column'][] = array("nama"=>"Author 2","type"=>"select2remoteauthor","value"=>$author2,"value_id"=>$author2_id,"placeholder"=>"","pst_name"=>"author2","id"=>"author2","col"=>"6","required"=>"");
		$data['column'][] = array("nama"=>"Author 3","type"=>"select2remoteauthor","value"=>$author3,"value_id"=>$author3_id,"placeholder"=>"","pst_name"=>"author3","id"=>"author3","col"=>"6","required"=>"");
		
		$data['column'][] = array("nama"=>"Description","type"=>"textarea","value"=>$r->description,"placeholder"=>"","pst_name"=>"description","id"=>"description","col"=>"12","required"=>"");
		


		$this->load->view('modal/books',$data);
	}



}
