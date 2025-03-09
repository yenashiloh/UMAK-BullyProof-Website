<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>View Report</title>

    @include('partials.admin-link')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
</head>
<style>
    .navigation-btn {
        z-index: 1051;
        width: 50px;
        height: 50px;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 10px;
    }

    #prevImage {
        left: 10px;
    }

    #nextImage {
        right: 10px;
    }

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

    #galleryViewer.d-none {
        display: none;
    }

    #closeGallery {
        position: relative;
        z-index: 1052;
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
                        {{-- <div class="card-header">
                            <h4 class="card-title">View Report</h4>
                        </div> --}}
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
                                        role="tab" aria-controls="pills-contact" aria-selected="false">Incident Report
                                        Details</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="line-actions-tab" data-bs-toggle="pill" href="#line-actions"
                                        role="tab" aria-controls="pills-actions" aria-selected="false">Actions & Support Details</a>
                                </li>
                            </ul>

                            <!-- Victim's Information -->
                            <div class="tab-content mt-3 mb-3" id="line-tabContent">
                                <div class="tab-pane fade show active" id="line-home" role="tabpanel"
                                    aria-labelledby="line-home-tab">
                                    <div class="row">
                                        
    
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <label class="mb-2 mt-2"><strong>Full Name:</strong></label>
                                                <input type="text" class="form-control"
                                                    value="{{ $reporterData['fullname'] }}" disabled>
                                            </div>
                                           
                                            <div class="col-md-6 mb-2">
                                                <label class="mb-2 mt-2"><strong>ID Number:</strong></label>
                                                <input type="text" class="form-control"
                                                    value="{{ $reporterData['idnumber'] }}" disabled>
                                            </div>
                                           
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <label class="mb-2 mt-2">
                                                    <strong>
                                                        {{ $reporterData['type'] === 'Student' ? 'Grade/Year Level' : 'Position' }}
                                                    </strong>
                                                </label>
                                                <input type="text" class="form-control" value="{{ $reporterData['position'] }}" disabled>
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <label class="mb-2 mt-2"><strong>Role at the University:</strong></label>
                                                <input type="text" class="form-control"
                                                    value="{{ $reporterData['type'] }}" disabled>
                                            </div>
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
                                            <label class="mb-2 mt-2"><strong>ID Number:</strong></label>
                                            <input type="text" class="form-control"
                                                value="{{ ucfirst($reportData['perpetratorSchoolId']) }}" disabled>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="mb-2 mt-2"><strong>Role at the University:</strong></label>
                                            <input type="text" class="form-control"
                                                value="{{ $reportData['perpetratorRole'] }}" disabled>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="mb-2 mt-2">
                                                <strong>
                                                    @if($reportData['perpetratorRole'] == 'Student')
                                                        Year Level:
                                                    @elseif($reportData['perpetratorRole'] == 'Employee')
                                                        Position:
                                                    @else
                                                        Grade/Year Level or Position:
                                                    @endif
                                                </strong>
                                            </label>
                                            <input type="text" class="form-control"
                                                value="{{ $reportData['perpetratorGradeYearLevel'] }}" disabled>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="mb-2 mt-2"><strong>Report As the Complainant:</strong></label>
                                            <input type="text" class="form-control"
                                                value="{{ $reportData['submitAs'] }}" disabled>
                                        </div>
                                        
                                        @if($reportData['submitAs'] == "No, I am submitting as a witness, friend, or other third party.")
                                            <div class="col-md-6 mb-2">
                                                <label class="mb-2 mt-2"><strong>Victim Relationship:</strong></label>
                                                <input type="text" class="form-control"
                                                    value="{{ $reportData['victimRelationship'] }}" disabled>
                                            </div>
                                        @endif                           
                                    </div>
                                </div>

                                <!-- Incident Report Details -->
                                <div class="tab-pane fade" id="line-contact" role="tabpanel"
                                    aria-labelledby="line-contact-tab">
                                    <div class="row">
                                        <!-- Reported Date and Time -->
                                        <div class="col-md-6 mb-2">
                                            <label class="mb-2 mt-2"><strong>Reported Date and Time:</strong></label>
                                            <input type="text" class="form-control"
                                                value="{{ \Carbon\Carbon::parse($reportData['reportDate'])->setTimezone('Asia/Manila')->format('F j, Y, g:i A') }}"
                                                disabled>
                                        </div>
                                    
                                        <!-- Platform where cyberbullying occurred -->
                                        <div class="col-md-6 mb-2">
                                            <label class="mb-2 mt-2"><strong>Platform where cyberbullying occurred:</strong></label>
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
                                        @endphp
                                    
                                            <input type="text" class="form-control" value="{{ $platformsString }}" disabled>
                                        </div>
                                    </div>     
                                    
                                    <div class="row">
                                        <div class="col-md-12 mb-2">
                                            <label class="mb-2 mt-2"><strong>Type of Cyberbullying was Involved:</strong></label>
                                            <input type="text" class="form-control"
                                            value="{{ str_replace(['[', ']'], '', implode(', ', $reportData['cyberbullyingTypes'])) }}" disabled>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 mb-2 mt-3">
                                            <strong>Incident Details:</strong>
                                        </div>
                                        <div class="col-12">
                                            <textarea class="form-control" rows="15" disabled>{{ $reportData['incidentDetails'] }}</textarea>
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

                                        <div
                                            class="d-flex justify-content-center align-items-center h-100 position-relative">
                                            <button
                                                class="btn btn-light navigation-btn position-absolute start-0 top-50 translate-middle-y"
                                                id="prevImage" style="display: none;">&lt;</button>

                                            <div class="d-flex justify-content-center align-items-center"
                                                style="height: 90vh;">
                                                <img id="fullImage" src="" alt="Full Size Image"
                                                    style="max-height: 90vh; max-width: 80vw; object-fit: contain;">
                                            </div>

                                            <button
                                                class="btn btn-light navigation-btn position-absolute end-0 top-50 translate-middle-y"
                                                id="nextImage" style="display: none;">&gt;</button>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="result">
                                        @if (isset($reportData['analysisResult']))
                                            <p class="mb-2">
                                                <strong>Result:</strong> 
                                                <span class="@if ($reportData['analysisProbability'] > 50) text-danger @else text-success @endif">
                                                    {{ number_format($reportData['analysisProbability'], 2) }}% {{ $reportData['analysisResult'] }}
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
                                <div class="tab-pane fade" id="line-actions" role="tabpanel" aria-labelledby="line-actions-tab">
                                    <div class="row">
                                        @if (empty($reportData['describeActions']) && empty($reportData['reportedTo']))
                                            @if ($reportData['actionsTaken'] != 'N/A')
                                                <div class="col-md-6 mb-2">
                                                    <label class="mb-2 mt-2"><strong>Have actions been taken so far:</strong></label>
                                                    <input type="text" class="form-control" value="{{ $reportData['actionsTaken'] }}" disabled>
                                                </div>
                                            @endif
                                            
                                            <div class="col-md-6 mb-2">
                                                <label class="mb-2 mt-2"><strong>Have Reported the Incident to Anyone Else:</strong></label>
                                                <input type="text" class="form-control" value="{{ $reportData['hasReportedBefore'] !== 'N/A' ? $reportData['hasReportedBefore'] : '' }}" disabled>
                                            </div>
                                            
                                            @if ($reportData['actionsTaken'] == 'N/A')
                                                <div class="col-md-6 mb-2">
                                                    <label class="mb-2 mt-2"><strong>Support Types:</strong></label>
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
                                                    @endphp
                                                    
                                                    @if(empty($uniqueSupportTypes))
                                                        <input type="text" class="form-control" value="N/A" disabled>
                                                    @else
                                                        <input type="text" class="form-control" value="{{ implode(', ', $uniqueSupportTypes) }}" disabled>
                                                    @endif
                                                </div>
                                            @endif
                                        @else
                                            @if ($reportData['actionsTaken'] != 'N/A')
                                                <div class="col-md-6 mb-2">
                                                    <label class="mb-2 mt-2"><strong>Have actions been taken so far:</strong></label>
                                                    <input type="text" class="form-control" value="{{ $reportData['actionsTaken'] }}" disabled>
                                                </div>
                                            @endif
                                            
                                            @if (!empty($reportData['describeActions']) && $reportData['describeActions'] != 'N/A')
                                                <div class="col-md-{{ $reportData['actionsTaken'] != 'N/A' ? '6' : '12' }} mb-2">
                                                    <label class="mb-2 mt-2"><strong>Describe Actions:</strong></label>
                                                    <input type="text" class="form-control" value="{{ $reportData['describeActions'] }}" disabled>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="mb-2 mt-2"><strong>Have Reported the Incident to Anyone Else:</strong></label>
                                            <input type="text" class="form-control" value="{{ $reportData['hasReportedBefore'] !== 'N/A' ? $reportData['hasReportedBefore'] : '' }}" disabled>
                                        </div>
                                        
                                        @if ($reportData['departmentCollege'] != 'N/A')
                                            <div class="col-md-6 mb-2">
                                                <label class="mb-2 mt-2"><strong>Department/College Reported:</strong></label>
                                                <input type="text" class="form-control" value="{{ $reportData['departmentCollege'] }}" disabled>
                                            </div>
                                        @elseif (!empty($reportData['reportedTo']) && $reportData['reportedTo'] != 'N/A')
                                            <div class="col-md-6 mb-2">
                                                <label class="mb-2 mt-2"><strong>Reported to:</strong></label>
                                                <input type="text" class="form-control" value="{{ $reportData['reportedTo'] }}" disabled>
                                            </div>
                                        @else
                                            <div class="col-md-6 mb-2">
                                                <label class="mb-2 mt-2"><strong>Support Types:</strong></label>
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
                                                @endphp
                                                
                                                @if(empty($uniqueSupportTypes))
                                                    <input type="text" class="form-control" value="N/A" disabled>
                                                @else
                                                    <input type="text" class="form-control" value="{{ implode(', ', $uniqueSupportTypes) }}" disabled>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    
                                    @if (!empty($reportData['reportedTo']) && $reportData['reportedTo'] != 'N/A')
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="mb-2 mt-2"><strong>Support Types:</strong></label>
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
                                            @endphp
                                            
                                            @if(empty($uniqueSupportTypes))
                                                <input type="text" class="form-control" value="N/A" disabled>
                                            @else
                                                <input type="text" class="form-control" value="{{ implode(', ', $uniqueSupportTypes) }}" disabled>
                                            @endif
                                        </div>
                                        
                                        @if($reportData['submitAs'] == "No, I am submitting as a witness, friend, or other third party.")
                                            <div class="col-md-6 mb-2">
                                                <label class="mb-2 mt-2"><strong>Participate in an Investigation:</strong></label>
                                                <input type="text" class="form-control" value="{{ $reportData['witnessChoice'] }}" disabled>
                                            </div>
                                        @endif
                                    </div>
                                    @endif
                                    
                                    @if($reportData['submitAs'] == "No, I am submitting as a witness, friend, or other third party.")
                                    <div class="row">
                                        @if(!(!empty($reportData['reportedTo']) && $reportData['reportedTo'] != 'N/A'))
                                            <div class="col-md-6 mb-2">
                                                <label class="mb-2 mt-2"><strong>Willingness to Participate in Investigation:</strong></label>
                                                <input type="text" class="form-control" value="{{ $reportData['witnessChoice'] }}" disabled>
                                            </div>
                                        @endif
                                        
                                        <div class="col-md-{{ (!empty($reportData['reportedTo']) && $reportData['reportedTo'] != 'N/A') ? '12' : '6' }} mb-2">
                                            <label class="mb-2 mt-2"><strong>Contact if Investigation Starts:</strong></label>
                                            <input type="text" class="form-control" value="{{ $reportData['contactChoice'] }}" disabled>
                                        </div>
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
    <script>
        // Function to show custom toast notifications
        function showToast(message, type) {
            const toastElement = document.createElement('div');
            toastElement.classList.add('toast', 'align-items-center', 'text-white', 'bg-' + type, 'border-0');
            toastElement.setAttribute('role', 'alert');
            toastElement.setAttribute('aria-live', 'assertive');
            toastElement.setAttribute('aria-atomic', 'true');

            toastElement.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;

            const toastContainer = document.getElementById('toastContainer');
            toastContainer.appendChild(toastElement);

            const toast = new bootstrap.Toast(toastElement);
            toast.show();
        }

        $(document).ready(function() {
            $('#updateReportForm').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                // Change the button text to "Saving..." and disable it
                $('#saveButton').text('Saving...').prop('disabled', true);

                // Send form data via AJAX
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        // Show toast with the response message and type
                        showToast(response.message, response.status);

                        // Reset the button text to "Save"
                        $('#saveButton').text('Save').prop('disabled', false);
                    },
                    error: function(xhr) {
                        // Show toast with the error message
                        var errorMessage = xhr.responseJSON.message ||
                            'An error occurred while saving the report.';
                        showToast(errorMessage, 'error');

                        // Reset the button text to "Save"
                        $('#saveButton').text('Save').prop('disabled', false);
                    }
                });
            });
        });

        $(document).ready(function() {
            const inputField = $('#id_number');
            const suggestionsContainer = $('#idNumberSuggestions');

            inputField.on('input', function() {
                const searchTerm = $(this).val();

                if (searchTerm.length >= 3) { // Trigger after 3 characters
                    $.ajax({
                        url: '{{ route('search.idNumber') }}',
                        method: 'GET',
                        data: {
                            term: searchTerm
                        },
                        success: function(data) {
                            // Remove duplicates using Set
                            const uniqueData = [...new Set(data)];

                            // Clear and reposition suggestions
                            suggestionsContainer.empty().hide();

                            if (uniqueData.length > 0) {
                                // Match width of input field
                                suggestionsContainer.css({
                                    width: inputField.outerWidth() + 'px',
                                    top: inputField.outerHeight() + 'px',
                                    left: inputField.position().left + 'px',
                                    position: 'absolute'
                                });

                                // Append suggestions
                                uniqueData.forEach(function(idNumber) {
                                    suggestionsContainer.append(`
                                    <a href="#" class="list-group-item list-group-item-action">${idNumber}</a>
                                `);
                                });

                                // Show the dropdown
                                suggestionsContainer.show();

                                // Add click event to suggestions
                                suggestionsContainer.find('a').on('click', function(e) {
                                    e.preventDefault();
                                    inputField.val($(this).text());
                                    suggestionsContainer.empty().hide();
                                });
                            }
                        }
                    });
                } else {
                    suggestionsContainer.empty().hide();
                }
            });

            // Hide suggestions if the user clicks outside the input or suggestions
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#id_number, #idNumberSuggestions').length) {
                    suggestionsContainer.empty().hide();
                }
            });
        });
    </script>
