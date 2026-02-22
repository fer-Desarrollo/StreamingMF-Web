<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'auth/splash';
$route['auth/login'] = 'auth/login';
$route['dashboard'] = 'dashboard/index';
$route['peliculas'] = 'peliculas/index';     
// Ruta para el Catálogo estilo Netflix
$route['catalogo'] = 'catalogo/index';
$route['usuarios/registrar'] = 'usuarios/registrar';
$route['usuarios'] = 'usuarios/index';
$route['peliculas/registrar'] = 'peliculas/registrar'; 
$route['api/auth/crear-admin']['post'] = 'api/auth/crear_admin';
$route['api/favoritos']['post'] = 'api/favoritos/agregar';
$route['api/peliculas/(:num)/miniatura']['post'] = 'api/peliculas/subir_miniatura/$1';
$route['api/perfil']['get'] = 'api/perfil/index';
$route['api/perfil']['put'] = 'api/perfil/actualizar';
$route['api/peliculas/(:num)']['get'] = 'api/peliculas/ver/$1';
$route['api/favoritos']['get'] = 'api/favoritos/index';
$route['api/peliculas']['post'] = 'api/peliculas/crear';
$route['api/auth/login']['post'] = 'api/auth/login';
$route['api/usuarios']['post'] = 'api/usuarios/crear';
$route['api/auth/cambiar-password']['post'] = 'api/auth/cambiar_password';
$route['api/peliculas/(:num)/miniatura']['get'] = 'api/peliculas/miniatura/$1';
$route['api/peliculas/(:num)']['put'] = 'api/peliculas/actualizar/$1';
$route['api/peliculas/(:num)/estado']['patch'] = 'api/peliculas/cambiar_estado/$1';
$route['api/peliculas/admin']['get'] = 'api/peliculas/admin';
$route['api/usuarios/(:num)']['put'] = 'api/usuarios/actualizar/$1';
$route['api/usuarios/(:num)/estado']['patch'] = 'api/usuarios/cambiar_estado/$1';
$route['api/usuarios/admin']['get'] = 'api/usuarios/admin';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
