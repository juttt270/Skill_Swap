<?php
include_once '../Skill_Swap/code.php';
if (!isset($_SESSION['user_id'])) header("Location: ../Admin_Panel/signin.php");
$uid = intval($_SESSION['user_id']);
$res = mysqli_query($connection, "SELECT * FROM notifications WHERE user_id=$uid ORDER BY created_at DESC");
?>
<!doctype html><html><head><title>Notifications</title><link rel="stylesheet" href="css/bootstrap.min.css"></head><body>
<div class="container py-4">
    <h3>All Notifications</h3>
    <ul class="list-group mt-3">
        <?php while($n = mysqli_fetch_assoc($res)): ?>
            <li class="list-group-item <?= $n['is_read'] ? '' : 'list-group-item-warning' ?>">
                <a href="notify.php?id=<?= $n['id'] ?>&redirect=<?= urlencode($n['url']?:'public.php') ?>" class="text-decoration-none">
                    <div><strong><?= htmlspecialchars($n['title']) ?></strong></div>
                    <div class="small text-muted"><?= htmlspecialchars($n['body'] ?? '') ?></div>
                    <div class="small text-muted"><?= $n['created_at'] ?></div>
                </a>
            </li>
        <?php endwhile; ?>
    </ul>
</div>
</body></html>
