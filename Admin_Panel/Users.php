<?php

// ---------- SEARCH ----------
$search = $_GET['search'] ?? '';
$where = "";
if ($search != "") {
    $where = "WHERE username LIKE '%$search%' OR email LIKE '%$search%'";
}

// ---------- PAGINATION ----------
$limit = 5;
$page = $_GET['page'] ?? 1;
$start = ($page - 1) * $limit;

// Total count
$total_q = mysqli_query($connection, "SELECT COUNT(*) as total FROM registers $where");
$total = mysqli_fetch_assoc($total_q)['total'];
$pages = ceil($total / $limit);

// Fetch users
$q = mysqli_query($connection, "SELECT * FROM registers where role != 'admin' $where ORDER BY id DESC LIMIT $start,$limit");

// ---------- KPI ----------
$total_users = mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as c FROM registers WHERE role!='admin'"))['c'];
$total_trainers = mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as c FROM registers WHERE role='trainer' OR role='both'"))['c'];
$total_learners = mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as c FROM registers WHERE role='learner' OR role='both'"))['c'];
?>
<div class="container-fluid p-4">

    <!-- KPI CARDS -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5>Total Users</h5>
                    <h3><?= $total_users ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5>Trainers</h5>
                    <h3><?= $total_trainers ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5>Learners</h5>
                    <h3><?= $total_learners ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- SEARCH + ADD USER -->
    <div class="d-flex justify-content-between mb-3">
        <form class="d-flex" method="get" action="public.php">
            <!-- Hidden input so "users" param always included -->
            <input type="hidden" name="users" value="1">
            <input type="text" name="search" value="<?= $search ?>" class="form-control me-2" placeholder="Search user">
            <button class="btn btn-primary">Search</button>
        </form>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">+ Add User</button>
    </div>

    <!-- USERS TABLE -->
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>City</th>
                        <th>Country</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    $i = 0;
                    while ($row = mysqli_fetch_assoc($q)) {
                        $i++;
                        ?>
                        <tr>
                            <td><?php echo $i ?></td>
                            <td><?= $row['username'] ?></td>
                            <td><?= $row['email'] ?></td>
                            <td><?= $row['role'] ?></td>
                            <td><?= $row['city'] ?></td>
                            <td><?= $row['country'] ?></td>
                            <td><?= $row['status'] ?></td>
                            <td>
                                <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewModal"
                                    data-id="<?= $row['id'] ?>" data-username="<?= $row['username'] ?>"
                                    data-email="<?= $row['email'] ?>" data-role="<?= $row['role'] ?>"
                                    data-city="<?= $row['city'] ?>" data-country="<?= $row['country'] ?>"
                                    data-status="<?= $row['status'] ?>">
                                    View
                                </button>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal"
                                    data-id="<?= $row['id'] ?>" data-username="<?= $row['username'] ?>"
                                    data-email="<?= $row['email'] ?>" data-role="<?= $row['role'] ?>"
                                    data-city="<?= $row['city'] ?>" data-country="<?= $row['country'] ?>"
                                    data-status="<?= $row['status'] ?>">
                                    Edit
                                </button>
                                <a href="../Skill_Swap/code.php?delete_user=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Delete this user?')">Delete</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <!-- PAGINATION -->
            <nav>
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $pages; $i++) { ?>
                        <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                            <a class="page-link" href="?users&page=<?= $i ?>&search=<?= $search ?>"><?= $i ?></a>
                        </li>
                    <?php } ?>
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- ADD USER MODAL -->
<div class="modal fade" id="addModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="../Skill_Swap/code.php">
                <div class="modal-header">
                    <h5>Add User</h5>
                </div>
                <div class="modal-body">
                    <input name="username" class="form-control mb-2" placeholder="Username">
                    <input name="email" type="email" class="form-control mb-2" placeholder="Email">
                    <input name="password" type="password" class="form-control mb-2" placeholder="Password">
                    <input name="city" class="form-control mb-2" placeholder="City">
                    <input name="country" class="form-control mb-2" placeholder="Country">
                    <select name="role" class="form-control mb-2">
                        <option>learner</option>
                        <option>trainer</option>
                        <option>both</option>
                    </select>
                    <select name="status" class="form-control mb-2">
                        <option>active</option>
                        <option>pending</option>
                        <option>disabled</option>
                    </select>
                </div>
                <div class="modal-footer"><button class="btn btn-primary" name="add_user">Save</button></div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT USER MODAL -->
<div class="modal fade" id="editModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="../Skill_Swap/code.php">
                <div class="modal-header">
                    <h5>Edit User</h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="e-id">
                    <input name="username" id="e-username" class="form-control mb-2">
                    <input name="email" id="e-email" class="form-control mb-2">
                    <input name="city" id="e-city" class="form-control mb-2">
                    <input name="country" id="e-country" class="form-control mb-2">
                    <select name="role" id="e-role" class="form-control mb-2">
                        <option>learner</option>
                        <option>trainer</option>
                        <option>both</option>
                    </select>
                    <select name="status" id="e-status" class="form-control mb-2">
                        <option>active</option>
                        <option>pending</option>
                        <option>disabled</option>
                    </select>
                </div>
                <div class="modal-footer"><button class="btn btn-primary" name="edit_user">Update</button></div>
            </form>
        </div>
    </div>
</div>

<!-- VIEW USER MODAL -->
<div class="modal fade" id="viewModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>User Details</h5>
            </div>
            <div class="modal-body">
                <!-- <p><b>ID:</b> <span id="v-id"></span></p> -->
                <img src="../Skill_Swap/<?php echo $_SESSION['pic'] ?>" alt="Profile_Pic"
                    style="width:100px;height:100px;border-radius:50%;">
                <p><b>Username:</b> <span id="v-username"></span></p>
                <p><b>Email:</b> <span id="v-email"></span></p>
                <p><b>Role:</b> <span id="v-role"></span></p>
                <p><b>City:</b> <span id="v-city"></span></p>
                <p><b>Country:</b> <span id="v-country"></span></p>
                <p><b>Status:</b> <span id="v-status"></span></p>
            </div>
        </div>
    </div>
</div>

<script>
    // Fill Edit Modal
    var editModal = document.getElementById('editModal');
    editModal.addEventListener('show.bs.modal', function (event) {
        var btn = event.relatedTarget;
        document.getElementById('e-id').value = btn.getAttribute('data-id');
        document.getElementById('e-username').value = btn.getAttribute('data-username');
        document.getElementById('e-email').value = btn.getAttribute('data-email');
        document.getElementById('e-city').value = btn.getAttribute('data-city');
        document.getElementById('e-country').value = btn.getAttribute('data-country');
        document.getElementById('e-role').value = btn.getAttribute('data-role');
        document.getElementById('e-status').value = btn.getAttribute('data-status');
    });

    // Fill View Modal
    var viewModal = document.getElementById('viewModal');
    viewModal.addEventListener('show.bs.modal', function (event) {
        var btn = event.relatedTarget;
        // document.getElementById('v-id').innerText = btn.getAttribute('data-id');
        document.getElementById('v-username').innerText = btn.getAttribute('data-username');
        document.getElementById('v-email').innerText = btn.getAttribute('data-email');
        document.getElementById('v-role').innerText = btn.getAttribute('data-role');
        document.getElementById('v-city').innerText = btn.getAttribute('data-city');
        document.getElementById('v-country').innerText = btn.getAttribute('data-country');
        document.getElementById('v-status').innerText = btn.getAttribute('data-status');
    });
</script>