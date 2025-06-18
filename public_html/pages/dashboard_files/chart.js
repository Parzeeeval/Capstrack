// Data sets for "Title Evaluation" chart
const chartData = {
    '1': {
        data: [20, 30, 50], // Data for BSIT 4DG1
    },
    '2': {
        data: [25, 35, 40], // Data for BSIT 4DG2
    },
    '3': {
        data: [30, 40, 30], // Data for BSIT 4EG1
    },
    '4': {
        data: [10, 20, 70], // Data for BSIT 4EG2
    }
};

// Data sets for "Total Title Defense" chart
const defenseChartData = {
    '1': {
        data: [15, 25, 60], // Data for BSIT 4DG1
    },
    '2': {
        data: [20, 30, 50], // Data for BSIT 4DG2
    },
    '3': {
        data: [35, 25, 40], // Data for BSIT 4EG1
    },
    '4': {
        data: [40, 20, 40], // Data for BSIT 4EG2
    }
};

// Default chart colors (for both charts)
const defaultColors = ['#27AE60', '#C0392B', '#E6BC41']; // Green, Red, Yellow

// Get canvas elements for both charts
const myChartElement = document.getElementById("my-chart");
const defenseChartElement = document.getElementById("defense-chart");

// Get dropdown elements for both charts
//const evaluationDropdown = document.getElementById("title-select-evaluation");

// Initialize "Title Evaluation" chart with default data
let myChart = new Chart(myChartElement, {
    type: "bar",
    data: {
        labels: ["Approved", "Rejected", "Need Improvement"], // Labels won't change
        datasets: [{
            label: "Title Evaluation",
            data: chartData['1'].data, // Default data (for BSIT 4DG1)
            backgroundColor: '#D1642E', // Fixed colors
            borderColor: '#ffffff',
            borderWidth: 1
        }]
    },
    options: {
        
        responsive: true,
        
        borderRadius: 100,
        borderSkipped: 'bottom',
        hoverBorderWidth: 0,
        scales: {
            x: {
                ticks: {
                    display: false // Hides the labels on the x-axis
                }
            },
            y: {
                ticks: {
                    display: false // Optional: Hides the labels on the y-axis as well
                }
            }
        },
        plugins: {
            legend: {
                display: false,
                position: 'bottom',
                align: 'center',
                labels: {
                    boxWidth: 20,
                    padding: 15
                }
            }
        }
    }
});

// Initialize "Total Title Defense" chart with default data
let defenseChart = new Chart(defenseChartElement, {
    type: "bar",
    data: {
        labels: ["Approved", "Rejected", "Approved w/Revisions"], // Labels won't change
        datasets: [{
            label: "Total Title Defense",
            data: defenseChartData['1'].data, // Default data (for BSIT 4DG1)
            backgroundColor: '#066ba3', // Fixed colors
            borderColor: '#ffffff',
            borderWidth: 1
        
        }]
    },
    options: {
        responsive: true,
        
        borderRadius: 100,
        hoverBorderWidth: 0,
        scales: {
            x: {
                ticks: {
                    display: false // Hides the labels on the x-axis
                }
            },
            y: {
                ticks: {
                    display: false // Optional: Hides the labels on the y-axis as well
                }
            }
        },
        responsive: true,
        
        plugins: {
            legend: {
                display: false,
                position: 'bottom',
                align: 'center',
                labels: {
                    boxWidth: 20,
                    padding: 15
                }
            }
        }
    }
});

// Event listener to update "Title Evaluation" chart when its dropdown changes
/*evaluationDropdown.addEventListener("change", function() {
    const selectedValue = this.value; // Get the selected value from dropdown

    // Update the "Title Evaluation" chart data
    myChart.data.datasets[0].data = chartData[selectedValue].data;
    myChart.update();
});

// Event listener to update "Total Title Defense" chart when its dropdown changes
evaluationDropdown.addEventListener("change", function() {
    const selectedValue = this.value; // Get the selected value from dropdown

    // Update the "Total Title Defense" chart data
    defenseChart.data.datasets[0].data = defenseChartData[selectedValue].data;
    defenseChart.update();
});*/
