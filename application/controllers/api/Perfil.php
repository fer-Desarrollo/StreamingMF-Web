<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Perfil extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->model('Perfil_model');

        header('Content-Type: application/json');
    }

    // 📄 VER MI PERFIL
    public function index()
    {
        // 🔐 JWT obligatorio
        $user = $this->auth_user;

        $perfil = $this->Perfil_model->obtener($user['id_usuario']);

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode($perfil));
    }

    // ✏️ EDITAR MI PERFIL
    public function actualizar()
    {
        // 🔐 JWT obligatorio
        $user = $this->auth_user;

        $input = json_decode($this->input->raw_input_stream, true);

        if (!$input) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'error' => 'JSON inválido'
                ]));
        }

        // Obtener id_persona
        $id_persona = $this->Perfil_model
            ->obtener_id_persona($user['id_usuario']);

        // Datos persona
        $data_persona = array_intersect_key($input, array_flip([
            'nombres',
            'apellidos',
            'fecha_nacimiento',
            'genero',
            'telefono',
            'pais',
            'ciudad'
        ]));

        if (!empty($data_persona)) {
            $this->Perfil_model
                ->actualizar_persona($id_persona, $data_persona);
        }

        // Datos usuario
        $data_usuario = array_intersect_key($input, array_flip([
            'email',
            'nombre_usuario'
        ]));

        if (!empty($data_usuario)) {
            $this->Perfil_model
                ->actualizar_usuario($user['id_usuario'], $data_usuario);
        }

        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'success' => true,
                'message' => 'Perfil actualizado correctamente'
            ]));
    }
}