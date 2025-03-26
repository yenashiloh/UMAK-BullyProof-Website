<?php

namespace App\Http\Controllers;
use MongoDB\Client;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Services\CyberbullyingDetectionService;
use Illuminate\Support\Facades\Hash;
use MongoDB\BSON\UTCDateTime;
use DateTime;
use DateTimeZone;

class UserController extends Controller
{
    protected $detectionService;

    public function __construct(CyberbullyingDetectionService $detectionService) 
    {
        $this->detectionService = $detectionService;
    }

    //show audit log 
    public function showAuditLog()
    {
        $client = new Client(env('MONGODB_URI'));
        $userCollection = $client->bullyproof->users;
        $auditCollection = $client->bullyproof->audit_trails;
    
        $adminId = session('admin_id');
        $admin = $client->bullyproof->admins->findOne(['_id' => new \MongoDB\BSON\ObjectId($adminId)]);
    
        $firstName = $admin->first_name ?? '';
        $lastName = $admin->last_name ?? '';
        $email = $admin->email ?? '';
    
        $auditTrails = $auditCollection->aggregate([
            [
                '$lookup' => [
                    'from' => 'users', 
                    'localField' => 'userId', 
                    'foreignField' => '_id',
                    'as' => 'user_info'
                ]
            ],
            [
                '$unwind' => [
                    'path' => '$user_info',
                    'preserveNullAndEmptyArrays' => true 
                ]
            ],
            [
                '$project' => [
                    'timestamp' => 1,  
                    'action' => 1,
                    'full_name' => ['$concat' => ['$user_info.fullname']],
                ]
            ],
            ['$sort' => ['timestamp' => -1]] 
        ])->toArray();
        
        foreach ($auditTrails as &$log) {
            if (isset($log['timestamp'])) {
                if ($log['timestamp'] instanceof \MongoDB\BSON\UTCDateTime) {
                    $dateTime = $log['timestamp']->toDateTime();
                } else {
                    $dateTime = new DateTime($log['timestamp']);
                }
                
                $dateTime->setTimezone(new DateTimeZone('Asia/Manila'));
                
                $log['formatted_date'] = $dateTime->format('F j, Y, g:iA');
            } else {
                $log['formatted_date'] = 'N/A';
            }
        }
    
        return view('admin.users.audit-log', compact(
            'firstName', 
            'lastName', 
            'email', 
            'auditTrails'
        ));
    }

    //show users table
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

    //change status of user disabled acc and active acc
    public function changeStatus($id, Request $request)
    {
        try {
            $client = new Client(env('MONGODB_URI'));
    
            $userCollection = $client->bullyproof->users;
    
            $status = $request->status;
    
            $user = $userCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
    
            if ($user) {
                $updateResult = $userCollection->updateOne(
                    ['_id' => new \MongoDB\BSON\ObjectId($id)],
                    ['$set' => ['status' => $status]]
                );
    
                if ($updateResult->getModifiedCount() > 0) {
                    return response()->json(['success' => true, 'status' => $status]);
                } else {
                    return response()->json(['success' => false, 'message' => 'No changes made to the user status.']);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'User not found.']);
            }
    
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating status: ' . $e->getMessage()]);
        }
    }
    
    //show create account
    public function showCreateAccountPage()
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
        return view ('admin.users.create-account', compact(
            'firstName', 
            'lastName', 
            'email',
        )); 
    }

    //store account 
    public function storeAccount(Request $request)
    {
        // Updated validation rules to match frontend requirements
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email',
            'contact_number' => 'required|string|max:20',
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*\.\-_\+])[A-Za-z\d!@#$%^&*\.\-_\+]{8,}$/'
            ],
            'password_confirmation' => 'required'
        ]);
    
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }
    
        try {
            // Create MongoDB connection with proper error handling
            $mongoClient = new Client(env('MONGODB_URI'), [
                'retryWrites' => true,
                'w' => 'majority',
                'timeout' => 5000
            ]);
    
            // Select database and collection
            $database = $mongoClient->selectDatabase('bullyproof');
            $collection = $database->selectCollection('admins');
    
            // Generate username from email
            $username = strtolower(explode('@', $request->email)[0]) . '-admin';
    
            // Prepare admin document
            $admin = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'username' => $username,
                'email' => strtolower($request->email),
                'contact_number' => $request->contact_number,
                'password' => Hash::make($request->password),
                'role' => 'discipline',
                'created_at' => new UTCDateTime(now()->timestamp * 1000)
            ];
    
            // Insert with write concern
            $result = $collection->insertOne($admin, [
                'writeConcern' => new \MongoDB\Driver\WriteConcern(\MongoDB\Driver\WriteConcern::MAJORITY)
            ]);
    
            if ($result->getInsertedCount() > 0) {
                // Log successful creation
                \Log::info('Admin account created successfully', [
                    'email' => $admin['email'],
                    'username' => $admin['username']
                ]);
    
                return redirect()
                    ->route('admin.users.create-account')
                    ->with('success', 'Admin account created successfully.');
            } else {
                \Log::error('Failed to create admin account - No document inserted');
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Failed to create admin account. Please try again.');
            }
    
        } catch (\MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
            \Log::error('MongoDB connection timeout: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Database connection timeout. Please try again.');
                
        } catch (\MongoDB\Driver\Exception\AuthenticationException $e) {
            \Log::error('MongoDB authentication failed: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Database authentication failed. Please contact system administrator.');
                
        } catch (\Exception $e) {
            \Log::error('Error creating admin account: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the admin account. Please try again.');
        }
    }
}
