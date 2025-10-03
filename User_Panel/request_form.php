<?php
// include("../Skill_Swap/code.php");
$me = $_SESSION['user_id'] ?? 1; // testing fallback
$provider = intval($_GET['provider_id'] ?? $_POST['provider_id'] ?? 0);
$skill_id = intval($_GET['skill_id'] ?? $_POST['skill_id'] ?? 0);

// if POST submitted, handle in code.php; here we only show form
// fetch provider info
$prov = mysqli_fetch_assoc(mysqli_query($connection, "SELECT id,username,city FROM registers WHERE id=$provider"));
$skill = mysqli_fetch_assoc(mysqli_query($connection, "SELECT id,name FROM skills WHERE id=$skill_id"));
?>
<div class="container p-4">
  <div class="card">
    <div class="card-header"><b>Send Request to <?= htmlspecialchars($prov['username'] ?? 'Provider') ?></b></div>
    <div class="card-body">
      <form method="post" action="../Skill_Swap/code.php">
        <input type="hidden" name="provider_id" value="<?= $provider ?>">
        <input type="hidden" name="skill_id" value="<?= $skill_id ?>">
        <div class="mb-2"><label>Skill</label><input class="form-control" value="<?= htmlspecialchars($skill['name'] ?? '') ?>" disabled></div>
        <div class="mb-2"><label>Message</label><textarea name="message" class="form-control" required></textarea></div>
        <div class="mb-2"><label>Preferred time (optional)</label><input name="scheduled_at" class="form-control" placeholder="YYYY-MM-DD HH:MM"></div>
        <button class="btn btn-primary" name="create_request">Send Request</button>
      </form>
    </div>
  </div>
</div>
