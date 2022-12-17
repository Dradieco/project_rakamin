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

    // Get
    public function index_get() {
    
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
                "message"   => "Username cannot be Empty"
            ], RestController::HTTP_BAD_REQUEST);
        }
        else if(empty($receiver_number)){
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
                $model_conversation = $this->message->check_conversation($data_sender['user_id'], $data_receiver['user_id']);
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

    // DELETE
    public function index_delete(){

    }
}