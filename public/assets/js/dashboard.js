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
        if (endDate.value && new Date(endDate.value) < new Date(this.value)) {
            endDate.value = this.value;
        }
        // Trigger chart update or data refresh
        updateCharts();
    });

    // Update start date max value when end date changes
    endDate.addEventListener('change', function() {
        startDate.max = this.value;
        if (startDate.value && new Date(startDate.value) > new Date(this.value)) {
            startDate.value = this.value;
        }
        // Trigger chart update or data refresh
        updateCharts();
    });

    // Initialize charts on page load
    initializeCharts();
});

function resetDateRange() {
    // Reset to default date range (current year)
    const currentYear = new Date().getFullYear();
    document.getElementById('start_date').value = `${currentYear}-01-01`;
    document.getElementById('end_date').value = new Date().toISOString().split('T')[0];
    
    // Update charts and submit form
    updateCharts();
    document.getElementById('dateRangeForm').submit();
}

function initializeCharts() {
    try {
        // Get data from hidden input elements
        const reportMonths = JSON.parse(document.getElementById('reportMonths').value);
        const reportCounts = JSON.parse(document.getElementById('reportCounts').value);
        const platformLabels = JSON.parse(document.getElementById('platformLabels').value);
        const platformData = JSON.parse(document.getElementById('platformData').value);

        // Store charts globally for potential updates
        window.lineChart = initializeLineChart(reportMonths, reportCounts);
        window.platformBarChart = initializePlatformBarChart(platformLabels, platformData);
    } catch (error) {
        console.error('Error initializing charts:', error);
    }
}

function updateCharts() {
    // Collect current date range
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;

    // Fetch filtered data (you'll need to implement this server-side or client-side)
    fetchFilteredData(startDate, endDate).then(data => {
        // Update line chart
        if (window.lineChart) {
            window.lineChart.data.labels = data.reportMonths;
            window.lineChart.data.datasets[0].data = data.reportCounts;
            window.lineChart.update();
        }

        // Update platform bar chart
        if (window.platformBarChart) {
            window.platformBarChart.data.labels = data.platformLabels;
            window.platformBarChart.data.datasets[0].data = data.platformData;
            window.platformBarChart.update();
        }
    }).catch(error => {
        console.error('Error updating charts:', error);
    });
}

// Placeholder function for fetching filtered data
async function fetchFilteredData(startDate, endDate) {
    // In a real application, this would be an AJAX call to your backend
    // For now, we'll simulate data fetching
    return new Promise((resolve) => {
        // Simulate an AJAX call with existing data
        const reportMonths = JSON.parse(document.getElementById('reportMonths').value);
        const reportCounts = JSON.parse(document.getElementById('reportCounts').value);
        const platformLabels = JSON.parse(document.getElementById('platformLabels').value);
        const platformData = JSON.parse(document.getElementById('platformData').value);

        resolve({
            reportMonths,
            reportCounts,
            platformLabels,
            platformData
        });
    });
}

function initializeLineChart(reportMonths, reportCounts) {
    const ctx = document.getElementById('lineChart').getContext('2d');
    
    return new Chart(ctx, {
        type: 'line',
        data: {
            labels: reportMonths,
            datasets: [{
                label: 'Number of Reports',
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
                    grid: { display: false },
                    ticks: { color: '#666' }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.1)' },
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
                    text: 'Monthly Reports',
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                }
            },
            elements: {
                line: { tension: 0.4 }
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
                    grid: { display: false },
                    offset: false,
                    padding: 0
                }
            },
            plugins: {
                legend: { display: false },
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

function initializeCyberbullyingPieChart() {
    try {
        const cyberbullyingDataElement = document.getElementById('cyberbullyingData');
        
        if (!cyberbullyingDataElement) {
            console.error('Cyberbullying data element not found');
            return null;
        }

        // Parse the JSON data
        const cyberbullyingData = JSON.parse(cyberbullyingDataElement.value);
        
        const ctx = document.getElementById('cyberbullyingPieChart');
        
        if (!ctx) {
            console.error('Canvas element not found');
            return null;
        }

        console.log('Cyberbullying Data:', cyberbullyingData);

        // Prepare data for pie chart
        const labels = Object.keys(cyberbullyingData);
        const data = Object.values(cyberbullyingData);
        
        // Generate random pastel colors
        const backgroundColors = labels.map(() => {
            const r = Math.floor(Math.random() * 127 + 127);
            const g = Math.floor(Math.random() * 127 + 127);
            const b = Math.floor(Math.random() * 127 + 127);
            return `rgba(${r}, ${g}, ${b}, 0.6)`;
        });

        return new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: backgroundColors,
                    borderColor: backgroundColors.map(color => color.replace('0.6', '1')),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Cyberbullying Types Distribution',
                        font: {
                            size: 16,
                            weight: 'bold'
                        }
                    },
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            boxWidth: 20,
                            usePointStyle: true,
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const value = context.parsed;
                                const percentage = ((value / total) * 100).toFixed(2);
                                return `${context.label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error initializing Cyberbullying Pie Chart:', error);
        return null;
    }
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
    const cyberbullyingPieChart = initializeCyberbullyingPieChart();
});