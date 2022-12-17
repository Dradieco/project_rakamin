<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_message extends CI_Model {

    // GET DATA
    public function get_all_conversation($sender_user_id) {
        $query = $this->db->query("
            SELECT
                conversation_id,
                (
                    SELECT 
                        a.message 
                    FROM 
                        user_message as a 
                    WHERE 
                        a.conversation_id = conversation_id 
                    AND
                        a.status = 1
                    ORDER BY
                        a.ts_insert
                    LIMIT 1
                ) as last_message,
                (
                    SELECT 
                        COUNT(*)
                    FROM 
                        user_message as b
                    WHERE 
                        b.conversation_id = conversation_id 
                    AND
                        b.status = 1
                    AND
                        b.is_read = 0
                    ORDER BY
                        b.ts_insert
                ) as Unread_count
            FROM
                user_message
            WHERE
                (sender_user_id = '$sender_user_id'
                OR
                receiver_user_id = '$sender_user_id')
            GROUP BY
                conversation_id
            ORDER BY
                ts_insert ASC
        ")->result_array();
        
        return $query;
    }

    public function get_conversation($sender_user_id, $receiver_user_id) {
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
            ORDER BY
                ts_insert ASC
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