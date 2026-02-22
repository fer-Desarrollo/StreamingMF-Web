<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->model(['Usuario_model','Persona_model']);

        // ✅ Email: carga automática del config
        $this->load->library('email');

        header('Content-Type: application/json');
    }

    public function crear()
    {
        require_role(['admin']);

        $input = json_decode($this->input->raw_input_stream, true);

        if (!$input) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['error' => 'JSON inválido']));
        }

        if ($this->Usuario_model->existe($input['email'], $input['nombre_usuario'])) {
            return $this->output
                ->set_status_header(409)
                ->set_output(json_encode(['error' => 'Usuario existe']));
        }

        // 🔐 Generar password temporal
        $password = substr(
            str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789!@#$'),
            0,
            10
        );

        // 1️⃣ Crear persona
        $id_persona = $this->Persona_model->crear([
            'nombres'   => $input['nombres'],
            'apellidos' => $input['apellidos']
        ]);

        // 2️⃣ Crear usuario
        $this->Usuario_model->crear([
            'id_persona'       => $id_persona,
            'id_rol'           => 3, // suscriptor
            'email'            => $input['email'],
            'nombre_usuario'   => $input['nombre_usuario'],
            'password_hash'    => password_hash($password, PASSWORD_BCRYPT),
            'password_temporal'=> 1,
            'activo'           => 1,
            'email_verificado' => 0
        ]);

        // ✉️ ENVIAR CORREO (HOSTINGER REAL)
        $this->email->from(
            'streamingmf@mediumvioletred-kudu-220345.hostingersite.com',
            'Streaming MF'
        );

        $this->email->to($input['email']);
        $this->email->subject('Acceso a la plataforma');

        $this->email->message("
            <h3>Bienvenido {$input['nombres']}</h3>
            <p><b>Usuario:</b> {$input['nombre_usuario']}</p>
            <p><b>Contraseña temporal:</b> {$password}</p>
            <p>Debes cambiarla al iniciar sesión.</p>
        ");

        // 🔴 SI FALLA, DEVUELVE ERROR REAL
        if (!$this->email->send()) {
            return $this->output
                ->set_status_header(500)
                ->set_output(json_encode([
                    'error' => 'Email no enviado',
                    'debug' => $this->email->print_debugger()
                ]));
        }

        return $this->output
            ->set_status_header(201)
            ->set_output(json_encode([
                'success' => true,
                'message' => 'Usuario creado y correo enviado'
            ]));
    }
}