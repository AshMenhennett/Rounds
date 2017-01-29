<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

class AdminDashboardController extends \App\Http\Controllers\Controller
{

    /**
     * Displays Admin Dashboard.
     *
     * @return Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.home');
    }

}
