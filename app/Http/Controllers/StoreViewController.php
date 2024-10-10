<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\Bento;

class StoreViewController extends Controller
{
    public function productIndex()
    {
        // Eager load the latest update for each bento-store combination
        $bentos = Bento::with(['updates' => function ($query) {
            $query->latest()->limit(1);
        }])->get();
    
        // Fetch stores data
        $stores = Store::all();  // You can adjust this query to include more specific data if necessary
    
        return view('product.index', compact('bentos', 'stores'));
    }
    


}

