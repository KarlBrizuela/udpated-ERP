<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <link href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <style>
        .memo-container {
            padding: 1.5rem;
        }

        .premium-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            border: none;
            overflow: hidden;
        }

        .premium-card-header {
            background: linear-gradient(135deg, #ff0000 0%, #b30000 100%);
            padding: 1.5rem 2rem;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .premium-card-header h4 {
            color: #fff;
            margin: 0;
            font-weight: 700;
            font-size: 1.25rem;
        }

        .premium-card-header p {
            margin: 0;
            opacity: 0.8;
            font-size: 0.9rem;
        }

        .memo-table-container {
            padding: 2rem;
        }

        #memoTable {
            width: 100% !important;
            border-collapse: separate;
            border-spacing: 0 0.5rem;
        }

        #memoTable thead th {
            background: #f8f9fa;
            border: none;
            color: #495057;
            text-transform: uppercase;
            font-size: 0.75rem;
            font-weight: 700;
            padding: 1rem;
            letter-spacing: 0.5px;
        }

        #memoTable tbody tr {
            transition: all 0.2s;
            background: #fff;
        }

        #memoTable tbody tr:hover {
            transform: scale(1.005);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            z-index: 1;
        }

        #memoTable td {
            padding: 1.25rem 1rem;
            vertical-align: middle;
            border-top: 1px solid #f1f1f1;
            border-bottom: 1px solid #f1f1f1;
            color: #333;
            font-size: 0.9rem;
        }

        #memoTable td:first-child { border-left: 1px solid #f1f1f1; border-top-left-radius: 8px; border-bottom-left-radius: 8px; }
        #memoTable td:last-child { border-right: 1px solid #f1f1f1; border-top-right-radius: 8px; border-bottom-right-radius: 8px; }

        .source-badge {
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }

        .source-journal { background-color: #e3f2fd; color: #1976d2; }
        .source-cv { background-color: #e8f5e9; color: #2e7d32; }
        .source-petty { background-color: #fff3e0; color: #ef6c00; }
        .source-advance { background-color: #f3e5f5; color: #7b1fa2; }
        .source-mis { background-color: #f1f8e9; color: #33691e; }
        .source-default { background-color: #f5f5f5; color: #616161; }

        .btn-view {
            background: #ff0000;
            color: #fff;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-view:hover {
            background: #cc0000;
            color: #fff;
            box-shadow: 0 2px 8px rgba(255, 0, 0, 0.3);
        }

        .memo-text {
            max-width: 400px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: block;
        }

        .memo-text:hover {
            white-space: normal;
            background: #fff;
            position: relative;
            z-index: 10;
        }

        /* Search input styling */
        .dataTables_filter input {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            margin-left: 0.5rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .dataTables_filter input:focus {
            border-color: #ff0000;
        }
    </style>
    @endpush

    <div class="memo-container">
        <div class="premium-card">
            <div class="premium-card-header">
                <div>
                    <h4>Memo List</h4>
                    <p>Centralized view of all memos and purposes across divisions</p>
                </div>
                <div>
                    <i class="las la-file-alt" style="font-size: 2.5rem; opacity: 0.5;"></i>
                </div>
            </div>

            <div class="memo-table-container">
                <table id="memoTable" class="display">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Source</th>
                            <th>Ref No.</th>
                            <th>Memo / Purpose</th>
                            <th>Submitted By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($memos as $memo)
                        <tr>
                            <td>
                                <span class="fw-bold">{{ \Carbon\Carbon::parse($memo['date'])->format('M. d, Y') }}</span><br>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($memo['date'])->format('h:i A') }}</small>
                            </td>
                            <td>
                                @php
                                    $sourceClass = 'source-default';
                                    if (str_contains(strtolower($memo['source']), 'journal')) $sourceClass = 'source-journal';
                                    elseif (str_contains(strtolower($memo['source']), 'cv')) $sourceClass = 'source-cv';
                                    elseif (str_contains(strtolower($memo['source']), 'petty')) $sourceClass = 'source-petty';
                                    elseif (str_contains(strtolower($memo['source']), 'advance')) $sourceClass = 'source-advance';
                                    elseif (str_contains(strtolower($memo['source']), 'request')) $sourceClass = 'source-mis';
                                @endphp
                                <span class="source-badge {{ $sourceClass }}">{{ $memo['source'] }}</span>
                            </td>
                            <td><span class="fw-bold text-dark">{{ $memo['ref_no'] }}</span></td>
                            <td>
                                <span class="memo-text" title="{{ $memo['memo'] }}">{{ $memo['memo'] }}</span>
                            </td>
                            <td>{{ $memo['submitted_by'] }}</td>
                            <td>
                                @if($memo['url'] != '#')
                                    <a href="{{ $memo['url'] }}" class="btn-view">
                                        <i class="las la-eye"></i> View
                                    </a>
                                @else
                                    <button class="btn-view" disabled style="opacity: 0.5; cursor: not-allowed;" title="No direct view available">
                                        <i class="las la-eye"></i> View
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="{{ asset('vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            var table = $('#memoTable').DataTable({
                "pageLength": 10,
                "order": [[0, "desc"]],
                "language": {
                    "search": "Filter Memos:",
                    "paginate": {
                        "next": '<i class="las la-chevron-right"></i>',
                        "previous": '<i class="las la-chevron-left"></i>'
                    }
                },
                "dom": '<"d-flex justify-content-between align-items-center mb-3"lf>rtip'
            });

            // Re-apply premium hover effect on table redraw
            table.on('draw', function() {
                $('#memoTable tbody tr').addClass('premium-row');
            });
        });
    </script>
    @endpush
</x-app-layout>
