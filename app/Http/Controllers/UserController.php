<?php

namespace App\Http\Controllers;
use MongoDB\Client;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function showUsers()
    {
        $client = new Client(env('MONGODB_URI'));
        $userCollection = $client->bullyproof->users;
        $adminCollection = $client->bullyproof->admins;

        $adminId = session('admin_id');
        $admin = $adminCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($adminId)]);

        $firstName = $admin->first_name ?? '';
        $lastName = $admin->last_name ?? '';
        $email = $admin->email ?? '';

        $users = $userCollection->find()->toArray();
        return view ('admin.users.users', compact(
            'firstName', 
            'lastName', 
            'email',
            'users')); 
    }

    public function showCounselling()
    {
        $client = new Client(env('MONGODB_URI'));
        $userCollection = $client->bullyproof->users;
        $adminCollection = $client->bullyproof->admins;

    $adminId = session('admin_id');
        $admin = $adminCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($adminId)]);

        $firstName = $admin->first_name ?? '';
        $lastName = $admin->last_name ?? '';
        $email = $admin->email ?? '';

        $users = $userCollection->find()->toArray();
        return view ('guidance.counselling.counselling', compact(
            'firstName', 
            'lastName', 
            'email',
            'users')); 
    }

    public function changeStatus($id, Request $request)
    {
        try {
            // Initialize the MongoDB client
            $client = new Client(env('MONGODB_URI'));
    
            // Select the appropriate collection
            $userCollection = $client->bullyproof->users;
    
            // Get the new status from the request
            $status = $request->status;
    
            // Find the user by their _id
            $user = $userCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
    
            if ($user) {
                // Update the user's status
                $updateResult = $userCollection->updateOne(
                    ['_id' => new \MongoDB\BSON\ObjectId($id)],
                    ['$set' => ['status' => $status]]
                );
    
                // Check if the update was successful
                if ($updateResult->getModifiedCount() > 0) {
                    return response()->json(['success' => true, 'status' => $status]);
                } else {
                    // Return failure if no rows were modified
                    return response()->json(['success' => false, 'message' => 'No changes made to the user status.']);
                }
            } else {
                // Return failure if the user is not found
                return response()->json(['success' => false, 'message' => 'User not found.']);
            }
    
        } catch (\Exception $e) {
            // Return error response in case of an exception
            return response()->json(['success' => false, 'message' => 'Error updating status: ' . $e->getMessage()]);
        }
    }
    

}
