<?php

// ---------- KPIs ----------
$user_id = $_SESSION['user_id']; // (yeh login user ka ID hoga, baad me $_SESSION se lena)

// Count user skills
$total_skills = mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as c FROM user_skills WHERE user_id=$user_id"))['c'];

// Requests sent
$sent = mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as c FROM requests WHERE requester_id=$user_id"))['c'];

// Requests received
$received = mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as c FROM requests WHERE provider_id=$user_id"))['c'];

// Completed swaps count
$completed_q = mysqli_query($connection, "
    SELECT COUNT(*) as c 
    FROM swaps s
    JOIN requests r ON s.request_id = r.id
    WHERE (r.requester_id = $user_id OR r.provider_id = $user_id) 
      AND s.status = 'completed'
");
$completed = mysqli_fetch_assoc($completed_q)['c'];

// Fetch recent skills
$skills_q = mysqli_query($connection, "SELECT us.id, s.name, s.category, us.level 
                                      FROM user_skills us 
                                      JOIN skills s ON us.skill_id=s.id 
                                      WHERE us.user_id=$user_id LIMIT 5");

// Fetch recent requests
$completed_swaps = mysqli_query($connection, "
  SELECT s.id as swap_id, 
       u1.username AS requester_name, 
       u2.username AS provider_name, 
       sk.name AS skill_name,
       s.started_at, 
       s.finished_at,
       s.status
FROM swaps s
JOIN requests r ON s.request_id = r.id
JOIN registers u1 ON r.requester_id = u1.id
JOIN registers u2 ON r.provider_id = u2.id
JOIN skills sk ON r.skill_id = sk.id
WHERE s.status !=' '
ORDER BY s.finished_at ASC limit 5
");

?>

<div class="container-fluid p-4">

    <!-- KPI CARDS -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5>My Skills</h5>
                    <h3><?= $total_skills ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5>Requests Sent</h5>
                    <h3><?= $sent ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5>Requests Received</h5>
                    <h3><?= $received ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5>Completed Swaps</h5>
                    <h3><?= $completed ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- MY SKILLS TABLE -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between">
            <h5>My Skills</h5>
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addSkillModal">+ Add
                Skill</button>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Level</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($s = mysqli_fetch_assoc($skills_q)) { ?>
                        <tr>
                            <td><?= $s['name'] ?></td>
                            <td><?= $s['category'] ?></td>
                            <td><?= $s['level'] ?></td>
                            <td>
                                <a href="../Skill_Swap/code.php?delete_user_skill=<?= $s['id'] ?>"
                                    class="btn btn-danger btn-sm" onclick="return confirm('Delete skill?')">Delete</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>


    <!-- RECENT REQUESTS TABLE -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Recent Swap Requests</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Swap No</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Skill</th>
                        <th>Started at</th>
                        <th>Completed at</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 0;
                    while ($row = mysqli_fetch_assoc($completed_swaps)) {
                        $i++;
                        echo "<tr>
            <td>
{$i}
            </td>
            <td>{$row['requester_name']}</td>
            <td>{$row['provider_name']}</td>
            <td>{$row['skill_name']}</td>
            <td>{$row['started_at']}</td>
            <td>{$row['finished_at']}</td>
          </tr>";
                    }

                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Skill Modal -->
    <div class="modal fade" id="addSkillModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add a Skill</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Add from Dropdown -->
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Select Existing Skill</label>
                            <select name="skill_id" class="form-control" required>
                                <option value="">-- Select Skill --</option>
                                <?php while ($s = mysqli_fetch_assoc($skills)) { ?>
                                    <option value="<?php echo $s['id']; ?>"><?php echo $s['name']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Skill Level</label>
                            <select name="level" class="form-control" required>
                                <option value="">-- Select Level --</option>
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                        </div>
                        <button type="submit" name="add_user_skill" class="btn btn-success">Add to My Skills</button>
                    </form>

                    <hr>

                    <!-- Add New Skill -->
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Suggest New Skill</label>
                            <input type="text" name="new_skill" class="form-control" placeholder="Enter skill name"
                                required>
                        </div>
                        <button type="submit" name="add_new_skill" class="btn btn-warning">Submit for Approval</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    
</div>