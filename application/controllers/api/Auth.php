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
    
    public function cambiar_password()
    {
        // 🔐 Usuario autenticado por JWT
        $user = $this->auth_user;
    
        $input = json_decode($this->input->raw_input_stream, true);
    
        if (!$input || empty($input['password'])) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'error' => 'La nueva contraseña es obligatoria'
                ]));
        }
    
        // Validación mínima
        if (strlen($input['password']) < 8) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'error' => 'La contraseña debe tener al menos 8 caracteres'
                ]));
        }
    
        $password_hash = password_hash($input['password'], PASSWORD_BCRYPT);
    
        $this->Usuario_model->cambiar_password(
            $user['id_usuario'],
            $password_hash
        );
    
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'success' => true,
                'message' => 'Contraseña actualizada correctamente'
            ]));
    }
    
}