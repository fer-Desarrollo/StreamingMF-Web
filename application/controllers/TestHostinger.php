<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TestHostinger extends CI_Controller {

    public function index()
    {
        // 🔴 CARGA CORRECTA DE SMTP
        $this->load->config('email');
        $this->load->library('email', $this->config->item('email'));
        $this->email->initialize($this->config->item('email'));

        $this->email->from(
            'streamingmf@mediumvioletred-kudu-220345.hostingersite.com',
            'Streaming MF'
        );

        // envíatelo a ti mismo
        $this->email->to('streamingmf@mediumvioletred-kudu-220345.hostingersite.com');
        $this->email->subject('Prueba SMTP Hostinger');
        $this->email->message('<h2>SMTP Hostinger funcionando ✅</h2>');

        if ($this->email->send()) {
            echo '✅ CORREO ENVIADO CORRECTAMENTE';
        } else {
            echo '<pre>';
            echo $this->email->print_debugger();
            echo '</pre>';
        }
    }
}