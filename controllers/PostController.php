<?php

use RedBeanPHP\R as R;

// database connection Ay
R::setup('mysql:host=localhost;dbname=binsta', 'bit_academy', 'bit_academy');


class PostController extends BaseController
{
    function index() 
    {
        $posts = R::findAll('posts', "ORDER BY id DESC");
        $reaction = R::findAll('reactions');
        $path = "posts/index.twig";
        dislayTemplate($path, ['posts' => $posts, 'reactions' => $reaction]);
    }

    function show($id = null)
    {
        $this->authorizeUser();
        // check if argument is passed to method
        if (is_null($id)) {
            $errorNumber = http_response_code(404);
            $errorMessage = "No post ID specified";
            error($errorNumber, ['error' => $errorMessage]);
        } else {
            $this->getBeanById('post', $id);
        }
    }

    function createPost()
    {
        $this->authorizeUser();
        // save new record & redirect it to show method
        if (!empty($_POST['content'])) {
            // needs improvement 
            $post = R::dispense("posts");
            $post->user_id = $_SESSION['id'];
            $post->code = $_POST['content'];
            $post->syntaxhighlight = $_POST['syntaxhighlight'];
            $post->theme = $_POST['theme'];
            $post->caption = $_POST['caption'];
            $post->date_posted = date('Y-m-d H:i:s');
            R::store($post);

            header("Location: /");
        } else {
            $errorNumber = http_response_code(404);
            $errorMessage = "Fill in all the fields";
            error($errorNumber, ['error' => $errorMessage]);
        }
    }

    function edit($id = null)
    {
        $this->authorizeUser();
        header("Location: /");
        // working on it...
        
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $post = $this->getBeanById('post', $id);
            if (!$post) {
                $errorNumber = http_response_code(404);
                $errorMessage = "No post ID specified";
                error($errorNumber, ['error' => $errorMessage]);
            }
            $path = "posts/edit.twig";
            dislayTemplate($path, ['post' => $post]);
        } else {
            $errorNumber = http_response_code(404);
            $errorMessage = "No post ID specified";
            error($errorNumber, ['error' => $errorMessage]);
        }
    }

    function editPost()
    {
        $this->authorizeUser();
        header("Location: /");
        // working on it
/*
        if (!empty($_POST['name']) || !empty($_POST['type']) || !empty($_POST['level'])) {
            $id = $_POST['id'];
            // updating the chosen posts
            $post = R::load('posts', $id);
            $post->name = $_POST['name'];
            $post->type = $_POST['type'];
            $post->level = $_POST['level'];
            $post->posts_id = $_POST['post'];
            R::store($post);

            // redirecting to show method to display the new post
            $id = $post->posts_id;
            $redirect = "/post/show?id=$id";
            header("Location: $redirect");
        } else {
            $errorNumber = http_response_code(404);
            $errorMessage = "Fill in all the fields";
            error($errorNumber, ['error' => $errorMessage]);
        }*/
    }

    function comment($id)
    {
        // get comments from post
    }

    function like($id)
    {
        // get likes from post
    }
}