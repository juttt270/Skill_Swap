<?php

// optional search by skill name
$skill_search = $_GET['skill_search'] ?? '';
$where_skill = $skill_search ? "WHERE name LIKE '%".mysqli_real_escape_string($connection,$skill_search)."%'" : "";

// fetch skills
$skills_q = mysqli_query($connection, "SELECT * FROM skills where status = 'active' $where_skill ORDER BY name");

// if skill selected, show providers
$skill_id = intval($_GET['skill_id'] ?? 0);
$providers_q = null;
if ($skill_id>0) {
    // providers from user_skills (only approved)
    $sql = "SELECT us.id AS us_id, r.id AS user_id, r.username, r.city, r.country, us.level
            FROM user_skills us
            JOIN registers r ON r.id = us.user_id
            WHERE us.skill_id = $skill_id
            ORDER BY r.username";
    $providers_q = mysqli_query($connection, $sql);
}
?>
<div class="container p-4">
  <div class="row mb-3">
    <div class="col-md-6">
      <form method="get" class="d-flex">
        <input name="skill_search" value="<?= htmlspecialchars($skill_search) ?>" class="form-control me-2" placeholder="Search skills...">
        <button class="btn btn-primary">Search</button>
      </form>
    </div>
  </div>

  <div class="row">
    <div class="col-md-4">
      <div class="card">
        <div class="card-header"><b>Skills</b></div>
        <div class="list-group list-group-flush">
          <?php while($s=mysqli_fetch_assoc($skills_q)): ?>
            <a class="list-group-item list-group-item-action <?= ($s['id']==$skill_id)?'active':'' ?>"
               href="public.php?browseskill=1&skill_id=<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?> <small class="text-muted"> (<?= htmlspecialchars($s['category']) ?>)</small></a>
          <?php endwhile; ?>
        </div>
      </div>
    </div>

    <div class="col-md-8">
      <div class="card">
        <div class="card-header"><b>Providers</b></div>
        <div class="card-body">
          <?php if (!$skill_id): ?>
            <p>Select a skill to see providers.</p>
          <?php else: ?>
            <?php if ($providers_q && mysqli_num_rows($providers_q)>0): ?>
              <table class="table">
                <thead><tr><th>Name</th><th>City</th><th>Level</th><th>Action</th></tr></thead>
                <tbody>
                  <?php while($p=mysqli_fetch_assoc($providers_q)): ?>
                    <tr>
                      <td><?= htmlspecialchars($p['username']) ?></td>
                      <td><?= htmlspecialchars($p['city']) ?></td>
                      <td><?= htmlspecialchars($p['level']) ?></td>
                      <td>
                        <!-- go to request form -->
                        <a href="public.php?requestform=1&provider_id=<?= $p['user_id'] ?>&skill_id=<?= $skill_id ?>" class="btn btn-sm btn-primary">Request</a>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                </tbody>
              </table>
            <?php else: ?>
              <p>No providers found for this skill.</p>
            <?php endif; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
