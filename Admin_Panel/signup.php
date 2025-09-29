<?php
include_once '../Skill_Swap/code.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>SkillSwap - Sign Up</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <style>
        body {
            font-family: 'Heebo', sans-serif;
            background: #f3f6f9;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 10px 0;
        }

        .signup-container {
            display: flex;
            max-width: 1100px;
            width: 100%;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            animation: slideIn 0.8s ease-in-out;
        }

        /* Left Info Panel */
        .signup-left {
            flex: 1;
            background: linear-gradient(135deg, #5FCF80, #4BBF6F);
            color: #fff;
            padding: 50px 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            animation: fadeInLeft 1s ease;
        }

        .signup-left h2 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .signup-left p {
            font-size: 15px;
            line-height: 1.6;
        }

        /* Right Form Panel */
        .signup-right {
            flex: 2;
            padding: 40px 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            animation: fadeInRight 1s ease;
        }

        .signup-right h3 {
            font-weight: 700;
            color: #5FCF80;
            margin-bottom: 25px;
            text-align: center;
        }

        .form-control,
        .form-select,
        textarea {
            width: 95%;
            padding: 12px 15px;
            margin-bottom: 18px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            transition: 0.3s;
        }

        .form-control:focus,
        .form-select:focus,
        textarea:focus {
            border-color: #5FCF80;
            box-shadow: 0 0 8px rgba(95, 207, 128, 0.3);
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
            background: #4BBF6F;
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

        @keyframes slideIn {
            from {
                transform: translateY(-40px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes fadeInLeft {
            from {
                transform: translateX(-50px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeInRight {
            from {
                transform: translateX(50px);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
    </style>
</head>

<body>

    <div class="signup-container">
        <!-- Left Info Panel -->
        <div class="signup-left">
            <h2>Join SkillSwap</h2>
            <p>Create your account and start sharing & learning skills with others.
                Connect with trainers and learners worldwide to grow together.</p>
        </div>

        <!-- Right Form Panel -->
        <div class="signup-right">
            <h3>Create Account</h3>

            <!-- Messages -->
            <?php if (isset($_SESSION['msg'])): ?>
                <div class="alert alert-<?= $_SESSION['msg_type'] ?>">
                    <?= $_SESSION['msg'] ?>
                </div>
                <?php unset($_SESSION['msg']);
                unset($_SESSION['msg_type']); ?>
            <?php endif; ?>

            <form action="../Skill_Swap/code.php" method="POST" enctype="multipart/form-data"
                onsubmit="return validateForm()">

                <input type="text" class="form-control" name="username" id="username" placeholder="Username" required>
                <input type="email" class="form-control" name="email" id="email" placeholder="Email address" required>
                <input type="password" class="form-control" name="password" id="password" placeholder="Password"
                    required>
                <input type="password" class="form-control" name="confirm_password" id="confirm_password"
                    placeholder="Confirm Password" required>
                <textarea class="form-control" name="bio" id="bio" placeholder="Short Bio" style="height:80px;"
                    required></textarea>
                <input type="text" class="form-control" name="phone" id="phone" placeholder="03xxxxxxxxx" required>
                <input type="text" class="form-control" name="city" id="city" placeholder="City" required>
                <input type="text" class="form-control" name="country" id="country" placeholder="Country" required>

                <select class="form-control" name="role" id="role" required>
                    <option value="">Select Role</option>
                    <option value="user">User</option>
                    <option value="trainer">Trainer</option>
                    <option value="both">Both</option>
                </select>

                <label style="margin-bottom:5px;">Profile Picture</label>
                <input type="file" class="form-control" name="profile_picture" accept="image/*">

                <button type="submit" name="register_btn" class="btn-primary w-100 mt-3">Sign Up</button>

                <div class="extra-links">
                    Already have an account? <a href="signin.php">Sign In</a>
                </div>
            </form>
        </div>
    </div>

    <!-- JS Validation -->
    <script>
        function validateForm() {
            let uname = document.getElementById("username").value.trim();
            let email = document.getElementById("email").value.trim();
            let pass = document.getElementById("password").value.trim();
            let cpass = document.getElementById("confirm_password").value.trim();
            let bio = document.getElementById("bio").value.trim();
            let phone = document.getElementById("phone").value.trim();
            let city = document.getElementById("city").value.trim();
            let country = document.getElementById("country").value.trim();
            let role = document.getElementById("role").value;

            let unameRegex = /^[a-zA-Z0-9_]{3,15}$/;
            let emailRegex = /^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/;
            let passRegex = /^.{6,}$/;
            let bioRegex = /^.{10,200}$/;
            let phoneRegex = /^03[0-9]{9}$/;
            let cityRegex = /^[a-zA-Z\s]{2,30}$/;
            let countryRegex = /^[a-zA-Z\s]{2,30}$/;

            if (!unameRegex.test(uname)) {
                alert("Username must be 3-15 chars, only letters, numbers, underscore allowed.");
                return false;
            }
            if (!emailRegex.test(email)) {
                alert("Invalid email format.");
                return false;
            }
            if (!passRegex.test(pass)) {
                alert("Password must be at least 6 characters.");
                return false;
            }
            if (pass !== cpass) {
                alert("Passwords do not match.");
                return false;
            }
            if (!bioRegex.test(bio)) {
                alert("Bio must be between 10 to 200 characters.");
                return false;
            }
            if (!phoneRegex.test(phone)) {
                alert("Phone must be in format 03XXXXXXXXX.");
                return false;
            }
            if (!cityRegex.test(city)) {
                alert("City should only contain letters, min 2 chars.");
                return false;
            }
            if (!countryRegex.test(country)) {
                alert("Country should only contain letters, min 2 chars.");
                return false;
            }
            if (role === "") {
                alert("Please select a role.");
                return false;
            }
            return true;
        }
    </script>

</body>

</html>
