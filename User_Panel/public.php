<?php
include_once '../Skill_Swap/code.php';
// check agar user login nahi hai to login page bhejo
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Admin_Panel/signin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>SKILL_SWAP - User Template</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <div class="container-xxl position-relative bg-white d-flex p-0">
        <!-- Spinner Start -->
        <!-- <div id="spinner"
            class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div> -->
        <!-- Spinner End -->


        <!-- Sidebar Start -->
        <div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-light navbar-light">
                <a href="../SKILL_SWAP/index.php" class="navbar-brand mx-4 mb-3">
                    <h3 style="color:#5FCF80;"></i>SKILLSWAP</h3>
                </a>
                <div class="d-flex align-items-center ms-4 mb-4">
                    <div class="position-relative">
                        <img class="rounded-circle" src="../Skill_Swap/<?php echo $_SESSION['pic']; ?>"
                            alt="Profile Pic" style="width: 40px; height: 40px;">
                        <div
                            class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1">
                        </div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0"><?php echo $_SESSION['username']; ?></h6>
                        <span><?php echo $_SESSION['role']; ?></span>
                    </div>
                </div>
                <div class="navbar-nav w-100">
                    <a href="public.php?index" class="nav-item nav-link active"><i
                            class="fa fa-tachometer-alt me-2"></i>Dashboard</a>
                    <a href="public.php?myskills" class="nav-item nav-link"><i class="fa fa-users me-2"></i>My
                        Skills</a>
                    <a href="public.php?browseskill" class="nav-item nav-link"><i class="fa fa-users me-2"></i>Browse
                        Skills</a>
                    <a href="public.php?providerrequest" class="nav-item nav-link"><i
                            class="fa fa-lightbulb me-2"></i>Requests</a>
                    <a href="public.php?myrequests" class="nav-item nav-link"><i class="fa fa-exchange-alt me-2"></i>My
                        Requests</a>
                    <a href="public.php?swaps" class="nav-item nav-link"><i
                            class="fa fa-handshake me-2"></i>Swaps/Sessions</a>
                    <a href="public.php?chat" class="nav-item nav-link"><i
                            class="fa fa-handshake me-2"></i>Chats</a>
                    <a href="public.php?notifications" class="nav-item nav-link"><i
                            class="fa fa-handshake me-2"></i>Notifications</a>
                    <a href="public.php?feedback" class="nav-item nav-link"><i
                            class="fa fa-handshake me-2"></i>Feedback</a>
                    <a href="public.php?profile" class="nav-item nav-link"><i
                            class="fa fa-handshake me-2"></i>Profile</a>
                    <a href="../Skill_Swap/logout.php" class="nav-item nav-link"><i
                            class="fa fa-handshake me-2"></i>Logout</a>
            </nav>
        </div>
        <!-- Sidebar End -->

        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
            <nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-0">
                <a href="index.html" class="navbar-brand d-flex d-lg-none me-4">
                    <h2 class="text-primary mb-0"><i class="fa fa-hashtag"></i></h2>
                </a>
                <a href="#" class="sidebar-toggler flex-shrink-0">
                    <i style="color:#5FCF80;" class="fa fa-bars"></i>
                </a>
                <form class="d-none d-md-flex ms-4">
                    <input class="form-control border-0" type="search" placeholder="Search">
                </form>
                <div class="navbar-nav align-items-center ms-auto">
                   
                    <!-- notification dropdown -->
                    <?php
                    // fetch notifications (small and simple)
                    $uid = intval($_SESSION['user_id']);
                    $notif_count_q = mysqli_query($connection, "SELECT COUNT(*) AS c FROM notifications WHERE user_id=$uid AND is_read=0");
                    $notif_count = (int) (mysqli_fetch_assoc($notif_count_q)['c'] ?? 0);
                    $not_q = mysqli_query($connection, "SELECT * FROM notifications WHERE user_id=$uid AND is_read=0 ORDER BY created_at DESC LIMIT 3");
                    ?>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle position-relative" data-bs-toggle="dropdown">
                            <i class="fa fa-bell me-lg-2"></i>
                            <span class="d-none d-lg-inline-flex">Notifications</span>
                            <?php if ($notif_count > 0): ?>
                                <span class="position-absolute start-100 translate-middle badge rounded-pill bg-danger"
                                    style="top: 15px !important;">
                                    <?= $notif_count ?>
                                </span>
                            <?php endif; ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0"
                            style="width:340px;">
                            <li class="dropdown-header">Notifications</li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <?php if (mysqli_num_rows($not_q) > 0): ?>
                                <?php while ($n = mysqli_fetch_assoc($not_q)):
                                    $bold = $n['is_read'] ? '' : 'fw-bold';
                                    $target = $n['url'] ? $n['url'] : 'public.php';
                                    // safe wrapper: notify.php marks read then redirects
                                    $href = "notify.php?id={$n['id']}&redirect=" . urlencode($target);
                                    ?>
                                    <li>
                                        <a class="dropdown-item <?= $bold ?>" href="<?= $href ?>">
                                            <div class="d-flex">
                                                <div class="me-2">
                                                    <i class="fa fa-info-circle"></i>
                                                </div>
                                                <div>
                                                    <div><strong><?= htmlspecialchars($n['title']) ?></strong></div>
                                                    <div class="small text-muted">
                                                        <?= htmlspecialchars(mb_substr($n['body'] ?? '', 0, 40)) ?>...</div>
                                                    <div class="small text-muted"><?= $n['created_at'] ?></div>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <li>
                                    <div class="p-3 text-muted">No notifications</div>
                                </li>
                            <?php endif; ?>
                            <li><a class="dropdown-item text-center" href="public.php?notifications">See all
                                    notifications</a></li>
                        </ul>
                    </div>

                    <!-- profile dropdown -->
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img class="rounded-circle me-lg-2" src="../Skill_Swap/<?php echo $_SESSION['pic']; ?>"
                                alt="Profile Pic" style="width: 40px; height: 40px;">
                            <span class="d-none d-lg-inline-flex"><?php echo $_SESSION['username']; ?></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                            <a href="#" class="dropdown-item">My Profile</a>
                            <a href="#" class="dropdown-item">Settings</a>
                            <a href="../Skill_Swap/Logout.php" class="dropdown-item">Log Out</a>
                        </div>
                    </div>
                </div>
            </nav>
            <!-- Navbar End -->

            <!-- Content Start -->
            <?php
            if (isset($_GET['index'])) {
                include 'index.php';
            }
            if (isset($_GET['myskills'])) {
                include 'user_skills.php';
            }

            if (isset($_GET['browseskill'])) {
                include 'browse_skills.php';
            }
            if (isset($_GET['requestform'])) {
                include 'request_form.php';
            }
            if (isset($_GET['providerrequest'])) {
                include 'provider_request.php';
            }
            if (isset($_GET['myrequests'])) {
                include 'my_requests.php';
            }
            if (isset($_GET['swaps'])) {
                include 'swaps.php';
            }
            if (isset($_GET['chat'])) {
                include 'chat_box.php';
            }
            if (isset($_GET['notifications'])) {
                include 'notifications.php';
            }
            if (isset($_GET['feedback'])) {
                include 'feedback.php';
            }
            if (isset($_GET['profile'])) {
                include 'profile.php';
            }

            ?>
            <!-- Content End -->

            <!-- Footer Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="bg-light rounded-top p-4">
                    <div class="row">
                        <div class="col-12 col-sm-6 text-center text-sm-start">
                            &copy; <a target="_blank" href="../Skill_Swap/index.php">Skill_Swap</a>, All Right Reserved.
                        </div>
                        <div class="col-12 col-sm-6 text-center text-sm-end">
                            Designed By <a target="_blank" href="https://juttt270.github.io/Waqas-portfolio/">Waqas Munir</a>
                         
                    </div>
                </div>
            </div>
            <!-- Footer End -->
        </div>
        <!-- Content End -->


        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/chart/chart.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>

</html>