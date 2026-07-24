// Academic Hub JavaScript - Chart.js Implementation

// Placement Statistics Pie Chart
const pieCtx = document.getElementById('placementPie');
if (pieCtx) {
    new Chart(pieCtx.getContext('2d'), {
        type: 'pie',
        data: {
            labels: ['Placed', 'Pending', 'Not Placed'],
            datasets: [{
                data: [378, 120, 126],
                backgroundColor: ['#0d6efd', '#ffc107', '#dc3545'],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
                }
            }
        }
    });
}

// Monthly Placement Bar Chart
const barCtx = document.getElementById('monthlyBar');
if (barCtx) {
    new Chart(barCtx.getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
            datasets: [{
                label: 'Placements',
                data: [25, 34, 40, 52, 60, 72, 88],
                backgroundColor: '#0d6efd',
                borderRadius: 6,
                barThickness: 18
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(13, 110, 253, 0.08)',
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });
}

// Department Placement Doughnut Chart
const doughnutCtx = document.getElementById('departmentChart');
if (doughnutCtx) {
    new Chart(doughnutCtx.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['CSE', 'IT', 'ECE', 'ME', 'CE'],
            datasets: [{
                data: [145, 98, 76, 54, 28],
                backgroundColor: ['#0d6efd', '#0dcaf0', '#198754', '#ffc107', '#fd7e14'],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: {
                            size: 12
                        }
                    }
                }
            },
            cutout: '65%'
        }
    });
}
