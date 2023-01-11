<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_supplier extends CI_Model {

	var $table = 'tbvendor';
	var $column_order = array(null, 'nama','aktif'); //set column field database for datatable orderable
	var $column_search = array('nama','aktif'); //set column field database for datatable searchable 
	var $order = array('id' => 'asc'); // default order 

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
		$wh .= " AND (u.kodevendor LIKE '%$s%' OR u.nama LIKE '%$s%')";

		$wh .= "  ORDER BY u.id DESC ";
		
		return "SELECT u.*,v.nama as kategori_name FROM tbvendor u LEFT JOIN tbmaster v ON v.id=u.kategori WHERE 1=1 $wh";
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
    
	function kategori_all(){
        return $this->db->query("SELECT id as id, nama as name FROM tbmaster WHERE kategori='harga'")->result();
	}

    function supplier_edit($id){
        return $this->db->query("SELECT * FROM tbvendor u where u.id='$id'");
	}

	function check_supplier($name) {
		$a= $this->db->query("SELECT * FROM tbvendor WHERE name LIKE '%$name%'")->row();
		if(!empty($a->id)) {
			return $a->id;
		} else {
			return 0;
		}

	}

	function supplier_all(){
        return $this->db->query("SELECT * FROM tbvendor u ")->result();
	}

}
