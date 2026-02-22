<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Peliculas extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        $this->load->model('Pelicula_model');

        header('Content-Type: application/json');
    }

    public function crear()
    {
        // 🔐 Solo admin y moderador
        require_role(['admin', 'moderador']);

        $input = json_decode($this->input->raw_input_stream, true);

        if (!$input || empty($input['titulo'])) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'error' => 'El título es obligatorio'
                ]));
        }

        $data = [
            'titulo'          => $input['titulo'],
            'titulo_original' => $input['titulo_original'] ?? null,
            'sinopsis'        => $input['sinopsis'] ?? null,
            'anio_estreno'    => $input['anio_estreno'] ?? null,
            'duracion_min'    => $input['duracion_min'] ?? null,
            'clasificacion'   => $input['clasificacion'] ?? null,
            'idioma_original' => $input['idioma_original'] ?? null,
            'pais_produccion' => $input['pais_produccion'] ?? null,
            'url_trailer'     => $input['url_trailer'] ?? null,
            'url_video'       => $input['url_video'] ?? null,
            'destacada'       => $input['destacada'] ?? 0,
            'activa'          => 1
        ];

        // 1️⃣ Crear película
        $id_pelicula = $this->Pelicula_model->crear($data);

        // 2️⃣ Asignar géneros (opcional)
        if (!empty($input['generos']) && is_array($input['generos'])) {
            $this->Pelicula_model->asignar_generos(
                $id_pelicula,
                $input['generos']
            );
        }

        return $this->output
            ->set_status_header(201)
            ->set_output(json_encode([
                'success' => true,
                'id_pelicula' => $id_pelicula
            ]));
    }
    
    
    public function subir_miniatura($id_pelicula)
    {
        // 🔐 Solo admin y moderador
        require_role(['admin', 'moderador']);
    
        if (empty($_FILES['miniatura'])) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'error' => 'Archivo miniatura es obligatorio'
                ]));
        }
    
        $archivo = $_FILES['miniatura'];
    
        // Validar errores
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'error' => 'Error al subir el archivo'
                ]));
        }
    
        // Validar tamaño (máx 2MB)
        if ($archivo['size'] > 2 * 1024 * 1024) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'error' => 'La miniatura no debe superar 2MB'
                ]));
        }
    
        // Validar MIME
        $permitidos = ['image/jpeg', 'image/png', 'image/webp'];
        $mime = mime_content_type($archivo['tmp_name']);
    
        if (!in_array($mime, $permitidos)) {
            return $this->output
                ->set_status_header(415)
                ->set_output(json_encode([
                    'error' => 'Formato no permitido (jpg, png, webp)'
                ]));
        }
    
        // Leer binario
        $binario = file_get_contents($archivo['tmp_name']);
    
        // Guardar en BD
        $this->Pelicula_model->guardar_miniatura(
            $id_pelicula,
            $binario,
            $mime
        );
    
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'success' => true,
                'message' => 'Miniatura subida correctamente'
            ]));
    }
    
    public function miniatura($id_pelicula)
    {
        $pelicula = $this->db
            ->select('miniatura, miniatura_mime')
            ->where('id_pelicula', $id_pelicula)
            ->get('peliculas')
            ->row();
    
        if (!$pelicula || !$pelicula->miniatura) {
            show_404();
            return;
        }
    
        header('Content-Type: ' . $pelicula->miniatura_mime);
        echo $pelicula->miniatura;
    }
    
    
    public function index()
    {
        $peliculas = $this->Pelicula_model->listar();
    
        $resultado = [];
    
        foreach ($peliculas as $p) {
        $resultado[] = [
            'id_pelicula'   => $p->id_pelicula,
            'titulo'        => $p->titulo,
            'sinopsis'      => $p->sinopsis,
            'anio_estreno'  => $p->anio_estreno,
            'clasificacion' => $p->clasificacion,
            'duracion_min'  => $p->duracion_min,
            'destacada'     => (bool) $p->destacada,
        
            // 🎥 URLs
            'url_trailer'   => $p->url_trailer,
            'url_video'     => $p->url_video,
        
            // 🖼️ Imagen
            'miniatura_url' => base_url("api/peliculas/{$p->id_pelicula}/miniatura")
        ];
                }
    
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode($resultado));
    }
    
    
    public function ver($id_pelicula)
    {
    
        $p = $this->Pelicula_model->obtener($id_pelicula);
    
        if (!$p) {
            return $this->output
                ->set_status_header(404)
                ->set_output(json_encode([
                    'error' => 'Película no encontrada'
                ]));
        }
    
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'id_pelicula'        => $p->id_pelicula,
                'titulo'             => $p->titulo,
                'titulo_original'    => $p->titulo_original,
                'sinopsis'           => $p->sinopsis,
                'anio_estreno'       => $p->anio_estreno,
                'duracion_min'       => $p->duracion_min,
                'clasificacion'      => $p->clasificacion,
                'idioma_original'    => $p->idioma_original,
                'pais_produccion'    => $p->pais_produccion,
    
                // 🎥 URLs (como pediste)
                'url_trailer'        => $p->url_trailer,
                'url_video'          => $p->url_video,
    
                // 🖼️ Imagen
                'miniatura_url'      => base_url("api/peliculas/{$p->id_pelicula}/miniatura")
            ]));
    }
    
    public function actualizar($id_pelicula)
    {
        require_role(['admin', 'moderador']);
    
        $input = json_decode($this->input->raw_input_stream, true);
    
        if (!$input) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode(['error' => 'JSON inválido']));
        }
    
        $permitidos = [
            'titulo',
            'titulo_original',
            'sinopsis',
            'anio_estreno',
            'duracion_min',
            'clasificacion',
            'idioma_original',
            'pais_produccion',
            'url_trailer',
            'url_video',
            'destacada'
        ];
    
        $data = array_intersect_key($input, array_flip($permitidos));
    
        if (empty($data)) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'error' => 'No hay datos para actualizar'
                ]));
        }
    
        $this->Pelicula_model->actualizar($id_pelicula, $data);
    
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'success' => true,
                'message' => 'Película actualizada'
            ]));
    }
    
    public function cambiar_estado($id_pelicula)
    {
        require_role(['admin', 'moderador']);
    
        $input = json_decode($this->input->raw_input_stream, true);
    
        if (!isset($input['activa'])) {
            return $this->output
                ->set_status_header(400)
                ->set_output(json_encode([
                    'error' => 'Debe enviar activa (0 o 1)'
                ]));
        }
    
        $this->Pelicula_model->cambiar_estado(
            $id_pelicula,
            (int) $input['activa']
        );
    
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode([
                'success' => true,
                'estado'  => (int) $input['activa']
            ]));
    }
    
    public function admin()
    {
        require_role(['admin', 'moderador']);
    
        $peliculas = $this->Pelicula_model->listar_admin();
    
        foreach ($peliculas as $p) {
            $p->miniatura_url = base_url(
                "api/peliculas/{$p->id_pelicula}/miniatura"
            );
        }
    
        return $this->output
            ->set_status_header(200)
            ->set_output(json_encode($peliculas));
    }
    
}