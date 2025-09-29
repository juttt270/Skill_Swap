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
    }
    else {
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
            $_SESSION['msg'] = "Login Successful!";
            $_SESSION['msg_type'] = "success";
            if ($user['role'] === 'admin')
                header("Location:../Admin_Panel/public.php?index");
            else{
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
?>