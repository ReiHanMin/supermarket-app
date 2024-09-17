<?php

namespace App\Http\Controllers\Api;

use App\Enums\AddressType;
use App\Enums\CustomerStatus;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\Dashboard\OrderResource;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Traits\ReportTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Enums\UserStatus;
use App\Models\Bento;
use App\Models\Store;
use App\Models\Review;



class DashboardController extends Controller
{
    use ReportTrait;

    public function activeUsers()
{
    try {
        // Fetch count of active users
        $activeUsersCount = User::where('status', UserStatus::Active->value)->count();

        // Return the count as a JSON response
        return response()->json($activeUsersCount, 200);
    } catch (\Exception $e) {
        // Log the exception and return an error response
        \Log::error('Error fetching active users: ' . $e->getMessage());
        return response()->json(['error' => 'Unable to fetch active users'], 500);
    }
}



public function activeBentos()
{
    try {
        // Fetch the count of all bentos from the database
        $count = Bento::count();

        // Return the count as a JSON response
        return response()->json(['count' => $count], 200);
    } catch (\Exception $e) {
        // Log any errors and return an error response
        \Log::error('Error fetching bento count: ' . $e->getMessage());
        return response()->json(['error' => 'Unable to fetch bento count'], 500);
    }
}


public function activeStores(Request $request)
{
    try {
        // Fetch the count of all stores
        $count = Store::count();

        // Return the count as a JSON response
        return response()->json(['count' => $count], 200);
    } catch (\Exception $e) {
        \Log::error('Error fetching store count: ' . $e->getMessage());
        return response()->json(['error' => 'Unable to fetch store count'], 500);
    }
}

public function activeReviews(Request $request)
{
    try {
        // Fetch the count of all reviews
        $count = Review::count();

        // Return the count as a JSON response
        return response()->json(['count' => $count], 200);
    } catch (\Exception $e) {
        \Log::error('Error fetching review count: ' . $e->getMessage());
        return response()->json(['error' => 'Unable to fetch review count'], 500);
    }
}

public function recentBentos(Request $request)
{
    try {
        // Fetch the most recent bentos with all required fields, ordered by created_at
        $recentBentos = Bento::select(
                'id', 
                'name', 
                'description', 
                'price', 
                'status', 
                'store_id', 
                'created_at', 
                'updated_at',
                'image_url', // New field
                'ingredients', // New field
                'calories', // New field
                'discount_percentage', // New field
                'availability', // New field
                'rating', // New field
                'reviews_count' // New field
            )
            ->orderBy('created_at', 'desc')
            ->take(5) // Limit to the most recent 5 bentos
            ->get()
            ->map(function ($bento) {
                // Construct the correct image URL
                $bento->image_url = $bento->image_url ? asset('storage/' . $bento->image_url) : null;
                return $bento;
            });

        // Return the bentos as a JSON response
        return response()->json($recentBentos, 200);
    } catch (\Exception $e) {
        \Log::error('Error fetching recent bentos: ' . $e->getMessage());
        return response()->json(['error' => 'Unable to fetch recent bentos'], 500);
    }
}







    public function paidOrders()
    {
        $fromDate = $this->getFromDate();
        $query = Order::query()->where('status', OrderStatus::Paid->value);

        if ($fromDate) {
            $query->where('created_at', '>', $fromDate);
        }

        return $query->count();
    }

    public function totalIncome()
    {
        $fromDate = $this->getFromDate();
        $query = Order::query()->where('status', OrderStatus::Paid->value);

        if ($fromDate) {
            $query->where('created_at', '>', $fromDate);
        }
        return round($query->sum('total_price'));
    }

    public function ordersByCountry()
    {
        $fromDate = $this->getFromDate();
        $query = Order::query()
            ->select(['c.name', DB::raw('count(orders.id) as count')])
            ->join('users', 'created_by', '=', 'users.id')
            ->join('customer_addresses AS a', 'users.id', '=', 'a.customer_id')
            ->join('countries AS c', 'a.country_code', '=', 'c.code')
            ->where('status', OrderStatus::Paid->value)
            ->where('a.type', AddressType::Billing->value)
            ->groupBy('c.name')
            ;

        if ($fromDate) {
            $query->where('orders.created_at', '>', $fromDate);
        }

        return $query->get();
    }

    public function latestCustomers()
    {
        return Customer::query()
            ->select(['id', 'first_name', 'last_name', 'u.email', 'phone', 'u.created_at'])
            ->join('users AS u', 'u.id', '=', 'customers.user_id')
            ->where('status', CustomerStatus::Active->value)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    public function latestOrders()
    {
        return OrderResource::collection(
            Order::query()
                ->select(['o.id', 'o.total_price', 'o.created_at', DB::raw('COUNT(oi.id) AS items'),
                    'c.user_id', 'c.first_name', 'c.last_name'])
                ->from('orders AS o')
                ->join('order_items AS oi', 'oi.order_id', '=', 'o.id')
                ->join('customers AS c', 'c.user_id', '=', 'o.created_by')
                ->where('o.status', OrderStatus::Paid->value)
                ->limit(10)
                ->orderBy('o.created_at', 'desc')
                ->groupBy('o.id', 'o.total_price', 'o.created_at', 'c.user_id', 'c.first_name', 'c.last_name')
                ->get()
        );
    }

    public function recentStores(Request $request)
{
    try {
        // Fetch the most recent stores updates, ordered by updated_at
        $recentStores = Store::select('name', 'updated_at') // Fetch required fields
            ->orderBy('updated_at', 'desc')
            ->take(5) // Adjust the number as needed
            ->get();

        // Prepare the data in the format required by the DoughnutChart component
        $responseData = [
            'labels' => $recentStores->pluck('name')->toArray(), // Extract store names for labels
            'datasets' => [
                [
                    'label' => 'Store Updates', // Label for the dataset
                    'data' => $recentStores->pluck('updated_at')->map(function ($date) {
                        // Here you can convert the 'updated_at' dates into a count or a relevant metric
                        // Example: Count of updates or convert date to a formatted string
                        return 1; // Assuming each entry represents 1 update
                    })->toArray(),
                    'backgroundColor' => ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'] // Customize colors
                ]
            ]
        ];

        // Return the response as JSON
        return response()->json($responseData, 200);

    } catch (\Exception $e) {
        \Log::error('Error fetching recent stores: ' . $e->getMessage());
        return response()->json(['error' => 'Unable to fetch recent stores'], 500);
    }
}

}
