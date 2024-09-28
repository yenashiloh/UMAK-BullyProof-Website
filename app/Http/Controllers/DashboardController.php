<?php

namespace App\Http\Controllers;
use MongoDB\Client;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function showDisciplineDashboard()
    {
        $client = new Client(env('MONGODB_URI'));
        $userCollection = $client->bullyproof->users;
        $adminCollection = $client->bullyproof->admins;
        $reportsCollection = $client->bullyproof->reports; 
    
        $adminId = session('admin_id');
    
        $totalUsers = $userCollection->countDocuments();
        $totalReports = $reportsCollection->countDocuments(); 
    
        $admin = $adminCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($adminId)]);
    
        $firstName = $admin->first_name ?? '';
        $lastName = $admin->last_name ?? '';
        $email = $admin->email ?? '';
    
        return view('admin.dashboard', compact(
            'totalUsers', 
            'totalReports', 
            'firstName', 
            'lastName', 
            'email'
        )); 
    }
    
    public function showGuidanceDashboard()
    {
        $client = new Client(env('MONGODB_URI'));
        $userCollection = $client->bullyproof->users;
        $adminCollection = $client->bullyproof->admins;
        $reportsCollection = $client->bullyproof->reports; 

        $adminId = session('admin_id');

        $totalUsers = $userCollection->countDocuments();
        $totalReports = $reportsCollection->countDocuments(); 

        $admin = $adminCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($adminId)]);

        $firstName = $admin->first_name ?? '';
        $lastName = $admin->last_name ?? '';
        $email = $admin->email ?? '';

        return view('guidance.dashboard', compact(
            'totalUsers', 
            'totalReports', 
            'firstName', 
            'lastName', 
            'email')); 
    }
}
    