<?php
include_once '../Skill_Swap/code.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Admin_Panel/signin.php");
    exit;
}
$user_id = intval($_SESSION['user_id']);

// ---------- HANDLE SUBMIT ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_feedback'])) {
    $swap_id = intval($_POST['swap_id'] ?? 0);
    $to_user = intval($_POST['to_user_id'] ?? 0);
    $rating = intval($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');

    if ($swap_id > 0 && $to_user > 0 && $rating >= 1 && $rating <= 5) {
        $check = mysqli_fetch_assoc(mysqli_query($connection, "
            SELECT s.id, rq.requester_id, rq.provider_id
            FROM swaps s JOIN requests rq ON rq.id = s.request_id
            WHERE s.id = $swap_id AND s.status = 'completed'
            LIMIT 1
        "));
        if ($check && ($check['requester_id'] == $user_id || $check['provider_id'] == $user_id)) {
            $exists = mysqli_fetch_assoc(mysqli_query($connection, "
                SELECT COUNT(*) as c FROM feedbacks
                WHERE swap_id = $swap_id AND from_user_id = $user_id
            "));
            if ($exists && intval($exists['c']) === 0) {
                $rating_db = intval($rating);
                $comment_db = mysqli_real_escape_string($connection, $comment);
                mysqli_query($connection, "
                    INSERT INTO feedbacks (swap_id, from_user_id, to_user_id, rating, comment)
                    VALUES ($swap_id, $user_id, $to_user, $rating_db, '$comment_db')
                ");
                $fid = mysqli_insert_id($connection);

                // add notification
                $short = substr($comment, 0, 120);
                $title = "New feedback received";
                $body = "You received $rating_db star(s) from " . ($_SESSION['username'] ?? 'A user') . ".";
                if ($short) $body .= " Comment: " . $short;
                $url = "public.php?feedback=" . $user_id;
                if (function_exists('add_notification')) {
                    add_notification($connection, $to_user, 'feedback', $swap_id, $title, $body, $url);
                }

                $_SESSION['flash_success'] = "Feedback submitted.";
            } else {
                $_SESSION['flash_error'] = "You have already submitted feedback for this swap.";
            }
        } else {
            $_SESSION['flash_error'] = "Invalid swap or not allowed.";
        }
    } else {
        $_SESSION['flash_error'] = "Invalid input.";
    }

    echo "<script>location.assign('public.php?feedback');</script>";
    exit;
}

// ---------- DATA for display ----------
// Pending feedbacks
$pending_q = mysqli_query($connection, "
    SELECT s.id AS swap_id, rq.skill_id, sk.name AS skill_name, sk.category,
           rq.requester_id, rq.provider_id,
           IF(rq.requester_id = $user_id, rq.provider_id, rq.requester_id) AS other_id
    FROM swaps s
    JOIN requests rq ON rq.id = s.request_id
    JOIN skills sk ON sk.id = rq.skill_id
    LEFT JOIN feedbacks f ON f.swap_id = s.id AND f.from_user_id = $user_id
    WHERE s.status = 'completed'
      AND (rq.requester_id = $user_id OR rq.provider_id = $user_id)
      AND f.id IS NULL
    ORDER BY s.id DESC
");

// Received feedbacks
$received_q = mysqli_query($connection, "
    SELECT f.*, u.username AS from_username, u.profile_picture
    FROM feedbacks f
    LEFT JOIN registers u ON u.id = f.from_user_id
    WHERE f.to_user_id = $user_id
    ORDER BY f.created_at DESC
");

// Given feedbacks
$given_q = mysqli_query($connection, "
    SELECT f.*, u.username AS to_username, u.profile_picture
    FROM feedbacks f
    LEFT JOIN registers u ON u.id = f.to_user_id
    WHERE f.from_user_id = $user_id
    ORDER BY f.created_at DESC
");

// Average rating
$avg_row = mysqli_fetch_assoc(mysqli_query($connection, "SELECT AVG(rating) AS avg_rating, COUNT(*) AS total FROM feedbacks WHERE to_user_id = $user_id"));
$avg_rating = round(floatval($avg_row['avg_rating'] ?? 0), 2);
$total_fb = intval($avg_row['total'] ?? 0);

// Flash messages
$flash_s = $_SESSION['flash_success'] ?? null;
$flash_e = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>

<div class="container-fluid p-4">
  <div class="row mb-3">
    <div class="col-md-6">
      <h4>Feedback & Ratings</h4>
    </div>
    <div class="col-md-6 text-end">
      <div>Average rating: <strong><?= $avg_rating ?></strong> (<?= $total_fb ?> reviews)</div>
    </div>
  </div>

  <?php if ($flash_s): ?>
    <div class="alert alert-success"><?= htmlspecialchars($flash_s) ?></div>
  <?php endif; ?>
  <?php if ($flash_e): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($flash_e) ?></div>
  <?php endif; ?>

  <div class="row">
    <!-- LEFT: Pending feedbacks -->
    <div class="col-md-5">
      <div class="card mb-3">
        <div class="card-header"><b>Feedback to give</b></div>
        <div class="card-body">
          <?php if (mysqli_num_rows($pending_q) == 0): ?>
            <div class="text-muted">No pending feedbacks.</div>
          <?php else: ?>
            <?php while ($p = mysqli_fetch_assoc($pending_q)): 
                $other_id = intval($p['other_id']);
                $other = mysqli_fetch_assoc(mysqli_query($connection, "SELECT id, username, profile_picture FROM registers WHERE id = $other_id LIMIT 1"));
            ?>
              <div class="d-flex align-items-start mb-3">
                <img src="../Skill_Swap/<?= htmlspecialchars($other['profile_picture'] ?? 'img/user.jpg') ?>" class="rounded-circle me-2" width="48" height="48" alt="">
                <div>
                  <div><strong><?= htmlspecialchars($other['username'] ?? 'User') ?></strong></div>
                  <div class="text-muted small"><?= htmlspecialchars($p['skill_name']) ?> (<?= htmlspecialchars($p['category']) ?>)</div>
                  <div class="mt-2">
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#feedModal" 
                        data-swap="<?= $p['swap_id'] ?>" data-to="<?= $other_id ?>" data-name="<?= htmlspecialchars($other['username'] ?? '') ?>" data-skill="<?= htmlspecialchars($p['skill_name']) ?>">
                      Give Feedback
                    </button>
                  </div>
                </div>
              </div>
            <?php endwhile; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- RIGHT: Received & Given Feedbacks -->
    <div class="col-md-7">
      <!-- Received -->
      <div class="card mb-3">
        <div class="card-header"><b>Received Feedbacks</b></div>
        <div class="card-body">
          <?php if (mysqli_num_rows($received_q) == 0): ?>
            <div class="text-muted">No feedbacks yet.</div>
          <?php else: ?>
            <?php while ($r = mysqli_fetch_assoc($received_q)): ?>
              <div class="mb-3">
                <div><strong><?= htmlspecialchars($r['from_username']) ?></strong>
                  <span class="text-muted small">• <?= htmlspecialchars($r['created_at']) ?></span>
                </div>
                <div>Rating: <?= str_repeat('★', intval($r['rating'])) . str_repeat('☆', 5 - intval($r['rating'])) ?></div>
                <?php if ($r['comment']): ?>
                  <div class="mt-1"><?= nl2br(htmlspecialchars($r['comment'])) ?></div>
                <?php endif; ?>
                <hr>
              </div>
            <?php endwhile; ?>
          <?php endif; ?>
        </div>
      </div>
 </div>
      <!-- Given -->
      <div class="card mb-3">
        <div class="card-header"><b>Feedbacks Given</b></div>
        <div class="card-body">
          <?php if (mysqli_num_rows($given_q) == 0): ?>
            <div class="text-muted">You haven't given any feedback yet.</div>
          <?php else: ?>
            <?php while ($g = mysqli_fetch_assoc($given_q)): ?>
              <div class="mb-3">
                <div><strong><?= htmlspecialchars($g['to_username']) ?></strong>
                  <span class="text-muted small">• <?= htmlspecialchars($g['created_at']) ?></span>
                </div>
                <div>Rating: <?= str_repeat('★', intval($g['rating'])) . str_repeat('☆', 5 - intval($g['rating'])) ?></div>
                <?php if ($g['comment']): ?>
                  <div class="mt-1"><?= nl2br(htmlspecialchars($g['comment'])) ?></div>
                <?php endif; ?>
                <hr>
              </div>
            <?php endwhile; ?>
          <?php endif; ?>
        </div>
      </div>
   
  </div>
</div>

<!-- FEEDBACK MODAL -->
<div class="modal fade" id="feedModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Give Feedback</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="swap_id" id="fm-swap" value="">
        <input type="hidden" name="to_user_id" id="fm-to" value="">
        <div class="mb-2">
          <label class="form-label">To</label>
          <input type="text" id="fm-name" class="form-control" readonly>
        </div>
        <div class="mb-2">
          <label class="form-label">Skill</label>
          <input type="text" id="fm-skill" class="form-control" readonly>
        </div>
        <div class="mb-2">
          <label class="form-label">Rating</label>
          <select name="rating" class="form-select" required>
            <option value="">Select</option>
            <option value="5">5 — Excellent</option>
            <option value="4">4 — Very good</option>
            <option value="3">3 — Good</option>
            <option value="2">2 — Fair</option>
            <option value="1">1 — Poor</option>
          </select>
        </div>
        <div class="mb-2">
          <label class="form-label">Comment (optional)</label>
          <textarea name="comment" class="form-control" rows="4"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="submit_feedback" class="btn btn-primary">Submit Feedback</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function(){
  var feedModal = document.getElementById('feedModal');
  feedModal.addEventListener('show.bs.modal', function (event) {
    var btn = event.relatedTarget;
    var swap = btn.getAttribute('data-swap');
    var to = btn.getAttribute('data-to');
    var name = btn.getAttribute('data-name');
    var skill = btn.getAttribute('data-skill');
    document.getElementById('fm-swap').value = swap;
    document.getElementById('fm-to').value = to;
    document.getElementById('fm-name').value = name;
    document.getElementById('fm-skill').value = skill;
  });
});
</script>
