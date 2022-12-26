<?php

use RedBeanPHP\R as R;

// database connection
R::setup('mysql:host=localhost;dbname=binsta', 'bit_academy', 'bit_academy');

class UserController extends BaseController
{
    function login()
    {
        session_destroy();
        $path = "user/login.twig";
        // rendering value to view
        dislayTemplate($path);
    }

    function loginPost()
    {
        if (empty($_POST['email']) || empty($_POST['password'])) {
            $errorMessage = "Please, Fill in all the fields";
            $path = "../user/login.twig";
            dislayTemplate($path, ['error' => $errorMessage]);
            exit;
        } else {
            $email = $_POST['email'];
            $password = $_POST['password'];
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                dislayTemplate('user/login.twig', ['error' => 'Invalid email']);
                exit;
              }
              
            $user = R::findOne("user", 'email = ?', ["$email"]);

            if (!empty($user)) {
                $hash_pass = $user->password;
                if (password_verify($password, $hash_pass)) {
                    $_SESSION['id'] = $user->id;
                    $_SESSION['name'] = $user->name;
                    $_SESSION['email'] = $user->email;
                    $_SESSION['profile'] = $user->profile_pic;

                    header("Location: /");
                    exit;
                } else {
                    $errorMessage = "Wrong password";
                    $path = "./user/login.twig";
                    dislayTemplate($path, ['error' => $errorMessage]);
                    exit;
                }
            } else {
                $errorMessage = "User don't exist";
                $path = "./user/login.twig";
                dislayTemplate($path, ['error' => $errorMessage]);
                exit;
            }
        }
    }

    function register()
    { 
        $path = "user/register.twig";
        // rendering value to view
        dislayTemplate($path);
    }

    function registerPost()
    {
        if (empty($_POST['email']) && empty($_POST['username']) && empty($_POST['psw']) && empty($_POST['pswre'])) {
            $errorMessage = "Please, Fill in all the fields";
            $path = "user/register.twig";
            dislayTemplate($path, ['error' => $errorMessage]);
            exit;
        }

        $name = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['psw'];
        $r_password = $_POST['pswre'];

        if ($password !== $r_password) {
            $errorMessage = "Passwords did not match";
            $path = "user/register.twig";
            dislayTemplate($path, ['error' => $errorMessage]);
            exit;
        }

        $user = R::findOne("user", 'email = ?', ["$email"]);

        if (!empty($user)) {
            $errorMessage = "Email is already taken";
            $path = "user/register.twig";
            dislayTemplate($path, ['error' => $errorMessage]);
            exit;
        } else {
            $user = R::dispense('user');
            $user->name = $name;
            $user->email = $email;
            $user->password = password_hash("$password", PASSWORD_DEFAULT);
            R::store($user);
            $_SESSION['id'] = $user->id;
            $_SESSION['name'] = $user->name;

            header("Location: /");
            exit;
        }
    }
    function show($id = null)
    {
        $this->authorizeUser();
        // check if argument is passed to method
        if (is_null($id)) {
            $errorNumber = http_response_code(404);
            $errorMessage = "No User ID specified";
            error($errorNumber, ['error' => $errorMessage]);
        } else { 
            $bean = $this->getBeanById('user', $id);

            if ($bean->id == 0) {
                $errorNumber = http_response_code(404);
                $errorMessage = "No User ID specified";
                error($errorNumber, ['error' => $errorMessage]);
                exit;
            }
            $arrPost = R::find('posts', "user_id = $id");
            $totalPost = count($arrPost);

            $path = "user/show.twig";
            dislayTemplate($path, ['user' => $bean, 'posts' => $arrPost, 'totalPost' => $totalPost]);
        }
    }

    function edit($id = null)
    {
        $this->authorizeUser();
        if (is_null($id)) {
            $errorNumber = http_response_code(404);
            $errorMessage = "No User ID specified";
            error($errorNumber, ['error' => $errorMessage]);
            exit;
        }
        $bean = $this->getBeanById('user', $id);
        // user is not allowed to edit another users database
        if ($bean->id == 0 || $bean->id !== $_SESSION['id']) {
            $errorNumber = http_response_code(404);
            $errorMessage = "No bean";
            error($errorNumber, ['error' => $errorMessage]);
            exit;
        }
        $_SESSION['id'] = $bean->id;
        $path = "user/edit.twig";
        dislayTemplate($path, ['user' => $bean]);
        exit;
    }


    function editPost()
    {
        $this->authorizeUser();
        // update record & redirect it to show method

        if (empty($_POST['email']) && empty($_POST['name']) && empty($_POST['quote']) && empty($_POST['bio'])) {
            $errorMessage = "Please, Fill in all the fields";
            $path = "user/edit.twig";
            dislayTemplate($path, ['error' => $errorMessage]);
            exit;
        }

        $id = $_SESSION['id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $quote = $_POST['quote'];
        $bio = $_POST['bio'];
        $profile = $_FILES['profile']['name'];
        
        // profile picture

        if (!$profile) {
            unset($profile);
            $profile = $_POST['old_profile'];
         } else if ($profile) {
            // upload profile
            if($_FILES['profile']['size'] > 500000) {
                $errorMessage = "Sorry! your file is too large";
                $path = "user/edit.twig";
                dislayTemplate($path, ['error' => $errorMessage]);
                exit;
            }
            if (move_uploaded_file($_FILES["profile"]["tmp_name"], "../images/people/" . $profile)) {
                //echo "<p>profile uploaded</p>";
            } else {
                $errorMessage = "Something went wrong, try again";
                $path = "user/edit.twig";
                dislayTemplate($path, ['error' => $errorMessage]);
                exit;
            }
        }
        $bean = R::findOne("user", 'email = ?', ["$email"]);
        
        if (!empty($bean) && $bean->id !== $id) {
            $errorMessage = "Email is already taken";
            $path = "user/edit.twig";
            dislayTemplate($path, ['error' => $errorMessage]);
            exit;
        } else {
            $user = R::load('user', $id);
            $user->name = $name;
            $user->email = $email;
            $user->profile_pic = $profile;
            $user->quote = $quote;
            $user->bio = $bio;
            R::store($user);

            $_SESSION['email'] = $user->email;
            $_SESSION['name'] = $user->name;
            $_SESSION['profile'] = $user->profile_pic;

            header("Location: /");
            exit;
        }
    }

    function delete($id)
    {
        // destroy user
    }
}
