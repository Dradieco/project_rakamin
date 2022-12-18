<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Message extends RestController {

    function __construct() {
        parent::__construct();

        $this->load->model("M_uuid", "uuid");
        $this->load->model("M_user", "user");
        $this->load->model("M_message", "message");
    }

    // GET
    public function index_get() {
        $account_number = $this->get("account_number");
        $target_number  = $this->get("target_number");
        $auth_key       = $this->get("auth_key");

        $data_account   = $this->user->get_user_by_number($account_number);
        $data_target    = $this->user->get_user_by_number($target_number);

        $check_auth     = $this->user->check_auth($auth_key, $account_number);

        if(empty($account_number)){
            $this->response([
                "status"    => false,
                "message"   => "Account Number cannot be Empty"
            ], RestController::HTTP_BAD_REQUEST);
        }
        else if(empty($auth_key)){
            $this->response([
                "status"    => false,
                "message"   => "Auth Key cannot be Empty"
            ], RestController::HTTP_BAD_REQUEST);
        }
        else if($check_auth == 0) {
            $this->response([
                "status"    => false,
                "message"   => "You are Unauthorized to Access This Function"
            ], RestController::HTTP_UNAUTHORIZED);
        }
        else {
            $meta_message = [];

            if(empty($target_number)) {
                $data = $this->message->get_all_conversation($data_account['user_id']);

                $meta_message = $data;
            }
            else {
                $data = $this->message->get_conversation($data_account['user_id'], $data_target['user_id'])->result_array();
                
                $meta_message = [
                    "username"          => $data_account['username'],
                    "target_username"   => $data_target['username'],
                    "data_message"      => $data
                ];
            }

            if(!empty($data)) {
                $this->response([
                    "status"    => true,
                    "message"   => "Data Conversation Found",
                    "data"      => $meta_message
                ], RestController::HTTP_OK);
            }
            else {
                $this->response([
                    "status"    => false,
                    "message"   => "Data Conversation Not Found"
                ], RestController::HTTP_NOT_FOUND);
            }
        }
    }

    // POST
    public function index_post() {
        $sender_number      = $this->post("sender_number");
        $receiver_number    = $this->post("receiver_number");
        $auth_key           = $this->post("auth_key_sender");
        $message            = $this->post("message");

        if(empty($sender_number)){
            $this->response([
                "status"    => false,
                "message"   => "Sender Number cannot be Empty"
            ], RestController::HTTP_BAD_REQUEST);
        }
        else if(empty($receiver_number)){
            $this->response([
                "status"    => false,
                "message"   => "Receiver Number cannot be Empty"
            ], RestController::HTTP_BAD_REQUEST);
        }
        else if(empty($auth_key)){
            $this->response([
                "status"    => false,
                "message"   => "Auth Key cannot be Empty"
            ], RestController::HTTP_BAD_REQUEST);
        }
        else if($message == '' || $message == NULL){
            $this->response([
                "status"    => false,
                "message"   => "The system not create any conversation and there is no delivered message"
            ], RestController::HTTP_OK);
        }
        else{
        
            $data_sender        = $this->user->get_user_by_number($sender_number);
            $data_receiver      = $this->user->get_user_by_number($receiver_number);
            
            $check_auth         = $this->user->check_auth($auth_key, $sender_number);

            if($check_auth == 0) {
                $this->response([
                    "status"    => false,
                    "message"   => "You are Unauthorized to Access This Function"
                ], RestController::HTTP_UNAUTHORIZED);
            }
            else if(empty($data_receiver)) {
                $this->response([
                    "status"    => false,
                    "message"   => "Target Number not found in the Database"
                ], RestController::HTTP_NOT_FOUND);
            }
            else {            
                $message_id         = $this->uuid->gen_id_unique("user_message", "message_id", "MSG", "5");

                // Check Conversation from this sender or target number if exist then use id existing
                $model_conversation = $this->message->get_conversation($data_sender['user_id'], $data_receiver['user_id']);
                $conversation_exist = $model_conversation->num_rows();
                $data_conversation  = $model_conversation->row_array();

                if($conversation_exist > 0 ){
                    $conversation_id    = $data_conversation['conversation_id'];
                }
                else {
                    $conversation_id    = $this->uuid->gen_id_unique("user_message", "conversation_id", "COV", "5");
                }
                
                $data = [
                    "conversation_id"   => $conversation_id,
                    "message_id"        => $message_id,
                    "sender_user_id"    => $data_sender['user_id'],
                    "receiver_user_id"  => $data_receiver['user_id'],
                    "message"           => $message,
                    // Default every send a message will flag unread until user access their message
                    "is_read"           => 0
                ];

                $insert = $this->message->insert_new_message($data);
        
                if($insert > 0){
                    if($conversation_exist > 0 ){
                        $this->response([
                            "status"    => true,
                            "message"   => "Send Message to ".$data_receiver['username']." has been Success"
                        ], RestController::HTTP_OK);
                    }
                    else {
                        $this->response([
                            "status"    => true,
                            "message"   => "Create New Message and Sent to ".$data_receiver['username']." has been Success"
                        ], RestController::HTTP_CREATED);
                    }
                }
                else {
                    $this->response([
                        "status"    => false,
                        "message"   => "Send Message to ".$data_receiver['username']." Failed"
                    ], RestController::HTTP_BAD_REQUEST);
                }
            }
        }
    }

    // PUT
    public function index_put() {
        $account_number     = $this->get("account_number");
        $conversation_id    = $this->get("conversation_id");
        $auth_key           = $this->get("auth_key");

        $data = [
            "is_read"   => 1
        ];
        
        $where = [
            "conversation_id"   => $conversation_id,
            "status"            => 1
        ];

        $check_auth     = $this->user->check_auth($auth_key, $account_number);

        if(empty($account_number)){
            $this->response([
                "status"    => false,
                "message"   => "Account Number cannot be Empty"
            ], RestController::HTTP_BAD_REQUEST);
        }
        else if(empty($account_number)){
            $this->response([
                "status"    => false,
                "message"   => "Target Number cannot be Empty"
            ], RestController::HTTP_BAD_REQUEST);
        }
        else if(empty($auth_key)){
            $this->response([
                "status"    => false,
                "message"   => "Auth Key cannot be Empty"
            ], RestController::HTTP_BAD_REQUEST);
        }
        else if($check_auth == 0) {
            $this->response([
                "status"    => false,
                "message"   => "You are Unauthorized to Access This Function"
            ], RestController::HTTP_UNAUTHORIZED);
        }
        else {
            $update = $this->message->update_read_message($data, $where);

            if( $update > 0){
                $this->response([
                    "status"    => true,
                    "message"   => "Update Read Message has Success"
                ], RestController::HTTP_OK);
            }
            else {
                $this->response([
                    "status"    => false,
                    "message"   => "No Data to be Updated"
                ], RestController::HTTP_BAD_REQUEST);
            }

        }
    }

    // DELETE
    public function index_delete(){
        $phonenumber        = $this->delete("phonenumber");
        $message_id         = $this->delete("message_id");
        $conversation_id    = $this->delete("conversation_id");
        $del_type           = $this->delete("del_type");
        $auth_key           = $this->delete("auth_key");
        
        if(empty($del_type) || ($del_type != "message" && $del_type != "conversation") ) {
            $this->response([
                "status"    => false,
                "message"   => "Choose Delete Type (message / conversation)"
            ], RestController::HTTP_BAD_REQUEST);
        }
        else {
            if($del_type == "message") {
                $where = [
                    "message_id"       => $message_id,
                    "status"        => 1
                ];
        
                if(empty($phonenumber)){
                    $this->response([
                        "status"    => false,
                        "message"   => "Phone Number cannot be Empty"
                    ], RestController::HTTP_BAD_REQUEST);
                }
                if(empty($message_id)){
                    $this->response([
                        "status"    => false,
                        "message"   => "Message ID cannot be Empty!"
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
                        $delete = $this->message->delete_message($where);
                
                        if($delete > 0){
                            $this->response([
                                "status"    => true,
                                "message"   => "Delete Message with Message ID : ".$message_id." has Success"
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
            else {
                $where = [
                    "conversation_id"   => $conversation_id,
                    "status"            => 1
                ];
        
                if(empty($phonenumber)){
                    $this->response([
                        "status"    => false,
                        "message"   => "Phone Number cannot be Empty"
                    ], RestController::HTTP_BAD_REQUEST);
                }
                if(empty($conversation_id)){
                    $this->response([
                        "status"    => false,
                        "message"   => "Conversation ID cannot be Empty!"
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
                        $delete = $this->message->delete_message($where);
                
                        if($delete > 0){
                            $this->response([
                                "status"    => true,
                                "message"   => "Delete Conversation with Conversation ID : ".$conversation_id." has Success"
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
    }
}