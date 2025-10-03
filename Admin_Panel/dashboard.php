<?php
// admin/dashboard.php
// Make sure this file is included in your Mentor admin template content area.
// It expects ../Skill_Swap/code.php to define $connection and the helper functions.

include_once '../Skill_Swap/code.php';

// optional admin check (if needed)
// if (!is_admin_session()) { die('Access denied'); }

$stats = get_admin_dashboard_stats($connection);

// convenience variables
$total_users = $stats['total_users'];
$active_now = $stats['active_now'];
$new_today = $stats['new_today'];
$new_7days = $stats['new_7days'];
$signupTrend = $stats['signup_trend'];

$ongoing_swaps = $stats['ongoing_swaps'];
$completed_swaps = $stats['completed_swaps'];
$pending_requests = $stats['pending_requests'];
$new_feedback = $stats['new_feedback'];
$open_reports = $stats['open_reports'];

$revenue_today = number_format($stats['revenue_today'], 2);
$revenue_month = number_format($stats['revenue_month'], 2);
$conversion_rate = $stats['conversion_rate'];

$recent_users = $stats['recent_users'];
$top_skills = $stats['top_skills'];
$skills_by_category = $stats['skills_by_category'];
$requests_month = $stats['requests_month'];
$swaps_month = $stats['swaps_month'];

$mentor_color = '#5FCF80';
?>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    /* small visual polish sticking to Mentor theme */
    .kpi-card {
        background: rgba(255, 255, 255, 0.9);
        border-radius: 10px;
        padding: 14px;
        transition: transform .12s ease, box-shadow .12s ease;
        box-shadow: 0 6px 16px rgba(18, 38, 63, 0.06);
    }

    .kpi-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 36px rgba(18, 38, 63, 0.10);
    }

    .kpi-icon {
        font-size: 28px;
        opacity: 0.95;
    }

    .section-title {
        font-weight: 700;
        color: #222;
    }

    .small-muted {
        color: #6c757d;
        font-size: 0.85rem;
    }

    .chart-area {
        min-height: 260px;
    }
</style>

<div class="container-fluid pt-3 px-4 mb-2">
    <div class="d-flex align-items-center justify-content-between">
        <h4 class="mb-0">Dashboard</h4>
        <div>
            <select id="dateRange" class="form-select form-select-sm w-auto d-inline-block me-2">
                <option value="7">Last 7 days</option>
                <option value="30">Last 30 days</option>
                <option value="90">Last 90 days</option>
            </select>
            <input class="form-control form-control-sm w-50 d-inline-block" placeholder="Global search..."
                id="globalSearch">
        </div>
    </div>
</div>

<div class="container-fluid pt-2 px-4">
    <div class="row g-3">
        <!-- Users -->
        <div class="col-6 col-sm-4 col-md-3">
            <div class="kpi-card h-100">
                <div class="d-flex">
                    <div><i class="fa fa-users kpi-icon" style="color:<?= $mentor_color ?>;"></i></div>
                    <div class="ms-3">
                        <div class="small-muted">Total Users</div>
                        <div class="d-flex align-items-baseline">
                            <h5 class="mb-0 me-2"><?= $total_users ?></h5>
                            <small class="text-success">New <?= $new_today ?></small>
                        </div>
                        <div class="small-muted">Signups (7d): <?= $new_7days ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Now -->
        <div class="col-6 col-sm-4 col-md-3">
            <div class="kpi-card h-100">
                <div class="d-flex">
                    <div><i class="fa fa-bolt kpi-icon" style="color:#FFB020;"></i></div>
                    <div class="ms-3">
                        <div class="small-muted">Active Now (15m)</div>
                        <h5 class="mb-0"><?= htmlspecialchars((string) $active_now) ?></h5>
                        <div class="small-muted">Realtime estimate</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Requests -->
        <div class="col-6 col-sm-4 col-md-3">
            <div class="kpi-card h-100">
                <div class="d-flex">
                    <div><i class="fa fa-hourglass-half kpi-icon" style="color:#FFB020;"></i></div>
                    <div class="ms-3">
                        <div class="small-muted">Pending Requests</div>
                        <h5 class="mb-0"><?= $pending_requests ?></h5>
                        <div class="small-muted">Awaiting action</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ongoing Swaps -->
        <div class="col-6 col-sm-4 col-md-3">
            <div class="kpi-card h-100">
                <div class="d-flex">
                    <div><i class="fa fa-play-circle kpi-icon" style="color:<?= $mentor_color ?>;"></i></div>
                    <div class="ms-3">
                        <div class="small-muted">Ongoing swaps</div>
                        <h5 class="mb-0"><?= $ongoing_swaps ?></h5>
                        <div class="small-muted">Live sessions</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Completed swaps -->
        <div class="col-6 col-sm-4 col-md-3">
            <div class="kpi-card h-100">
                <div class="d-flex">
                    <div><i class="fa fa-check-circle kpi-icon" style="color:<?= $mentor_color ?>;"></i></div>
                    <div class="ms-3">
                        <div class="small-muted">Completed swaps</div>
                        <h5 class="mb-0"><?= $completed_swaps ?></h5>
                        <div class="small-muted">Total completed</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- New feedback -->
        <div class="col-6 col-sm-4 col-md-3">
            <div class="kpi-card h-100">
                <div class="d-flex">
                    <div><i class="fa fa-star kpi-icon" style="color:#FFC107;"></i></div>
                    <div class="ms-3">
                        <div class="small-muted">New feedback (today)</div>
                        <h5 class="mb-0"><?= $new_feedback ?></h5>
                        <div class="small-muted">Unread reviews</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reports -->
        <div class="col-6 col-sm-4 col-md-3">
            <div class="kpi-card h-100">
                <div class="d-flex">
                    <div><i class="fa fa-flag kpi-icon" style="color:#FF4D4F;"></i></div>
                    <div class="ms-3">
                        <div class="small-muted">Open reports</div>
                        <h5 class="mb-0"><?= $open_reports ?></h5>
                        <div class="small-muted">Moderation</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue -->
        <div class="col-6 col-sm-4 col-md-3">
            <div class="kpi-card h-100">
                <div class="d-flex">
                    <div><i class="fa fa-dollar-sign kpi-icon" style="color:<?= $mentor_color ?>;"></i></div>
                    <div class="ms-3">
                        <div class="small-muted">Revenue (today)</div>
                        <h5 class="mb-0">₹<?= $revenue_today ?></h5>
                        <div class="small-muted">Month: ₹<?= $revenue_month ?></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Charts -->
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="bg-white rounded p-3 chart-area shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0 section-title">Users Over Last 7 Days</h6>
                    <small class="small-muted">Signups trend</small>
                </div>
                <canvas id="usersChart" style="height:260px;"></canvas>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="bg-white rounded p-3 chart-area shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0 section-title">Top Skills</h6>
                    <a href="public.php?skills" class="small-muted">Manage Skills</a>
                </div>
                <canvas id="topSkillsChart" style="height:260px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Lower charts: skills by category and requests/swaps by month -->
<div class="container-fluid pt-4 px-4">
    <div class="row g-4">
        <div class="col-md-6">
            <div class="bg-white rounded p-3 shadow-sm">
                <h6 class="section-title">Skills by Category</h6>
                <canvas id="skillsCategoryChart" style="height:260px;"></canvas>
            </div>
        </div>

        <div class="col-md-6">
            <div class="bg-white rounded p-3 shadow-sm">
                <h6 class="section-title">Requests & Swaps (last 6 months)</h6>
                <canvas id="reqSwapChart" style="height:260px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Users Table with simple search -->
<div class="container-fluid pt-4 px-4">
    <div class="bg-white rounded p-3 shadow-sm">
        <ul class="nav nav-tabs mb-3" id="dashTabs" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-users">Recent
                    Users</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-requests">Recent
                    Requests</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-feedback">Recent
                    Feedback</button></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="tab-users">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>City</th>
                                <th>Joined</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="recentUsersTbody">
                            <?php if (!empty($recent_users)):
                                foreach ($recent_users as $u): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($u['id']) ?></td>
                                        <td><?= htmlspecialchars($u['username']) ?></td>
                                        <td><?= htmlspecialchars($u['email'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($u['role'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($u['city'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($u['created_at'] ?? '-') ?></td>
                                        <td><?= htmlspecialchars($u['status'] ?? '-') ?></td>
                                    </tr>
                                <?php endforeach; else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">No recent users found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-requests">
                <div class="table-responsive">
                    <?php
                    // show last 10 requests
                    if (table_exists($connection, 'requests')) {
                        $sql = "SELECT r.id, r.requester_id, r.provider_id, r.skill_id, r.status, r.requested_at, sk.name AS skill_name FROM requests r LEFT JOIN skills sk ON sk.id=r.skill_id ORDER BY r.requested_at DESC LIMIT 10";
                        $res = $connection->query($sql);
                        echo '<table class="table table-bordered"><thead><tr><th>ID</th><th>From</th><th>Skill</th><th>Status</th><th>Requested</th></tr></thead><tbody>';
                        if ($res) {
                            while ($row = $res->fetch_assoc()) {
                                echo '<tr><td>' . intval($row['id']) . '</td><td>' . intval($row['requester_id']) . '</td><td>' . htmlspecialchars($row['skill_name']) . '</td><td>' . htmlspecialchars($row['status']) . '</td><td>' . htmlspecialchars($row['requested_at']) . '</td></tr>';
                            }
                        }
                        echo '</tbody></table>';
                    } else {
                        echo '<p class="text-muted">Requests table not available.</p>';
                    }
                    ?>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-feedback">
                <div class="table-responsive">
                    <?php
                    if (table_exists($connection, 'feedbacks')) {
                        $r = $connection->query("SELECT f.id, f.swap_id, f.rating, f.comment, f.created_at, u.username AS from_user FROM feedbacks f LEFT JOIN registers u ON u.id=f.from_user_id ORDER BY f.created_at DESC LIMIT 10");
                        echo '<table class="table table-bordered"><thead><tr><th>ID</th><th>From</th><th>Rating</th><th>Comment</th><th>Date</th></tr></thead><tbody>';
                        if ($r) {
                            while ($row = $r->fetch_assoc()) {
                                echo '<tr><td>' . intval($row['id']) . '</td><td>' . htmlspecialchars($row['from_user']) . '</td><td>' . intval($row['rating']) . '</td><td>' . htmlspecialchars(mb_substr($row['comment'], 0, 80)) . '</td><td>' . htmlspecialchars($row['created_at']) . '</td></tr>';
                            }
                        }
                        echo '</tbody></table>';
                    } else {
                        echo '<p class="text-muted">Feedback table not available.</p>';
                    }
                    ?>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- JS: prepare data and render charts -->
<script>
    const signupLabels = <?php
    $labels = [];
    for ($i = 6; $i >= 0; $i--)
        $labels[] = date('M d', strtotime("-$i day"));
    echo json_encode($labels);
    ?>;

    const signupData = <?= json_encode(array_values($signupTrend)); ?>;

    // top skills arrays
    const topSkillsLabels = <?= json_encode(array_map(function ($s) {
        return $s['label'];
    }, $top_skills)); ?>;
    const topSkillsData = <?= json_encode(array_map(function ($s) {
        return $s['count'];
    }, $top_skills)); ?>;

    // skills by category
    const catLabels = <?= json_encode(array_map(function ($s) {
        return $s['label'];
    }, $skills_by_category)); ?>;
    const catData = <?= json_encode(array_map(function ($s) {
        return $s['count'];
    }, $skills_by_category)); ?>;

    // requests & swaps monthly
    const reqLabels = <?= json_encode($requests_month['labels']); ?>;
    const reqData = <?= json_encode($requests_month['data']); ?>;
    const swapsData = <?= json_encode($swaps_month['data']); ?>;

    // Users chart
    (function () {
        const ctx = document.getElementById('usersChart');
        if (!ctx) return;
        new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: { labels: signupLabels, datasets: [{ label: 'Signups', data: signupData, fill: true, tension: 0.3, backgroundColor: 'rgba(95,207,128,0.12)', borderColor: 'rgba(95,207,128,0.95)', pointRadius: 0 }] },
            options: { responsive: true, plugins: { legend: { display: false } } }
        });
    })();

    // Top skills (horizontal bar)
    (function () {
        const ctx = document.getElementById('topSkillsChart');
        if (!ctx) return;
        new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: { labels: topSkillsLabels, datasets: [{ label: 'Count', data: topSkillsData, backgroundColor: 'rgba(95,207,128,0.85)' }] },
            options: { indexAxis: 'y', responsive: true, plugins: { legend: { display: false } } }
        });
    })();

    // skills by category (doughnut)
    (function () {
        const ctx = document.getElementById('skillsCategoryChart');
        if (!ctx) return;
        new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: { labels: catLabels, datasets: [{label: 'Count', data: catData, backgroundColor: ['#5FCF80', '#FFB020', '#FFC107', '#FF6B6B', '#6C5CE7', '#36C5F0'] }] },
            options: {indexAxis: 'x', responsive: true, plugins: { legend: { display: false } } }
        });
    })();

    // requests & swaps stacked line
    (function () {
        const ctx = document.getElementById('reqSwapChart');
        if (!ctx) return;
        new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: {
                labels: reqLabels, datasets: [
                    { label: 'Requests', data: reqData, fill: false, borderColor: '#FFB020', tension: 0.3 },
                    { label: 'Swaps', data: swapsData, fill: false, borderColor: '#5FCF80', tension: 0.3 }
                ]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });
    })();

    // simple global search for recent users table
    document.getElementById('globalSearch')?.addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('#recentUsersTbody tr').forEach(tr => {
            tr.style.display = tr.innerText.toLowerCase().indexOf(q) >= 0 ? '' : 'none';
        });
    });
</script>