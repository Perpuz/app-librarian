<?php

namespace App\Controllers;

class Admin extends BaseController
{
    public function dashboard()
    {
        return view('dashboard');
    }

    public function books()
    {
        return view('books');
    }

    public function members()
    {
        return view('members');
    }

    public function transactions()
    {
        return view('transactions');
    }
}
