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
                                @foreach ($formBuilderData['steps'] as $index => $step)
                                    <li class="nav-item">
                                        <a class="nav-link {{ $index == 0 ? 'active' : '' }}"
                                            id="line-tab-{{ $step['id'] }}" data-bs-toggle="pill"
                                            href="#line-content-{{ $step['id'] }}" role="tab"
                                            aria-controls="line-content-{{ $step['id'] }}"
                                            aria-selected="{{ $index == 0 ? 'true' : 'false' }}">
                                            {{ $step['title'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>

        
                            <!-- Tab content for steps -->
                            <div class="tab-content mt-3" id="myTabContent">
                                @foreach ($formBuilderData['steps'] as $index => $step)
                                    <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}"
                                        id="line-content-{{ $step['id'] }}" role="tabpanel"
                                        aria-labelledby="line-tab-{{ $step['id'] }}">
                                        <div class="row">
                                            @if (isset($formElements[$step['id']]))
                                                @foreach ($formElements[$step['id']] as $element)
                                                    <div class="col-md-6 mb-2">
                                                        <label
                                                            class="mb-2 mt-2"><strong>{{ $element['title'] ?? '' }}</strong></label>
                                                        @php
                                                            $elementId = $element['_id'] ?? '';
                                                            $elementType = $element['element_type'] ?? '';
                                                            $value = '';
                                                            $displayValue = '';

                                                            // Get the step data for this step
                                                            $stepData = $formDataArray['steps_data'][$step['id']] ?? [];

                                                            // Check if there's a value for this element ID in the step data
                                                            if (!empty($elementId) && is_array($stepData)) {
                                                                // Convert $elementId to string to match the keys in $stepData
                                                                $elementIdStr =
                                                                    is_array($elementId) && isset($elementId['$oid'])
                                                                        ? $elementId['$oid']
                                                                        : (string) $elementId;

                                                                if (array_key_exists($elementIdStr, $stepData)) {
                                                                    $rawValue = $stepData[$elementIdStr];

                                                                    // For file_upload: Handle array of file paths
                                                                    if ($elementType == 'file_upload') {
                                                                        if (is_array($rawValue) && !empty($rawValue)) {
                                                                            $value = $rawValue[0]; // Take the first file path if it's an array
                                                                        } elseif (is_string($rawValue)) {
                                                                            $value = $rawValue;
                                                                        }
                                                                        $displayValue = $value;
                                                                    }
                                                                    // For paragraph or textarea: Handle as a string
                                                                    elseif (
                                                                        $elementType == 'paragraph' ||
                                                                        $elementType == 'textarea'
                                                                    ) {
                                                                        $value = is_array($rawValue)
                                                                            ? implode(', ', $rawValue)
                                                                            : $rawValue;
                                                                        $displayValue = $value;
                                                                    }
                                                                    // For dropdown (single selection)
                                                                    elseif ($elementType == 'dropdown') {
                                                                        $value = $rawValue;
                                                                        if (
                                                                            is_string($rawValue) &&
                                                                            isset($element['options'])
                                                                        ) {
                                                                            $options = is_string($element['options'])
                                                                                ? (json_decode(
                                                                                    $element['options'],
                                                                                    true,
                                                                                ) ?:
                                                                                [])
                                                                                : $element['options'];
                                                                            if (is_array($options)) {
                                                                                foreach ($options as $option) {
                                                                                    if (
                                                                                        is_array($option) &&
                                                                                        isset($option['id']) &&
                                                                                        $option['id'] == $rawValue
                                                                                    ) {
                                                                                        $displayValue =
                                                                                            $option['text'] ??
                                                                                            $rawValue;
                                                                                        break;
                                                                                    }
                                                                                }
                                                                            }
                                                                            if (empty($displayValue)) {
                                                                                $displayValue = $rawValue;
                                                                            }
                                                                        } else {
                                                                            $displayValue = $rawValue;
                                                                        }
                                                                    }
                                                                    // For checkbox or multiple_choice (multiple selections)
                                                                    elseif (
                                                                        $elementType == 'checkbox' ||
                                                                        $elementType == 'multiple_choice'
                                                                    ) {
                                                                        if (is_array($rawValue)) {
                                                                            $value = implode(', ', $rawValue); // Store the raw IDs
                                                                            $textValues = [];
                                                                            if (isset($element['options'])) {
                                                                                $options = is_string(
                                                                                    $element['options'],
                                                                                )
                                                                                    ? (json_decode(
                                                                                        $element['options'],
                                                                                        true,
                                                                                    ) ?:
                                                                                    [])
                                                                                    : $element['options'];
                                                                                if (is_array($options)) {
                                                                                    foreach ($rawValue as $optionId) {
                                                                                        $found = false;
                                                                                        foreach ($options as $option) {
                                                                                            if (
                                                                                                is_array($option) &&
                                                                                                isset($option['id']) &&
                                                                                                $option['id'] ==
                                                                                                    $optionId
                                                                                            ) {
                                                                                                $textValues[] =
                                                                                                    $option['text'] ??
                                                                                                    $optionId;
                                                                                                $found = true;
                                                                                                break;
                                                                                            }
                                                                                        }
                                                                                        if (!$found) {
                                                                                            $textValues[] = $optionId;
                                                                                        }
                                                                                    }
                                                                                } else {
                                                                                    $textValues = $rawValue;
                                                                                }
                                                                            } else {
                                                                                $textValues = $rawValue;
                                                                            }
                                                                            $displayValue = !empty($textValues)
                                                                                ? implode(', ', $textValues)
                                                                                : $value;
                                                                        } else {
                                                                            $value = $rawValue;
                                                                            $displayValue = $rawValue;
                                                                        }
                                                                    }
                                                                    // Default handling for other element types
                                                                    else {
                                                                        $value = is_array($rawValue)
                                                                            ? implode(', ', $rawValue)
                                                                            : $rawValue;
                                                                        $displayValue = $value;
                                                                    }
                                                                }
                                                            }
                                                        @endphp

                                                        @if ($element['element_type'] == 'file_upload')
                                                            <div class="form-control bg-light">
                                                                @if (!empty($value) && filter_var($value, FILTER_VALIDATE_URL))
                                                                    <a href="{{ $value }}" target="_blank">View
                                                                        File</a>
                                                                @elseif (!empty($value))
                                                                    {{ $value }} (File path)
                                                                @else
                                                                    No file uploaded
                                                                @endif
                                                            </div>
                                                        @elseif ($element['element_type'] == 'paragraph' || $elementType == 'textarea')
                                                            <input type="text" class="form-control"
                                                                value="{{ $displayValue }}" readonly>
                                                        @else
                                                            <input type="text" class="form-control"
                                                                value="{{ $displayValue }}" readonly>
                                                            @if (
                                                                $element['element_type'] == 'checkbox' ||
                                                                    $element['element_type'] == 'dropdown' ||
                                                                    $element['element_type'] == 'multiple_choice')
                                                                @if ($value != $displayValue && !empty($value))
                                                                    {{-- <small class="text-muted">Selected ID(s): {{ $value }}</small> --}}
                                                                @endif
                                                            @endif
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="col-12">
                                                    <div class="alert alert-info">No form elements found for this step.
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- <div class="mt-3 p-3 border rounded bg-light">
                                    <h6>Debug Information:</h6>
                                    <pre>Step ID: {{ $step['id'] }}
                                    Available Step Data: {{ json_encode($formDataArray['steps_data'][$step['id']] ?? [], JSON_PRETTY_PRINT) }}</pre>
                        
                                    <h6 class="mt-3">Elements Debug:</h6>
                                    @if (isset($formElements[$step['id']]))
                                        @foreach ($formElements[$step['id']] as $element)
                                            <div class="mb-2">
                                                <strong>{{ $element['title'] ?? 'Unnamed Element' }} ({{ $element['element_type'] ?? 'unknown' }}):</strong>
                                                <pre>{{ json_encode($element, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                        @endforeach
                                    @endif
                                </div> --}}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        @include('partials.admin-footer')
