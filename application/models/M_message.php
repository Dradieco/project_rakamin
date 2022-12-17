<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_message extends CI_Model {

    // GET DATA
    public function check_conversation($sender_user_id, $receiver_user_id) {
        $query = $this->db->query("
            SELECT
                *
            FROM
                user_message
            WHERE
                sender_user_id IN ('$sender_user_id', '$receiver_user_id')
            AND
                receiver_user_id IN ('$sender_user_id', '$receiver_user_id')
            AND
                status = 1
            GROUP BY
                message_id
        ");
        
        return $query;
    }

    // POST DATA
    public function insert_new_message($data) {
        $this->db->insert("user_message", $data);
        
        return $this->db->affected_rows();
    }

    // DELETE DATA
    public function delete_message($where) {
        $this->db->delete("user_message", $where);
        
        return $this->db->affected_rows();
    }
}