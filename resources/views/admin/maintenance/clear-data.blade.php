<x-app-layout :title="$title" :role="$role" :sidebar="$sidebar">
    <div class="container">
        <div class="card">
            <div class="card-header">Maintenance</div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <p class="text-danger"><strong>Warning:</strong> This will delete most data in the application. <em>Users</em> and <em>Book Categories</em> will be preserved.</p>

                <form method="POST" action="{{ route('admin.maintenance.clear-data.post') }}">
                    @csrf
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="confirm" name="confirm" value="1" required>
                        <label class="form-check-label" for="confirm">I understand and want to clear the database (except Users and Book Categories)</label>
                    </div>
                    <button type="submit" class="btn btn-danger">Clear Database</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
