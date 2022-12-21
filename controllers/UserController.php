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
            
            $path = "user/show.twig";
            dislayTemplate($path, ['user' => $bean, 'posts' => $arrPost]);
        }
    }

    function edit()
    {
        $this->authorizeUser();
        // save new record & redirect it to show method
        if (!empty($_POST['name']) && !empty($_POST['description'])) {
            $user = R::dispense("users");
            $user->name = $_POST['name'];
            $user->description = $_POST['description'];
            R::store($user);

            // redirecting to show method to display the new user
            $id = $user->id;
            $redirect = "./show?id=$id";
            header("Location: $redirect");
        } else {
            $errorNumber = http_response_code(404);
            $errorMessage = "Fill in all the fields";
            error($errorNumber, ['error' => $errorMessage]);
        }
    }


    function editPost()
    {
        $this->authorizeUser();
        // update record & redirect it to show method

        if (!empty($_POST['name']) || !empty($_POST['description'])) {
            $id = $_POST['id'];

            // updating the chosen users
            $user = R::load('users', $id);
            $user->name = $_POST['name'];
            $user->description = $_POST['description'];
            R::store($user);

            // redirecting to show method to display the new user
            $id = $user->id;
            $redirect = "./show?id=$id";
            header("Location: $redirect");
        } else {
            $errorNumber = http_response_code(404);
            $errorMessage = "Fill in all the fields";
            error($errorNumber, ['error' => $errorMessage]);
        }
    }

    function delete($id)
    {
        // destroy user
    }
}
