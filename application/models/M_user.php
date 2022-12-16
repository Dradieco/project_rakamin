<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_user extends CI_Model {

    public function get_all_user() {
        $query = $this->db->get_where("user", ["status" => 1])->result_array();

        return $query;
    }

    public function get_user_by_name($name) {
        $query = $this->db->get_where("user", ["username" => $name, "status" => 1])->result_array();
        
        return $query;
    }

    public function get_user_by_number($number) {
        $query = $this->db->get_where("user", ["phonenumber" => $number, "status" => 1])->result_array();
        
        return $query;
    }
    
    public function get_user_by_name_number($name, $number) {
        $query = $this->db->get_where("user", ["username" => $name, "phonenumber" => $number, "status" => 1])->result_array();
        
        return $query;
    }
}