<?php

namespace App\Http\Controllers;
use MongoDB\Client;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //show discipline dashboard page
    public function showDisciplineDashboard(Request $request)
    {
        $adminId = session('admin_id');
        if (!$adminId) {
            return redirect()->route('admin.login')->with('error', 'Please login to access the dashboard');
        }
    
        $client = new Client(env('MONGODB_URI'));
        $userCollection = $client->bullyproof->users;
        $adminCollection = $client->bullyproof->admins;
        $reportsCollection = $client->bullyproof->reports;
        $appointmentsCollection = $client->bullyproof->appointments;
    
        $startDate = $request->input('start_date', date('Y-m-d', strtotime('first day of january this year')));
        $endDate = $request->input('end_date', date('Y-m-d'));
    
        $startDateTime = new \MongoDB\BSON\UTCDateTime(strtotime($startDate) * 1000);
        $endDateTime = new \MongoDB\BSON\UTCDateTime(strtotime($endDate . ' 23:59:59') * 1000);
    
        $dateFilter = [
            'reportDate' => [
                '$gte' => $startDateTime,
                '$lte' => $endDateTime
            ]
        ];
    
        $totalUsers = $userCollection->countDocuments();
        $totalReports = $reportsCollection->countDocuments($dateFilter);
        $toReviewCount = $reportsCollection->countDocuments(['status' => 'For Review'] + $dateFilter);
        $underInvestigationCount = $reportsCollection->countDocuments(['status' => 'Under Investigation'] + $dateFilter);
        $resolvedCount = $reportsCollection->countDocuments(['status' => 'Resolved'] + $dateFilter);
        
        // New status counts
        $dismissedCount = $reportsCollection->countDocuments(['status' => 'Dismissed'] + $dateFilter);
        $underMediationCount = $reportsCollection->countDocuments(['status' => 'Under Mediation'] + $dateFilter);
        $reopenedCount = $reportsCollection->countDocuments(['status' => 'Reopened'] + $dateFilter);
        $awaitingResponseCount = $reportsCollection->countDocuments(['status' => 'Awaiting Response'] + $dateFilter);
        $withdrawnCount = $reportsCollection->countDocuments(['status' => 'Withdrawn'] + $dateFilter);
        
        $waitingForConfirmationCount = $appointmentsCollection->countDocuments([
            'status' => 'Waiting for Confirmation',
            'created_at' => [
                '$gte' => new \MongoDB\BSON\UTCDateTime((new \DateTime($startDate))->getTimestamp() * 1000),
                '$lte' => new \MongoDB\BSON\UTCDateTime((new \DateTime($endDate . ' 23:59:59'))->getTimestamp() * 1000),
            ],
        ]);
    
        // Cyberbullying Type Pipeline
        $pipeline = [
            [
                '$match' => $dateFilter
            ],
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
    
        // Monthly Reports Pipeline
        $monthPipeline = [
            [
                '$match' => $dateFilter
            ],
            [
                '$group' => [
                    '_id' => [
                        '$dateToString' => [
                            'format' => '%Y-%m',
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
    
        $reportMonthData = [];
        $reportCounts = [];
        
        $period = new \DatePeriod(
            new \DateTime($startDate),
            new \DateInterval('P1M'),
            new \DateTime($endDate)
        );
    
        foreach ($period as $date) {
            $monthKey = $date->format('Y-m');
            $reportMonthData[] = $date->format('F Y');
            $reportCounts[$monthKey] = 0;
        }
    
        foreach ($reportMonthCounts as $report) {
            $monthKey = $report->_id;
            if (isset($reportCounts[$monthKey])) {
                $reportCounts[$monthKey] = $report->count;
            }
        }
    
        // Platform Pipeline
        $platformPipeline = [
            [
                '$match' => $dateFilter
            ],
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
        if (!$admin) {
            return redirect()->route('admin.login')->with('error', 'Admin not found');
        }
    
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
            'platformLabels',
            'startDate',
            'endDate',
            'waitingForConfirmationCount',
            'dismissedCount',
            'underMediationCount',
            'reopenedCount',
            'awaitingResponseCount',
            'withdrawnCount'
          
        ));
    }
    
    public function showSuperAdminDashboard(Request $request)
    {
        $adminId = session('admin_id');
        if (!$adminId) {
            return redirect()->route('admin.login')->with('error', 'Please login to access the dashboard');
        }
    
        $client = new Client(env('MONGODB_URI'));
        $userCollection = $client->bullyproof->users;
        $adminCollection = $client->bullyproof->admins;
        $reportsCollection = $client->bullyproof->reports;
    
        $startDate = $request->input('start_date', date('Y-m-d', strtotime('first day of january this year')));
        $endDate = $request->input('end_date', date('Y-m-d'));
    
        $startDateTime = new \MongoDB\BSON\UTCDateTime(strtotime($startDate) * 1000);
        $endDateTime = new \MongoDB\BSON\UTCDateTime(strtotime($endDate . ' 23:59:59') * 1000);
    
        $dateFilter = [
            'reportDate' => [
                '$gte' => $startDateTime,
                '$lte' => $endDateTime
            ]
        ];
    
        $totalUsers = $userCollection->countDocuments();
        $totalReports = $reportsCollection->countDocuments($dateFilter);
        $toReviewCount = $reportsCollection->countDocuments(['status' => 'For Review'] + $dateFilter);
        $underInvestigationCount = $reportsCollection->countDocuments(['status' => 'Under Investigation'] + $dateFilter);
        $resolvedCount = $reportsCollection->countDocuments(['status' => 'Resolved'] + $dateFilter);
    
        // Cyberbullying Type Pipeline
        $pipeline = [
            [
                '$match' => $dateFilter
            ],
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
    
        // Monthly Reports Pipeline
        $monthPipeline = [
            [
                '$match' => $dateFilter
            ],
            [
                '$group' => [
                    '_id' => [
                        '$dateToString' => [
                            'format' => '%Y-%m',
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
    
        $reportMonthData = [];
        $reportCounts = [];
        
        $period = new \DatePeriod(
            new \DateTime($startDate),
            new \DateInterval('P1M'),
            new \DateTime($endDate)
        );
    
        foreach ($period as $date) {
            $monthKey = $date->format('Y-m');
            $reportMonthData[] = $date->format('F Y');
            $reportCounts[$monthKey] = 0;
        }
    
        foreach ($reportMonthCounts as $report) {
            $monthKey = $report->_id;
            if (isset($reportCounts[$monthKey])) {
                $reportCounts[$monthKey] = $report->count;
            }
        }
    
        // Platform Pipeline
        $platformPipeline = [
            [
                '$match' => $dateFilter
            ],
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
        if (!$admin) {
            return redirect()->route('admin.login')->with('error', 'Admin not found');
        }
    
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
            'platformLabels',
            'startDate',
            'endDate',
          
        ));
    }
}
    