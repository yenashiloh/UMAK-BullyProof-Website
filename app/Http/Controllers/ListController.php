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
        $reportCollection = $client->bullyproof->reports;
        $adminCollection = $client->bullyproof->admins;
        $studentsCollection = $client->bullyproof->students;
        
        // Get all reports
        $reports = $reportCollection->find()->toArray();
        
        // Get all students
        $allStudents = $studentsCollection->find()->toArray();
        
        // Track perpetrators and their violation counts
        $perpetrators = [];
        
        foreach ($reports as $report) {
            if (!empty($report->perpetratorName)) {
                $perpetratorName = trim(strtoupper($report->perpetratorName));
                $perpetratorRole = $report->perpetratorRole ?? 'Unknown';
                $perpetratorSchoolId = '';
                
                // Try to match perpetrator with a student record to get ID
                foreach ($allStudents as $student) {
                    $studentName = trim(strtoupper($student->name));
                    
                    $perpetratorName = str_replace('"', '', $perpetratorName);
                    $studentName = str_replace('"', '', $studentName);
                    
                    // Try with lastname, firstname format
                    $perpParts = explode(',', $perpetratorName);
                    $perpLastName = trim($perpParts[0] ?? '');
                    
                    if (!empty($perpLastName) && strpos($studentName, $perpLastName) !== false) {
                        $perpFirstNames = '';
                        if (isset($perpParts[1])) {
                            $perpFirstNames = trim($perpParts[1]);
                        }
                        
                        if (empty($perpFirstNames) || $this->hasAnyNamePartMatch($perpFirstNames, $studentName)) {
                            $perpetratorSchoolId = $student->schoolId;
                            break;
                        }
                    }
                    
                    // Try with space-separated names
                    if (empty($perpetratorSchoolId) && !str_contains($perpetratorName, ',')) {
                        $perpWords = preg_split('/\s+/', $perpetratorName);
                        $studentWords = preg_split('/\s+/', $studentName);
                        
                        $commonWords = array_intersect($perpWords, $studentWords);
                        
                        if (count($commonWords) >= 2) {
                            $perpetratorSchoolId = $student->schoolId;
                            break;
                        }
                    }
                }
                
                // Use school ID as the key if available, otherwise use name
                $key = !empty($perpetratorSchoolId) ? $perpetratorSchoolId : $perpetratorName;
                
                if (!isset($perpetrators[$key])) {
                    $perpetrators[$key] = [
                        'name' => $report->perpetratorName,
                        'idNumber' => $perpetratorSchoolId,
                        'role' => $perpetratorRole,
                        'count' => 1,
                        'reports' => [(string)$report->_id]
                    ];
                } else {
                    $perpetrators[$key]['count']++;
                    $perpetrators[$key]['reports'][] = (string)$report->_id;
                }
            }
        }
        
        // Convert to array format for view
        $filteredPerpetrators = array_values($perpetrators);
        
        // Get admin info
        $adminId = session('admin_id');
        $admin = $adminCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($adminId)]);
        $firstName = $admin->first_name ?? '';
        $lastName = $admin->last_name ?? '';
        $email = $admin->email ?? '';

        return view('admin.list.list-perpetrators', compact('filteredPerpetrators', 'firstName', 'lastName', 'email'));
    }

    // Add this helper function if it doesn't exist already
    private function hasAnyNamePartMatch($namePartsStr, $fullName)
    {
        $nameParts = preg_split('/\s+/', $namePartsStr);
        foreach ($nameParts as $part) {
            if (!empty($part) && strpos($fullName, trim($part)) !== false) {
                return true;
            }
        }
        return false;
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


    public function viewPerpetratorDiscipline($id)
    {
        $client = new Client(env('MONGODB_URI'));
        $reportCollection = $client->bullyproof->reports;
        $userCollection = $client->bullyproof->users;
        $adminCollection = $client->bullyproof->admins;
        $studentsCollection = $client->bullyproof->students;
    
        $adminId = session('admin_id');
        
        $admin = $adminCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($adminId)]);
        if (!$admin) {
            abort(404, 'Admin not found');
        }
    
        $firstName = $admin->first_name ?? '';
        $lastName = $admin->last_name ?? '';
        $email = $admin->email ?? '';
        
        // Check if the ID is a report ID or a perpetrator's ID number
        if (strlen($id) == 24 && ctype_xdigit($id)) {
            // This is likely a MongoDB ObjectId (report ID)
            $report = $reportCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
            if (!$report) {
                abort(404, 'Report not found');
            }
            
            $perpetratorName = $report->perpetratorName ?? '';
            $perpetratorIdNumber = $report->perpetratorSchoolId ?? '';
            $perpetratorRole = $report->perpetratorRole ?? '';
            
            // Find all reports with this perpetrator
            if (!empty($perpetratorName)) {
                $reports = $reportCollection->find([
                    'perpetratorName' => ['$regex' => $perpetratorName, '$options' => 'i']
                ])->toArray();
            } else {
                $reports = [$report];
            }
        } else {
            // This is a perpetrator's ID number
            $perpetratorIdNumber = $id;
            
            // Find student/perpetrator details from students collection
            $perpetrator = $studentsCollection->findOne(['schoolId' => $perpetratorIdNumber]);
            
            if ($perpetrator) {
                $perpetratorName = $perpetrator->name ?? 'Unknown';
                $perpetratorRole = 'Student';
                
                // Find all reports with this perpetrator name
                $reports = $reportCollection->find([
                    '$or' => [
                        ['perpetratorSchoolId' => $perpetratorIdNumber],
                        ['perpetratorName' => ['$regex' => $perpetratorName, '$options' => 'i']]
                    ]
                ])->toArray();
            } else {
                // If not found in students, search directly in reports
                $reports = $reportCollection->find([
                    'perpetratorSchoolId' => $perpetratorIdNumber
                ])->toArray();
                
                // Get first report to extract perpetrator details
                $firstReport = !empty($reports) ? $reports[0] : null;
                $perpetratorName = $firstReport->perpetratorName ?? 'Unknown';
                $perpetratorRole = $firstReport->perpetratorRole ?? 'Unknown';
            }
        }
        
        // Format the reports data
        $formattedReports = [];
        foreach ($reports as $report) {
            // Find reporter details
            $reporterId = $report->reportedBy ?? null;
            $reporter = null;
            
            if ($reporterId) {
                if ($reporterId instanceof \MongoDB\BSON\ObjectId) {
                    $reporter = $userCollection->findOne(['_id' => $reporterId]);
                } else {
                    $reporter = $userCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId((string)$reporterId)]);
                }
            }
            
            $reporterName = '';
            if ($reporter) {
                $reporterName = $reporter->fullname ?? '';
            } else {
                $reporterName = $report->reporterFullName ?? 'Unknown';
            }
            
            $reportDate = null;
            if (isset($report->reportDate)) {
                if ($report->reportDate instanceof \MongoDB\BSON\UTCDateTime) {
                    $reportDate = $report->reportDate->toDateTime();
                } else {
                    $reportDate = new \DateTime($report->reportDate);
                }
            } else {
                $reportDate = new \DateTime();
            }
            
            $formattedReports[] = [
                '_id' => (string)$report->_id,
                'reportDate' => $reportDate,
                'reporterFullName' => $reporterName,
                'status' => $report->status ?? 'For Review',
                'perpetratorName' => $report->perpetratorName ?? $perpetratorName,
                'perpetratorRole' => $report->perpetratorRole ?? $perpetratorRole,
                'perpetratorSchoolId' => $report->perpetratorSchoolId ?? $perpetratorIdNumber,
            ];
        }
        
        $perpetratorDetails = [
            'name' => $perpetratorName,
            'idNumber' => $perpetratorIdNumber,
            'role' => $perpetratorRole,
            'count' => count($formattedReports)
        ];
        
        // Set null values for variables that may be expected by the view
        $reportData = null;
        $reporterData = null;
        
        return view('admin.list.view-perpertrators', compact(
            'firstName',
            'lastName',
            'email',
            'perpetratorDetails',
            'reportData',
            'reporterData'
        ) + ['reports' => $formattedReports]);
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
            $displayedVictimRelationship = $report->victimRelationship;
        }

        // Reporter data
        $reporterData = [
            'fullname' => $reporter->fullname ?? '',
            'idnumber' => $reporter->idnumber ?? '',
            'type' => $reporter->type ?? '',
            'position' => $reporter->position ?? ''
        ];

        // Support types
        $reportData = [
            '_id' => (string)$report->_id, 
            'reportDate' => $report->reportDate->toDateTime()->format('Y-m-d H:i:s'),
            'victimRelationship' => $displayedVictimRelationship,
            'idNumber' => $report->idNumber ?? '',  
            'remarks' => $report->remarks ?? '',  
            'reporterFullName' => $reporter->fullname,
            'reporterEmail' => $reporter->email,
            'hasReportedBefore' => $report->hasReportedBefore ?? 'N/A',
            'submitAs'=> $report->submitAs ?? 'N/A',
            'reportedTo' => $report->reportedTo ?? 'N/A',
            'platformUsed' => $report->platformUsed instanceof \MongoDB\Model\BSONArray ? $report->platformUsed->getArrayCopy() : [],
            'cyberbullyingTypes' => $report->cyberbullyingTypes instanceof \MongoDB\Model\BSONArray ? $report->cyberbullyingTypes->getArrayCopy() : [],
            'otherPlatformUsed' => $report->otherPlatformUsed,
            'supportTypes' => $report->supportTypes instanceof \MongoDB\Model\BSONArray ? $report->supportTypes->getArrayCopy() : [],
            'otherSupportTypes' => $report->otherSupportTypes,
            'incidentDetails' => $incidentDetails,
            'perpetratorName' => $report->perpetratorName,
            'perpetratorRole' => $report->perpetratorRole,
            'perpetratorGradeYearLevel' => $report->perpetratorGradeYearLevel,
            'actionsTaken' => $report->actionsTaken ?? 'N/A',
            'describeActions' => $report->describeActions ?? 'N/A',

            'incidentEvidence' => $report->incidentEvidence,



            'analysisResult' => $analysisResult['analysisResult'],
            'analysisProbability' => $analysisResult['analysisProbability'],

            'departmentCollege' => $report->departmentCollege,

            'witnessChoice' => $report->witnessChoice,
            'contactChoice' => $report->contactChoice,
            
        ];

        if (!empty($reportData['otherPlatformUsed'])) {
            $reportData['platformUsed'][] = $reportData['otherPlatformUsed'];
        }
        
        $reportData['platformUsed'] = array_filter($reportData['platformUsed'], function($platform) {
            return $platform !== "Others (Please Specify)";
        });
        $reportData['platformUsed'] = array_values($reportData['platformUsed']);
    
        if (!empty($analysisResult['error'])) {
            $reportData['error'] = $analysisResult['error'];
        }
    
        //otherSupportTypes
        if (!empty($report->otherSupportTypes)) {
            $reportData['supportTypes'][] = $report->otherSupportTypes;
        }

        //remove "Others (Please Specify"
        $reportData['supportTypes'] = array_filter($reportData['supportTypes'], function($supportType) {
            return $supportType !== "Others (Please Specify)";
        });

        $reportData['supportTypes'] = array_values($reportData['supportTypes']);

        // Perpetrator school ID
        $perpetratorSchoolId = '';
        if (!empty($report->perpetratorName)) {
            $perpetratorName = trim(strtoupper($report->perpetratorName));
            $studentsCollection = $client->bullyproof->students;
            $allStudents = $studentsCollection->find()->toArray();
            
            foreach ($allStudents as $student) {
                $studentName = trim(strtoupper($student->name));
                
                $perpetratorName = str_replace('"', '', $perpetratorName);
                $studentName = str_replace('"', '', $studentName);
                
                $perpParts = explode(',', $perpetratorName);
                $perpLastName = trim($perpParts[0] ?? '');
                
                if (!empty($perpLastName) && strpos($studentName, $perpLastName) !== false) {
                    $perpFirstNames = '';
                    if (isset($perpParts[1])) {
                        $perpFirstNames = trim($perpParts[1]);
                    }
                    
                    if (empty($perpFirstNames) || $this->hasAnyNamePartMatch($perpFirstNames, $studentName)) {
                        $perpetratorSchoolId = $student->schoolId;
                        break;
                    }
                }
                
                if (empty($perpetratorSchoolId) && !str_contains($perpetratorName, ',')) {
                    $perpWords = preg_split('/\s+/', $perpetratorName);
                    $studentWords = preg_split('/\s+/', $studentName);
                    
                    $commonWords = array_intersect($perpWords, $studentWords);
                    
                    if (count($commonWords) >= 2) {
                        $perpetratorSchoolId = $student->schoolId;
                        break;
                    }
                }
            }
        }
        
        $reportData['perpetratorSchoolId'] = $perpetratorSchoolId;
    
        return view('admin.list.view-report', compact(
            'firstName',
            'lastName',
            'email',
            'reportData',
            'reporterData'
        ));
    }
}
