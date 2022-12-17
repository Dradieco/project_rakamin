<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_uuid extends CI_Model {
    public function gen_id_unique($table_name, $column_name, $string){
        $id = '';

        do{
            $random_string = random_string("numeric", "3");
            $unique_id =  $string."-".$random_string;

            $check = $this->db->get_where($table_name, [$column_name => $id])->num_rows();
        }
        while($check > 0);

        return $unique_id;
    }


    public function gen_auth(){
        do{
            $auth_key = random_string("alnum", "10");
            
            $check = $this->db->get_where("user", ["auth_key" => $auth_key])->num_rows();
        }
        while($check > 0);

        return $auth_key;
    }
}