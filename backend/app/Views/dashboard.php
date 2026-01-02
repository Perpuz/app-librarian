<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="dashboard-content">
    <!-- Welcome Card -->
    <div class="dashboard-welcome-card">
        <h1 class="dashboard-welcome-title">Welcome back, <span>Admin</span>! 👋</h1>
        <p class="dashboard-welcome-text">Explore our digital collection of books and manage your library activities.</p>
    </div>
    
    <!-- Stats Row -->
    <div class="dashboard-stats-grid">
        <div class="dashboard-stat-card">
            <div class="stat-card-content">
                <div class="stat-info">
                    <h3 class="stat-label">TOTAL BOOKS</h3>
                    <div id="stat-books" class="stat-value">-</div>
                </div>
                <div class="stat-icon stat-icon-primary">
                    <i class="fas fa-book"></i>
                </div>
            </div>
        </div>
        
        <div class="dashboard-stat-card">
            <div class="stat-card-content">
                <div class="stat-info">
                    <h3 class="stat-label">ACTIVE MEMBERS</h3>
                    <div id="stat-members" class="stat-value">-</div>
                </div>
                <div class="stat-icon stat-icon-success">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
        
        <div class="dashboard-stat-card">
            <div class="stat-card-content">
                <div class="stat-info">
                    <h3 class="stat-label">TOTAL HISTORY</h3>
                    <div id="stat-history" class="stat-value">-</div>
                </div>
                <div class="stat-icon stat-icon-danger">
                    <i class="fas fa-history"></i>
                </div>
            </div>
        </div>
        
        <div class="dashboard-stat-card">
            <div class="stat-card-content">
                <div class="stat-info">
                    <h6 class="text-uppercase text-muted mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">TOTAL DENDA</h6>
                    <div id="stat-fines" class="stat-value">-</div>
                </div>
                <div class="stat-icon stat-icon-warning" style="background: rgba(245, 158, 11, 0.1); color: #F59E0B;">
                    <i class="fas fa-coins"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Transactions -->
    <div class="dashboard-section">
        <div class="section-header">
            <h2 class="section-title">Recent Transactions</h2>
            <a href="/transactions" class="section-link">View All</a>
        </div>
        
        <div class="dashboard-card" style="overflow-x: auto;">
            <table class="w-100" style="width: 100%; text-align: left; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid #eee; color: #888; font-size: 0.85rem; text-transform: uppercase;">
                        <th style="padding: 1rem;">Book Title</th>
                        <th style="padding: 1rem;">Member</th>
                        <th style="padding: 1rem;">Due Date</th>
                        <th style="padding: 1rem;">Status</th>
                        <th style="padding: 1rem;">Action</th>
                    </tr>
                </thead>
                <tbody id="recent-transactions-list">
                        <tr><td colspan="5" style="padding: 1rem; text-align: center;">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    async function loadDashboard() {
        try {
            // Call the correct endpoint /dashboard which maps to Transactions::dashboard
            const data = await api.get('/dashboard');
            
            // Update Stats
            document.getElementById('stat-books').textContent = data.total_books ?? 0;
            document.getElementById('stat-members').textContent = data.active_members ?? 0;
            document.getElementById('stat-history').textContent = data.total_transactions ?? 0;
            document.getElementById('stat-fines').textContent = 'Rp ' + Number(data.total_fines || 0).toLocaleString('id-ID');

            // Update Recent Transactions (provided by the same endpoint)
            const recent = data.recent_transactions || [];
            const tbody = document.getElementById('recent-transactions-list');
            
            if (recent.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="padding: 1rem; text-align: center;">No recent transactions</td></tr>';
            } else {
                let html = '';
                recent.forEach(t => {
                    let statusColor = t.status === 'borrowed' ? '#F59E0B' : '#10B981';
                    let statusBadge = `<span style="background: ${statusColor}20; color: ${statusColor}; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: 500;">${t.status}</span>`;
                    
                    html += `
                        <tr style="border-bottom: 1px solid #f9fafb;">
                            <td style="padding: 1rem; font-weight: 500; color: #333;">${t.book_title || '-'}</td>
                            <td style="padding: 1rem; color: #333;">${t.member_name || '-'}</td>
                            <td style="padding: 1rem; color: #333;">${t.due_date}</td>
                            <td style="padding: 1rem;">${statusBadge}</td>
                            <td style="padding: 1rem;">
                                <a href="/transactions" style="color: #E53935; text-decoration: none; font-weight: 500; font-size: 0.9rem;">Detail</a>
                            </td>
                        </tr>
                    `;
                });
                tbody.innerHTML = html;
            }

        } catch (err) {
            console.error(err);
             document.getElementById('stat-books').textContent = 'Error';
        }
    }
    loadDashboard();
</script>
<?= $this->endSection() ?>
