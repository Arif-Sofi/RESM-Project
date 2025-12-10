<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display the report generation page.
     */
    public function index()
    {
        return view('reports.index');
    }
}
