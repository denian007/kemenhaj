<footer class="main-footer">
    <div>
        &copy; <?= date('Y') ?> Kementerian Haji dan Umrah Kota Subulussalam.
    </div>
    <div>
        Panel Administrasi Terpadu v2.0
    </div>
</footer>
</div> <!-- Penutup main-content -->

<!-- SCRIPTS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    // 1. TOGGLE SIDEBAR
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const btnToggleSidebar = document.getElementById('btnToggleSidebar');

    if (btnToggleSidebar) {
        btnToggleSidebar.addEventListener('click', () => {
            if (window.innerWidth > 992) {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
                setTimeout(() => window.dispatchEvent(new Event('resize')), 350);
            } else {
                sidebar.classList.toggle('open');
                if (sidebarOverlay) sidebarOverlay.classList.toggle('active');
            }
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            sidebarOverlay.classList.remove('active');
        });
    }

    // 2. JAM & TANGGAL DIGITAL
    function updateClock() {
        const now = new Date();
        const timeEl = document.getElementById('clockTime');
        const dateEl = document.getElementById('clockDate');
        if (timeEl) {
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            timeEl.textContent = `${hours}:${minutes}:${seconds}`;
        }
        if (dateEl) {
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            };
            dateEl.textContent = now.toLocaleDateString('id-ID', options);
        }
    }
    setInterval(updateClock, 1000);
    updateClock();

    // 3. LIGHT / DARK MODE
    const btnTheme = document.getElementById('btnTheme');
    const htmlEl = document.documentElement;
    const savedTheme = localStorage.getItem('theme') || 'light';

    htmlEl.setAttribute('data-theme', savedTheme);
    updateThemeIcon(savedTheme);

    if (btnTheme) {
        btnTheme.addEventListener('click', () => {
            const currentTheme = htmlEl.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            htmlEl.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            updateThemeIcon(newTheme);
            if (typeof renderCharts === 'function') renderCharts(newTheme);
        });
    }

    function updateThemeIcon(theme) {
        if (btnTheme) btnTheme.innerHTML = theme === 'dark' ? '<i class="fa-solid fa-sun"></i>' : '<i class="fa-solid fa-moon"></i>';
    }

    // 4. FULLSCREEN
    const btnFullscreen = document.getElementById('btnFullscreen');
    if (btnFullscreen) {
        btnFullscreen.addEventListener('click', () => {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    Swal.fire('Error', `Gagal fullscreen: ${err.message}`, 'error');
                });
                btnFullscreen.innerHTML = '<i class="fa-solid fa-compress"></i>';
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                    btnFullscreen.innerHTML = '<i class="fa-solid fa-expand"></i>';
                }
            }
        });
    }

    // 5. LOGOUT (Mengarah ke ../logout)
    const btnLogout = document.getElementById('btnLogout');
    if (btnLogout) {
        btnLogout.addEventListener('click', () => {
            Swal.fire({
                title: 'Konfirmasi Logout',
                text: "Apakah Anda yakin ingin keluar dari sistem?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Keluar!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../logout';
                }
            });
        });
    }
</script>
</body>

</html>
