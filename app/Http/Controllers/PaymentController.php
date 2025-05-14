<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function index()
    {
        return Inertia::render('payments/Index');
    }

    public function create()
    {
        return Inertia::render('payments/Create');
    }
}
