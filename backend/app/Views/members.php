<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="dashboard-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title">Library Members</h2>
        <button class="btn btn-primary d-flex align-items-center gap-2" style="background-color: var(--primary-color, #DC2626); border: none;" data-bs-toggle="modal" data-bs-target="#addMemberModal">
            <i class="fas fa-plus"></i> Add New Member
        </button>
    </div>

    <div class="dashboard-card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-secondary small text-uppercase">
                        <th style="padding: 1rem; border-bottom: 2px solid #f0f0f0; color: #6b7280; font-weight: 600;">NIM</th>
                        <th class="border-0 py-3">Name</th>
                        <th class="border-0 py-3">Email</th>
                        <th class="border-0 py-3">Status</th>
                        <th class="border-0 py-3 pe-4 text-end">Action</th>
                    </tr>
                </thead>
                <tbody id="membersTableBody">
                    <tr><td colspan="5" class="text-center py-4 text-secondary">Loading members...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add Member Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold">Add New Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addMemberForm">
                    <div class="mb-3"><label class="form-label">Member Code</label><input type="text" class="form-control" name="member_code" required></div>
                    <div class="mb-3"><label class="form-label">Name</label><input type="text" class="form-control" name="name" required></div>
                    <div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control" name="email" required></div>
                    <div class="mb-3"><label class="form-label">Phone</label><input type="text" class="form-control" name="phone"></div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary" style="background-color: var(--primary-color, #DC2626); border: none;">Save Member</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Member Modal -->
<div class="modal fade" id="editMemberModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold">Edit Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editMemberForm">
                    <input type="hidden" name="id" id="editMemberId">
                    <div class="mb-3"><label class="form-label">Member Code</label><input type="text" class="form-control" name="member_code" id="editMemberCode" required></div>
                    <div class="mb-3"><label class="form-label">Name</label><input type="text" class="form-control" name="name" id="editMemberName" required></div>
                    <div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control" name="email" id="editMemberEmail" required></div>
                    <div class="mb-3"><label class="form-label">Phone</label><input type="text" class="form-control" name="phone" id="editMemberPhone"></div>
                    <div class="mb-3"><label class="form-label">Status</label>
                        <select class="form-select" name="status" id="editMemberStatus">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary" style="background-color: var(--primary-color, #DC2626); border: none;">Update Member</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    window.openEditMember = function (member) {
        document.getElementById('editMemberId').value = member.id;
        document.getElementById('editMemberCode').value = member.member_code;
        document.getElementById('editMemberName').value = member.name;
        document.getElementById('editMemberEmail').value = member.email;
        document.getElementById('editMemberPhone').value = member.phone;
        document.getElementById('editMemberStatus').value = member.status;
        new bootstrap.Modal(document.getElementById('editMemberModal')).show();
    };

    async function loadMembers() {
        try {
            const members = await api.get('/members');
            const tbody = document.getElementById('membersTableBody');
            tbody.innerHTML = '';
            if (members.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-secondary">No members found</td></tr>';
                return;
            }
            members.forEach(m => {
                const row = `
                    <tr>
                        <td class="ps-4"><code>${m.member_code}</code></td>
                        <td class="fw-semibold text-dark">${m.name}</td>
                        <td class="text-secondary">${m.email}</td>
                        <td><span class="badge ${m.status === 'active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary'} rounded-pill px-3">${m.status}</span></td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-link text-primary p-0 me-3" onclick="openEditMember(${JSON.stringify(m).replace(/"/g, '&quot;')})">Edit</button>
                            <button class="btn btn-sm btn-link text-danger p-0" onclick="deleteMember(${m.id})">Delete</button>
                        </td>
                    </tr>`;
                tbody.insertAdjacentHTML('beforeend', row);
            });
        } catch (err) { console.error(err); }
    }
    loadMembers();

    document.getElementById('addMemberForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        try {
            await api.post('/members', Object.fromEntries(new FormData(e.target)));
            bootstrap.Modal.getInstance(document.getElementById('addMemberModal')).hide();
            e.target.reset();
            loadMembers();
        } catch (err) { alert(err.message); }
    });

    document.addEventListener('DOMContentLoaded', () => {
        const editForm = document.getElementById('editMemberForm');
        if(editForm){
            editForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const id = document.getElementById('editMemberId').value;
                try {
                    await api.put('/members/' + id, Object.fromEntries(new FormData(e.target)));
                    bootstrap.Modal.getInstance(document.getElementById('editMemberModal')).hide();
                    loadMembers();
                } catch (error) { alert(error.message); }
            });
        }
    });

    async function deleteMember(id) {
        if (confirm('Are you sure you want to delete this member?')) {
            try {
                await api.delete('/members/' + id);
                loadMembers();
            } catch (err) {
                alert('Error: ' + err.message);
            }
        }
    }
</script>
<?= $this->endSection() ?>
