<?php

require_once 'baseController.php';
use RedBeanPHP\R as R;

class FeedController extends BaseController
{
    public function __construct()
    {
        BaseController::dbconnect();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newpost'])) {
            $this->newpostPost();
            return;
        }
    }

    public function index($twig)
    {
        $posts = R::findAll('posts', 'ORDER BY posted_at DESC');
        $data = ['posts' => $posts];
        foreach ($posts as $post) {
            $comments_count = R::count('comments', 'post_id = ?', [$post->id]);
            $data['posts'][$post->id]['comments_count'] = $comments_count;

            $user = R::findOne('users', 'id = ?', [$post->user_id]);
            $data['posts'][$post->id]['userpic'] = $user->profile_pic;
            $data['posts'][$post->id]['username'] = $user->username;
            $postedAt = date_create($data['posts'][$post->id]['posted_at']);
            $now = date_create('now');
            $timeDiff = date_diff($postedAt, $now);

            if ($timeDiff->y > 0) {
                $timeAgo = $timeDiff->format('%y years ago');
            } elseif ($timeDiff->m > 0) {
                $timeAgo = $timeDiff->format('%m months ago');
            } elseif ($timeDiff->d > 0) {
                $timeAgo = $timeDiff->format('%d days ago');
            } elseif ($timeDiff->h > 0) {
                $timeAgo = $timeDiff->format('%h hours ago');
            } else {
                $timeAgo = $timeDiff->format('%i minutes ago');
            }
            $data['posts'][$post->id]['posted_at'] = $timeAgo;
        }

        BaseController::render($twig, 'feed/index.twig', $data);
    }

    public function newpost($twig)
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /user/login');
            die();
        }
        BaseController::render($twig, 'feed/newpost.twig', []);
    }

    public function newpostPost()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /user/login');
            die();
        }
        $user = R::findOne('users', 'id = ?', [$_SESSION['user']->id]);
        $post = R::dispense('posts');
        $post->user_id = $user->id;
        $post->language = $_POST['language'];
        $post->code = $_POST['code'];
        $post->description = $_POST['description'];
        $post->posted_at = date('Y-m-d H:i:s');
        $id = R::store($post);
        header('Location: /feed');
    }

    public function like()
    {
        BaseController::like();
    }
}