<?php
include_once '../Skill_Swap/code.php';
?>

<div class="container mt-5">
    <h3 class="mb-4">My Skills</h3>

    <!-- List of User Skills -->
    <div class="row">
        <?php while ($row = mysqli_fetch_assoc($user_skills)) { ?>
            <div class="col-md-3">
                <div class="card p-3 text-center shadow-sm mb-3">
                    <h6><?php echo $row['name']; ?></h6>
                    <small class="text-muted"><?php echo ucfirst($row['level']); ?></small>
                </div>
            </div>
        <?php } ?>
    </div>

    <!-- Add Skill Button -->
    <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addSkillModal">+ Add Skill</button>
</div>

<!-- Add Skill Modal -->
<div class="modal fade" id="addSkillModal" tabindex="-1">
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
                    <input type="text" name="new_skill" class="form-control" placeholder="Enter skill name" required>
                </div>
                <button type="submit" name="add_new_skill" class="btn btn-warning">Submit for Approval</button>
            </form>
        </div>
    </div>
  </div>
</div>
