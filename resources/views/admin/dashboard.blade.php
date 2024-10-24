<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Dashboard</title>

    @include('partials.admin-link')
</head>

<body>
    <div id="loading-overlay">
        <div class="spinner"></div>
    </div>
    @include('partials.admin-sidebar')
    @include('partials.admin-header')
    <div class="container">
        <div class="page-inner">
            <div class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4">
                <div>
                    <h3 class="fw-bold mb-3">Dashboard</h3>
                    <h6 class="op-7 mb-2">All reports are displayed in this dashboard</h6>
                </div>
                <div class="ms-md-auto py-2 py-md-0">
                    {{-- <a href="#" class="btn btn-label-info btn-round me-2">Manage</a> --}}
                    <a href="#" class="btn btn-primary btn-round">Generate Report</a>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-4">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-primary bubble-shadow-small">
                                        <i class="fas fa-users"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ms-3 ms-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Total Users</p>
                                        <h4 class="card-title">{{ $totalUsers }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-info bubble-shadow-small">
                                        <i class="fas fa-clipboard-list"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ms-3 ms-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Total Incidents Reported</p>
                                        <h4 class="card-title">{{ $totalReports }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-warning bubble-shadow-small">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ms-3 ms-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Total Review Report</p>
                                        <h4 class="card-title">{{ $toReviewCount }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-secondary bubble-shadow-small">
                                        <i class="fas fa fa-search"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ms-3 ms-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Total Under Investigation</p>
                                        <h4 class="card-title">{{ $underInvestigationCount }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- <div class="col-12 col-md-4">
                <div class="card card-stats card-round">
                  <div class="card-body">
                    <div class="row align-items-center">
                      <div class="col-icon">
                        <div class="icon-big text-center icon-danger bubble-shadow-small">
                          <i class="fas fa-exclamation-circle"></i>
                        </div>
                      </div>
                      <div class="col col-stats ms-3 ms-sm-0">
                        <div class="numbers">
                          <p class="card-category">Total Unresolved Incidents</p>
                          <h4 class="card-title"></h4>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div> --}}

                <div class="col-12 col-md-4">
                    <div class="card card-stats card-round">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-icon">
                                    <div class="icon-big text-center icon-success bubble-shadow-small">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                                <div class="col col-stats ms-3 ms-sm-0">
                                    <div class="numbers">
                                        <p class="card-category">Total Resolved Incidents</p>
                                        <h4 class="card-title">{{ $resolvedCount }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Types of Cyberbullying</div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="pieChart" style="width: 50%; "></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Number of Reports</div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="lineChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Cyberbullying Platforms</div>
                        </div>
                        <div class="card-body">
                            <div class="chart-container">
                                <canvas id="platformBarChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- End Custom template -->
    </div>
    @include('partials.admin-footer')

    <script>
        var cyberbullyingTypes = @json(array_keys($cyberbullyingData));
        var cyberbullyingCounts = @json(array_values($cyberbullyingData));

        var myPieChart = new Chart(pieChart, {
            type: "pie",
            data: {
                datasets: [{
                    data: cyberbullyingCounts,
                    backgroundColor: ["#1d7af3", "#f3545d", "#fdaf4b", "#36a2eb",
                        "#ff6384"
                    ],
                    borderWidth: 0,
                }, ],
                labels: cyberbullyingTypes,
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    position: "bottom",
                    labels: {
                        fontColor: "rgb(154, 154, 154)",
                        fontSize: 11,
                        usePointStyle: true,
                        padding: 20,
                    },
                },
                pieceLabel: {
                    render: "percentage",
                    fontColor: "white",
                    fontSize: 14,
                },
                tooltips: false,
                layout: {
                    padding: {
                        left: 20,
                        right: 20,
                        top: 20,
                        bottom: 20,
                    },
                },
            },
        });

        //line chart
        var ctx = document.getElementById('lineChart').getContext('2d');

        var reportMonths = @json($reportMonthData);
        var reportCounts = @json(array_values($reportCounts));

        var myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: reportMonths,
                datasets: [{
                    label: 'Number reports',
                    data: reportCounts,
                    backgroundColor: 'rgba(29, 122, 243, 0.1)',
                    borderColor: 'rgb(29, 122, 243)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgb(29, 122, 243)',
                    pointBorderColor: 'rgb(29, 122, 243)',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#666'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            color: '#666',
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    },
                    title: {
                        display: true,
                        text: 'Line Chart',
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    }
                },
                elements: {
                    line: {
                        tension: 0.4
                    }
                }
            }
        });

        //bar chart
        var ctx = document.getElementById('platformBarChart').getContext('2d');

        function getRandomColor() {
            var r = Math.floor(Math.random() * 255);
            var g = Math.floor(Math.random() * 255);
            var b = Math.floor(Math.random() * 255);
            return `rgba(${r}, ${g}, ${b}, 0.3)`; 
        }

        var randomColors = @json($platformLabels).map(() => getRandomColor());

        var platformBarChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($platformLabels),
                datasets: [{
                    label: 'Number of Incidents',
                    data: @json($platformData),
                    backgroundColor: randomColors,
                    borderColor: randomColors.map(color => color.replace('0.6',
                    '1')), 
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Platforms Where Cyberbullying Occurred'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Platforms'
                        },
                        ticks: {
                            autoSkip: false,
                            maxRotation: 0,
                            minRotation: 0
                        },
                        grid: {
                            display: false
                        },
                        offset: false,
                        padding: 0
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Common Platforms Used in Incidents'
                    }
                },
                layout: {
                    padding: {
                        left: 10,
                        right: 10
                    }
                }
            }
        });
    </script>
</body>

</html>
