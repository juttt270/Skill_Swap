<?php
// ---------- SEARCH ----------
$search = $_GET['search'] ?? '';
$where = "";
if ($search != "") {
    $where = "WHERE name LIKE '%$search%' OR category LIKE '%$search%'";
}

// ---------- PAGINATION ----------
$limit = 5;
$page = $_GET['page'] ?? 1;
$start = ($page-1)*$limit;

// Total count
$total_q = mysqli_query($connection, "SELECT COUNT(*) as total FROM skills $where");
$total = mysqli_fetch_assoc($total_q)['total'];
$pages = ceil($total/$limit);

// Fetch skills
$q = mysqli_query($connection, "SELECT * FROM skills $where ORDER BY id DESC LIMIT $start,$limit");

// ---------- KPI ----------
$total_skills = mysqli_fetch_assoc(mysqli_query($connection,"SELECT COUNT(*) as c FROM skills"))['c'];
$active_skills = mysqli_fetch_assoc(mysqli_query($connection,"SELECT COUNT(*) as c FROM skills WHERE status='active'"))['c'];
$pending_skills = mysqli_fetch_assoc(mysqli_query($connection,"SELECT COUNT(*) as c FROM skills WHERE status='pending'"))['c'];
?>

<div class="container-fluid p-4">

    <!-- KPI CARDS -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center"><div class="card-body">
                <h5>Total Skills</h5>
                <h3><?= $total_skills ?></h3>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card text-center"><div class="card-body">
                <h5>Active</h5>
                <h3><?= $active_skills ?></h3>
            </div></div>
        </div>
        <div class="col-md-3">
            <div class="card text-center"><div class="card-body">
                <h5>Pending</h5>
                <h3><?= $pending_skills ?></h3>
            </div></div>
        </div>
    </div>

    <!-- SEARCH + ADD SKILL -->
    <div class="d-flex justify-content-between mb-3">
        <form class="d-flex" method="get" action="public.php">
            <input type="hidden" name="skills" value="1">
            <input type="text" name="search" value="<?= $search ?>" class="form-control me-2" placeholder="Search skill">
            <button class="btn btn-primary">Search</button>
        </form>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">+ Add Skill</button>
    </div>

    <!-- SKILLS TABLE -->
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th><th>Name</th><th>Category</th><th>Status</th><th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $i=0;
                    while($row=mysqli_fetch_assoc($q)){ 
                        $i++;
                        ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td><?= $row['name'] ?></td>
                        <td><?= $row['category'] ?></td>
                        <td><?= $row['status'] ?></td>
                        <td>
                            <button class="btn btn-sm btn-info" 
                                data-bs-toggle="modal" data-bs-target="#viewModal"
                                data-id="<?= $row['id'] ?>" 
                                data-name="<?= $row['name'] ?>"
                                data-category="<?= $row['category'] ?>"
                                data-status="<?= $row['status'] ?>">
                                View
                            </button>
                            <button class="btn btn-sm btn-warning" 
                                data-bs-toggle="modal" data-bs-target="#editModal"
                                data-id="<?= $row['id'] ?>" 
                                data-name="<?= $row['name'] ?>"
                                data-category="<?= $row['category'] ?>"
                                data-status="<?= $row['status'] ?>">
                                Edit
                            </button>
                            <a href="../Skill_Swap/code.php?delete_skill=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                               onclick="return confirm('Delete this skill?')">Delete</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <!-- PAGINATION -->
            <nav>
                <ul class="pagination">
                    <?php for($i=1;$i<=$pages;$i++){ ?>
                        <li class="page-item <?= ($page==$i)?'active':'' ?>">
                            <a class="page-link" href="?skills&page=<?= $i ?>&search=<?= $search ?>"><?= $i ?></a>
                        </li>
                    <?php } ?>
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- ADD SKILL MODAL -->
<div class="modal fade" id="addModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="../Skill_Swap/code.php">
        <div class="modal-header"><h5>Add Skill</h5></div>
        <div class="modal-body">
            <input name="name" class="form-control mb-2" placeholder="Skill Name">
            <input name="category" class="form-control mb-2" placeholder="Category">
            <select name="status" class="form-control mb-2">
                <option>active</option><option>pending</option><option>disabled</option>
            </select>
        </div>
        <div class="modal-footer"><button class="btn btn-primary" name="add_skill">Save</button></div>
      </form>
    </div>
  </div>
</div>

<!-- EDIT SKILL MODAL -->
<div class="modal fade" id="editModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="../Skill_Swap/code.php">
        <div class="modal-header"><h5>Edit Skill</h5></div>
        <div class="modal-body">
            <input type="hidden" name="id" id="e-id">
            <input name="name" id="e-name" class="form-control mb-2">
            <input name="category" id="e-category" class="form-control mb-2">
            <select name="status" id="e-status" class="form-control mb-2">
                <option>active</option><option>pending</option><option>disabled</option>
            </select>
        </div>
        <div class="modal-footer"><button class="btn btn-primary" name="edit_skill">Update</button></div>
      </form>
    </div>
  </div>
</div>

<!-- VIEW SKILL MODAL -->
<div class="modal fade" id="viewModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5>Skill Details</h5></div>
      <div class="modal-body">
        <p><b>ID:</b> <span id="v-id"></span></p>
        <p><b>Name:</b> <span id="v-name"></span></p>
        <p><b>Category:</b> <span id="v-category"></span></p>
        <p><b>Status:</b> <span id="v-status"></span></p>
      </div>
    </div>
  </div>
</div>

<script>
// Fill Edit Modal
var editModal=document.getElementById('editModal');
editModal.addEventListener('show.bs.modal', function(event){
  var btn=event.relatedTarget;
  document.getElementById('e-id').value=btn.getAttribute('data-id');
  document.getElementById('e-name').value=btn.getAttribute('data-name');
  document.getElementById('e-category').value=btn.getAttribute('data-category');
  document.getElementById('e-status').value=btn.getAttribute('data-status');
});

// Fill View Modal
var viewModal=document.getElementById('viewModal');
viewModal.addEventListener('show.bs.modal', function(event){
  var btn=event.relatedTarget;
  document.getElementById('v-id').innerText=btn.getAttribute('data-id');
  document.getElementById('v-name').innerText=btn.getAttribute('data-name');
  document.getElementById('v-category').innerText=btn.getAttribute('data-category');
  document.getElementById('v-status').innerText=btn.getAttribute('data-status');
});
</script>
