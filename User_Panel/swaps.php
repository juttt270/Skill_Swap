<?php
include_once('../Skill_Swap/code.php');
$user_id = $_SESSION['user_id'];

// Fetch all swaps where user is requester or provider
$swaps_q = mysqli_query($connection, "
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
WHERE s.status!=' '
ORDER BY s.finished_at ASC
");
?>

<div class="container-fluid p-4">

    <div class="card mb-4">
        <div class="card-header">
            <h5>My Swaps</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Swap ID</th>
                        <th>Skill</th>
                        <th>Requester</th>
                        <th>Provider</th>
                        <th>Status</th>
                        <th>Started At</th>
                        <th>Finished At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($swap = mysqli_fetch_assoc($swaps_q)) { ?>
                        <tr>
                            <td><?= $swap['swap_id'] ?></td>
                            <td><?= $swap['skill_name'] ?></td>
                            <td><?= $swap['requester_name'] ?></td>
                            <td><?= $swap['provider_name'] ?></td>
                            <td><?= $swap['status'] ?></td>
                            <td><?= $swap['started_at'] ?></td>
                            <td><?= $swap['finished_at'] ?? '-' ?></td>
                            <td>
                                <?php if ($swap['status'] == 'ongoing') { ?>
                                    <a href="swaps.php?complete=<?= $swap['swap_id'] ?>" class="btn btn-success btn-sm"
                                        onclick="return confirm('Mark as completed?')">Complete</a>
                                <?php } elseif ($swap['status'] == 'discussion') { ?>
                                    <a href="swaps.php?start=<?= $swap['swap_id'] ?>" class="btn btn-success btn-sm"
                                        onclick="return confirm('Start Swap?')">Start Swap</a>
                                    <?php
                                } else {
                                    echo 'Done';
                                } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>