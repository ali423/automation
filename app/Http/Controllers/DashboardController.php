<?php

namespace App\Http\Controllers;


class DashboardController extends Controller
{

    public function __construct()
    {
        $this->shareView();
    }

    public function index(){
        return view('dashboard.index');
    }
}
