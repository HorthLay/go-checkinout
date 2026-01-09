<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
  public function qrmake(){
    return view("admin.qrmake");
  }
}
