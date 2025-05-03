<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Content Management</title>

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
                <h3 class="fw-bold mb-3">Profile</h3>
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
                        <a href="#">Profile</a>
                    </li>
                </ul>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Content Management</div>
                        </div>
                        <div class="card-body">
                            <div class="bg-secondary text-white p-2 mb-4">
                                <span>Step 1 of 2</span>
                            </div>

                            <!-- Add Step -->
                            <!-- First Form Group -->
                            <div class="row mb-4 border p-3">
                                <!-- Left Column (Content Input) -->
                                <div class="col-md-11">
                                    <div class="mb-3">
                                        <input type="text" class="form-control" id="title"
                                            placeholder="Add Section Title">
                                    </div>
                                    <div class="btn-group" role="group">
                                        <button type="button"
                                            class="btn btn-outline-secondary btn-sm mb-4"><strong>B</strong></button>
                                        <button type="button"
                                            class="btn btn-outline-secondary btn-sm mb-4"><em>I</em></button>
                                        <button type="button"
                                            class="btn btn-outline-secondary btn-sm mb-4"><u>U</u></button>
                                    </div>
                                    <hr>
                                    <div class="text-end d-flex justify-content-end align-items-center">
                                        <button class="btn btn-outline-danger btn-sm me-2" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <button class="btn btn-primary btn-sm me-3" title="Copy">
                                            <i class="fas fa-copy"></i>
                                        </button>

                                        <!-- Separator and Required Toggle -->
                                        <span class=" ">|</span>

                                        <!-- Modified Required Toggle with small label and switch -->
                                        <div class="form-check form-switch d-flex align-items-center">
                                            <label class="form-check-label small me-2" for="requiredToggle"
                                                style="margin-bottom: 0;">Required</label>
                                            <div>
                                                <input class="form-check-input form-check-input-sm" type="checkbox"
                                                    id="requiredToggle" style="width: 2rem; height: 1rem;">
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <!-- Right Column (Vertical Buttons) -->
                                <div class="col-md-1 d-flex justify-content-end">
                                    <div class="d-flex flex-column">
                                        <button class="btn btn-primary btn-sm  mb-1" title="Add content"><i
                                                class="fas fa-plus"></i></button>
                                        <button class="btn btn-primary btn-sm  mb-1" title="Text formatting"><i
                                                class="fas fa-font"></i></button>
                                        <button class="btn btn-primary btn-sm  mb-1" title="Another Step"><i
                                                class="fas fa-columns"></i></button>
                                    </div>
                                </div>
                            </div>

                            <!-- Add Paragraph -->
                            <div class="row mb-4 border p-3">
                                <!-- Left Column (Form Content) -->
                                <div class="col-md-11">
                                    <div class="mb-3">
                                        <div class="input-group">
                                            <input type="text" class="form-control"
                                                value="Are you submitting this report as the Complainant?">
                                            <div class="dropdown">
                                                <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                                    id="dropdownMenuButton" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i class="fas fa-align-left"></i> Paragraph
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <li><a class="dropdown-item" href="#"><i class="fas fa-list-ul"></i> Multiple Choice</a></li>
                                                    <li><a class="dropdown-item" href="#"><i class="fas fa-check-square"></i> Checkbox</a></li>
                                                    <li><a class="dropdown-item" href="#"><i class="fas fa-caret-down"></i> Dropdown</a></li>
                                                    <li><a class="dropdown-item" href="#"><i class="fas fa-upload"></i> Upload File</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                
                                    <!-- Paragraph Input Field -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">1. Enter your paragraph:</label>
                                        <textarea class="form-control" rows="4" placeholder="Write your response here..."></textarea>
                                    </div>
                                
                                    <div class="btn-group mb-3" role="group">
                                        <button type="button" class="btn btn-outline-secondary"><strong>B</strong></button>
                                        <button type="button" class="btn btn-outline-secondary"><em>I</em></button>
                                        <button type="button" class="btn btn-outline-secondary"><u>U</u></button>
                                    </div>
                                
                                    <hr>
                                
                                    <div class="text-end d-flex justify-content-end align-items-center">
                                        <button class="btn btn-outline-danger btn-sm me-2" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <button class="btn btn-primary btn-sm me-3" title="Copy">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                
                                        <!-- Separator and Required Toggle -->
                                        <span class="fs-5 me-3">|</span>
                                
                                        <!-- Modified Required Toggle with small label and switch -->
                                        <div class="form-check form-switch d-flex align-items-center">
                                            <label class="form-check-label small me-2" for="requiredToggle"
                                                style="margin-bottom: 0;">Required</label>
                                            <div>
                                                <input class="form-check-input form-check-input-sm" type="checkbox"
                                                    id="requiredToggle" style="width: 2rem; height: 1rem;">
                                            </div>
                                        </div>
                                    </div>
                                
                                </div>
                                
                                <!-- Right Column (Vertical Buttons) -->
                                <div class="col-md-1 d-flex justify-content-end">
                                    <div class="d-flex flex-column">
                                        <button class="btn btn-primary btn-sm  mb-1" title="Add content"><i
                                                class="fas fa-plus"></i></button>
                                        <button class="btn btn-primary btn-sm  mb-1"
                                            title="Text formatting"><i class="fas fa-font"></i></button>
                                        <button class="btn btn-primary btn-sm  mb-1" title="Another Step"><i
                                                class="fas fa-columns"></i></button>
                                    </div>
                                </div>
                            </div>

                            <!-- Add Multiple Choice -->
                            <div class="row mb-4 border p-3">
                                <!-- Left Column (Form Content) -->
                                <div class="col-md-11">
                                    <div class="mb-3">
                                        <div class="input-group">
                                            <input type="text" class="form-control"
                                                value="Are you submitting this report as the Complainant?">
                                            <div class="dropdown">
                                                <button class="btn btn-outline-secondary dropdown-toggle"
                                                    type="button" id="dropdownMenuButton" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i class="fas fa-list-ul"></i> Multiple Choice
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <li><a class="dropdown-item" href="#"><i
                                                                class="fas fa-align-left"></i> Paragraph</a></li>
                                                    <li><a class="dropdown-item" href="#"><i
                                                                class="fas fa-check-square"></i> Checkbox</a></li>
                                                    <li><a class="dropdown-item" href="#"><i
                                                                class="fas fa-caret-down"></i> Dropdown</a></li>
                                                    <li><a class="dropdown-item" href="#"><i
                                                                class="fas fa-upload"></i> Upload File</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="btn-group mb-3" role="group">
                                        <button type="button"
                                            class="btn btn-outline-secondary"><strong>B</strong></button>
                                        <button type="button" class="btn btn-outline-secondary"><em>I</em></button>
                                        <button type="button" class="btn btn-outline-secondary"><u>U</u></button>
                                    </div>

                                    <!-- Multiple Choice Options -->
                                    <div class="">
                                        <div class="form-check d-flex align-items-center">
                                            <input class="form-check-input me-2" type="radio" name="multipleChoice" id="choice1" disabled>
                                            <input type="text" class="form-control form-control-sm border-0 border-bottom w-50" value="Yes" placeholder="Option 1">
                                        </div>
                                        <div class="form-check d-flex align-items-center">
                                            <input class="form-check-input me-2" type="radio" name="multipleChoice" id="choice2" disabled>
                                            <input type="text" class="form-control form-control-sm border-0 border-bottom w-50" value="No" placeholder="Option 2">
                                        </div>
                                        <div class="form-check d-flex align-items-center">
                                            <input class="form-check-input me-2" type="radio" name="multipleChoice" id="choice3" disabled>
                                            <input type="text" class="form-control form-control-sm border-0 border-bottom w-50" value="Prefer not to say" placeholder="Option 3">
                                        </div>
                                    
                                        <!-- Add Option Button -->
                                        <button type="button" class="btn btn-secondary btn-sm mt-2" id="addOptionBtn">
                                            <i class="fas fa-plus"></i> Add Option
                                        </button>
                                        
                                    </div>


                                    <hr>
                                    <div class="text-end d-flex justify-content-end align-items-center">
                                        <button class="btn btn-outline-danger btn-sm me-2" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <button class="btn btn-primary btn-sm me-3" title="Copy">
                                            <i class="fas fa-copy"></i>
                                        </button>

                                        <!-- Separator and Required Toggle -->
                                        <span class="fs-5 me-3">|</span>

                                        <!-- Modified Required Toggle with larger switch and label -->
                                        <div class="form-check form-switch d-flex align-items-center">
                                            <label class="form-check-label small me-2" for="requiredToggle"
                                                style="margin-bottom: 0;">Required</label>
                                            <div>
                                                <input class="form-check-input form-check-input-sm" type="checkbox"
                                                    id="requiredToggle" style="width: 2rem; height: 1rem;">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column (Vertical Buttons) -->
                                <div class="col-md-1 d-flex justify-content-end">
                                    <div class="d-flex flex-column">
                                        <button class="btn btn-primary btn-sm mb-1" title="Add content"><i
                                                class="fas fa-plus"></i></button>
                                        <button class="btn btn-primary btn-sm mb-1" title="Text formatting"><i
                                                class="fas fa-font"></i></button>
                                        <button class="btn btn-primary btn-sm mb-1" title="Another Step"><i
                                                class="fas fa-columns"></i></button>
                                    </div>
                                </div>
                            </div>

                            <!-- Add Dropdowns -->
                            <div class="row mb-4 border p-3">
                                <!-- Left Column (Form Content) -->
                                <div class="col-md-11">
                                    <div class="mb-3">
                                        <div class="input-group">
                                            <input type="text" class="form-control"
                                                value="Are you submitting this report as the Complainant?">
                                            <div class="dropdown">
                                                <button class="btn btn-outline-secondary dropdown-toggle"
                                                    type="button" id="dropdownMenuButton" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i class="fas fa-caret-down"></i> Dropdown
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <li><a class="dropdown-item" href="#"><i
                                                                class="fas fa-align-left"></i> Paragraph</a></li>
                                                    <li><a class="dropdown-item" href="#"><i
                                                                class="fas fa-check-square"></i> Checkbox</a></li>
                                                    <li><a class="dropdown-item" href="#"><i
                                                                class="fas fa-list-ul"></i> Multiple Choice</a></li>
                                                    <li><a class="dropdown-item" href="#"><i
                                                                class="fas fa-upload"></i> Upload File</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="btn-group mb-3" role="group">
                                        <button type="button"
                                            class="btn btn-outline-secondary"><strong>B</strong></button>
                                        <button type="button" class="btn btn-outline-secondary"><em>I</em></button>
                                        <button type="button" class="btn btn-outline-secondary"><u>U</u></button>
                                    </div>

                                    <!-- Editable Dropdown (simulated) -->
                                    <div class="mb-3">
                                        <div class="mt-2">
                                            <div class="d-flex align-items-center mb-1">
                                                <span class="me-2">1.</span>
                                                <input type="text"
                                                    class="form-control form-control-sm border-0 border-bottom w-50"
                                                    value="Yes" placeholder="Option 1">
                                            </div>
                                            <div class="d-flex align-items-center mb-1">
                                                <span class="me-2">2.</span>
                                                <input type="text"
                                                    class="form-control form-control-sm border-0 border-bottom w-50"
                                                    value="No" placeholder="Option 2">
                                            </div>
                                            <div class="d-flex align-items-center mb-1">
                                                <span class="me-2">3.</span>
                                                <input type="text"
                                                    class="form-control form-control-sm border-0 border-bottom w-50"
                                                    value="Prefer not to say" placeholder="Option 3">
                                            </div>
                                             <!-- Add Option Button -->
                                        <button type="button" class="btn btn-secondary btn-sm mt-2" id="addOptionBtn">
                                            <i class="fas fa-plus"></i> Add Option
                                        </button>
                                        </div>
                                    </div>

                                    <hr>
                                    <div class="text-end d-flex justify-content-end align-items-center">
                                        <button class="btn btn-outline-danger btn-sm me-2" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <button class="btn btn-primary btn-sm me-3" title="Copy">
                                            <i class="fas fa-copy"></i>
                                        </button>

                                        <span class="fs-5 me-3">|</span>

                                        <div class="form-check form-switch d-flex align-items-center">
                                            <label class="form-check-label small me-2" for="requiredToggle"
                                                style="margin-bottom: 0;">Required</label>
                                            <div>
                                                <input class="form-check-input form-check-input-sm" type="checkbox"
                                                    id="requiredToggle" style="width: 2rem; height: 1rem;">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column (Vertical Buttons) -->
                                <div class="col-md-1 d-flex justify-content-end">
                                    <div class="d-flex flex-column">
                                        <button class="btn btn-primary btn-sm mb-1" title="Add content"><i
                                                class="fas fa-plus"></i></button>
                                        <button class="btn btn-primary btn-sm mb-1" title="Text formatting"><i
                                                class="fas fa-font"></i></button>
                                        <button class="btn btn-primary btn-sm mb-1" title="Another Step"><i
                                                class="fas fa-columns"></i></button>
                                    </div>
                                </div>
                            </div>

                            <!-- File Upload Component -->
                            <div class="row mb-4 border p-3">
                                <!-- Left Column (Form Content) -->
                                <div class="col-md-11">
                                    <div class="mb-3">
                                        <div class="input-group">
                                            <input type="text" class="form-control" value="Question"
                                                placeholder="Enter your question here">
                                            <div class="dropdown">
                                                <button class="btn btn-outline-secondary dropdown-toggle"
                                                    type="button" id="fileUploadDropdown" data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                    <i class="fas fa-caret-down"></i> File upload
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="fileUploadDropdown">
                                                    <li><a class="dropdown-item" href="#"><i
                                                                class="fas fa-align-left"></i> Paragraph</a></li>
                                                    <li><a class="dropdown-item" href="#"><i
                                                                class="fas fa-check-square"></i> Checkbox</a></li>
                                                    <li><a class="dropdown-item" href="#"><i
                                                                class="fas fa-list-ul"></i> Multiple Choice</a></li>
                                                    <li><a class="dropdown-item" href="#"><i
                                                                class="fas fa-upload"></i> Upload File</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="btn-group mb-3" role="group">
                                        <button type="button"
                                            class="btn btn-outline-secondary"><strong>B</strong></button>
                                        <button type="button" class="btn btn-outline-secondary"><em>I</em></button>
                                        <button type="button" class="btn btn-outline-secondary"><u>U</u></button>
                                    </div>

                                    <!-- File Upload Settings -->
                                    <div class="">
                                        <!-- Allow specific file types -->
                                        <div class="d-flex align-items-center">
                                            <label class="form-check-label mb-0 me-2" for="allowSpecificTypes">Allow only specific file types</label>
                                            <div class="form-check form-switch" style="margin: 0; padding: 0;">
                                                <input class="form-check-input" type="checkbox" id="allowSpecificTypes" checked style="margin-top: 4px;">
                                            </div>
                                        </div>

                                        <!-- File types checkboxes -->
                                        <div class="row">
                                            <div class="col-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="pdfType">
                                                    <label class="form-check-label" for="pdfType">PDF</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="videoType">
                                                    <label class="form-check-label" for="videoType">Video</label>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="imageType">
                                                    <label class="form-check-label" for="imageType">Image</label>
                                                </div>
                                              
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="audioType">
                                                    <label class="form-check-label" for="audioType">Audio</label>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Maximum number of files -->
                                        <div class="mb-3 d-flex align-items-center">
                                            <label for="maxFiles" class="form-label me-3" style="width: 200px;">Maximum number of files:</label>
                                            <select class="form-select w-auto" id="maxFiles">
                                                <option value="1" selected>1</option>
                                                <option value="5">5</option>
                                                <option value="10">10</option>
                                                <option value="unlimited">Unlimited</option>
                                            </select>
                                        </div>

                                        <!-- Maximum file size -->
                                        <div class="mb-3 d-flex align-items-center">
                                            <label for="maxFileSize" class="form-label me-3" style="width: 200px;">Maximum file size:</label>
                                            <select class="form-select w-auto" id="maxFileSize">
                                                <option value="1">1 MB</option>
                                                <option value="10" selected>10 MB</option>
                                                <option value="100">100 MB</option>
                                                <option value="1000">1 GB</option>
                                            </select>
                                        </div>
                                    </div>

                                    <hr>
                                    <div class="text-end d-flex justify-content-end align-items-center">
                                        <button class="btn btn-outline-danger btn-sm me-2" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <button class="btn btn-primary btn-sm me-3" title="Copy">
                                            <i class="fas fa-copy"></i>
                                        </button>

                                        <span class="fs-5 me-3">|</span>

                                        <div class="form-check form-switch d-flex align-items-center">
                                            <label class="form-check-label small me-2" for="requiredToggle"
                                                style="margin-bottom: 0;">Required</label>
                                            <div>
                                                <input class="form-check-input form-check-input-sm" type="checkbox"
                                                    id="requiredToggle" style="width: 2rem; height: 1rem;">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column (Vertical Buttons) -->
                                <div class="col-md-1 d-flex justify-content-end">
                                    <div class="d-flex flex-column">
                                        <button class="btn btn-primary btn-sm mb-1" title="Add content"><i
                                                class="fas fa-plus"></i></button>
                                        <button class="btn btn-primary btn-sm mb-1" title="Text formatting"><i
                                                class="fas fa-font"></i></button>
                                        <button class="btn btn-primary btn-sm mb-1" title="Another Step"><i
                                                class="fas fa-columns"></i></button>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-secondary text-white p-2 mb-4">
                                <span>Step 2 of 2</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    @include('partials.admin-footer')
