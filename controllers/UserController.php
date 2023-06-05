<?php

use RedBeanPHP\R as R;
use RedUNIT\Base;

class UserController extends BaseController
{
    public function __construct()
    {
        BaseController::dbconnect();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
            $this->loginPost();
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
            $this->registerPost();
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
            $this->editPost();
            return;
        }
    }

    public function login($twig)
    {
        $invalid = isset($_GET['invalid']) ? $_GET['invalid'] : false;
        BaseController::render($twig, 'login/login.twig', ['invalid' => $invalid]);
    }

    public function loginPost()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $user = R::findOne('users', 'username = ?', [$username]);

        if ($user == null) {
            unset($_POST['login']);
            header('Location: /user/login?invalid=true');
            die();
        }

        if ($password == $user->password) {
            $_SESSION['user'] = $user;
            header('Location: /feed');
        } else {
            header('Location: /user/login?invalid=true');
        }
    }

    public function logout()
    {
        unset($_SESSION['user']);
        header('Location: ../../feed');
    }

    public function register($twig)
    {
        $invalidpass = isset($_GET['invalidpass']) ? $_GET['invalidpass'] : false;
        $invaliduser = isset($_GET['invaliduser']) ? $_GET['invaliduser'] : false;
        BaseController::render($twig, 'login/register.twig', ['invalidpass' => $invalidpass, 'invaliduser' => $invaliduser]);
    }

    public function registerPost()
    {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $password2 = $_POST['copassword'];

        if ($password !== $password2) {
            unset($_POST['register']);
            header('Location: /user/register?invalidpass=true');
            die();
        }

        $user = R::findOne('users', 'username = ?', [$username]);

        if ($user !== null) {
            unset($_POST['register']);
            header('Location: /user/register?invaliduser=true');
            die();
        }

        $user = R::dispense('users');
        $user->username = $username;
        $user->password = $password;
        $user->created_at = date('Y-m-d H:i:s');
        $id = R::store($user);
        $_SESSION['user'] = $id;
        header('Location: /feed');
    }

    public function profile($twig, $username)
    {
        $user = R::findOne('users', 'username = ?', [$username]);
        $user_id = $user->id;
        $posts = R::findAll('posts', 'user_id = ? ORDER BY posted_at DESC', [$user_id]);
        $data = [
            'user' => [
                'userpic' => $user->profile_pic,
                'username' => $user->username,
                'bio' => $user->bio,
                'fullname' => $user->full_name,
            ],
            'posts' => [],
        ];

        foreach ($posts as $post) {
            $comments_count = R::count('comments', 'post_id = ?', [$post->id]);
            $data['posts'][] = [
                'id' => $post->id,
                'language' => $post->language,
                'code' => $post->code,
                'description' => $post->description,
                'likes' => $post->likes,
                'comments_count' => $comments_count,
            ];
        }

        echo $twig->render('feed/profile.twig', ['user' => $data['user'], 'posts' => $data['posts']]);
    }

    public function edit($twig)
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /user/login');
            die();
        }
        $user = R::findOne('users', 'id = ?', [$_SESSION['user']->id]);
        BaseController::render($twig, 'feed/edit.twig', []);
    }

    public function editPost()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /user/login');
            die();
        }
        $user = R::findOne('users', 'id = ?', [$_SESSION['user']->id]);
        if (isset($_POST['fullname']) && $_POST['fullname'] != "") {
            $user->full_name = $_POST['fullname'];
        }
        if (isset($_POST['bio']) && $_POST['bio'] != "") {
            $user->bio = $_POST['bio'];
        }
        if (isset($_POST['username']) && $_POST['username'] != "") {
            $user->username = $_POST['username'];
        }
        if (isset($_POST['password']) && $_POST['password'] != "") {
            $user->password = $_POST['password'];
        }
        if (isset($_POST['profilepic']) && $_POST['profilepic'] != "") {
            $user->profile_pic = $_POST['profilepic'];
        }
        R::store($user);
        $_SESSION['user'] = $user;
        header('Location: /user/profile?name=' . $user->username);
    }
}