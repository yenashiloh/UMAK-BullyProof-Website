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
                                        <div class="col-3 mb-2 mt-3"><strong>Reported Date and Time:</strong></div>
                                        <div class="col-8 mt-3">
                                            {{ \Carbon\Carbon::parse($reportData['reportDate'])->setTimezone('Asia/Manila')->format('F j, Y, g:i A') }}
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-3 mb-2"><strong>Victim's Role:</strong></div>
                                        <div class="col-8">{{ $reportData['victimType'] }}</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-3 mb-2"><strong>Victim's Name:</strong></div>
                                        <div class="col-8">{{ $reportData['victimName'] }}</div>
                                    </div>
                                    <div class="row">
                                        <div class="col-3 mb-2"><strong>Grade Year Level:</strong></div>
                                        <div class="col-8">{{ $reportData['gradeYearLevel'] }}</div>
                                    </div>
                                </div>


                                <div class="tab-pane fade" id="pills-profile" role="tabpanel"
                                    aria-labelledby="pills-incident-details">
                                    {{-- <p>Even the all-powerful Pointing has no control about the blind texts it is an almost unorthographic life One day however a small line of blind text by the name of Lorem Ipsum decided to leave for the far World of Grammar.</p>
                        <p>The Big Oxmox advised her not to do so, because there were thousands of bad Commas, wild Question Marks and devious Semikoli, but the Little Blind Text didn’t listen. She packed her seven versalia, put her initial into the belt and made herself on the way.
                        </p> --}}
                                </div>
                                <div class="tab-pane fade" id="pills-contact" role="tabpanel"
                                    aria-labelledby="pills-preperator-information">
                                    {{-- <p>Pityful a rethoric question ran over her cheek, then she continued her way. On her way she met a copy. The copy warned the Little Blind Text, that where it came from it would have been rewritten a thousand times and everything that was left from its origin would be the word "and" and the Little Blind Text should turn around and return to its own, safe country.</p>

                        <p> But nothing the copy said could convince her and so it didn’t take long until a few insidious Copy Writers ambushed her, made her drunk with Longe and Parole and dragged her into their agency, where they abused her for their</p> --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @include('partials.admin-footer')
