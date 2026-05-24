<?php require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone_number']);
    $password = $_POST['password'];

    $check = $conn->prepare("SELECT id FROM users WHERE phone_number = ?");
    $check->bind_param("s", $phone);
    $check->execute();

    if ($check->get_result()->num_rows > 0) {
        $error = "Nombor telefon sudah berdaftar.";
    } elseif (strlen($phone) !== 10 || !ctype_digit($phone)) {
        $error = "Nombor telefon mesti tepat 10 digit.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (full_name, phone_number, password, role) VALUES (?, ?, ?, 'user')");
        $stmt->bind_param("sss", $full_name, $phone, $password);
        if ($stmt->execute()) {
            $success = "Pendaftaran berjaya. Sila gelogin.";
        } else {
            $error = "Pendaftaran gagal.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Pengguna - VouteX KKJR</title>
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
        .register-card {
            background: rgba(22, 33, 62, 0.95);
            border-radius: 25px;
            padding: 50px 40px;
            box-shadow: 0 10px 50px rgba(255, 215, 0, 0.4), 0 0 30px rgba(233, 69, 96, 0.3);
            max-width: 420px;
            width: 100%;
            border: 2px solid #0f3460;
        }
        .register-card h2 {
            text-align: center;
            margin-bottom: 10px;
            color: #0f3460;
            font-family: 'Orbitron', sans-serif;
            text-shadow: 0 0 10px #0f3460;
        }
        .register-card .subtitle {
            text-align: center;
            color: #ffd700;
            margin-bottom: 30px;
            font-size: 0.9rem;
        }
        .btn-pyramid {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #0f3460 0%, #16213e 100%);
            border: 2px solid #ffd700;
            color: #ffd700;
            font-weight: bold;
            border-radius: 10px;
            transition: all 0.3s;
        }
        .btn-pyramid:hover {
            background: linear-gradient(135deg, #ffd700 0%, #ffeb3b 100%);
            color: #1a1a2e;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 215, 0, 0.5);
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
            border-bottom: 80px solid #0f3460;
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
            border: 1px solid #0f3460;
            color: #ffd700;
        }
        .form-control:focus {
            background: rgba(15, 52, 96, 1);
            border-color: #ffd700;
            color: #ffd700;
            box-shadow: 0 0 10px rgba(255, 215, 0, 0.3);
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
        .alert-success {
            background: rgba(40, 167, 69, 0.3);
            border: 1px solid #28a745;
            color: #7fff7f;
        }
        .link-text {
            color: #a0a0a0;
            text-decoration: none;
            transition: color 0.3s;
        }
        .link-text:hover {
            color: #0f3460;
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
    <div class="register-card">
        <div class="logo-pyramid"></div>
        <h2>Pendaftaran</h2>
        <div class="subtitle">VouteX KKJR</div>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nama Penuh</label>
                <input type="text" name="full_name" class="form-control" required placeholder="Masukkan nama penuh">
            </div>
            <div class="mb-3">
                <label class="form-label">Nombor Telefon (10 digit)</label>
                <input type="text" name="phone_number" class="form-control" pattern="[0-9]{10}" maxlength="10" required placeholder="10 digit">
            </div>
            <div class="mb-4">
                <label class="form-label">Kata Laluan</label>
                <input type="password" name="password" class="form-control" required placeholder="Cipta kata laluan">
            </div>
            <button type="submit" class="btn btn-pyramid">Daftar</button>
        </form>
        <div class="text-center mt-4">
            <a href="./login_pengguna.php" class="link-text">Sudah ada akaun? Log Masuk</a>
        </div>
    </div>
</body>
</html>
