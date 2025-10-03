<?php
include_once '../Skill_Swap/code.php';
if (!isset($_SESSION['user_id'])) { header("Location: ../Admin_Panel/signin.php"); exit; }
$user_id = intval($_SESSION['user_id']);

// ---------------- HANDLE PROFILE UPDATE ----------------
$flash_success = $flash_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1) Update Info
    if (isset($_POST['update_info'])) {
        // Collect all possible user fields
        $data = [
            'username' => $_POST['username'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'bio' => $_POST['bio'] ?? '',
            'role' => $_POST['role'] ?? 'user'
        ];
        if (update_profile($connection, $user_id, $data)) {
            $flash_success = "Profile info updated.";
            $_SESSION['username'] = $data['username']; // update session
        } else $flash_error = "Failed to update profile.";
    }

    // 2) Change Password
    if (isset($_POST['change_password'])) {
        $old = $_POST['old_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        if ($new !== $confirm) $flash_error = "New passwords do not match.";
        else {
            $res = change_password($connection, $user_id, $old, $new);
            if (isset($res['success'])) $flash_success = $res['success'];
            else $flash_error = $res['error'] ?? 'Error changing password.';
        }
    }

    // 3) Profile Picture
    if (isset($_POST['upload_picture'])) {
        $res = update_profile_picture($connection, $user_id, $_FILES['profile_picture']);
        if (isset($res['success'])) $flash_success = $res['success'];
        else $flash_error = $res['error'] ?? 'Error uploading picture.';
    }
}

// Fetch user data
$user = mysqli_fetch_assoc(mysqli_query($connection, "SELECT * FROM registers WHERE id=$user_id"));
$rating = get_user_rating($connection, $user_id);
?>

<div class="container-fluid p-4">
    <div class="row mb-3">
        <div class="col-12"><h4>My Profile</h4></div>
    </div>

    <?php if($flash_success): ?><div class="alert alert-success"><?= htmlspecialchars($flash_success) ?></div><?php endif; ?>
    <?php if($flash_error): ?><div class="alert alert-danger"><?= htmlspecialchars($flash_error) ?></div><?php endif; ?>

    <div class="row">
        <!-- LEFT: Profile Picture + Rating -->
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header"><b>Profile Picture</b></div>
                <div class="card-body text-center">
                    <img src="../Skill_Swap/<?= htmlspecialchars($user['profile_picture'] ?? 'img/user.jpg') ?>" class="rounded-circle mb-2" width="120" height="120">
                    <form method="post" enctype="multipart/form-data">
                        <input type="file" name="profile_picture" class="form-control mb-2" required>
                        <button type="submit" name="upload_picture" class="btn btn-primary btn-sm">Upload</button>
                    </form>
                    <div class="mt-3">
                        <div>Role: <strong><?= htmlspecialchars($user['role']) ?></strong></div>
                        <div>Member since: <?= htmlspecialchars($user['created_at']) ?></div>
                        <div class="mt-2">Average Rating: <strong><?= $rating['avg'] ?></strong> (<?= $rating['total'] ?> reviews)</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: Info + Password -->
        <div class="col-md-8">
            <!-- Update Info -->
            <div class="card mb-3">
                <div class="card-header"><b>Profile Info</b></div>
                <div class="card-body">
                    <form method="post">
                        <div class="mb-2">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Bio</label>
                            <textarea name="bio" class="form-control" rows="3"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select">
                                <option value="learner" <?= ($user['role']=='learner')?'selected':'' ?>>Learner</option>
                                <option value="trainer" <?= ($user['role']=='trainer')?'selected':'' ?>>Trainer</option>
                                <option value="both" <?= ($user['role']=='both')?'selected':'' ?>>Both</option>
                            </select>
                        </div>
                        <button type="submit" name="update_info" class="btn btn-primary btn-sm">Update Info</button>
                    </form>
                </div>
            </div>

            <!-- Change Password -->
            <div class="card mb-3">
                <div class="card-header"><b>Change Password</b></div>
                <div class="card-body">
                    <form method="post">
                        <div class="mb-2">
                            <label class="form-label">Old Password</label>
                            <input type="password" name="old_password" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">New Password</label>
                            <input type="password" name="new_password" class="form-control" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                        <button type="submit" name="change_password" class="btn btn-warning btn-sm">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>