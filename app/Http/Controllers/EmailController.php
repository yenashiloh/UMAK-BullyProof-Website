<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MongoDB\Client;

class EmailController extends Controller
{
    //show email content management page
    public function showEmailManagementPage()
    {
        $client = new Client(env('MONGODB_URI'));
        $userCollection = $client->bullyproof->users;
        $adminCollection = $client->bullyproof->admins;
        $emailContentCollection = $client->bullyproof->emailContent; 
    
        $adminId = session('admin_id');
        $admin = $adminCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($adminId)]);
    
        $firstName = $admin->first_name ?? '';
        $lastName = $admin->last_name ?? '';
        $email = $admin->email ?? '';
    
        $latestEmailContent = $emailContentCollection->findOne([], ['sort' => ['created_at' => -1]]);  
    
        $complainantEmailContent = $latestEmailContent['complainant_email_content'] ?? '';
        $complainantDepartmentEmailContent = $latestEmailContent['complainant_department_email_content'] ?? '';
        $complaineeEmailContent = $latestEmailContent['complainee_email_content'] ?? '';
        $complaineeDepartmentEmailContent = $latestEmailContent['complainee_department_email_content'] ?? '';
        $rescheduleEmailContent = $latestEmailContent['reschedule_email_content'] ?? '';
    
        return view('admin.email.email-management', compact(
            'firstName',
            'lastName',
            'email',
            'complainantEmailContent',
            'complainantDepartmentEmailContent',
            'complaineeDepartmentEmailContent',
            'complaineeEmailContent',
            'rescheduleEmailContent'
        ));
    }
    
    //store email content
    public function storeEmailContent(Request $request)
    {
        $client = new Client(env('MONGODB_URI'));
        $emailContentCollection = $client->bullyproof->emailContent;  
    
        $complainantEmailContent = $request->input('complainant_email_content');
        $complainantDepartmentEmailContent = $request->input('complainant_department_email_content');
        $complaineeEmailContent = $request->input('complainee_email_content');
        $complaineeDepartmentEmailContent = $request->input('complainee_department_email_content');
        $rescheduleEmailContent = $request->input('reschedule_email_content');
        
        $result = $emailContentCollection->insertOne([
            'complainant_email_content' => $complainantEmailContent,
            'complainant_department_email_content' => $complainantDepartmentEmailContent,
            'complainee_email_content' => $complaineeEmailContent,
            'complainee_department_email_content' => $complaineeDepartmentEmailContent,
            'reschedule_email_content' => $rescheduleEmailContent,
            'created_at' => new \MongoDB\BSON\UTCDateTime(),  
        ]);
    
        if ($result->getInsertedCount() > 0) {
            return redirect()->back()->with('toast', 'Email content saved successfully!');
        } else {
            return redirect()->back()->with('toast', 'Failed to save email content.');
        }
    }
}
