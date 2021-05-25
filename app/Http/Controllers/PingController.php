<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class PingController extends Controller
{
    public function pingAction()
    {
        return new JsonResponse('ping');
    }
}
