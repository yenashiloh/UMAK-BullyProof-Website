<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css' />

    <title>Users</title>

    @include('partials.guidance-link')

    <style>
        .fc-content {
            background-color: rgb(202, 202, 255);
        }

        .fc-day-number {
            color: black;
        }
    </style>

</head>

<body>

    <div id="loading-overlay">
        <div class="spinner"></div>
    </div>

    @include('partials.guidance-sidebar')
    @include('partials.guidance-header')

    <div class="container">
        <div class="page-inner">
            <div class="page-header">
                <h3 class="fw-bold mb-3">Appointment</h3>
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
                        <a href="{{ route('admin.users.users') }}">Appointment</a>
                    </li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Counselling Appointment</h4>
                        </div>
                        <div class="ui container">

                            {{-- <div class="ui menu">
                  <div class="header item">Brand</div>
                  <a class="active item">Link</a>
                  <a class="item">Link</a>
                  <div class="ui dropdown item">
                    Dropdown
                    <i class="dropdown icon"></i>
                    <div class="menu">
                      <div class="item">Action</div>
                      <div class="item">Another Action</div>
                      <div class="item">Something else here</div>
                      <div class="divider"></div>
                      <div class="item">Separated Link</div>
                      <div class="divider"></div>
                      <div class="item">One more separated link</div>
                    </div>
                  </div>
                  <div class="right menu">
                    <div class="item">
                      <div class="ui action left icon input">
                        <i class="search icon"></i>
                        <input type="text" placeholder="Search">
                        <button class="ui button">Submit</button>
                      </div>
                    </div>
                    <a class="item">Link</a>
                  </div>
                </div>
              </div> --}}

                            <br />
                            <div class="ui container">
                                <div class="ui grid">
                                    <div class="ui sixteen column">
                                        <div id="calendar"></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">List of Users</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="basic-datatables" class="display table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Full Name</th>
                                            <th>Email</th>
                                            <th>Contact</th>
                                            <th>Roles</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $index => $user)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ isset($user['fullname']) ? ucwords(strtolower($user['fullname'])) : '' }}
                                                </td>
                                                <td>{{ $user['email'] ?? '' }}</td>
                                                <td>{{ $user['contact'] ?? '' }}</td>
                                                <td>{{ $user['type'] ?? '' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div> --}}
    </div>
    <!-- End Custom template -->
    </div>

    @include('partials.admin-footer')
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js'></script>
    <script>
        $(document).ready(function() {

            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,basicWeek,basicDay'
                },
                defaultDate: '2024-12-12',
                navLinks: true, 
                editable: true,
                eventLimit: true, 
                events: [{
                        title: 'Shiloh Eugenio',
                        start: '2024-12-01'
                    },
                    {
                        title: 'Paul Angelo Derige',
                        start: '2024-12-07',
                        end: '2024-12-10'
                    },
                    {
                        id: 999,
                        title: 'Ryan Corda',
                        start: '2024-12-09T16:00:00'
                    },
                    {
                        id: 999,
                        title: 'Ryan Corda',
                        start: '2024-12-16T16:00:00'
                    },
                    {
                        title: 'Shiloh Eugenio',
                        start: '2024-12-11',
                        end: '2024-12-13'
                    },
                    {
                        title: 'Shiloh Eugenio',
                        start: '2024-12-12T10:30:00',
                        end: '2024-12-12T12:30:00'
                    },
                    {
                        title: 'Ryan Corda',
                        start: '2024-12-12T12:00:00'
                    },
                    {
                        title: 'Shiloh Eugenio',
                        start: '2024-12-12T14:30:00'
                    },
                    {
                        title: 'Jade Daniele Bantilo',
                        start: '2024-12-12T17:30:00'
                    }
                    // {
                    //     title: 'Dinner',
                    //     start: '2024-12-12T20:00:00'
                    // },
                    // {
                    //     title: 'Birthday Party',
                    //     start: '2024-12-13T07:00:00'
                    // },
                    // {
                    //     title: 'Click for Google',
                    //     url: 'https://google.com/',
                    //     start: '2024-12-28'
                    // }
                ]
            });

        });
    </script>
