<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class User extends RestController {

    function __construct() {
        parent::__construct();

        $this->load->model("M_user", "user");
    }

    public function index_get() {

        $name = $this->get("username");
        $number = $this->get("phonenumber");
        
        // Menampilkan data berdasarkan nama dan nomor
        if(!empty($name) && !empty($nomor)){
            $data = $this->user->get_user_by_name_number($name, $nomor);
        }
        // Menampilkan data berdasarkan nomor
        else if(!empty($number)){
            $data = $this->user->get_user_by_number($number);
        }
        // Menampilkan data berdasarkan nama
        if(!empty($name)) {
            $data = $this->user->get_user_by_name($name);
        }
        // Manampilkan semua data user
        else {
            $data = $this->user->get_all_user();
        }

        if(!empty($data)) {
            $this->response([
                "status_code" => true,
                "data" => $data,
            ], 200);
        }
        else {
            $this->response([
                "status_code" => 404,
                "message" => "Data tidak ditemukan"
            ], 404);
        }

        echo json_encode($data);
    }


}