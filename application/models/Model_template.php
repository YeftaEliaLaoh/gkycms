<?php 
class Model_template extends CI_model{
    function template(){
        return $this->db->query("SELECT * FROM templates");
    }

    function template_tambah(){
        $datadb = array('judul'=>$this->db->escape_str($this->input->post('a')),
                        'pembuat'=>$this->db->escape_str($this->input->post('b')),
                        'folder'=>$this->db->escape_str($this->input->post('c')),
                        'aktif'=>$this->db->escape_str($this->input->post('d')));
        $this->db->insert('templates',$datadb);
    }

    function template_update(){
        $datadb = array('judul'=>$this->db->escape_str($this->input->post('a')),
                        'pembuat'=>$this->db->escape_str($this->input->post('b')),
                        'folder'=>$this->db->escape_str($this->input->post('c')),
                        'aktif'=>$this->db->escape_str($this->input->post('d')));
        $this->db->where('id_templates',$this->input->post('id'));
        $this->db->update('templates',$datadb);
    }

    function template_edit($id){
        return $this->db->query("SELECT * FROM templates where id_templates='$id'");
    }

    function template_delete($id){
        return $this->db->query("DELETE FROM templates where id_templates='$id'");
    }
}