<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>View Report</title>

    @include('partials.admin-link')
</head>
<style>
    .gallery-image:hover {
        transform: scale(1.05);
        transition: transform 0.2s;
    }

    #galleryViewer {
        opacity: 0;
        transition: opacity 0.3s;
    }

    #galleryViewer.show {
        opacity: 1;
    }
</style>

<body>

    <div id="loading-overlay">
        <div class="spinner"></div>
    </div>

    @include('partials.admin-sidebar')
    @include('partials.admin-header')
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
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="icon-home"></i>
                        </a>
                    </li>

                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.reports.incident-reports') }}">Incident Reports</a>
                    </li>
                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li class="nav-item fw-bold">
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
                                    <a class="nav-link" id="pills-preperator-information" data-bs-toggle="pill"
                                        href="#pills-contact" role="tab" aria-controls="pills-contact"
                                        aria-selected="false">Respondent's Information</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="pills-incident-details" data-bs-toggle="pill"
                                        href="#pills-profile" role="tab" aria-controls="pills-profile"
                                        aria-selected="false">Incident Details</a>
                                </li>
                            </ul>

                            <!-- Victim's Information -->
                            <div class="tab-content mt-2 mb-3" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="pills-home" role="tabpanel"
                                    aria-labelledby="pills-home-tab">
                                    <div class="row">
                                        <div class="col-md-6 mb-2 mt-3">
                                            <label class="mb-2 mt-2"><strong>Reported Date and Time:</strong></label>
                                            <input type="text" class="form-control"
                                                value="{{ \Carbon\Carbon::parse($reportData['reportDate'])->setTimezone('Asia/Manila')->format('F j, Y, g:i A') }}"
                                                disabled>
                                        </div>
                                        <div class="col-md-6 mb-2 mt-3">
                                            <label class="mb-2 mt-2"><strong>Complainant's Name:</strong></label>
                                            <input type="text" class="form-control"
                                                value="{{ $reportData['victimName'] }}" disabled>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="mb-2 mt-2"><strong>Victim’s Year Level or
                                                    Position:</strong></label>
                                            <input type="text" class="form-control"
                                                value="{{ $reportData['gradeYearLevel'] }}" disabled>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="mb-2 mt-2"><strong>Complainant's Role:</strong></label>
                                            <input type="text" class="form-control"
                                                value="{{ $reportData['victimType'] }}" disabled>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="mb-2 mt-2"><strong>Relationship to the
                                                    Victim:</strong></label>
                                            <input type="text" class="form-control"
                                                value="{{ $reportData['victimRelationship'] }}" disabled>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="mb-2 mt-2"><strong>Have Reported the Incident to Anyone
                                                    Else:</strong></label>
                                            <input type="text" class="form-control"
                                                value="{{ $reportData['hasReportedBefore'] }}" disabled>
                                        </div>
                                    </div>

                                    @if (!empty($reportData['reportedTo']))
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <label class="mb-2 mt-2"><strong>Reported to:</strong></label>
                                                <input type="text" class="form-control"
                                                    value="{{ $reportData['reportedTo'] }}" disabled>
                                            </div>
                                        </div>
                                    @endif
                                </div>



                                <!-- Respondent's Information -->
                                <div class="tab-pane fade" id="pills-contact" role="tabpanel"
                                    aria-labelledby="pills-preperator-information">
                                    <div class="row">
                                        <div class="col-md-6 mb-2 mt-3">
                                            <label class="mb-2 mt-2"><strong>Respondents’s Fullname:</strong></label>
                                            <input type="text" class="form-control"
                                                value="{{ $reportData['perpetratorName'] }}" disabled>
                                        </div>
                                        <div class="col-md-6 mb-2 mt-3">
                                            <label class="mb-2 mt-2"><strong>Respondents’s Role in the
                                                    University:</strong></label>
                                            <input type="text" class="form-control"
                                                value="{{ $reportData['perpetratorRole'] }}" disabled>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="mb-2 mt-2"><strong>Respondents’s Grade/Year Level or
                                                    Position:</strong></label>
                                            <input type="text" class="form-control"
                                                value="{{ $reportData['perpetratorGradeYearLevel'] }}" disabled>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="mb-2 mt-2"><strong>Have actions been taken so
                                                    far:</strong></label>
                                            <input type="text" class="form-control"
                                                value="{{ $reportData['actionsTaken'] }}" disabled>
                                        </div>
                                    </div>

                                    @if (!empty($reportData['describeActions']))
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <label class="mb-2 mt-2"><strong>Describe Actions:</strong></label>
                                                <input type="text" class="form-control"
                                                    value="{{ $reportData['describeActions'] }}" disabled>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Incident Details -->
                                <div class="tab-pane fade" id="pills-profile" role="tabpanel"
                                    aria-labelledby="pills-incident-details">
                                    <div class="row">
                                        <div class="col-12 mb-2 mt-3">
                                            <strong>Platform where cyberbullying occurred:</strong>
                                        </div>
                                        <div class="col-12">
                                            @foreach ($reportData['platformUsed'] as $platform)
                                                <div class="mb-2">
                                                    <input type="text" class="form-control"
                                                        value="{{ $platform }}" disabled>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-12 mb-2 mt-3">
                                            <strong>Incident Details:</strong>
                                        </div>
                                        <div class="col-12">
                                            <textarea class="form-control" rows="12" disabled>{{ $reportData['incidentDetails'] }}</textarea>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 mb-2 mt-3">
                                            <strong>Incident Evidence:</strong>
                                        </div>
                                        <div class="col-12">
                                            @if (!empty($reportData['incidentEvidence']))
                                                <div class="d-flex flex-wrap">
                                                    @foreach ($reportData['incidentEvidence'] as $index => $base64Image)
                                                        <div class="m-2">
                                                            <img src="data:image/jpeg;base64,{{ $base64Image }}"
                                                                alt="Incident Evidence"
                                                                class="img-fluid img-thumbnail gallery-image"
                                                                style="width: 150px; height: 150px; object-fit: cover; cursor: pointer;"
                                                                data-gallery-index="{{ $index }}">
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p>No incident evidence available.</p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Full Screen Image Viewer -->
                                    <div id="galleryViewer"
                                        class="position-fixed top-0 start-0 w-100 h-100 bg-black d-none"
                                        style="z-index: 1050;">
                                        <div class="position-absolute top-0 end-0 p-3">
                                            <button type="button" class="btn btn-light" id="closeGallery">
                                                <span>&times;</span>
                                            </button>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center h-100 px-3">
                                            <button class="btn btn-light" id="prevImage">&lt;</button>

                                            <div class="text-center" style="height: 90vh;">
                                                <img id="fullImage" src="" alt="Full Size Image"
                                                    style="max-height: 100%; max-width: 90vw; object-fit: contain;">
                                            </div>

                                            <button class="btn btn-light" id="nextImage">&gt;</button>
                                        </div>
                                    </div>

                                    <hr>
                                    <div class="result">
                                        @if (isset($reportData['analysisResult']))
                                         
                                                <h3 class="text-lg font-semibold mb-2">Cyberbullying Analysis</h3>
                                                <p class="mb-2">
                                                    <span class="font-medium">Result:</span>
                                                    <span
                                                        class="@if ($reportData['analysisResult'] === 'Cyberbullying Detected') text-danger @else text-success @endif">
                                                        {{ $reportData['analysisResult'] }}
                                                    </span>
                                                </p>
                                                <p>
                                                    <span class="font-medium">Probability:</span>
                                                    <span
                                                        class="@if ($reportData['analysisProbability'] > 50) text-danger @else text-success @endif">
                                                        {{ number_format($reportData['analysisProbability'], 2) }}%
                                                    </span>
                                                </p>
                                            
                                        @else
                                            <div class="bg-yellow-50 p-4 rounded-lg shadow mb-4">
                                                <p class="text-yellow-700">Analysis result not available</p>
                                            </div>
                                        @endif

                                        @if (isset($reportData['error']))
                                            <div class="bg-red-50 p-4 rounded-lg shadow">
                                                <p class="text-red-700">Error: {{ $reportData['error'] }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @include('partials.admin-footer')

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        window.galleryImages = @json($reportData['incidentEvidence'] ?? []);
    </script>
    <script src="../../../../assets/js/report.js"></script>
