<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Persona_model extends CI_Model {

    public function crear($data)
    {
        $this->db->insert('personas', $data);
        return $this->db->insert_id();
    }
}