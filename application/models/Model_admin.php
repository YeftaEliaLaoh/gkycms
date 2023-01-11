<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_billing extends CI_Model {

	var $table = 'billing';
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

		$jenis = $this->input->post("jenis");
		$tahun = $this->input->post("tahun");
		$pabean = $this->input->post("pabean");
		$angkutan = $this->input->post("angkutan");
		
		if(!empty($jenis)) {
			$wh .= " AND jenis = '$jenis'";
		}
		if(!empty($tahun)) {
			$wh .= " AND DATE_FORMAT(b.tanggal, '%Y') = '$tahun'";
		}
		if(!empty($pabean)) {
			$wh .= " AND u.pabean = '$pabean'";
		}
		if(!empty($angkutan)) {
			$wh .= " AND u.angkutan = '$angkutan'";
		}
		
		$s = $_POST['search']['value'];
		$wh .= " AND (b.no LIKE '%$s%' OR u.no LIKE '%$s%' OR v.nama LIKE '%$s%' OR u.no_aju LIKE '%$s%' OR u.bl LIKE '%$s%' OR u.awb LIKE '%$s%')";

		$wh .= "  ORDER BY b.id DESC ";
		
		return "SELECT b.*,v.nama as pelanggan_name,w.nama as wilayah_name,p.nama as pemasok_name,g.nama as gudang_name,u.no as joborder_no,u.no_aju,u.bl,u.awb 
		FROM tbbilling b LEFT JOIN joborder u ON u.id=b.id_joborder 
		LEFT JOIN tbvendor v ON v.kodevendor=u.idpelanggan 
		LEFT JOIN wilayah w ON w.id=u.idwilayah
		LEFT JOIN pemasok p ON p.id=u.idpemasok
		LEFT JOIN gudang g ON g.id=u.idgudang
		LEFT JOIN peti_kemas pk ON pk.id=u.jenis_peti WHERE 1=1 $wh";
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
    
    function billing_edit($id){
        return $this->db->query("SELECT u.*,j.no as joborder_name FROM tbbilling u LEFT JOIN joborder j ON j.id=u.id_joborder where u.id='$id'");
	}

	function checkdetailbilling($id) {
		return $this->db->query("SELECT * FROM tb_detail_billing u where u.id='$id'")->row();
	}
	
    function detail_billing($id){
        return $this->db->query("SELECT * FROM tb_detail_billing u where u.no_billing='$id'");
	}

	function check_billing($name) {
		$a= $this->db->query("SELECT * FROM tbbilling WHERE no LIKE '%$name%'")->row();
		if(!empty($a->id)) {
			return $a->id;
		} else {
			return 0;
		}

	}

	function jenis_all(){
		$opt2[] = (object)array("id"=>"ump","name"=>"Uang Muka / Penggantian");
		$opt2[] = (object)array("id"=>"inklaring","name"=>"Inklaring");
		$opt2[] = (object)array("id"=>"tagihan","name"=>"Tagihan");
		$opt2[] = (object)array("id"=>"jasangkut","name"=>"Angkutan");

		return $opt2;
	}

	function tahun(){
		$now = date('Y');
		$before = $now-3;
		$after = $now+2;
		for($i = $before;$i<=$after;$i++) {
			$opt2[] = (object)array("id"=>$i,"name"=>$i);
		}

		return $opt2;
	}
	
	function jenis_all_update($id){
		if($id == 'ump') {
			$opt2[] = (object)array("id"=>"ump","name"=>"Uang Muka / Penggantian");
		} else if($id == 'inklaring') {
			$opt2[] = (object)array("id"=>"inklaring","name"=>"Inklaring");
		} else if ($id == 'jasangkut') {
			$opt2[] = (object)array("id"=>"jasangkut","name"=>"Angkutan");
		} else {
			$opt2[] = (object)array("id"=>"tagihan","name"=>"Tagihan");
		} 
		

		return $opt2;
	}

	function akun_all() {
		return $this->db->query("SELECT kodeakun as id, namaakun as name FROM tbakun ORDER BY namaakun ASC")->result();
	}

	function akun_type($id) {
		return $this->db->query("SELECT * FROM tbakun WHERE akunmaster = $id")->result();
	}

	function get_akun_id($id) {
		return $this->db->query("SELECT * FROM tbakun WHERE kodeakun = '$id'")->row();
	}

	function update_fp($no_fp1,$no_fp2,$no,$tanggal) {
		
		$this->db->set('updated_date', 'NOW()', FALSE);
		$this->db->where('kode',$no_fp2);
		$post2 = array("tipe"=>$no_fp1,"no_billing"=>$no,"tanggal"=>tgl_simpan($tanggal));
		$this->db->update("faktur_pajak",$post2);

	}

	function ambilnojurnal($no){
		$sql = "select * from tbjurnal";
		$query = $this->db->query($sql);
		$num = $query->num_rows();
		$y = 2;

		for($x=1;$x<=$y;$x++){
			$num +=1;
			$nojurnal = ("JU-".$no."-".$num);
			$sql2 = "select * from tbjurnal where nojurnal='$nojurnal'";
			$query2 = $this->db->query($sql2);
			$num2 = $query2->num_rows();
			if($num2==0){
				$x = $y+1;
			}else{
				$y++;
			}
		}
		return $nojurnal;		
	}

	function insert_jurnal($no,$tanggal,$user,$total_tagihan,$id_jo) {
		$sql = "SELECT tbvendor.kodevendor FROM tbvendor INNER JOIN joborder ON joborder.idpelanggan = tbvendor.kodevendor WHERE joborder.id = '$id_jo'";
		$query = $this->db->query($sql);
		
		$re = $query->row();
		$lokasi = "G01";
		
		$vendor = $re->kodevendor;
		$nojurnal = $this->ambilnojurnal($no);
		$tgl = tgl_simpan($tanggal);

		$datajurnal = array("nojurnal"=>$nojurnal,"tgl"=>$tgl,"document_date"=>$tgl,"iduser"=>$user,"headerdata"=>$no,"project_id"=>$lokasi);
		$this->db->insert("tbjurnal",$datajurnal);

		$id = $this->db->insert_id();

		$datadetailjurnal = array("nojurnal"=>$nojurnal,"kodeakun"=>'111301001',"debet"=>$total_tagihan,"credit"=>0,"asignment"=>$no,"vendor"=>$vendor);
		$this->db->insert("tbjurnaldetil",$datadetailjurnal);

		$datadetailjurnal2 = array("nojurnal"=>$nojurnal,"kodeakun"=>'631101003',"debet"=>0,"credit"=>$total_tagihan,"asignment"=>$no);
		$this->db->insert("tbjurnaldetil",$datadetailjurnal2);
	}

}
