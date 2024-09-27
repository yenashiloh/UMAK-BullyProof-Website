<?php

namespace App\Http\Controllers;
use MongoDB\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class ProfileController extends Controller
{
    //show profile
    public function showDisciplineProfile()
    {
        $client = new Client(env('MONGODB_URI'));
        $adminCollection = $client->bullyproof->admins;
    
        $adminId = session('admin_id');
        $admin = $adminCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($adminId)]);
    
        $firstName = $admin->first_name ?? '';
        $lastName = $admin->last_name ?? '';
        $email = $admin->email ?? '';
        $contactNumber = $admin->contact_number ?? ''; 
    
        return view('admin.profile', compact(
            'firstName', 
            'lastName', 
            'email', 
            'contactNumber'
        )); 
    }

    //show guidance profile
    public function showGuidanceProfile()
    {
        $client = new Client(env('MONGODB_URI'));
        $adminCollection = $client->bullyproof->admins;
    
        $adminId = session('admin_id');
        $admin = $adminCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($adminId)]);
    
        $firstName = $admin->first_name ?? '';
        $lastName = $admin->last_name ?? '';
        $email = $admin->email ?? '';
        $contactNumber = $admin->contact_number ?? ''; 
    
        return view('guidance.profile', compact(
            'firstName', 
            'lastName', 
            'email', 
            'contactNumber'
        )); 
    }

    //update profile
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:15', 
            'email' => 'required|email|max:255',
            'password' => 'nullable|string|min:6|confirmed', 
        ]);
    
        if ($validator->fails()) {
            return redirect()->route('admin.profile')
                ->withErrors($validator)
                ->withInput();
        }
    
        $client = new Client(env('MONGODB_URI'));
        $adminCollection = $client->bullyproof->admins;
    
        $adminId = session('admin_id');
    
        $updateData = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'contact_number' => $request->contact_number,
            'email' => $request->email,
        ];
    
        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password); 
        }
    
        $adminCollection->updateOne(
            ['_id' => $adminId],
            ['$set' => $updateData]
        );
    
        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully!');
    }

    //update guidance profile
    public function updateGuidanceProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'contact_number' => 'required|string|max:15', 
            'email' => 'required|email|max:255',
            'password' => 'nullable|string|min:6|confirmed', 
        ]);
    
        if ($validator->fails()) {
            return redirect()->route('guidance.profile')
                ->withErrors($validator)
                ->withInput();
        }
    
        $client = new Client(env('MONGODB_URI'));
        $adminCollection = $client->bullyproof->admins;
    
        $adminId = session('admin_id');
    
        $updateData = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'contact_number' => $request->contact_number,
            'email' => $request->email,
        ];
    
        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($request->password); 
        }
    
        $result = $adminCollection->updateOne(
            ['_id' => new \MongoDB\BSON\ObjectId($adminId)],
            ['$set' => $updateData]
        );
    
        if ($result->getModifiedCount() > 0) {
            return redirect()->route('guidance.profile')->with('success', 'Profile updated successfully!');
        } else {
            return redirect()->route('guidance.profile')->with('error', 'Profile update failed. Please try again.');
        }
    }
}
