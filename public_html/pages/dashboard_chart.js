const ctx = document.getElementById('pieChart');

new Chart(ctx, {
  type: 'doughnut',
  data: {
    labels: ['Approved', 'Needs Improvement', 'Rejected'],
    datasets: [{
      label: '# of projects',
      data: [33,33,33],
      borderWidth: 1
    }]
  },
  options: {
    scales: {
      y: {
        beginAtZero: true
      }
    }
  }
});