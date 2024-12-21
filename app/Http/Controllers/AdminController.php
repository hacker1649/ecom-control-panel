<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
  /**
   * Display a login form.
   *
   * @return \Illuminate\Http\Response
   */
  public function a_login()
  {
    return view('admin.a_login');
  }

  /**
   * Authenticate the user.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function a_authenticate(Request $request)
  {
    // Validate input credentials
    $request->validate([
      'email' => 'required|email|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', // Email format check
      'password' => ['required', 'min:8', 'regex:/[A-Z]/',  'regex:/[a-z]/',  'regex:/[0-9]/', 'regex:/[^a-zA-Z0-9]/'],
    ], [
      'password.regex' => 'The password must include at least one uppercase letter, one lowercase letter, one number, and one special character.',
    ]);

    $attemp = [
      'email' => $request->email,
      'password' => $request->password,
      'user_status' => 1,
      'is_admin' => 1,
    ];

    // Attempt to authenticate the user
    if (Auth::attempt($attemp)) {
      $request->session()->regenerate();
      return redirect()->route('a_dashboard')
        ->withSuccess('You have successfully logged in!');
    } else {
      Auth::logout();
      return back()->withErrors([
        'email' => 'You are not authorized to access this area.',
      ])->onlyInput('email');
    }

    return back()->withErrors([
      'email' => 'Your provided credentials do not match in our records.',
    ])->onlyInput('email');
  }


  /**
   * Display a dashboard to authenticated users.
   *
   * @return \Illuminate\Http\Response
   */
  public function a_dashboard()
  {
    // Ensure user is logged in and is an admin
    if (Auth::check() && Auth::user()->is_admin == 1) {
      return view('admin.a_dashboard');
    } else {
      // If user is not logged in or not an admin, redirect to login page
      return redirect()->route('a_login')
        ->withErrors([
          'email' => 'Please login to access the dashboard.',
        ])->onlyInput('email');
    }
  }

  // display the manage user page
  public function fetch_users(Request $request)
  {
    // Fetch the email query parameter
    $emailQuery = $request->input('email', '');
    $nameQuery = $request->input('name', ''); // New name filter

    // Use the method from the User model
    $users = User::fetchUsersWithFilters($nameQuery, $emailQuery)->paginate(5);

    foreach ($users as $user) {
      $user->encrypted_id = Crypt::encrypt($user->id);
    }

    // Return the view with users and filter data
    return view('admin.manage_user', compact('users', 'nameQuery', 'emailQuery'));
  }

  // display the add user form page
  public function add_user()
  {
    return view('admin.add_user');
  }

  public function storeUser(Request $request)
  {
    $request->validate([
      'image' => 'required|nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
      'f_name' => 'required|string',
      'l_name' => 'required|string',
      'email' => 'required|email|unique:users,email|',
      'password' => ['required', 'min:8', 'regex:/[A-Z]/',  'regex:/[a-z]/',  'regex:/[0-9]/', 'regex:/[^a-zA-Z0-9]/'],
      'phone' => 'required|string|max:15',
      'address' => 'required|string',
      'country' => 'required',
      'state' => 'required',
      'city' => 'required',
    ], [
      'password.regex' => 'The password must include at least one uppercase letter, one lowercase letter, one number, and one special character.',
    ]);

    try {

      // Start a transaction
      DB::beginTransaction();

      // Create the user in the users table
      $user = User::create([
        'name' => $request->f_name . ' ' . $request->l_name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'user_status' => 1,
      ]);

      $id = $user->id;

      // Handle Profile Photo Upload if exists
      $imagePath = $request->file('image')->store("upload/{$id}", 'public');

      // Insert into the UserProfile table using DB facade
      DB::table('users_profile')->insert([
        'id' => $user->id,
        'image' => $imagePath,
        'phone' => $request->phone,
        'address' => $request->address,
        'country' => $request->country,
        'state' => $request->state,
        'city' => $request->city,
      ]);

      // Commit the transaction
      DB::commit();

      return redirect()->route('manage_user')
        ->withSuccess('User added successfully!');
    } catch (\Exception $e) {

      // Rollback the transaction
      DB::rollBack();

      return redirect()->back()->withErrors(['error' => 'Something went wrong. ' . $e->getMessage()]);
    }
  }

  // display the edit user page
  public function edit_userData($encryptedId)
  {
    // Decrypt the ID to get the user ID
    $id = Crypt::decrypt($encryptedId);

    // Fetch the user and their profile data
    $user = User::findOrFail($id);

    $nameParts = explode(' ', $user->name, 2);
    $user->f_name = $nameParts[0];
    $user->l_name = $nameParts[1] ?? '';

    // Pass both the user and user profile to the view
    return view('admin.edit_user', compact('user'));
  }

  public function updateUser(Request $request, $id)
  {
    $request->validate([
      'f_name' => 'required|string|max:255',
      'l_name' => 'required|string|max:255',
      'old_password' => ['nullable', 'min:8', 'regex:/[A-Z]/',  'regex:/[a-z]/',  'regex:/[0-9]/'],
      'new_password' => ['nullable', 'min:8', 'regex:/[A-Z]/',  'regex:/[a-z]/',  'regex:/[0-9]/'],
      'confirm_password' => ['nullable', 'min:8'],
      'phone' => 'required|string',
      'address' => 'required|string',
      'country' => 'required|string',
      'state' => 'required|string',
      'city' => 'required|string',
      'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
    ], [
      'new_password.regex' => 'The password must include at least one uppercase letter, one lowercase letter, one number, and one special character.',
      'confirm_password.regex' => 'The password must include at least one uppercase letter, one lowercase letter, one number, and one special character.',
    ]);

    $user = User::findOrFail($id);

    if ($request->filled('old_password') && $request->filled('new_password') && $request->filled('confirm_password')) {
      if (!Hash::check($request->old_password, $user->password)) {
        return redirect()->back()->withErrors(['old_password' => 'The old password is incorrect.']);
      }
      // Check if new password and confirm password are the same
      if ($request->new_password !== $request->confirm_password) {
        return redirect()->back()->withErrors(['confirm_password' => 'The new password and confirm password do not match.']);
      }
      $user->password = Hash::make($request->new_password);
    }

    $user->name = $request->f_name . ' ' . $request->l_name;
    $user->save();

    $user_profile = $user->user_profile;

    $user_profile->phone = $request->phone;
    $user_profile->address = $request->address;
    $user_profile->country = $request->country;
    $user_profile->state = $request->state;
    $user_profile->city = $request->city;
    $user_profile->save();

    // Handle image upload if a new image is provided
    if ($request->hasFile('image')) {

      // Define the user's image folder path
      $folderPath = "public/upload/{$id}";

      // Check if the folder already exists and delete it if it does
      if (Storage::exists($folderPath)) {
        Storage::deleteDirectory($folderPath); // This will delete the entire folder and its contents
      }

      $imagePath = $request->file('image')->store("upload/{$id}", 'public');

      // Update the image path in the database
      $userProfile = UserProfile::find($id);
      $userProfile->image = $imagePath;
      $userProfile->save();
    }

    return redirect()->route('manage_user')->withSuccess('User updated successfully!');
  }

  // function to delete the user for the display
  public function deactivate($id)
  {
    $user = User::findOrFail($id); // Find the user by id
    $user->user_status = 0; // Set the status to 0 (inactive)
    $user->save(); // Save the changes

    return redirect()->route('manage_user')->withSuccess('User has been deactivated.');
  }

  public function upload(Request $request)
  {
    // Validate the file and user ID
    $request->validate([
      'file' => 'required|file|mimes:pdf,docx,doc|max:2048',
      'user_id' => 'required|exists:users,id',
    ]);

    // Get the user and file
    $user = User::find($request->user_id);
    $file = $request->file('file');
    $fileName = $request->input('filename');

    // Define the custom storage path (relative to the project root)
    $folderPath = base_path('uploads/' . $user->id . '/');  // base_path() gives the root of the project

    // Ensure the folder exists, create it if not
    if (!file_exists($folderPath)) {
      mkdir($folderPath, 0775, true);
    }

    // Get the last uploaded file by the user from the database
    $lastUploadedFile = Upload::query()
      ->where('id', $user->id)
      ->orderBy('created_at', 'desc') // Assuming created_at is used to track uploads
      ->first();

    if ($lastUploadedFile) {
      +$existingFilePath = $folderPath . $lastUploadedFile->f_name;

      if (file_exists($existingFilePath)) {
        // Rename the old file by appending a timestamp
        $timestamp = time();
        $newFilename = pathinfo($lastUploadedFile->f_name, PATHINFO_FILENAME) . '-' . $timestamp . '.' . pathinfo($lastUploadedFile->f_name, PATHINFO_EXTENSION);

        // Rename the old file
        rename($existingFilePath, $folderPath . $newFilename);

        // Update the status of the old file in the database
        $lastUploadedFile->update([
          'f_name' => $newFilename,
          'f_status' => 0,
          'updated_at' => $timestamp, // Update the timestamp
        ]);
      }
    }

    // Generate the filename for the new file
    $filename = $fileName . '.' . $file->getClientOriginalExtension();

    // Store the new file in the defined folder
    $file->move($folderPath, $filename);

    // Get the file size manually using PHP's filesize function
    $fileSize = filesize($folderPath . $filename);

    // Insert record into the upload table using Eloquent for the new file
    Upload::create([
      'f_name' => $filename,
      'f_size' => $fileSize, // Use the manually calculated size
      'f_path' => $folderPath . $filename,
      'id' => $user->id,
      'f_status' => 1,  // Set the status of the new file to 1
      'created_at' => time(),
    ]);

    // Return a success response
    return redirect()->back()->with('message', 'File uploaded successfully!');
  }

  public function download($id)
  {
    $path = Upload::query()
      ->where('id', $id)
      ->where('f_status', 1)
      ->value('f_path');

    if (file_exists($path)) {
      return Response::download($path);
    }
  }
}
