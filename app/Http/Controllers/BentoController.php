<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bento;
use App\Models\Store;

class BentoController extends Controller
{
    public function index(Request $request)
    {
        $bentos = Bento::paginate(12);  // Fetch data from the database
    
        // Proceed to pass the $bentos variable to the view
        return view('product.index', compact('bentos'));
    }
    

    public function show(Bento $bento)
    {
    $bento->load(['stores', 'relatedItems', 'reviews']); // Eager load relationships
    return view('product.bento', compact('bento')); // Return the Blade view with the bento data
    }

     



}
