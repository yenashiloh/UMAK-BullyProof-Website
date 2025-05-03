$(document).ready(function() {
    // Global counter for new elements
    let elementCounter = 0;
    let stepCounter = 1;
    let currentFormId = null;

    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();

    // Create a new form
    $('#createFormBtn').click(function() {
        const title = $('#formTitle').val();
        const description = $('#formDescription').val();
        
        $.ajax({
            url: '/admin/content/create-form',
            type: 'POST',
            data: {
                title: title,
                description: description,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    currentFormId = response.form._id;
                    $('#formBuilderContainer').removeClass('d-none');
                    $('#createFormContainer').addClass('d-none');
                    
                    // Set form title
                    $('#formBuilderTitle').text(response.form.title);
                    
                    // Initialize first step
                    stepCounter = response.form.steps.length;
                    renderSteps(response.form.steps);
                    
                    // Show success message
                    showAlert('success', 'Form created successfully!');
                }
            },
            error: function(error) {
                showAlert('danger', 'Error creating form: ' + error.responseJSON.message);
            }
        });
    });

    // Load existing form
    function loadFormBuilder(formId) {
        $.ajax({
            url: '/admin/content/get-form/' + formId,
            type: 'GET',
            success: function(response) {
                currentFormId = response.form._id;
                
                // Set form title
                $('#formBuilderTitle').text(response.form.title);
                
                // Render steps
                renderSteps(response.form.steps);
                
                // Render elements
                response.elements.forEach(element => {
                    renderElement(element);
                });
                
                $('#formBuilderContainer').removeClass('d-none');
                $('#formsListContainer').addClass('d-none');
            },
            error: function(error) {
                showAlert('danger', 'Error loading form: ' + error.responseJSON.message);
            }
        });
    }

    // Render steps
    function renderSteps(steps) {
        $('#stepsContainer').empty();
        
        steps.forEach((step, index) => {
            const stepHtml = `
                <div class="bg-secondary text-white p-2 mb-4" data-step-id="${step.id}">
                    <span>Step ${index + 1} of ${steps.length}</span>
                </div>
                <div class="step-content" data-step-id="${step.id}"></div>
            `;
            
            $('#stepsContainer').append(stepHtml);
        });
    }

    // Add new step
    $('#addStepBtn').click(function() {
        $.ajax({
            url: '/admin/content/add-step/' + currentFormId,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    stepCounter++;
                    
                    // Refresh steps view
                    $.ajax({
                        url: '/admin/content/get-form/' + currentFormId,
                        type: 'GET',
                        success: function(response) {
                            renderSteps(response.form.steps);
                            showAlert('success', 'Step added successfully!');
                        }
                    });
                }
            },
            error: function(error) {
                showAlert('danger', 'Error adding step: ' + error.responseJSON.message);
            }
        });
    });

    // Add new element
    $(document).on('click', '.add-content-btn', function() {
        const stepId = $(this).closest('.row').prev('.step-content').data('step-id');
        
        // Show element type selection modal
        $('#elementTypeModal').modal('show');
        $('#elementTypeModal').data('step-id', stepId);
    });

    // Element type selection
    $('.element-type-btn').click(function() {
        const elementType = $(this).data('element-type');
        const stepId = $('#elementTypeModal').data('step-id');
        
        // Close the modal
        $('#elementTypeModal').modal('hide');
        
        // Calculate position (count existing elements + 1)
        const position = $('.step-content[data-step-id="' + stepId + '"]').children().length + 1;
        
        // Add the element
        addElement(stepId, elementType, position);
    });

    // Add element function
    function addElement(stepId, elementType, position) {
        $.ajax({
            url: '/admin/content/add-element',
            type: 'POST',
            data: {
                form_builder_id: currentFormId,
                step_id: stepId,
                element_type: elementType,
                position: position,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    renderElement(response.element);
                    showAlert('success', 'Element added successfully!');
                }
            },
            error: function(error) {
                showAlert('danger', 'Error adding element: ' + error.responseJSON.message);
            }
        });
    }

    // Render element based on type
    function renderElement(element) {
        let elementHtml = '';
        const elementId = element._id;
        const stepId = element.step_id;
        
        // Common element wrapper start
        elementHtml += `
            <div class="row mb-4 border p-3 element-container" data-element-id="${elementId}" data-element-type="${element.element_type}">
                <div class="col-md-11">
                    <div class="mb-3">
                        <div class="input-group">
                            <input type="text" class="form-control element-title" value="${element.title || 'Add Section Title'}" 
                                placeholder="Add Section Title" data-element-id="${elementId}">
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle element-type-dropdown" 
                                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
        `;
        
        // Element type specific icon and label
        switch(element.element_type) {
            case 'section':
                elementHtml += `<i class="fas fa-heading"></i> Section`;
                break;
            case 'paragraph':
                elementHtml += `<i class="fas fa-align-left"></i> Paragraph`;
                break;
            case 'multiple_choice':
                elementHtml += `<i class="fas fa-list-ul"></i> Multiple Choice`;
                break;
            case 'checkbox':
                elementHtml += `<i class="fas fa-check-square"></i> Checkbox`;
                break;
            case 'dropdown':
                elementHtml += `<i class="fas fa-caret-down"></i> Dropdown`;
                break;
            case 'file_upload':
                elementHtml += `<i class="fas fa-upload"></i> File Upload`;
                break;
        }
        
        // Common dropdown menu
        elementHtml += `
                                </button>
                                <ul class="dropdown-menu element-type-menu" aria-labelledby="dropdownMenuButton">
                                    <li><a class="dropdown-item change-element-type" href="#" data-element-id="${elementId}" data-type="section"><i class="fas fa-heading"></i> Section</a></li>
                                    <li><a class="dropdown-item change-element-type" href="#" data-element-id="${elementId}" data-type="paragraph"><i class="fas fa-align-left"></i> Paragraph</a></li>
                                    <li><a class="dropdown-item change-element-type" href="#" data-element-id="${elementId}" data-type="multiple_choice"><i class="fas fa-list-ul"></i> Multiple Choice</a></li>
                                    <li><a class="dropdown-item change-element-type" href="#" data-element-id="${elementId}" data-type="checkbox"><i class="fas fa-check-square"></i> Checkbox</a></li>
                                    <li><a class="dropdown-item change-element-type" href="#" data-element-id="${elementId}" data-type="dropdown"><i class="fas fa-caret-down"></i> Dropdown</a></li>
                                    <li><a class="dropdown-item change-element-type" href="#" data-element-id="${elementId}" data-type="file_upload"><i class="fas fa-upload"></i> File Upload</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="btn-group mb-3" role="group">
                        <button type="button" class="btn btn-outline-secondary text-formatting-btn"><strong>B</strong></button>
                        <button type="button" class="btn btn-outline-secondary text-formatting-btn"><em>I</em></button>
                        <button type="button" class="btn btn-outline-secondary text-formatting-btn"><u>U</u></button>
                    </div>
        `;
        
        // Element type specific content
        switch(element.element_type) {
            case 'section':
                elementHtml += `
                    <div class="mb-3">
                        <input type="text" class="form-control" id="title-${elementId}"
                            placeholder="Add Section Title" value="${element.title || ''}">
                    </div>
                `;
                break;
                
            case 'paragraph':
                elementHtml += `
                    <div class="mb-3">
                        <label class="form-label fw-bold">Enter your paragraph:</label>
                        <textarea class="form-control element-content" rows="4" 
                            placeholder="Write your response here..." data-element-id="${elementId}">${element.content || ''}</textarea>
                    </div>
                `;
                break;
                
            case 'multiple_choice':
                elementHtml += `<div class="options-container mb-3" data-element-id="${elementId}">`;
                
                if (element.options && element.options.length > 0) {
                    element.options.forEach((option, index) => {
                        elementHtml += `
                            <div class="form-check d-flex align-items-center option-item mb-1">
                                <input class="form-check-input me-2" type="radio" name="multipleChoice-${elementId}" id="choice-${option.id}" disabled>
                                <input type="text" class="form-control form-control-sm border-0 border-bottom w-50 option-text" 
                                    value="${option.text}" placeholder="Option ${index + 1}" 
                                    data-element-id="${elementId}" data-option-id="${option.id}">
                                <button class="btn btn-sm text-danger ms-2 remove-option-btn" 
                                    data-element-id="${elementId}" data-option-id="${option.id}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        `;
                    });
                } else {
                    // Default options if none exist
                    for (let i = 1; i <= 3; i++) {
                        const optionId = 'opt-' + Date.now() + '-' + i;
                        elementHtml += `
                            <div class="form-check d-flex align-items-center option-item mb-1">
                                <input class="form-check-input me-2" type="radio" name="multipleChoice-${elementId}" id="choice-${optionId}" disabled>
                                <input type="text" class="form-control form-control-sm border-0 border-bottom w-50 option-text" 
                                    value="Option ${i}" placeholder="Option ${i}" 
                                    data-element-id="${elementId}" data-option-id="${optionId}">
                                <button class="btn btn-sm text-danger ms-2 remove-option-btn" 
                                    data-element-id="${elementId}" data-option-id="${optionId}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        `;
                    }
                }
                
                elementHtml += `
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm mt-2 add-option-btn" data-element-id="${elementId}">
                        <i class="fas fa-plus"></i> Add Option
                    </button>
                `;
                break;
                
            case 'checkbox':
                elementHtml += `<div class="options-container mb-3" data-element-id="${elementId}">`;
                
                if (element.options && element.options.length > 0) {
                    element.options.forEach((option, index) => {
                        elementHtml += `
                            <div class="form-check d-flex align-items-center option-item mb-1">
                                <input class="form-check-input me-2" type="checkbox" id="checkbox-${option.id}" disabled>
                                <input type="text" class="form-control form-control-sm border-0 border-bottom w-50 option-text" 
                                    value="${option.text}" placeholder="Option ${index + 1}" 
                                    data-element-id="${elementId}" data-option-id="${option.id}">
                                <button class="btn btn-sm text-danger ms-2 remove-option-btn" 
                                    data-element-id="${elementId}" data-option-id="${option.id}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        `;
                    });
                } else {
                    // Default options if none exist
                    for (let i = 1; i <= 3; i++) {
                        const optionId = 'opt-' + Date.now() + '-' + i;
                        elementHtml += `
                            <div class="form-check d-flex align-items-center option-item mb-1">
                                <input class="form-check-input me-2" type="checkbox" id="checkbox-${optionId}" disabled>
                                <input type="text" class="form-control form-control-sm border-0 border-bottom w-50 option-text" 
                                    value="Option ${i}" placeholder="Option ${i}" 
                                    data-element-id="${elementId}" data-option-id="${optionId}">
                                <button class="btn btn-sm text-danger ms-2 remove-option-btn" 
                                    data-element-id="${elementId}" data-option-id="${optionId}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        `;
                    }
                }
                
                elementHtml += `
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm mt-2 add-option-btn" data-element-id="${elementId}">
                        <i class="fas fa-plus"></i> Add Option
                    </button>
                `;
                break;
                
            case 'dropdown':
                elementHtml += `<div class="options-container mb-3" data-element-id="${elementId}">`;
                
                if (element.options && element.options.length > 0) {
                    element.options.forEach((option, index) => {
                        elementHtml += `
                            <div class="d-flex align-items-center option-item mb-1">
                                <span class="me-2">${index + 1}.</span>
                                <input type="text" class="form-control form-control-sm border-0 border-bottom w-50 option-text" 
                                    value="${option.text}" placeholder="Option ${index + 1}" 
                                    data-element-id="${elementId}" data-option-id="${option.id}">
                                <button class="btn btn-sm text-danger ms-2 remove-option-btn" 
                                    data-element-id="${elementId}" data-option-id="${option.id}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        `;
                    });
                } else {
                    // Default options if none exist
                    for (let i = 1; i <= 3; i++) {
                        const optionId = 'opt-' + Date.now() + '-' + i;
                        elementHtml += `
                            <div class="d-flex align-items-center option-item mb-1">
                                <span class="me-2">${i}.</span>
                                <input type="text" class="form-control form-control-sm border-0 border-bottom w-50 option-text" 
                                    value="Option ${i}" placeholder="Option ${i}" 
                                    data-element-id="${elementId}" data-option-id="${optionId}">
                                <button class="btn btn-sm text-danger ms-2 remove-option-btn" 
                                    data-element-id="${elementId}" data-option-id="${optionId}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        `;
                    }
                }
                
                elementHtml += `
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm mt-2 add-option-btn" data-element-id="${elementId}">
                        <i class="fas fa-plus"></i> Add Option
                    </button>
                `;
                break;
                
            case 'file_upload':
                // Set default values if not provided
                const settings = element.settings || {
                    allow_specific_types: true,
                    file_types: [],
                    max_files: 1,
                    max_file_size: 10
                };
                
                elementHtml += `
                    <div class="file-upload-settings">
                        <!-- Allow specific file types -->
                        <div class="d-flex align-items-center mb-2">
                            <label class="form-check-label mb-0 me-2" for="allowSpecificTypes-${elementId}">Allow only specific file types</label>
                            <div class="form-check form-switch" style="margin: 0; padding: 0;">
                                <input class="form-check-input allow-specific-types" type="checkbox" id="allowSpecificTypes-${elementId}" 
                                    data-element-id="${elementId}" ${settings.allow_specific_types ? 'checked' : ''}>
                            </div>
                        </div>

                        <!-- File types checkboxes -->
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <div class="form-check">
                                    <input class="form-check-input file-type-checkbox" type="checkbox" id="pdfType-${elementId}" 
                                        data-element-id="${elementId}" data-type="pdf" ${settings.file_types.includes('pdf') ? 'checked' : ''}>
                                    <label class="form-check-label" for="pdfType-${elementId}">PDF</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input file-type-checkbox" type="checkbox" id="videoType-${elementId}" 
                                        data-element-id="${elementId}" data-type="video" ${settings.file_types.includes('video') ? 'checked' : ''}>
                                    <label class="form-check-label" for="videoType-${elementId}">Video</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-check">
                                    <input class="form-check-input file-type-checkbox" type="checkbox" id="imageType-${elementId}" 
                                        data-element-id="${elementId}" data-type="image" ${settings.file_types.includes('image') ? 'checked' : ''}>
                                    <label class="form-check-label" for="imageType-${elementId}">Image</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input file-type-checkbox" type="checkbox" id="audioType-${elementId}" 
                                        data-element-id="${elementId}" data-type="audio" ${settings.file_types.includes('audio') ? 'checked' : ''}>
                                    <label class="form-check-label" for="audioType-${elementId}">Audio</label>
                                </div>
                            </div>
                        </div>

                        <!-- Maximum number of files -->
                        <div class="mb-3 d-flex align-items-center">
                            <label for="maxFiles-${elementId}" class="form-label me-3" style="width: 200px;">Maximum number of files:</label>
                            <select class="form-select w-auto max-files-select" id="maxFiles-${elementId}" data-element-id="${elementId}">
                                <option value="1" ${settings.max_files === 1 ? 'selected' : ''}>1</option>
                                <option value="5" ${settings.max_files === 5 ? 'selected' : ''}>5</option>
                                <option value="10" ${settings.max_files === 10 ? 'selected' : ''}>10</option>
                                <option value="-1" ${settings.max_files === -1 ? 'selected' : ''}>Unlimited</option>
                            </select>
                        </div>

                        <!-- Maximum file size -->
                        <div class="mb-3 d-flex align-items-center">
                            <label for="maxFileSize-${elementId}" class="form-label me-3" style="width: 200px;">Maximum file size:</label>
                            <select class="form-select w-auto max-file-size-select" id="maxFileSize-${elementId}" data-element-id="${elementId}">
                                <option value="1" ${settings.max_file_size === 1 ? 'selected' : ''}>1 MB</option>
                                <option value="10" ${settings.max_file_size === 10 ? 'selected' : ''}>10 MB</option>
                                <option value="100" ${settings.max_file_size === 100 ? 'selected' : ''}>100 MB</option>
                                <option value="1000" ${settings.max_file_size === 1000 ? 'selected' : ''}>1 GB</option>
                            </select>
                        </div>
                    </div>
                `;
                break;
        }

        // Common footer for all elements
        elementHtml += `
                    <hr>
                    <div class="text-end d-flex justify-content-end align-items-center">
                        <button class="btn btn-outline-danger btn-sm me-2 delete-element-btn" title="Delete" data-element-id="${elementId}">
                            <i class="fas fa-trash"></i>
                        </button>
                        <button class="btn btn-primary btn-sm me-3 duplicate-element-btn" title="Copy" data-element-id="${elementId}">
                            <i class="fas fa-copy"></i>
                        </button>

                        <span class="me-3">|</span>

                        <div class="form-check form-switch d-flex align-items-center">
                            <label class="form-check-label small me-2" for="requiredToggle-${elementId}" style="margin-bottom: 0;">Required</label>
                            <div>
                                <input class="form-check-input form-check-input-sm required-toggle" type="checkbox" 
                                    id="requiredToggle-${elementId}" style="width: 2rem; height: 1rem;" 
                                    data-element-id="${elementId}" ${element.is_required ? 'checked' : ''}>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column (Vertical Buttons) -->
                <div class="col-md-1 d-flex justify-content-end">
                    <div class="d-flex flex-column">
                        <button class="btn btn-primary btn-sm mb-1 add-content-btn" title="Add content">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button class="btn btn-primary btn-sm mb-1 text-formatting-btn" title="Text formatting">
                            <i class="fas fa-font"></i>
                        </button>
                        <button class="btn btn-primary btn-sm mb-1 add-step-btn" title="Another Step">
                            <i class="fas fa-columns"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        // Append to the appropriate step
        $('.step-content[data-step-id="' + stepId + '"]').append(elementHtml);
    }

    // Update element title
    $(document).on('change', '.element-title', function() {
        const elementId = $(this).data('element-id');
        const title = $(this).val();
        
        updateElement(elementId, { title: title });
    });

    // Update element content (paragraph)
    $(document).on('change', '.element-content', function() {
        const elementId = $(this).data('element-id');
        const content = $(this).val();
        
        updateElement(elementId, { content: content });
    });

    // Update required toggle
    $(document).on('change', '.required-toggle', function() {
        const elementId = $(this).data('element-id');
        const isRequired = $(this).prop('checked');
        
        updateElement(elementId, { is_required: isRequired });
    });

    // Delete element
    $(document).on('click', '.delete-element-btn', function() {
        const elementId = $(this).data('element-id');
        
        if (confirm('Are you sure you want to delete this element?')) {
            $.ajax({
                url: '/admin/content/delete-element/' + elementId,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        // Remove element from DOM
                        $('[data-element-id="' + elementId + '"].element-container').remove();
                        showAlert('success', 'Element deleted successfully!');
                    }
                },
                error: function(error) {
                    showAlert('danger', 'Error deleting element: ' + error.responseJSON.message);
                }
            });
        }
    });

    // Duplicate element
    $(document).on('click', '.duplicate-element-btn', function() {
        const elementId = $(this).data('element-id');
        
        $.ajax({
            url: '/admin/content/duplicate-element/' + elementId,
            type: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    renderElement(response.element);
                    showAlert('success', 'Element duplicated successfully!');
                }
            },
            error: function(error) {
                showAlert('danger', 'Error duplicating element: ' + error.responseJSON.message);
            }
        });
    });

    // Add option to multiple choice, checkbox, dropdown
    $(document).on('click', '.add-option-btn', function() {
        const elementId = $(this).data('element-id');
        const optionId = 'opt-' + Date.now();
        const $optionsContainer = $('.options-container[data-element-id="' + elementId + '"]');
        const elementType = $optionsContainer.closest('.element-container').data('element-type');
        const optionCount = $optionsContainer.children().length + 1;
        
        let optionHtml = '';
        
        if (elementType === 'multiple_choice') {
            optionHtml = `
                <div class="form-check d-flex align-items-center option-item mb-1">
                    <input class="form-check-input me-2" type="radio" name="multipleChoice-${elementId}" id="choice-${optionId}" disabled>
                    <input type="text" class="form-control form-control-sm border-0 border-bottom w-50 option-text" 
                        value="Option ${optionCount}" placeholder="Option ${optionCount}" 
                        data-element-id="${elementId}" data-option-id="${optionId}">
                    <button class="btn btn-sm text-danger ms-2 remove-option-btn" 
                        data-element-id="${elementId}" data-option-id="${optionId}">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        } else if (elementType === 'checkbox') {
            optionHtml = `
                <div class="form-check d-flex align-items-center option-item mb-1">
                    <input class="form-check-input me-2" type="checkbox" id="checkbox-${optionId}" disabled>
                    <input type="text" class="form-control form-control-sm border-0 border-bottom w-50 option-text" 
                        value="Option ${optionCount}" placeholder="Option ${optionCount}" 
                        data-element-id="${elementId}" data-option-id="${optionId}">
                    <button class="btn btn-sm text-danger ms-2 remove-option-btn" 
                        data-element-id="${elementId}" data-option-id="${optionId}">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        } else if (elementType === 'dropdown') {
            optionHtml = `
                <div class="d-flex align-items-center option-item mb-1">
                    <span class="me-2">${optionCount}.</span>
                    <input type="text" class="form-control form-control-sm border-0 border-bottom w-50 option-text" 
                        value="Option ${optionCount}" placeholder="Option ${optionCount}" 
                        data-element-id="${elementId}" data-option-id="${optionId}">
                    <button class="btn btn-sm text-danger ms-2 remove-option-btn" 
                        data-element-id="${elementId}" data-option-id="${optionId}
                        ">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        }
        
        $optionsContainer.append(optionHtml);
        
        // Update element options in database
        updateElementOptions(elementId);
    });

    // Remove option
    $(document).on('click', '.remove-option-btn', function() {
        const elementId = $(this).data('element-id');
        const optionId = $(this).data('option-id');
        
        // Remove option from DOM
        $(this).closest('.option-item').remove();
        
        // Update element options in database
        updateElementOptions(elementId);
    });

    // Update option text
    $(document).on('change', '.option-text', function() {
        const elementId = $(this).data('element-id');
        
        // Update element options in database
        updateElementOptions(elementId);
    });

    // File upload settings
    // Toggle allow specific file types
    $(document).on('change', '.allow-specific-types', function() {
        const elementId = $(this).data('element-id');
        const allowSpecificTypes = $(this).prop('checked');
        
        // Update file upload settings
        updateFileUploadSettings(elementId);
    });

    // File type checkboxes
    $(document).on('change', '.file-type-checkbox', function() {
        const elementId = $(this).data('element-id');
        
        // Update file upload settings
        updateFileUploadSettings(elementId);
    });

    // Maximum number of files
    $(document).on('change', '.max-files-select', function() {
        const elementId = $(this).data('element-id');
        
        // Update file upload settings
        updateFileUploadSettings(elementId);
    });

    // Maximum file size
    $(document).on('change', '.max-file-size-select', function() {
        const elementId = $(this).data('element-id');
        
        // Update file upload settings
        updateFileUploadSettings(elementId);
    });

    // Change element type
    $(document).on('click', '.change-element-type', function(e) {
        e.preventDefault();
        const elementId = $(this).data('element-id');
        const newType = $(this).data('type');
        
        $.ajax({
            url: '/admin/content/update-element/' + elementId,
            type: 'PUT',
            data: {
                element_type: newType,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Replace the element with the new type
                    $('[data-element-id="' + elementId + '"].element-container').replaceWith('');
                    renderElement(response.element);
                    showAlert('success', 'Element type changed successfully!');
                }
            },
            error: function(error) {
                showAlert('danger', 'Error changing element type: ' + error.responseJSON.message);
            }
        });
    });

    // Update element function
    function updateElement(elementId, data) {
        $.ajax({
            url: '/admin/content/update-element/' + elementId,
            type: 'PUT',
            data: {
                ...data,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // No need to update DOM since we're just updating properties
                }
            },
            error: function(error) {
                showAlert('danger', 'Error updating element: ' + error.responseJSON.message);
            }
        });
    }

    // Update element options function
    function updateElementOptions(elementId) {
        const options = [];
        
        // Get all options for element
        $('.option-text[data-element-id="' + elementId + '"]').each(function() {
            options.push({
                id: $(this).data('option-id'),
                text: $(this).val()
            });
        });
        
        $.ajax({
            url: '/admin/content/update-element-options/' + elementId,
            type: 'PUT',
            data: {
                options: options,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // No need to update DOM since we're just updating options
                }
            },
            error: function(error) {
                showAlert('danger', 'Error updating options: ' + error.responseJSON.message);
            }
        });
    }

    // Update file upload settings function
    function updateFileUploadSettings(elementId) {
        const allowSpecificTypes = $('#allowSpecificTypes-' + elementId).prop('checked');
        const fileTypes = [];
        
        // Collect selected file types
        if (allowSpecificTypes) {
            $('.file-type-checkbox[data-element-id="' + elementId + '"]:checked').each(function() {
                fileTypes.push($(this).data('type'));
            });
        }
        
        const maxFiles = parseInt($('#maxFiles-' + elementId).val());
        const maxFileSize = parseInt($('#maxFileSize-' + elementId).val());
        
        $.ajax({
            url: '/admin/content/update-file-settings/' + elementId,
            type: 'PUT',
            data: {
                settings: {
                    allow_specific_types: allowSpecificTypes,
                    file_types: fileTypes,
                    max_files: maxFiles,
                    max_file_size: maxFileSize
                },
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // No need to update DOM since we're just updating settings
                }
            },
            error: function(error) {
                showAlert('danger', 'Error updating file upload settings: ' + error.responseJSON.message);
            }
        });
    }

    // Save form
    $('#saveFormBtn').click(function() {
        showAlert('success', 'Form saved successfully!');
    });

    // Publish form
    $('#publishFormBtn').click(function() {
        $.ajax({
            url: '/admin/content/publish-form/' + currentFormId,
            type: 'PUT',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', 'Form published successfully!');
                }
            },
            error: function(error) {
                showAlert('danger', 'Error publishing form: ' + error.responseJSON.message);
            }
        });
    });

    // Preview form
    $('#previewFormBtn').click(function() {
        const previewUrl = '/admin/content/preview-form/' + currentFormId;
        window.open(previewUrl, '_blank');
    });

    // Back to forms list
    $('#backToFormsBtn').click(function() {
        $('#formBuilderContainer').addClass('d-none');
        $('#formsListContainer').removeClass('d-none');
    });

    // Show alert message
    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        $('#alertContainer').html(alertHtml);
        
        // Auto dismiss after 3 seconds
        setTimeout(function() {
            $('.alert').alert('close');
        }, 3000);
    }

    // Text formatting buttons functionality
    $(document).on('click', '.text-formatting-btn', function() {
        // Get closest content editable or textarea
        const $editor = $(this).closest('.element-container').find('.element-content');
        
        if ($editor.length) {
            // Check which button was clicked
            if ($(this).find('strong').length) {
                // Bold
                wrapSelectedText($editor[0], '<strong>', '</strong>');
            } else if ($(this).find('em').length) {
                // Italic
                wrapSelectedText($editor[0], '<em>', '</em>');
            } else if ($(this).find('u').length) {
                // Underline
                wrapSelectedText($editor[0], '<u>', '</u>');
            }
            
            // Update element content
            $editor.trigger('change');
        }
    });

    // Helper function to wrap selected text with tags
    function wrapSelectedText(input, openTag, closeTag) {
        if (input.selectionStart !== undefined) {
            const startPos = input.selectionStart;
            const endPos = input.selectionEnd;
            const selectedText = input.value.substring(startPos, endPos);
            const newText = input.value.substring(0, startPos) + 
                            openTag + selectedText + closeTag + 
                            input.value.substring(endPos);
            
            input.value = newText;
            
            // Set new cursor position after inserted tags
            input.selectionStart = endPos + openTag.length + closeTag.length;
            input.selectionEnd = input.selectionStart;
            input.focus();
        }
    }

    // Load forms list on page load
    function loadFormsList() {
        $.ajax({
            url: '/admin/content/get-forms',
            type: 'GET',
            success: function(response) {
                if (response.forms && response.forms.length > 0) {
                    const $formsList = $('#formsList');
                    $formsList.empty();
                    
                    response.forms.forEach(form => {
                        const formHtml = `
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">${form.title}</h5>
                                    <p class="card-text">${form.description}</p>
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <span class="badge ${form.status === 'published' ? 'bg-success' : 'bg-secondary'}">${form.status}</span>
                                            <small class="text-muted ms-2">Created: ${new Date(form.created_at).toLocaleDateString()}</small>
                                        </div>
                                        <div>
                                            <button class="btn btn-sm btn-primary edit-form-btn" data-form-id="${form._id}">Edit</button>
                                            <button class="btn btn-sm btn-danger delete-form-btn" data-form-id="${form._id}">Delete</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        $formsList.append(formHtml);
                    });
                } else {
                    $('#formsList').html('<p class="text-center">No forms found. Create your first form!</p>');
                }
            },
            error: function(error) {
                showAlert('danger', 'Error loading forms: ' + error.responseJSON.message);
            }
        });
    }

    // Edit form
    $(document).on('click', '.edit-form-btn', function() {
        const formId = $(this).data('form-id');
        loadFormBuilder(formId);
    });

    // Delete form
    $(document).on('click', '.delete-form-btn', function() {
        const formId = $(this).data('form-id');
        
        if (confirm('Are you sure you want to delete this form? This action cannot be undone.')) {
            $.ajax({
                url: '/admin/content/delete-form/' + formId,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        loadFormsList();
                        showAlert('success', 'Form deleted successfully!');
                    }
                },
                error: function(error) {
                    showAlert('danger', 'Error deleting form: ' + error.responseJSON.message);
                }
            });
        }
    });

    // Initialize forms list
    loadFormsList();
});