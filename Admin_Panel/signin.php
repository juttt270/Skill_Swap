<?php
include_once '../Skill_Swap/code.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>SkillSwap - Sign In</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <style>
        /* ===== Mentor Template Inspired Colors ===== */
        body {
            font-family: 'Heebo', sans-serif;
            background: #f3f6f9;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
        }

        .signin-container {
            display: flex;
            max-width: 950px;
            width: 100%;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            animation: slideIn 0.8s ease-in-out;
        }

        /* ===== Left Side (Info Panel) ===== */
        .signin-left {
            flex: 1;
            background: linear-gradient(135deg, #5FCF80, #38a169);
            color: #fff;
            padding: 50px 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            animation: fadeInLeft 1s ease;
        }

        .signin-left h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .signin-left p {
            font-size: 15px;
            line-height: 1.6;
        }

        /* ===== Right Side (Form Panel) ===== */
        .signin-right {
            flex: 1;
            padding: 40px 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            animation: fadeInRight 1s ease;
        }

        .signin-right h3 {
            font-weight: 700;
            color: #5FCF80;
            margin-bottom: 25px;
            text-align: center;
        }

        .form-control {
            width: 95%;
            padding: 12px 15px;
            margin-bottom: 18px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            transition: 0.3s;
        }

        .form-control:focus {
            border-color: #5FCF80;
            box-shadow: 0 0 8px rgba(95,207,128,0.4);
            outline: none;
        }

        .btn-primary {
            background: #5FCF80;
            border: none;
            padding: 12px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 8px;
            color: #fff;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-primary:hover {
            background: #4ca96a;
        }

        .extra-links {
            margin-top: 15px;
            text-align: center;
            font-size: 14px;
        }

        .extra-links a {
            color: #5FCF80;
            text-decoration: none;
            transition: 0.3s;
        }

        .extra-links a:hover {
            text-decoration: underline;
        }

        .form-check {
            margin-bottom: 15px;
        }

        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 6px;
            font-size: 14px;
        }

        .alert-success {
            background: #d1e7dd;
            color: #0f5132;
        }

        .alert-danger {
            background: #f8d7da;
            color: #842029;
        }

        /* ===== Animations ===== */
        @keyframes slideIn {
            from { transform: translateY(-40px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes fadeInLeft {
            from { transform: translateX(-50px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes fadeInRight {
            from { transform: translateX(50px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
</head>
<body>

<div class="signin-container">
    <!-- Left Info Panel -->
    <div class="signin-left">
        <h2>Welcome Back!</h2>
        <p>Sign in to continue your journey with SkillSwap.  
        Connect, Learn and Grow with peers across multiple skills.</p>
    </div>

    <!-- Right Form Panel -->
    <div class="signin-right">
        <h3>Sign In</h3>

        <!-- Messages -->
        <?php if(isset($_SESSION['msg'])): ?>
            <div class="alert alert-<?=$_SESSION['msg_type']?>">
                <?=$_SESSION['msg']?>
            </div>
            <?php unset($_SESSION['msg']); unset($_SESSION['msg_type']); ?>
        <?php endif; ?>

        <form action="../Skill_Swap/code.php" method="POST" onsubmit="return validateForm()">
            <input type="email" class="form-control" name="email" id="email" placeholder="Email Address" required>
            <input type="password" class="form-control" name="password" id="password" placeholder="Password" required>
            
            <div class="form-check">
                <input type="checkbox" id="remember">
                <label for="remember"> Remember Me </label>
            </div>

            <button type="submit" name="signin_btn" class="btn-primary w-100">Sign In</button>

            <div class="extra-links">
                <a href="#">Forgot Password?</a> | 
                <a href="signup.php">Create Account</a>
            </div>
        </form>
    </div>
</div>

<!-- Validation Script -->
<script>
function validateForm(){
    let email = document.getElementById("email").value.trim();
    let pass  = document.getElementById("password").value.trim();

    let emailRegex = /^[\\w-\\.]+@([\\w-]+\\.)+[\\w-]{2,4}$/;
    let passRegex  = /^.{6,}$/;

    if(!emailRegex.test(email)){
        alert("Invalid email format");
        return false;
    }
    if(!passRegex.test(pass)){
        alert("Password must be at least 6 characters");
        return false;
    }
    return true;
}
</script>

</body>
</html>
