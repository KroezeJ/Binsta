<?php

use RedBeanPHP\R as R;

class BaseController
{
    public function getBeanById($typeOfBean, $queryStringKey)
    {
        $bean = R::findOne($typeOfBean, 'id = ?', [$queryStringKey]);
        if (!$bean) {
            error(404, "recipe with ID " . $queryStringKey . ' not found');
        }
        return $bean;
    }

    public function render($twig, $item, ?array $data)
    {
        if (isset($data)) {
            $content = $twig->render($item, $data);
        } else {
            $content = $twig->render($item);
        }
        echo $content;
    }

    public function dbconnect()
    {
        $host = 'localhost';
        $dbname = 'binsta';
        $username = 'bit_academy';
        $password = 'bit_academy';

        if (!R::testConnection()) {
            R::setup("mysql:host=$host;dbname=$dbname", $username, $password);
        }
    }

    public function like()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /user/login');
            die();
        }
        $post = R::findOne('posts', 'id = ?', [$_GET['id']]);
        $post->likes += 1;
        R::store($post);
        header('Location: /feed');
    }
}