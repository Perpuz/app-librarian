<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="dashboard-content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title">Manage Books</h2>
        <button class="btn btn-primary d-flex align-items-center gap-2" style="background-color: var(--primary-color, #DC2626); border: none;" data-bs-toggle="modal" data-bs-target="#addBookModal">
            <i class="fas fa-plus"></i> Add New Book
        </button>
    </div>

    <div class="row g-4" id="booksGrid">
        <div class="col-12 text-center text-secondary">Loading books...</div>
    </div>
</div>

<!-- Modals -->
<div class="modal fade" id="addBookModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold">Add New Book</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs mb-3" id="bookTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="manual-tab" data-bs-toggle="tab" data-bs-target="#manual">Manual Entry</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="openlibrary-tab" data-bs-toggle="tab" data-bs-target="#openlibrary">Search OpenLibrary</button>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="manual">
                        <form id="addBookForm">
                            <input type="hidden" name="cover_url" id="addBookCoverUrl">
                            <div class="d-flex gap-4 mb-3" id="addBookPreviewContainer" style="display: none !important;">
                                <div style="width: 100px; height: 140px; background: #f3f4f6; border-radius: 8px; overflow: hidden; flex-shrink: 0;">
                                    <img id="addBookCoverPreview" src="https://via.placeholder.com/100x140?text=Cover" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                                <div class="flex-grow-1">
                                    <div class="alert alert-info py-2 small"><i class="fas fa-info-circle me-1"></i> Review details and set stock before saving.</div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-12"><label class="form-label">Title</label><input type="text" class="form-control" name="title" id="addBookTitle" required></div>
                                <div class="col-md-6"><label class="form-label">Author</label><input type="text" class="form-control" name="author" id="addBookAuthor"></div>
                                <div class="col-md-6"><label class="form-label">ISBN</label><input type="text" class="form-control" name="isbn" id="addBookIsbn"></div>
                                <div class="col-md-6"><label class="form-label">Stock</label><input type="number" class="form-control" name="stock" id="addBookStock" value="1" min="0" required></div>
                            </div>
                            <div class="mt-4 text-end">
                                <button type="submit" class="btn btn-primary" style="background-color: var(--primary-color, #DC2626); border: none;">Save Book</button>
                            </div>
                        </form>
                    </div>
                    <div class="tab-pane fade" id="openlibrary">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="searchQuery" placeholder="Search...">
                            <button class="btn btn-primary" id="searchBtn" style="background-color: var(--primary-color, #DC2626); border: none;">Search</button>
                        </div>
                        <div id="searchResults" class="list-group gap-2"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editBookModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow" style="border-radius: 12px;">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold">Edit Book</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editBookForm">
                    <input type="hidden" name="id" id="editBookId">
                    <div class="mb-3"><label class="form-label">Title</label><input type="text" class="form-control" name="title" id="editBookTitle" required></div>
                    <div class="mb-3"><label class="form-label">Author</label><input type="text" class="form-control" name="author" id="editBookAuthor"></div>
                    <div class="mb-3"><label class="form-label">ISBN</label><input type="text" class="form-control" name="isbn" id="editBookIsbn"></div>
                    <div class="mb-3"><label class="form-label">Stock</label><input type="number" class="form-control" name="stock" id="editBookStock" min="0" required></div>
                    <div class="mb-3"><label class="form-label">Cover URL</label><input type="text" class="form-control" name="cover_url" id="editBookCover"></div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary" style="background-color: var(--primary-color, #DC2626); border: none;">Update Book</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmImportModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header border-bottom-0 py-3 px-4 bg-light">
                <h5 class="modal-title fw-bold text-dark">Import Book Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="importBookForm">
                    <input type="hidden" name="title" id="importTitle">
                    <input type="hidden" name="author" id="importAuthor">
                    <input type="hidden" name="cover_url" id="importCover">
                    <!-- ISBN removed from hidden, will be in visible input -->

                    <div class="d-flex gap-4">
                        <!-- Left: Cover Image -->
                        <div style="width: 120px; flex-shrink: 0;">
                            <div class="position-relative shadow-sm" style="border-radius: 8px; overflow: hidden; aspect-ratio: 2/3;">
                                <img id="importPreview" src="" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        </div>

                        <!-- Right: Details & Inputs -->
                        <div class="flex-grow-1">
                            <h5 class="fw-bold text-dark mb-1" id="importDisplayTitle">Title</h5>
                            <p class="text-secondary mb-3" id="importDisplayAuthor">Author</p>

                            <div class="mb-3">
                                <label class="form-label text-secondary small text-uppercase fw-bold mb-1">ISBN</label>
                                <input type="text" class="form-control" name="isbn" id="importIsbnInput" placeholder="Enter ISBN" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-secondary small text-uppercase fw-bold mb-1">Stock Quantity</label>
                                <div class="input-group">
                                    <button type="button" class="btn btn-outline-secondary" onclick="this.nextElementSibling.stepDown()" style="border-color: #dee2e6;">-</button>
                                    <input type="number" class="form-control text-center" name="stock" value="1" min="1" required style="max-width: 80px;">
                                    <button type="button" class="btn btn-outline-secondary" onclick="this.previousElementSibling.stepUp()" style="border-color: #dee2e6;">+</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 d-grid">
                        <button type="submit" class="btn btn-primary py-2 fw-semibold" style="background-color: var(--primary-color, #DC2626); border: none; border-radius: 8px;">
                            Confirm & Import Book
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    window.openEditModal = function (book) {
        document.getElementById('editBookId').value = book.id;
        document.getElementById('editBookTitle').value = book.title;
        document.getElementById('editBookAuthor').value = book.author;
        document.getElementById('editBookIsbn').value = book.isbn;
        document.getElementById('editBookStock').value = book.stock;
        document.getElementById('editBookCover').value = book.cover_url;
        new bootstrap.Modal(document.getElementById('editBookModal')).show();
    };

    document.addEventListener('DOMContentLoaded', () => {
        // Edit Form
        const editForm = document.getElementById('editBookForm');
        if(editForm) {
            editForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const id = document.getElementById('editBookId').value;
                const data = Object.fromEntries(new FormData(e.target));
                try {
                    await api.put('/books/' + id, data);
                    bootstrap.Modal.getInstance(document.getElementById('editBookModal')).hide();
                    loadBooks();
                } catch (error) { alert(error.message); }
            });
        }
    });

    async function loadBooks() {
        try {
            const books = await api.get('/books');
            const grid = document.getElementById('booksGrid');
            grid.innerHTML = '';
            if (books.length === 0) {
                grid.innerHTML = '<div class="col-12 text-center text-secondary">No books found.</div>';
                return;
            }
            books.forEach(book => {
                const card = `
                    <div class="col-md-3">
                        <div class="card h-100 border-0 shadow-sm bg-white" style="border-radius: 12px; overflow: hidden;">
                            <div style="position: relative;">
                                <img src="${book.cover_url || 'https://via.placeholder.com/150x200?text=No+Cover'}" class="card-img-top" style="height: 200px; object-fit: cover;">
                                <button class="btn btn-sm btn-light position-absolute top-0 end-0 m-2 rounded-circle" style="width: 32px; height: 32px; display:flex; align-items:center; justify-content:center;" onclick="openEditModal(${JSON.stringify(book).replace(/"/g, '&quot;')})">
                                    <i class="fas fa-edit" style="font-size:14px; color: #333;"></i>
                                </button>
                            </div>
                            <div class="card-body p-3">
                                <h6 class="card-title fw-bold text-dark text-truncate" title="${book.title}">${book.title}</h6>
                                <p class="card-text text-secondary small mb-3 text-dark">${book.author}</p>
                                <span class="badge" style="background: #F3F4F6; color: #4B5563;">Stock: ${book.stock}</span>
                            </div>
                        </div>
                    </div>`;
                grid.insertAdjacentHTML('beforeend', card);
            });
        } catch (error) { console.error(error); }
    }
    loadBooks();

    document.getElementById('addBookForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        try {
            await api.post('/books', Object.fromEntries(new FormData(e.target)));
            bootstrap.Modal.getInstance(document.getElementById('addBookModal')).hide();
            e.target.reset();
            loadBooks();
        } catch (error) { alert(error.message); }
    });

    document.getElementById('searchBtn').addEventListener('click', async () => {
        const query = document.getElementById('searchQuery').value;
        if (!query) return;
        const resDiv = document.getElementById('searchResults');
        resDiv.innerHTML = 'Searching...';
        try {
            const res = await api.get(`/openlibrary/search?q=${encodeURIComponent(query)}`);
            resDiv.innerHTML = '';
            if(res.length===0) { resDiv.innerHTML = 'No results.'; return; }
            res.forEach(b => {
                const item = document.createElement('div');
                item.className = 'list-group-item d-flex align-items-center gap-3 py-2';
                item.innerHTML = `
                    <div style="width: 45px; height: 65px; flex-shrink: 0; background-color: #f3f4f6; border-radius: 4px; overflow: hidden;">
                        <img src="${b.cover_url || 'https://via.placeholder.com/45x65?text=No+Img'}" style="width: 100%; height: 100%; object-fit: cover;" alt="Cover">
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1 text-dark fw-semibold" style="font-size: 0.95rem;">${b.title}</h6>
                        <small class="text-secondary d-block">${b.author}</small>
                    </div>
                    <button class="btn btn-sm btn-outline-primary px-3 fw-medium">Select</button>
                `;
                item.querySelector('button').onclick = () => {
                     // Set Data for Import Modal
                     document.getElementById('importTitle').value = b.title || '';
                     document.getElementById('importAuthor').value = b.author || '';
                     document.getElementById('importCover').value = b.cover_url || '';
                     
                     // Set Visible Inputs
                     document.getElementById('importIsbnInput').value = b.isbn || ''; // Editable ISBN
                     
                     document.getElementById('importDisplayTitle').textContent = b.title || 'Unknown Title';
                     document.getElementById('importDisplayAuthor').textContent = b.author || 'Unknown Author';
                     document.getElementById('importPreview').src = b.cover_url || 'https://via.placeholder.com/120x180?text=No+Cover';

                     // Transition Modals
                     const addModalEl = document.getElementById('addBookModal');
                     const importModalEl = document.getElementById('confirmImportModal');
                     
                     bootstrap.Modal.getInstance(addModalEl).hide();
                     new bootstrap.Modal(importModalEl).show();

                     // Handle Cancel/Back (Restore Search Modal)
                     const restoreSearch = () => {
                        new bootstrap.Modal(addModalEl).show();
                        importModalEl.removeEventListener('hidden.bs.modal', restoreSearch); 
                     };
                     
                     // If user manually closes Import Modal, bring back Search Modal
                     // We use { once: true } to avoid stacking listeners? No, manual handler is better.
                     // Actually, if we use hidden.bs.modal, it fires on ANY close.
                     // Let's just make the "X" and "Cancel" buttons do it explicitly if needed.
                     // But Standard Bootstrap "Close" just hides.
                     // Let's add a listener specifically for this instance.
                     importModalEl.addEventListener('hidden.bs.modal', function handler() {
                         // Only if we haven't submitted (Success handles its own flow)
                         // We can check if addModal is already open? No.
                         // Simple approach: When Import Hides, Show AddModal.
                         // But if Success hides it?
                         if(!document.getElementById('importBookForm').dataset.submitted) {
                            new bootstrap.Modal(addModalEl).show();
                         }
                         importModalEl.removeEventListener('hidden.bs.modal', handler);
                         document.getElementById('importBookForm').removeAttribute('data-submitted');
                     });
                };
                resDiv.appendChild(item);
            });
        } catch (e) { resDiv.innerHTML = 'Error'; }
    });

    // Handle Import Submit
    document.getElementById('importBookForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        // Mark as submitted so we don't re-open search modal
        e.target.dataset.submitted = 'true';
        
        try {
            await api.post('/books', Object.fromEntries(new FormData(e.target)));
            bootstrap.Modal.getInstance(document.getElementById('confirmImportModal')).hide();
            // Parent is already hidden
            e.target.reset();
            loadBooks();
            alert('Book Imported Successfully'); 
        } catch (error) { 
            alert(error.message); 
            delete e.target.dataset.submitted; // Reset flag on error
        }
    });
</script>
</script>
<?= $this->endSection() ?>
