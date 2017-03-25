<?php

namespace App\Http\Controllers;

use App\EcosystemButton;
use Illuminate\Http\Request;

class EcosystemController extends Controller
{
    /**
     * Displays all Ecosystem buttons.
     *
     * @return Illuminate\Http\Response
     */
    public function index()
    {
        return view('ecosystem', [
            'buttons' => EcosystemButton::all(),
        ]);
    }

}
