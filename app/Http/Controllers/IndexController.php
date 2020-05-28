<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;

class IndexController extends Controller
{
    public function index()
    {
        $soul = DB::table('soul')
            ->inRandomOrder()
            ->first();
        $title = $soul->title;
        return view('welcome', ['title' => $title]);
    }


}
