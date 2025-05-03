    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <title>Content Management</title>
        @include('partials.admin-link')
        <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
        <style>
            .loading-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(255, 255, 255, 0.8);
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
                z-index: 9999;
            }

            .spinner {
                border: 4px solid #f3f3f3;
                border-top: 4px solid #3498db;
                border-radius: 50%;
                width: 40px;
                height: 40px;
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                0% {
                    transform: rotate(0deg);
                }

                100% {
                    transform: rotate(360deg);
                }
            }

            .step-card {
                border: 1px solid #ddd;
                border-radius: 8px;
                padding: 15px;
                background: #fff;
                margin-bottom: 20px;
            }

            .element-card {
                border: none;
                border-bottom: 1px solid #e0e0e0;
                padding: 15px 0;
                margin-bottom: 10px;
                background: transparent;
            }

            .element-card .form-control {
                border: none;
                border-bottom: 1px solid #ccc;
                border-radius: 0;
                box-shadow: none;
            }

            .add-element-modal .list-group-item {
                cursor: pointer;
            }

            .add-element-modal .list-group-item:hover {
                background: #f0f0f0;
            }

            .step-header {
                display: flex;
                align-items: center;
                margin-bottom: 10px;
            }

            .step-title-input {
                flex-grow: 1;
                margin-left: 10px;
                border: none;
                border-bottom: 1px solid #ccc;
                border-radius: 0;
                box-shadow: none;
            }

            .step-counter {
                white-space: nowrap;
                display: inline-flex;
                align-items: center;
                background-color: #343a40;
                color: white;
                padding: 0.5rem 1rem;
                border-radius: 0.25rem;
                font-size: 0.9rem;
                margin-bottom: 0.5rem;
            }
        </style>
    </head>

    <body>
        <div id="loading-overlay" style="display: none;">
            <img id="loading-logo" src="{{ asset('assets/img/logo-4.png') }}" alt="Loading Logo">
            <div class="spinner"></div>
        </div>

        @include('partials.admin-sidebar')
        @include('partials.admin-header')

        <div class="container">
            <div class="page-inner">
                <div class="page-header">
                    <h3 class="fw-bold mb-3">Content</h3>
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
                            <a href="#">Content</a>
                        </li>
                    </ul>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Content Management</div>
                            </div>
                            <div class="card-body" id="form-builder">
                                <div id="steps-container"></div>
                                <button class="btn btn-secondary mt-3" id="add-step-btn">
                                    <i class="fas fa-plus"></i> Add Step
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Element Modal -->
        <div class="modal fade" id="addElementModal" tabindex="-1" aria-labelledby="addElementModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addElementModalLabel">Add Element</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="list-group add-element-modal">
                            <a href="#" class="list-group-item list-group-item-action" data-type="paragraph"><i
                                    class="fas fa-align-left me-2"></i>Paragraph</a>
                            <a href="#" class="list-group-item list-group-item-action"
                                data-type="multiple_choice"><i class="fas fa-list-ul me-2"></i>Multiple Choice</a>
                            <a href="#" class="list-group-item list-group-item-action" data-type="checkbox"><i
                                    class="fas fa-check-square me-2"></i>Checkbox</a>
                            <a href="#" class="list-group-item list-group-item-action" data-type="dropdown"><i
                                    class="fas fa-caret-down me-2"></i>Dropdown</a>
                            <a href="#" class="list-group-item list-group-item-action" data-type="file_upload"><i
                                    class="fas fa-upload me-2"></i>File Upload</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('partials.admin-footer')

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script>
            $(document).ready(function() {
                // CSRF Token Setup
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                let formBuilderId = null;
                let steps = [];

                // Initial form data from the server
                const initialFormData = @json($formBuilderData);

                // Initialize Form Builder
                function initFormBuilder() {
                    $('#loading-overlay').show();
                    if (initialFormData && initialFormData.id && initialFormData.steps) {
                        formBuilderId = initialFormData.id;
                        steps = initialFormData.steps || [];
                        renderSteps();
                        // Load elements for each step
                        steps.forEach(step => loadElements(step.id));
                    } else {
                        // Fetch form data from server if initialFormData is incomplete
                        $.ajax({
                            url: '{{ route('form-builders.create') }}',
                            method: 'POST',
                            data: {
                                title: 'New Form',
                                description: 'Form created on {{ now()->format('Y-m-d') }}'
                            },
                            beforeSend: function() {
                                $('#loading-overlay').show();
                            },
                            success: function(response) {
                                if (response.success && response.form && response.form.id) {
                                    formBuilderId = response.form.id;
                                    steps = response.form.steps || [];
                                    renderSteps();
                                    // Load elements for each step
                                    steps.forEach(step => loadElements(step.id));
                                } else {
                                    toastr.error('Failed to initialize form builder.');
                                    console.error('Invalid response:', response);
                                }
                            },
                            error: function(xhr) {
                                toastr.error('Failed to create form: ' + (xhr.responseJSON?.message ||
                                    'Unknown error.'));
                                console.error('Init error:', xhr);
                            },
                            complete: function() {
                                $('#loading-overlay').hide();
                            }
                        });
                    }
                }


                // Render Steps
                function renderSteps() {
                    const $stepsContainer = $('#steps-container');
                    // Store existing step DOM elements
                    const existingSteps = {};
                    $stepsContainer.find('.step-card').each(function() {
                        const stepId = $(this).data('step-id');
                        existingSteps[stepId] = $(this);
                    });

                    // Clear container
                    $stepsContainer.empty();

                    // Re-render steps, reusing existing DOM if possible
                    steps.forEach((step, index) => {
                        let $stepCard;
                        if (existingSteps[step.id]) {
                            // Reuse existing step DOM
                            $stepCard = existingSteps[step.id];
                            // Update step title if changed
                            $stepCard.find('.step-title-input').val(step.title || 'Untitled Step');
                            $stepCard.find('.step-counter span').text(`Step ${index + 1} of ${steps.length}`);
                            // Show/hide delete button
                            if (index === 0) {
                                $stepCard.find('.delete-step-btn').hide();
                            } else {
                                $stepCard.find('.delete-step-btn').show();
                            }
                        } else {
                            // Create new step DOM
                            const stepHtml = `
                <div class="step-card" data-step-id="${step.id}">
                    <div class="step-header">
                        <div class="step-counter bg-secondary text-white p-2 rounded mb-2">
                            <span>Step ${index + 1} of ${steps.length}</span>
                        </div>
                        <input type="text" class="form-control step-title-input" data-step-id="${step.id}" value="${step.title || 'Untitled Step'}" placeholder="Enter step title">
                        <button class="btn btn-outline-danger btn-sm delete-step-btn" data-step-id="${step.id}" ${index === 0 ? 'style="display:none;"' : ''} title="Delete Step">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <div class="elements-container" data-step-id="${step.id}"></div>
                    <button class="btn btn-primary btn-sm add-element-btn" data-step-id="${step.id}">
                        <i class="fas fa-plus"></i> Add Element
                    </button>
                </div>
            `;
                            $stepCard = $(stepHtml);
                            loadElements(step.id, $stepCard.find(
                                `.elements-container[data-step-id="${step.id}"]`));
                        }
                        $stepsContainer.append($stepCard);
                    });
                }
                // Load Elements for a Step
                function loadElements(stepId) {
                    if (!formBuilderId) {
                        toastr.error('Form builder not initialized.');
                        return;
                    }
                    if (!stepId) {
                        toastr.error('Step ID is missing.');
                        return;
                    }

                    if (initialFormData && initialFormData.elements[stepId]) {
                        renderElements(stepId, initialFormData.elements[stepId]);
                    } else {
                        $.ajax({
                            url: '{{ route('form-elements.get-by-step', ['formId' => ':formId', 'stepId' => ':stepId']) }}'
                                .replace(':formId', formBuilderId)
                                .replace(':stepId', encodeURIComponent(stepId)),
                            method: 'GET',
                            success: function(response) {
                                if (response.success && Array.isArray(response.elements)) {
                                    renderElements(stepId, response.elements);
                                } else {
                                    toastr.error('Failed to load elements.');
                                    console.error('Invalid elements response:', response);
                                }
                            },
                            error: function(xhr) {
                                toastr.error('Failed to load elements: ' + (xhr.responseJSON?.message ||
                                    'Unknown error.'));
                                console.error('Load elements error:', xhr);
                            }
                        });
                    }
                }

                // Render Elements
                function renderElements(stepId, elements) {
                    const $container = $(`.elements-container[data-step-id="${stepId}"]`);
                    $container.empty();
                    elements.forEach(element => {
                        let elementHtml = '';
                        switch (element.element_type) {
                            case 'paragraph':
                                elementHtml = `
                                    <div class="element-card row" data-element-id="${element.id}">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <input type="text" class="form-control element-title" value="${element.title || 'Untitled Paragraph'}" placeholder="Enter title">
                                            </div>
                                            <div class="mb-3">
                                                <textarea class="form-control element-content" rows="4" placeholder="Write your response here...">${element.content || ''}</textarea>
                                            </div>
                                            <div class="d-flex justify-content-end align-items-center">
                                                <button class="btn btn-outline-danger btn-sm me-2 delete-element" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <button class="btn btn-primary btn-sm me-2 duplicate-element" title="Copy">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input required-toggle" type="checkbox" id="required-${element.id}" ${element.is_required ? 'checked' : ''}>
                                                    <label class="form-check-label" for="required-${element.id}">Required</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                break;
                            case 'multiple_choice':
                                elementHtml = `
                                    <div class="element-card row" data-element-id="${element.id}">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <input type="text" class="form-control element-title" value="${element.title || 'Untitled Question'}" placeholder="Enter question">
                                            </div>
                                            <div class="options-container">
                                                ${element.options?.map((opt, idx) => `
                                                                                <div class="form-check mb-2 d-flex align-items-center">
                                                                                    <input class="form-check-input me-2" type="checkbox" disabled>
                                                                                    <input type="text" class="form-control form-control-sm option-input" data-option-id="${opt.id}" value="${opt.text}">
                                                                                </div>
                                                                            `).join('') || ''}
                                                <button type="button" class="btn btn-outline-secondary btn-sm add-option" style="margin-left: 0.8rem;">
                                                    <i class="fas fa-plus"></i> Add Option
                                                </button>
                                            </div>
                                            <div class="d-flex justify-content-end align-items-center mt-3">
                                                <button class="btn btn-outline-danger btn-sm me-2 delete-element" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <button class="btn btn-primary btn-sm me-2 duplicate-element" title="Copy">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                                <span class="mx-2">|</span>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input required-toggle" type="checkbox" id="required-${element.id}" ${element.is_required ? 'checked' : ''}>
                                                    <label class="form-check-label" for="required-${element.id}">Required</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                break;
                            case 'checkbox':
                                elementHtml = `
                                    <div class="element-card row" data-element-id="${element.id}">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <input type="text" class="form-control element-title" value="${element.title || 'Untitled Checkbox Question'}" placeholder="Enter question">
                                            </div>
                                            <div class="options-container">
                                                ${element.options?.map((opt, idx) => `
                                                                                <div class="form-check mb-2 d-flex align-items-center">
                                                                                    <input class="form-check-input me-2" type="checkbox" disabled>
                                                                                    <input type="text" class="form-control form-control-sm option-input" data-option-id="${opt.id}" value="${opt.text}">
                                                                                </div>
                                                                            `).join('') || ''}
                                                <button type="button" class="btn btn-outline-secondary btn-sm add-option" style="margin-left: 0.8rem;">
                                                    <i class="fas fa-plus"></i> Add Option
                                                </button>
                                            </div>
                                            <div class="d-flex justify-content-end align-items-center mt-3">
                                                <button class="btn btn-outline-danger btn-sm me-2 delete-element" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <button class="btn btn-primary btn-sm me-2 duplicate-element" title="Copy">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                                <span class="mx-2">|</span>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input required-toggle" type="checkbox" id="required-${element.id}" ${element.is_required ? 'checked' : ''}>
                                                    <label class="form-check-label" for="required-${element.id}">Required</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                break;
                            case 'dropdown':
                                elementHtml = `
                                    <div class="element-card row" data-element-id="${element.id}">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <input type="text" class="form-control element-title" value="${element.title || 'Untitled Dropdown'}" placeholder="Enter question">
                                            </div>
                                            <div class="options-container">
                                                ${element.options?.map((opt, idx) => `
                                                                                    <div class="d-flex align-items-center mb-1">
                                                                                        <span class="me-2">${idx + 1}.</span>
                                                                                        <input type="text" class="form-control form-control-sm option-input" data-option-id="${opt.id}" value="${opt.text}">
                                                                                    </div>
                                                                                `).join('') || ''}
                                                <button type="button" class="btn btn-outline-secondary btn-sm add-option mt-2" style="margin-left: 0.8rem;"> 
                                                    <i class="fas fa-plus"></i> Add Option
                                                </button>
                                            </div>
                                            <div class="d-flex justify-content-end align-items-center mt-3">
                                                <button class="btn btn-outline-danger btn-sm me-2 delete-element" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <button class="btn btn-primary btn-sm me-2 duplicate-element" title="Copy">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input required-toggle" type="checkbox" id="required-${element.id}" ${element.is_required ? 'checked' : ''}>
                                                    <label class="form-check-label" for="required-${element.id}">Required</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                break;
                            case 'file_upload':
                                elementHtml = `
                                    <div class="element-card row" data-element-id="${element.id}">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <input type="text" class="form-control element-title" value="${element.title || 'Untitled File Upload'}" placeholder="Enter title">
                                            </div>
                                            <div class="file-upload-settings">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" id="allow-specific-${element.id}" ${element.settings?.allow_specific_types ? 'checked' : ''}>
                                                    <label class="form-check-label" for="allow-specific-${element.id}">Allow only specific file types</label>
                                                </div>
                                                <div class="row">
                                                    <div class="col-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="pdf-${element.id}" ${element.settings?.file_types?.includes('pdf') ? 'checked' : ''}>
                                                            <label class="form-check-label" for="pdf-${element.id}">PDF</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="image-${element.id}" ${element.settings?.file_types?.includes('image') ? 'checked' : ''}>
                                                            <label class="form-check-label" for="image-${element.id}">Image</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="video-${element.id}" ${element.settings?.file_types?.includes('video') ? 'checked' : ''}>
                                                            <label class="form-check-label" for="video-${element.id}">Video</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="audio-${element.id}" ${element.settings?.file_types?.includes('audio') ? 'checked' : ''}>
                                                            <label class="form-check-label" for="audio-${element.id}">Audio</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="max-files-${element.id}" class="form-label">Max files:</label>
                                                    <select class="form-select" id="max-files-${element.id}">
                                                        <option value="1" ${element.settings?.max_files === 1 ? 'selected' : ''}>1</option>
                                                        <option value="5" ${element.settings?.max_files === 5 ? 'selected' : ''}>5</option>
                                                        <option value="10" ${element.settings?.max_files === 10 ? 'selected' : ''}>10</option>
                                                        <option value="unlimited" ${element.settings?.max_files === 'unlimited' ? 'selected' : ''}>Unlimited</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="max-file-size-${element.id}" class="form-label">Max file size:</label>
                                                    <select class="form-select" id="max-file-size-${element.id}">
                                                        <option value="1" ${element.settings?.max_file_size === 1 ? 'selected' : ''}>1 MB</option>
                                                        <option value="10" ${element.settings?.max_file_size === 10 ? 'selected' : ''}>10 MB</option>
                                                        <option value="100" ${element.settings?.max_file_size === 100 ? 'selected' : ''}>100 MB</option>
                                                        <option value="1000" ${element.settings?.max_file_size === 1000 ? 'selected' : ''}>1 GB</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-end align-items-center">
                                                <button class="btn btn-outline-danger btn-sm me-2 delete-element" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                <button class="btn btn-primary btn-sm me-2 duplicate-element" title="Copy">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input required-toggle" type="checkbox" id="required-${element.id}" ${element.is_required ? 'checked' : ''}>
                                                    <label class="form-check-label" for="required-${element.id}">Required</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                break;
                        }
                        $container.append(elementHtml);
                    });
                }

                // Add Step
                $('#add-step-btn').click(function() {
                    if (!formBuilderId) {
                        toastr.error('Form builder not initialized.');
                        return;
                    }
                    $.ajax({
                        url: '{{ route('form-builders.steps.add', ':formId') }}'.replace(':formId',
                            formBuilderId),
                        method: 'POST',
                        data: {
                            title: 'Untitled Step'
                        },
                        beforeSend: function() {
                            $('#loading-overlay').show();
                        },
                        success: function(response) {
                            if (response.success && response.step) {
                                steps.push(response.step);
                                // Append new step without re-rendering all
                                const stepHtml = `
                    <div class="step-card" data-step-id="${response.step.id}">
                        <div class="step-header">
                            <div class="step-counter bg-secondary text-white p-2 rounded mb-2">
                                <span>Step ${steps.length} of ${steps.length}</span>
                            </div>
                            <input type="text" class="form-control step-title-input" data-step-id="${response.step.id}" value="${response.step.title || 'Untitled Step'}" placeholder="Enter step title">
                            <button class="btn btn-outline-danger btn-sm delete-step-btn" data-step-id="${response.step.id}" title="Delete Step">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="elements-container" data-step-id="${response.step.id}"></div>
                        <button class="btn btn-primary btn-sm add-element-btn" data-step-id="${response.step.id}">
                            <i class="fas fa-plus"></i> Add Element
                        </button>
                    </div>
                `;
                                $('#steps-container').append(stepHtml);
                                // Update step counters for existing steps
                                $('#steps-container .step-card').each(function(index) {
                                    $(this).find('.step-counter span').text(
                                        `Step ${index + 1} of ${steps.length}`);
                                    // Show/hide delete button based on position
                                    if (index === 0) {
                                        $(this).find('.delete-step-btn').hide();
                                    } else {
                                        $(this).find('.delete-step-btn').show();
                                    }
                                });
                                toastr.success('Step added successfully.');
                            } else {
                                toastr.error('Failed to add step.');
                                console.error('Add step response:', response);
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Failed to add step: ' + (xhr.responseJSON?.message ||
                                'Unknown error.'));
                            console.error('Add step error:', xhr);
                        },
                        complete: function() {
                            $('#loading-overlay').hide();
                        }
                    });
                });

                // Delete Step
                $(document).on('click', '.delete-step-btn', function() {
                    if (!formBuilderId) {
                        toastr.error('Form builder not initialized.');
                        return;
                    }
                    const stepId = $(this).data('step-id');
                    $.ajax({
                        url: '{{ route('form-builders.steps.delete', ['formId' => ':formId', 'stepId' => ':stepId']) }}'
                            .replace(':formId', formBuilderId)
                            .replace(':stepId', encodeURIComponent(stepId)),
                        method: 'DELETE',
                        beforeSend: function() {
                            $('#loading-overlay').show();
                        },
                        success: function(response) {
                            if (response.success) {
                                steps = response.steps || steps.filter(step => step.id !== stepId);
                                $(`.step-card[data-step-id="${stepId}"]`).remove();
                                // Update step counters
                                $('#steps-container .step-card').each(function(index) {
                                    $(this).find('.step-counter span').text(
                                        `Step ${index + 1} of ${steps.length}`);
                                    if (index === 0) {
                                        $(this).find('.delete-step-btn').hide();
                                    } else {
                                        $(this).find('.delete-step-btn').show();
                                    }
                                });
                                toastr.success('Step deleted successfully.');
                            } else {
                                toastr.error('Failed to delete step: ' + (response.message ||
                                    'Unknown error.'));
                                console.error('Delete step response:', response);
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Failed to delete step: ' + (xhr.responseJSON?.message ||
                                'Unknown error.'));
                            console.error('Delete step error:', xhr);
                        },
                        complete: function() {
                            $('#loading-overlay').hide();
                        }
                    });
                });

                // Update Step Title
                $(document).on('change', '.step-title-input', function() {
                    if (!formBuilderId) {
                        toastr.error('Form builder not initialized.');
                        return;
                    }
                    const stepId = $(this).data('step-id');
                    const title = $(this).val();
                    $.ajax({
                        url: '{{ route('form-builders.update', ':id') }}'.replace(':id', formBuilderId),
                        method: 'PUT',
                        data: {
                            steps: steps.map(step => ({
                                id: step.id,
                                title: step.id === stepId ? title : step.title
                            }))
                        },
                        success: function(response) {
                            if (response.success) {
                                steps = response.form.steps || steps;
                                toastr.success('Step title updated.');
                            } else {
                                toastr.error('Failed to update step title.');
                                console.error('Update step title response:', response);
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Failed to update step title: ' + (xhr.responseJSON
                                ?.message || 'Unknown error.'));
                            console.error('Update step title error:', xhr);
                        }
                    });
                });

                // Show Add Element Modal
                $(document).on('click', '.add-element-btn', function() {
                    const stepId = $(this).data('step-id');
                    $('#addElementModal').data('step-id', stepId).modal('show');
                });

                // Add Element from Modal
                $(document).on('click', '.add-element-modal .list-group-item', function(e) {
                    e.preventDefault();
                    if (!formBuilderId) {
                        toastr.error('Form builder not initialized.');
                        return;
                    }
                    const stepId = $('#addElementModal').data('step-id');
                    const elementType = $(this).data('type');
                    const position = $(`.elements-container[data-step-id="${stepId}"] .element-card`).length +
                        1;

                    $.ajax({
                        url: '{{ route('form-elements.add') }}',
                        method: 'POST',
                        data: {
                            form_builder_id: formBuilderId,
                            step_id: stepId,
                            element_type: elementType,
                            position: position,
                            title: `Untitled ${elementType.replace('_', ' ')}`
                        },
                        beforeSend: function() {
                            $('#loading-overlay').show();
                        },
                        success: function(response) {
                            if (response.success) {
                                loadElements(stepId);
                                toastr.success('Element added successfully.');
                                $('#addElementModal').modal('hide');
                            } else {
                                toastr.error('Failed to add element.');
                                console.error('Add element response:', response);
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Failed to add element: ' + (xhr.responseJSON?.message ||
                                'Unknown error.'));
                            console.error('Add element error:', xhr);
                        },
                        complete: function() {
                            $('#loading-overlay').hide();
                        }
                    });
                });

                // Update Element Title or Content
                $(document).on('change', '.element-title, .element-content', function() {
                    if (!formBuilderId) {
                        toastr.error('Form builder not initialized.');
                        return;
                    }
                    const $card = $(this).closest('.element-card');
                    const

                        elementId = $card.data('element-id');
                    const data = {};
                    if ($(this).hasClass('element-title')) {
                        data.title = $(this).val();
                    } else {
                        data.content = $(this).val();
                    }
                    $.ajax({
                        url: '{{ route('form-elements.update', ':id') }}'.replace(':id', elementId),
                        method: 'PUT',
                        data: data,
                        success: function(response) {
                            if (response.success) {
                                toastr.success('Element updated.');
                            } else {
                                toastr.error('Failed to update element.');
                                console.error('Update element response:', response);
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Failed to update element: ' + (xhr.responseJSON
                                ?.message || 'Unknown error.'));
                            console.error('Update element error:', xhr);
                        }
                    });
                });

                // Update Required Toggle
                $(document).on('change', '.required-toggle', function() {
                    if (!formBuilderId) {
                        toastr.error('Form builder not initialized.');
                        return;
                    }
                    const $card = $(this).closest('.element-card');
                    const elementId = $card.data('element-id');
                    $.ajax({
                        url: '{{ route('form-elements.update', ':id') }}'.replace(':id', elementId),
                        method: 'PUT',
                        data: {
                            is_required: $(this).is(':checked')
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success('Required status updated.');
                            } else {
                                toastr.error('Failed to update required status.');
                                console.error('Update required response:', response);
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Failed to update required status: ' + (xhr.responseJSON
                                ?.message || 'Unknown error.'));
                            console.error('Update required error:', xhr);
                        }
                    });
                });

                // Delete Element
                $(document).on('click', '.delete-element', function() {
                    if (!formBuilderId) {
                        toastr.error('Form builder not initialized.');
                        return;
                    }
                    const $card = $(this).closest('.element-card');
                    const elementId = $card.data('element-id');
                    const stepId = $card.closest('.step-card').data('step-id');
                    $.ajax({
                        url: '{{ route('form-elements.delete', ':id') }}'.replace(':id', elementId),
                        method: 'DELETE',
                        beforeSend: function() {
                            $('#loading-overlay').show();
                        },
                        success: function(response) {
                            if (response.success) {
                                $card.remove();
                                toastr.success('Element deleted.');
                            } else {
                                toastr.error('Failed to delete element.');
                                console.error('Delete element response:', response);
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Failed to delete element: ' + (xhr.responseJSON
                                ?.message || 'Unknown error.'));
                            console.error('Delete element error:', xhr);
                        },
                        complete: function() {
                            $('#loading-overlay').hide();
                        }
                    });
                });

                // Duplicate Element
                $(document).on('click', '.duplicate-element', function() {
                    if (!formBuilderId) {
                        toastr.error('Form builder not initialized.');
                        return;
                    }
                    const $card = $(this).closest('.element-card');
                    const elementId = $card.data('element-id');
                    const stepId = $card.closest('.step-card').data('step-id');
                    $.ajax({
                        url: '{{ route('form-elements.duplicate', ':id') }}'.replace(':id', elementId),
                        method: 'POST',
                        beforeSend: function() {
                            $('#loading-overlay').show();
                        },
                        success: function(response) {
                            if (response.success && response.element) {
                                const $container = $(
                                    `.elements-container[data-step-id="${stepId}"]`);
                                let elementHtml = '';
                                const element = response.element;
                                switch (element.element_type) {
                                    case 'paragraph':
                                        elementHtml = `
                                            <div class="element-card row" data-element-id="${element.id}">
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <input type="text" class="form-control element-title" value="${element.title || 'Untitled Paragraph'}" placeholder="Enter title">
                                                    </div>
                                                    <div class="mb-3">
                                                        <textarea class="form-control element-content" rows="4" placeholder="Write your response here...">${element.content || ''}</textarea>
                                                    </div>
                                                    <div class="d-flex justify-content-end align-items-center">
                                                        <button class="btn btn-outline-danger btn-sm me-2 delete-element" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                        <button class="btn btn-primary btn-sm me-2 duplicate-element" title="Copy">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input required-toggle" type="checkbox" id="required-${element.id}" ${element.is_required ? 'checked' : ''}>
                                                            <label class="form-check-label" for="required-${element.id}">Required</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                        break;
                                    case 'multiple_choice':
                                        elementHtml = `
                                            <div class="element-card row" data-element-id="${element.id}">
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <input type="text" class="form-control element-title" value="${element.title || 'Untitled Question'}" placeholder="Enter question">
                                                    </div>
                                                    <div class="options-container">
                                                        ${element.options?.map((opt, idx) => `
                                                                                            <div class="form-check mb-2 d-flex align-items-center">
                                                                                                <input class="form-check-input me-2" type="checkbox" disabled>
                                                                                                <input type="text" class="form-control form-control-sm option-input" data-option-id="${opt.id}" value="${opt.text}">
                                                                                            </div>
                                                                                        `).join('') || ''}
                                                        <button type="button" class="btn btn-outline-secondary btn-sm add-option" style="margin-left: 0.8rem;">
                                                            <i class="fas fa-plus"></i> Add Option
                                                        </button>
                                                    </div>
                                                    <div class="d-flex justify-content-end align-items-center mt-3">
                                                        <button class="btn btn-outline-danger btn-sm me-2 delete-element" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                        <button class="btn btn-primary btn-sm me-2 duplicate-element" title="Copy">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                        <span class="mx-2">|</span>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input required-toggle" type="checkbox" id="required-${element.id}" ${element.is_required ? 'checked' : ''}>
                                                            <label class="form-check-label" for="required-${element.id}">Required</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                        break;
                                    case 'checkbox':
                                        elementHtml = `
                                            <div class="element-card row" data-element-id="${element.id}">
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <input type="text" class="form-control element-title" value="${element.title || 'Untitled Checkbox Question'}" placeholder="Enter question">
                                                    </div>
                                                    <div class="options-container">
                                                        ${element.options?.map((opt, idx) => `
                                                                                            <div class="form-check mb-2 d-flex align-items-center">
                                                                                                <input class="form-check-input me-2" type="checkbox" disabled>
                                                                                                <input type="text" class="form-control form-control-sm option-input" data-option-id="${opt.id}" value="${opt.text}">
                                                                                            </div>
                                                                                        `).join('') || ''}
                                                        <button type="button" class="btn btn-outline-secondary btn-sm add-option" style="margin-left: 0.8rem;">
                                                            <i class="fas fa-plus"></i> Add Option
                                                        </button>
                                                    </div>
                                                    <div class="d-flex justify-content-end align-items-center mt-3">
                                                        <button class="btn btn-outline-danger btn-sm me-2 delete-element" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                        <button class="btn btn-primary btn-sm me-2 duplicate-element" title="Copy">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                        <span class="mx-2">|</span>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input required-toggle" type="checkbox" id="required-${element.id}" ${element.is_required ? 'checked' : ''}>
                                                            <label class="form-check-label" for="required-${element.id}">Required</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                        break;
                                    case 'dropdown':
                                        elementHtml = `
                                            <div class="element-card row" data-element-id="${element.id}">
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <input type="text" class="form-control element-title" value="${element.title || 'Untitled Dropdown'}" placeholder="Enter question">
                                                    </div>
                                                    <div class="options-container">
                                                        ${element.options?.map((opt, idx) => `
                                                                                            <div class="d-flex align-items-center mb-1">
                                                                                                <span class="me-2">${idx + 1}.</span>
                                                                                                <input type="text" class="form-control form-control-sm option-input" data-option-id="${opt.id}" value="${opt.text}">
                                                                                            </div>
                                                                                        `).join('') || ''}
                                                        <button type="button" class="btn btn-outline-secondary btn-sm add-option mt-2" style="margin-left: 0.8rem;">
                                                            <i class="fas fa-plus"></i> Add Option
                                                        </button>
                                                    </div>
                                                    <div class="d-flex justify-content-end align-items-center mt-3">
                                                        <button class="btn btn-outline-danger btn-sm me-2 delete-element" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                        <button class="btn btn-primary btn-sm me-2 duplicate-element" title="Copy">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input required-toggle" type="checkbox" id="required-${element.id}" ${element.is_required ? 'checked' : ''}>
                                                            <label class="form-check-label" for="required-${element.id}">Required</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                        break;
                                    case 'file_upload':
                                        elementHtml = `
                                            <div class="element-card row" data-element-id="${element.id}">
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <input type="text" class="form-control element-title" value="${element.title || 'Untitled File Upload'}" placeholder="Enter title">
                                                    </div>
                                                    <div class="file-upload-settings">
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox" id="allow-specific-${element.id}" ${element.settings?.allow_specific_types ? 'checked' : ''}>
                                                            <label class="form-check-label" for="allow-specific-${element.id}">Allow only specific file types</label>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-3">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" id="pdf-${element.id}" ${element.settings?.file_types?.includes('pdf') ? 'checked' : ''}>
                                                                    <label class="form-check-label" for="pdf-${element.id}">PDF</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-3">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" id="image-${element.id}" ${element.settings?.file_types?.includes('image') ? 'checked' : ''}>
                                                                    <label class="form-check-label" for="image-${element.id}">Image</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-3">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" id="video-${element.id}" ${element.settings?.file_types?.includes('video') ? 'checked' : ''}>
                                                                    <label class="form-check-label" for="video-${element.id}">Video</label>
                                                                </div>
                                                            </div>
                                                            <div class="col-3">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox" id="audio-${element.id}" ${element.settings?.file_types?.includes('audio') ? 'checked' : ''}>
                                                                    <label class="form-check-label" for="audio-${element.id}">Audio</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="max-files-${element.id}" class="form-label">Max files:</label>
                                                            <select class="form-select" id="max-files-${element.id}">
                                                                <option value="1" ${element.settings?.max_files === 1 ? 'selected' : ''}>1</option>
                                                                <option value="5" ${element.settings?.max_files === 5 ? 'selected' : ''}>5</option>
                                                                <option value="10" ${element.settings?.max_files === 10 ? 'selected' : ''}>10</option>
                                                                <option value="unlimited" ${element.settings?.max_files === 'unlimited' ? 'selected' : ''}>Unlimited</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="max-file-size-${element.id}" class="form-label">Max file size:</label>
                                                            <select class="form-select" id="max-file-size-${element.id}">
                                                                <option value="1" ${element.settings?.max_file_size === 1 ? 'selected' : ''}>1 MB</option>
                                                                <option value="10" ${element.settings?.max_file_size === 10 ? 'selected' : ''}>10 MB</option>
                                                                <option value="100" ${element.settings?.max_file_size === 100 ? 'selected' : ''}>100 MB</option>
                                                                <option value="1000" ${element.settings?.max_file_size === 1000 ? 'selected' : ''}>1 GB</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex justify-content-end align-items-center">
                                                        <button class="btn btn-outline-danger btn-sm me-2 delete-element" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                        <button class="btn btn-primary btn-sm me-2 duplicate-element" title="Copy">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input required-toggle" type="checkbox" id="required-${element.id}" ${element.is_required ? 'checked' : ''}>
                                                            <label class="form-check-label" for="required-${element.id}">Required</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                        break;
                                }
                                $container.append(elementHtml);
                                toastr.success('Element duplicated.');
                            } else {
                                toastr.error('Failed to duplicate element.');
                                console.error('Duplicate element response:', response);
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Failed to duplicate element: ' + (xhr.responseJSON
                                ?.message || 'Unknown error.'));
                            console.error('Duplicate element error:', xhr);
                        },
                        complete: function() {
                            $('#loading-overlay').hide();
                        }
                    });
                });

                // Add Option
                $(document).on('click', '.add-option', function() {
                    if (!formBuilderId) {
                        toastr.error('Form builder not initialized.');
                        return;
                    }
                    const $card = $(this).closest('.element-card');
                    const elementId = $card.data('element-id');
                    const stepId = $card.closest('.step-card').data('step-id');
                    const $optionsContainer = $card.find('.options-container');
                    const options = $optionsContainer.find('.option-input').map(function() {
                        return {
                            id: $(this).data('option-id'),
                            text: $(this).val()
                        };
                    }).get();
                    options.push({
                        id: 'opt-' + Math.random().toString(36).substr(2, 9),
                        text: 'New Option'
                    });
                    $.ajax({
                        url: '{{ route('form-elements.options.update', ':id') }}'.replace(':id',
                            elementId),
                        method: 'PUT',
                        data: {
                            options: options
                        },
                        success: function(response) {
                            if (response.success) {
                                loadElements(stepId);
                                toastr.success('Option added.');
                            } else {
                                toastr.error('Failed to add option.');
                                console.error('Add option response:', response);
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Failed to add option: ' + (xhr.responseJSON?.message ||
                                'Unknown error.'));
                            console.error('Add option error:', xhr);
                        }
                    });
                });

                // Update Option in Real-Time
                $(document).on('change', '.option-input', function() {
                    if (!formBuilderId) {
                        toastr.error('Form builder not initialized.');
                        return;
                    }
                    const $card = $(this).closest('.element-card');
                    const elementId = $card.data('element-id');
                    const $optionsContainer = $card.find('.options-container');
                    const options = $optionsContainer.find('.option-input').map(function() {
                        return {
                            id: $(this).data('option-id'),
                            text: $(this).val()
                        };
                    }).get();
                    $.ajax({
                        url: '{{ route('form-elements.options.update', ':id') }}'.replace(':id',
                            elementId),
                        method: 'PUT',
                        data: {
                            options: options
                        },
                        success: function(response) {
                            if (response.success) {
                                toastr.success('Option updated.');
                            } else {
                                toastr.error('Failed to update option.');
                                console.error('Update option response:', response);
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Failed to update option: ' + (xhr.responseJSON?.message ||
                                'Unknown error.'));
                            console.error('Update option error:', xhr);
                        }
                    });
                });

                // Update File Upload Settings
                $(document).on('change', '.file-upload-settings input, .file-upload-settings select', function() {
                    if (!formBuilderId) {
                        toastr.error('Form builder not initialized.');
                        return;
                    }
                    const $card = $(this).closest('.element-card');
                    const elementId = $card.data('element-id');
                    const settings = {
                        allow_specific_types: $card.find(`#allow-specific-${elementId}`).is(':checked'),
                        file_types: $card.find(
                                `#pdf-${elementId}, #image-${elementId}, #video-${elementId}, #audio-${elementId}`
                            )
                            .filter(':checked').map(function() {
                                return this.id.split('-')[0];
                            }).get(),
                        max_files: $card.find(`#max-files-${elementId}`).val(),
                        max_file_size: parseInt($card.find(`#max-file-size-${elementId}`).val())
                    };
                    $.ajax({
                        url: '{{ route('form-elements.file-settings.update', ':id') }}'.replace(':id',
                            elementId),
                        method: 'PUT',
                        data: settings,
                        success: function(response) {
                            if (response.success) {
                                toastr.success('File upload settings updated.');
                            } else {
                                toastr.error('Failed to update file upload settings.');
                                console.error('Update file settings response:', response);
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Failed to update file upload settings: ' + (xhr
                                .responseJSON?.message || 'Unknown error.'));
                            console.error('Update file settings error:', xhr);
                        }
                    });
                });

                // Initialize
                initFormBuilder();
            });
        </script>
    </body>

    </html>
