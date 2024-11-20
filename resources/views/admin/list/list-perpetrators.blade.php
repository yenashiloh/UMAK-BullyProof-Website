<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>List of Perpetrator</title>

    @include('partials.admin-link')
</head>

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
                <h3 class="fw-bold mb-3">Respondents</h3>
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
                        <a href="#">Respondents</a>
                    </li>
                </ul>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="ms-auto d-flex align-items-center">
                                <a href="{{ route('admin.list.add-complainee') }}" class="btn btn-primary btn-round">
                                    <i class="fas fa-plus"></i> Add Respondents
                                </a>
                                
                               
                                <div class="dropdown ms-2">
                                    <a href="#" class="btn btn-label-info btn-round dropdown-toggle" id="exportDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-file-export"></i> Export
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                                        <li>
                                            <a class="dropdown-item export-link no-loading" href="">
                                                <i class="fas fa-file-csv"></i> Export to CSV
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item export-link no-loading" href="">
                                                <i class="fas fa-file-excel"></i> Export to XLSX
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="basic-datatables" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Respondent's Name</th>
                                            <th>Grade/Year Level & Section</th>
                                            <th>ID Number</th>
                                            <th>Remarks</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reports as $index => $report)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $report->perpetratorName ?? 'N/A' }}</td>
                                                <td>{{ $report->perpetratorGradeYearLevel ?? 'N/A' }}</td>
                                                <td>{{ $report->idNumber ?? 'N/A' }}</td> 
                                                <td>{{ $report->offenseCounts ?? 'N/A' }}</td> 
                                                <td class="d-flex">
                                                    <a href="{{ route('admin.list.view-perpertrators', ['id' => $report->_id]) }}"
                                                       class="btn btn-link btn-primary me-2" data-bs-toggle="tooltip"
                                                       title="View" data-original-title="View"> 
                                                       <i class="fa fa-eye"></i>
                                                    </a>
                                                
                                                    <a href="#" class="btn btn-link btn-primary" data-bs-toggle="tooltip"
                                                       title="Edit" data-original-title="Edit">
                                                       <i class="fa fa-edit"></i>
                                                    </a>
                                                </td>   
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="respondentModal" tabindex="-1" aria-labelledby="respondentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="respondentModalLabel">Add Complainee</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                        <form>
                            <!-- Incident Number and ID Number in the same row -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="incidentNumber" class="form-label">Incident Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="incidentNumber" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="idNumber" class="form-label">ID Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="idNumber" required>
                                </div>
                            </div>
                        
                            <!-- First Name, Middle Name, and Last Name in the same row -->
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="firstName" class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="firstName" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="middleName" class="form-label">Middle Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="middleName" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="lastName" class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="lastName" required>
                                </div>
                            </div>
                        
                            <!-- Department selection -->
                            <div class="row mb-3">
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
                            <div class="row mb-3">
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
                                <div class="col-md-6">
                                    <label for="section" class="form-label">Section <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="section" required>
                                </div>
                            </div>
                        
                            <!-- Remarks textarea -->
                            <div class="mb-3">
                                <label for="additionalInfo" class="form-label">Remarks <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="additionalInfo" rows="5"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Respondent</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- End Custom template -->
    </div>

    @include('partials.admin-footer')
    <script>
        $(document).ready(function() {
            $("#basic-datatables").DataTable({});
        });
    </script>
