<?php

use RedBeanPHP\R as R;

class BaseController
{
    public function getBeanById($typeOfBean, $queryStringKey) 
    {
        // query to get value from database
        $bean = R::load($typeOfBean, $queryStringKey);
        return $bean;
        /*
        if (!$bean->id) { 
            $errorNumber = http_response_code(404);
            $errorMessage = "No $typeOfBean with ID $queryStringKey found";
            error($errorNumber, ['error' => $errorMessage]);
        } else {
            // display it on twig
            $path = "$typeOfBean/show.twig";
            $arrRecipe = array();
            // assigin array of recipe to variable
            foreach ($bean->ownRecipesList as $recipe) {
                $arrRecipe[] = $recipe;
            }
            
            $loader = new \Twig\Loader\FilesystemLoader('./../views');
            $twig = new \Twig\Environment($loader);
            echo $twig->render($path, array('items' => $bean, 'recipes' => $arrRecipe));
        }*/
    }

    public function authorizeUser()
    {
        if (!isset($_SESSION['id']) && !isset($_SESSION['username'])) {
            header('location: /user/login');
            exit;
        }
    }
}