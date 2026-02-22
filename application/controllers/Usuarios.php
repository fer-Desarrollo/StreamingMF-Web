<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Usuarios extends MY_Controller {

    public function registrar()
    {
        $this->load->view('layout/header');
        $this->load->view('usuarios/registrar'); // La vista que acabamos de crear
        $this->load->view('layout/footer');
    }
    
    // Y para el listado que me imagino que haremos después:
    public function index()
    {
        $this->load->view('layout/header');
        $this->load->view('usuarios/index'); 
        $this->load->view('layout/footer');
    }
}