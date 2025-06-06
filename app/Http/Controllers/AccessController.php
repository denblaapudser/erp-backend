<?php

namespace App\Http\Controllers;

use App\Models\UserAccess;
use Illuminate\Http\Request;

class AccessController extends Controller
{
    public function list(){
        return UserAccess::all();
    }
}
