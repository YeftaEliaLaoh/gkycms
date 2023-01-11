<?php 
class Model_utama extends CI_model{
    // Klasifikasi
    function klasifikasi(){
        return $this->db->query("SELECT * FROM klasifikasi ORDER BY id_klasifikasi DESC");
    }

    function klasifikasi_tambah(){
        $id = $this->session->username;
        $datadb = array('nama'=>$this->db->escape_str($this->input->post('a')),
                        'aktif'=>$this->db->escape_str($this->input->post('b')),'user_created' => $id,'user_updated' => $id);
        $this->db->set('dt_created', 'NOW()', FALSE);
        $this->db->set('dt_updated', 'NOW()', FALSE);
        $this->db->insert('klasifikasi',$datadb);
    }

    function klasifikasi_edit($id){
        return $this->db->query("SELECT * FROM klasifikasi where id_klasifikasi='$id'");
    }

    function klasifikasi_update(){
        $id = $this->session->username;
        $datadb = array('nama'=>$this->db->escape_str($this->input->post('a')),
                        'aktif'=>$this->db->escape_str($this->input->post('b')),'user_updated' => $id);
        $this->db->set('dt_updated', 'NOW()', FALSE);
        $this->db->where('id_klasifikasi',$this->input->post('id'));
        $this->db->update('klasifikasi',$datadb);
    }

    function klasifikasi_delete($id){
        return $this->db->query("DELETE FROM klasifikasi where id_klasifikasi='$id'");
    }

    // Jenis Sertifikat
    function jenis(){
        return $this->db->query("SELECT * FROM jenis_sertifikat ORDER BY id_jenis_sertifikat DESC");
    }

    function jenis_tambah(){
        $id = $this->session->username;
        $datadb = array('nama'=>$this->db->escape_str($this->input->post('a')),
                        'aktif'=>$this->db->escape_str($this->input->post('b')),'user_created' => $id,'user_updated' => $id);
        $this->db->set('dt_created', 'NOW()', FALSE);
        $this->db->set('dt_updated', 'NOW()', FALSE);
        $this->db->insert('jenis_sertifikat',$datadb);
    }

    function jenis_edit($id){
        return $this->db->query("SELECT * FROM jenis_sertifikat where id_jenis_sertifikat='$id'");
    }

    function jenis_update(){
        $id = $this->session->username;
        $datadb = array('nama'=>$this->db->escape_str($this->input->post('a')),
                        'aktif'=>$this->db->escape_str($this->input->post('b')),'user_updated' => $id);
        $this->db->set('dt_updated', 'NOW()', FALSE);
        $this->db->where('id_klasifikasi',$this->input->post('id'));
        $this->db->update('jenis_sertifikat',$datadb);
    }

    function jenis_delete($id){
        return $this->db->query("DELETE FROM jenis_sertifikat where id_jenis_sertifikat='$id'");
    }


    //Ahli
    function ahli(){
        return $this->db->query("SELECT * FROM ahli ORDER BY id_ahli DESC");
    }

    function ahli_tambah(){
        $id = $this->session->username;
        $datadb = array('nama'=>$this->db->escape_str($this->input->post('a')),
                        'aktif'=>$this->db->escape_str($this->input->post('b')),'user_created' => $id,'user_updated' => $id);
        $this->db->set('dt_created', 'NOW()', FALSE);
        $this->db->set('dt_updated', 'NOW()', FALSE);
        $this->db->insert('ahli',$datadb);
    }

    function ahli_edit($id){
        return $this->db->query("SELECT * FROM ahli where id_ahli='$id'");
    }

    function ahli_update(){
        $id = $this->session->username;
        $datadb = array('nama'=>$this->db->escape_str($this->input->post('a')),
                        'aktif'=>$this->db->escape_str($this->input->post('b')),'user_updated' => $id);
        $this->db->set('dt_updated', 'NOW()', FALSE);
        $this->db->where('id_ahli',$this->input->post('id'));
        $this->db->update('ahli',$datadb);
    }

    function ahli_delete($id){
        return $this->db->query("DELETE FROM ahli where id_ahli='$id'");
    }

    
    function show_provinsi() {
        
        $query = $this->db->query("SELECT * FROM ref_provinsi ORDER BY nama_provinsi");

        return $query;
    }

    function show_kota($p) {
        if(!empty($p)) {
            $query = $this->db->query("SELECT * FROM ref_kabkota WHERE id_provinsi='$p' ORDER BY nama_kabkota");
        } else {
            $query = "";
        }
        

        return $query;
    }

    function show_kecamatan($p) {
        if(!empty($p)) {
            $query = $this->db->query("SELECT * FROM ref_kecamatan WHERE id_kabkota='$p' ORDER BY nama_kecamatan");
        } else {
            $query = "";
        }
        

        return $query;
    }

    function show_kelurahan($p) {
        if(!empty($p)) {
            $query = $this->db->query("SELECT * FROM ref_kelurahan WHERE id_kecamatan='$p' ORDER BY nama_kelurahan");
        } else {
            $query = "";
        }
        

        return $query;
    }


    
}