<?php

require_once 'baseController.php';
use RedBeanPHP\R as R;

class PostController extends BaseController
{
    public function __construct()
    {
        BaseController::dbconnect();

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
            $this->commentPost();
            return;
        }
    }

    public function show($twig)
    {
        $post = R::findOne('posts', 'id = ?', [$_GET['id']]);
        $comments = R::findAll('comments', 'post_id = ?', [$_GET['id']]);
        $user = R::findOne('users', 'id = ?', [$post->user_id]);
        $data = ['post' => $post, 'comments' => $comments, 'user' => $user];
        foreach ($data["comments"] as $comment) {
            $commentUser = R::findOne('users', 'id = ?', [$comment->user_id]);
            $data['comments'][$comment->id]['username'] = $commentUser->username;
        }
        $postedAt = date_create($data['post']['posted_at']);
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
        $data['post']['posted_at'] = $timeAgo;

        BaseController::render($twig, 'feed/show.twig', $data);
    }

    public function commentPost()
    {
        if (!isset($_SESSION['user'])) {
            header('Location: /user/login');
            die();
        }
        $comment = R::dispense('comments');
        $comment->user_id = $_SESSION['user']->id;
        $comment->post_id = $_POST['post_id'];
        $comment->content = $_POST['comment'];
        $comment->commented_at = date('Y-m-d H:i:s');
        R::store($comment);
        header('Location: /post/show?id=' . $_POST['post_id']);
    }

    public function like()
    {
        BaseController::like();
    }
}