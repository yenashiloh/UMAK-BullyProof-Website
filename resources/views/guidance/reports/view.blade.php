<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Profile</title>

    @include('partials.guidance-link')
</head>

<body>

    <div id="loading-overlay">
        <div class="spinner"></div>
    </div>

    @include('partials.guidance-sidebar')
    @include('partials.guidance-header')
    <!-- Toastr CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

    <!-- jQuery (required for Toastr) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <div class="container">
        <div class="page-inner">
            <div class="page-header">
                <h3 class="fw-bold mb-3">Incident Reports</h3>
                <ul class="breadcrumbs mb-3">
                    <li class="nav-home">
                        <a href="{{ route('guidance.dashboard') }}">
                            <i class="icon-home"></i>
                        </a>
                    </li>

                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('guidance.reports.incident-reports') }}">Incident Reports</a>
                    </li>
                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li class="nav-item">
                        <a href="">View Report</a>
                    </li>
                </ul>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">View Report</h4>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-pills nav-secondary" id="pills-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="pills-victim-information" data-bs-toggle="pill"
                                        href="#pills-home" role="tab" aria-controls="pills-home"
                                        aria-selected="true">Victim's Information</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="pills-incident-details" data-bs-toggle="pill"
                                        href="#pills-profile" role="tab" aria-controls="pills-profile"
                                        aria-selected="false">Incident Details</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="pills-preperator-information" data-bs-toggle="pill"
                                        href="#pills-contact" role="tab" aria-controls="pills-contact"
                                        aria-selected="false">Perpetrator's Information</a>
                                </li>
                            </ul>
                            <div class="tab-content mt-2 mb-3" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="pills-home" role="tabpanel"
                                    aria-labelledby="pills-home-tab">
                                    <div class="row">
                                        <div class="col-4 mb-2 mt-3"><strong>Reported Date and Time:</strong></div>
                                        <div class="col-8 mt-3">
                                            {{ \Carbon\Carbon::parse($reportData['reportDate'])->setTimezone('Asia/Manila')->format('F j, Y, g:i A') }}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4 mb-2"><strong>Victim's Name:</strong></div>
                                        <div class="col-8">{{ $reportData['victimName'] }}</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4 mb-2"><strong>Victim’s Year Level or Position: </strong></div>
                                        <div class="col-8">{{ $reportData['gradeYearLevel'] }}</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4 mb-2"><strong>Victim's Role:</strong></div>
                                        <div class="col-8">{{ $reportData['victimType'] }}</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4 mb-2"><strong>Relationship to the Victim:</strong></div>
                                        <div class="col-8">{{ $reportData['victimRelationship'] }}</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4 mb-2"><strong>Have the incident to anyone else: </strong>
                                        </div>
                                        <div class="col-8">{{ $reportData['hasReportedBefore'] }}</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4 mb-2"><strong>Reported to: </strong></div>
                                        <div class="col-8">{{ $reportData['reportedTo'] }}</div>
                                    </div>
                                </div>


                                <div class="tab-pane fade" id="pills-profile" role="tabpanel"
                                    aria-labelledby="pills-incident-details">
                                    <div class="row">
                                        <div class="col-12 mb-2 mt-3">
                                            <strong>Platform where cyberbullying occurred:</strong>
                                        </div>
                                        <div class="col-12">
                                            @foreach ($reportData['platformUsed'] as $platform)
                                                <div>
                                                    <ul>
                                                        <li>{{ $platform }}
                                                        </li>
                                                    </ul>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 mb-2 mt-3">
                                            <strong>Type of cyberbullying was involved:</strong>
                                        </div>
                                        <div class="col-12">
                                            @foreach ($reportData['cyberbullyingType'] as $type)
                                                <div>
                                                    <ul>
                                                        <li>{{ $type }}
                                                        </li>
                                                    </ul>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 mb-2 mt-3">
                                            <strong>Incident Details:</strong>
                                        </div>
                                        <div class="col-12">
                                            {!! nl2br(e($reportData['incidentDetails'])) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="pills-contact" role="tabpanel"
                                aria-labelledby="pills-preperator-information">
                               <div class="row">
                                   <div class="col-4 mb-2 mt-3"><strong>Perpetrator’s Fullname:</strong></div>
                                   <div class="col-8 mt-3">{{ $reportData['perpetratorName'] }}</div>
                               </div>
                               <div class="row">
                                   <div class="col-4 mb-2"><strong>Perpetrator’s Role in the University:</strong></div>
                                   <div class="col-8">{{ $reportData['perpetratorRole'] }}</div>
                               </div>
                               <div class="row">
                                   <div class="col-4 mb-2"><strong>Perpetrator’s Grade/Year Level or Position:</strong></div>
                                   <div class="col-8">{{ $reportData['perpetratorGradeYearLevel'] }}</div>
                               </div>
                               <div class="row">
                                   <div class="col-4 mb-2"><strong>Have actions been taken so far: </strong></div>
                                   <div class="col-8">{{ $reportData['actionsTaken'] }}</div>
                               </div>
                               <div class="row">
                                   <div class="col-4 mb-2"><strong>Describe Actions: </strong></div>
                                   <div class="col-8">{{ $reportData['describeActions'] }}</div>
                               </div>
                           </div>
                           
                        </div>
                    </div>
                </div>
            </div>

            @include('partials.admin-footer')
