<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Add Complainee</title>

    @include('partials.admin-link')
</head>

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
                <h3 class="fw-bold mb-1">Complainee</h3>
                <ul class="breadcrumbs mb-1">
                    <li class="nav-home">
                        <a href="{{ route('admin.dashboard') }}">
                            <i class="icon-home"></i>
                        </a>
                    </li>

                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li class="nav-item">
                        <a href="#">Complainees</a>
                    </li>
                    <li class="separator">
                        <i class="icon-arrow-right"></i>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="fw-bold">Add Complainee</a>
                    </li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Add Complainee</div>
                        </div>
                        <div class="card-body">
                            <form>
                                <!-- Incident Number and ID Number in the same row -->
                                <div class="row mb-1">
                                    <div class="col-md-6 form-group">
                                        <label for="incidentNumber" class="form-label">Incident Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="incidentNumber" required>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label for="idNumber" class="form-label">ID Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="idNumber" required>
                                    </div>
                                </div>
                
                                <!-- First Name, Middle Name, and Last Name in the same row -->
                                <div class="row mb-1">
                                    <div class="col-md-4 form-group">
                                        <label for="firstName" class="form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="firstName" required>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label for="middleName" class="form-label">Middle Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="middleName" required>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label for="lastName" class="form-label">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="lastName" required>
                                    </div>
                                </div>
                
                                <!-- Department selection -->
                                <div class="row mb-1">
                                    <div class="col-md-12 form-group">
                                        <label for="department" class="form-label">Department <span class="text-danger">*</span></label>
                                        <select class="form-select form-control" id="department" required>
                                            <option value="" disabled selected>Select Department</option>
                                            <option value="College of Continuing, Advanced and Professional Studies (CCAPS)">College of Continuing, Advanced and Professional Studies (CCAPS)</option>
                                            <option value="College of Human Kinetics (CHK)">College of Human Kinetics (CHK)</option>
                                            <option value="College of Business and Financial Science (CBFS)">College of Business and Financial Science (CBFS)</option>
                                            <option value="College of Computing and Information Sciences (CCIS)">College of Computing and Information Sciences (CCIS)</option>
                                            <option value="College of Governance and Public Policy (CGPP)">College of Governance and Public Policy (CGPP)</option>
                                            <option value="College of Technology Management (CTM)">College of Technology Management (CTM)</option>
                                            <option value="College of Construction Sciences and Engineering (CCSE)">College of Construction Sciences and Engineering (CCSE)</option>
                                            <option value="College of Innovative Teacher Education (CITE)">College of Innovative Teacher Education (CITE)</option>
                                            <option value="College of Tourism and Hospitality Management">College of Tourism and Hospitality Management</option>
                                            <option value="College of Liberal Arts & Sciences (CLAS)">College of Liberal Arts & Sciences (CLAS)</option>
                                            <option value="School of Law (SOL)">School of Law (SOL)</option>
                                            <option value="Institute of Accountancy (IOA)">Institute of Accountancy (IOA)</option>
                                            <option value="Institute of Nursing (ION)">Institute of Nursing (ION)</option>
                                            <option value="Institute of Pharmacy (IOP)">Institute of Pharmacy (IOP)</option>
                                            <option value="Institute of Imaging Health Science (IIHS)">Institute of Imaging Health Science (IIHS)</option>
                                            <option value="Institute of Psychology (IOPsy)">Institute of Psychology (IOPsy)</option>
                                            <option value="College of Innovative Teacher Education – Higher School ng UMak (HSU)">College of Innovative Teacher Education – Higher School ng UMak (HSU)</option>
                                        </select>
                                    </div>
                                </div>
                
                                <!-- Year Level selection -->
                                <div class="row mb-1">
                                    <div class="col-md-6 form-group">
                                        <label for="yearLevel" class="form-label">Grade/Year Level <span class="text-danger">*</span></label>
                                        <select class="form-select form-control" id="yearLevel" required>
                                            <option value="" disabled selected>Grade/Year Level</option>
                                            <option value="Grade 11">Grade 11</option>
                                            <option value="Grade 12">Grade 12</option>
                                            <option value="1st Year College">1st Year College</option>
                                            <option value="2nd Year College">2nd Year College</option>
                                            <option value="3rd Year College">3rd Year College</option>
                                            <option value="4th Year College">4th Year College</option>
                                        </select>
                                    </div>
                
                                    <!-- Section input -->
                                    <div class="col-md-6 form-group">
                                        <label for="section" class="form-label">Section <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="section" required>
                                    </div>
                             
                
                                <!-- Remarks textarea -->
                                <div class="col-md-12 form-group">
                                    <label for="additionalInfo" class="form-label">Remarks <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="additionalInfo" rows="5"></textarea>
                                </div>
                
                                <!-- Save button -->
                                <div class="text-start">
                                    <button type="submit" class="btn btn-secondary">Submit</button>
                                  
                                </div>
                            </form>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Custom template -->
    </div>

    @include('partials.admin-footer')
