<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Catalogo extends MY_Controller {

    public function index()
    {
        // 1. Cargamos el Header (menú de navegación)
        $this->load->view('layout/header');
        
        // 2. Cargamos la vista de Netflix que acabamos de crear
        // (Asegúrate de que el HTML anterior lo guardaste en: application/views/catalogo/index.php)
        $this->load->view('catalogo/index');
        
        // 3. Cargamos el Footer
        $this->load->view('layout/footer');
    }
}