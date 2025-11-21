<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{
    public function index()
    {
        return view('qr.index');
    }

    public function generate(Request $request)
    {

        $request->validate([
            'url' => 'required|url',
        ]);

        $url = $request->input('url');
        $qrCode = QrCode::size(300)->generate($url); // SVG形式
        // dd($qrCode);

        return view('qr.index', compact('qrCode'));
    }
}
