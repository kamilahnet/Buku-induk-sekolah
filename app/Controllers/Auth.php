<?php

namespace App\Controllers;

class Auth extends BaseController
{
    public function setup(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if (db_connect()->table('users')->countAllResults() > 0) {
            return redirect()->to('/login');
        }

        return view('auth/setup', ['title' => 'Buat akun administrator']);
    }

    public function createAdministrator(): \CodeIgniter\HTTP\RedirectResponse
    {
        if (db_connect()->table('users')->countAllResults() > 0) {
            return redirect()->to('/login');
        }

        foreach (['name', 'username', 'password', 'password_confirmation'] as $field) {
            if (! is_string($this->request->getPost($field))) {
                return redirect()->back()->with('error', 'Data akun tidak valid.');
            }
        }

        $rules = [
            'name'                  => 'required|min_length[2]|max_length[100]',
            'username'              => 'required|min_length[3]|max_length[50]|alpha_numeric_punct|is_unique[users.username]',
            'password'              => 'required|min_length[12]|max_length[255]',
            'password_confirmation' => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        db_connect()->table('users')->insert([
            'name'       => trim((string) $this->request->getPost('name')),
            'username'   => trim((string) $this->request->getPost('username')),
            'password'   => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'       => 'admin',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/login')->with('message', 'Administrator berhasil dibuat. Silakan masuk.');
    }

    public function login(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if (session()->get('user_id')) {
            return redirect()->to('/dashboard');
        }

        if (db_connect()->table('users')->countAllResults() === 0) {
            return redirect()->to('/setup');
        }

        return view('auth/login', ['title' => 'Masuk']);
    }

    public function authenticate(): \CodeIgniter\HTTP\RedirectResponse
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        if (! is_string($username) || ! is_string($password)) {
            return redirect()->back()->with('error', 'Nama pengguna atau kata sandi tidak sesuai.');
        }

        $username = trim($username);
        $user     = db_connect()->table('users')->where('username', $username)->get()->getRowArray();

        if ($user === null || ! password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Nama pengguna atau kata sandi tidak sesuai.');
        }

        session()->regenerate();
        session()->set([
            'user_id'   => $user['id'],
            'user_name' => $user['name'],
            'user_role' => $user['role'],
        ]);

        return redirect()->to('/dashboard');
    }

    public function logout(): \CodeIgniter\HTTP\RedirectResponse
    {
        session()->destroy();

        return redirect()->to('/login');
    }
}
