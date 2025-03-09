<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyberbullying Incident Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .form-title {
            text-align: center;
            font-weight: bold;
            margin-bottom: 20px;
            font-size: 18px !important; 
        }
        .section-title {
            font-weight: bold;
            font-style: italic;
            margin-top: 15px;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .form-field {
            margin-bottom: 10px;
            display: flex;
            align-items: baseline;
        }
        .field-label {
            font-weight: bold;
            white-space: nowrap;
            margin-right: 5px;
        }
        .field-value {
            border-bottom: 1px solid #000;
            display: inline-block;
            flex-grow: 1;
            padding: 0 5px;
        }
        .full-width {
            width: 100%;
        }
        .incident-field {
            margin-top: 5px;
            margin-bottom: 20px;
        }
        .incident-line {
            border-bottom: 1px solid #000;
            display: block;
            width: 100%;
            min-height: 1.6em;
            margin-bottom: 10px;
            padding: 0 5px;
            line-height: 1.6;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        @media print {
            body {
                padding: 0;
                margin: 0;
            }
            .no-print {
                display: none;
            }
            .section-title {
                font-size: 17px !important; 
            }
            .form-title{
                font-size: 18px !important; 
            }
        }
    </style>
    <script>
        function printDirectly() {
            window.print();
        }
    </script>
</head>
<body>
    <div class="no-print" style="text-align: right; margin-bottom: 20px;">
        <button onclick="printDirectly()">Print Report</button>
    </div>

    <div class="form-title">CYBERBULLYING INCIDENT REPORT FORM</div>
    
    <div class="form-field">
        <span class="field-label">Date Reported: </span>
        <span class="">{{ \Carbon\Carbon::parse($reportData['reportDate'])->setTimezone('Asia/Manila')->format('F j, Y, g:i A') }}</span>
    </div>

    <br>
    <div class="section-title">Complainant's Information</div>
    
    <div class="form-field">
        <span class="field-label">Name: </span>
        <span class="field-value">{{ $reporterData['fullname'] }}</span>
    </div>
    
    <div class="form-field">
        <span class="field-label">Role at the University: </span>
        <span class="field-value">{{ $reporterData['type'] }}</span>
    </div>
    
    <div class="form-field">
        <span class="field-label">
            @if($reporterData['position'] == 'Student')
            Year Level:
            @elseif($reporterData['position'] == 'Employee')
                Position:
            @else
                Grade/Year Level or Position:
            @endif
        </span>
        <span class="field-value">{{ $reporterData['position'] }}</span>
    </div>
    
    <div class="form-field">
        <span class="field-label">ID Number: </span>
        <span class="field-value">{{ $reporterData['idnumber'] }}</span>
    </div>
    
    <div class="form-field">
        <span class="field-label">Are you submitting this report as the complainant?: </span>
    </div>

    <div class="form-field">
        <span class="field-value">{{ $reportData['submitAs'] }}</span>
    </div>
    
    @if($reportData['submitAs'] == "No, I am submitting as a witness, friend, or other third party.")
    <div class="form-field">
        <span class="field-label">Relationship to the complainant: </span>
        <span class="field-value">{{ $reportData['victimRelationship'] }}</span>
    </div>
    @endif

    <br>
    <div class="section-title">Complainee's Information</div>
    
    <div class="form-field">
        <span class="field-label">Name: </span>
        <span class="field-value">{{ $reportData['perpetratorName'] }}</span>
    </div>
    
    <div class="form-field">
        <span class="field-label">Role at the University: </span>
        <span class="field-value">{{ $reportData['perpetratorRole'] }}</span>
    </div>
    
    <div class="form-field">
        <span class="field-label">Grade/Year Level or Position: </span>
        <span class="field-value">{{ $reportData['perpetratorGradeYearLevel'] }}</span>
    </div>
    
    <div class="form-field">
        <span class="field-label">ID Number: </span>
        <span class="field-value">{{ ucfirst($reportData['perpetratorSchoolId']) }}</span>
    </div>

    <br>
    <div class="section-title">Incident Report Details</div>
    
    <div class="form-field">
        <span class="field-label">Platform or Medium Used for Cyberbullying: </span>
        <span class="field-value">
            @php
                $uniquePlatforms = [];
                
                foreach ($reportData['platformUsed'] as $platform) {
                    if ($platform == 'N/A') {
                        continue;
                    }
                    
                    if (is_array($platform) || (is_string($platform) && strpos($platform, ',') !== false)) {
                        $platformItems = is_array($platform) ? $platform : explode(', ', $platform);
                        
                        foreach ($platformItems as $item) {
                            $cleanedItem = trim($item);
                            $cleanedItem = preg_replace('/[\[\]\(\)]/', '', $cleanedItem);
                            
                            if (!empty($cleanedItem) && !in_array($cleanedItem, $uniquePlatforms)) {
                                $uniquePlatforms[] = $cleanedItem;
                            }
                        }
                    } else {
                        $cleanedPlatform = $platform;
                        
                        if (str_starts_with($platform, 'Others (Please Specify)')) {
                            $cleanedPlatform = trim(str_replace('Others (Please Specify), ', '', $platform));
                        } 
                        elseif (str_starts_with($platform, 'Social Media')) {
                            $cleanedPlatform = 'Social Media';
                        }
                        
                        $cleanedPlatform = preg_replace('/[\[\]\(\),]/', '', $cleanedPlatform);
                        $cleanedPlatform = trim($cleanedPlatform);
                        
                        if (!empty($cleanedPlatform) && !in_array($cleanedPlatform, $uniquePlatforms)) {
                            $uniquePlatforms[] = $cleanedPlatform;
                        }
                    }
                }
                
                if (empty($uniquePlatforms)) {
                    $uniquePlatforms[] = 'N/A';
                }
                
                $platformsString = implode(', ', $uniquePlatforms);
                echo $platformsString;
            @endphp
        </span>
    </div>
    
    <div class="form-field">
        <span class="field-label">What type of cyberbullying was involved?: </span>
        <span class="field-value">{{ str_replace(['[', ']'], '', implode(', ', $reportData['cyberbullyingTypes'])) }}</span>
    </div>
    
    <div class="form-field">
        <span class="field-label">Describe the incident:</span>
    </div>
    
    <div class="incident-field">
        @php
            if (!empty($reportData['incidentDetails'])) {
                echo '<div>' . $reportData['incidentDetails'] . '</div>';
            } else {
                echo '<div>&nbsp;</div>';
            }
        @endphp
    </div>

    <br>
    <div class="section-title">Actions & Support Details</div>
    
    <div class="form-field">
        <span class="field-label">Have you reported the incident to the other Office/College/Department?: </span>
        <span class="field-value">{{ $reportData['hasReportedBefore'] }}</span>
    </div>
    
    @if($reportData['hasReportedBefore'] == "Yes")
    <div class="form-field">
        <span class="field-label">Please specify Office/College/Department: </span>
        <span class="field-value">{{ $reportData['departmentCollege'] }}</span>
    </div>
    
    <div class="form-field">
        <span class="field-label">Name of person from Office/College/Department you've reported this incident: </span>
    </div>

    <div class="form-field">
        <span class="field-value">{{ $reportData['reportedTo'] }}</span>
    </div>

    @endif
    
    <div class="form-field">
        <span class="field-label">Have any actions been taken to address or resolve this matter?: </span>
        <span class="field-value">{{ $reportData['actionsTaken'] }}</span>
    </div>
    
    @if($reportData['actionsTaken'] == "Yes")
    <div class="form-field">
        <span class="field-label">Please describe the actions taken: </span>
        <span class="field-value">{{ $reportData['describeActions'] }}</span>
    </div>
    @endif
    
    @if($reportData['submitAs'] == "No, I am submitting as a witness, friend, or other third party.")
    <div class="form-field">
        <span class="field-label">Would you like to participate in an investigation if one is needed?: </span>
        <span class="field-value">{{ $reportData['witnessChoice'] }}</span>
    </div>
    
    <div class="form-field">
        <span class="field-label">May we contact you if the investigation starts?: </span>
        <span class="field-value">{{ $reportData['contactChoice'] }}</span>
    </div>
    @endif
    
    <div class="form-field">
        <span class="field-label">Support Types Requested: </span>
        <span class="field-value">
            @php
                $uniqueSupportTypes = [];
                
                foreach ($reportData['supportTypes'] as $supportType) {
                    if ($supportType == 'N/A') {
                        continue;
                    }
                    
                    $cleanedSupportType = preg_replace('/[\[\]]/', '', $supportType);
                    $cleanedSupportType = trim($cleanedSupportType);
                    
                    if (!empty($cleanedSupportType) && !in_array($cleanedSupportType, $uniqueSupportTypes)) {
                        $uniqueSupportTypes[] = $cleanedSupportType;
                    }
                }
                
                echo empty($uniqueSupportTypes) ? 'N/A' : implode(', ', $uniqueSupportTypes);
            @endphp
        </span>
    </div>
    <div class="footer">
        <p>Report generated on {{ now()->setTimezone('Asia/Manila')->format('F j, Y, g:i A') }}</p>
    </div>
    
</body>
</html>