<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>View Report</title>

    @include('partials.admin-link')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
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
        <img id="loading-logo" src="{{ asset('assets/img/logo-4.png') }}" alt="Loading Logo">
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
                <h3 class="fw-bold mb-3">Details of Report</h3>
                <ul class="breadcrumbs mb-3">
                    <li class="nav-home">
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="icon-home"></i>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="{{ route('admin.list.list-perpetrators') }}">Complainees</a>
                    </li>
                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.reports.byIdNumber', ['idNumber' => $reportData['idNumber']]) }}">View
                            All Reports</a>
                    </li>

                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li class="nav-item fw-bold">
                        <a href="">Details of Report</a>
                    </li>
                </ul>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <ul class="nav nav-tabs nav-line nav-color-secondary" id="line-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="line-home-tab" data-bs-toggle="pill"
                                        href="#line-home" role="tab" aria-controls="pills-home"
                                        aria-selected="true">Complainant's Information</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="line-profile-tab" data-bs-toggle="pill" href="#line-profile"
                                        role="tab" aria-controls="pills-profile" aria-selected="false">Complainee's
                                        Information</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="line-contact-tab" data-bs-toggle="pill" href="#line-contact"
                                        role="tab" aria-controls="pills-contact" aria-selected="false">Incident
                                        Details</a>
                                </li>
                            </ul>

                            <!-- Victim's Information -->
                            <div class="tab-content mt-3 mb-3" id="line-tabContent">
                                <div class="tab-pane fade show active" id="line-home" role="tabpanel"
                                    aria-labelledby="line-home-tab">

                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="mb-2 mt-2"><strong>Reported Date and Time:</strong></label>
                                            <input type="text" class="form-control"
                                                value="{{ \Carbon\Carbon::parse($reportData['reportDate'])->setTimezone('Asia/Manila')->format('F j, Y, g:i A') }}"
                                                disabled>
                                        </div>
                                        
                                        <div class="col-md-6 mb-2">
                                            <label class="mb-2 mt-2"><strong>Relationship to Victim:</strong></label>
                                            <input type="text" class="form-control" value="{{ $reportData['victimRelationship'] }}" disabled>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                        
                                        <div class="col-md-6 mb-2">
                                            <label class="mb-2 mt-2"><strong>Full Name:</strong></label>
                                            <input type="text" class="form-control"
                                                value="{{ $reportData['victimName'] }}" disabled>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="mb-2 mt-2"><strong>Year Level or
                                                    Position:</strong></label>
                                            <input type="text" class="form-control"
                                                value="{{ $reportData['gradeYearLevel'] }}" disabled>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="mb-2 mt-2"><strong>Role in the
                                                    University:</strong></label>
                                            <input type="text" class="form-control"
                                                value="{{ $reportData['victimType'] }}" disabled>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="mb-2 mt-2"><strong>Relationship to the
                                                    Victim:</strong></label>
                                            <input type="text" class="form-control"
                                                value="{{ $reportData['victimRelationship'] }}" disabled>
                                        </div>
                                    </div>
                                </div>

                                <!-- Complainee's Information -->
                                <div class="tab-pane fade" id="line-profile" role="tabpanel"
                                    aria-labelledby="line-profile-tab">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="mb-2 mt-2"><strong>Full Name:</strong></label>
                                            <input type="text" class="form-control"
                                                value="{{ $reportData['perpetratorName'] }}" disabled>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="mb-2 mt-2"><strong>Role in the University:</strong></label>
                                            <input type="text" class="form-control"
                                                value="{{ $reportData['perpetratorRole'] }}" disabled>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="mb-2 mt-2"><strong>Grade/Year Level or
                                                    Position:</strong></label>
                                            <input type="text" class="form-control"
                                                value="{{ $reportData['perpetratorGradeYearLevel'] }}" disabled>
                                        </div>
                                        <div class="col-md-6 mb-2 position-relative">
                                            <label class="mb-2 mt-2"><strong>ID Number</strong></label>
                                            <div class="input-group">
                                                <input type="text" name="id_number" id="id_number"
                                                    class="form-control" value="{{ $reportData['idNumber'] }}"
                                                    autocomplete="off" disabled>

                                            </div>
                                        </div>
                                    </div>

                                    <label class="mb-2 mt-2"><strong>Remarks</strong></label>
                                    <div class="input-group">
                                        <textarea name="remarks" class="form-control" rows="4" disabled>{{ $reportData['remarks'] }}</textarea>
                                    </div>

                                    <div id="toastContainer" class="position-fixed bottom-0 end-0 p-3"
                                        style="z-index: 1050;"></div>

                                </div>

                                <!-- Incident Details -->
                                <div class="tab-pane fade" id="line-contact" role="tabpanel"
                                    aria-labelledby="line-contact-tab">
                                    <div class="row">
                                        @if (empty($reportData['describeActions']) && empty($reportData['reportedTo']))
                                            <div class="col-md-6 mb-2">
                                                <label class="mb-2 mt-2"><strong>Have actions been taken so
                                                        far:</strong></label>
                                                <input type="text" class="form-control"
                                                    value="{{ $reportData['actionsTaken'] }}" disabled>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <label class="mb-2 mt-2"><strong>Have Reported the Incident to Anyone
                                                        Else:</strong></label>
                                                <input type="text" class="form-control"
                                                    value="{{ $reportData['hasReportedBefore'] }}" disabled>
                                            </div>
                                        @else
                                            <div class="col-md-6 mb-2">
                                                <label class="mb-2 mt-2"><strong>Have actions been taken so
                                                        far:</strong></label>
                                                <input type="text" class="form-control"
                                                    value="{{ $reportData['actionsTaken'] }}" disabled>
                                            </div>
                                            @if (!empty($reportData['describeActions']))
                                                <div class="col-md-6 mb-2">
                                                    <label class="mb-2 mt-2"><strong>Describe Actions:</strong></label>
                                                    <input type="text" class="form-control"
                                                        value="{{ $reportData['describeActions'] }}" disabled>
                                                </div>
                                            @endif

                                            <div class="col-md-6 mb-2">
                                                <label class="mb-2 mt-2"><strong>Have Reported the Incident to Anyone
                                                        Else:</strong></label>
                                                <input type="text" class="form-control"
                                                    value="{{ $reportData['hasReportedBefore'] }}" disabled>
                                            </div>
                                            @if (!empty($reportData['reportedTo']))
                                                <div class="col-md-6 mb-2">
                                                    <label class="mb-2 mt-2"><strong>Reported to:</strong></label>
                                                    <input type="text" class="form-control"
                                                        value="{{ $reportData['reportedTo'] }}" disabled>
                                                </div>
                                            @endif
                                        @endif
                                    </div>


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
                                            <textarea class="form-control" rows="10" disabled>{{ $reportData['incidentDetails'] }}</textarea>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 mb-2 mt-3">
                                            <strong>Support Types:</strong>
                                        </div>
                                        <div class="col-12">
                                            @foreach ($reportData['supportTypes'] as $supportType)
                                                <div class="mb-2">
                                                    <input type="text" class="form-control" value="{{ $supportType }}" disabled>
                                                </div>
                                            @endforeach
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

                                        <div class="d-flex justify-content-center align-items-center h-100">
                                            <button class="btn btn-light navigation-btn me-3" id="prevImage"
                                                style="display: none;">&lt;</button>

                                            <div class="d-flex justify-content-center align-items-center"
                                                style="height: 90vh;">
                                                <img id="fullImage" src="" alt="Full Size Image"
                                                    style="max-height: 90vh; max-width: 80vw; object-fit: contain;">
                                            </div>

                                            <button class="btn btn-light navigation-btn ms-3" id="nextImage"
                                                style="display: none;">&gt;</button>
                                        </div>
                                    </div>


                                    <hr>
                                    <div class="result">
                                        @if (isset($reportData['analysisResult']))
                                            <h5 class="text-lg font-semibold mb-2 fw-bold">Cyberbullying Analysis</h5>
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
    <script>
        window.galleryImages = @json($reportData['incidentEvidence'] ?? []);
    </script>
    <script src="../../../../assets/js/report.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
