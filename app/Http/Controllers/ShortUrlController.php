<?php

namespace App\Http\Controllers;


class ShortUrlController extends Controller
{
    public function index()
    {
        return view('shortUrlIndex');
    }
}
