<?php
namespace Lib;
use Controllers\ErrorController;
// para almacenar las rutas que configuremos desde el archivo index.php
class Router
{

    private static $routes = [];
    //para ir añadiendo los métodos y las rutas en el tercer parámetro.
    public static function add(string $method, string $action, callable $controller): void
    {
        //die($action);
        $action = trim($action, '/');

        self::$routes[$method][$action] = $controller;

    }

    // Este método se encarga de obtener el sufijo de la URL que permitirá seleccionar
    // la ruta y mostrar el resultado de ejecutar la función pasada al metodo add para esa ruta
    // usando call_user_func()
    public static function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
    
        // Eliminar solo el prefijo sin afectar los parámetros GET
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = str_replace('/Instituto/DWES/TiendaJWT/', '', $uri);
        $uri = trim($uri, '/');
    
        $matched = false;
    
        foreach (self::$routes[$method] as $route => $callback) {
            // Convertir los parámetros dinámicos en la URL
            $pattern = preg_replace('/:([^\/]+)/', '(?<$1>[^/]+)', $route);
            $pattern = "#^{$pattern}$#";
    
            if (preg_match($pattern, $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY); // Extraemos solo los parámetros nombrados
    
                // Combinar los parámetros de la URL con los parámetros GET
                $params = array_merge($params, $_GET); // Incluimos los parámetros GET si los hay (por ejemplo, los de PayPal)
    
                // Asegurarnos de pasar los parámetros correctamente como valores individuales
                echo call_user_func_array($callback, array_values($params));  // Usamos array_values para pasar los parámetros de forma adecuada
    
                $matched = true;
                break;
            }
        }
    
        if (!$matched) {
            ErrorController::error404();
        }
    }
    

    
}


