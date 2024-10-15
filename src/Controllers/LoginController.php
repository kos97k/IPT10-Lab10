<?php

namespace App\Controllers;

use App\Models\User;
use App\Controllers\BaseController;

class LoginController extends BaseController
{
    public function showForm()
    {
        // Check if user is already logged in
        session_start();
        if (isset($_SESSION['is_logged_in'])) {
            header('Location: /welcome');
            exit;
        }

        // Check failed attempts
        $disabled = isset($_SESSION['attempts']) && $_SESSION['attempts'] >= 3;
        $attempts = $disabled ? 0 : 3 - ($_SESSION['attempts'] ?? 0);

        $template = 'login-form';
        $data = [
            'title' => 'User Login',
            'disabled' => $disabled,
            'attempts' => $attempts
        ];

        return $this->render($template, $data);
    }

    public function login()
    {
        session_start();
        // Initialize attempts
        $_SESSION['attempts'] = $_SESSION['attempts'] ?? 0;

        if ($_SESSION['attempts'] >= 3) {
            // Redirect back to login form
            header('Location: /login-form');
            exit;
        }

        $user = new User();
        $username = $_POST['username'];
        $password = $_POST['password'];

        if ($user->login($username, $password)) {
            // Successful login
            $_SESSION['is_logged_in'] = true;
            $_SESSION['user_id'] = $username; // Use actual user ID if stored in the database
            $_SESSION['attempts'] = 0; // Reset attempts
            header('Location: /welcome');
            exit;
        } else {
            // Failed login
            $_SESSION['attempts']++;
            header('Location: /login-form');
            exit;
        }
    }

    public function logout()
    {
        session_start();
        session_destroy();
        header('Location: /login-form');
        exit;
    }
}
