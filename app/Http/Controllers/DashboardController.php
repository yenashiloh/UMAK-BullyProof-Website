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
    
    
        // Cyberbullying Type Pipeline
        $cyberbullyingTypesData = [];

        // Use $reportsCollection to aggregate cyberbullying types
        $cyberbullyingTypesPipeline = [
            [
                '$match' => $dateFilter
            ],
            [
                '$unwind' => '$cyberbullyingTypes'
            ],
            [
                '$addFields' => [
                    'splitTypes' => ['$split' => ['$cyberbullyingTypes', ', ']]
                ]
            ],
            [
                '$unwind' => '$splitTypes'
            ],
            [
                '$group' => [
                    '_id' => '$splitTypes',
                    'count' => ['$sum' => 1]
                ]
            ],
            [
                '$sort' => ['count' => -1]
            ]
        ];
        
        $cyberbullyingTypesResult = $reportsCollection->aggregate($cyberbullyingTypesPipeline);
        
        $cyberbullyingTypesData = [];
        foreach ($cyberbullyingTypesResult as $type) {
            $cyberbullyingTypesData[$type->_id] = $type->count;
        }
        
        // Optional: Add logging to verify
        \Log::info('Cyberbullying Types Data:', $cyberbullyingTypesData);

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
            'withdrawnCount',
              'cyberbullyingTypesData'
          
        ));
    }
    
    // public function showSuperAdminDashboard(Request $request)
    // {
    //     $adminId = session('admin_id');
    //     if (!$adminId) {
    //         return redirect()->route('admin.login')->with('error', 'Please login to access the dashboard');
    //     }
    
    //     $client = new Client(env('MONGODB_URI'));
    //     $userCollection = $client->bullyproof->users;
    //     $adminCollection = $client->bullyproof->admins;
    //     $reportsCollection = $client->bullyproof->reports;
    
    //     $startDate = $request->input('start_date', date('Y-m-d', strtotime('first day of january this year')));
    //     $endDate = $request->input('end_date', date('Y-m-d'));
    
    //     $startDateTime = new \MongoDB\BSON\UTCDateTime(strtotime($startDate) * 1000);
    //     $endDateTime = new \MongoDB\BSON\UTCDateTime(strtotime($endDate . ' 23:59:59') * 1000);
    
    //     $dateFilter = [
    //         'reportDate' => [
    //             '$gte' => $startDateTime,
    //             '$lte' => $endDateTime
    //         ]
    //     ];
    
    //     $totalUsers = $userCollection->countDocuments();
    //     $totalReports = $reportsCollection->countDocuments($dateFilter);
    //     $toReviewCount = $reportsCollection->countDocuments(['status' => 'For Review'] + $dateFilter);
    //     $underInvestigationCount = $reportsCollection->countDocuments(['status' => 'Under Investigation'] + $dateFilter);
    //     $resolvedCount = $reportsCollection->countDocuments(['status' => 'Resolved'] + $dateFilter);
    
    //     // Cyberbullying Type Pipeline
    //     $pipeline = [
    //         [
    //             '$match' => $dateFilter
    //         ],
    //         [
    //             '$unwind' => '$cyberbullyingType'
    //         ],
    //         [
    //             '$group' => [
    //                 '_id' => '$cyberbullyingType',
    //                 'count' => ['$sum' => 1]
    //             ]
    //         ]
    //     ];
    
    //     $cyberbullyingCounts = $reportsCollection->aggregate($pipeline);
    //     $cyberbullyingData = [];
    //     foreach ($cyberbullyingCounts as $typeCount) {
    //         $cyberbullyingData[(string)$typeCount->_id] = $typeCount->count;
    //     }
    
    //     // Monthly Reports Pipeline
    //     $monthPipeline = [
    //         [
    //             '$match' => $dateFilter
    //         ],
    //         [
    //             '$group' => [
    //                 '_id' => [
    //                     '$dateToString' => [
    //                         'format' => '%Y-%m',
    //                         'date' => '$reportDate'
    //                     ]
    //                 ],
    //                 'count' => ['$sum' => 1]
    //             ]
    //         ],
    //         [
    //             '$sort' => ['_id' => 1]
    //         ]
    //     ];
    
    //     $reportMonthCounts = $reportsCollection->aggregate($monthPipeline);
    
    //     $reportMonthData = [];
    //     $reportCounts = [];
        
    //     $period = new \DatePeriod(
    //         new \DateTime($startDate),
    //         new \DateInterval('P1M'),
    //         new \DateTime($endDate)
    //     );
    
    //     foreach ($period as $date) {
    //         $monthKey = $date->format('Y-m');
    //         $reportMonthData[] = $date->format('F Y');
    //         $reportCounts[$monthKey] = 0;
    //     }
    
    //     foreach ($reportMonthCounts as $report) {
    //         $monthKey = $report->_id;
    //         if (isset($reportCounts[$monthKey])) {
    //             $reportCounts[$monthKey] = $report->count;
    //         }
    //     }
    
    //     // Platform Pipeline
    //     $platformPipeline = [
    //         [
    //             '$match' => $dateFilter
    //         ],
    //         [
    //             '$unwind' => '$platformUsed'
    //         ],
    //         [
    //             '$group' => [
    //                 '_id' => '$platformUsed',
    //                 'count' => ['$sum' => 1]
    //             ]
    //         ],
    //         [
    //             '$sort' => ['count' => -1]
    //         ]
    //     ];
    
    //     $platformCounts = $reportsCollection->aggregate($platformPipeline);
    //     $platformLabels = [];
    //     $platformData = [];
    //     foreach ($platformCounts as $platform) {
    //         $platformLabels[] = $platform->_id;
    //         $platformData[] = $platform->count;
    //     }
    
    
    //     $admin = $adminCollection->findOne(['_id' => new \MongoDB\BSON\ObjectId($adminId)]);
    //     if (!$admin) {
    //         return redirect()->route('admin.login')->with('error', 'Admin not found');
    //     }
    
    //     $firstName = $admin->first_name ?? '';
    //     $lastName = $admin->last_name ?? '';
    //     $email = $admin->email ?? '';
    
    //     return view('admin.dashboard', compact(
    //         'totalUsers',
    //         'totalReports',
    //         'toReviewCount',
    //         'underInvestigationCount',
    //         'resolvedCount',
    //         'firstName',
    //         'lastName',
    //         'email',
    //         'cyberbullyingData',
    //         'reportMonthData',
    //         'reportCounts',
    //         'platformData',
    //         'platformLabels',
    //         'startDate',
    //         'endDate',
          
    //     ));
    // }

    public function showUploadedFiles()
    {
        // authentication check
        if (!auth()->check()) {
            return redirect()->route('login');
        }
    
        $userId = auth()->id();
        $user = auth()->user();
    
        // role check
        if (!in_array($user->role, ['faculty', 'faculty-coordinator'])) {
            return redirect()->route('login');
        }
    
        $notifications = Notification::where('user_login_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();
        $notificationCount = $notifications->count();
        $firstName = $user->first_name;
        $surname = $user->surname;
        $folders = FolderName::all();
        
        // upload schedule checking
        $currentDateTime = Carbon::now('Asia/Manila');
        $uploadSchedule = UploadSchedule::first();
    
        $isUploadOpen = false;
        $statusMessage = '';
        $remainingTime = null;
        $formattedStartDate = null;
        $formattedEndDate = null;
    
        if ($uploadSchedule) {
            $startDateTime = Carbon::parse($uploadSchedule->start_date . ' ' . $uploadSchedule->start_time, 'Asia/Manila');
            $endDateTime = Carbon::parse($uploadSchedule->end_date . ' ' . $uploadSchedule->stop_time, 'Asia/Manila');
    
            $formattedStartDate = $startDateTime->format('l, j F Y, g:i A');
            $formattedEndDate = $endDateTime->format('l, j F Y, g:i A');
    
            if ($currentDateTime->between($startDateTime, $endDateTime)) {
                $isUploadOpen = true;
                $remainingTime = $currentDateTime->diffForHumans($endDateTime, [
                    'parts' => 2,
                    'short' => true,
                    'syntax' => Carbon::DIFF_ABSOLUTE
                ]);
                $statusMessage = "Upload is open. Closes in {$remainingTime}.";
            } elseif ($currentDateTime->lt($startDateTime)) {
                $isUploadOpen = false;
                $remainingTime = $currentDateTime->diffForHumans($startDateTime, [
                    'parts' => 2,
                    'short' => true,
                    'syntax' => Carbon::DIFF_ABSOLUTE
                ]);
                $statusMessage = "Upload opens in {$remainingTime}.";
            } elseif ($currentDateTime->gt($endDateTime)) {
                $isUploadOpen = false;
                $statusMessage = "The upload period is already closed.";
            } else {
                $isUploadOpen = false;
                $statusMessage = "Upload is closed.";
            }
        } else {
            $statusMessage = "No upload schedule set.";
        }
    
        // get and log course schedules from API
        $courseSchedules = collect($this->flssApiService->getCourseSchedules());
    
        // filter schedules for current faculty and convert to proper format
        $courseSchedules = $courseSchedules
            ->filter(function ($schedule) use ($user) {
                Log::info('Comparing IDs:', [
                    'faculty_id' => $user->faculty_id,
                    'user_login_id' => $schedule['user_login_id']
                ]);
                return (string)$schedule['user_login_id'] === (string)$user->faculty_id;
            })
            ->map(function ($schedule) {
                return (object) [
                    'course_schedule_id' => $schedule['course_schedule_id'],
                    'course_code' => $schedule['course_code'],
                    'course_subjects' => $schedule['course_subjects'],
                    'year_section' => $schedule['year_section'],
                    'program' => $schedule['program'],
                    'schedule' => $schedule['schedule']
                ];
            });
    
        // get and process course files
        $courseFiles = collect($this->flssApiService->getCourseFiles())
            ->filter(function ($file) use ($user) {
                return (string)$file['user_login_id'] === (string)$user->faculty_id;
            });
    
        // extract unique semesters and school years from API response
        $semesters = $courseFiles->pluck('semester')
            ->unique()
            ->filter()
            ->sort()
            ->values();
    
        $schoolYears = $courseFiles->pluck('school_year')
            ->unique()
            ->filter()
            ->sort()
            ->values();
    
        // get files for the authenticated user
        $files = CoursesFile::with('courseSchedule')
        ->join('folder_name', 'courses_files.folder_name_id', '=', 'folder_name.folder_name_id')
        ->where('courses_files.user_login_id', $userId)
        ->where('courses_files.is_archived', 0)
        ->where('folder_name.main_folder_name', 'Classroom Management')
        ->orderBy('courses_files.created_at', 'desc')
        ->select('courses_files.*') 
        ->get();
    
    
        // Process files with subjects
       $filesWithSubjects = $files->map(function ($file, $index) {
        $courseSchedule = $file->courseSchedule;
        $fileObject = new \stdClass();
        $fileObject->index = $index + 1;
        $fileObject->courses_files_id = $file->courses_files_id;
        $fileObject->semester = $file->semester;
        $fileObject->school_year = $file->school_year;
        $fileObject->subject = $file->subject;
        $fileObject->folder_name = $file->folderName ? $file->folderName->folder_name : 'N/A'; 
        $fileObject->created_at = $file->created_at;
        $fileObject->program = $courseSchedule ? $courseSchedule->program : 'N/A';
        $fileObject->subject_name = $courseSchedule ? $courseSchedule->course_subjects : 'N/A';
        $fileObject->code = $courseSchedule ? $courseSchedule->course_code : 'N/A';
        $fileObject->year = $courseSchedule ? $courseSchedule->year_section : 'N/A';
        $fileObject->schedule = $courseSchedule ? $courseSchedule->schedule : 'N/A';
        $fileObject->files = $file->files;
        $fileObject->original_file_name = $file->original_file_name;
        $fileObject->status = $file->status;
        $fileObject->declined_reason = $file->declined_reason;
        return $fileObject;
    });
    
    $consolidatedFiles = $filesWithSubjects->map(function ($file) {
        $fileObject = new \stdClass();
        $fileObject->courses_files_id = $file->courses_files_id;
        $fileObject->semester = $file->semester ?? 'N/A';
        $fileObject->school_year = $file->school_year ?? 'N/A';
        $fileObject->folder_name = $file->folder_name;  
        $fileObject->program = $file->program;
        $fileObject->subject_name = $file->subject_name;
        $fileObject->year = $file->year;
        $fileObject->subject = $file->subject;
        $fileObject->course_code = $file->code;
        $fileObject->schedule = $file->schedule;
        $fileObject->files = [];
    
        $filesData = is_string($file->files) ? json_decode($file->files, true) : $file->files;
        $originalNames = is_string($file->original_file_name) ? 
            json_decode($file->original_file_name, true) : 
            $file->original_file_name;
    
        if (is_array($filesData)) {
            foreach ($filesData as $index => $fileData) {
                $fileObject->files[] = [
                    'id' => $file->courses_files_id,
                    'path' => is_array($fileData) ? $fileData['path'] : $fileData,
                    'name' => $originalNames[$index] ?? (is_array($fileData) ? $fileData['name'] : basename($fileData)),
                    'status' => $file->status,
                    'declined_reason' => $file->declined_reason,
                    'created_at' => $file->created_at,
                ];
            }
        }
    
        $fileObject->status = $file->status;
        return json_decode(json_encode($fileObject), true);
    })->values();
    
        //overall progress 
        $mainFolders = ['Classroom Management', 'Test Administration', 'Syllabus Preparation'];
        $folderProgress = [];
        
        foreach ($mainFolders as $mainFolder) {
            $subFolders = FolderName::where('main_folder_name', $mainFolder)->get();
            $mainFolderProgress = 0;
        
            foreach ($subFolders as $subFolder) {
                $totalFiles = $subFolder->coursesFiles()
                    ->where('user_login_id', $userId)
                    ->count();
        
                $approvedFiles = $subFolder->coursesFiles()
                    ->where('user_login_id', $userId)
                    ->where('status', 'Approved')
                    ->count();
        
                $subFolderProgress = ($totalFiles > 0) ? ($approvedFiles / $totalFiles) * 100 : 0;
                $mainFolderProgress += $subFolderProgress;
            }
        
            $folderProgress[$mainFolder] = ($subFolders->count() > 0) ?
                $mainFolderProgress / $subFolders->count() : 0;
        }
        
        $overallProgress = count($folderProgress) > 0 ?
            array_sum($folderProgress) / count($folderProgress) : 0;

        $folderStatus = FolderName::with(['coursesFiles' => function ($query) {
            $query->where('user_login_id', auth()->id());
        }])->get()->map(function ($folder) {
            $totalFiles = $folder->coursesFiles->count();
            $approvedFiles = $folder->coursesFiles->where('status', 'Approved')->count();
            return [
                'folder_name' => $folder->folder_name,
                'main_folder_name' => $folder->main_folder_name,
                'approved_count' => $approvedFiles,
                'total_count' => $totalFiles,
                'progress' => ($totalFiles > 0) ? ($approvedFiles / $totalFiles) * 100 : 0,
            ];
        });
    
        // Calculate department progress
        $departments = Department::all();
        $departmentProgress = [];
    
        foreach ($departments as $department) {
            $userIds = UserLogin::where('department_id', $department->department_id)->pluck('user_login_id');
    
            $totalFiles = CoursesFile::whereIn('user_login_id', $userIds)
                ->where('is_archived', 0)
                ->count();
    
            $approvedFiles = CoursesFile::whereIn('user_login_id', $userIds)
                ->where('status', 'Approved')
                ->where('is_archived', 0)
                ->count();
    
            $departmentProgress[$department->department_name] = ($totalFiles > 0) ?
                ($approvedFiles / $totalFiles) * 100 : 0;
        }
        
      $messages = Message::whereIn('courses_files_id', 
        $consolidatedFiles->pluck('courses_files_id')
        )->with('userLogin', 'folderName')
        ->orderBy('created_at', 'asc')
        ->get();

    //requirements that already upload 
   $uploadedFiles = CoursesFile::where('user_login_id', $userId)
        ->select('folder_name_id', 'semester', 'school_year')
        ->get()
        ->groupBy(function($file) {
            return $file->semester . '_' . $file->school_year;
        });

    // Get all folders with Classroom Management
    $allFolders = FolderName::where('main_folder_name', 'Classroom Management')->get();
    
    // Structure the data for JavaScript
    $uploadedFoldersByPeriod = [];
    foreach ($uploadedFiles as $period => $files) {
        list($semester, $schoolYear) = explode('_', $period);
        $uploadedFoldersByPeriod[$period] = $files->pluck('folder_name_id')->toArray();
    }

    
    $hasUploaded = $consolidatedFiles->isNotEmpty(); 
        return view('faculty.accomplishment.uploaded-files', compact(
            'folderStatus', 'folderProgress', 'overallProgress',
            'courseSchedules', 'consolidatedFiles', 'isUploadOpen',
            'statusMessage', 'remainingTime', 'formattedStartDate',
            'formattedEndDate', 'semesters', 'schoolYears', 'departmentProgress', 'notifications',
            'firstName',  'surname', 'folders', 'hasUploaded',  'messages', 'allFolders', 'uploadedFoldersByPeriod' 
        ));
    }

}
    