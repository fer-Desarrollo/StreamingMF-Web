<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

    public function index()
    {
        $this->load->view('layout/header');
        $this->load->view('dashboard');
        $this->load->view('layout/footer');
    }
}