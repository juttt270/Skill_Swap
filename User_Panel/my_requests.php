<?php
$loggedInUser = $_SESSION['user_id']; // assume login ke time set kiya tha

// fetch requests by this user
$sql = "SELECT r.id, s.name AS skill_name, p.username AS provider_name, r.status, r.message, r.requested_at
        FROM requests r
        JOIN skills s ON r.skill_id = s.id
        JOIN registers p ON r.provider_id = p.id
        WHERE r.requester_id = $loggedInUser
        ORDER BY r.requested_at DESC";
$requests = mysqli_query($connection, $sql);
?>

<div class="container p-4">
  <h3 class="mb-3">My Requests</h3>
  <table class="table table-bordered table-striped">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Skill</th>
        <th>Provider</th>
        <th>Status</th>
        <th>Message</th>
        <th>Requested At</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if(mysqli_num_rows($requests) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($requests)): ?>
          <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['skill_name']) ?></td>
            <td><?= htmlspecialchars($row['provider_name']) ?></td>
            <td>
              <?php if($row['status']=='pending'): ?>
                <span class="badge bg-warning">Pending</span>
              <?php elseif($row['status']=='accepted'): ?>
                <span class="badge bg-success">Accepted</span>
              <?php elseif($row['status']=='under_discussion'): ?>
                <span class="badge bg-primary">Under_Discussion</span>
              <?php else: ?>
                <span class="badge bg-danger">Rejected</span>
              <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($row['message']) ?></td>
            <td><?= $row['requested_at'] ?></td>
            <td>
              <!-- view -->
              <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#view<?= $row['id'] ?>">View</button>
              <!-- edit -->
              <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#edit<?= $row['id'] ?>">Edit</button>
              <!-- delete -->
              <form method="post" action="../Skill_Swap/code.php" style="display:inline;">
                <input type="hidden" name="delete_request_id" value="<?= $row['id'] ?>">
                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Cancel this request?')">Delete</button>
              </form>
            </td>
          </tr>

          <!-- view modal -->
          <div class="modal fade" id="view<?= $row['id'] ?>" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header"><h5>Request #<?= $row['id'] ?></h5></div>
                <div class="modal-body">
                  <p><b>Skill:</b> <?= htmlspecialchars($row['skill_name']) ?></p>
                  <p><b>Provider:</b> <?= htmlspecialchars($row['provider_name']) ?></p>
                  <p><b>Status:</b> <?= htmlspecialchars($row['status']) ?></p>
                  <p><b>Message:</b> <?= htmlspecialchars($row['message']) ?></p>
                  <p><b>Date:</b> <?= $row['requested_at'] ?></p>
                </div>
              </div>
            </div>
          </div>

          <!-- edit modal -->
          <div class="modal fade" id="edit<?= $row['id'] ?>" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <form method="post" action="../Skill_Swap/code.php">
                  <div class="modal-header"><h5>Edit Request</h5></div>
                  <div class="modal-body">
                    <input type="hidden" name="edit_request_id" value="<?= $row['id'] ?>">
                    <div class="mb-3">
                      <label>Message</label>
                      <textarea name="message" class="form-control"><?= htmlspecialchars($row['message']) ?></textarea>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button class="btn btn-success">Update</button>
                  </div>
                </form>
              </div>
            </div>
          </div>

        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="7">No requests found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
