<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->model(['Usuario_model','Persona_model']);
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

 
        $password = substr(
            str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZ23456789!@#$'),
            0,
            10
        );

        // Crear persona
        $id_persona = $this->Persona_model->crear([
            'nombres'   => $input['nombres'],
            'apellidos' => $input['apellidos']
        ]);

        //  Crear usuario
        $this->Usuario_model->crear([
            'id_persona'        => $id_persona,
            'id_rol'            => 3,
            'email'             => $input['email'],
            'nombre_usuario'    => $input['nombre_usuario'],
            'password_hash'     => password_hash($password, PASSWORD_BCRYPT),
            'password_temporal' => 1,
            'activo'            => 1,
            'email_verificado'  => 0
        ]);

        // Inicializar Brevo SMTP
        $this->email->initialize([
            'protocol'    => 'smtp',
            'smtp_host'   => 'smtp-relay.brevo.com',
            'smtp_user'   => 'a2f1a0001@smtp-brevo.com',
            'smtp_pass'   => 'RHxrmKkEU51I4J7C',
            'smtp_port'   => 587,
            'smtp_crypto' => 'tls',
            'mailtype'    => 'html',
            'charset'     => 'utf-8',
            'newline'     => "\r\n",
            'crlf'        => "\r\n",
        ]);

        //  Cambia esto por el correo con el que te registraste en Brevo
        $this->email->from('daysgone8336@gmail.com', 'Streaming MF');
        $this->email->to($input['email']);
        $this->email->subject('Acceso a la plataforma');
        $this->email->message("
            <h3>Bienvenido {$input['nombres']}</h3>
            <p><b>Usuario:</b> {$input['nombre_usuario']}</p>
            <p><b>Contraseña temporal:</b> {$password}</p>
            <p>Debes cambiarla al iniciar sesión.</p>
        ");

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
    
    public function actualizar($id_usuario)
    {
        require_role(['admin']);
    
        $input = json_decode($this->input->raw_input_stream, true);
    
        if (!$input) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['error' => 'JSON inválido']));
        }
    
        $usuario = $this->Usuario_model->obtener_con_persona($id_usuario);
    
        if (!$usuario) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode(['error' => 'Usuario no encontrado']));
        }
    
        /* ========= PERSONA ========= */
        $campos_persona = [
            'nombres',
            'apellidos',
            'fecha_nacimiento',
            'genero',
            'telefono',
            'pais',
            'ciudad'
        ];
    
        $data_persona = array_intersect_key(
            $input,
            array_flip($campos_persona)
        );
    
        if (!empty($data_persona)) {
            $this->Usuario_model
                ->actualizar_persona($usuario->id_persona, $data_persona);
        }
    
        /* ========= USUARIO ========= */
        $campos_usuario = [
            'email',
            'nombre_usuario',
            'id_rol',
            'activo'
        ];
    
        $data_usuario = array_intersect_key(
            $input,
            array_flip($campos_usuario)
        );
    
        if (!empty($data_usuario)) {
            $this->Usuario_model
                ->actualizar_usuario($id_usuario, $data_usuario);
        }
    
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'success' => true,
                'message' => 'Usuario actualizado correctamente'
            ]));
    }
    
    public function cambiar_estado($id_usuario)
    {
        require_role(['admin']);
    
        $input = json_decode($this->input->raw_input_stream, true);
    
        if (!isset($input['activo'])) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'error' => 'Debe enviar activo (0 o 1)'
                ]));
        }
    
        $this->Usuario_model->cambiar_estado(
            $id_usuario,
            (int) $input['activo']
        );
    
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'success' => true,
                'activo'  => (int) $input['activo']
            ]));
    }
   
   public function admin()
    {
        require_role(['admin']);
    
        $usuarios = $this->Usuario_model->listar_admin();
    
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode($usuarios));
    }
    
}