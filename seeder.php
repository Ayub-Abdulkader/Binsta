<?php

$path = realpath('./vendor/autoload.php');
include_once($path);

use RedBeanPHP\R as R;

// database connection
R::setup('mysql:host=localhost;dbname=binsta', 'bit_academy', 'bit_academy');

/* the user table have to have

1/ name
2/ email
3/ password
4/ profile picture
5/ bio
6/ quote

post should have to have 

1/ foreign key to user
2/ title
3/ code
4/ code highlighting
5/ themes
6/ caption

likes&comment table 

1/ foreignkey to post
2/ likes
3/ comment
*/
$users = [
    [
        'id' => 1,
        'name' => 'Ayub',
        'email' => 'ayubinsta@gmail.com',
        'password' => 'Ayub',
        'profile_pic' => 'Ayub.jpg',
        'bio' => 'full-stack developer',
        'quote' => 'Code is like humor. When you have to explain it, it’s bad.'
    ],
    [
        'id' => 2,
        'name' => 'John',
        'email' => 'johnbinsta@gmail.com',
        'password' => 'john',
        'profile_pic' => 'john.jpg',
        'bio' => 'front-end developer',
        'quote' => 'Googling like pro'
    ]

];

$posts = [
    [
        'id' => 1,
        'user_id' => 1,
        'code' => 
        '<h2 class="user">New kitchen</h2>
        <div class="f_field">
            <form action="#" id="form1" method="POST">
            <label for="name">Name</label><br>
            <input type="text" id="name" name="name"><br>
            <label for="description">Description</label><br>
            <textarea rows="4" cols="50" name="description" form="form1">Description here...</textarea>
            </form>
        <button class="button button2" type="submit" form="form1" value="Submit">Create</button>
        <a href="/kitchen" class="button button2">Back</a>
        </div>',
        'syntaxhighlight'=> 'HTML',
        'theme' => 'dark',
        'caption' => 'form in html',
        'date_posted' => '2022-09-05 13:56:00'
    ],
    [
        'id' => 2,
        'user_id' => 2,
        'code' => '* {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        li, a, .button {
            font-family: "Montserrat";
            font-weight: 500;
            font-size: 16px;
            color: #edf0f1;
            text-decoration: none;
        }
        .button {
            padding: 9px 25px;
            background-color: rgba(0, 136, 169, 1);
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.2s ease 0s;
        }',
        'syntaxhighlight'=> 'CSS',
        'theme' => 'light',
        'caption' => 'form in html',
        'date_posted' => '2022-09-06 03:36:00'
    ]

];

$post_reactions = [
    [
        'id' => 1,
        'post_id' => 1,
        'like' => 50,
        'comment' => 'cool, thanks for sharing'
    ],
    [
        'id' => 3,
        'post_id' => 2,
        'like' => 15,
        'comment' => 'appreciate it'
    ],

];

R::wipe("users");

foreach ($users as $key=> $value) {
    $user = R::dispense("user");
    $user->name = $value['name'];
    $user->email = $value['email'];
    $user->password = password_hash($value['password'], PASSWORD_DEFAULT);
    $user->profile_pic = $value['profile_pic'];
    $user->bio = $value['bio'];
    $user->quote = $value['quote'];
    R::store($user);
}
print_r(R::count('user') . " users inserted ") . PHP_EOL;

R::wipe("posts");

foreach ($posts as $key=> $value) {
    $user = R::load('user', $value['user_id']);
    $post = R::dispense("posts");
    $post->code = $value['code'];
    $post->syntaxhighlight = $value['syntaxhighlight'];
    $post->theme = $value['theme'];
    $post->caption = $value['caption'];
    $post->date_posted = $value['date_posted'];
    $user->ownPostList[] = $post;
    R::store($user);
}
print_r(R::count('posts') . " posts inserted ") . PHP_EOL;

R::wipe("post_reactions");

foreach ($post_reactions as $key=> $value) {
    $post = R::load('posts', $value['post_id']);
    $reaction = R::dispense("reactions");
    $reaction->like = $value['like'];
    $reaction->comment = $value['comment'];
    $post->ownReactionList[] = $reaction;
    R::store($post);
}
print_r(R::count('reactions') . " reactions inserted ") . PHP_EOL;