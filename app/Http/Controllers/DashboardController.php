<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index(Request $request): Response
    {
        $bookings = $request->user()
            ->bookings()
            ->with('room')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Dashboard', [
            'bookings' => $bookings,
            'appVersion' => config('app.version', '1.0.0'),
        ]);
    }
}
