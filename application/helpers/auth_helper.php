<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function require_role($roles)
{
    $CI =& get_instance();
    if (!in_array($CI->auth_user['rol'], $roles)) {
        header('Content-Type: application/json');
        http_response_code(403);
        echo json_encode(['error' => 'No autorizado']);
        exit;
    }
}