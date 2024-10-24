<?php

namespace App\Http\Controllers;
use MongoDB\Client;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //show discipline dashboard page
    public function showDisciplineDashboard()
    {
        $client = new Client(env('MONGODB_URI'));
        $userCollection = $client->bullyproof->users;
        $adminCollection = $client->bullyproof->admins;
        $reportsCollection = $client->bullyproof->reports; 
    
        $adminId = session('admin_id');
    
        $totalUsers = $userCollection->countDocuments();
        $totalReports = $reportsCollection->countDocuments(); 
    
        $toReviewCount = $reportsCollection->countDocuments(['status' => 'To Review']);
        $underInvestigationCount = $reportsCollection->countDocuments(['status' => 'Under Investigation']);
        $resolvedCount = $reportsCollection->countDocuments(['status' => 'Resolved']);
    
        $pipeline = [
            [
                '$unwind' => '$cyberbullyingType' 
            ],
            [
                '$group' => [
                    '_id' => '$cyberbullyingType', 
                    'count' => ['$sum' => 1]
                ]
            ]
        ];
        
        $cyberbullyingCounts = $reportsCollection->aggregate($pipeline);
        
        $cyberbullyingData = [];
        foreach ($cyberbullyingCounts as $typeCount) {
            $cyberbullyingData[(string)$typeCount->_id] = $typeCount->count; 
        }
    

        $monthPipeline = [
            [
                '$group' => [
                    '_id' => [
                        '$dateToString' => [
                            'format' => '%m',
                            'date' => '$reportDate'
                        ]
                    ],
                    'count' => ['$sum' => 1] 
                ]
            ],
            [
                '$sort' => ['_id' => 1]
            ]
        ];
    
        $reportMonthCounts = $reportsCollection->aggregate($monthPipeline);
    
        $months = [
            '01' => 'January', '02' => 'February', '03' => 'March', 
            '04' => 'April', '05' => 'May', '06' => 'June', 
            '07' => 'July', '08' => 'August', '09' => 'September', 
            '10' => 'October', '11' => 'November', '12' => 'December'
        ];
    
        $reportMonthData = [];
        $reportCounts = [];
        foreach ($months as $monthNumber => $monthName) {
            $reportMonthData[] = $monthName; 
            $reportCounts[$monthNumber] = 0; 
        }
    
        foreach ($reportMonthCounts as $report) {
            $month = $report->_id; 
            $reportCounts[$month] = $report->count; 
        }


        $platformPipeline = [
            [
                '$unwind' => '$platformUsed'
            ],
            [
                '$group' => [
                    '_id' => '$platformUsed',
                    'count' => ['$sum' => 1]
                ]
            ],
            [
                '$sort' => ['count' => -1]
            ]
        ];
    
        $platformCounts = $reportsCollection->aggregate($platformPipeline);
        
        $platformLabels = [];
        $platformData = [];
        foreach ($platformCounts as $platform) {
            $platformLabels[] = $platform->_id;
            $platformData[] = $platform->count;
        }
        
        $admin = $adminCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($adminId)]);
    
        $firstName = $admin->first_name ?? '';
        $lastName = $admin->last_name ?? '';
        $email = $admin->email ?? '';
    
        return view('admin.dashboard', compact(
            'totalUsers', 
            'totalReports', 
            'toReviewCount',
            'underInvestigationCount', 
            'resolvedCount', 
            'firstName', 
            'lastName', 
            'email',
            'cyberbullyingData',
            'reportMonthData', 
            'reportCounts', 
            'platformData', 
            'platformLabels'
        )); 
    }    
    
    //show guidance dashboard page
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
    