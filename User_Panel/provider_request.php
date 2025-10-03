<?php
// provider_request.php
// Shows incoming requests for provider and action buttons (Start Discussion / Confirm / Reject)

if (!isset($_SESSION)) session_start();
$user = intval($_SESSION['user_id'] ?? 0);

// fetch requests for this provider and any existing swap id
$q = mysqli_query($connection, "
    SELECT r.*, s.name AS skill_name, s.category AS skill_category, u.username AS requester_name,
           (SELECT id FROM swaps WHERE request_id = r.id LIMIT 1) AS swap_id
    FROM requests r
    JOIN skills s ON s.id = r.skill_id
    JOIN registers u ON u.id = r.requester_id
    WHERE r.provider_id = $user
    ORDER BY r.requested_at DESC
");
?>
<div class="container p-4">
  <div class="card">
    <div class="card-header"><b>Incoming Requests</b></div>
    <div class="card-body table-responsive">
      <table class="table table-striped table-sm">
        <thead>
          <tr>
            <th>ID</th>
            <th>From</th>
            <th>Skill</th>
            <th>Category</th>
            <th>Message</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php while($r = mysqli_fetch_assoc($q)): ?>
            <tr>
              <td><?= intval($r['id']) ?></td>
              <td><?= htmlspecialchars($r['requester_name']) ?></td>
              <td><?= htmlspecialchars($r['skill_name']) ?></td>
              <td><?= htmlspecialchars($r['skill_category']) ?></td>
              <td><?= htmlspecialchars(mb_substr($r['message'], 0, 80)) ?></td>
              <td><?= htmlspecialchars($r['status']) ?></td>
              <td>
                <?php
                  $status = $r['status'];
                  $req_id = intval($r['id']);
                  $swap_id = intval($r['swap_id'] ?? 0);

                  if ($status === 'pending') {
                      // Start discussion: create a swap (discussion) and let them chat before accept
                      echo '<a href="public.php?start_discussion=' . $req_id . '" class="btn btn-primary btn-sm">Start Discussion</a>';
                  } elseif ($status === 'under_discussion') {
                      // If swap exists, open chat; also allow confirm or reject
                      if ($swap_id > 0) {
                          echo '<a href="public.php?chat&swap_id=' . $swap_id . '" class="btn btn-info btn-sm me-1">Open Chat</a>';
                      } else {
                          // fallback: start discussion (shouldn't normally happen)
                          echo '<a href="public.php?start_discussion=' . $req_id . '" class="btn btn-secondary btn-sm me-1">Start / Open</a>';
                      }
                      echo '<a href="public.php?confirm_swap=' . $req_id . '" class="btn btn-success btn-sm me-1">Confirm Request</a>'.
                      '<a href="public.php?reject_request=' . $req_id . '" class="btn btn-danger btn-sm">Reject</a>';
                    
                  } elseif ($status === 'accepted') {
                      // accepted → swap should exist, show view swap
                      if ($swap_id > 0) {
                          echo '<a href="public.php?swaps&swap_id=' . $swap_id . '" class="btn btn-info btn-sm">View Swap</a>';
                      } else {
                          echo '<a href="public.php?confirm_swap=' . $req_id . '" class="btn btn-success btn-sm">Ensure Swap</a>';
                      }
                  } elseif ($status === 'rejected') {
                      echo '<span class="text-muted small">Rejected</span>';
                  } else {
                      // unknown statuses
                      echo '<span class="text-muted small">' . htmlspecialchars($status) . '</span>';
                  }
                ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
