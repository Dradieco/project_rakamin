<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_message extends CI_Model {

    // GET DATA
    public function get_all_conversation($sender_user_id) {
        $query = $this->db->query("
            SELECT
                DISTINCT(msg.conversation_id),
                CASE
                    WHEN 
                        msg.sender_user_id != '$sender_user_id' THEN msg.sender_user_id
                    WHEN 
                        msg.receiver_user_id != '$sender_user_id' THEN msg.receiver_user_id
                END as target_user_id,
                (
                    SELECT 
                        us.username 
                    FROM 
                        user as us
                    WHERE 
                        us.user_id = target_user_id
                    AND
                        us.status = 1
                    LIMIT 1
                ) as target_username,
                (
                    SELECT 
                        us.phonenumber 
                    FROM 
                        user as us
                    WHERE 
                        us.user_id = target_user_id
                    AND
                        us.status = 1
                    LIMIT 1
                ) as target_phonenumber,
                (
                    SELECT 
                        a.message 
                    FROM 
                        user_message as a 
                    WHERE 
                        a.conversation_id = msg.conversation_id
                    AND
                        a.status = 1
                    ORDER BY
                        a.ts_insert DESC
                    LIMIT 1
                ) as last_message,
                (
                    SELECT 
                        a.ts_insert 
                    FROM 
                        user_message as a 
                    WHERE 
                        a.conversation_id = msg.conversation_id
                    AND
                        a.status = 1
                    ORDER BY
                        a.ts_insert DESC
                    LIMIT 1
                ) as datetime_last_message,
                (
                    SELECT 
                        COUNT(*)
                    FROM 
                        user_message as b
                    WHERE 
                        b.conversation_id = msg.conversation_id 
                    AND
                        b.status = 1
                    AND
                        b.is_read = 0
                ) as Unread_count
            FROM
                user_message as msg
            WHERE
                (msg.sender_user_id = '$sender_user_id'
                OR
                msg.receiver_user_id = '$sender_user_id')
            AND
                msg.status = 1
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

    // UPDATE DATA
    public function update_read_message($data, $where) {
        $this->db->update("user_message", $data, $where);
        
        return $this->db->affected_rows();
    }

    // DELETE DATA
    public function delete_message($where) {
        $this->db->delete("user_message", $where);
        
        return $this->db->affected_rows();
    }
}