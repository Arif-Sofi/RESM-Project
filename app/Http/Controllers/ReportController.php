<?php

namespace App\Http\Controllers;

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
