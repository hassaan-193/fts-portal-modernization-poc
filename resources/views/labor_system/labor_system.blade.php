@extends('layouts.master')

@section('css')
    @include('layouts.datatables_css')
@endsection

@section('content')

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark text-weight-bold">labor attendance</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">labor attendance</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        @include('flash::message')
        <div class="bg-white card-primary card-maroon">
            <div class="card-header">
                <h3 class="card-title">Attendance</h3>
            </div>
            <div class="card-body">
                <style>
                    @media (max-width: 1024px) { /* Tablets */
                        .labor-system .row {
                            display: flex;
                            flex-wrap: nowrap;
                            align-items: stretch; /* Ensures both sections stretch equally */
                        }
                    
                        .col-md-4 {
                            flex: 0 0 35%; /* Set a fixed width for "Available Labors" */
                            max-width: 35%;
                        }
                    
                        .col-md-8 {
                            flex: 0 0 65%; /* Allow right section to take remaining space */
                            max-width: 65%;
                        }
                    
                        .available-labors {
                            width: 232px !important;
                            height: auto;
                            overflow-y: auto;
                        }
                    
                        .assignment-section {
                            width: 100%;
                            padding: 15px;
                        }
                    }
                    
                    @media (max-width: 850px) { /* Smaller Tablets */
                        .labor-system .row {
                            flex-direction: column;
                        }
                    
                        .col-md-4, .col-md-8 {
                            max-width: 100%;
                            flex: 100%;
                        }
                    
                        .available-labors {
                            width: 100%;
                        }
                    
                        .assignment-section {
                            width: 100%;
                        }
                    }
                    </style>
                <style>

                .content-wrapper {
                    background-color: white !important;
                }
                .labor-system {
                    padding: 20px;
                }
                .available-labors {
                    position: sticky;
                    top: 0;
                    border: 1px solid #ddd;
                    padding: 15px;
                    min-height: 500px;
                    width: 300px;
                    max-height: 90vh;
                    overflow-y: auto;
                }
                .labor-item {
                    cursor: move;
                    margin: 5px;
                    padding: 8px;
                    background: #f8f9fa;
                    border: 1px solid #dee2e6;
                    border-radius: 4px;
                }
                .assignment-section {
                    margin-bottom: 20px;
                    border: 1px solid #ddd;
                    padding: 15px;
                    border-radius: 4px;
                    position: relative;
                }
                .drop-zone {
                    min-height: 70px;
                    border: 2px dashed rgb(122, 122, 253);
                    padding: 10px;
                    margin: 10px 0;
                    background: #fff;
                }
                .assigned-labor {
                    display: inline-block;
                    margin: 5px;
                    padding: 5px 10px;
                    background: #e9ecef;
                    border-radius: 4px;
                }
                .remove-section {
                    position: absolute;
                    top: 10px;
                    right: 10px;
                }
                .date-group {
                    display: flex;
                    gap: 10px;
                    margin-top: 10px;
                }
                .date-group input {
                    flex: 1;
                }
            </style>
            
            <div class="container labor-system">
                
                <div class="row">
                    <div class="col-md-4">
                        <div class="available-labors">
                            <h4>Available Labors</h4>
                            <input type="text" id="labor-search" class="form-control mb-2" placeholder="Search labor...">
            
                            <div id="labor-list" class="mb-3">
                                <!-- Labors will be loaded via AJAX -->
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div id="assignment-sections">
                            <div class="assignment-section">
                                <button type="button" class="btn btn-danger btn-sm remove-section">×</button>
                                <div class="form-group">
                                    <label>Select Project</label>
                                    <select class="form-control project-select" required>
                                        <option value="">Loading projects...</option>
                                    </select>
                                </div>
                                <div class="visit-schedule-wrapper" style="display: none;">
                                    <div class="form-group">
                                        <label>Visit Schedule (AMC Only)</label>
                                        <select class="form-control visit-schedule-select">
                                            <option value="">Select Visit Schedule</option>
                                        </select>
                                    </div>
                                </div>
                                <label>Date</label>
                                <div class="date-group">
                                    <input type="date" class="form-control start-date" 
                                           value="{{ date('Y-m-d') }}" required>
                                    <input type="date" class="form-control end-date" 
                                           value="{{ date('Y-m-d') }}" required hidden>
                                </div>
                                <br>
                                <div class="form-group">
                                    <label>Hours Worked</label>
                                    <input type="number" class="form-control hours-worked" required min="1" step="1">
                                </div>
                                <div class="drop-zone">
                                    <p class="text-muted">Drag labors here</p>
                                </div>
                            </div>
                        </div>
                        <div class="text-right mt-3">
                            <button id="add-section" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Add Another Project
                            </button>
                            <button id="submit-all" class="btn btn-success">
                                <i class="fas fa-save"></i> Submit All Assignments
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Search functionality
    $('#labor-search').on('keyup', function() {
        const searchText = $(this).val().toLowerCase();
        $('#labor-list .labor-item').each(function() {
            $(this).toggle($(this).text().toLowerCase().includes(searchText));
        });
    });

    // Load initial data
    let allProjects = [], allLabors = [];
    const today = '{{ date('Y-m-d') }}';

    function fetchLabors(date) {
        $.get("{{ route('labor.getLaborers') }}", { date }, function(labors) {
            allLabors = labors;
            $('#labor-list').empty();
            labors.forEach(l => {
                $('#labor-list').append(
                    `<div class="labor-item" data-id="${l.id}" draggable="true">${l.name}</div>`
                );
            });
        });
    }

    fetchLabors(today);

    // Re-fetch on date change
    $('#assignment-sections').on('change', '.start-date', function() {
        fetchLabors($(this).val());
    });

    // Load projects
    $.get("{{ route('labor.getProjects') }}", function(projects) {
        allProjects = projects;
        $('.project-select').each(function() {
            initProjectSelect($(this));
        });
    });

    function initProjectSelect($sel) {
        $sel.empty().append('<option value="">Choose Project</option>');
        allProjects.forEach(p => {
            $sel.append(
                `<option value="${p.id}" data-category="${p.category}">${p.subject}</option>`
            );
        });
    }

    // Add/remove sections
    $('#add-section').click(function() {
        const $new = $('.assignment-section').first().clone();
        $new.find('.project-select').each(function() { initProjectSelect($(this)); });
        $new.find('.visit-schedule-wrapper').hide();
        $new.find('.drop-zone').empty();
        $('#assignment-sections').append($new);
    });

    $('#assignment-sections').on('click', '.remove-section', function() {
        const $sec = $(this).closest('.assignment-section');
        $sec.find('.assigned-labor').each(function() {
            const id = $(this).data('id'), name = $(this).find('span').text();
            $('#labor-list').append(
                `<div class="labor-item" data-id="${id}" draggable="true">${name}</div>`
            );
        });
        $sec.remove();
    });

    // Toggle AMC schedule
    $('#assignment-sections').on('change', '.project-select', function() {
        const cat = $(this).find(':selected').data('category'),
              $wrap = $(this).closest('.assignment-section').find('.visit-schedule-wrapper');
        if (cat === 'amc') {
            $wrap.show();
            $.get("{{ route('labor.getVisitSchedules') }}", { project_id: $(this).val() }, function(schs) {
                const $vs = $wrap.find('.visit-schedule-select').empty()
                                .append('<option value="">Select Visit Schedule</option>');
                schs.forEach(s => $vs.append(`<option value="${s.id}">${s.visit_date}</option>`));
            });
        } else $wrap.hide();
    });

    // Drag‑n‑drop
    $('#labor-list').on('dragstart', '.labor-item', function(e) {
        e.originalEvent.dataTransfer.setData('text', $(this).data('id'));
    });

    $('#assignment-sections').on('dragover', '.drop-zone', e => e.preventDefault());

    $('#assignment-sections').on('drop', '.drop-zone', function(e) {
        e.preventDefault();
        const laborId = e.originalEvent.dataTransfer.getData('text'),
              $src = $('#labor-list').find(`[data-id="${laborId}"]`),
              laborName = $src.text();

        if ($(this).find(`.assigned-labor[data-id="${laborId}"]`).length) {
            return alert("This laborer is already assigned.");
        }

        $src.remove();

        $(this).append(`
          <div class="assigned-labor d-flex align-items-center mb-2" data-id="${laborId}">
            <span class="mr-2">${laborName}</span>
            <div class="input-group input-group-sm quantity-group mr-2" style="width: 100px;">
              <div class="input-group-prepend">
                <button class="btn btn-outline-secondary btn-decrement" type="button">−</button>
              </div>
              <input type="text" class="form-control text-center quantity-input" value="0" readonly>
              <div class="input-group-append">
                <button class="btn btn-outline-secondary btn-increment" type="button">+</button>
              </div>
            </div>
            <button class="btn btn-sm btn-danger X-btn" type="button">×</button>
          </div>
        `);
    });

    // Remove assigned labor
    $('#assignment-sections').on('click', '.X-btn', function() {
        const $lab = $(this).closest('.assigned-labor'),
              id = $lab.data('id'),
              name = $lab.find('span').text();
        $lab.remove();
        $('#labor-list').append(
            `<div class="labor-item" data-id="${id}" draggable="true">${name}</div>`
        );
    });

    // Quantity buttons
    $('#assignment-sections').on('click', '.btn-increment', function() {
        const $inp = $(this).closest('.quantity-group').find('.quantity-input'),
              v = parseInt($inp.val(),10) || 0;
        $inp.val(v + 1);
    });

    $('#assignment-sections').on('click', '.btn-decrement', function() {
        const $inp = $(this).closest('.quantity-group').find('.quantity-input'),
              v = parseInt($inp.val(),10) || 0;
        if (v > 0) $inp.val(v - 1);
    });

    // Submit
    $('#submit-all').click(function() {
    let allAssignments = [];

    $('#assignment-sections .assignment-section').each(function() {
        const projectId = $(this).find('.project-select').val();
        const visitScheduleId = $(this).find('.visit-schedule-select').val();
        const laborIds = [];
        const hoursWorked = $(this).find('.hours-worked').val();  // Capture hours worked
        const startDate = $(this).find('.start-date').val();
        let endDate = $(this).find('.end-date').val();

        if (!endDate || endDate < startDate) {
            endDate = startDate;  // Set end_date to start_date if it's invalid
        }

        $(this).find('.assigned-labor').each(function() {
            const id = $(this).data('id');
            const qty = parseInt($(this).find('.quantity-input').val(), 10) || 0;
                laborIds.push({ id: id, quantity: qty });
        });

        if (projectId && laborIds.length > 0 && hoursWorked) {
            allAssignments.push({
                project_id: projectId,
                visit_schedule_id: visitScheduleId,
                labor_ids: laborIds,
                start_date: startDate,
                end_date: endDate,
                hours_worked: hoursWorked
            });
        }
    });

    console.log(allAssignments);  // Log the data before sending it

    if (allAssignments.length > 0) {
        $.post("{{ route('labor.assignLabor') }}", {
            assignments: allAssignments,
            _token: "{{ csrf_token() }}"
        }, function(response) {
            alert(response.message);
            location.reload();
        }).fail(function(response) {
            console.error("Error Response:", response.responseJSON);  // Log the full error response
            alert(response.responseJSON.message);
        });
    } else {
        alert("Please complete all sections before submitting.");
    }
});

});
</script>
@endsection
