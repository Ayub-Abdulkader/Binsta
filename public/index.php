<?php

require_once realpath('../vendor/autoload.php');

session_start();

if (isset($_GET['url'])) {
    $url = explode("/", $_GET['url']);
    $controllerName = ucfirst($url[0]) . "Controller";
    
    // if only controller exist call index
    if (isset($url[0]) && !isset($url[1])) {
        $controllerName = ucfirst($url[0]) . "Controller";
        $filename = ucfirst($url[0]) . 'Controller.php';
        // check if controller exist
        if (file_exists("../controllers/$filename")) {
            $controller = new $controllerName();
            return $controller->index();
        } else {
            $errorNumber = http_response_code(404);
            $errorMessage = "Controller '$controllerName' not found";
            error($errorNumber, ['error' => $errorMessage]);
        }
    }
    
    // if controller and method exist call both
    if (isset($url[0]) && isset($url[1])) {
        $filename = ucfirst($url[0]) . 'Controller.php';
        if (file_exists("../controllers/$filename")) {
            $controllerName = ucfirst($url[0]) . "Controller";
            $controller = new $controllerName();
            $methode = $url[1];
        } else {
            $errorNumber = http_response_code(404);
            $errorMessage = "Controller '$controllerName' not found";
            error($errorNumber, ['error' => $errorMessage]);
            exit;
        }
        // check if methode exist
        if (method_exists($controllerName, $methode)) {
            // check the requested method
            //$methodServer = $_SERVER['REQUEST_METHOD'];
            // check if there is id given
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                return $controller->$methode($id);
            } else {
                return $controller->$methode();
            }
        } else {
            $errorNumber = http_response_code(404);
            $errorMessage = "Method '$url[1]' not found";
            error($errorNumber, ['error' => $errorMessage]);
        }
    }
} else {
    $controller = new PostController();
    return $controller->index();
}