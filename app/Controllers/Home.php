<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): \CodeIgniter\HTTP\RedirectResponse
    {
        $users = db_connect()->table('users');

        return redirect()->to($users->countAllResults() === 0 ? '/setup' : '/dashboard');
    }
}
