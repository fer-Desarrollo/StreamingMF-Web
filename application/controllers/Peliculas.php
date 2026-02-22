<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Peliculas extends MY_Controller {

    // Método para cargar la vista del formulario (Registrar)
    public function registrar()
    {
        $this->load->view('layout/header');
        // Asegúrate de guardar el HTML que hicimos en: application/views/peliculas/registrar.php
        $this->load->view('peliculas/registrar'); 
        $this->load->view('layout/footer');
    }

    // Método para cargar la vista de la tabla (Consultar - que haremos después)
    public function index()
    {
        $this->load->view('layout/header');
        // Esta vista la crearemos a continuación: application/views/peliculas/index.php
        $this->load->view('peliculas/index');
        $this->load->view('layout/footer');
    }
}