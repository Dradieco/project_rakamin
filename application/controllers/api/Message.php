<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use chriskacerguis\RestServer\RestController;

class Message extends RestController {

    function __construct() {
        parent::__construct();

        $this->load->model("M_uuid", "uuid");
        $this->load->model("M_user", "user");
        $this->load->model("M_mesage", "message");
    }

    // Get
    public function index_get() {
    
    }

    // POST
    public function index_post() {

    }

    // DELETE
    public function index_delete(){

    }
}