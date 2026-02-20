<?php

class AuthController extends Controller
{
    private User $users;

    public function __construct()
    {
        $this->users = new User();
    }

    public function showLogin(): void
    {
        $this->view('auth/login');
    }

    public function login(): void
    {
        Session::start();
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            Session::flash('error', 'Invalid CSRF token.');
            $this->redirect('/login');
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $user = $this->users->findByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            Session::flash('error', 'Invalid credentials.');
            $this->redirect('/login');
        }

        session_regenerate_id(true);
        unset($user['password']);
        Session::set('user', $user);
        $this->redirect('/dashboard');
    }

    public function showRegister(): void
    {
        $this->view('auth/register');
    }

    public function register(): void
    {
        Session::start();
        if (!Csrf::validate($_POST['_csrf'] ?? null)) {
            Session::flash('error', 'Invalid CSRF token.');
            $this->redirect('/register');
        }

        $required = ['name', 'email', 'password', 'role'];
        foreach ($required as $field) {
            if (trim($_POST[$field] ?? '') === '') {
                Session::flash('error', 'All required fields must be filled.');
                $this->redirect('/register');
            }
        }

        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Invalid email address.');
            $this->redirect('/register');
        }

        if (strlen($_POST['password']) < 8) {
            Session::flash('error', 'Password must be at least 8 characters.');
            $this->redirect('/register');
        }

        $role = in_array($_POST['role'], ['user', 'orphanage'], true) ? $_POST['role'] : 'user';

        if ($this->users->findByEmail($_POST['email'])) {
            Session::flash('error', 'Email already registered.');
            $this->redirect('/register');
        }

        $this->users->create([
            'name' => trim($_POST['name']),
            'email' => trim($_POST['email']),
            'password' => $_POST['password'],
            'role' => $role,
            'address' => trim($_POST['address'] ?? ''),
            'latitude' => trim($_POST['latitude'] ?? ''),
            'longitude' => trim($_POST['longitude'] ?? ''),
        ]);

        Session::flash('success', 'Registration successful. Please login.');
        $this->redirect('/login');
    }

    public function logout(): void
    {
        Session::start();
        Session::destroy();
        $this->redirect('/login');
    }
}
