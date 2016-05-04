<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class PublicController extends Controller
{
    public function homePage()
    {
        return view('welcome');
    }
    public function aboutPage()
    {
        return view('wechat.about');
    }
}
