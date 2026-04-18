<?php
ob_start();
session_start();
include 'config.php';

// Already logged in? Redirect away from login page
if (!empty($_SESSION['usermail'])) {
    if (!empty($_SESSION['is_admin'])) {
        header('Location: admin/admin.php');
    } else {
        header('Location: home.php');
    }
    exit();
}

function prepareAndExecute($conn, $sql, $params)
{
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception('DB prepare error: ' . $conn->error);
    }
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    $stmt->execute();
    return $stmt;
}

// ---- User Login ----
if (isset($_POST['user_login_submit'])) {
    try {
        $email    = trim($_POST['Email']);
        $password = $_POST['Password'];
        $sql  = "SELECT * FROM signup WHERE Email = ? AND Password = BINARY ?";
        $stmt = prepareAndExecute($conn, $sql, [$email, $password]);
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $_SESSION['usermail'] = $email;
            $_SESSION['is_admin'] = false;
            header('Location: home.php');
            exit();
        } else {
            $login_error = "Invalid email or password.";
        }
    } catch (Exception $e) {
        $login_error = "Database error. Please make sure the database is set up correctly.";
    }
}

// ---- Staff / Admin Login ----
if (isset($_POST['Emp_login_submit'])) {
    try {
        $email    = trim($_POST['Emp_Email']);
        $password = $_POST['Emp_Password'];
        $sql  = "SELECT * FROM emp_login WHERE Emp_Email = ? AND Emp_Password = BINARY ?";
        $stmt = prepareAndExecute($conn, $sql, [$email, $password]);
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $_SESSION['usermail'] = $email;
            $_SESSION['is_admin'] = true;
            header('Location: admin/admin.php');
            exit();
        } else {
            $staff_error = "Invalid staff email or password.";
        }
    } catch (Exception $e) {
        $staff_error = "Database error. Please make sure the database is set up correctly.";
    }
}

// ---- Sign Up ----
if (isset($_POST['user_signup_submit'])) {
    $username  = trim($_POST['Username']);
    $email     = trim($_POST['SignupEmail']);
    $password  = $_POST['SignupPassword'];
    $cpassword = $_POST['CPassword'];

    if ($username == "" || $email == "" || $password == "") {
        $signup_error = "Please fill in all fields.";
    } elseif ($password !== $cpassword) {
        $signup_error = "Passwords do not match.";
    } else {
        $sql_check = "SELECT * FROM signup WHERE Email = ?";
        $stmt_check = prepareAndExecute($conn, $sql_check, [$email]);
        $result = $stmt_check->get_result();
        if ($result->num_rows > 0) {
            $signup_error = "Email already registered.";
        } else {
            $sql_insert = "INSERT INTO signup (Username, Email, Password) VALUES (?, ?, ?)";
            $stmt_insert = prepareAndExecute($conn, $sql_insert, [$username, $email, $password]);
            if ($stmt_insert->affected_rows > 0) {
                $_SESSION['usermail'] = $email;
                $_SESSION['is_admin'] = false;
                header('Location: home.php');
                exit();
            } else {
                $signup_error = "Registration failed. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/login.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
    <script src="https://cdn.jsdelivr.net/npm/pace-js@latest/pace.min.js"></script>
    <link rel="stylesheet" href="./css/flash.css">
    <title>Hotel Blue Bird</title>
    <style>.error-msg { color:#dc3545; font-size:14px; margin:6px 0 0; text-align:center; }</style>
</head>
<body>
    <section id="carouselExampleControls" class="carousel slide carousel_section" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active"><img class="carousel-image" src="./image/hotel1.jpg"></div>
            <div class="carousel-item"><img class="carousel-image" src="./image/hotel2.jpg"></div>
            <div class="carousel-item"><img class="carousel-image" src="./image/hotel3.jpg"></div>
            <div class="carousel-item"><img class="carousel-image" src="./image/hotel4.jpg"></div>
        </div>
    </section>

    <section id="auth_section">
        <div class="logo">
            <img class="bluebirdlogo" src="./image/bluebirdlogo.png" alt="logo">
            <p>BLUEBIRD</p>
        </div>
        <div class="auth_container">

            <!-- LOGIN -->
            <div id="Log_in">
                <h2>Log In</h2>
                <div class="role_btn">
                    <div class="btns active">User</div>
                    <div class="btns">Staff</div>
                </div>

                <!-- User Login Form -->
                <form class="user_login authsection active" id="userlogin" action="" method="POST">
                    <?php if (!empty($login_error)): ?>
                        <p class="error-msg"><?php echo htmlspecialchars($login_error); ?></p>
                    <?php endif; ?>
                    <div class="form-floating">
                        <input type="email" class="form-control" name="Email" placeholder=" " required>
                        <label>Email</label>
                    </div>
                    <div class="form-floating">
                        <input type="password" class="form-control" name="Password" placeholder=" " required>
                        <label>Password</label>
                    </div>
                    <button type="submit" name="user_login_submit" class="auth_btn">Log in</button>
                    <div class="footer_line">
                        <h6>Don't have an account? <span class="page_move_btn" onclick="signuppage()">Sign up</span></h6>
                    </div>
                </form>

                <!-- Staff Login Form -->
                <form class="employee_login authsection" id="employeelogin" action="" method="POST">
                    <?php if (!empty($staff_error)): ?>
                        <p class="error-msg"><?php echo htmlspecialchars($staff_error); ?></p>
                    <?php endif; ?>
                    <div class="form-floating">
                        <input type="email" class="form-control" name="Emp_Email" placeholder=" " required>
                        <label>Staff Email</label>
                    </div>
                    <div class="form-floating">
                        <input type="password" class="form-control" name="Emp_Password" placeholder=" " required>
                        <label>Password</label>
                    </div>
                    <button type="submit" name="Emp_login_submit" class="auth_btn">Log in</button>
                </form>
            </div>

            <!-- SIGN UP -->
            <div id="sign_up">
                <h2>Sign Up</h2>
                <form class="user_signup" id="usersignup" action="" method="POST">
                    <?php if (!empty($signup_error)): ?>
                        <p class="error-msg"><?php echo htmlspecialchars($signup_error); ?></p>
                    <?php endif; ?>
                    <div class="form-floating">
                        <input type="text" class="form-control" name="Username" placeholder=" " required>
                        <label>Username</label>
                    </div>
                    <div class="form-floating">
                        <input type="email" class="form-control" name="SignupEmail" placeholder=" " required>
                        <label>Email</label>
                    </div>
                    <div class="form-floating">
                        <input type="password" class="form-control" name="SignupPassword" placeholder=" " required>
                        <label>Password</label>
                    </div>
                    <div class="form-floating">
                        <input type="password" class="form-control" name="CPassword" placeholder=" " required>
                        <label>Confirm Password</label>
                    </div>
                    <button type="submit" name="user_signup_submit" class="auth_btn">Sign up</button>
                    <div class="footer_line">
                        <h6>Already have an account? <span class="page_move_btn" onclick="loginpage()">Log in</span></h6>
                    </div>
                </form>
            </div>

        </div>
    </section>

    <script src="./javascript/index.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>AOS.init();</script>

    <?php if (!empty($login_error)): ?>
    <script>swal({ title: "Login Failed", text: "<?php echo addslashes($login_error); ?>", icon: "error" });</script>
    <?php endif; ?>
    <?php if (!empty($staff_error)): ?>
    <script>swal({ title: "Login Failed", text: "<?php echo addslashes($staff_error); ?>", icon: "error" });</script>
    <?php endif; ?>
    <?php if (!empty($signup_error)): ?>
    <script>swal({ title: "Sign Up Failed", text: "<?php echo addslashes($signup_error); ?>", icon: "error" });</script>
    <?php endif; ?>

</body>
</html>
