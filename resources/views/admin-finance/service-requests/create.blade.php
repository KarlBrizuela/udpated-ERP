<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    @push('styles')
    <style>
        .request-form {
            background: #fff;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }

        .form-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e0e0e0;
        }

        .form-header .company-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .form-header .company-logo {
            width: 60px;
            height: 60px;
            background: #ff0000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 2rem;
            font-weight: bold;
        }

        .document-title {
            text-align: center;
            font-size: 1.75rem;
            font-weight: 700;
            color: #333;
            margin-top: 1rem;
            text-transform: uppercase;
        }

        .section-box {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #333;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
        }

        .form-actions {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }

        .btn-submit {
            background: #28a745;
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-submit:hover {
            background: #218838;
        }

        .btn-cancel {
            background: #6c757d;
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
        }

        .btn-cancel:hover {
            background: #5a6268;
        }

        .department-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .department-option {
            position: relative;
        }

        .department-option input[type="radio"] {
            position: absolute;
            opacity: 0;
        }

        .department-option label {
            display: block;
            padding: 1rem;
            border: 2px solid #ddd;
            border-radius: 4px;
            text-align: center;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
            margin: 0;
        }

        .department-option input[type="radio"]:checked + label {
            background: #ff0000;
            color: white;
            border-color: #ff0000;
        }
    </style>
    @endpush

    <div class="row">
        <div class="col-xl-10 offset-xl-1">
            <div class="card request-form">
                <!-- Form Header -->
                <div class="form-header">
                    <div class="company-info">
                        <div class="company-logo">C</div>
                        <div>
                            <div class="company-name fw-bold">CLARETIAN COMMUNICATIONS FOUNDATION INC.</div>
                            <div class="text-muted small">8 Mayumi St., UP Village, Diliman, Quezon City</div>
                        </div>
                    </div>
                    <div class="document-title">Create Service Request</div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Errors:</strong>
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin-finance.service-requests.store') }}">
                    @csrf

                    <!-- Select Department -->
                    <div class="section-box">
                        <div class="section-title">Select Department</div>
                        <div class="department-grid">
                            <div class="department-option">
                                <input type="radio" id="dept_gsd" name="department" value="GSD" required {{ old('department') === 'GSD' ? 'checked' : '' }}>
                                <label for="dept_gsd">
                                    <i class="las la-cube me-2"></i>GSD
                                </label>
                            </div>
                            <div class="department-option">
                                <input type="radio" id="dept_mis" name="department" value="MIS" required {{ old('department') === 'MIS' ? 'checked' : '' }}>
                                <label for="dept_mis">
                                    <i class="las la-laptop me-2"></i>MIS
                                </label>
                            </div>
                            <div class="department-option">
                                <input type="radio" id="dept_hr" name="department" value="HR" required {{ old('department') === 'HR' ? 'checked' : '' }}>
                                <label for="dept_hr">
                                    <i class="las la-users me-2"></i>HR
                                </label>
                            </div>
                            <div class="department-option">
                                <input type="radio" id="dept_dto" name="department" value="DTO" required {{ old('department') === 'DTO' ? 'checked' : '' }}>
                                <label for="dept_dto">
                                    <i class="las la-briefcase me-2"></i>DTO
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Service Request Details -->
                    <div class="section-box">
                        <div class="section-title">Service Request Details</div>

                        <div class="mb-3">
                            <label for="requestor_name" class="form-label fw-bold">Requestor's Name <span class="text-danger">*</span></label>
                            <input 
                                type="text" 
                                class="form-control @error('requestor_name') is-invalid @enderror" 
                                id="requestor_name" 
                                name="requestor_name" 
                                value="{{ old('requestor_name') ?? auth()->user()->name }}"
                                required
                            >
                            @error('requestor_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="date" class="form-label fw-bold">Date <span class="text-danger">*</span></label>
                            <input 
                                type="date" 
                                class="form-control @error('date') is-invalid @enderror" 
                                id="date" 
                                name="date" 
                                value="{{ old('date') ?? date('Y-m-d') }}"
                                required
                            >
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="nature_of_request" class="form-label fw-bold">Nature of Request <span class="text-danger">*</span></label>
                            <textarea 
                                class="form-control @error('nature_of_request') is-invalid @enderror" 
                                id="nature_of_request" 
                                name="nature_of_request" 
                                rows="5"
                                placeholder="Describe the nature of your service request..."
                                required
                            >{{ old('nature_of_request') }}</textarea>
                            @error('nature_of_request')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <a href="{{ route('admin-finance.mis.job-orders') }}" class="btn btn-cancel">
                            <i class="las la-arrow-left me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-submit">
                            <i class="las la-paper-plane me-2"></i>Submit Service Request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
