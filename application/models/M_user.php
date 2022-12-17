<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_user extends CI_Model {
    
    // GET DATA
    public function get_all_user() {
        $query = $this->db->get_where("user", ["status" => 1])->result_array();

        return $query;
    }

    public function get_user_by_name($name) {
        $query = $this->db->get_where("user", ["username" => $name, "status" => 1])->row_array();
        
        return $query;
    }

    public function get_user_by_number($number) {
        $query = $this->db->get_where("user", ["phonenumber" => $number, "status" => 1])->row_array();
        
        return $query;
    }
    
    public function get_user_by_name_number($name, $number) {
        $query = $this->db->get_where("user", ["username" => $name, "phonenumber" => $number, "status" => 1])->row_array();
        
        return $query;
    }

    public function check_number($phonenumber){
        $query = $this->db->get_where("user", ["phonenumber" => $phonenumber,"status" => 1])->num_rows();

        return $query;
    }

    public function check_auth($auth_key, $phonenumber){
        $query = $this->db->get_where("user", ["phonenumber" => $phonenumber, "auth_key" => $auth_key,"status" => 1])->num_rows();

        return $query;
    }

    // POST DATA
    public function insert_new_user($data) {
        $this->db->insert("user", $data);
        
        return $this->db->affected_rows();
    }

    // UPDATE DATA
    public function update_user($data, $where) {
        $this->db->update("user", $data, $where);
        
        return $this->db->affected_rows();
    }

    // DELETE DATA
    public function delete_user($where) {
        $this->db->delete("user", $where);
        
        return $this->db->affected_rows();
    }
}