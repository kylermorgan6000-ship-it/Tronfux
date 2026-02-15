<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TraderController extends Controller
{
    public function index() {
        return response()->json(['ok' => true]);
    }
}