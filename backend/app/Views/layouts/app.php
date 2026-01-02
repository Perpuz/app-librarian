<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Perpuz Admin</title>

    <!-- Bootstrap 5 CSS (For Grid & Modals inside content) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- App Member Styles (Cloned) -->
    <link href="/assets/css/member-style.css" rel="stylesheet">
    <style>
        body { font-family: 'Instrument Sans', sans-serif; }
        .modal-backdrop { z-index: 1040; }
        .modal { z-index: 1050; }
        .modal-content { background-color: #ffffff !important; color: #1f2937 !important; }
        .modal-header .modal-title { color: #111827 !important; }
        
        /* Sidebar Overrides for Alignment */
        .sidebar-header { padding-left: 1.25rem !important; padding-right: 1.25rem !important; height: 70px; display: flex; align-items: center; }
        .sidebar-nav ul { padding-left: 0 !important; margin-left: 0 !important; }
        .sidebar-nav ul li { margin-left: 0 !important; padding-left: 0 !important; }
        .sidebar-nav ul li a { padding-left: 1.25rem !important; padding-right: 1.25rem !important; }
        .logo { gap: 0.5rem !important; }
    </style>
</head>
<body class="antialiased">
    <div id="app">
        <!-- Sidebar Overlay -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo" style="display: flex; align-items: center; gap: 0.75rem;">
                     <i class="fas fa-book-reader" style="color: #DC2626; font-size: 1.5rem;"></i>
                    <span style="font-weight: 700; font-size: 1.25rem; color: #111827;">Perpuz Admin</span>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <li>
                        <a href="/dashboard">
                            <i class="fas fa-home"></i>
                            <span>Overview</span>
                        </a>
                    </li>
                    <li>
                        <a href="/books">
                            <i class="fas fa-book"></i>
                            <span>Browse Books</span>
                        </a>
                    </li>
                    <li>
                        <a href="/members">
                            <i class="fas fa-users"></i>
                            <span>Members</span>
                        </a>
                    </li>
                    <li>
                        <a href="/transactions">
                            <i class="fas fa-exchange-alt"></i>
                            <span>Transactions</span>
                        </a>
                    </li>
                </ul>
            </nav>
            
            <div class="sidebar-footer">
                <a href="#" id="logout-btn" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Topbar -->
            <header class="topbar">
                <div class="topbar-left">
                    <div class="toggle-sidebar" id="toggleSidebar">
                        <i class="fas fa-bars"></i>
                    </div>
                </div>
                
                <div class="topbar-right">
                    <div class="user-menu">
                        <span class="user-name">Admin</span>
                        <div class="avatar bg-primary text-white" style="background-color: #DC2626; color: white; display:flex;align-items:center;justify-content:center;">
                            A
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="content-wrapper">
                <?= $this->renderSection('content') ?>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/api.js"></script>
    <script>
        // Set Active Link
        const path = window.location.pathname;
        document.querySelectorAll('.sidebar-nav a').forEach(link => {
            if (link.getAttribute('href') === path) {
                link.parentElement.classList.add('active');
            }
        });

        // Sidebar Toggle
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('toggleSidebar');

        if(toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
            });
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            });
        }

        // Logout
        document.getElementById('logout-btn').addEventListener('click', (e) => {
            e.preventDefault();
            localStorage.removeItem('token');
            localStorage.removeItem('user');
            window.location.href = '/';
        });
    </script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
