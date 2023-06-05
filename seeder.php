<?php

require_once 'vendor/autoload.php';

use RedBeanPHP\R as R;

$host = 'localhost';
$dbname = 'binsta';
$username = 'bit_academy';
$password = 'bit_academy';

// Check if a connection is already established
if (!R::testConnection()) {
    R::setup("mysql:host=$host;dbname=$dbname", $username, $password);
}

R::wipe('users');
R::wipe('posts');
R::wipe('comments');

R::exec("
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL,
        password VARCHAR(255) NOT NULL,
        profile_pic VARCHAR(255),
        bio TEXT,
        full_name VARCHAR(255),
        created_at DATETIME,
        is_admin BOOLEAN DEFAULT 0
    )
");

R::exec("
    CREATE TABLE IF NOT EXISTS posts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        code TEXT,
        user_id INT NOT NULL,
        likes INT DEFAULT 0,
        posted_at DATETIME,
        description TEXT,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )
");

R::exec("
    CREATE TABLE IF NOT EXISTS comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        content TEXT,
        user_id INT NOT NULL,
        post_id INT NOT NULL,
        likes INT DEFAULT 0,
        commented_at DATETIME,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (post_id) REFERENCES posts(id)
    )
");

// Sample data for users table
$users = [
    [
        'username' => 'john_doe',
        'password' => password_hash('password123', PASSWORD_DEFAULT),
        'profile_pic' => 'profile1.jpg',
        'bio' => 'Hello, I am John Doe!',
        'full_name' => 'John Doe',
        'created_at' => date('Y-m-d H:i:s'),
        'is_admin' => false,
    ],
    [
        'username' => 'jane_smith',
        'password' => password_hash('password456', PASSWORD_DEFAULT),
        'profile_pic' => 'profile2.jpg',
        'bio' => 'Nice to meet you!',
        'full_name' => 'Jane Smith',
        'created_at' => date('Y-m-d H:i:s'),
        'is_admin' => false,
    ],
    [
        'username' => 'admin',
        'password' => password_hash('admin123', PASSWORD_DEFAULT),
        'profile_pic' => 'admin.jpg',
        'bio' => 'Administrator account',
        'full_name' => 'Admin',
        'created_at' => date('Y-m-d H:i:s'),
        'is_admin' => true,
    ],
    [
        'username' => 'test_user1',
        'password' => password_hash('test123', PASSWORD_DEFAULT),
        'profile_pic' => 'profile3.jpg',
        'bio' => 'Testing account 1',
        'full_name' => 'Test User 1',
        'created_at' => date('Y-m-d H:i:s'),
        'is_admin' => false,
    ],
    [
        'username' => 'test_user2',
        'password' => password_hash('test456', PASSWORD_DEFAULT),
        'profile_pic' => 'profile4.jpg',
        'bio' => 'Testing account 2',
        'full_name' => 'Test User 2',
        'created_at' => date('Y-m-d H:i:s'),
        'is_admin' => false,
    ],
];

foreach ($users as $user) {
    $userEntity = R::dispense('users');
    $userEntity->username = $user['username'];
    $userEntity->password = $user['password'];
    $userEntity->profile_pic = $user['profile_pic'];
    $userEntity->bio = $user['bio'];
    $userEntity->full_name = $user['full_name'];
    $userEntity->created_at = $user['created_at'];
    $userEntity->is_admin = $user['is_admin'];
    R::store($userEntity);
}

// Sample data for posts table
$posts = [
    [
        'code' => 'ABC123',
        'user_id' => 1,
        'likes' => 10,
        'language' => 'php',
        'posted_at' => date('Y-m-d H:i:s'),
        'description' => 'This is my first post!',
    ],
    [
        'code' => 'DEF456',
        'user_id' => 2,
        'likes' => 5,
        'language' => 'php',
        'posted_at' => date('Y-m-d H:i:s'),
        'description' => 'Just a random post.',
    ],
    [
        'code' => 'GHI789',
        'user_id' => 3,
        'likes' => 15,
        'language' => 'php',
        'posted_at' => date('Y-m-d H:i:s'),
        'description' => 'Admin post.',
    ],
    [
        'code' => 'JKL012',
        'user_id' => 4,
        'likes' => 3,
        'language' => 'php',
        'posted_at' => date('Y-m-d H:i:s'),
        'description' => 'Test post 1.',
    ],
    [
        'code' => 'MNO345',
        'user_id' => 5,
        'likes' => 8,
        'language' => 'php',
        'posted_at' => date('Y-m-d H:i:s'),
        'description' => 'Test post 2.',
    ],
];

foreach ($posts as $post) {
    $postEntity = R::dispense('posts');
    $postEntity->code = $post['code'];
    $postEntity->user_id = $post['user_id'];
    $postEntity->likes = $post['likes'];
    $postEntity->language = $post['language'];
    $postEntity->posted_at = $post['posted_at'];
    $postEntity->description = $post['description'];
    R::store($postEntity);
}

// Sample data for comments table
$comments = [
    [
        'content' => 'Great post!',
        'user_id' => 1,
        'post_id' => 1,
        'likes' => 5,
        'commented_at' => date('Y-m-d H:i:s'),
    ],
    [
        'content' => 'Nice photo!',
        'user_id' => 2,
        'post_id' => 1,
        'likes' => 3,
        'commented_at' => date('Y-m-d H:i:s'),
    ],
    [
        'content' => 'Keep up the good work!',
        'user_id' => 3,
        'post_id' => 2,
        'likes' => 7,
        'commented_at' => date('Y-m-d H:i:s'),
    ],
    [
        'content' => 'Interesting caption!',
        'user_id' => 4,
        'post_id' => 3,
        'likes' => 2,
        'commented_at' => date('Y-m-d H:i:s'),
    ],
    [
        'content' => 'Beautiful photo!',
        'user_id' => 5,
        'post_id' => 4,
        'likes' => 4,
        'commented_at' => date('Y-m-d H:i:s'),
    ],
];

foreach ($comments as $comment) {
    $commentEntity = R::dispense('comments');
    $commentEntity->content = $comment['content'];
    $commentEntity->user_id = $comment['user_id'];
    $commentEntity->post_id = $comment['post_id'];
    $commentEntity->likes = $comment['likes'];
    $commentEntity->commented_at = $comment['commented_at'];
    R::store($commentEntity);
}

echo "Seeder executed successfully.";

die();
