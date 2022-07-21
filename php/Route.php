<?php
// from https://github.com/steampixel/simplePHPRouter
class Route
{

  private static $routes = array();
  private static $pathNotFound = null;
  private static $methodNotAllowed = null;

  public static function add($expression, $function, $method = 'any', $visibility = 'public')
  {
    array_push(self::$routes, array(
      'expression' => $expression,
      'function' => $function,
      'method' => $method,
      'visibility' => $visibility
    ));
  }
  public static function get($expression, $function, $visibility = 'public')
  {
    array_push(self::$routes, array(
      'expression' => $expression,
      'function' => $function,
      'method' => 'get',
      'visibility' => $visibility
    ));
  }

  public static function post($expression, $function, $visibility = 'public')
  {
    array_push(self::$routes, array(
      'expression' => $expression,
      'function' => $function,
      'method' => 'post',
      'visibility' => $visibility
    ));
  }
  
  public static function getAll(){
    return self::$routes;
  }

  public static function pathNotFound($function)
  {
    self::$pathNotFound = $function;
  }

  public static function methodNotAllowed($function)
  {
    self::$methodNotAllowed = $function;
  }

  public static function run($basepath = '/')
  {
    // Parse current url
    $parsed_url = parse_url($_SERVER['REQUEST_URI']); //Parse Uri

    if (isset($parsed_url['path'])) {
      $path = $parsed_url['path'];
    } else {
      $path = '/';
    }
    // Get current request method
    $method = $_SERVER['REQUEST_METHOD'];

    $path_match_found = false;

    $route_match_found = false;

    foreach (self::$routes as $route) {


      // If the method matches check the path

      // Add basepath to matching string
      if ($basepath != '' && $basepath != '/') {
        $route['expression'] = '(' . $basepath . ')' . $route['expression'];
      }

      // Add 'find string start' automatically
      $route['expression'] = '^' . $route['expression'];

      // Add 'find string end' automatically
      $route['expression'] = $route['expression'] . '$';

      // echo $route['expression'].'<br/>';

      // Check path match	
      if (preg_match('#' . $route['expression'] . '#', $path, $matches)) {

        $path_match_found = true;

        // Check method match
        if ($method == 'any' || strtolower($method) == strtolower($route['method'])) {

          array_shift($matches); // Always remove first element. This contains the whole string

          if ($basepath != '' && $basepath != '/') {
            array_shift($matches); // Remove basepath
          }
          
          // check if route is only allowed for logged-in users
         if ($route['visibility'] === 'login' && (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true)) {
            // redirect to login
            header("Location: " . ROOTPATH . "/user/login?redirect=" . $_SERVER['REQUEST_URI']);
            die();
            break;
          } elseif ($route['visibility'] === 'admin' && (!isset($_SESSION['username']) || $_SESSION['username'] !== 'juk20')) {
            // pages are only visible for me
            $path_match_found = false;
            break;
          }


          call_user_func_array($route['function'], $matches);

          $route_match_found = true;

          // Do not check other routes
          break;
        }
      }
    }

    // No matching route was found
    if (!$route_match_found) {

      // But a matching path exists
      if ($path_match_found) {
        header("HTTP/1.0 405 Method Not Allowed");
        if (self::$methodNotAllowed) {
          call_user_func_array(self::$methodNotAllowed, array($path, $method));
        }
      } else {
        header("HTTP/1.0 404 Not Found");
        if (self::$pathNotFound) {
          call_user_func_array(self::$pathNotFound, array($path));
        }
      }
    }
  }
}
