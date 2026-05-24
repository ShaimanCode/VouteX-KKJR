<style>
.pyramid-gradient {
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
}
.pyramid-accent {
    color: #e94560;
}
.pyramid-gold {
    color: #ffd700;
}
.pyramid-text {
    font-family: 'Segoe UI', sans-serif;
}
.navbar-pyramid {
    background: linear-gradient(90deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
    box-shadow: 0 4px 15px rgba(233, 69, 96, 0.3);
}
.logo-pyramid {
    width: 50px;
    height: 50px;
    position: relative;
}
.logo-pyramid::before {
    content: '';
    position: absolute;
    width: 0;
    height: 0;
    border-left: 25px solid transparent;
    border-right: 25px solid transparent;
    border-bottom: 40px solid #ffd700;
    top: 5px;
}
.logo-pyramid::after {
    content: '';
    position: absolute;
    width: 0;
    height: 0;
    border-left: 15px solid transparent;
    border-right: 15px solid transparent;
    border-bottom: 25px solid #e94560;
    top: 15px;
    left: 10px;
}
/* Force normal arrow cursor site-wide to override any pointer styles */
*:not(input):not(textarea):not(select) {
    cursor: default !important;
}
</style><nav class="navbar navbar-expand-lg navbar-dark navbar-pyramid pyramid-text">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <div class="logo-pyramid me-3"></div>
            <div class="d-flex flex-column">
                <span class="fw-bold text-white" style="font-size: 0.95rem;">Sistem Pengundian Ahli Jawatankuasa Kelab Keselamatan Jalan Raya</span>
                <small class="pyramid-gold" style="font-size: 0.75rem;">VouteX KKJR</small>
            </div>
        </a>
        <?php if (isset($_SESSION['user_id'])): ?>
        <div class="d-flex align-items-center">
            <span class="text-white-50 me-3">
                <small>Selamat Datang,</small><br>
                <span class="pyramid-gold fw-bold"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
            </span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">
                <i style="font-style: normal;">Keluar</i>
            </a>
        </div>
        <?php endif; ?>
    </div>
</nav>
