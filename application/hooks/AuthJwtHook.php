<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AuthJwtHook {

    private $CI;

    private $public = [
        'api/auth/login',
        'api/auth/crear-admin'
    ];

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->library('jwt');
    }

    public function verificar()
    {
        $uri = $this->CI->uri->uri_string();

        // 🔴 SOLO PROTEGER API
        if (strpos($uri, 'api/') === false) {
            return;
        }

        // Permitir rutas públicas del API
        foreach ($this->public as $ruta) {
            if (strpos($uri, $ruta) !== false) {
                return;
            }
        }

        $headers = $this->CI->input->request_headers();

        $authHeader =
            $headers['Authorization'] ??
            $headers['authorization'] ??
            $_SERVER['HTTP_AUTHORIZATION'] ??
            null;

        if (!$authHeader) {
            $this->deny('Token requerido');
        }

        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $this->deny('Formato de token inválido');
        }

        try {
            $payload = $this->CI->jwt->decode($matches[1]);
        } catch (Exception $e) {
            $this->deny($e->getMessage());
        }

        $this->CI->auth_user = $payload;
    }

    private function deny($msg)
    {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['error' => $msg]);
        exit;
    }
}