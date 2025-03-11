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
    
        $status = request('status', 'all');
    
        $pipeline = [
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
        ];
    
        if ($status !== 'all') {
            array_unshift($pipeline, [
                '$match' => ['status' => $status]
            ]);
        }
    
        $reports = $reportCollection->aggregate($pipeline)->toArray();
    
        if (request()->ajax()) {
            return response()->json(['data' => $reports]);
        }
    
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

    //view report incident
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

        return view('admin.reports.view', compact(
            'firstName',
            'lastName',
            'email',
            'reportData',
            'reporterData'
        ));
    }
    
    private function hasAnyNamePartMatch($nameString, $targetString) {
        $nameParts = preg_split('/\s+/', $nameString);
        foreach ($nameParts as $part) {
            if (strlen($part) > 1 && strpos($targetString, $part) !== false) {
                return true;
            }
        }
        return false;
    }
    
    //update the remarks and id number of complainee
    public function updateReport(Request $request)
    {
        $validated = $request->validate([
            'id_number' => 'required|string',
            'remarks' => 'nullable|string',
            'report_id' => 'required|string',
        ]);
    
        try {
            $reportId = $request->input('report_id');
            $idNumber = $request->input('id_number');
            $remarks = $request->input('remarks', ''); 
    
            $client = new Client(env('MONGODB_URI'));
            $reportCollection = $client->bullyproof->reports;
    
            $updateData = ['idNumber' => $idNumber];
    
            if (!empty($remarks)) {
                $updateData['remarks'] = $remarks;
            }
    
            $updateResult = $reportCollection->updateOne(
                ['_id' => new \MongoDB\BSON\ObjectId($reportId)],
                ['$set' => $updateData]
            );
    
            if ($updateResult->getModifiedCount() > 0) {
                return response()->json(['message' => 'Saved Successfully!', 'status' => 'success'], 200);
            } else {
                return response()->json(['message' => 'No changes were made to the ID Number or remarks.', 'status' => 'error'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to save ID Number and remarks: ' . $e->getMessage(), 'status' => 'error'], 500);
        }
    }
    
    //search id number
    public function searchIdNumber(Request $request)
    {
        $searchTerm = $request->input('term', '');

        $client = new Client(env('MONGODB_URI'));
        $reportCollection = $client->bullyproof->reports;

        $reports = $reportCollection->find([
            'idNumber' => new \MongoDB\BSON\Regex('^' . preg_quote($searchTerm), 'i')
        ]);

        $idNumbers = [];
        foreach ($reports as $report) {
            $idNumbers[] = $report['idNumber'];
        }

        return response()->json($idNumbers);
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
            'otherVictimRelationship' => $otherVictimRelationship, 
            'victimName' => $report->victimName,
            'victimType' => $report->victimType, 
            'gradeYearLevel' => $report->gradeYearLevel,
            'reporterFullName' => $reporter->fullname,
            'reporterEmail' => $reporter->email,
            'hasReportedBefore' => $report->hasReportedBefore ?? 'N/A', 
            'departmentCollege' => $report->departmentCollege, 
            'reportedTo' => $report->reportedTo ?? 'N/A', 
            'platformUsed' => $report->platformUsed instanceof \MongoDB\Model\BSONArray ? $report->platformUsed->getArrayCopy() : [],
            'otherPlatformUsed' => $report->otherPlatformUsed, 
            'hasWitness' => $hasWitness, 
            'witnessInfo' => $witnessInfo, 
            'incidentDetails' => $report->incidentDetails ?? 'N/A',
            'perpetratorName' => $report->perpetratorName,
            'perpetratorRole' => $report->perpetratorRole,
            'perpetratorGradeYearLevel' => $report->perpetratorGradeYearLevel,
            'supportTypes' => $supportTypes,
            'otherSupportTypes' => $otherSupportTypes, 
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
    public function changeStatus($id, Request $request)
    {
        $client = new Client(env('MONGODB_URI'));
        $reportCollection = $client->bullyproof->reports;
        $notificationCollection = $client->bullyproof->notifications;
    
        $report = $reportCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
    
        if (!$report) {
            return redirect()->back()->with('error', 'Report not found.');
        }
    
        $newStatus = $request->input('status');
        $validStatuses = [
            'For Review', 'Under Investigation', 'Resolved', 
            'Dismissed', 'Under Mediation', 'Reopened', 
            'Awaiting Response', 'Withdrawn'
        ];
    
        if (!in_array($newStatus, $validStatuses)) {
            return redirect()->back()->with('error', 'Invalid status update.');
        }
    
        $statusMessages = [
            'Under Investigation' => 'Your report is now under investigation.',
            'Resolved' => 'Your report has been resolved.',
            'Dismissed' => 'Your report has been dismissed.',
            'Under Mediation' => 'Your report is under mediation.',
            'Reopened' => 'Your report has been reopened.',
            'Awaiting Response' => 'Awaiting response for your report.',
            'Withdrawn' => 'Your report has been withdrawn.'
        ];
    
        $notificationMessage = $statusMessages[$newStatus] ?? 'Your report status has been updated.';
    
        $reportCollection->updateOne(
            ['_id' => new \MongoDB\BSON\ObjectId($id)],
            ['$set' => ['status' => $newStatus]]
        );
    
        $notification = [
            'userId' => new \MongoDB\BSON\ObjectId($report->reportedBy),
            'reportId' => new \MongoDB\BSON\ObjectId($id),
            'type' => 'status_update',
            'message' => $notificationMessage,
            'status' => 'unread',
            'createdAt' => new \MongoDB\BSON\UTCDateTime(time() * 1000),
            'readAt' => null
        ];
    
        try {
            $notificationCollection->insertOne($notification);
            return redirect()->back()->with('success', 'Status Updated Successfully!')->with('toastType', 'success');
        } catch (\Exception $e) {
            \Log::error('Failed to create notification: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Status updated but failed to create notification.')->with('toastType', 'danger');
        }
    }
    
    //direct print report
    public function directPrint($id)
    {
        $report = $this->printReport($id);
        return $report; 
    }


    //print report
    public function getPrintContent(Request $request)
    {
        $id = $request->input('id');
        
        if (!$id) {
            return response()->json(['error' => 'No report ID provided'], 400);
        }
        
        $client = new Client(env('MONGODB_URI'));
        $reportCollection = $client->bullyproof->reports;
        $userCollection = $client->bullyproof->users;
        
        $report = $reportCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($id)]);
        if (!$report) {
            return response()->json(['error' => 'Report not found'], 404);
        }
        
        $reporter = $userCollection->findOne(['_id' => $report->reportedBy]);
        if (!$reporter) {
            return response()->json(['error' => 'Reporter not found'], 404);
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

        $html = view('admin.reports.print-report', compact('reportData', 'reporterData'))->render();
    
        return response($html);
    }
}
