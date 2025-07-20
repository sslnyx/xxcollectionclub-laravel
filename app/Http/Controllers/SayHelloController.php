<?php

namespace App\Http\Controllers;

use App\Mail\SayHelloMail;
use App\Models\SayHello;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SayHelloController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email:rfc,dns',
        ]);
      

        SayHello::create($request->all());
        return response()->json(200);

        // Mail::to("sslnyx@gmail.com")->send(new SayHelloMail($request->all()));
    }
}
