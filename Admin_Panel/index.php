<!-- Dashboard Stats Start -->
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4 shadow-sm">
                <i class="fa fa-users fa-3x" style="color:#5FCF80;"></i>
                <div class="ms-3">
                    <p class="mb-2">Total Users</p>
                    <h6 class="mb-0">120</h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4 shadow-sm">
                <i class="fa fa-chalkboard-teacher fa-3x" style="color:#5FCF80;"></i>
                <div class="ms-3">
                    <p class="mb-2">Total Trainers</p>
                    <h6 class="mb-0">35</h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4 shadow-sm">
                <i class="fa fa-user-graduate fa-3x" style="color:#5FCF80;"></i>
                <div class="ms-3">
                    <p class="mb-2">Total Learners</p>
                    <h6 class="mb-0">85</h6>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3">
            <div class="bg-light rounded d-flex align-items-center justify-content-between p-4 shadow-sm">
                <i class="fa fa-envelope-open-text fa-3x" style="color:#5FCF80;"></i>
                <div class="ms-3">
                    <p class="mb-2">Feedback</p>
                    <h6 class="mb-0">15</h6>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Dashboard Stats End -->


<!-- Charts Section Start -->
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-12 col-xl-6">
            <div class="bg-light text-center rounded p-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h6 class="mb-0">User Growth</h6>
                    <a href="#">Show All</a>
                </div>
                <canvas id="user-growth"></canvas>
            </div>
        </div>
        <div class="col-sm-12 col-xl-6">
            <div class="bg-light text-center rounded p-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h6 class="mb-0">Requests Overview</h6>
                    <a href="#">Show All</a>
                </div>
                <canvas id="requests-overview"></canvas>
            </div>
        </div>
    </div>
</div>
<!-- Charts Section End -->


<!-- Recent Users Start -->
<div class="container-fluid pt-4 px-4">
    <div class="bg-light text-center rounded p-4 shadow-sm">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h6 class="mb-0">Recent Registered Users</h6>
            <a href="#">View All</a>
        </div>
        <div class="table-responsive">
            <table class="table text-start align-middle table-bordered table-hover mb-0">
                <thead>
                    <tr class="text-dark">
                        <th scope="col">#</th>
                        <th scope="col">Username</th>
                        <th scope="col">Email</th>
                        <th scope="col">Role</th>
                        <th scope="col">City</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Ali Khan</td>
                        <td>ali@example.com</td>
                        <td>Trainer</td>
                        <td>Karachi</td>
                        <td><span class="badge bg-success">Active</span></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Sara Ahmed</td>
                        <td>sara@example.com</td>
                        <td>Learner</td>
                        <td>Lahore</td>
                        <td><span class="badge bg-warning">Pending</span></td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Usman</td>
                        <td>usman@example.com</td>
                        <td>Both</td>
                        <td>Islamabad</td>
                        <td><span class="badge bg-success">Active</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- Recent Users End -->


<!-- Widgets Start (Messages, Calendar, To-Do) -->
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-sm-12 col-md-6 col-xl-4">
            <div class="h-100 bg-light rounded p-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <h6 class="mb-0">Messages</h6>
                    <a href="#">Show All</a>
                </div>
                <div class="d-flex align-items-center border-bottom py-3">
                    <img class="rounded-circle flex-shrink-0" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                    <div class="w-100 ms-3">
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-0">Ali Khan</h6>
                            <small>5 min ago</small>
                        </div>
                        <span>Hello, I need help with PHP...</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-md-6 col-xl-4">
            <div class="h-100 bg-light rounded p-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h6 class="mb-0">Calendar</h6>
                </div>
                <div id="calendar"></div>
            </div>
        </div>

        <div class="col-sm-12 col-md-6 col-xl-4">
            <div class="h-100 bg-light rounded p-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h6 class="mb-0">To Do List</h6>
                </div>
                <div class="d-flex mb-2">
                    <input class="form-control bg-transparent" type="text" placeholder="Enter task">
                    <button type="button" class="btn btn-success ms-2">Add</button>
                </div>
                <div class="d-flex align-items-center border-bottom py-2">
                    <input class="form-check-input m-0" type="checkbox">
                    <div class="w-100 ms-3">
                        <div class="d-flex w-100 align-items-center justify-content-between">
                            <span>Review new trainers</span>
                            <button class="btn btn-sm text-danger"><i class="fa fa-times"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Widgets End -->
