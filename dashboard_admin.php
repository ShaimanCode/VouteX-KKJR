<?php require_once 'config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login_admin.php');
    exit();
}

// CRUD PENGGUNA
if (isset($_POST['add_user'])) {
    $name = $_POST['user_name'];
    $phone = $_POST['user_phone'];
    $pass = $_POST['user_password'];
    $role = $_POST['user_role'];
    $conn->query("INSERT INTO users (full_name, phone_number, password, role) VALUES ('$name', '$phone', '$pass', '$role')");
}
if (isset($_GET['delete_user'])) {
    $id = intval($_GET['delete_user']);
    $conn->query("DELETE FROM users WHERE id = $id AND role != 'admin'");
}
if (isset($_POST['edit_user'])) {
    $id = intval($_POST['edit_user_id']);
    $name = $_POST['edit_user_name'];
    $phone = $_POST['edit_user_phone'];
    $pass = $_POST['edit_user_password'];
    $role = $_POST['edit_user_role'];
    $conn->query("UPDATE users SET full_name='$name', phone_number='$phone', password='$pass', role='$role' WHERE id=$id");
}

// CRUD JAWATAN
if (isset($_POST['add_position'])) {
    $pos = $_POST['position_name'];
    $conn->query("INSERT INTO positions (position_name) VALUES ('$pos')");
}
if (isset($_GET['delete_position'])) {
    $id = intval($_GET['delete_position']);
    $conn->query("DELETE FROM positions WHERE id = $id");
}
if (isset($_POST['edit_position'])) {
    $id = intval($_POST['edit_position_id']);
    $name = $_POST['edit_position_name'];
    $conn->query("UPDATE positions SET position_name='$name' WHERE id=$id");
}

// CRUD CALON
$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

if (isset($_POST['add_candidate'])) {
    $cnum = $_POST['candidate_number'];
    $cname = $_POST['candidate_name'];
    $cclass = $_POST['candidate_class'];
    $pid = $_POST['position_id'];
    $imgName = 'default.jpg';
    if (!empty($_FILES['candidate_image']['name'])) {
        $imgName = time() . '_' . basename($_FILES['candidate_image']['name']);
        move_uploaded_file($_FILES['candidate_image']['tmp_name'], $uploadDir . $imgName);
    }
    $conn->query("INSERT INTO candidates (candidate_number, candidate_name, candidate_class, candidate_image, position_id) VALUES ('$cnum', '$cname', '$cclass', '$imgName', $pid)");
}
if (isset($_GET['delete_candidate'])) {
    $id = intval($_GET['delete_candidate']);
    $conn->query("DELETE FROM candidates WHERE id = $id");
}
if (isset($_POST['edit_candidate'])) {
    $id = intval($_POST['edit_candidate_id']);
    $cnum = $_POST['edit_candidate_number'];
    $cname = $_POST['edit_candidate_name'];
    $cclass = $_POST['edit_candidate_class'];
    $pid = $_POST['edit_position_id'];
    $imgSql = "";
    if (!empty($_FILES['edit_candidate_image']['name'])) {
        $imgName = time() . '_' . basename($_FILES['edit_candidate_image']['name']);
        move_uploaded_file($_FILES['edit_candidate_image']['tmp_name'], $uploadDir . $imgName);
        $imgSql = ", candidate_image='$imgName'";
    }
    $conn->query("UPDATE candidates SET candidate_number='$cnum', candidate_name='$cname', candidate_class='$cclass', position_id=$pid $imgSql WHERE id=$id");
}

// IMPORT CALON
if (isset($_POST['import_candidates']) && !empty($_FILES['import_file']['tmp_name'])) {
    $file = fopen($_FILES['import_file']['tmp_name'], 'r');
    while (($line = fgets($file)) !== false) {
        $data = explode('|', trim($line));
        if (count($data) >= 4) {
            $cnum = $conn->real_escape_string($data[0]);
            $cname = $conn->real_escape_string($data[1]);
            $cclass = $conn->real_escape_string($data[2]);
            $pid = intval($data[3]);
            $conn->query("INSERT INTO candidates (candidate_number, candidate_name, candidate_class, candidate_image, position_id) VALUES ('$cnum', '$cname', '$cclass', 'default.jpg', $pid)");
        }
    }
    fclose($file);
}

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'history';
$history_search = isset($_GET['history_search']) ? trim($_GET['history_search']) : '';
$history_search_escaped = $conn->real_escape_string($history_search);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - VouteX KKJR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap');
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); color: #f8f9fa; }
        [data-theme="dark"] { background-color: #212529 !important; color: #f8f9fa !important; }
        [data-theme="dark"] .card { background-color: #343a40 !important; }
        .content-wrapper { transition: transform 0.3s; }
        .nav-tabs { border-bottom: 2px solid #e94560; }
        .nav-tabs .nav-link { color: #ffd700; border: none; border-radius: 8px 8px 0 0; transition: all 0.3s; }
        .nav-tabs .nav-link:hover { background: rgba(233, 69, 96, 0.2); color: #fff; }
        .nav-tabs .nav-link.active { background: linear-gradient(135deg, #e94560 0%, #ff6b8a 100%); color: #fff; border: none; }
        .card { background: rgba(15, 52, 96, 0.95); border: 1px solid #e94560; border-radius: 15px; box-shadow: 0 5px 20px rgba(233, 69, 96, 0.3); }
        .card-body h5 { color: #ffd700; font-family: 'Orbitron', sans-serif; text-shadow: 0 0 10px rgba(255, 215, 0, 0.5); }
        .table { color: #f8f9fa; }
        .table thead th { background: linear-gradient(135deg, #e94560 0%, #ff6b8a 100%); color: #fff; border: none; }
        .table tbody tr:hover { background: rgba(233, 69, 96, 0.2); }
        .btn-pyramid-primary { background: linear-gradient(135deg, #e94560 0%, #ff6b8a 100%); border: none; color: white; border-radius: 8px; }
        .btn-pyramid-primary:hover { background: linear-gradient(135deg, #ff6b8a 0%, #ffd700 100%); transform: translateY(-2px); box-shadow: 0 3px 15px rgba(255, 215, 0, 0.4); }
        .btn-pyramid-warning { background: linear-gradient(135deg, #ffd700 0%, #ffeb3b 100%); border: none; color: #1a1a2e; border-radius: 8px; }
        .btn-pyramid-danger { background: linear-gradient(135deg, #ff3545 0%, #dc3545 100%); border: none; color: white; border-radius: 8px; }
        .btn-pyramid-secondary { background: rgba(15, 52, 96, 0.8); border: 1px solid #ffd700; color: #ffd700; border-radius: 8px; }
        .modal-content { background: linear-gradient(135deg, #16213e 0%, #0f3460 100%); color: #f8f9fa; border: 2px solid #e94560; border-radius: 15px; }
        .modal-header { border-bottom: 1px solid #e94560; }
        .modal-header .btn-close { filter: invert(1); }
        .form-control, .form-select { background: rgba(15, 52, 96, 0.6); border: 1px solid #e94560; color: #fff; border-radius: 8px; }
        .form-control::placeholder { color: rgba(255, 255, 255, 0.85); opacity: 1; }
        .form-control[type="search"] { color: #ffffff; text-shadow: 0 0 2px rgba(0, 0, 0, 0.4); }
        .form-control:focus, .form-select:focus { background: rgba(15, 52, 96, 0.9); border-color: #ffd700; color: #fff; box-shadow: 0 0 10px rgba(255, 215, 0, 0.3); }
        label { color: #ffd700; }
        @media print { .control-panel, .nav-tabs, .btn { display: none !important; } body { background: white !important; color: black !important; } }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="content-wrapper">
<div class="container-fluid mt-3">
    <ul class="nav nav-tabs">
        <li class="nav-item"><a class="nav-link <?php echo $tab=='history'?'active':''; ?>" href="?tab=history">Sejarah Undian</a></li>
        <li class="nav-item"><a class="nav-link <?php echo $tab=='users'?'active':''; ?>" href="?tab=users">Urus Pengguna</a></li>
        <li class="nav-item"><a class="nav-link <?php echo $tab=='positions'?'active':''; ?>" href="?tab=positions">Urus Jawatan</a></li>
        <li class="nav-item"><a class="nav-link <?php echo $tab=='candidates'?'active':''; ?>" href="?tab=candidates">Urus Calon</a></li>
        <li class="nav-item"><a class="nav-link <?php echo $tab=='report'?'active':''; ?>" href="?tab=report">Laporan Undian</a></li>
        <li class="nav-item"><a class="nav-link <?php echo $tab=='graph'?'active':''; ?>" href="?tab=graph">Graf Undian</a></li>
    </ul>

    <div class="tab-content mt-3">
        <?php if ($tab == 'history'): ?>
        <div class="card"><div class="card-body">
            <h5>Sejarah Undian</h5>
            <form method="GET" class="row g-2 align-items-center mb-3">
                <input type="hidden" name="tab" value="history">
                <div class="col-sm-8 col-md-6">
                    <input type="search" name="history_search" class="form-control" placeholder="Cari nama calon atau pengguna" value="<?php echo htmlspecialchars($history_search); ?>">
                </div>
                <div class="col-auto">
                    <button class="btn btn-pyramid-primary btn-sm" type="submit">Cari</button>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-pyramid-secondary btn-sm" onclick="window.print()">Cetak</button>
                </div>
            </form>
            <table class="table table-bordered">
                <thead><tr><th>#</th><th>Pengguna</th><th>Calon</th><th>Jawatan</th><th>Tarikh</th></tr></thead>
                <tbody>
                    <?php
                    $historyWhere = "1=1";
                    if ($history_search_escaped !== '') {
                        $historyWhere .= " AND (u.full_name LIKE '%$history_search_escaped%' OR c.candidate_name LIKE '%$history_search_escaped%')";
                    }
                    $r = $conn->query("SELECT v.*, u.full_name as uname, c.candidate_name, p.position_name FROM votes v JOIN users u ON v.user_id=u.id JOIN candidates c ON v.candidate_id=c.id JOIN positions p ON v.position_id=p.id WHERE $historyWhere ORDER BY v.vote_date DESC");
                    $i=1; while($row = $r->fetch_assoc()): ?>
                    <tr><td><?php echo $i++; ?></td><td><?php echo $row['uname']; ?></td><td><?php echo $row['candidate_name']; ?></td><td><?php echo $row['position_name']; ?></td><td><?php echo $row['vote_date']; ?></td></tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div></div>

        <?php elseif ($tab == 'users'): ?>
        <div class="card"><div class="card-body">
            <h5>Urus Pengguna</h5>
            <button class="btn btn-pyramid-primary btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#addUserModal">Tambah Pengguna</button>
            <button class="btn btn-pyramid-secondary btn-sm mb-2" onclick="window.print()">Cetak</button>
            <table class="table table-bordered">
                <thead><tr><th>#</th><th>Nama</th><th>Telefon</th><th>Peranan</th><th>Tindakan</th></tr></thead>
                <tbody>
                    <?php $r = $conn->query("SELECT * FROM users"); $i=1;
                    while($row = $r->fetch_assoc()): ?>
                    <tr><td><?php echo $i++; ?></td><td><?php echo $row['full_name']; ?></td><td><?php echo $row['phone_number']; ?></td><td><?php echo $row['role'] == 'admin' ? 'Pentadbir' : 'Pengguna'; ?></td>
                    <td>
                        <button class="btn btn-pyramid-warning btn-sm" onclick="editUser(<?php echo htmlspecialchars(json_encode($row)); ?>)">Edit</button>
                        <?php if ($row['role'] != 'admin'): ?>
                        <a href="?tab=users&delete_user=<?php echo $row['id']; ?>" class="btn btn-pyramid-danger btn-sm" onclick="return confirm('Padam pengguna ini?')">Padam</a>
                        <?php endif; ?>
                    </td></tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div></div>

        <?php elseif ($tab == 'positions'): ?>
        <div class="card"><div class="card-body">
            <h5>Urus Jawatan</h5>
            <button class="btn btn-pyramid-primary btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#addPositionModal">Tambah Jawatan</button>
            <button class="btn btn-pyramid-secondary btn-sm mb-2" onclick="window.print()">Cetak</button>
            <table class="table table-bordered">
                <thead><tr><th>#</th><th>Nama Jawatan</th><th>Tindakan</th></tr></thead>
                <tbody>
                    <?php $r = $conn->query("SELECT * FROM positions"); $i=1;
                    while($row = $r->fetch_assoc()): ?>
                    <tr><td><?php echo $i++; ?></td><td><?php echo $row['position_name']; ?></td>
                    <td>
                        <button class="btn btn-pyramid-warning btn-sm" onclick="editPosition(<?php echo $row['id']; ?>, '<?php echo addslashes($row['position_name']); ?>')">Edit</button>
                        <a href="?tab=positions&delete_position=<?php echo $row['id']; ?>" class="btn btn-pyramid-danger btn-sm" onclick="return confirm('Padam jawatan ini?')">Padam</a>
                    </td></tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div></div>

        <?php elseif ($tab == 'candidates'): ?>
        <div class="card"><div class="card-body">
            <h5>Urus Calon</h5>
            <button class="btn btn-pyramid-primary btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#addCandidateModal">Tambah Calon</button>
            <button class="btn btn-pyramid-secondary btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#importModal">Import dari TXT</button>
            <button class="btn btn-pyramid-secondary btn-sm mb-2" onclick="window.print()">Cetak</button>
            <table class="table table-bordered">
                <thead><tr><th>#</th><th>No. Calon</th><th>Nama</th><th>Kelas</th><th>Jawatan</th><th>Gambar</th><th>Tindakan</th></tr></thead>
                <tbody>
                    <?php $r = $conn->query("SELECT c.*, p.position_name FROM candidates c JOIN positions p ON c.position_id=p.id"); $i=1;
                    while($row = $r->fetch_assoc()): ?>
                    <tr><td><?php echo $i++; ?></td><td><?php echo $row['candidate_number']; ?></td><td><?php echo $row['candidate_name']; ?></td><td><?php echo $row['candidate_class']; ?></td><td><?php echo $row['position_name']; ?></td>
                    <td><img src="uploads/<?php echo $row['candidate_image']; ?>" width="50" height="50" style="object-fit:cover; border:2px solid #ffd700; border-radius:5px;"></td>
                    <td>
                        <button class="btn btn-pyramid-warning btn-sm" onclick="editCandidate(<?php echo htmlspecialchars(json_encode($row)); ?>)">Edit</button>
                        <a href="?tab=candidates&delete_candidate=<?php echo $row['id']; ?>" class="btn btn-pyramid-danger btn-sm" onclick="return confirm('Padam calon ini?')">Padam</a>
                    </td></tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div></div>

        <?php elseif ($tab == 'report'): ?>
        <div class="card"><div class="card-body">
            <h5>Laporan Undian</h5>
            <form method="GET" class="row g-2 mb-3">
                <input type="hidden" name="tab" value="report">
                <div class="col-auto"><input type="date" name="date_from" class="form-control" value="<?php echo $_GET['date_from']??''; ?>"></div>
                <div class="col-auto"><input type="date" name="date_to" class="form-control" value="<?php echo $_GET['date_to']??''; ?>"></div>
                <div class="col-auto"><button class="btn btn-pyramid-primary">Tapis</button></div>
                <div class="col-auto"><button class="btn btn-pyramid-secondary" type="button" onclick="window.print()">Cetak</button></div>
            </form>
            <table class="table table-bordered">
                <thead><tr><th>No. Calon</th><th>Tarikh</th><th>Jumlah Undian</th></tr></thead>
                <tbody>
                    <?php
                    $where = "1=1";
                    if (!empty($_GET['date_from'])) $where .= " AND DATE(vote_date) >= '".$_GET['date_from']."'";
                    if (!empty($_GET['date_to'])) $where .= " AND DATE(vote_date) <= '".$_GET['date_to']."'";
                    $r = $conn->query("SELECT c.candidate_number, DATE(v.vote_date) as vdate, COUNT(*) as total FROM votes v JOIN candidates c ON v.candidate_id=c.id WHERE $where GROUP BY c.candidate_number, DATE(v.vote_date) ORDER BY vdate DESC");
                    while($row = $r->fetch_assoc()): ?>
                    <tr><td><?php echo $row['candidate_number']; ?></td><td><?php echo $row['vdate']; ?></td><td><span class="badge bg-warning text-dark"><?php echo $row['total']; ?></span></td></tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div></div>

        <?php elseif ($tab == 'graph'): ?>
        <div class="card"><div class="card-body">
            <h5>Graf Undian - Kedudukan Mengikut Jawatan</h5>
            <button class="btn btn-pyramid-secondary btn-sm mb-2" onclick="window.print()">Cetak</button>
            <canvas id="voteChart" width="400" height="200"></canvas>
            <?php
            $pos = $conn->query("SELECT * FROM positions");
            $chartData = [];
            $colors = ['#e94560', '#ffd700', '#00d4ff', '#7fff00', '#ff6b8a', '#ffeb3b'];
            while ($p = $pos->fetch_assoc()) {
                $cands = $conn->query("SELECT c.candidate_name, COUNT(v.id) as votes FROM candidates c LEFT JOIN votes v ON c.id=v.candidate_id WHERE c.position_id=".$p['id']." GROUP BY c.id ORDER BY votes DESC");
                $labels = []; $votes = [];
                while ($c = $cands->fetch_assoc()) {
                    $labels[] = $c['candidate_name'];
                    $votes[] = $c['votes'];
                }
                $chartData[] = ['position' => $p['position_name'], 'labels' => $labels, 'votes' => $votes];
            }
            ?>
            <script>
                const chartData = <?php echo json_encode($chartData); ?>;
                const ctx = document.getElementById('voteChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: chartData.flatMap(d => d.labels),
                        datasets: chartData.map((d, i) => ({
                            label: d.position,
                            data: d.votes,
                            backgroundColor: ['#e94560', '#ffd700', '#00d4ff', '#7fff00', '#ff6b8a', '#ffeb3b'][i % 6]
                        }))
                    },
                    options: { responsive: true, plugins: { legend: { labels: { color: '#fff' } } }, scales: { y: { beginAtZero: true, ticks: { color: '#fff' } }, x: { ticks: { color: '#fff' } } } }
                });
            </script>
        </div></div>
        <?php endif; ?>
    </div>
</div>
</div>

<!-- Tambah Pengguna Modal -->
<div class="modal fade" id="addUserModal"><div class="modal-dialog"><div class="modal-content">
<div class="modal-header"><h5 class="modal-title">Tambah Pengguna</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<form method="POST"><div class="modal-body">
    <div class="mb-2"><label class="form-label">Nama Penuh</label><input type="text" name="user_name" class="form-control" required></div>
    <div class="mb-2"><label class="form-label">Telefon (10 digit)</label><input type="text" name="user_phone" class="form-control" pattern="[0-9]{10}" maxlength="10" required></div>
    <div class="mb-2"><label class="form-label">Kata Laluan</label><input type="text" name="user_password" class="form-control" required></div>
    <div class="mb-2"><label class="form-label">Peranan</label><select name="user_role" class="form-select"><option value="user">Pengguna</option><option value="admin">Pentadbir</option></select></div>
</div><div class="modal-footer"><button type="submit" name="add_user" class="btn btn-pyramid-primary">Tambah</button></div></form>
</div></div></div>

<!-- Edit Pengguna Modal -->
<div class="modal fade" id="editUserModal"><div class="modal-dialog"><div class="modal-content">
<div class="modal-header"><h5 class="modal-title">Edit Pengguna</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<form method="POST"><div class="modal-body">
    <input type="hidden" name="edit_user_id" id="edit_user_id">
    <div class="mb-2"><label class="form-label">Nama Penuh</label><input type="text" name="edit_user_name" id="edit_user_name" class="form-control" required></div>
    <div class="mb-2"><label class="form-label">Telefon</label><input type="text" name="edit_user_phone" id="edit_user_phone" class="form-control" pattern="[0-9]{10}" required></div>
    <div class="mb-2"><label class="form-label">Kata Laluan</label><input type="text" name="edit_user_password" id="edit_user_password" class="form-control" required></div>
    <div class="mb-2"><label class="form-label">Peranan</label><select name="edit_user_role" id="edit_user_role" class="form-select"><option value="user">Pengguna</option><option value="admin">Pentadbir</option></select></div>
</div><div class="modal-footer"><button type="submit" name="edit_user" class="btn btn-pyramid-primary">Kemaskini</button></div></form>
</div></div></div>

<!-- Tambah Jawatan Modal -->
<div class="modal fade" id="addPositionModal"><div class="modal-dialog"><div class="modal-content">
<div class="modal-header"><h5 class="modal-title">Tambah Jawatan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<form method="POST"><div class="modal-body">
    <div class="mb-2"><label class="form-label">Nama Jawatan</label><input type="text" name="position_name" class="form-control" required placeholder="Contoh: Presiden"></div>
</div><div class="modal-footer"><button type="submit" name="add_position" class="btn btn-pyramid-primary">Tambah</button></div></form>
</div></div></div>

<!-- Edit Jawatan Modal -->
<div class="modal fade" id="editPositionModal"><div class="modal-dialog"><div class="modal-content">
<div class="modal-header"><h5 class="modal-title">Edit Jawatan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<form method="POST"><div class="modal-body">
    <input type="hidden" name="edit_position_id" id="edit_position_id">
    <div class="mb-2"><label class="form-label">Nama Jawatan</label><input type="text" name="edit_position_name" id="edit_position_name" class="form-control" required></div>
</div><div class="modal-footer"><button type="submit" name="edit_position" class="btn btn-pyramid-primary">Kemaskini</button></div></form>
</div></div></div>

<!-- Tambah Calon Modal -->
<div class="modal fade" id="addCandidateModal"><div class="modal-dialog"><div class="modal-content">
<div class="modal-header"><h5 class="modal-title">Tambah Calon</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<form method="POST" enctype="multipart/form-data"><div class="modal-body">
    <div class="mb-2"><label class="form-label">No. Calon</label><input type="text" name="candidate_number" class="form-control" required placeholder="Contoh: P001"></div>
    <div class="mb-2"><label class="form-label">Nama Calon</label><input type="text" name="candidate_name" class="form-control" required></div>
    <div class="mb-2"><label class="form-label">Kelas</label><input type="text" name="candidate_class" class="form-control" required placeholder="Contoh: 5A Sains"></div>
    <div class="mb-2"><label class="form-label">Jawatan</label><select name="position_id" class="form-select" required>
        <?php $p = $conn->query("SELECT * FROM positions"); while($pos = $p->fetch_assoc()): ?>
        <option value="<?php echo $pos['id']; ?>"><?php echo $pos['position_name']; ?></option>
        <?php endwhile; ?>
    </select></div>
    <div class="mb-2"><label class="form-label">Gambar Calon</label><input type="file" name="candidate_image" class="form-control" accept="image/*"></div>
</div><div class="modal-footer"><button type="submit" name="add_candidate" class="btn btn-pyramid-primary">Tambah</button></div></form>
</div></div></div>

<!-- Edit Calon Modal -->
<div class="modal fade" id="editCandidateModal"><div class="modal-dialog"><div class="modal-content">
<div class="modal-header"><h5 class="modal-title">Edit Calon</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<form method="POST" enctype="multipart/form-data"><div class="modal-body">
    <input type="hidden" name="edit_candidate_id" id="edit_candidate_id">
    <div class="mb-2"><label class="form-label">No. Calon</label><input type="text" name="edit_candidate_number" id="edit_candidate_number" class="form-control" required></div>
    <div class="mb-2"><label class="form-label">Nama Calon</label><input type="text" name="edit_candidate_name" id="edit_candidate_name" class="form-control" required></div>
    <div class="mb-2"><label class="form-label">Kelas</label><input type="text" name="edit_candidate_class" id="edit_candidate_class" class="form-control" required></div>
    <div class="mb-2"><label class="form-label">Jawatan</label><select name="edit_position_id" id="edit_position_id" class="form-select" required>
        <?php $p = $conn->query("SELECT * FROM positions"); while($pos = $p->fetch_assoc()): ?>
        <option value="<?php echo $pos['id']; ?>"><?php echo $pos['position_name']; ?></option>
        <?php endwhile; ?>
    </select></div>
    <div class="mb-2"><label class="form-label">Gambar (Biarkan kosong untuk kekal)</label><input type="file" name="edit_candidate_image" class="form-control" accept="image/*"></div>
</div><div class="modal-footer"><button type="submit" name="edit_candidate" class="btn btn-pyramid-primary">Kemaskini</button></div></form>
</div></div></div>

<!-- Import Modal -->
<div class="modal fade" id="importModal"><div class="modal-dialog"><div class="modal-content">
<div class="modal-header"><h5 class="modal-title">Import Calon dari TXT</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
<form method="POST" enctype="multipart/form-data"><div class="modal-body">
    <p class="text-muted">Format: NoCalon|Nama|Kelas|IDJawatan (satu per baris)</p>
    <div class="mb-2"><label class="form-label">Fail TXT</label><input type="file" name="import_file" class="form-control" accept=".txt" required></div>
</div><div class="modal-footer"><button type="submit" name="import_candidates" class="btn btn-pyramid-primary">Import</button></div></form>
</div></div></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function editUser(data) {
    document.getElementById('edit_user_id').value = data.id;
    document.getElementById('edit_user_name').value = data.full_name;
    document.getElementById('edit_user_phone').value = data.phone_number;
    document.getElementById('edit_user_password').value = data.password;
    document.getElementById('edit_user_role').value = data.role;
    new bootstrap.Modal(document.getElementById('editUserModal')).show();
}
function editPosition(id, name) {
    document.getElementById('edit_position_id').value = id;
    document.getElementById('edit_position_name').value = name;
    new bootstrap.Modal(document.getElementById('editPositionModal')).show();
}
function editCandidate(data) {
    document.getElementById('edit_candidate_id').value = data.id;
    document.getElementById('edit_candidate_number').value = data.candidate_number;
    document.getElementById('edit_candidate_name').value = data.candidate_name;
    document.getElementById('edit_candidate_class').value = data.candidate_class;
    document.getElementById('edit_position_id').value = data.position_id;
    new bootstrap.Modal(document.getElementById('editCandidateModal')).show();
}
</script>
<?php include 'controls.php'; ?>
</body>
</html>
