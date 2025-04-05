
<div class="container px-lg-5 mb-4">
    <header class=" d-flex justify-content-between mt-3">
        <h4 class=" fw-medium">Rental Request/s</h4>
        <button class="btn btn-primary fw-bold rounded-5 px-4">
            Send Email
        </button>
    </header>
    <div class=" container d-flex align-items-center justify-content-end gap-3 mt-2">
        <span class=" text-black-50">Select All</span>
        <input type="checkbox" name="all-rental-request" class=" form-check-input" id="check-all">
    </div>

    <div class="container mt-3">
        <div class="row gap-3">
            <div class="col-12 d-flex justify-content-between align-items-center gap-3">
                <div class="card">
                    <div class="card-header d-sm-flex d-grid gap-2 justify-content-between">
                        <div class=" d-flex">
                            <div class="card-img d-flex align-items-center gap-3">
                                <img width="70" height="70" class="rounded-circle"
                                    src="/rent-master2/admin/reports/images/man-8741800_1280.jpg" alt="Man">
                                <div class="">
                                    <h4 class="fw-medium card-title mb-1">Jane Doe</h4>
                                    <span class=" opacity-75 d-block card-subtitle">janedoe@gmail.com</span>
                                    <span class=" opacity-75 d-block card-subtitle">0995-784-8765</span>
                                </div>
                            </div>
                        </div>
                        <div class=" text-black-50 ">March 18, 2025 10:30 AM</div>
                    </div>
                    <div class="card-body">
                        <blockquote class="blockquote">
                            <p class=" fs-6 fw-medium lh-1">Dear Mr. Caricot</p>
                            <p class=" opacity-75 fs-6 ps-3">I’m interested in renting 324 Tara Place and would love
                                to learn more. The home’s spacious interiors and modern features are exactly what
                                I’m looking for. Could we schedule a viewing at your earliest convenience? Please
                                let me know the next steps.</p>
                        </blockquote>
                        <div class=" d-sm-flex d-grid justify-content-between">
                            <div>
                                <p class=" fs-6 fw-medium lh-1">Property</p>
                                <ul class=" list-unstyled ps-3">
                                    <li><span class=" fw-medium">ID: </span>House 407</li>
                                    <li><span class=" fw-medium">Name: </span>405 Lock House , Goa</li>
                                    <li><span class=" fw-medium">Location: </span>Los Angeles California, USA </li>
                                </ul>
                            </div>
                            <div class=" d-flex gap-3 align-self-end">
                                <a href="#" class=" rounded-5 btn btn-primary px-3 fw-medium">Approved</a>
                                <a href="#" class=" rounded-5 btn btn-secondary px-3 fw-medium">Reject</a>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="checkbox" name="rental-request" class=" rental-request form-check-input" id="">
            </div>
            <div class="col-12 d-flex justify-content-between align-items-center gap-3">
                <div class="card">
                    <div class="card-header d-sm-flex d-grid gap-2 justify-content-between">
                        <div class=" d-flex">
                            <div class="card-img d-flex align-items-center gap-3">
                                <img width="70" height="70" class="rounded-circle"
                                    src="/rent-master2/admin/reports/images/ai-generated-9009342_1280.jpg" alt="Man">
                                <div class="">
                                    <h4 class="fw-medium card-title  mb-1">Isaac Newton</h4>
                                    <span class=" opacity-75 d-block card-subtitle">newtonlawsofmotion@gmail.com</span>
                                    <span class=" opacity-75 d-block card-subtitle">09434-384-8455</span>
                                </div>
                            </div>
                        </div>
                        <div class=" text-black-50 ">March 13, 2025 2:30 AM</div>
                    </div>
                    <div class="card-body">
                        <blockquote class="blockquote">
                            <p class=" fs-6 fw-medium lh-1">Dear Sir. Jerico Caricot</p>
                            <p class=" opacity-75 fs-6 ps-3">I’m very interested in renting 410 Epic Mansion in Los Angeles. The property's features and location make it an ideal home for me. I’d love to schedule a viewing and discuss the rental terms. Please let me know the next steps at your convenience.</p>
                        </blockquote>
                        <div class=" d-sm-flex d-grid justify-content-between">
                            <div>
                                <p class=" fs-6 fw-medium lh-1">Property</p>
                                <ul class=" list-unstyled ps-3">
                                    <li><span class=" fw-medium">ID: </span>House 408</li>
                                    <li><span class=" fw-medium">Name: </span> 410 Epic Manshion , LA</li>
                                    <li><span class=" fw-medium">Location: </span>Oaklahoma City, USA </li>
                                </ul>
                            </div>
                            <div class=" d-flex gap-3 align-self-end">
                                <a href="#" class=" rounded-5 btn btn-primary px-3 fw-medium">Approved</a>
                                <a href="#" class=" rounded-5 btn btn-secondary px-3 fw-medium">Reject</a>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="checkbox" name="rental-request" class=" rental-request form-check-input" id="">

            </div>
        </div>
    </div>
</div>


<script>
    const checkAll = document.getElementById("check-all");
    const rentRequest = document.querySelectorAll(".rental-request");
    checkAll.addEventListener('input', function (e) {
        rentRequest.forEach( r => {
            r.checked = e.target.checked ? true : false;
        })
    })
</script>
