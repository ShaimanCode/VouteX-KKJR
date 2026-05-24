<?php require_once 'config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: login_pengguna.php');
    exit();
}

// Simpan undian
if (isset($_POST['vote']) && isset($_POST['candidate_id']) && isset($_POST['position_id'])) {
    $uid = $_SESSION['user_id'];
    $cid = intval($_POST['candidate_id']);
    $pid = intval($_POST['position_id']);

    $check = $conn->query("SELECT id FROM votes WHERE user_id = $uid AND position_id = $pid");
    if ($check->num_rows === 0) {
        $conn->query("INSERT INTO votes (user_id, candidate_id, position_id) VALUES ($uid, $cid, $pid)");
        $msg = "Undian berjaya disimpan!";
        $popup_success = true;
        $popup_user = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Nama Pengguna';
    } else {
        $error = "Anda sudah mengundi untuk jawatan ini.";
    }
}

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'vote';
$vote_search = isset($_GET['vote_search']) ? trim($_GET['vote_search']) : '';
$vote_search_escaped = $conn->real_escape_string($vote_search);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengguna - VouteX KKJR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap');
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #0f3460 0%, #16213e 50%, #1a1a2e 100%); color: #f8f9fa; }
        [data-theme="dark"] { background-color: #212529 !important; color: #f8f9fa !important; }
        [data-theme="dark"] .card { background-color: #343a40 !important; }
        .content-wrapper { transition: transform 0.3s; }
        .nav-tabs { border-bottom: 2px solid #ffd700; }
        .nav-tabs .nav-link { color: #e94560; border: none; border-radius: 8px 8px 0 0; transition: all 0.3s; }
        .nav-tabs .nav-link:hover { background: rgba(255, 215, 0, 0.2); color: #fff; }
        .nav-tabs .nav-link.active { background: linear-gradient(135deg, #ffd700 0%, #ffeb3b 100%); color: #1a1a2e; border: none; }
        .card { background: rgba(15, 52, 96, 0.95); border: 1px solid #ffd700; border-radius: 15px; box-shadow: 0 5px 20px rgba(255, 215, 0, 0.3); }
        .card-body h5 { color: #e94560; font-family: 'Orbitron', sans-serif; text-shadow: 0 0 10px rgba(233, 69, 96, 0.5); }
        .table { color: #f8f9fa; }
        .table thead th { background: linear-gradient(135deg, #ffd700 0%, #ffeb3b 100%); color: #1a1a2e; border: none; }
        .table tbody tr:hover { background: rgba(255, 215, 0, 0.15); }
        .candidate-card {
            background: linear-gradient(135deg, rgba(15, 52, 96, 0.9) 0%, rgba(22, 33, 62, 0.9) 100%);
            border: 2px solid #e94560;
            border-radius: 15px;
            padding: 20px 15px;
            text-align: center;
            transition: all 0.4s;
            position: relative;
            overflow: hidden;
        }
        .candidate-card::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 215, 0, 0.1), transparent);
            transform: rotate(45deg);
            transition: all 0.5s;
        }
        .candidate-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 15px 40px rgba(233, 69, 96, 0.5), 0 0 30px rgba(255, 215, 0, 0.3);
            border-color: #ffd700;
        }
        .candidate-card:hover::before {
            left: 100%;
        }
        .candidate-card img {
            width: 130px;
            height: 130px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 12px;
            border: 3px solid #ffd700;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            transition: all 0.3s;
        }
        .candidate-card:hover img {
            transform: scale(1.05);
            border-color: #e94560;
        }
        .candidate-number {
            font-size: 1.1rem;
            font-weight: bold;
            color: #ffd700;
            margin-bottom: 5px;
            font-family: 'Orbitron', sans-serif;
        }
        .candidate-name {
            font-size: 1rem;
            color: #fff;
            margin-bottom: 5px;
        }
        .candidate-class {
            font-size: 0.85rem;
            color: #a0a0a0;
            margin-bottom: 15px;
        }
        .btn-vote {
            background: linear-gradient(135deg, #e94560 0%, #ff6b8a 100%);
            border: none;
            color: white;
            padding: 10px 25px;
            border-radius: 25px;
            font-weight: bold;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        .btn-vote:hover {
            background: linear-gradient(135deg, #ffd700 0%, #ffeb3b 100%);
            color: #1a1a2e;
            transform: scale(1.1);
            box-shadow: 0 5px 20px rgba(255, 215, 0, 0.5);
        }
        .btn-vote:disabled {
            background: rgba(100, 100, 100, 0.5);
            color: #a0a0a0;
            cursor: not-allowed;
        }
        .btn-pyramid-primary {
            background: linear-gradient(135deg, #ffd700 0%, #ffeb3b 100%);
            border: none;
            color: #1a1a2e;
            padding: 8px 20px;
            border-radius: 6px;
            font-weight: bold;
            transition: all 0.3s;
            cursor: default;
        }
        .btn-pyramid-primary:hover {
            background: linear-gradient(135deg, #ffeb3b 0%, #ffd700 100%);
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(255, 215, 0, 0.6);
        }
        .position-section {
            margin-bottom: 50px;
            padding: 20px;
            background: rgba(15, 52, 96, 0.3);
            border-radius: 15px;
            border-left: 4px solid #ffd700;
        }
        .position-title {
            color: #ffd700;
            font-family: 'Orbitron', sans-serif;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255, 215, 0, 0.3);
        }
        .badge-voted {
            background: linear-gradient(135deg, #28a745 0%, #7fff00 100%);
            color: #fff;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        @keyframes pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(233, 69, 96, 0.5); }
            50% { box-shadow: 0 0 0 10px rgba(233, 69, 96, 0); }
        }
        .candidate-card:hover {
            animation: pulse 1.5s infinite;
        }
        .alert-success {
            background: rgba(40, 167, 69, 0.3);
            border: 1px solid #28a745;
            color: #7fff7f;
        }
        .alert-danger {
            background: rgba(220, 53, 69, 0.3);
            border: 1px solid #dc3545;
            color: #ff6b8a;
        }
        .vote-popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, 100%);
            z-index: 1055;
            max-width: 340px;
            width: calc(100% - 40px);
            background: transparent;
            border-radius: 30px;
            padding: 0;
            opacity: 0;
            animation: popupFullFlow 6.5s ease-in-out forwards;
        }
        .vote-popup-inner {
            width: 100%;
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.98), rgba(233, 69, 96, 0.95));
            border: 2px solid #fff;
            border-radius: 30px;
            color: #1a1a2e;
            padding: 24px 26px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
        }
        .vote-popup h4 {
            margin: 0 0 10px;
            font-family: 'Orbitron', sans-serif;
            font-size: 1.1rem;
            letter-spacing: 0.02em;
        }
        .vote-popup p {
            margin: 0;
            font-size: 0.95rem;
            color: #1a1a2e;
        }
        .vote-popup .popup-emoji {
            display: inline-block;
            font-size: 1.5rem;
            margin-right: 8px;
            animation: emojiBounce 1.2s ease infinite;
        }
        @keyframes popupFullFlow {
            0% { 
                transform: translate(-50%, 100%); 
                opacity: 0; 
            }
            8% { 
                transform: translate(-50%, -50%); 
                opacity: 1; 
            }
            20% { 
                transform: translate(-40%, -50%); 
                opacity: 1;
            }
            35% { 
                transform: translate(-62%, -42%); 
                opacity: 1;
            }
            50% { 
                transform: translate(-50%, -58%); 
                opacity: 1;
            }
            65% { 
                transform: translate(-58%, -46%); 
                opacity: 1;
            }
            80% { 
                transform: translate(-42%, -54%); 
                opacity: 1;
            }
            85% { 
                transform: translate(-50%, -50%); 
                opacity: 1; 
            }
            100% { 
                transform: translate(-50%, -150%); 
                opacity: 0; 
            }
        }
        @keyframes popupDrift {
            0% { transform: translate(0, 0); }
            25% { transform: translate(10px, -8px); }
            50% { transform: translate(-12px, 8px); }
            75% { transform: translate(8px, 12px); }
            100% { transform: translate(0, 0); }
        }
        @keyframes emojiBounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }
        @keyframes popupPulse {
            0%,100% { box-shadow: 0 20px 45px rgba(255, 215, 0, 0.35); }
            50% { box-shadow: 0 28px 55px rgba(255, 215, 0, 0.45); }
        }
        @media print { .control-panel, .nav-tabs, .btn { display: none !important; } }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<?php if (!empty($popup_success)): ?>
<div class="vote-popup" id="votePopup">
    <div class="vote-popup-inner">
        <h4><span class="popup-emoji">❤️‍🔥</span>Terima Kasih</h4>
        <p><?php echo htmlspecialchars($popup_user); ?>, undian anda telah direkodkan!</p>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const popup = document.getElementById('votePopup');
        if (popup) {
            popup.style.animation = popup.style.animation;
        }
    });
</script>
<?php endif; ?>
<div class="content-wrapper">
<div class="container-fluid mt-3">
    <ul class="nav nav-tabs">
        <li class="nav-item"><a class="nav-link <?php echo $tab=='vote'?'active':''; ?>" href="?tab=vote">Undi</a></li>
        <li class="nav-item"><a class="nav-link <?php echo $tab=='history'?'active':''; ?>" href="?tab=history">Sejarah Undian Peribadi</a></li>
        <li class="nav-item"><a class="nav-link <?php echo $tab=='graph'?'active':''; ?>" href="?tab=graph">Graf Undian Keseluruhan</a></li>
    </ul>

    <div class="tab-content mt-3">
        <?php if ($tab == 'vote'): ?>
        <div class="card"><div class="card-body">
            <h5>Undi Calon</h5>
            <form method="GET" class="row g-2 align-items-center mb-3">
                <input type="hidden" name="tab" value="vote">
                <div class="col-sm-8 col-md-6">
                    <input type="search" name="vote_search" class="form-control" placeholder="Cari calon mengikut nama, nombor atau kelas" value="<?php echo htmlspecialchars($vote_search); ?>">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-pyramid-primary btn-sm">Cari</button>
                </div>
            </form>
            <?php if (isset($msg)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $msg; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php
            $positions = $conn->query("SELECT * FROM positions ORDER BY id");
            while ($pos = $positions->fetch_assoc()):
                $pid = $pos['id'];
                $voted = $conn->query("SELECT id FROM votes WHERE user_id = ".$_SESSION['user_id']." AND position_id = $pid")->num_rows > 0;
            ?>
            <div class="position-section">
                <h5 class="position-title">
                    <?php echo $pos['position_name']; ?>
                    <?php if ($voted): ?>
                        <span class="badge-voted ms-2">Sudah Diundi</span>
                    <?php endif; ?>
                </h5>
                <div class="row">
                    <?php
                    $candWhere = "position_id = $pid";
                    if ($vote_search_escaped !== '') {
                        $candWhere .= " AND (candidate_name LIKE '%$vote_search_escaped%' OR candidate_number LIKE '%$vote_search_escaped%' OR candidate_class LIKE '%$vote_search_escaped%')";
                    }
                    $candidates = $conn->query("SELECT * FROM candidates WHERE $candWhere");
                    while ($cand = $candidates->fetch_assoc()):
                    ?>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="candidate-card">
                            <img src="uploads/<?php echo $cand['candidate_image']; ?>" alt="<?php echo $cand['candidate_name']; ?>">
                            <div class="candidate-number"><?php echo $cand['candidate_number']; ?></div>
                            <div class="candidate-name"><?php echo $cand['candidate_name']; ?></div>
                            <div class="candidate-class"><?php echo $cand['candidate_class']; ?></div>
                            <?php if ($voted): ?>
                                <button class="btn-vote" disabled>Sudah Diundi</button>
                            <?php else: ?>
                            <form method="POST">
                                <input type="hidden" name="candidate_id" value="<?php echo $cand['id']; ?>">
                                <input type="hidden" name="position_id" value="<?php echo $pid; ?>">
                                <button type="submit" name="vote" class="btn-vote">Undi</button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php endwhile; ?>
        </div></div>

        <?php elseif ($tab == 'history'): ?>
        <div class="card"><div class="card-body">
            <h5>Sejarah Undian Peribadi</h5>
            <table class="table table-bordered">
                <thead><tr><th>#</th><th>Calon</th><th>Jawatan</th><th>Tarikh</th></tr></thead>
                <tbody>
                    <?php
                    $r = $conn->query("SELECT v.*, c.candidate_name, c.candidate_number, p.position_name FROM votes v JOIN candidates c ON v.candidate_id=c.id JOIN positions p ON v.position_id=p.id WHERE v.user_id = ".$_SESSION['user_id']." ORDER BY v.vote_date DESC");
                    $i=1; while($row = $r->fetch_assoc()): ?>
                    <tr><td><?php echo $i++; ?></td><td><span class="text-warning fw-bold"><?php echo $row['candidate_number']; ?></span> - <?php echo $row['candidate_name']; ?></td><td><?php echo $row['position_name']; ?></td><td><?php echo $row['vote_date']; ?></td></tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php if ($r->num_rows == 0): ?>
                <p class="text-center text-muted mt-3">Tiada sejarah undian lagi.</p>
            <?php endif; ?>
        </div></div>

        <?php elseif ($tab == 'graph'): ?>
        <div class="card"><div class="card-body">
            <h5>Graf Undian Keseluruhan</h5>
            <canvas id="overallChart" width="400" height="200"></canvas>
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
                const ctx = document.getElementById('overallChart').getContext('2d');
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
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { labels: { color: '#fff', font: { size: 14 } } },
                            title: { display: true, text: 'Kedudukan Undian Mengikut Jawatan', color: '#ffd700', font: { size: 18, family: 'Orbitron' } }
                        },
                        scales: {
                            y: { beginAtZero: true, ticks: { color: '#fff' }, grid: { color: 'rgba(255,255,255,0.1)' } },
                            x: { ticks: { color: '#fff' }, grid: { color: 'rgba(255,255,255,0.1)' } }
                        }
                    }
                });
            </script>
        </div></div>
        <?php endif; ?>
    </div>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<?php include 'controls.php'; ?>
</body>
</html>
