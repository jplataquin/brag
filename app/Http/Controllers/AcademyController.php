<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AcademyController extends Controller
{
    /**
     * Show the Brag Academy page.
     */
    public function index()
    {
        return view('academy.index');
    }
}
