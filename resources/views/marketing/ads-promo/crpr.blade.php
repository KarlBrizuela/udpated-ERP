<x-app-layout :title="'Marketing Plan Itinerary Budget'" :role="'Marketing Manager'" :sidebar="'marketing'">

    @push('styles')
    <style>
        /* ===== CRPR Page Styles ===== */
        .crpr-header-card {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            border-radius: 16px;
            padding: 28px 32px;
            color: #fff;
            position: relative;
            overflow: hidden;
            margin-bottom: 24px;
        }
        .crpr-header-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -30%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.06) 0%, transparent 70%);
            border-radius: 50%;
        }
        .crpr-header-card::after {
            content: '';
            position: absolute;
            bottom: -40%;
            left: -10%;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(228,80,73,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }
        .crpr-header-content {
            position: relative;
            z-index: 2;
        }
        .crpr-header-title {
            font-size: 1.6rem;
            font-weight: 700;
            letter-spacing: -0.3px;
            margin-bottom: 4px;
        }
        .crpr-header-subtitle {
            font-size: 0.9rem;
            opacity: 0.7;
            font-weight: 400;
        }
        .crpr-header-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 8px;
            padding: 6px 14px;
            font-size: 0.8rem;
            font-weight: 500;
            color: rgba(255,255,255,0.9);
        }
        .crpr-header-badge .badge-label {
            opacity: 0.7;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            white-space: nowrap;
        }
        .crpr-header-badge .badge-input {
            background: transparent;
            border: none;
            border-bottom: 1px solid rgba(255,255,255,0.3);
            color: #fff;
            font-size: 0.82rem;
            font-weight: 600;
            padding: 2px 4px;
            outline: none;
            min-width: 100px;
            max-width: 220px;
            transition: border-color 0.2s ease;
        }
        .crpr-header-badge .badge-input:focus {
            border-bottom-color: #e45049;
        }
        .crpr-header-badge .badge-input::placeholder {
            color: rgba(255,255,255,0.4);
        }
        .crpr-header-badge .badge-input-wide {
            min-width: 200px;
        }
        .crpr-header-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            background: rgba(228,80,73,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            color: #e45049;
            flex-shrink: 0;
        }

        /* Filters Row */
        .crpr-filters {
            background: #fff;
            border-radius: 14px;
            padding: 20px 24px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
            border: 1px solid #e9ecef;
            margin-bottom: 24px;
            display: flex;
            align-items: flex-end;
            gap: 18px;
            flex-wrap: wrap;
        }
        .crpr-filter-group label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
            margin-bottom: 6px;
            display: block;
        }
        .crpr-filter-group select,
        .crpr-filter-group input[type="date"] {
            border-radius: 10px;
            border: 1.5px solid #dee2e6;
            padding: 8px 14px;
            font-size: 0.875rem;
            font-weight: 500;
            color: #212529;
            transition: all 0.2s ease;
            min-width: 160px;
        }
        .crpr-filter-group select:focus,
        .crpr-filter-group input[type="date"]:focus {
            border-color: #e45049;
            box-shadow: 0 0 0 3px rgba(228,80,73,0.12);
            outline: none;
        }
        .crpr-filter-group .crpr-input-label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
            font-weight: 600;
            margin-bottom: 6px;
        }

        /* Table Container */
        .crpr-table-container {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
            border: 1px solid #e9ecef;
            overflow: hidden;
        }
        .crpr-table-header {
            padding: 18px 24px;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .crpr-table-header h5 {
            font-size: 1rem;
            font-weight: 700;
            color: #212529;
            margin: 0;
        }
        .crpr-table-header .table-info {
            font-size: 0.8rem;
            color: #6c757d;
        }

        /* Custom Table */
        .crpr-table-wrap {
            overflow-x: auto;
            padding: 0;
        }
        .crpr-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 0.8rem;
        }
        .crpr-table thead th {
            background: #f8f9fb;
            color: #495057;
            font-weight: 700;
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 12px 10px;
            border-bottom: 2px solid #e9ecef;
            border-right: 1px solid #e9ecef;
            white-space: nowrap;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .crpr-table thead th:last-child {
            border-right: none;
        }
        .crpr-table thead .group-header {
            background: #1a1a2e;
            color: #fff;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            border-bottom: none;
        }
        .crpr-table thead .group-year { background: linear-gradient(135deg, #0f3460, #16213e); color: #fff; }
        .crpr-table thead .group-strategy { background: linear-gradient(135deg, #e45049, #c0392b); color: #fff; }
        .crpr-table thead .group-progress { background: linear-gradient(135deg, #27ae60, #1e8449); color: #fff; }

        .crpr-table tbody td {
            padding: 6px 6px;
            border-bottom: 1px solid #f0f2f5;
            border-right: 1px solid #f0f2f5;
            vertical-align: middle;
            text-align: center;
        }
        .crpr-table tbody td:last-child {
            border-right: none;
        }
        .crpr-table tbody tr {
            transition: background-color 0.15s ease;
        }
        .crpr-table tbody tr:hover {
            background-color: #fef9f9;
        }

        /* Location Section Headers */
        .crpr-table tbody .location-header td {
            background: linear-gradient(90deg, #fff5f5, #fff);
            font-weight: 700;
            font-size: 0.85rem;
            color: #c0392b;
            text-align: left;
            padding: 10px 14px;
            border-bottom: 2px solid #e45049;
            letter-spacing: 0.3px;
        }
        .crpr-table tbody .location-header td .loc-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 8px;
            background: #e45049;
            color: #fff;
            font-size: 0.75rem;
            margin-right: 8px;
        }

        /* Inputs inside table */
        .crpr-table .crpr-input {
            width: 100%;
            min-width: 70px;
            border: 1.5px solid #e9ecef;
            border-radius: 6px;
            padding: 5px 6px;
            font-size: 0.78rem;
            transition: all 0.2s ease;
            background: #fff;
            text-align: center;
        }
        .crpr-table .crpr-input:focus {
            border-color: #e45049;
            box-shadow: 0 0 0 2px rgba(228,80,73,0.1);
            outline: none;
        }
        .crpr-table .crpr-input-wide {
            min-width: 110px;
        }
        .crpr-table .crpr-input-remarks {
            min-width: 120px;
            text-align: left;
        }
        .crpr-table .crpr-select {
            min-width: 130px;
            border: 1.5px solid #e9ecef;
            border-radius: 6px;
            padding: 5px 6px;
            font-size: 0.78rem;
            background: #fff;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .crpr-table .crpr-select:focus {
            border-color: #e45049;
            box-shadow: 0 0 0 2px rgba(228,80,73,0.1);
            outline: none;
        }

        /* Purpose Checkboxes */
        .purpose-pills {
            display: flex;
            flex-direction: column;
            gap: 3px;
            padding: 2px 0;
        }
        .purpose-pill {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.7rem;
            color: #495057;
            white-space: nowrap;
        }
        .purpose-pill input[type="checkbox"] {
            width: 14px;
            height: 14px;
            border-radius: 3px;
            border: 1.5px solid #ced4da;
            accent-color: #e45049;
            cursor: pointer;
            flex-shrink: 0;
        }
        .purpose-pill label {
            cursor: pointer;
            margin: 0;
            font-weight: 500;
        }

        /* Progress Checkboxes */
        .progress-check {
            width: 16px;
            height: 16px;
            accent-color: #27ae60;
            cursor: pointer;
        }

        /* Action Button */
        .crpr-btn-delete {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 8px;
            border: none;
            background: #fff5f5;
            color: #e45049;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .crpr-btn-delete:hover {
            background: #e45049;
            color: #fff;
            transform: scale(1.05);
        }

        /* Add Row Buttons */
        .crpr-actions-bar {
            padding: 16px 24px;
            border-top: 1px solid #e9ecef;
            display: flex;
            gap: 12px;
            align-items: center;
        }
        .crpr-btn-add {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            border-radius: 10px;
            border: 1.5px dashed #ced4da;
            background: transparent;
            color: #495057;
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .crpr-btn-add:hover {
            border-color: #e45049;
            color: #e45049;
            background: #fef9f9;
        }
        .crpr-btn-add i {
            font-size: 1rem;
        }
        .crpr-btn-fill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            border-radius: 10px;
            border: none;
            background: linear-gradient(135deg, #e45049, #c0392b);
            color: #fff;
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 3px 10px rgba(228,80,73,0.25);
        }
        .crpr-btn-fill:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(228,80,73,0.35);
        }

        /* Client name in table */
        .client-name {
            font-weight: 500;
            color: #212529;
            font-size: 0.78rem;
            text-align: left;
            max-width: 250px;
        }

        /* Sticky first two columns */
        .crpr-table thead th:nth-child(1),
        .crpr-table tbody td:nth-child(1) {
            position: sticky;
            left: 0;
            z-index: 5;
            background: #f8f9fb;
        }
        .crpr-table thead th:nth-child(2),
        .crpr-table tbody td:nth-child(2) {
            position: sticky;
            left: 80px;
            z-index: 5;
            background: #f8f9fb;
        }
        .crpr-table tbody td:nth-child(1),
        .crpr-table tbody td:nth-child(2) {
            background: #fff;
        }
        .crpr-table tbody tr:hover td:nth-child(1),
        .crpr-table tbody tr:hover td:nth-child(2) {
            background: #fef9f9;
        }
        .crpr-table tbody .location-header td:nth-child(1) {
            background: linear-gradient(90deg, #fff5f5, #fff);
        }

        /* Stats pills */
        .crpr-stats {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }
        .crpr-stat-pill {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 10px;
            padding: 8px 14px;
        }
        .crpr-stat-pill .stat-val {
            font-size: 1.2rem;
            font-weight: 700;
        }
        .crpr-stat-pill .stat-label {
            font-size: 0.72rem;
            opacity: 0.7;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
    </style>
    @endpush

    {{-- ===== Header Card ===== --}}
    <div class="crpr-header-card">
        <div class="crpr-header-content">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="crpr-header-icon">
                        <i class="las la-route"></i>
                    </div>
                    <div>
                        <div class="crpr-header-title">Provincial Area Market Plan | Progress Report</div>
                        <div class="crpr-header-subtitle">CRPR — Client Route Plan Report</div>
                    </div>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <div class="crpr-header-badge">
                        <i class="las la-user"></i>
                        <span class="badge-label">AE:</span>
                        <input type="text" class="badge-input" id="aeInput" value="{{ auth()->user()->name ?? 'N/A' }}" placeholder="Account Executive">
                    </div>
                    <div class="crpr-header-badge">
                        <i class="las la-car"></i>
                        <span class="badge-label">Driver:</span>
                        <input type="text" class="badge-input" id="driverInput" value="" placeholder="Enter driver name">
                    </div>
                    <div class="crpr-header-badge">
                        <i class="las la-map-marker-alt"></i>
                        <span class="badge-label">Area:</span>
                        <input type="text" class="badge-input badge-input-wide" id="areaInput" value="" placeholder="e.g. Makati, South Area & Tagaytay">
                    </div>
                </div>
            </div>
            <div class="crpr-stats mt-3">
                <div class="crpr-stat-pill">
                    <div>
                        <div class="stat-val" id="statClients">0</div>
                        <div class="stat-label">Total Clients</div>
                    </div>
                </div>
                <div class="crpr-stat-pill">
                    <div>
                        <div class="stat-val" id="statLocations">6</div>
                        <div class="stat-label">Locations</div>
                    </div>
                </div>
                <div class="crpr-stat-pill">
                    <div>
                        <div class="stat-val" id="statRows">0</div>
                        <div class="stat-label">Rows</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Filters ===== --}}
    <div class="crpr-filters">
        <div class="crpr-filter-group">
            <div class="crpr-input-label"><i class="las la-calendar"></i> Year</div>
            <select id="yearSelect" class="form-select">
                @for($i = 2024; $i <= 2030; $i++)
                    <option value="{{ $i }}" {{ $i == 2026 ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </select>
        </div>
        <div class="crpr-filter-group">
            <div class="crpr-input-label"><i class="las la-calendar-alt"></i> Month</div>
            <select id="monthSelect" class="form-select">
                <option value="Jan" {{ date('n') == 1 ? 'selected' : '' }}>January</option>
                <option value="Feb" {{ date('n') == 2 ? 'selected' : '' }}>February</option>
                <option value="Mar" {{ date('n') == 3 ? 'selected' : '' }}>March</option>
                <option value="Apr" {{ date('n') == 4 ? 'selected' : '' }}>April</option>
                <option value="May" {{ date('n') == 5 ? 'selected' : '' }}>May</option>
                <option value="Jun" {{ date('n') == 6 ? 'selected' : '' }}>June</option>
                <option value="Jul" {{ date('n') == 7 ? 'selected' : '' }}>July</option>
                <option value="Aug" {{ date('n') == 8 ? 'selected' : '' }}>August</option>
                <option value="Sep" {{ date('n') == 9 ? 'selected' : '' }}>September</option>
                <option value="Oct" {{ date('n') == 10 ? 'selected' : '' }}>October</option>
                <option value="Nov" {{ date('n') == 11 ? 'selected' : '' }}>November</option>
                <option value="Dec" {{ date('n') == 12 ? 'selected' : '' }}>December</option>
            </select>
        </div>
        <div class="crpr-filter-group">
            <div class="crpr-input-label"><i class="las la-calendar-day"></i> Date</div>
            <input type="date" id="dateSelect" value="{{ date('Y-m-d') }}">
        </div>

    </div>

    {{-- ===== Table ===== --}}
    <div class="crpr-table-container">
        <div class="crpr-table-header">
            <div>
                <h5><i class="las la-table me-2" style="color: #e45049;"></i>Itinerary Data Table</h5>
                <span class="table-info">Date: <strong id="displayDate">{{ date('F d, Y') }}</strong> — Year: <strong><span id="yearDisplay">2026</span></strong></span>
            </div>
        </div>
        <div class="crpr-table-wrap">
            <table class="crpr-table" id="crprTable">
                <thead>
                    <tr>
                        <th rowspan="2" style="min-width:80px;">No.</th>
                        <th rowspan="2" style="min-width:230px;">CLIENTS</th>
                        <th colspan="3" class="group-header group-year">Year <span class="yearSub">2026</span></th>
                        <th colspan="7" class="group-header group-strategy">STRATEGY</th>
                        <th colspan="9" class="group-header group-progress">PROGRESS</th>
                        <th rowspan="2" style="min-width:50px;"></th>
                    </tr>
                    <tr>
                        {{-- Year --}}
                        <th>Net Sales<br><small class="monthYearSub">{{ date('M') }} 2026</small></th>
                        <th>A/R<br><small class="monthYearSub">{{ date('M') }} 2026</small></th>
                        <th>D/R<br><small class="monthYearSub">{{ date('M') }} 2026</small></th>
                        {{-- Strategy --}}
                        <th>Sales</th>
                        <th>Collection</th>
                        <th style="min-width:120px;">Purpose</th>
                        <th>Charge</th>
                        <th>CFC</th>
                        <th>CFC Pmt<br>Recv'd</th>
                        <th>Cash</th>
                        {{-- Progress --}}
                        <th>OC<br>Visited</th>
                        <th>In<br>Met</th>
                        <th>Left<br>Flyers</th>
                        <th>Not<br>Visited</th>
                        <th>New Client<br>w/ Sales</th>
                        <th>Consign<br>Tra/Coll</th>
                        <th>Replenish</th>
                        <th>w/<br>Compli</th>
                        <th>Masscard</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- Rows populated by JS -->
                </tbody>
            </table>
        </div>
        <div class="crpr-actions-bar">
            <button class="crpr-btn-add" onclick="addSingleRow()">
                <i class="las la-plus-circle"></i> Add Row
            </button>
            <button class="crpr-btn-add" onclick="addLocationBlock()">
                <i class="las la-layer-group"></i> Add Location Block
            </button>
        </div>
    </div>

    @push('scripts')
    <script>
    (function() {
        'use strict';

        const clientsData = {
            'LP': [
                'University of Perpetual Help School-Las Piñas',
                'St. Joseph Academy-LP'
            ],
            'Makati': [
                'BPI Makati',
                'Colegio San Agustin Makati',
                'Franciscan Development Office-Makati',
                'Guadalupe Catholic School-Makati',
                'St. Paul College Makati',
                'Sto. Nino Religious Store-Makati',
                'Sr. Myla Sanayan',
                'Society of St. Paul Inc.'
            ],
            'Muntinlupa': [
                'St. Peregrine Laziosi Parish c/o Mam Virgie Betaiczar Tel. 842-2424 CP# 0948-893-1003',
                'De La Salle Santiago Zobel School-Muntinlupa',
                'Divine Light Academy-Muntinlupa',
                'San Beda College-Alabang',
                'San Roque Catholic School-Muntinlupa',
                'St. Jerome House-Muntinlupa',
                'Our Lady of Abandoned Catholic School',
                'PAREF Woodrose School',
                'Southridge School for Boys'
            ],
            'Parañaque': [
                'Holy Infant Jesus Parish-Parañaque',
                'Manresa School-Parañaque',
                'Presentation of the Child Jesus Bookstore'
            ],
            'Pasay': [
                'Paulines Communications Center-Pasay',
                'Sta. Clara Parish School-Pasay'
            ],
            'Tagaytay': [
                'Kubo ni Maria Tagaytay',
                'Superio General Sisters of Mary',
                'St. John the Baptist-Lian'
            ]
        };

        const purposes = ['Promote', 'Collect', 'Inventory', 'Pull-out', 'Replenish'];
        let rowCounter = 0;

        function updateStats() {
            const tbody = document.getElementById('tableBody');
            const dataRows = tbody.querySelectorAll('tr:not(.location-header)');
            document.getElementById('statRows').textContent = dataRows.length;
            document.getElementById('statClients').textContent = dataRows.length;
        }

        function createPurposeCells(rowId) {
            let html = '<div class="purpose-pills">';
            purposes.forEach((p, i) => {
                html += `<div class="purpose-pill">
                    <input type="checkbox" id="p-${rowId}-${i}">
                    <label for="p-${rowId}-${i}">${p}</label>
                </div>`;
            });
            html += '</div>';
            return html;
        }

        function buildRow(location, clientName) {
            rowCounter++;
            const id = rowCounter;
            return `<tr data-row-id="${id}">
                <td><span style="font-weight:600;color:#6c757d;font-size:0.75rem;">${location}</span></td>
                <td class="client-name">${clientName}</td>
                <td><input type="number" class="crpr-input" placeholder="0.00"></td>
                <td><input type="number" class="crpr-input" placeholder="0.00"></td>
                <td><input type="number" class="crpr-input" placeholder="0.00"></td>
                <td><input type="number" class="crpr-input" placeholder="0.00"></td>
                <td><input type="number" class="crpr-input" placeholder="0.00"></td>
                <td>${createPurposeCells(id)}</td>
                <td><input type="number" class="crpr-input" placeholder="0.00"></td>
                <td><input type="number" class="crpr-input" placeholder="0.00"></td>
                <td><input type="number" class="crpr-input" placeholder="0.00"></td>
                <td><input type="number" class="crpr-input" placeholder="0.00"></td>
                <td><input type="checkbox" class="progress-check"></td>
                <td><input type="checkbox" class="progress-check"></td>
                <td><input type="checkbox" class="progress-check"></td>
                <td><input type="checkbox" class="progress-check"></td>
                <td><input type="checkbox" class="progress-check"></td>
                <td><input type="checkbox" class="progress-check"></td>
                <td><input type="checkbox" class="progress-check"></td>
                <td><input type="checkbox" class="progress-check"></td>
                <td><input type="checkbox" class="progress-check"></td>
                <td><button class="crpr-btn-delete" onclick="deleteRow(this)" title="Remove row"><i class="las la-times"></i></button></td>
            </tr>`;
        }

        function buildLocationHeader(locationName) {
            const totalCols = 22;
            return `<tr class="location-header">
                <td colspan="${totalCols}">
                    <span class="loc-icon"><i class="las la-map-pin"></i></span>${locationName} Area
                </td>
            </tr>`;
        }

        function addLocationBlock() {
            const tbody = document.getElementById('tableBody');
            // Show a prompt to pick a location
            const locOptions = Object.keys(clientsData);
            let choice = prompt('Select location:\n' + locOptions.map((l, i) => `${i+1}. ${l}`).join('\n') + '\n\nEnter the number:');
            if (!choice) return;
            const idx = parseInt(choice) - 1;
            if (idx < 0 || idx >= locOptions.length) return;
            const loc = locOptions[idx];

            let html = buildLocationHeader(loc);
            clientsData[loc].forEach(client => {
                html += buildRow(loc, client);
            });
            tbody.insertAdjacentHTML('beforeend', html);
            updateStats();
        }

        function addSingleRow() {
            const tbody = document.getElementById('tableBody');
            rowCounter++;
            const id = rowCounter;

            // Build a row with a location selector
            const locOptions = Object.keys(clientsData);
            let locSelectHtml = '<select class="crpr-select" onchange="window.crprUpdateClient(this, ' + id + ')">';
            locSelectHtml += '<option value="">Select...</option>';
            locOptions.forEach(l => { locSelectHtml += `<option value="${l}">${l}</option>`; });
            locSelectHtml += '</select>';

            let clientSelectHtml = '<select class="crpr-select" id="clientSel-' + id + '"><option value="">— select location first —</option></select>';

            const row = `<tr data-row-id="${id}">
                <td>${locSelectHtml}</td>
                <td>${clientSelectHtml}</td>
                <td><input type="number" class="crpr-input" placeholder="0.00"></td>
                <td><input type="number" class="crpr-input" placeholder="0.00"></td>
                <td><input type="number" class="crpr-input" placeholder="0.00"></td>
                <td><input type="number" class="crpr-input" placeholder="0.00"></td>
                <td><input type="number" class="crpr-input" placeholder="0.00"></td>
                <td>${createPurposeCells(id)}</td>
                <td><input type="number" class="crpr-input" placeholder="0.00"></td>
                <td><input type="number" class="crpr-input" placeholder="0.00"></td>
                <td><input type="number" class="crpr-input" placeholder="0.00"></td>
                <td><input type="number" class="crpr-input" placeholder="0.00"></td>
                <td><input type="checkbox" class="progress-check"></td>
                <td><input type="checkbox" class="progress-check"></td>
                <td><input type="checkbox" class="progress-check"></td>
                <td><input type="checkbox" class="progress-check"></td>
                <td><input type="checkbox" class="progress-check"></td>
                <td><input type="checkbox" class="progress-check"></td>
                <td><input type="checkbox" class="progress-check"></td>
                <td><input type="checkbox" class="progress-check"></td>
                <td><input type="checkbox" class="progress-check"></td>
                <td><button class="crpr-btn-delete" onclick="deleteRow(this)" title="Remove row"><i class="las la-times"></i></button></td>
            </tr>`;

            tbody.insertAdjacentHTML('beforeend', row);
            updateStats();
        }

        function populateAll() {
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = '';
            const locations = Object.keys(clientsData);
            locations.forEach(loc => {
                let html = buildLocationHeader(loc);
                clientsData[loc].forEach(client => {
                    html += buildRow(loc, client);
                });
                tbody.insertAdjacentHTML('beforeend', html);
            });
            updateStats();
        }

        // Expose to global for inline handlers
        window.deleteRow = function(btn) {
            const row = btn.closest('tr');
            row.remove();
            updateStats();
        };

        window.addSingleRow = addSingleRow;
        window.addLocationBlock = addLocationBlock;
        window.populateAll = populateAll;

        window.crprUpdateClient = function(locSelect, rowId) {
            const clientSelect = document.getElementById('clientSel-' + rowId);
            const loc = locSelect.value;
            clientSelect.innerHTML = '';
            if (loc && clientsData[loc]) {
                clientsData[loc].forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c;
                    opt.textContent = c;
                    clientSelect.appendChild(opt);
                });
            } else {
                clientSelect.innerHTML = '<option value="">— select location first —</option>';
            }
        };

        // Shared function to update month+year sub-headers
        function updateMonthYearHeaders() {
            const year = document.getElementById('yearSelect').value;
            const month = document.getElementById('monthSelect').value;
            document.getElementById('yearDisplay').textContent = year;
            document.querySelectorAll('.yearSub').forEach(el => {
                el.textContent = year;
            });
            document.querySelectorAll('.monthYearSub').forEach(el => {
                el.textContent = month + ' ' + year;
            });
        }

        // Year / Month / Date events
        document.getElementById('yearSelect').addEventListener('change', updateMonthYearHeaders);
        document.getElementById('monthSelect').addEventListener('change', updateMonthYearHeaders);

        document.getElementById('dateSelect').addEventListener('change', function() {
            const d = new Date(this.value);
            const opts = { year: 'numeric', month: 'long', day: '2-digit' };
            document.getElementById('displayDate').textContent = d.toLocaleDateString('en-US', opts);
        });

        // Auto-populate on load
        document.addEventListener('DOMContentLoaded', function() {
            populateAll();
        });
    })();
    </script>
    @endpush
</x-app-layout>
