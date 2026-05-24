<div class="control-panel" id="controlPanel">
    <button class="control-btn" id="modeToggle" title="Mod Siang / Malam">
        <span id="modeIcon">&#9728;</span>
    </button>
    <button class="control-btn" id="zoomIn" title="Zoom Masuk">+</button>
    <div class="zoom-display" id="zoomDisplay">100%</div>
    <button class="control-btn" id="zoomOut" title="Zoom Keluar">-</button>
    <button class="control-btn" id="resetZoom" title="Set Semula Zoom">&#8634;</button>
</div>
<style>
.control-panel {
    position: fixed;
    bottom: 20px;
    right: 20px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    z-index: 9999;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
    padding: 15px;
    border-radius: 25px;
    box-shadow: 0 4px 20px rgba(233, 69, 96, 0.4), 0 0 30px rgba(255, 215, 0, 0.2);
    border: 2px solid #e94560;
}
.control-btn {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    border: 2px solid #ffd700;
    background: linear-gradient(135deg, #16213e 0%, #0f3460 100%);
    color: #ffd700;
    cursor: default;
    font-size: 22px;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}
.control-btn:hover {
    transform: scale(1.15);
    background: linear-gradient(135deg, #e94560 0%, #ff6b8a 100%);
    box-shadow: 0 0 15px #ffd700;
}
.zoom-display {
    text-align: center;
    font-weight: bold;
    font-size: 13px;
    color: #ffd700;
    padding: 5px;
    border: 1px solid #ffd700;
    border-radius: 10px;
    background: rgba(0,0,0,0.3);
}
[data-theme="dark"] {
    --control-bg: #343a40;
    --btn-bg: #e94560;
    --btn-hover: #ff6b8a;
    --btn-color: #ffd700;
}
@keyframes glow {
    0%, 100% { box-shadow: 0 0 5px #ffd700; }
    50% { box-shadow: 0 0 20px #ffd700, 0 0 30px #e94560; }
}
.control-panel:hover {
    animation: glow 2s infinite;
}
</style>
<script>
let currentZoom = 100;
let isDark = false;

document.getElementById('modeToggle').addEventListener('click', function() {
    isDark = !isDark;
    document.body.setAttribute('data-theme', isDark ? 'dark' : 'light');
    document.getElementById('modeIcon').innerHTML = isDark ? '&#9790;' : '&#9728;';
    document.body.style.backgroundColor = isDark ? '#1a1a2e' : '#f8f9fa';
    document.body.style.color = isDark ? '#f8f9fa' : '#212529';
});

document.getElementById('zoomIn').addEventListener('click', function() {
    if (currentZoom < 150) {
        currentZoom += 10;
        applyZoom();
    }
});

document.getElementById('zoomOut').addEventListener('click', function() {
    if (currentZoom > 50) {
        currentZoom -= 10;
        applyZoom();
    }
});

document.getElementById('resetZoom').addEventListener('click', function() {
    currentZoom = 100;
    applyZoom();
});

function applyZoom() {
    document.getElementById('zoomDisplay').textContent = currentZoom + '%';
    document.querySelectorAll('.content-wrapper').forEach(function(el) {
        el.style.transform = 'scale(' + (currentZoom / 100) + ')';
        el.style.transformOrigin = 'top center';
    });
}
</script>
