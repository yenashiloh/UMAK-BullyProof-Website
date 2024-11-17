<?php

namespace App\Http\Controllers;
use App\Services\CyberbullyingDetectionService;
use Illuminate\Http\Request;
use MongoDB\Client;


class ReportsController extends Controller
{
    protected $detectionService;

    public function __construct(CyberbullyingDetectionService $detectionService) 
    {
        $this->detectionService = $detectionService;
    }

    //show reports in discipline side
    public function showReportsDiscipline()
    {
        $client = new Client(env('MONGODB_URI'));
        $userCollection = $client->bullyproof->users;
        $adminCollection = $client->bullyproof->admins;
        $reportCollection = $client->bullyproof->reports;
    
        $adminId = session('admin_id');
        $admin = $adminCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($adminId)]);
        $firstName = $admin->first_name ?? '';
        $lastName = $admin->last_name ?? '';
        $email = $admin->email ?? '';
    
        $reports = $reportCollection->aggregate([
            [
                '$lookup' => [
                    'from' => 'users',
                    'localField' => 'reportedBy',
                    'foreignField' => '_id',
                    'as' => 'reporter'
                ]
            ],
            [
                '$unwind' => '$reporter'
            ],
            [
                '$project' => [
                    'reportDate' => 1,
                    'victimName' => 1,
                    'perpetratorName' => 1,
                    'gradeYearLevel' => 1,
                    'status' => 1,
                    'reporterFullName' => '$reporter.fullname',
                    'reporterEmail' => '$reporter.email'
                ]
            ],
            [
                '$sort' => ['reportDate' => -1]
            ]
        ])->toArray();
    
        return view('admin.reports.incident-reports', compact(
            'firstName', 
            'lastName', 
            'email',
            'reports'
        )); 
    }

    //show reports in guidance side
    public function showReportsGuidance()
    {
        $client = new Client(env('MONGODB_URI'));
        $userCollection = $client->bullyproof->users;
        $adminCollection = $client->bullyproof->admins;
        $reportCollection = $client->bullyproof->reports;
    
        $adminId = session('admin_id');
        $admin = $adminCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($adminId)]);
        $firstName = $admin->first_name ?? '';
        $lastName = $admin->last_name ?? '';
        $email = $admin->email ?? '';
    
        $reports = $reportCollection->aggregate([
            [
                '$lookup' => [
                    'from' => 'users',
                    'localField' => 'reportedBy',
                    'foreignField' => '_id',
                    'as' => 'reporter'
                ]
            ],
            [
                '$unwind' => '$reporter'
            ],
            [
                '$project' => [
                    'reportDate' => 1,
                    'victimName' => 1,
                    'gradeYearLevel' => 1,
                    'reporterFullName' => '$reporter.fullname',
                    'reporterEmail' => '$reporter.email'
                ]
            ],
            [
                '$sort' => ['reportDate' => -1]
            ]
        ])->toArray();
    
        return view('guidance.reports.incident-reports', compact(
            'firstName', 
            'lastName', 
            'email',
            'reports'
        )); 
    }

    public function viewReportDiscipline($id) 
    {
        $client = new Client(env('MONGODB_URI'));
        $reportCollection = $client->bullyproof->reports;
        $userCollection = $client->bullyproof->users;
        $adminCollection = $client->bullyproof->admins;
    
        $adminId = session('admin_id');
    
        $report = $reportCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
        if (!$report) {
            abort(404, 'Report not found');
        }
    
        $admin = $adminCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($adminId)]);
        if (!$admin) {
            abort(404, 'Admin not found');
        }
    
        $firstName = $admin->first_name ?? '';
        $lastName = $admin->last_name ?? '';
        $email = $admin->email ?? '';
    
        $reporter = $userCollection->findOne(['_id' => $report->reportedBy]);
        if (!$reporter) {
            abort(404, 'Reporter not found');
        }

        // Get incident details from the report
        $incidentDetails = $report->incidentDetails ?? '';
        
        // Analyze the incident details using the detection service
        $analysisResult = $this->detectionService->analyze($incidentDetails);

        $reportData = [
            'reportDate' => $report->reportDate->toDateTime()->format('Y-m-d H:i:s'),
            'victimRelationship' => $report->victimRelationship,
            'victimName' => $report->victimName,
            'victimType' => $report->victimType,
            'gradeYearLevel' => $report->gradeYearLevel,
            'reporterFullName' => $reporter->fullname,
            'reporterEmail' => $reporter->email,
            'hasReportedBefore' => $report->hasReportedBefore ?? 'N/A',
            'reportedTo' => $report->reportedTo ?? 'N/A',
            'platformUsed' => $report->platformUsed instanceof \MongoDB\Model\BSONArray ? $report->platformUsed->getArrayCopy() : [],
            'cyberbullyingType' => $report->cyberbullyingType instanceof \MongoDB\Model\BSONArray ? $report->cyberbullyingType->getArrayCopy() : [],
            'incidentDetails' => $incidentDetails,
            'perpetratorName' => $report->perpetratorName,
            'perpetratorRole' => $report->perpetratorRole,
            'perpetratorGradeYearLevel' => $report->perpetratorGradeYearLevel,
            'actionsTaken' => $report->actionsTaken ?? 'N/A',
            'describeActions' => $report->describeActions ?? 'N/A',
            'incidentEvidence' => $report->incidentEvidence instanceof \MongoDB\Model\BSONArray ? $report->incidentEvidence->getArrayCopy() : [],
            // Add the analysis results from the Python script
            'analysisResult' => $analysisResult['analysisResult'],
            'analysisProbability' => $analysisResult['analysisProbability']
        ];


        // Add error to reportData if present
        if (!empty($analysisResult['error'])) {
            $reportData['error'] = $analysisResult['error'];
        }
    
        return view('admin.reports.view', compact(
            'firstName',
            'lastName',
            'email',
            'reportData'
        ));
    }
    
    //view report for guidance
    public function viewReportGuidance($id)
    {
        $client = new Client(env('MONGODB_URI'));
        $reportCollection = $client->bullyproof->reports;
        $userCollection = $client->bullyproof->users;
        $adminCollection = $client->bullyproof->admins;

        $adminId = session('admin_id');
        $report = $reportCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
        $admin = $adminCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($adminId)]);
        $firstName = $admin->first_name ?? '';
        $lastName = $admin->last_name ?? '';
        $email = $admin->email ?? '';

    
        $reporter = $userCollection->findOne(['_id' => $report->reportedBy]);

        $reportData = [
            'reportDate' => $report->reportDate->toDateTime()->format('Y-m-d H:i:s'),
            'victimRelationship' => $report->victimRelationship,
            'victimName' => $report->victimName,
            'victimType' => $report->victimType,
            'gradeYearLevel' => $report->gradeYearLevel,
            'reporterFullName' => $reporter->fullname,
            'reporterEmail' => $reporter->email,
            'hasReportedBefore' => $report->hasReportedBefore ?? 'N/A', 
            'reportedTo' => $report->reportedTo ?? 'N/A', 
            'platformUsed' => $report->platformUsed instanceof \MongoDB\Model\BSONArray ? $report->platformUsed->getArrayCopy() : [],
            'cyberbullyingType' => $report->cyberbullyingType instanceof \MongoDB\Model\BSONArray ? $report->cyberbullyingType->getArrayCopy() : [],
            'incidentDetails' => $report->incidentDetails ?? 'N/A',
            'perpetratorName' => $report->perpetratorName,
            'perpetratorRole' => $report->perpetratorRole,
            'perpetratorGradeYearLevel' => $report->perpetratorGradeYearLevel,
            'actionsTaken' => $report->actionsTaken ?? 'N/A',
            'describeActions' => $report->describeActions ?? 'N/A',
            'incidentEvidence' => $report->incidentEvidence instanceof \MongoDB\Model\BSONArray ? $report->incidentEvidence->getArrayCopy() : [],
        ];
 
        return view('guidance.reports.view', compact(
            'firstName', 
            'lastName', 
            'email',
            'reportData'));
    }

    //change status
    public function changeStatus($id)
    {
        $client = new Client(env('MONGODB_URI'));
        $reportCollection = $client->bullyproof->reports;

        $report = $reportCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);

        if (!$report) {
            return redirect()->back()->with('error', 'Report not found.');
        }

        $currentStatus = $report->status;
        $newStatus = '';

        if ($currentStatus == 'For Review') {
            $newStatus = 'Under Investigation';
        } elseif ($currentStatus == 'Under Investigation') {
            $newStatus = 'Resolved';
        } else {
            return redirect()->back()->with('error', 'Invalid status change.');
        }

        $reportCollection->updateOne(
            ['_id' => new \MongoDB\BSON\ObjectId($id)],
            ['$set' => ['status' => $newStatus]]
        );

        return redirect()->back()->with('success', 'Status updated successfully.');
    }

}
