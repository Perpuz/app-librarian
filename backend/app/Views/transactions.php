<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="dashboard-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title">Transactions</h2>
        <button class="btn btn-primary d-flex align-items-center gap-2" style="background-color: var(--primary-color, #DC2626); border: none;" data-bs-toggle="modal" data-bs-target="#addTrxModal">
            <i class="fas fa-plus"></i> Borrow Book
        </button>
    </div>

    <div class="dashboard-card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="d-flex align-items-center p-4">
            <div class="rounded-circle p-3 me-3" style="background: rgba(245, 158, 11, 0.1); color: #F59E0B;">
                <i class="fas fa-coins fa-2x"></i>
            </div>
            <div>
                <h6 class="text-uppercase text-muted mb-1" style="font-size: 0.85rem;">TOTAL DENDA TERKUMPUL</h6>
                <h3 class="mb-0 fw-bold" id="totalFinesSummary">-</h3>
            </div>
        </div>
    </div>

    <div class="dashboard-card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="w-100" style="width: 100%; text-align: left; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid #eee; color: #888; font-size: 0.85rem; text-transform: uppercase;">
                        <th style="padding: 1rem;">Book Title</th>
                        <th style="padding: 1rem;">Member</th>
                        <th style="padding: 1rem;">Borrow Date</th>
                        <th style="padding: 1rem;">Due Date</th>
                        <th style="padding: 1rem;">DENDA</th>
                        <th style="padding: 1rem;">Status</th>
                        <th style="padding: 1rem;">Action</th>
                    </tr>
                </thead>
                <tbody id="trxTableBody">
                    <tr><td colspan="6" style="padding: 1rem; text-align: center;">Loading transactions...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Transaction Modal -->
<div class="modal fade" id="addTrxModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold">Borrow Book</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addTrxForm">
                    <div class="mb-3"><label class="form-label">Member ID</label><input type="number" class="form-control" name="member_id" required placeholder="Enter Member ID"></div>
                    <div class="mb-3"><label class="form-label">Book ID</label><input type="number" class="form-control" name="book_id" required placeholder="Enter Book ID"></div>
                    <div class="mb-3"><label class="form-label">Borrow Date</label><input type="date" class="form-control" name="borrow_date" required></div>
                    <div class="mb-3"><label class="form-label">Due Date</label><input type="date" class="form-control" name="due_date" required></div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary" style="background-color: var(--primary-color, #DC2626); border: none;">Process Borrow</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    async function loadTrx() {
        try {
            const trxs = await api.get('/transactions');
            const tbody = document.getElementById('trxTableBody');
            tbody.innerHTML = '';
            if (trxs.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-secondary">No transactions found</td></tr>';
                return;
            }
            let totalFines = 0;
            trxs.forEach(t => {
                totalFines += Number(t.fine || 0);

                let statusBadge = t.status === 'borrowed' || t.status === 'overdue' 
                    ? `<span class="badge bg-warning-subtle text-warning rounded-pill px-3">${t.status}</span>`
                    : `<span class="badge bg-success-subtle text-success rounded-pill px-3">Returned</span>`;
                
                let actionBtn = (t.status === 'borrowed' || t.status === 'overdue')
                    ? `<button class="btn btn-sm btn-outline-success" onclick="returnBook(${t.id})">Return</button>`
                    : `<span class="text-muted small">Completed</span>`;

                let fineDisplay = '-';
                if (Number(t.fine) > 0) {
                     fineDisplay = `<span class="text-danger fw-bold">Rp ${Number(t.fine).toLocaleString('id-ID')}</span>`;
                } else if (t.status === 'borrowed' || t.status === 'overdue') {
                    // Check Estimations
                    const today = new Date();
                    const due = new Date(t.due_date);
                    today.setHours(0,0,0,0);
                    due.setHours(0,0,0,0);
                    
                    if (today > due) {
                        const diffTime = Math.abs(today - due);
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)); 
                        const estFine = diffDays * 5000;
                        fineDisplay = `<span class="text-danger small">Est. Denda Rp ${estFine.toLocaleString('id-ID')}</span>`;
                    }
                }

                const row = `
                    <tr style="border-bottom: 1px solid #f9fafb;">
                        <td style="padding: 1rem; font-weight: 500; color: #333;">${t.book_title || 'Unknown Title'}</td>
                        <td style="padding: 1rem; color: #333;">${t.member_name || 'Unknown Member'}</td>
                        <td style="padding: 1rem; color: #333;">${t.borrow_date}</td>
                        <td style="padding: 1rem; color: #333;">${t.due_date}</td>
                        <td style="padding: 1rem;">${fineDisplay}</td>
                        <td style="padding: 1rem;">${statusBadge}</td>
                        <td style="padding: 1rem;">${actionBtn}</td>
                    </tr>`;
                tbody.insertAdjacentHTML('beforeend', row);
            });
            
            // Update Summary
            const summaryEl = document.getElementById('totalFinesSummary');
            // Debug Alert (Remove later)
            // alert('Total Fines Calculated: ' + totalFines);
            
            if(summaryEl) {
                summaryEl.textContent = 'Rp ' + totalFines.toLocaleString('id-ID');
                summaryEl.style.color = 'black'; // Force Black Color
                console.log('Set content to:', summaryEl.textContent);
            } else {
                console.error('Summary Element NOT FOUND');
            }

        } catch (err) { console.error(err); alert('JS Error: ' + err.message); }
    }
    
    // Ensure DOM is ready before loading
    document.addEventListener('DOMContentLoaded', () => {
        loadTrx();
    });

    document.getElementById('addTrxForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        try {
            await api.post('/transactions', Object.fromEntries(new FormData(e.target)));
            bootstrap.Modal.getInstance(document.getElementById('addTrxModal')).hide();
            e.target.reset();
            loadTrx();
        } catch (err) { alert(err.message); }
    });

    async function returnBook(id) {
        if (confirm('Confirm return?')) {
            try {
                await api.put('/transactions/' + id, { status: 'returned' });
                loadTrx();
            } catch (err) { alert('Error: ' + err.message); }
        }
    }
</script>
<?= $this->endSection() ?>
