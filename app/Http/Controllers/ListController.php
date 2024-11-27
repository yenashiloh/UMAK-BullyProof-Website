<?php

namespace App\Http\Controllers;
use MongoDB\Client;
use Illuminate\Http\Request;
use App\Services\CyberbullyingDetectionService;

class ListController extends Controller
{
    protected $detectionService;

    public function __construct(CyberbullyingDetectionService $detectionService) 
    {
        $this->detectionService = $detectionService;
    }
    
    public function showListOfPerpetrators()
    {
        $client = new Client(env('MONGODB_URI'));
        $userCollection = $client->bullyproof->users;
        $adminCollection = $client->bullyproof->admins;
        $reportCollection = $client->bullyproof->reports;
        
        $reports = $reportCollection->find()->toArray();
    
        // Count incidents by ID Number
        $incidentCounts = [];
        foreach ($reports as $report) {
            if (isset($report->idNumber)) {
                $idNumber = $report->idNumber;
                if (!isset($incidentCounts[$idNumber])) {
                    $incidentCounts[$idNumber] = 0;
                }
                $incidentCounts[$idNumber]++;
            }
        }
    
        // Filter to keep only the first occurrence of each ID Number
        $filteredReports = [];
        $seenIdNumbers = [];
        foreach ($reports as $report) {
            if (isset($report->idNumber)) {
                $idNumber = $report->idNumber;
                if (!in_array($idNumber, $seenIdNumbers)) {
                    $report->incidentCount = $incidentCounts[$idNumber] ?? 0; // Add the incident count
                    $filteredReports[] = $report; // Add to filtered reports
                    $seenIdNumbers[] = $idNumber; // Mark this ID as seen
                }
            }
        }
    
        $adminId = session('admin_id');
        $admin = $adminCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($adminId)]);
        $firstName = $admin->first_name ?? '';
        $lastName = $admin->last_name ?? '';
        $email = $admin->email ?? '';
    
        return view('admin.list.list-perpetrators', compact('filteredReports', 'firstName', 'lastName', 'email'));
    }
    
    public function viewReportsByIdNumber($idNumber)
    {
        $client = new Client(env('MONGODB_URI'));
        $reportCollection = $client->bullyproof->reports;
        $userCollection = $client->bullyproof->users;
        $adminCollection = $client->bullyproof->admins;

        // Fetch all reports related to the given idNumber
        $reports = $reportCollection->find(['idNumber' => $idNumber])->toArray();

        $adminId = session('admin_id');
        $admin = $adminCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($adminId)]);
        $firstName = $admin->first_name ?? '';
        $lastName = $admin->last_name ?? '';
        $email = $admin->email ?? '';
    

        return view('admin.list.view-perpertrators', compact('reports', 'idNumber', 'firstName', 'lastName', 'email'));
    }


    public function viewPerpetratorDiscipline($id, CyberbullyingClassifier $classifier)
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
            'incidentDetails' => $report->incidentDetails ?? 'N/A',
            'perpetratorName' => $report->perpetratorName,
            'perpetratorRole' => $report->perpetratorRole,
            'perpetratorGradeYearLevel' => $report->perpetratorGradeYearLevel,
            'actionsTaken' => $report->actionsTaken ?? 'N/A',
            'describeActions' => $report->describeActions ?? 'N/A',
        ];
    
        // Classify the incident
        $classificationResult = $classifier->detectCyberbullying($reportData['incidentDetails']);
       $reportData['isCyberbullying'] = $classificationResult['isCyberbullying'];
       $reportData['cyberbullyingPercentage'] = $classificationResult['cyberbullyingPercentage'];
       $reportData['detectedWords'] = $classificationResult['detectedWords'];

        return view('admin.list.view-perpertrators', compact(
            'firstName',
            'lastName',
            'email',
            'reportData'
        ));
    }

    //show complainee page
    public function showAddComplainee()
    {
        $client = new Client(env('MONGODB_URI'));
        $userCollection = $client->bullyproof->users;
        $adminCollection = $client->bullyproof->admins;
        $reportCollection = $client->bullyproof->reports;

        $reports = $reportCollection->find()->toArray();

        $adminId = session('admin_id');
        $admin = $adminCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($adminId)]);
        $firstName = $admin->first_name ?? '';
        $lastName = $admin->last_name ?? '';
        $email = $admin->email ?? '';

        return view('admin.list.add-complainee', compact('reports', 'firstName', 'lastName', 'email')); 
    }
    
    //view report 
    public function viewReportForComplainee($id) 
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
    
        $incidentDetails = $report->incidentDetails ?? '';
        $analysisResult = $this->detectionService->analyze($incidentDetails);
    
        $displayedVictimRelationship = '';
        if (!empty($report->otherVictimRelationship)) {
            $displayedVictimRelationship = $report->otherVictimRelationship;
        } elseif ($report->victimRelationship === "Other" && !empty($report->otherVictimRelationship)) {
            $displayedVictimRelationship = $report->otherVictimRelationship;
        } else {
            $displayedVictimRelationship = $report->victimRelationship ?? '';
        }
    
        $reportData = [
            '_id' => (string)$report->_id, 
            'reportDate' => $report->reportDate->toDateTime()->format('Y-m-d H:i:s'),
            'victimRelationship' => $displayedVictimRelationship,
            'otherVictimRelationship' => $report->otherVictimRelationship ?? '',
            'victimName' => $report->victimName ?? '',
            'victimType' => $report->victimType ?? '',
            'gradeYearLevel' => $report->gradeYearLevel ?? '',
            'idNumber' => $report->idNumber ?? '',  
            'remarks' => $report->remarks ?? '',  
            'reporterFullName' => $reporter->fullname ?? '', 
            'reporterEmail' => $reporter->email ?? '', 
            'hasReportedBefore' => $report->hasReportedBefore ?? 'N/A',
            'reportedTo' => $report->reportedTo ?? 'N/A',
            'platformUsed' => $report->platformUsed instanceof \MongoDB\Model\BSONArray ? $report->platformUsed->getArrayCopy() : [],
            'otherPlatformUsed' => $report->otherPlatformUsed ?? '', 
            'supportTypes' => $report->supportTypes instanceof \MongoDB\Model\BSONArray ? $report->supportTypes->getArrayCopy() : [],
            'otherSupportTypes' => $report->otherSupportTypes ?? '', 
            'incidentDetails' => $incidentDetails,
            'perpetratorName' => $report->perpetratorName ?? '', 
            'perpetratorRole' => $report->perpetratorRole ?? '', 
            'perpetratorGradeYearLevel' => $report->perpetratorGradeYearLevel ?? '', 
            'actionsTaken' => $report->actionsTaken ?? 'N/A',
            'describeActions' => $report->describeActions ?? 'N/A',
            'incidentEvidence' => $report->incidentEvidence instanceof \MongoDB\Model\BSONArray ? $report->incidentEvidence->getArrayCopy() : [],
            'analysisResult' => $analysisResult['analysisResult'] ?? '', 
            'analysisProbability' => $analysisResult['analysisProbability'] ?? ''
        ];
    
        if (!empty($reportData['otherPlatformUsed'])) {
            $reportData['platformUsed'][] = $reportData['otherPlatformUsed'];
        }
    
        $reportData['platformUsed'] = array_filter($reportData['platformUsed'], function($platform) {
            return $platform !== "Others (Please Specify)";
        });
    
        $reportData['platformUsed'] = array_values($reportData['platformUsed']);
    
        if (!empty($report->otherSupportTypes)) {
            $reportData['supportTypes'][] = $report->otherSupportTypes;
        }
    
        $reportData['supportTypes'] = array_filter($reportData['supportTypes'], function($supportType) {
            return $supportType !== "Others (Please Specify)";
        });
    
        $reportData['supportTypes'] = array_values($reportData['supportTypes']);
    
        if (!empty($analysisResult['error'])) {
            $reportData['error'] = $analysisResult['error'];
        }
    
        $idNumber = $report->idNumber ?? ''; 
    
        return view('admin.list.view-report', compact(
            'firstName',
            'lastName',
            'email',
            'reportData',
            'idNumber'
        ));
    }
}
