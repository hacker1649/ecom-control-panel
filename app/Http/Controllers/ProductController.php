<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImages;
use Illuminate\Support\Facades\Crypt;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Fetch the email query parameter
        $productNameSearch = $request->product_name;
        $categoryNameSearch = $request->category_name;
        $priceSearch = $request->price;

        // Use the method from the User model
        $products = Product::fetchProductsWithFilters($productNameSearch, $categoryNameSearch, $priceSearch)
            ->withCount('images') // Include the count of images
            ->paginate(5);

        foreach ($products as $product) {
            $product->encrypted_id = Crypt::encrypt($product->p_id);
        }

        // Return the view with products and filter data
        return view('admin.product.index', compact('products', 'productNameSearch', 'categoryNameSearch', 'priceSearch'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.product.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'p_name' => 'required|string|max:255',
            'p_desc' => 'required|string',
            'p_price' => 'required|numeric',
            'category' => 'required|string',
            'popularity' => 'required|string|in:Featured,Non-Featured',
            'images' => 'required|image|mimes:jpeg,jpg,png',
        ]);

        $categoryId = Category::getCategoryIdByName($request->category);
        $popularity = Product::setPopularity($request->popularity);

        // Insert record into the product table using Eloquent for the new product
        $product = Product::create([
            'p_name' => $request->p_name,
            'p_desc' => $request->p_desc,
            'p_price' => $request->p_price,
            'c_id' => $categoryId,
            'popularity' => $popularity,
            'p_status' => 1,  // Set the status of the new product to 1
            'created_on' => time(),
        ]);
        
        // Retrieve the product ID
        $productId = $product->id;

        // manage uploaded image
        $image = $request->file('images');
        $filename = time() . '_' . $image->getClientOriginalName();
        $destinationPath = public_path('products/' . $productId . '/'); // Define the directory path
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true); // Create the directory if it doesn't exist
        }
        $image->move($destinationPath, $filename); // Move the uploaded file to the directory

        // Insert the priority value in product images table with high priority
        ProductImages::create([
            'p_id' => $productId,
            'f_name' => $filename,
            'f_path' => 'products/'. $productId . '/'  . $filename,
            'priority' => 1,
        ]);

        return redirect()->route('product.index')->withSuccess('Product added successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($encryptedId)
    {
        // Decrypt the ID to get the user ID
        $id = Crypt::decrypt($encryptedId);

        // Fetch the product data
        $product = Product::where('p_id', $id)->firstOrFail();

        return view('admin.product.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'p_name' => 'required|string|max:255',
            'p_desc' => 'required|string',
            'p_price' => 'required|numeric',
            'category' => 'required|string',
            'popularity' => 'required|string|in:Featured,Non-Featured',
        ]);

        $categoryId = Category::getCategoryIdByName($request->category);
        $popularity = Product::setPopularity($request->popularity);

        Product::query()
            ->where('p_id', $id)
            ->update([
                'p_name' => $request->p_name,
                'p_desc' => $request->p_desc,
                'p_price' => $request->p_price,
                'c_id' => $categoryId,
                'popularity' => $popularity,
                'updated_on' => time(),
            ]);

        return redirect()->route('product.index')->withSuccess('Product updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Product::where('p_id', $id)->update(['p_status' => 0]);

        return redirect()->route('product.index')->with('message', 'Product deactivated successfully.');
    }

    // handle image upload 
    public function uploadImages(Request $request)
    {
        // Validate the uploaded images
        $request->validate([
            'product_id' => 'required|exists:product,p_id', // Ensure product exists
            'images' => 'nullable|array|min:1', // Validate image count (1 to 4 images)
            'images.*' => 'image|mimes:jpeg,jpg,png', // Only allow jpg, jpeg, png
        ]);

        // Retrieve the product by ID
        $product = Product::where('p_id', $request->product_id)->first();
        // Check if the product already has 4 active images
        $existingImagesCount = ProductImages::where('p_id', $product->p_id)->count();

        //If there are already 4 active images, block any more uploads
        if ($existingImagesCount >= 4) {
            return back()->withErrors(['error' => 'You cannot upload more than 4 images for this product.']);
        }

        // Check if the user is trying to upload more than 4 images at once
        $uploadedImageCount = count($request->file('images'));

        if ($uploadedImageCount > 4) {
            return back()->withErrors(['error' => 'You cannot upload more than 4 images together.']);
        }

        // Ensure that the user does not exceed 4 images in total (existing + new)
        if ($existingImagesCount + $uploadedImageCount > 4) {
            return back()->withErrors(['error' => 'You can only upload a total of 4 images for this product.']);
        }

        // Loop through the uploaded files and store them
        foreach ($request->file('images') as $image) {
            $filename = time() . '_' . $image->getClientOriginalName();
            $destinationPath = public_path('products/' . $product->p_id . '/'); // Define the directory path
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true); // Create the directory if it doesn't exist
            }
            $image->move($destinationPath, $filename); // Move the uploaded file to the directory

            // Save the image details in the tbl_productimage
            ProductImages::create([
                'p_id' => $product->p_id,
                'f_name' => $filename,
                'f_path' => $destinationPath . $filename,
                'priority' => 0,
            ]);
        }

        // Redirect back with a success message
        return redirect()->route('product.index')->with('message', 'Images uploaded successfully!');
    }
}
