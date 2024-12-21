<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Cart;
use App\Models\UserProfile;

class CartController extends Controller
{
  public function index()
  {
    // Check if an active cart exists for the logged-in user
    $cart = Cart::where('id', auth()->id())
      ->whereIn('c_status', [1, 2])  // Active or pending status
      ->first();

    if ($cart) {
      // Fetch the cart items for the active cart with product images
      $cartItem = CartItem::with('product.images')
        ->where('c_id', $cart->c_id)
        ->get();

      // Calculate the total amount for the items in the active cart
      $t_amount = $cartItem->sum(function ($item) {
        return $item->quantity * $item->p_price;
      });
    } else {
      // If no active cart exists, initialize cartItem as an empty collection
      $cartItem = collect();
      $t_amount = 0;
    }

    return view('add_to_cart', compact('cartItem', 't_amount'));
  }

  public function add($productId)
  {
    if (Auth::check()) {
      $user = Auth::user();

      // Check for an active cart, or one with status 1 or 2
      $cart = Cart::where('id', $user->id)
        ->whereIn('c_status', [1, 2])
        ->first();

      if (!$cart) {
        // Create a new cart if none exists
        $cart = Cart::create([
          'id' => $user->id,
          'c_status' => 1, // Start with status 1 (active)
          'created_on' => time(),
        ]);
      } else {
        if ($cart->c_status == 2) {
          $cart->c_status = 1;
          $cart->updated_on = time();
          $cart->save();
        }
      }

      // Check if the product exists in the product table
      $product = Product::where('p_id', $productId)->first();

      // Check if the product is already in the cart
      $CartItem = CartItem::where('c_id', $cart->c_id)
        ->where('p_id', $productId)
        ->first();

      if ($CartItem) {
        // Update the quantity and total price if product exists in cart
        $CartItem->quantity += 1;
        $CartItem->tp_price = $CartItem->quantity * $CartItem->p_price; // Recalculate total price
        $CartItem->save();
      } else {
        // Add product to cart
        CartItem::create([
          'c_id' => $cart->c_id,
          'id' => $user->id,
          'p_id' => $product->p_id,
          'quantity' => 1,
          'p_price' => $product->p_price,
          'tp_price' => $product->p_price,
        ]);
      }

      // Recalculate total amount of the cart
      $cart->t_amount = $cart->cartItems->sum('tp_price');
      $cart->save();

      // Redirect to the cart page or add to cart page
      return redirect()->back()->with('success', 'Item added to cart successfully...');
    } else {
      // Redirect to login if not authenticated
      return redirect()->route('login');
    }
  }

  public function update(Request $request, $cartItemId)
  {
    // Fetch the cart item
    $cartItem = CartItem::where('i_id', $cartItemId)->first();

    if (!$cartItem) {
      return redirect()->route('cart.index')->with('error', 'Cart item not found.');
    }

    // Update quantity based on the button pressed (+ or -)
    if ($request->has('quantity')) {
      $quantityChange = (int) $request->quantity;

      // Update the quantity
      $cartItem->quantity += $quantityChange;

      // If the quantity becomes zero or less, delete the cart item
      if ($cartItem->quantity <= 0) {
        $cartItem->delete();
      } else {
        // Recalculate the total product price if the item is not deleted
        $cartItem->tp_price = $cartItem->quantity * $cartItem->p_price;
        $cartItem->save();
      }
    }

    // Update the cart's total amount
    $cart = $cartItem->cart;
    $cart->t_amount = $cart->cartItems->sum('tp_price');
    $cart->save();

    // Fetch the updated cart data
    $cartItem = $cart->cartItems()->with('product')->get();
    $t_amount = $cart->t_amount;

    // Return the updated view with fresh data
    return view('add_to_cart', compact('cartItem', 't_amount'))->with('success', 'Item updated successfully');
  }

  // function to remove an item from the cart
  public function remove(Request $request)
  {
    $item_id = $request->i_id;

    // Find the specific cart item
    $cartItem = CartItem::where('i_id', $item_id)->first();

    if (!$cartItem) {
      return redirect()->route('cart.index')->with('error', 'Cart item not found.');
    }

    // Find the parent cart
    $cart = Cart::where('c_id', $cartItem->c_id)->first();

    // Delete the specific cart item
    $cartItem->delete();

    // Recalculate total cart amount after item removal
    if ($cart) {
      $cart->t_amount = $cart->cartItems->sum('tp_price'); // Recalculate total
      $cart->save();
    }

    // Redirect back with a success message
    return redirect()->route('cart.index')->with('success', 'Item removed from cart successfully');
  }

  // function to update the cart status to 2
  public function updateCartStatus(Request $request)
  {
    $cart = Cart::where('id', auth()->id())->where('c_status', 1)->first();
    if ($cart) {
      $cart->c_status = $request->c_status;
      $cart->updated_on = time();
      $cart->save();
      return response()->json(['status' => 'success']);
    }
    return response()->json(['status' => 'failure']);
  }

  // function to update the user and users profile data
  public function updateUserProfile(Request $request)
  {
    $user = Auth::user();

    // Validate input data
    $validated = $request->validate([
      'phone' => 'required|string|max:15',
      'address' => 'required|string|max:255',
    ]);

    $user_profile = $user->user_profile;

    // Check if the user profile exists
    if (!$user_profile) {
      // Create a new user profile if it doesn't exist
      $user_profile = UserProfile::create([
        'id' => $user->id, // Set the user_id
        'phone' => $validated['phone'],
        'address' => $validated['address'],
      ]);
    } else {
      // Update existing user profile data
      $user_profile->update([
        'phone' => $validated['phone'],
        'address' => $validated['address'],
      ]);
    }

    // Redirect the user to the payment page with a success message
    return redirect()->route('payment.page')->with('success', 'Profile updated successfully.');
  }

  // function to show the payment page 
  public function showPaymentPage()
  {
    // Assuming the cart is identified by user_id or session_id
    $userId = auth()->id(); // If user is logged in
    $cart = Cart::where('id', $userId)->orderBy('c_id', 'desc')->first(); // Adjust based on your DB structure

    if ($cart) {
      $totalAmount = $cart->t_amount;
    } else {
      $totalAmount = 0; // Default value if no cart is found
    }

    return view('payment', compact('totalAmount'));
  }

  public function createPaypalOrder()
  {
    try {
      // Fetch cart and calculate total amount
      $cart = Cart::where('id', auth()->id())
        ->whereIn('c_status', [1, 2])
        ->first();

      $totalAmount = 0;

      if ($cart) {
        $totalAmount = $cart->cartItems->sum(function ($cartItem) {
          return $cartItem->quantity * $cartItem->product->p_price;
        });
      }

      if ($totalAmount == 0) {
        return response()->json([
          'status' => 'error',
          'message' => 'Cart is empty.'
        ], 400);
      }

      // Create PayPal order response
      $paypalOrder = [
        'purchase_units' => [
          [
            'amount' => [
              'value' => number_format($totalAmount, 2, '.', ''),
              'currency_code' => 'USD'
            ]
          ]
        ]
      ];

      return response()->json([
        'status' => 'success',
        'order' => $paypalOrder
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'status' => 'error',
        'message' => $e->getMessage()
      ], 500);
    }
  }


  // main function to show the success page after successful completion of the order
  public function showSuccessPage()
  {
    // Retrieve the latest order with the related order items and payment method
    $cart = Cart::with('cartItems')->where('id', Auth::id())->where('c_status', 3)->orderBy('created_on', 'desc')->first();
    $paymentMethod = $cart->p_mode;

    // Pass data to the view
    return view('success', compact('cart', 'paymentMethod'));
  }

  // function to show the success page after successful completion of the order using cash
  public function successOnCash()
  {
    // Assuming you have a Cart model and the cart is associated with the currently authenticated user
    $userId = auth()->id(); // Get the currently authenticated user's ID

    // Update the cart status and payment mode
    $cart = Cart::where('id', $userId)->orderBy('c_id', 'desc')->first(); // Adjust based on your DB structure

    if ($cart) {
      $cart->update([
        'c_status' => 3, // Update the status to 3 (completed)
        'p_mode' => 'cash', // Set payment mode to 'cash'
        'updated_on' => time(),
      ]);

      // Call the function to send the order confirmation email
      $this->sendOrderConfirmationEmail($cart);
    }

    // Redirect to the success view
    return redirect()->route('order.success'); // Assuming 'success' is the name of your route
  }


  // function to show the success page after successful completion of the order using paypal
  public function successOnPaypal()
  {
    // Assuming you have a Cart model and the cart is associated with the currently authenticated user
    $userId = auth()->id(); // Get the currently authenticated user's ID

    // Update the cart status and payment mode
    $cart = Cart::where('id', $userId)->orderBy('c_id', 'desc')->first(); // Adjust based on your DB structure

    if ($cart) {
      // Fetch the transaction details sent from the frontend (assumes JSON format)
      $transactionDetails = request('transaction_details'); // Adjust based on the payload key

      // Update the cart with status, payment mode, and transaction details
      $cart->update([
        'c_status' => 3, // Mark the cart as completed
        'p_mode' => 'paypal', // Indicate PayPal was used
        'transaction_details' => $transactionDetails, // Save the transaction details
        'updated_on' => time(),
      ]);

      // Call the function to send the order confirmation email
      $this->sendOrderConfirmationEmail($cart);
    }

    return response()->json(['success' => true]);
  }

  // Function to send the order confirmation email
  private function sendOrderConfirmationEmail($cart)
  {
    $cartItemsHtml = '';
    foreach ($cart->cartItems as $item) {
      $cartItemsHtml .= "
            <tr>
                <td style='border: 1px solid #ddd; padding: 10px;'>{$item->product->p_name}</td>
                <td style='border: 1px solid #ddd; padding: 10px;'>{$item->quantity}</td>
                <td style='border: 1px solid #ddd; padding: 10px;'>\$" . number_format($item->p_price, 2) . "</td>
            </tr>
        ";
    }

    // Store the recipent email 
    $recipentEmail = $cart->user->email;

    // Cretae the email subject
    $emailSubject = "Order Conformation - #{$cart->c_id}";

    // Create the email body
    $emailBody = "
        <div style='font-family: Arial, sans-serif; color: #333;'>
            <h2>Order Placed Successfully!</h2>
            <p>Thank you <strong>" . Auth::user()->name . "</strong> for shopping with us. Your order has been successfully placed!</p>

            <h4>Your Order Details:</h4>
            <table style='width: 100%; text-align: center; border-collapse: collapse;'>
                <thead>
                    <tr style='background-color: #f2f2f2;'>
                        <th style='border: 1px solid #ddd; padding: 10px;'>Product Name</th>
                        <th style='border: 1px solid #ddd; padding: 10px;'>Quantity</th>
                        <th style='border: 1px solid #ddd; padding: 10px;'>Price</th>
                    </tr>
                </thead>
                <tbody>
                    {$cartItemsHtml}
                </tbody>
            </table>

            <div style='margin-top: 20px;'>
                <p><strong>Total Amount: </strong><span>\$" . number_format($cart->t_amount, 2) . "</span></p>
                <p><strong>Payment Method: </strong><span>{$cart->p_mode}</span></p>
            </div>

            <p style='margin-top: 20px;'>If you have any questions, please contact our <a href='#'>support team</a>.</p>
            
            <p>Regards,</p>
            <p>The Support Team</p>
        </div>
    ";

    // Send email notification
    $mailer = new \Helper();

    $email_info = [
      'recipient_email' => [$recipentEmail], // Add primary recipients
      'cc' => ['muhammadanas@xolva.com'], // Add CC recipients if any
      'bcc' => [], // Add BCC recipients if any
      'subject' => $emailSubject,
      'body' => $emailBody,
    ];

    $mailer->sendEmail($email_info);
  }
}
