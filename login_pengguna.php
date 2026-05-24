<?php require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = $_POST['phone_number'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, full_name, password FROM users WHERE phone_number = ? AND role = 'user'");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if ($user['password'] === $password) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = 'user';
            header('Location: dashboard_pengguna.php');
            exit();
        }
    }
    $error = "Maklumat tidak sah atau bukan akaun pengguna.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Masuk - VouteX KKJR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap');
        body {
            background: linear-gradient(135deg, #0f3460 0%, #16213e 50%, #1a1a2e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .login-card {
            background: rgba(22, 33, 62, 0.95);
            border-radius: 25px;
            padding: 50px 40px;
            box-shadow: 0 10px 50px rgba(255, 215, 0, 0.4), 0 0 30px rgba(233, 69, 96, 0.3);
            max-width: 420px;
            width: 100%;
            border: 2px solid #ffd700;
        }
        .login-card h2 {
            text-align: center;
            margin-bottom: 10px;
            color: #e94560;
            font-family: 'Orbitron', sans-serif;
            text-shadow: 0 0 10px #e94560;
        }
        .login-card .subtitle {
            text-align: center;
            color: #ffd700;
            margin-bottom: 30px;
            font-size: 0.9rem;
        }
        .btn-pyramid {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #ffd700 0%, #ffeb3b 100%);
            border: none;
            color: #1a1a2e;
            font-weight: bold;
            border-radius: 10px;
            transition: all 0.3s;
        }
        .btn-pyramid:hover {
            background: linear-gradient(135deg, #e94560 0%, #ff6b8a 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(233, 69, 96, 0.5);
        }
        .logo-pyramid {
            width: 100px;
            height: 100px;
            margin: 0 auto 20px;
            position: relative;
        }
        .logo-pyramid::before {
            content: '';
            position: absolute;
            width: 0;
            height: 0;
            border-left: 50px solid transparent;
            border-right: 50px solid transparent;
            border-bottom: 80px solid #e94560;
            top: 0;
            left: 0;
        }
        .logo-pyramid::after {
            content: '';
            position: absolute;
            width: 0;
            height: 0;
            border-left: 30px solid transparent;
            border-right: 30px solid transparent;
            border-bottom: 50px solid #ffd700;
            top: 20px;
            left: 20px;
        }
        .form-control {
            background: rgba(15, 52, 96, 0.8);
            border: 1px solid #ffd700;
            color: #fff;
        }
        .form-control:focus {
            background: rgba(15, 52, 96, 1);
            border-color: #e94560;
            color: #fff;
            box-shadow: 0 0 10px rgba(233, 69, 96, 0.3);
        }
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }
        label {
            color: #ffd700;
            font-weight: 500;
        }
        .alert {
            border-radius: 10px;
        }
        .alert-danger {
            background: rgba(220, 53, 69, 0.3);
            border: 1px solid #dc3545;
            color: #ff6b8a;
        }
        .link-text {
            color: #a0a0a0;
            text-decoration: none;
            transition: color 0.3s;
        }
        .link-text:hover {
            color: #ffd700;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .logo-pyramid {
            animation: float 3s ease-in-out infinite;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo-pyramid"></div>
        <h2>Log Masuk Pengguna</h2>
        <div class="subtitle">VouteX KKJR</div>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nombor Telefon</label>
                <input type="text" name="phone_number" class="form-control" pattern="[0-9]{10}" maxlength="10" required placeholder="10 digit">
            </div>
            <div class="mb-4">
                <label class="form-label">Kata Laluan</label>
                <input type="password" name="password" class="form-control" required placeholder="Masukkan kata laluan">
            </div>
            <button type="submit" class="btn btn-pyramid">Masuk</button>
        </form>
        <div class="text-center mt-4">
            <a href="./daftar_pengguna.php" class="link-text">Daftar Akaun Baru</a> |
            <a href="./login_admin.php" class="link-text">Log Masuk Admin</a>
        </div>
    </div>
</body>
</html>
