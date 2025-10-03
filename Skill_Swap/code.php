<?php
include_once 'session.php';

$connection = mysqli_connect('localhost', 'root', '', 'skillswap');

// Check connection
if ($connection->connect_error) {
    die("DB Connection failed: " . $connection->connect_error);
}

// Registration Handling
if (isset($_POST['register_btn'])) {
    $uname = $_POST['username'];
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $confirm_pass = $_POST['confirm_password'];
    $bio = $_POST['bio'];
    $phone = $_POST['phone'];
    $city = $_POST['city'];
    $country = $_POST['country'];
    $role = trim($_POST['role']);

    // Password match check
    if ($pass !== $confirm_pass) {
        $_SESSION['msg'] = "Passwords do not match!";
        $_SESSION['msg_type'] = "danger";
        header("Location: ../Skill_Swap/signup.php");
        exit();
    }

    // Profile picture upload
    $profile_pic = "";
    if (!empty($_FILES['profile_picture']['name'])) {
        $target_dir = "img/";
        if (!is_dir($target_dir))
            mkdir($target_dir);
        $profile_pic = $target_dir . time() . "_" . basename($_FILES["profile_picture"]["name"]);
        move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $profile_pic);
    }

    // Check email unique
    $check = "SELECT * FROM registers WHERE email='$email'";
    $result = $connection->query($check);

    if ($result->num_rows > 0) {
        $_SESSION['msg'] = "Email already registered!";
        $_SESSION['msg_type'] = "danger";
        header("Location: ../Skill_Swap/signup.php");
        exit();
    } else {
        $sql = "INSERT INTO registers (username,email,password,bio,profile_picture,phone,city,country,role,status) 
                VALUES ('$uname','$email','$pass','$bio','$profile_pic','$phone','$city','$country','$role','inactive')";
        if ($connection->query($sql) === TRUE) {
            $_SESSION['msg'] = "Registration Successful!";
            $_SESSION['msg_type'] = "success";
            header("Location:index.php");
        } else {
            $_SESSION['msg'] = "Error: " . $connection->error;
            $_SESSION['msg_type'] = "danger";
            header("Location: ../Skill_Swap/signup.php");
        }
    }
}

// Signin Handling
if (isset($_POST['signin_btn'])) {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $sql = "SELECT * FROM registers WHERE email='$email' AND password='$pass'";
    $result = $connection->query($sql);

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if ($user['status'] === 'active') {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['pic'] = $user['profile_picture'];
            $_SESSION['msg'] = "Login Successful!";
            $_SESSION['msg_type'] = "success";
            if ($user['role'] === 'admin')
                header("Location:../Admin_Panel/public.php?index");
            else {
                header("Location: index.php");
            }
        } else {
            $_SESSION['msg'] = "Account inactive. Please contact admin.";
            $_SESSION['msg_type'] = "warning";
            header("Location: ../Admin_Panel/signin.php");
        }
    } else {
        $_SESSION['msg'] = "Invalid email or password!";
        $_SESSION['msg_type'] = "danger";
        header("Location: ../Admin_Panel/signin.php");
    }
}

$user = intval($_SESSION['user_id'] ?? 0);

// admin dashboard
// They are wrapped with function_exists to avoid redeclare errors.

if (!function_exists('table_exists')) {
    function table_exists($connection, $table) {
        $table = $connection->real_escape_string($table);
        $res = $connection->query("SHOW TABLES LIKE '$table'");
        return ($res && $res->num_rows > 0);
    }
}

if (!function_exists('column_exists_in_table')) {
    function column_exists_in_table($connection, $table, $column) {
        $table = $connection->real_escape_string($table);
        $column = $connection->real_escape_string($column);
        $res = $connection->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
        return ($res && $res->num_rows > 0);
    }
}

if (!function_exists('get_user_skills_table_name')) {
    function get_user_skills_table_name($connection) {
        if (table_exists($connection, 'user_skills')) return 'user_skills';
        if (table_exists($connection, 'userskill')) return 'userskill';
        return null;
    }
}

if (!function_exists('get_top_skills_by_count')) {
    /**
     * Returns array of ['label'=>name,'count'=>n] for top skills
     */
    function get_top_skills_by_count($connection, $limit = 6) {
        $out = [];
        if (!table_exists($connection, 'skills')) return $out;

        $user_skills_table = get_user_skills_table_name($connection);
        if ($user_skills_table) {
            // prefer counting user_skills
            $sql = "SELECT sk.name, COUNT(uk.skill_id) AS cnt
                    FROM " . $user_skills_table . " uk
                    JOIN skills sk ON sk.id = uk.skill_id
                    WHERE sk.status = 'active'
                    GROUP BY sk.id
                    ORDER BY cnt DESC
                    LIMIT " . intval($limit);
        } else {
            // fallback: count requests per skill (approx)
            if (table_exists($connection, 'requests')) {
                $sql = "SELECT sk.name, COUNT(r.skill_id) AS cnt
                        FROM requests r
                        JOIN skills sk ON sk.id = r.skill_id
                        GROUP BY sk.id
                        ORDER BY cnt DESC
                        LIMIT " . intval($limit);
            } else {
                return $out;
            }
        }

        $res = $connection->query($sql);
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $out[] = ['label' => $row['name'], 'count' => (int)$row['cnt']];
            }
        }
        return $out;
    }
}

if (!function_exists('get_skills_by_category')) {
    /**
     * Returns array of ['label'=>category, 'count'=>n]
     */
    function get_skills_by_category($connection) {
        $out = [];
        if (!table_exists($connection, 'skills')) return $out;
        $sql = "SELECT IFNULL(category, 'Uncategorized') AS cat, COUNT(*) AS cnt
                FROM skills
                WHERE status = 'active'
                GROUP BY category
                ORDER BY cnt DESC";
        $res = $connection->query($sql);
        if ($res) while ($r = $res->fetch_assoc()) $out[] = ['label' => $r['cat'], 'count' => (int)$r['cnt']];
        return $out;
    }
}

if (!function_exists('get_requests_per_month')) {
    /**
     * Returns ['labels'=>[], 'data'=>[]] for last $months months (default 6)
     */
    function get_requests_per_month($connection, $months = 6) {
        $labels = [];
        $data = [];
        if (!table_exists($connection, 'requests') || !column_exists_in_table($connection, 'requests', 'requested_at')) {
            // fallback zeros
            for ($i = $months - 1; $i >= 0; $i--) {
                $labels[] = date('M Y', strtotime("-$i month"));
                $data[] = 0;
            }
            return ['labels'=>$labels,'data'=>$data];
        }

        for ($i = $months - 1; $i >= 0; $i--) {
            $y = date('Y', strtotime("-$i month"));
            $m = date('m', strtotime("-$i month"));
            $labels[] = date('M Y', strtotime("-$i month"));
            $q = $connection->query("SELECT COUNT(*) AS c FROM requests WHERE YEAR(requested_at) = $y AND MONTH(requested_at) = $m");
            $data[] = $q ? (int)$q->fetch_assoc()['c'] : 0;
        }
        return ['labels'=>$labels,'data'=>$data];
    }
}

if (!function_exists('get_swaps_per_month')) {
    function get_swaps_per_month($connection, $months = 6) {
        $labels = [];
        $data = [];
        if (!table_exists($connection, 'swaps') || !column_exists_in_table($connection, 'swaps', 'started_at')) {
            for ($i = $months - 1; $i >= 0; $i--) {
                $labels[] = date('M Y', strtotime("-$i month"));
                $data[] = 0;
            }
            return ['labels'=>$labels,'data'=>$data];
        }
        for ($i = $months - 1; $i >= 0; $i--) {
            $y = date('Y', strtotime("-$i month"));
            $m = date('m', strtotime("-$i month"));
            $labels[] = date('M Y', strtotime("-$i month"));
            $q = $connection->query("SELECT COUNT(*) AS c FROM swaps WHERE YEAR(started_at) = $y AND MONTH(started_at) = $m");
            $data[] = $q ? (int)$q->fetch_assoc()['c'] : 0;
        }
        return ['labels'=>$labels,'data'=>$data];
    }
}

if (!function_exists('get_admin_dashboard_stats')) {
    /**
     * Returns a structured array of admin stats (uses table/column checks)
     */
    function get_admin_dashboard_stats($connection) {
        $out = [];

        // Users
        $out['total_users'] = 0;
        $out['trainers'] = 0;
        $out['learners'] = 0;
        $out['active_now'] = 'N/A';
        $out['new_today'] = 0;
        $out['new_7days'] = 0;
        $out['signup_trend'] = [0,0,0,0,0,0,0];

        if (table_exists($connection, 'registers')) {
            $r = $connection->query("SELECT COUNT(*) AS c FROM registers");
            $out['total_users'] = $r ? (int)$r->fetch_assoc()['c'] : 0;

            if (column_exists_in_table($connection, 'registers', 'role')) {
                $r = $connection->query("SELECT COUNT(*) AS c FROM registers WHERE role='Trainer'");
                $out['trainers'] = $r ? (int)$r->fetch_assoc()['c'] : 0;
                $r = $connection->query("SELECT COUNT(*) AS c FROM registers WHERE role='Learner'");
                $out['learners'] = $r ? (int)$r->fetch_assoc()['c'] : 0;
            }
            if (column_exists_in_table($connection, 'registers', 'last_login')) {
                $r = $connection->query("SELECT COUNT(*) AS c FROM registers WHERE last_login >= (NOW() - INTERVAL 15 MINUTE)");
                $out['active_now'] = $r ? (int)$r->fetch_assoc()['c'] : 0;
            } elseif (column_exists_in_table($connection, 'registers', 'updated_at')) {
                $r = $connection->query("SELECT COUNT(*) AS c FROM registers WHERE updated_at >= (NOW() - INTERVAL 15 MINUTE)");
                $out['active_now'] = $r ? (int)$r->fetch_assoc()['c'] : 0;
            } else {
                $out['active_now'] = 'N/A';
            }

            if (column_exists_in_table($connection, 'registers', 'created_at')) {
                $r = $connection->query("SELECT SUM(created_at >= CURDATE()) AS today, SUM(created_at >= (CURDATE() - INTERVAL 6 DAY)) AS last7 FROM registers");
                if ($r) {
                    $row = $r->fetch_assoc();
                    $out['new_today'] = (int)$row['today'];
                    $out['new_7days'] = (int)$row['last7'];
                }
                $dates = [];
                for ($i = 6; $i >= 0; $i--) $dates[date('Y-m-d', strtotime("-$i day"))] = 0;
                $r = $connection->query("SELECT DATE(created_at) as d, COUNT(*) as cnt FROM registers WHERE created_at >= (CURDATE() - INTERVAL 6 DAY) GROUP BY DATE(created_at)");
                if ($r) {
                    while ($row = $r->fetch_assoc()) $dates[$row['d']] = (int)$row['cnt'];
                }
                $out['signup_trend'] = array_values($dates);
            }
        }

        // Skills
        $out['total_skills'] = 0;
        $out['active_skills'] = 0;
        $out['pending_skills'] = 0;
        if (table_exists($connection, 'skills')) {
            $r = $connection->query("SELECT COUNT(*) AS c FROM skills");
            $out['total_skills'] = $r ? (int)$r->fetch_assoc()['c'] : 0;
            if (column_exists_in_table($connection, 'skills', 'status')) {
                $r = $connection->query("SELECT COUNT(*) AS c FROM skills WHERE status='active'");
                $out['active_skills'] = $r ? (int)$r->fetch_assoc()['c'] : 0;
                $r = $connection->query("SELECT COUNT(*) AS c FROM skills WHERE status='inactive'");
                $out['pending_skills'] = $r ? (int)$r->fetch_assoc()['c'] : 0;
            }
        }

        // Requests & swaps
        $out['total_requests'] = 0;
        $out['pending_requests'] = 0;
        $out['ongoing_swaps'] = 0;
        $out['completed_swaps'] = 0;
        if (table_exists($connection, 'requests')) {
            $r = $connection->query("SELECT COUNT(*) AS c FROM requests");
            $out['total_requests'] = $r ? (int)$r->fetch_assoc()['c'] : 0;
            if (column_exists_in_table($connection, 'requests', 'status')) {
                $r = $connection->query("SELECT COUNT(*) AS c FROM requests WHERE status='pending'");
                $out['pending_requests'] = $r ? (int)$r->fetch_assoc()['c'] : 0;
            }
        }
        if (table_exists($connection, 'swaps')) {
            if (column_exists_in_table($connection, 'swaps', 'status')) {
                $r = $connection->query("SELECT COUNT(*) AS c FROM swaps WHERE status='ongoing'");
                $out['ongoing_swaps'] = $r ? (int)$r->fetch_assoc()['c'] : 0;
                $r = $connection->query("SELECT COUNT(*) AS c FROM swaps WHERE status='completed'");
                $out['completed_swaps'] = $r ? (int)$r->fetch_assoc()['c'] : 0;
            }
        }

        // Feedbacks
        $out['new_feedback'] = 0;
        if (table_exists($connection, 'feedbacks')) {
            $r = $connection->query("SELECT COUNT(*) AS c FROM feedbacks WHERE DATE(created_at) = CURDATE()");
            $out['new_feedback'] = $r ? (int)$r->fetch_assoc()['c'] : 0;
        }

        // Reports
        $out['open_reports'] = 0;
        if (table_exists($connection, 'reports')) {
            $r = $connection->query("SELECT COUNT(*) AS c FROM reports WHERE status='open'");
            $out['open_reports'] = $r ? (int)$r->fetch_assoc()['c'] : 0;
        }

        // Revenue (transactions) - optional
        $out['revenue_today'] = 0.00;
        $out['revenue_month'] = 0.00;
        if (table_exists($connection, 'transactions') && column_exists_in_table($connection, 'transactions', 'amount')) {
            $r = $connection->query("SELECT COALESCE(SUM(amount),0) AS s FROM transactions WHERE status='completed' AND DATE(created_at)=CURDATE()");
            $out['revenue_today'] = $r ? (float)$r->fetch_assoc()['s'] : 0.00;
            $r = $connection->query("SELECT COALESCE(SUM(amount),0) AS s FROM transactions WHERE status='completed' AND MONTH(created_at)=MONTH(CURDATE()) AND YEAR(created_at)=YEAR(CURDATE())");
            $out['revenue_month'] = $r ? (float)$r->fetch_assoc()['s'] : 0.00;
        }

        // conversion rate simple
        $out['conversion_rate'] = 'N/A';
        if ($out['total_users'] > 0) {
            $out['conversion_rate'] = ($out['total_requests'] > 0) ? round(($out['total_requests'] / max(1, $out['total_users'])) * 100, 1) . '%' : '0%';
        }

        // recent users
        $out['recent_users'] = [];
        if (table_exists($connection, 'registers')) {
            $cols = "id, username";
            if (column_exists_in_table($connection, 'registers', 'email')) $cols .= ", email";
            if (column_exists_in_table($connection, 'registers', 'role')) $cols .= ", role";
            if (column_exists_in_table($connection, 'registers', 'city')) $cols .= ", city";
            if (column_exists_in_table($connection, 'registers', 'created_at')) $cols .= ", created_at";
            if (column_exists_in_table($connection, 'registers', 'status')) $cols .= ", status";
            $r = $connection->query("SELECT $cols FROM registers ORDER BY id DESC LIMIT 10");
            if ($r) while ($row = $r->fetch_assoc()) $out['recent_users'][] = $row;
        }

        // charts and lists
        $out['top_skills'] = get_top_skills_by_count($connection, 6);
        $out['skills_by_category'] = get_skills_by_category($connection);
        $out['requests_month'] = get_requests_per_month($connection, 6);
        $out['swaps_month'] = get_swaps_per_month($connection, 6);

        // ensure shapes exist
        if (empty($out['signup_trend'])) $out['signup_trend'] = [0,0,0,0,0,0,0];

        return $out;
    }
}


// ADD USER
if (isset($_POST['add_user'])) {
    $u = $_POST['username'];
    $e = $_POST['email'];
    $p = $_POST['password'];
    $c = $_POST['city'];
    $co = $_POST['country'];
    $r = $_POST['role'];
    $s = $_POST['status'];
    $sql = "INSERT INTO registers(username,email,password,city,country,role,status) 
          VALUES('$u','$e','$p','$c','$co','$r','$s')";
    mysqli_query($connection, $sql);
    header("Location: ../Admin_Panel/public.php?users");
}

// EDIT USER
if (isset($_POST['edit_user'])) {
    $id = $_POST['id'];
    $u = $_POST['username'];
    $e = $_POST['email'];
    $c = $_POST['city'];
    $co = $_POST['country'];
    $r = $_POST['role'];
    $s = $_POST['status'];
    $sql = "UPDATE registers SET username='$u', email='$e', city='$c', country='$co',
          role='$r', status='$s' WHERE id=$id";
    mysqli_query($connection, $sql);
    header("Location: ../Admin_Panel/public.php?users");
}

// DELETE USER
if (isset($_GET['delete_user'])) {
    // $id = $_GET['delete_user'];
    $id = $_GET['delete_user'];
    mysqli_query($connection, "DELETE FROM registers WHERE id=$id");
    header("Location: ../Admin_Panel/public.php?users");
}

// ---------- ADD SKILL ----------
if (isset($_POST['add_skill'])) {
    $name = $_POST['name'];
    $cat = $_POST['category'];
    $status = $_POST['status'];
    mysqli_query($connection, "INSERT INTO skills(name,category,status) VALUES('$name','$cat','$status')");
    header("Location: ../Admin_Panel/public.php?skills");
    exit;
}

// ---------- EDIT SKILL ----------
if (isset($_POST['edit_skill'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $cat = $_POST['category'];
    $status = $_POST['status'];
    mysqli_query($connection, "UPDATE skills SET name='$name', category='$cat', status='$status' WHERE id=$id");
    header("Location: ../Admin_Panel/public.php?skills");
    exit;
}

// ---------- DELETE SKILL ----------
if (isset($_GET['delete_skill'])) {
    $id = $_GET['delete_skill'];
    mysqli_query($connection, "DELETE FROM skills WHERE id=$id");
    header("Location: ../Admin_Panel/public.php?skills");
    exit;
}

// ---------------------------------------------------------------------------User Panel----------------------------------------------------------------------------------------------------
// ---------- ADD USER SKILL ----------
// Add skill to userskill table
if (isset($_POST['add_user_skill'])) {
    $skill_id = $_POST['skill_id'];
    $level = $_POST['level'];

    // Check if user already has this skill
    $check = mysqli_query($connection, "SELECT * FROM user_skills WHERE user_id='$user_id' AND skill_id='$skill_id'");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('You already added this skill');location.assign('public.php?myskills');</script>";
    } else {
        $insert = mysqli_query($connection, "INSERT INTO user_skills (user_id, skill_id, level) VALUES ('$user_id','$skill_id','$level')");
        if ($insert) {
            echo "<script>alert('Skill added successfully');location.assign('public.php?myskills');</script>";
        } else {
            echo "<script>alert('Error adding skill');</script>";
        }
    }
}

// Add new skill request (goes to skills table as inactive)
if (isset($_POST['add_new_skill'])) {
    $new_skill = trim($_POST['new_skill']);

    // Check if skill already exists
    $check_skill = mysqli_query($connection, "SELECT * FROM skills WHERE name='$new_skill'");
    if (mysqli_num_rows($check_skill) > 0) {
        echo "<script>alert('This skill already exists in system');location.assign('public.php?myskills');</script>";
    } else {
        $insert = mysqli_query($connection, "INSERT INTO skills (name, status) VALUES ('$new_skill','inactive')");
        if ($insert) {
            echo "<script>alert('New skill submitted for approval. Admin will activate it soon.');location.assign('public.php?myskills');</script>";
        } else {
            echo "<script>alert('Error submitting skill');</script>";
        }
    }
}

// Fetch active skills for dropdown
$skills = mysqli_query($connection, "SELECT * FROM skills WHERE status='active' ORDER BY name ASC");

// Fetch user skills
$user_skills = mysqli_query($connection, "SELECT u.id, s.name, u.level 
    FROM user_skills u 
    JOIN skills s ON u.skill_id = s.id 
    WHERE u.user_id='$user'");


// ---------- DELETE USER SKILL ----------
if (isset($_GET['delete_user_skill'])) {
    $id = $_GET['delete_user_skill'];
    mysqli_query($connection, "DELETE FROM user_skills WHERE id=$id");
    header("Location: ../User_Panel/public.php?index");
    exit;
}


// CREATE REQUEST
if (isset($_POST['create_request'])) {
    $requester = $_SESSION['user_id'];
    $provider = intval($_POST['provider_id']);
    $skill = intval($_POST['skill_id']);
    $msg = mysqli_real_escape_string($connection, $_POST['message']);
    $scheduled = mysqli_real_escape_string($connection, $_POST['scheduled_at'] ?? '');

    // Insert request
    mysqli_query($connection, "INSERT INTO requests (requester_id, provider_id, skill_id, message, requested_at) 
                               VALUES ($requester,$provider,$skill,'$msg',NOW())");

    // Get the last inserted request id
    $request_id = mysqli_insert_id($connection);

    // Insert notification for provider
    $notif_title = "New Request Received";
    $notif_body = "You have a new skill swap request from" . $_SESSION['username'];
    $notif_url = "public.php?providerrequest&request_id=" . $request_id;

    $notif_sql = "
        INSERT INTO notifications (user_id, type, related_id, title, body, url, is_read, created_at)
        VALUES ($provider, 'request', $request_id, 
                '" . mysqli_real_escape_string($connection, $notif_title) . "',
                '" . mysqli_real_escape_string($connection, $notif_body) . "',
                '" . mysqli_real_escape_string($connection, $notif_url) . "',
                0, NOW())";
    mysqli_query($connection, $notif_sql);

    header("Location: ../User_Panel/public.php?browseskill");
    exit;
}

// ---------- Provider request flow handlers ----------
// $user = intval($_SESSION['user_id'] ?? 0);

// ---------- START DISCUSSION ----------
if (isset($_GET['start_discussion'])) {
    $req = intval($_GET['start_discussion']);

    $r = mysqli_fetch_assoc(mysqli_query($connection, "SELECT requester_id, provider_id FROM requests WHERE id=$req LIMIT 1"));
    if ($r && intval($r['provider_id']) === $user) {

        mysqli_query($connection, "UPDATE requests SET status='under_discussion' WHERE id=$req");

        $check = mysqli_fetch_assoc(mysqli_query($connection, "SELECT id, status FROM swaps WHERE request_id=$req LIMIT 1"));
        if (!$check) {
            mysqli_query($connection, "INSERT INTO swaps (request_id, status, started_at) VALUES ($req, 'discussion', NOW())");
            $swap_id = intval(mysqli_insert_id($connection));
        } else {
            $swap_id = intval($check['id']);
            mysqli_query($connection, "UPDATE swaps SET status='discussion' WHERE id=$swap_id");
        }

        // notify requester
        $requester_id = intval($r['requester_id']);
        $title = "Provider started discussion";
        $body = "Provider " . ($_SESSION['username'] ?? 'a provider') . " started a discussion.";
        $url = "public.php?chat&swap_id=$swap_id";

        if (function_exists('add_notification')) {
            add_notification($connection, $requester_id, 'discussion', $swap_id, $title, $body, $url);
        } else {
            $ti = mysqli_real_escape_string($connection, $title);
            $bo = mysqli_real_escape_string($connection, $body);
            $ur = mysqli_real_escape_string($connection, $url);
            mysqli_query(
                $connection,
                "INSERT INTO notifications (user_id, type, related_id, title, body, url, is_read, created_at)
                 VALUES ($requester_id, 'discussion', $swap_id, '$ti', '$bo', '$ur', 0, NOW())"
            );
        }

        header("Location: ../User_Panel/public.php?chat&swap_id=$swap_id");
        exit;
    }
    header("Location: ../User_Panel/public.php?providerrequest");
    exit;
}

// ---------- CONFIRM SWAP ----------
if (isset($_GET['confirm_swap'])) {
    $req = intval($_GET['confirm_swap']);

    $r = mysqli_fetch_assoc(mysqli_query($connection, "SELECT requester_id, provider_id FROM requests WHERE id=$req LIMIT 1"));
    if ($r && intval($r['provider_id']) === $user) {
        // update request
        mysqli_query($connection, "UPDATE requests SET status='accepted' WHERE id=$req");

        // ensure swap
        $check = mysqli_fetch_assoc(mysqli_query($connection, "SELECT id FROM swaps WHERE request_id=$req LIMIT 1"));
        if ($check) {
            $swap_id = intval($check['id']);
            mysqli_query($connection, "UPDATE swaps SET status='ongoing', started_at = COALESCE(started_at, NOW()) WHERE id=$swap_id");
        } else {
            mysqli_query($connection, "INSERT INTO swaps (request_id, status, started_at) VALUES ($req, 'ongoing', NOW())");
            $swap_id = intval(mysqli_insert_id($connection));
        }

        // notify
        $requester_id = intval($r['requester_id']);
        $title = "Request accepted";
        $body = "Your request has been accepted by " . ($_SESSION['username'] ?? 'the provider') . ".";
        $url = "public.php?swaps&swap_id=$swap_id";

        if (function_exists('add_notification')) {
            add_notification($connection, $requester_id, 'request_accepted', $req, $title, $body, $url);
        } else {
            $ti = mysqli_real_escape_string($connection, $title);
            $bo = mysqli_real_escape_string($connection, $body);
            $ur = mysqli_real_escape_string($connection, $url);
            mysqli_query(
                $connection,
                "INSERT INTO notifications (user_id, type, related_id, title, body, url, is_read, created_at)
                 VALUES ($requester_id, 'request_accepted', $req, '$ti', '$bo', '$ur', 0, NOW())"
            );
        }

        header("Location: ../User_Panel/public.php?swaps&swap_id=$swap_id");
        exit;
    }
    header("Location: ../User_Panel/public.php?providerrequest");
    exit;
}

// ---------- REJECT REQUEST ----------
if (isset($_GET['reject_request'])) {
    $req = intval($_GET['reject_request']);

    $r = mysqli_fetch_assoc(mysqli_query($connection, "SELECT requester_id, provider_id FROM requests WHERE id=$req LIMIT 1"));
    if ($r && intval($r['provider_id']) === $user) {
        mysqli_query($connection, "UPDATE requests SET status='rejected' WHERE id=$req");

        // also mark swap if exists
        $check = mysqli_fetch_assoc(mysqli_query($connection, "SELECT id FROM swaps WHERE request_id=$req LIMIT 1"));
        if ($check) {
            $swap_id = intval($check['id']);
            mysqli_query($connection, "UPDATE swaps SET status='cancelled' WHERE id=$swap_id");
        }

        // notify
        $requester_id = intval($r['requester_id']);
        $title = "Request rejected";
        $body = "Your request was rejected by " . ($_SESSION['username'] ?? 'the provider') . ".";
        $url = "public.php?myrequests";

        if (function_exists('add_notification')) {
            add_notification($connection, $requester_id, 'request_rejected', $req, $title, $body, $url);
        } else {
            $ti = mysqli_real_escape_string($connection, $title);
            $bo = mysqli_real_escape_string($connection, $body);
            $ur = mysqli_real_escape_string($connection, $url);
            mysqli_query(
                $connection,
                "INSERT INTO notifications (user_id, type, related_id, title, body, url, is_read, created_at)
                 VALUES ($requester_id, 'request_rejected', $req, '$ti', '$bo', '$ur', 0, NOW())"
            );
        }
    }
    header("Location: ../User_Panel/public.php?providerrequest");
    exit;
}


// delete request
if (isset($_POST['delete_request_id'])) {
    $id = intval($_POST['delete_request_id']);
    mysqli_query($connection, "DELETE FROM requests WHERE id=$id");
    header("Location: ../User_Panel/public.php?myrequests");
    exit;
}

// edit request (update message)
if (isset($_POST['edit_request_id'])) {
    $id = intval($_POST['edit_request_id']);
    $message = mysqli_real_escape_string($connection, $_POST['message']);
    mysqli_query($connection, "UPDATE requests SET message='$message' WHERE id=$id");
    header("Location: ../User_Panel/public.php?myrequests");
    exit;
}

// Mark swap as start
if (isset($_GET['start'])) {
    $swap_id = $_GET['start'];
    $update = mysqli_query($connection, "
        UPDATE swaps 
        SET status='ongoing' 
        WHERE id=$swap_id
    ");
    if ($update) {
        echo "<script>alert('Swap marked as start');</script>";
        header("Location: public.php?swaps");
    } else {
        echo "<script>alert('Error completing swap');</script>";
        header("Location: public.php?swaps");
    }
}

// Mark swap as completed
if (isset($_GET['complete'])) {
    $swap_id = $_GET['complete'];
    $update = mysqli_query($connection, "
        UPDATE swaps 
        SET status='completed', finished_at=NOW() 
        WHERE id=$swap_id
    ");
    if ($update) {
        echo "<script>alert('Swap marked as completed');</script>";
        header("Location: public.php?swaps");
    } else {
        echo "<script>alert('Error completing swap');</script>";
        header("Location: public.php?swaps");
    }
}

// ---------------------- SEND MESSAGE ----------------------
// $user = intval($_SESSION['user_id']);
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

// GET params
$swap_id = intval($_GET['swap_id'] ?? 0);
$parent_id = intval($_GET['reply_to'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send'])) {
    $message = trim($_POST['message']);
    $parent_id_post = intval($_POST['parent_id'] ?? 0);

    if ($message !== '' && $swap_id > 0) {
        // find receiver
        $swap_sql = "
            SELECT rq.requester_id, rq.provider_id
            FROM swaps s
            JOIN requests rq ON rq.id = s.request_id
            WHERE s.id = $swap_id
        ";
        $swap_res = mysqli_query($connection, $swap_sql);
        $swap = mysqli_fetch_assoc($swap_res);

        if ($swap) {
            $receiver_id = ($swap['requester_id'] == $user_id) ? $swap['provider_id'] : $swap['requester_id'];
            $msg_db = mysqli_real_escape_string($connection, $message);
            $title = mysqli_real_escape_string($connection, "New Message");
            $body = mysqli_real_escape_string($connection, $_SESSION['username'] . " sent you a new message");
            $url = "public.php?chat&swap_id=" . $swap_id;

            // insert message
            $insert_sql = "
                INSERT INTO messages (swap_id, sender_id, receiver_id, parent_id, message, is_read, created_at)
                VALUES ($swap_id, $user_id, $receiver_id, 
                        " . ($parent_id_post > 0 ? $parent_id_post : "NULL") . ", 
                        '$msg_db', 0, NOW())
            ";
            mysqli_query($connection, $insert_sql);

            // ---------------------- INSERT NOTIFICATION ----------------------
            // $notif_text = mysqli_real_escape_string($connection, "New message from user #$_session[username]");
            $notif_sql = "
    INSERT INTO notifications (user_id, type, related_id, title, body, url, is_read, created_at)
    VALUES ($receiver_id, 'message', $swap_id, '$title', '$body', '$url', 0, NOW())
";
            mysqli_query($connection, $notif_sql);
        }
    }
    echo "<script>window.location='public.php?chat&swap_id=$swap_id';</script>";
    exit;
}

// ---------------------- FETCH SWAPS LIST ----------------------
$swaps_sql = "
    SELECT s.id, r.skill_id, sk.name, sk.category
    FROM swaps s
    JOIN requests r ON r.id = s.request_id
    JOIN skills sk ON sk.id = r.skill_id
    WHERE r.requester_id = $user_id OR r.provider_id = $user_id
    ORDER BY s.id DESC
";
$swaps = mysqli_query($connection, $swaps_sql);

// ---------------------- FETCH MESSAGES ----------------------
$messages = [];
$parent_text = '';
if ($swap_id > 0) {
    // mark all received messages as read
    $update_sql = "UPDATE messages SET is_read = 1 WHERE swap_id = $swap_id AND receiver_id = $user_id";
    mysqli_query($connection, $update_sql);

    $msg_sql = "
        SELECT m.*, u.username AS sender_name, u.profile_picture
        FROM messages m
        LEFT JOIN registers u ON u.id = m.sender_id
        WHERE m.swap_id = $swap_id
        ORDER BY m.created_at ASC
    ";
    $messages = mysqli_query($connection, $msg_sql);

    // fetch parent text for reply
    if ($parent_id > 0) {
        $pt_sql = "SELECT message FROM messages WHERE id = $parent_id LIMIT 1";
        $pt_res = mysqli_query($connection, $pt_sql);
        if ($pt_row = mysqli_fetch_assoc($pt_res)) {
            $parent_text = mb_substr($pt_row['message'], 0, 30) . (strlen($pt_row['message']) > 30 ? '...' : '');
        }
    }
}

//notification message
// helper: insert simple notification
function add_notification($connection, $user_id, $type, $related_id, $title, $body = '', $url = '')
{
    $user_id = intval($user_id);
    $type = mysqli_real_escape_string($connection, $type);
    $title = mysqli_real_escape_string($connection, $title);
    $body = mysqli_real_escape_string($connection, $body);
    $url = mysqli_real_escape_string($connection, $url);
    mysqli_query($connection, "
        INSERT INTO notifications (user_id, type, related_id ,title, body, url)
        VALUES ($user_id, '$type','$related_id', '$title', '$body', '$url')
    ");
}

// ------------------------- UPDATE PROFILE INFO -------------------------
function update_profile($connection, $user_id, $data)
{
    // $data = associative array of columns to update
    $updates = [];
    foreach ($data as $col => $val) {
        $val_db = mysqli_real_escape_string($connection, $val);
        $updates[] = "$col='$val_db'";
    }
    $sql = "UPDATE registers SET " . implode(',', $updates) . " WHERE id=$user_id";
    return mysqli_query($connection, $sql);
}

// ------------------------- CHANGE PASSWORD -------------------------
function change_password($connection, $user_id, $old_password, $new_password)
{
    // 1️⃣ Check old password
    $user = mysqli_fetch_assoc(mysqli_query($connection, "SELECT password FROM registers WHERE id=$user_id"));
    if (!$user)
        return ['error' => 'User not found.'];

    if ($user['password'] !== $old_password) {
        return ['error' => 'Old password incorrect.'];
    }

    // 2️⃣ Update to new password
    mysqli_query($connection, "UPDATE registers SET password='$new_password' WHERE id=$user_id");
    return ['success' => 'Password changed successfully.'];
}


// ------------------------- UPDATE PROFILE PICTURE -------------------------
function update_profile_picture($connection, $user_id, $file)
{
    if ($file['error'] !== 0)
        return ['error' => 'File upload error.'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif']))
        return ['error' => 'Invalid file type.'];

    $new_name = 'img/profile_' . $user_id . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], '../Skill_Swap/' . $new_name))
        return ['error' => 'Failed to upload.'];

    mysqli_query($connection, "UPDATE registers SET profile_picture='$new_name' WHERE id=$user_id");
    return ['success' => 'Profile picture updated.'];
}

// ------------------------- FETCH AVERAGE RATING -------------------------
function get_user_rating($connection, $user_id)
{
    $row = mysqli_fetch_assoc(mysqli_query($connection, "SELECT AVG(rating) AS avg_rating, COUNT(*) AS total FROM feedbacks WHERE to_user_id=$user_id"));
    return ['avg' => round(floatval($row['avg_rating'] ?? 0), 2), 'total' => intval($row['total'] ?? 0)];
}
?>