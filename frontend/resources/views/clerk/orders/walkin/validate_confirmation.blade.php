@extends('layouts.clerk_app')

@section('content')
<div class="container-fluid">
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <div class="mb-3 fw-semibold" id="confirmModalLabel">Are you sure you want to proceed ?</div>
                    <div class="d-flex justify-content-center gap-3">
                        <form method="POST" action="{{ route('clerk.orders.walkin.done', $order) }}" class="m-0">
                            @csrf
                            <button type="submit" class="btn btn-success">Confirm</button>
                        </form>
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modal = new bootstrap.Modal(document.getElementById('confirmModal'));
        modal.show();
    });
</script>
@endsection