<?php

namespace App\Http\Controllers;

use Storage;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\SemaphoreService;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $semaphore;
    
    public function __construct(SemaphoreService $semaphore)
    {
        $this->semaphore = $semaphore;
    }

    public function index()
    {
        return Inertia::render('users/UsersTable');
    }

    public function create()
    {
        return Inertia::render('users/CreateUser');
    }

    public function list(Request $request)
    {
        try{
            $search = $request->input('search', '');
            $users = User::query()
                ->when($search, function ($query, $search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                })
                ->paginate(10);
            $data = $users->getcollection()->map(function ($user) {
                return [
                    'id' => encrypt($user->id),
                    'name' => $user->name,
                    'email' => $user->email,
                    'profile_photo_url' => $user->profile_photo_url,
                    'address' => $user->address,
                    'role' => $user->role,
                    'mobile_number' => $user->mobile_number,
                ];
            });

            return response()->json([
                'result' => true,
                'data' => $data,
                'pagination' => [
                    'total' => $users->total(),
                    'per_page' => $users->perPage(),
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'from' => $users->firstItem(),
                    'to' => $users->lastItem(),
                ],
            ]);

        }catch(\Exception $e){
            return response()->json([
                'result' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request){
        try{
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'address' => 'nullable|string|max:255',
                'mobile_number' => 'nullable|string|max:20',
                'role' => 'required|in:admin,user',
            ]);

            $password = Str::random(10);
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => $password, 
                'address' => $validatedData['address'] ?? null,
                'mobile_number' => $validatedData['mobile_number'] ?? null,
                'role' => $validatedData['role'],
            ]);

            $number = $user->mobile_number;
            $message = "Hello {$user->name}, your account has been created. Your password is: {$password}. Please change it after your first login.";
            $response = $this->semaphore->sendSMS($number, $message);

            return response()->json([
                'result' => true,
                'message' => 'User created successfully.',
                'data' => [
                    'id' => encrypt($user->id),
                    'name' => $user->name,
                    'email' => $user->email,
                    'address' => $user->address,
                    'mobile_number' => $user->mobile_number,
                    'role' => $user->role,
                ],
            ], 201);
        }catch(\Exception $e){
            return response()->json([
                'result' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id){
        try{
            $decryptedId = decrypt($id);
            $user = User::findOrFail($decryptedId);

            return response()->json([
                'result' => true,
                'data' => [
                    'id' => encrypt($user->id),
                    'name' => $user->name,
                    'email' => $user->email,
                    'address' => $user->address,
                    'mobile_number' => $user->mobile_number,
                    'role' => $user->role,
                ],
                'message' => 'User fetched successfully.',
            ]);
        }catch(\Exception $e){
            return response()->json([
                'result' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request){
        try{
            $validatedData = $request->validate([
                'id' => 'required|string',
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . decrypt($request->input('id')),
                'address' => 'nullable|string|max:255',
                'mobile_number' => 'nullable|string|max:20',
                'role' => 'required|in:admin,user',
            ]);

            $decryptedId = decrypt($validatedData['id']);
            $user = User::findOrFail($decryptedId);

            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            $user->address = $validatedData['address'] ?? null;
            $user->mobile_number = $validatedData['mobile_number'] ?? null;
            $user->role = $validatedData['role'];
            $user->save();
            
            return response()->json([
                'result' => true,
                'message' => 'User updated successfully.',
                'data' => [
                    'id' => encrypt($user->id),
                    'name' => $user->name,
                    'email' => $user->email,
                    'address' => $user->address,
                    'mobile_number' => $user->mobile_number,
                    'role' => $user->role,
                ],
                
            ]);
        }catch(\Exception $e){
            return response()->json([
                'result' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id){
        try{
            $decryptedId = decrypt($id);
            $user = User::findOrFail($decryptedId);
            $user->delete();

            return response()->json([
                'result' => true,
                'message' => 'User deleted successfully.',
            ]);
        }catch(\Exception $e){
            return response()->json([
                'result' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function profile(){
        return Inertia::render('auth/profile/MyProfile');
    }

    public function myProfileData()
    {
        try {
            $user = Auth::user();

            $profilePhotoUrl = $user->profile_photo_path
                ? asset('storage/' . $user->profile_photo_path)
                : null;

            $data = [
                'name' => $user->name,
                'email' => $user->email,
                'address' => $user->address,
                'mobile_number' => $user->mobile_number,
                'profile_photo_url' => $profilePhotoUrl,
                'role' => $user->role,
                'created_at' => $user->created_at,
            ];

            return response()->json([
                'result' => true,
                'message' => 'User data retrieved successfully.',
                'data' => $data,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function updateProfile(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'mobile_number' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:255',
                'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            $user = auth()->user();

            // Handle profile photo upload
            if ($request->hasFile('profile_photo')) {
                // Delete old photo if it exists
                if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }

                // Store new photo
                $path = $request->file('profile_photo')->store('profile_photos', 'public');
                $user->profile_photo_path = $path;
            }

            // Update user data
            $user->name = $validated['name'];
            $user->mobile_number = $validated['mobile_number'] ?? $user->mobile_number;
            $user->address = $validated['address'] ?? $user->address;

            $user->save();

            return response()->json([
                'result' => true,
                'message' => 'Profile updated successfully!',
                'data' => $user,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}
