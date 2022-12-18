<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class User extends RestController {

    function __construct() {
        parent::__construct();

        $this->load->model("M_uuid", "uuid");
        $this->load->model("M_user", "user");
    }

    // Get
    public function index_get() {

        $name = $this->get("username");
        $number = $this->get("phonenumber");
        
        // Menampilkan data berdasarkan nama dan nomor
        if(!empty($name) && !empty($number)){
            $data = $this->user->get_user_by_name_number($name, $number);
        }
        // Menampilkan data berdasarkan nomor
        else if(!empty($number)){
            $data = $this->user->get_user_by_number($number);
        }
        // Menampilkan data berdasarkan nama
        else if(!empty($name)) {
            $data = $this->user->get_user_by_name($name);
        }
        // Manampilkan semua data user
        else {
            $data = $this->user->get_all_user();
        }

        if(!empty($data)) {
            $this->response([
                "status"    => true,
                "message"   => "Data Found",
                "data"      => $data,
            ], RestController::HTTP_OK);
        }
        else {
            $this->response([
                "status"    => false,
                "message"   => "User Data Not Found"
            ], RestController::HTTP_NOT_FOUND);
        }
    }


    // POST
    public function index_post() {
        $username = $this->post("username");
        $phonenumber = $this->post("phonenumber");

        $data = [
            "user_id"       => $this->uuid->gen_id_unique("user", "user_id", "USR"),
            "username"      => $username,
            "phonenumber"   => $phonenumber,
            "auth_key"      => $this->uuid->gen_auth()
        ];

        if(empty($username)){
            $this->response([
                "status"    => false,
                "message"   => "Username cannot be Empty"
            ], RestController::HTTP_BAD_REQUEST);
        }
        else if(empty($phonenumber)){
            $this->response([
                "status"    => false,
                "message"   => "Phone Number cannot be Empty"
            ], RestController::HTTP_BAD_REQUEST);
        }
        else{
            $check_existing_number = $this->user->check_number($phonenumber);

            if($check_existing_number > 0) {
                $this->response([
                    "status"    => false,
                    "message"   => "This Phone Number has been Registered"
                ], RestController::HTTP_NOT_ACCEPTABLE);
            }
            else {
                $insert = $this->user->insert_new_user($data);
        
                if($insert > 0){
                    $this->response([
                        "status"    => true,
                        "message"   => "Insert new User has Success"
                    ], RestController::HTTP_CREATED);
                }
                else {
                    $this->response([
                        "status"    => false,
                        "message"   => "Insert new User Failed"
                    ], RestController::HTTP_BAD_REQUEST);
                }
            }
        }
    }

    // PUT
    public function index_put(){
        $username = $this->put("username");
        $phonenumber = $this->put("phonenumber");
        $auth_key = $this->put("auth_key");

        $data = [
            "username"  => $username,
        ];

        $where = [
            "phonenumber"   => $phonenumber,
            "auth_key"      => $auth_key,
            "status"        => 1
        ];

        if(empty($username)){
            $this->response([
                "status"    => false,
                "message"   => "Username cannot be Empty"
            ], RestController::HTTP_BAD_REQUEST);
        }
        else if(empty($phonenumber)){
            $this->response([
                "status"    => false,
                "message"   => "Phone Number cannot be Empty"
            ], RestController::HTTP_BAD_REQUEST);
        }
        else if(empty($auth_key)){
            $this->response([
                "status"    => false,
                "message"   => "Auth Key cannot be Empty"
            ], RestController::HTTP_BAD_REQUEST);
        }
        else{
            $check_auth = $this->user->check_auth($auth_key, $phonenumber);

            if($check_auth == 0) {
                $this->response([
                    "status"    => false,
                    "message"   => "You are Unauthorized to Access This Function"
                ], RestController::HTTP_UNAUTHORIZED);
            }
            else {
                $update = $this->user->update_user($data, $where);
        
                if($update > 0){
                    $this->response([
                        "status"    => true,
                        "message"   => "Update User has Success"
                    ], RestController::HTTP_OK);
                }
                else {
                    $this->response([
                        "status"    => true,
                        "message"   => "No Data to be Updated"
                    ], RestController::HTTP_OK);
                }
            }
        }
    }

    // DELETE
    public function index_delete(){
        $phonenumber = $this->delete("phonenumber");
        $auth_key = $this->delete("auth_key");
        
        $where = [
            "phonenumber"   => $phonenumber,
            "auth_key"      => $auth_key,
            "status"        => 1
        ];

        if(empty($phonenumber)){
            $this->response([
                "status"    => false,
                "message"   => "Phone Number cannot be Empty"
            ], RestController::HTTP_BAD_REQUEST);
        }
        if(empty($auth_key)){
            $this->response([
                "status"    => false,
                "message"   => "Auth Key cannot be Empty"
            ], RestController::HTTP_BAD_REQUEST);
        }
        else{
            $check_auth = $this->user->check_auth($auth_key, $phonenumber);

            if($check_auth == 0) {
                $this->response([
                    "status"    => false,
                    "message"   => "You are Unauthorized to Access This Function"
                ], RestController::HTTP_UNAUTHORIZED);
            }
            else {
                $delete = $this->user->delete_user($where);
        
                if($delete > 0){
                    $this->response([
                        "status"    => true,
                        "message"   => "Delete User with Number : ".$phonenumber." has Success"
                    ], RestController::HTTP_OK);
                }
                else {
                    $this->response([
                        "status"    => false,
                        "message"   => "No Data to be Deleted"
                    ], RestController::HTTP_BAD_REQUEST);
                }
            }
        }
    }
}