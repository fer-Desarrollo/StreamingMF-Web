<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Favoritos extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->model('Favorito_model');

        header('Content-Type: application/json');
    }

    public function agregar()
    {
        // 🔐 JWT obligatorio (cualquier rol)
        $user = $this->auth_user;

        $input = json_decode($this->input->raw_input_stream, true);

        if (!$input) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['error' => 'JSON inválido']));
        }

        // Validar que venga película o serie
        if (empty($input['id_pelicula']) && empty($input['id_serie'])) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'error' => 'Debe enviar id_pelicula o id_serie'
                ]));
        }

        // ❌ No permitir ambos a la vez
        if (!empty($input['id_pelicula']) && !empty($input['id_serie'])) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'error' => 'Solo puede enviar película o serie'
                ]));
        }

        // Evitar duplicados
        if ($this->Favorito_model->existe(
            $user['id_usuario'],
            $input['id_pelicula'] ?? null,
            $input['id_serie'] ?? null
        )) {
            return $this->output
                ->set_status_header(409)
                ->set_output(json_encode([
                    'error' => 'Ya está en favoritos'
                ]));
        }

        // Guardar
        $this->Favorito_model->agregar([
            'id_usuario'  => $user['id_usuario'],
            'id_pelicula' => $input['id_pelicula'] ?? null,
            'id_serie'    => $input['id_serie'] ?? null
        ]);

        return $this->output
            ->set_status_header(201)
            ->set_output(json_encode([
                'success' => true,
                'message' => 'Agregado a favoritos'
            ]));
    }
    
    public function index()
    {
        // 🔐 JWT obligatorio (cualquier rol)
        $user = $this->auth_user;
    
        $lista = $this->Favorito_model->listar_por_usuario(
            $user['id_usuario']
        );
    
        $resultado = [];
    
        foreach ($lista as $item) {
    
            // 🎬 Película
            if ($item->id_pelicula) {
                $resultado[] = [
                    'tipo'           => 'pelicula',
                    'id'             => $item->id_pelicula,
                    'titulo'         => $item->pelicula_titulo,
                    'anio_estreno'   => $item->anio_estreno,
                    'clasificacion'  => $item->clasificacion,
                    'miniatura_url'  => base_url(
                        "api/peliculas/{$item->id_pelicula}/miniatura"
                    ),
                    'agregado_en'    => $item->agregado_en
                ];
            }
    
            // 📺 Serie
            if ($item->id_serie) {
                $resultado[] = [
                    'tipo'           => 'serie',
                    'id'             => $item->id_serie,
                    'titulo'         => $item->serie_titulo,
                    'miniatura_url'  => base_url(
                        "api/series/{$item->id_serie}/miniatura"
                    ),
                    'agregado_en'    => $item->agregado_en
                ];
            }
        }
    
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode($resultado));
    }
}