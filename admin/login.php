<?php
session_start();
require_once "../includes/db.php";

// Redirect if already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];

    // Fetch admin with matching phone
    $stmt = $pdo->prepare("SELECT * FROM members WHERE phone = ? AND role = 'admin'");
    $stmt->execute([$phone]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        // Password correct → create session
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_name'] = $admin['name'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "❌ Invalid phone number or password.";
    }
}
?>

<?php include "../includes/admin_navbar.php"; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-center text-success mb-4">Admin Login</h3>
                    <?php if ($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Login</button>
                    </form>
                </div>
            </div>
            <p class="text-center mt-3"><a href="../index.php" class="text-success">Back to Home</a></p>
        </div>
    </div>
</div>

<?php include "../includes/footer.php"; ?>
