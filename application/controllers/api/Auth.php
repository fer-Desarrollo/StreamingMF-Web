<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Usuario_model','Persona_model']);
        header('Content-Type: application/json');
    }

    public function crear_admin()
    {
        $input = json_decode($this->input->raw_input_stream, true);

        if ($this->Usuario_model->existe_email($input['email'])) {
            http_response_code(409);
            echo json_encode(['error' => 'Admin ya existe']);
            return;
        }

        $id_persona = $this->Persona_model->crear([
            'nombres' => $input['nombres'],
            'apellidos' => $input['apellidos']
        ]);

        $this->Usuario_model->crear([
            'id_persona' => $id_persona,
            'id_rol' => 1,
            'email' => $input['email'],
            'nombre_usuario' => $input['nombre_usuario'],
            'password_hash' => password_hash($input['password'], PASSWORD_BCRYPT),
            'password_temporal' => 0,
            'email_verificado' => 1
        ]);

        echo json_encode(['success' => true]);
    }

    public function login()
    {
        $input = json_decode($this->input->raw_input_stream, true);
        $u = $this->Usuario_model->obtener_por_email($input['email']);

        if (!$u || !password_verify($input['password'], $u->password_hash)) {
            http_response_code(401);
            echo json_encode(['error' => 'Credenciales inválidas']);
            return;
        }

        $token = $this->jwt->encode([
            'id_usuario' => $u->id_usuario,
            'rol' => $u->rol
        ]);

        echo json_encode([
            'token' => $token,
            'password_temporal' => $u->password_temporal
        ]);
    }
}