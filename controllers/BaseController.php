<?php

use RedBeanPHP\R as R;

class BaseController
{
    public function getBeanById($typeOfBean, $queryStringKey) 
    {
        //  get value from database
        $bean = R::load($typeOfBean, $queryStringKey);
        return $bean;
    }

    public function authorizeUser()
    {
        if (!isset($_SESSION['id']) && !isset($_SESSION['username'])) {
            header('location: /user/login');
            exit;
        }
    }
}
