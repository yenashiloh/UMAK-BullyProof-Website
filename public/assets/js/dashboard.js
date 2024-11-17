document.addEventListener('DOMContentLoaded', function() {
    // Initialize date inputs with min/max constraints
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');

    // Set max date to today for both inputs
    const today = new Date().toISOString().split('T')[0];
    startDate.max = today;
    endDate.max = today;

    // Update end date min value when start date changes
    startDate.addEventListener('change', function() {
        endDate.min = this.value;
        if (endDate.value && endDate.value < this.value) {
            endDate.value = this.value;
        }
    });

    // Update start date max value when end date changes
    endDate.addEventListener('change', function() {
        startDate.max = this.value;
        if (startDate.value && startDate.value > this.value) {
            startDate.value = this.value;
        }
    });
});

function resetDateRange() {
    // Reset to default date range (current year)
    const currentYear = new Date().getFullYear();
    document.getElementById('start_date').value = `${currentYear}-01-01`;
    document.getElementById('end_date').value = new Date().toISOString().split('T')[0];
    document.getElementById('dateRangeForm').submit();
}
function initializeLineChart(reportMonths, reportCounts) {
    const ctx = document.getElementById('lineChart').getContext('2d');
    
    return new Chart(ctx, {
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
}

function getRandomColor() {
    const r = Math.floor(Math.random() * 255);
    const g = Math.floor(Math.random() * 255);
    const b = Math.floor(Math.random() * 255);
    return `rgba(${r}, ${g}, ${b}, 0.3)`;
}

function initializePlatformBarChart(platformLabels, platformData) {
    const ctx = document.getElementById('platformBarChart').getContext('2d');
    const randomColors = platformLabels.map(() => getRandomColor());

    return new Chart(ctx, {
        type: 'bar',
        data: {
            labels: platformLabels,
            datasets: [{
                label: 'Number of Incidents',
                data: platformData,
                backgroundColor: randomColors,
                borderColor: randomColors.map(color => color.replace('0.3', '1')),
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
}

// Initialize charts when document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Get data from PHP variables passed to the view
    const reportMonths = JSON.parse(document.getElementById('reportMonths').value);
    const reportCounts = JSON.parse(document.getElementById('reportCounts').value);
    const platformLabels = JSON.parse(document.getElementById('platformLabels').value);
    const platformData = JSON.parse(document.getElementById('platformData').value);

    // Initialize charts
    const lineChart = initializeLineChart(reportMonths, reportCounts);
    const platformBarChart = initializePlatformBarChart(platformLabels, platformData);
});