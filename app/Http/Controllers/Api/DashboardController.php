<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Bento;
use App\Models\Store;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function activeUsers()
    {
        try {
            $activeUsersCount = User::where('status', 'active')->count();
            return response()->json($activeUsersCount, 200);
        } catch (\Exception $e) {
            \Log::error('Error fetching active users: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to fetch active users'], 500);
        }
    }

    public function activeBentos()
    {
        try {
            $count = Bento::count();
            return response()->json(['count' => $count], 200);
        } catch (\Exception $e) {
            \Log::error('Error fetching bento count: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to fetch bento count'], 500);
        }
    }

    public function activeStores(Request $request)
    {
        try {
            $count = Store::count();
            return response()->json(['count' => $count], 200);
        } catch (\Exception $e) {
            \Log::error('Error fetching store count: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to fetch store count'], 500);
        }
    }

    public function activeReviews(Request $request)
    {
        try {
            $count = Review::count();
            return response()->json(['count' => $count], 200);
        } catch (\Exception $e) {
            \Log::error('Error fetching review count: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to fetch review count'], 500);
        }
    }

    public function recentBentos(Request $request)
{
    try {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search', '');
        $sortField = $request->input('sort_field', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');

        // Fetch the most recent bentos with updated fields, including image_url
        $query = Bento::select(
                    'bentos.id',
                    'bentos.name',
                    'bentos.description',
                    'bentos.original_price',
                    'bentos.usual_discount_percentage',
                    'bentos.usual_discounted_price',
                    'bentos.estimated_discount_time',
                    'bentos.calories',
                    'bentos.discount_percentage',
                    'bentos.availability',
                    'bentos.created_at',
                    'bentos.updated_at',
                    'bentos.image_url',  // Include image_url here
                    'stores.name as store_name',
                    'stores.chain_name'
                )
                // Join bento_store to link bentos with specific stores
                ->leftJoin('bento_store', 'bentos.id', '=', 'bento_store.bento_id')
                ->leftJoin('stores', 'bento_store.store_id', '=', 'stores.id')  // Join stores via bento_store
                ->where('bentos.name', 'like', "%{$search}%")
                ->orderBy($sortField, $sortDirection);

        $recentBentos = $query->paginate($perPage);

        // Modify image_url in the paginated result
        $recentBentos->getCollection()->transform(function ($bento) {
            $bento->image_url = $bento->image_url ? asset('storage/' . $bento->image_url) : null;
            return $bento;
        });

        \Log::info('Recent Bentos Result:', $recentBentos->items());  // Log the result for debugging

        return response()->json($recentBentos, 200);
    } catch (\Exception $e) {
        \Log::error('Error fetching recent bentos: ' . $e->getMessage());
        return response()->json(['error' => 'Unable to fetch recent bentos'], 500);
    }
}

    


    public function getUserFeedback()
    {
        try {
            $feedback = Review::select('reviews.comment', 'reviews.created_at', 'users.name as user_name')
                ->join('users', 'reviews.user_id', '=', 'users.id')
                ->orderBy('reviews.created_at', 'desc')
                ->limit(5)
                ->get();

            return response()->json($feedback, 200);
        } catch (\Exception $e) {
            \Log::error('Error fetching user feedback: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to fetch feedback'], 500);
        }
    }

    public function recentStores(Request $request)
    {
        try {
            $recentStores = Store::select('name', 'updated_at')
                ->orderBy('updated_at', 'desc')
                ->take(5)
                ->get();

            $responseData = [
                'labels' => $recentStores->pluck('name')->toArray(),
                'datasets' => [
                    [
                        'label' => 'Store Updates',
                        'data' => $recentStores->pluck('updated_at')->map(fn ($date) => 1)->toArray(),
                        'backgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
                    ]
                ]
            ];

            return response()->json($responseData, 200);
        } catch (\Exception $e) {
            \Log::error('Error fetching recent stores: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to fetch recent stores'], 500);
        }
    }
}
