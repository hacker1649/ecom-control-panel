<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\User;

class OrderController extends Controller
{
  public function index(Request $request)
  {
    // Get the search parameters from the request
    $userNameSearch = $request->input('user_name');
    $userEmailSearch = $request->input('user_email');

    // Build the query to fetch carts/orders with status 1, 2, or 3
    $orders = Cart::whereIn('c_status', [1, 2, 3]);

    // Apply search filters if user_name or user_email is provided
    if ($userNameSearch) {
      $orders->whereHas('user', function ($query) use ($userNameSearch) {
        $query->where('name', 'like', '%' . $userNameSearch . '%');
      });
    }

    if ($userEmailSearch) {
      $orders->whereHas('user', function ($query) use ($userEmailSearch) {
        $query->where('email', 'like', '%' . $userEmailSearch . '%');
      });
    }

    // Paginate the results
    $orders = $orders->paginate(5);

    // Return the view with the orders and search parameters
    return view('admin.order.order', compact('orders', 'userNameSearch', 'userEmailSearch'));
  }
}
