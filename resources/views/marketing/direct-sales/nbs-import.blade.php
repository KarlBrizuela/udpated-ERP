<x-app-layout :title="$title" :sidebar="$sidebar">
    <div class="row">
        <div class="col-xl-9 col-lg-11 mx-auto">

            {{-- PAGE HEADER --}}
            <div class="d-flex align-items-center mb-4">
                <div class="me-3" style="width:42px;height:42px;background:linear-gradient(135deg,#e63946,#c1121f);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <i class="las la-file-import text-white" style="font-size:1.3rem;"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-0">NBS Purchase Order Import</h4>
                    <p class="text-muted small mb-0">Bulk-import NBS POs directly to Logistics Pick Lists</p>
                </div>
            </div>

            <div class="card border-0 shadow-sm" style="border-radius:14px;overflow:hidden;">

                {{-- ── STEP 1 : Select Company & Branch ─────────────────────────────── --}}
                <div class="card-body p-0">
                    <div class="px-4 pt-4 pb-3 border-bottom" style="background:#fafafa;">
                        <div class="d-flex align-items-center mb-1">
                            <span class="badge me-2" style="background:#e63946;font-size:.7rem;padding:4px 8px;border-radius:20px;">Step 1</span>
                            <h6 class="fw-bold mb-0">Select Company &amp; Branch</h6>
                        </div>
                        <p class="text-muted small mb-3">
                            Choose a company and its branch. The sub-branches of that branch will appear as a selectable dropdown in the <strong>NBS Branch</strong> column when you download the Excel template.
                        </p>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-uppercase text-muted" style="letter-spacing:.05em;">Company</label>
                                <select id="companySelect" class="form-select form-select-sm" style="border-radius:8px;">
                                    <option value="">— Select Company —</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->company_id }}">{{ $company->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-uppercase text-muted" style="letter-spacing:.05em;">
                                    Branch
                                    <span id="branchLoading" class="d-none ms-1">
                                        <span class="spinner-border spinner-border-sm text-danger" style="width:.7rem;height:.7rem;"></span>
                                    </span>
                                </label>
                                <select id="branchSelect" class="form-select form-select-sm" style="border-radius:8px;" disabled>
                                    <option value="">— Select Company First —</option>
                                </select>
                            </div>
                        </div>

                        {{-- Success notice after branch selected --}}
                        <div id="subBranchNotice" class="d-none mt-3 d-flex align-items-center gap-2 p-2 rounded-3" style="background:#eaf7ee;border:1px solid #b8e0c4;">
                            <i class="las la-check-circle text-success fs-5"></i>
                            <span class="small">
                                Sub-branches of <strong id="selectedBranchName"></strong> will be pre-loaded as selectable NBS Branch options in the Excel template.
                            </span>
                        </div>
                    </div>

                    {{-- ── STEP 2 : Download Template ───────────────────────────────── --}}
                    <div class="px-4 py-3 border-bottom" style="background:#fff;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <span class="badge me-2" style="background:#1d6fa4;font-size:.7rem;padding:4px 8px;border-radius:20px;">Step 2</span>
                                <div>
                                    <h6 class="fw-bold mb-0">Download Excel Template</h6>
                                    <p class="text-muted small mb-0">Template will include a branch dropdown pre-filled with your selection above.</p>
                                </div>
                            </div>
                            <a href="#" id="downloadTemplateBtn" onclick="return downloadTemplate(event)"
                               class="btn btn-sm fw-semibold d-flex align-items-center gap-1 flex-shrink-0"
                               style="background:linear-gradient(135deg,#198754,#157347);color:#fff;border-radius:8px;padding:7px 16px;white-space:nowrap;">
                                <i class="las la-file-download" style="font-size:1rem;"></i>
                                Download Template
                            </a>
                        </div>
                    </div>

                    {{-- Format info --}}
                    <div class="px-4 py-3 border-bottom" style="background:#f0f6ff;">
                        <div class="d-flex align-items-start gap-3">
                            <i class="las la-info-circle fs-4 text-primary mt-1 flex-shrink-0"></i>
                            <div class="small text-muted">
                                <strong class="text-dark">Supported import formats:</strong>
                                <ul class="mb-0 mt-1 ps-3">
                                    <li><strong>Bulk Excel (.xlsx)</strong> — Select a branch above, download the template, fill in the PO data (including <em>Discount</em>, <em>Discount Type</em>, <em>Remarks</em>, and <em>Address</em>), then upload here. The <em>NBS Branch</em> column will already have a dropdown with the sub-branches of your chosen branch. Orders go straight to <strong>Logistics Pick Lists</strong>.</li>
                                    <li><strong>Legacy NBS Export (.csv)</strong> — Standard HD/DT CSV export. Creates Sales Orders in <em>draft</em> status for marketing review.</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    {{-- ── STEP 3 : Upload & Process ────────────────────────────────── --}}
                    <div class="px-4 pt-4 pb-4" style="background:#fff;">
                        <div class="d-flex align-items-center mb-3">
                            <span class="badge me-2" style="background:#6f42c1;font-size:.7rem;padding:4px 8px;border-radius:20px;">Step 3</span>
                            <h6 class="fw-bold mb-0">Upload Completed File</h6>
                        </div>

                        <form action="{{ route('marketing.nbs-import.process') }}" method="POST" enctype="multipart/form-data" id="importForm">
                            @csrf
                            <input type="hidden" name="company_id" id="hiddenCompanyId" value="">
                            <input type="hidden" name="branch_id" id="hiddenBranchId" value="">

                            {{-- Drop zone --}}
                            <div id="dropZone"
                                 class="position-relative text-center mb-4"
                                 style="border:2px dashed #d0d5dd;border-radius:12px;padding:2.5rem 1rem;cursor:pointer;transition:all .25s;background:#fafbfc;">
                                <input type="file" name="po_file" id="poFile"
                                       class="position-absolute top-0 start-0 w-100 h-100 opacity-0"
                                       style="cursor:pointer;z-index:2;"
                                       accept=".csv,.xls,.xlsx" required>

                                <div id="uploadPlaceholder">
                                    <div class="mb-2" style="font-size:2.5rem;color:#adb5bd;">
                                        <i class="las la-cloud-upload-alt"></i>
                                    </div>
                                    <h6 class="fw-semibold mb-1">Drag &amp; drop your file here</h6>
                                    <p class="text-muted small mb-2">or click anywhere in this box to browse</p>
                                    <span class="badge bg-light text-dark border px-3 py-1" style="font-size:.75rem;">
                                        Accepted: .csv &nbsp;·&nbsp; .xls &nbsp;·&nbsp; .xlsx
                                    </span>
                                </div>

                                <div id="fileInfo" class="d-none">
                                    <div class="mb-2" style="font-size:2.5rem;color:#198754;">
                                        <i class="las la-file-excel"></i>
                                    </div>
                                    <h6 class="fw-semibold mb-0" id="selectedFileName">file.xlsx</h6>
                                    <p class="text-muted small mb-2" id="selectedFileSize">0 KB</p>
                                    <button type="button" class="btn btn-link btn-sm text-danger p-0" id="removeFile">
                                        <i class="las la-times-circle me-1"></i>Remove file
                                    </button>
                                </div>
                            </div>

                            {{-- Submit --}}
                            <div class="d-grid">
                                <button type="submit" id="importBtn" disabled
                                        class="btn py-3 fw-bold fs-6 shadow-sm"
                                        style="background:linear-gradient(135deg,#e63946,#c1121f);color:#fff;border:none;border-radius:10px;">
                                    <span id="btnText">
                                        <i class="las la-check-circle me-2"></i>Process NBS Purchase Orders
                                    </span>
                                    <span id="btnLoading" class="d-none">
                                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                                        Importing… please wait
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="card-footer border-top py-2 text-center" style="background:#f8f9fa;">
                    <span class="text-muted small">
                        <i class="las la-lock me-1"></i>Secure processing. Data will be mapped to existing book and customer records.
                    </span>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        #dropZone:hover { border-color:#e63946 !important; background:#fff5f5 !important; }
        #dropZone.drag-over { border-color:#0d6efd !important; background:rgba(13,110,253,.04) !important; }
        #companySelect:focus, #branchSelect:focus { border-color:#e63946; box-shadow:0 0 0 3px rgba(230,57,70,.15); }
        #branchSelect:disabled { background:#f3f4f6; color:#adb5bd; }
    </style>
    @endpush

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {

        /* ── element refs ── */
        const companySelect      = document.getElementById('companySelect');
        const branchSelect       = document.getElementById('branchSelect');
        const branchLoading      = document.getElementById('branchLoading');
        const subBranchNotice    = document.getElementById('subBranchNotice');
        const selectedBranchName = document.getElementById('selectedBranchName');
        const poFile             = document.getElementById('poFile');
        const dropZone           = document.getElementById('dropZone');
        const uploadPlaceholder  = document.getElementById('uploadPlaceholder');
        const fileInfo           = document.getElementById('fileInfo');
        const selectedFileName   = document.getElementById('selectedFileName');
        const selectedFileSize   = document.getElementById('selectedFileSize');
        const removeFile         = document.getElementById('removeFile');
        const importBtn          = document.getElementById('importBtn');
        const importForm         = document.getElementById('importForm');
        const btnText            = document.getElementById('btnText');
        const btnLoading         = document.getElementById('btnLoading');

        /* ── cascade: company → branch ── */
        companySelect.addEventListener('change', async function () {
            const id = this.value;
            document.getElementById('hiddenCompanyId').value = id;
            document.getElementById('hiddenBranchId').value = '';
            branchSelect.innerHTML = '<option value="">— Select Branch —</option>';
            branchSelect.disabled = true;
            subBranchNotice.classList.add('d-none');

            if (!id) { branchSelect.innerHTML = '<option value="">— Select Company First —</option>'; return; }

            branchLoading.classList.remove('d-none');
            try {
                const res = await fetch(`/marketing/companies/${id}/branches`, { headers: { Accept: 'application/json' } });
                const branches = await res.json();

                if (!branches.length) {
                    branchSelect.innerHTML = '<option value="">No branches found</option>';
                } else {
                    branches.forEach(b => {
                        const o = document.createElement('option');
                        o.value = b.company_id;
                        o.textContent = b.company_name;
                        branchSelect.appendChild(o);
                    });
                    branchSelect.disabled = false;
                }
            } catch { branchSelect.innerHTML = '<option value="">Error loading branches</option>'; }
            finally  { branchLoading.classList.add('d-none'); }
        });

        branchSelect.addEventListener('change', function () {
            document.getElementById('hiddenBranchId').value = this.value;
            if (this.value) {
                selectedBranchName.textContent = this.options[this.selectedIndex].text;
                subBranchNotice.classList.remove('d-none');
            } else {
                subBranchNotice.classList.add('d-none');
            }
        });

        /* ── file picker ── */
        function showFile(file) {
            selectedFileName.textContent = file.name;
            selectedFileSize.textContent = (file.size / 1024).toFixed(2) + ' KB';
            uploadPlaceholder.classList.add('d-none');
            fileInfo.classList.remove('d-none');
            importBtn.disabled = false;
            dropZone.style.borderColor = '#198754';
        }
        function clearFile() {
            poFile.value = '';
            uploadPlaceholder.classList.remove('d-none');
            fileInfo.classList.add('d-none');
            importBtn.disabled = true;
            dropZone.style.borderColor = '#d0d5dd';
        }

        poFile.addEventListener('change', function () { if (this.files[0]) showFile(this.files[0]); });
        removeFile.addEventListener('click', clearFile);

        dropZone.addEventListener('dragenter', e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
        dropZone.addEventListener('dragover',  e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
        dropZone.addEventListener('dragleave', e => { dropZone.classList.remove('drag-over'); });
        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.classList.remove('drag-over');
            const f = e.dataTransfer.files[0];
            if (f) { poFile.files = e.dataTransfer.files; showFile(f); }
        });

        /* ── form submit loading state ── */
        importForm.addEventListener('submit', function () {
            importBtn.disabled = true;
            btnText.classList.add('d-none');
            btnLoading.classList.remove('d-none');
            dropZone.style.pointerEvents = 'none';
            dropZone.style.opacity = '0.6';
        });
    });

    /* ── download template with branch_id ── */
    function downloadTemplate(e) {
        e.preventDefault();
        const bid = document.getElementById('branchSelect').value;
        let url   = '{{ route("marketing.nbs-import.template") }}';
        if (bid) url += '?branch_id=' + encodeURIComponent(bid);
        window.location.href = url;
        return false;
    }
    </script>
    @endpush
</x-app-layout>
