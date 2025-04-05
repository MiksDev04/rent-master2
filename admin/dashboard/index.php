<div class="container">
    <h4 class="text-center fw-medium mt-3">Welcome Admin</h4>


    <div class="container px-lg-5">
        <h5 class="text-black-50 mt-4">Overview this Month</h5>
        <hr>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 gx-lg-5 gy-3 overviews">
            <div class="col">
                <a href="?page=properties/index" class=" text-decoration-none card h-100  shadow text-white rounded-4 shortcuts" id="houses" onclick="ShowProperties()">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-evenly">
                            <img src="/rent-master/admin/assets/icons/Neighborhood.png" alt="">
                            <div class="d-flex flex-column align-items-center">
                                <h1 class="fw-bolder fs-1">20</h1>
                                <span class="fw-bold">Houses</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-evenly rounded-4 bg-danger">
                        <span>View more</span>
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" width="24px" fill="whitesmoke"
                            viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                            <path
                                d="M0 256a256 256 0 1 0 512 0A256 256 0 1 0 0 256zM297 385c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l71-71L120 280c-13.3 0-24-10.7-24-24s10.7-24 24-24l214.1 0-71-71c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0L409 239c9.4 9.4 9.4 24.6 0 33.9L297 385z" />
                        </svg>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="?page=tenants/index"  class=" text-decoration-none card h-100 shadow  text-white rounded-4 shortcuts" id="tenants" onclick="ShowTenants()">
                    <div class="card-body ">
                        <div class="d-flex align-items-center justify-content-evenly">
                            <img src="/rent-master/admin/assets/icons/Tenant.png" alt="">
                            <div class="d-flex flex-column align-items-center">
                                <h1 class="fw-bolder fs-1">18</h1>
                                <span class="fw-bold">Tenants</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-evenly rounded-4 bg-primary ">
                        <span>View more</span>
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" width="24px" fill="whitesmoke"
                            viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                            <path
                                d="M0 256a256 256 0 1 0 512 0A256 256 0 1 0 0 256zM297 385c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l71-71L120 280c-13.3 0-24-10.7-24-24s10.7-24 24-24l214.1 0-71-71c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0L409 239c9.4 9.4 9.4 24.6 0 33.9L297 385z" />
                        </svg>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="?page=payments/index"  class=" text-decoration-none card  h-100 shadow text-white  rounded-4 shortcuts" id="payments" onclick="ShowPayments()">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-evenly">
                            <img src="/rent-master/admin/assets/icons/Online Payment.png" alt="Payment icon">
                            <div class="d-flex flex-column align-items-center">
                                <h1 class="fw-bolder fs-1">14</h1>
                                <span class="fw-bold">Paid</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex align-items-center justify-content-evenly bg-success rounded-4">
                        <span>View more</span>
                        <svg xmlns="http://www.w3.org/2000/svg" height="24px" width="24px" fill="whitesmoke"
                            viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.-->
                            <path
                                d="M0 256a256 256 0 1 0 512 0A256 256 0 1 0 0 256zM297 385c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l71-71L120 280c-13.3 0-24-10.7-24-24s10.7-24 24-24l214.1 0-71-71c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0L409 239c9.4 9.4 9.4 24.6 0 33.9L297 385z" />
                        </svg>
                    </div>
                </a>
            </div>
        </div>

        <h5 class="text-black-50 mt-4">Monthly History</h5>
        <hr>
    </div>
    <div class=" container mb-3" style="width: 100%; height: 300px; margin: auto;">
        <canvas id="myChart" class=" w-100"></canvas>
    </div>
    
</div>
<script>
function initChart() {
    const ctx = document.getElementById('myChart');
    if (!ctx) return;

    new Chart(ctx.getContext('2d'), {
        type: 'bar',
        data: {
            labels: ['October', 'November', 'December', 'January', 'February', 'March'],
            datasets: [{
                label: '# of Tenants',
                data: [15, 19, 18, 15, 14, 19],
                backgroundColor: ['rgba(255, 99, 133, 0.7)', 'rgba(54, 162, 235, 0.7)', 'rgba(255, 206, 86, 0.7)', 'rgba(75, 192, 192, 0.7)', 'rgba(153, 102, 255, 0.7)', 'rgba(255, 159, 64, 0.7)'],
                borderColor: ['rgba(255, 99, 132, 1)', 'rgba(54, 162, 235, 1)', 'rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)', 'rgba(153, 102, 255, 1)', 'rgba(255, 159, 64, 1)'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// Run initChart when the page loads
document.addEventListener('DOMContentLoaded', initChart);
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
