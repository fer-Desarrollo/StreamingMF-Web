<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuario_model extends CI_Model {

    public function crear($data)
    {
        $this->db->insert('usuarios', $data);
        return $this->db->insert_id();
    }

    public function obtener_por_email($email)
    {
        return $this->db
            ->select('u.*, r.nombre AS rol')
            ->from('usuarios u')
            ->join('roles r', 'r.id_rol = u.id_rol')
            ->where('email', $email)
            ->where('activo', 1)
            ->get()->row();
    }

    public function existe($email, $username)
    {
        return $this->db
            ->group_start()
            ->where('email', $email)
            ->or_where('nombre_usuario', $username)
            ->group_end()
            ->get('usuarios')->row();
    }

    public function existe_email($email)
    {
        return $this->db
            ->where('email', $email)
            ->get('usuarios')
            ->row();
    }

    public function rol_por_nombre($nombre)
    {
        return $this->db->where('nombre', $nombre)->get('roles')->row();
    }
}

