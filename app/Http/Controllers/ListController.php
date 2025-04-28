<?php

namespace App\Http\Controllers;

use App\Services\CyberbullyingDetectionService;
use Illuminate\Http\Request;
use MongoDB\Client;

class ListController extends Controller
{
    protected $detectionService;

    public function __construct(CyberbullyingDetectionService $detectionService) 
    {
        $this->detectionService = $detectionService;
    }

    //show list of perpetrators
    public function showListOfPerpetrators()
    {
        $client = new Client(env('MONGODB_URI'));
        $reportCollection = $client->bullyproof->reports;
        $adminCollection = $client->bullyproof->admins;
        $studentsCollection = $client->bullyproof->students;
    
        $reports = $reportCollection->find([
            'perpetratorName' => ['$exists' => true, '$ne' => '']
        ])->toArray();
    
        $allStudents = $studentsCollection->find([], [
            'projection' => ['name' => 1, 'schoolId' => 1]
        ])->toArray();
    
        $studentsMap = [];
        foreach ($allStudents as $student) {
            if (!isset($student->name) || !isset($student->schoolId)) continue;
    
            $studentName = trim(strtoupper($student->name));
            $studentName = str_replace('"', '', $studentName);
            $studentsMap[$studentName] = $student->schoolId;
    
            $nameParts = preg_split('/\s+|,\s*/', $studentName);
            foreach ($nameParts as $part) {
                if (strlen($part) > 2) {
                    if (!isset($studentsMap['PART:' . $part])) {
                        $studentsMap['PART:' . $part] = [];
                    }
                    $studentsMap['PART:' . $part][] = [
                        'fullName' => $studentName,
                        'schoolId' => $student->schoolId
                    ];
                }
            }
        }
    
        $perpetrators = [];
    
        foreach ($reports as $report) {
            if (!empty($report->perpetratorName)) {
                $perpetratorName = trim(strtoupper($report->perpetratorName));
                $perpetratorRole = $report->perpetratorRole ?? 'Unknown';
                $perpetratorSchoolId = '';
    
                $perpetratorName = str_replace('"', '', $perpetratorName);
    
                if (isset($studentsMap[$perpetratorName])) {
                    $perpetratorSchoolId = $studentsMap[$perpetratorName];
                } else {
                    $perpParts = explode(',', $perpetratorName);
                    $perpLastName = trim($perpParts[0] ?? '');
    
                    if (!empty($perpLastName) && isset($studentsMap['PART:' . $perpLastName])) {
                        foreach ($studentsMap['PART:' . $perpLastName] as $possibleMatch) {
                            $studentFullName = $possibleMatch['fullName'];
    
                            if (isset($perpParts[1])) {
                                $perpFirstNames = trim($perpParts[1]);
                                $perpFirstParts = preg_split('/\s+/', $perpFirstNames);
    
                                foreach ($perpFirstParts as $firstPart) {
                                    if (strlen($firstPart) > 2 && strpos($studentFullName, $firstPart) !== false) {
                                        $perpetratorSchoolId = $possibleMatch['schoolId'];
                                        break 2;
                                    }
                                }
                            } else {
                                $perpetratorSchoolId = $possibleMatch['schoolId'];
                                break;
                            }
                        }
                    }
    
                    if (empty($perpetratorSchoolId)) {
                        $perpWords = preg_split('/\s+/', $perpetratorName);
                        $matchScores = [];
    
                        foreach ($perpWords as $word) {
                            if (strlen($word) <= 2) continue;
    
                            if (isset($studentsMap['PART:' . $word])) {
                                foreach ($studentsMap['PART:' . $word] as $possibleMatch) {
                                    $studentId = $possibleMatch['schoolId'];
                                    if (!isset($matchScores[$studentId])) {
                                        $matchScores[$studentId] = 0;
                                    }
                                    $matchScores[$studentId]++;
                                }
                            }
                        }
    
                        arsort($matchScores);
                        $bestMatches = array_keys($matchScores);
    
                        if (!empty($bestMatches) && $matchScores[$bestMatches[0]] >= 2) {
                            $perpetratorSchoolId = $bestMatches[0];
                        }
                    }
                }
    
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
    
        $filteredPerpetrators = array_values($perpetrators);
    
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

    //View Complainees
    public function viewPerpetratorDiscipline($identifier)
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
        
        // Initialize variables
        $perpetratorName = '';
        $perpetratorRole = '';
        $perpetratorIdNumber = '';
        $student = null;
        
        // Special handling for "Not identified" cases
        if ($identifier === 'Not identified') {
            $perpetratorIdNumber = 'Not identified';
        }
        // Check if identifier is a URL-encoded name (decode it)
        else if (strpos($identifier, '%') !== false) {
            $decodedName = urldecode($identifier);
            $perpetratorName = $decodedName;
            
            // Try to find the student by name
            $student = $studentsCollection->findOne(['name' => new \MongoDB\BSON\Regex('^'.preg_quote($decodedName).'$', 'i')]);
            if ($student) {
                $perpetratorIdNumber = $student->schoolId ?? 'Not identified';
                $perpetratorRole = 'Student';
            }
        } 
        // Check if identifier is a school ID (k prefix or A prefix or numeric)
        else if (preg_match('/^[kKaA][0-9]/i', $identifier) || is_numeric($identifier)) {
            $perpetratorIdNumber = $identifier;
            $student = $studentsCollection->findOne(['schoolId' => $identifier]);
            
            if ($student) {
                $perpetratorName = $student->name ?? '';
                $perpetratorRole = 'Student';
            } else {
                // Try to find from reports if not found in students
                $report = $reportCollection->findOne(['perpetratorSchoolId' => $identifier]);
                if ($report) {
                    $perpetratorName = $report->perpetratorName ?? '';
                    $perpetratorRole = $report->perpetratorRole ?? 'Unknown';
                }
            }
        } 
        // Check if identifier is a MongoDB Object ID
        else if (strlen($identifier) == 24 && ctype_xdigit($identifier)) {
            try {
                $report = $reportCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($identifier)]);
                
                if ($report) {
                    $perpetratorName = $report->perpetratorName ?? '';
                    $perpetratorIdNumber = $report->perpetratorSchoolId ?? 'Not identified';
                    $perpetratorRole = $report->perpetratorRole ?? 'Unknown';
                }
            } catch (\Exception $e) {
                // Handle exception
            }
        }
        // Assume identifier is a plain name
        else {
            $perpetratorName = $identifier;
            
            // Try to find in students collection
            $student = $studentsCollection->findOne([
                'name' => new \MongoDB\BSON\Regex('^'.preg_quote($identifier).'$', 'i')
            ]);
            
            if ($student) {
                $perpetratorIdNumber = $student->schoolId ?? 'Not identified';
                $perpetratorRole = 'Student';
            } else {
                // Check reports for this name
                $report = $reportCollection->findOne([
                    'perpetratorName' => new \MongoDB\BSON\Regex('^'.preg_quote($identifier).'$', 'i')
                ]);
                
                if ($report) {
                    $perpetratorIdNumber = $report->perpetratorSchoolId ?? 'Not identified';
                    $perpetratorRole = $report->perpetratorRole ?? 'Unknown';
                } else {
                    $perpetratorIdNumber = 'Not identified';
                }
            }
        }
        
        // Now get all reports related to this perpetrator by name or school ID
        $allReports = [];
        
        // Find by exact ID match (if we have an ID and it's not "Not identified")
        if (!empty($perpetratorIdNumber) && $perpetratorIdNumber !== 'Not identified') {
            $reportsByID = $reportCollection->find(['perpetratorSchoolId' => $perpetratorIdNumber])->toArray();
            foreach ($reportsByID as $report) {
                $allReports[(string)$report->_id] = $report;
            }
        }
        
        // Find by name (case insensitive) - this is crucial for "Not identified" cases
        if (!empty($perpetratorName)) {
            $reportsByName = $reportCollection->find([
                'perpetratorName' => new \MongoDB\BSON\Regex('^'.preg_quote($perpetratorName).'$', 'i')
            ])->toArray();
            
            foreach ($reportsByName as $report) {
                $allReports[(string)$report->_id] = $report;
            }
            
            // Also try with name parts (more flexible matching)
            $nameParts = preg_split('/\s+/', $perpetratorName);
            if (count($nameParts) > 1) {
                foreach ($nameParts as $part) {
                    if (strlen($part) < 3) continue; // Skip short parts
                    
                    $partialReports = $reportCollection->find([
                        'perpetratorName' => new \MongoDB\BSON\Regex(preg_quote($part), 'i')
                    ])->toArray();
                    
                    foreach ($partialReports as $report) {
                        $allReports[(string)$report->_id] = $report;
                    }
                }
            }
        }
        
        // If we still have no reports and have only a plain name (like single names in your list)
        // Do a more flexible search
        if (empty($allReports) && !empty($perpetratorName) && !str_contains($perpetratorName, ' ')) {
            $looseReports = $reportCollection->find([
                'perpetratorName' => new \MongoDB\BSON\Regex($perpetratorName, 'i')
            ])->toArray();
            
            foreach ($looseReports as $report) {
                $allReports[(string)$report->_id] = $report;
            }
        }
        
        // Format the reports for display
        $formattedReports = [];
        $reporterCache = [];
        
        foreach ($allReports as $report) {
            $reporterId = isset($report->reportedBy) ? (string)$report->reportedBy : null;
            $reporterName = 'Unknown';
            
            if ($reporterId) {
                if (isset($reporterCache[$reporterId])) {
                    $reporterName = $reporterCache[$reporterId];
                } else {
                    try {
                        $reporter = $userCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($reporterId)]);
                        if ($reporter) {
                            $reporterName = $reporter->fullname ?? 'Unknown';
                            $reporterCache[$reporterId] = $reporterName;
                        } else {
                            $reporterName = $report->reporterFullName ?? 'Unknown';
                        }
                    } catch (\Exception $e) {
                        $reporterName = $report->reporterFullName ?? 'Unknown';
                    }
                }
            } else {
                $reporterName = $report->reporterFullName ?? 'Unknown';
            }
            
            // Handle date formats
            $reportDate = null;
            if (isset($report->reportDate)) {
                if ($report->reportDate instanceof \MongoDB\BSON\UTCDateTime) {
                    $reportDate = $report->reportDate->toDateTime();
                } else {
                    try {
                        $reportDate = new \DateTime($report->reportDate);
                    } catch (\Exception $e) {
                        $reportDate = new \DateTime();
                    }
                }
            } else {
                $reportDate = new \DateTime();
            }
            
            $formattedReports[] = [
                '_id' => (string)$report->_id,
                'reportDate' => $reportDate->format('Y-m-d H:i:s'),
                'reporterFullName' => $reporterName,
                'status' => $report->status ?? 'For Review',
                'perpetratorName' => $report->perpetratorName ?? $perpetratorName,
                'perpetratorRole' => $report->perpetratorRole ?? $perpetratorRole,
                'perpetratorSchoolId' => $report->perpetratorSchoolId ?? $perpetratorIdNumber,
                'description' => $report->description ?? '',
            ];
        }
        
        $perpetratorDetails = [
            'name' => $perpetratorName,
            'idNumber' => $perpetratorIdNumber,
            'role' => $perpetratorRole,
            'count' => count($formattedReports)
        ];
        
        return view('admin.list.view-perpertrators', compact(
            'firstName',
            'lastName',
            'email',
            'perpetratorDetails',
            'formattedReports'
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
            $displayedVictimRelationship = $report->victimRelationship;
        }

        // Reporter data
        $reporterData = [
            'fullname' => $reporter->fullname ?? '',
            'idnumber' => $reporter->idnumber ?? '',
            'type' => $reporter->type ?? '',
            'position' => $reporter->position ?? '',
            
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
            
            // Remove the status filter to get all students
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
                        // You can add a way to capture the status here if needed
                        $perpetratorStatus = $student->status ?? 'No Status';
                        break;
                    }
                }
                
                if (empty($perpetratorSchoolId) && !str_contains($perpetratorName, ',')) {
                    $perpWords = preg_split('/\s+/', $perpetratorName);
                    $studentWords = preg_split('/\s+/', $studentName);
                    
                    $commonWords = array_intersect($perpWords, $studentWords);
                    
                    if (count($commonWords) >= 2) {
                        $perpetratorSchoolId = $student->schoolId;
                        // You can add a way to capture the status here if needed
                        $perpetratorStatus = $student->status ?? 'No Status';
                        break;
                    }
                }
            }
        }
        
        $reportData['perpetratorSchoolId'] = $perpetratorSchoolId;
        $reportData['perpetratorStatus'] = $perpetratorStatus ?? 'No Status';
    
        return view('admin.list.view-report', compact(
            'firstName',
            'lastName',
            'email',
            'reportData',
            'reporterData'
        ));
    }
}
