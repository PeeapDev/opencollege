<?php

namespace App\Modules\Library\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    public function index()
    {
        return view('library::library.index');
    }

    public function create()
    {
        return view('library::library.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('library.index')->with('success', 'Book added.');
    }
}
