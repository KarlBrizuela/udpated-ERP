<!-- Global Step 1: Rejection Reason Modal -->
<div class="modal fade" id="globalRejectionReasonModal" tabindex="-1" aria-hidden="true" style="z-index: 1080;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg text-start">
            <div class="modal-header bg-danger text-white py-3">
                <h5 class="modal-title text-white fw-bold"><i class="las la-times-circle me-2"></i>Reject Request</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label for="global_rejection_reason_input" class="form-label fw-bold text-dark mb-2">
                        Reason for Rejection / Remarks <span class="text-danger">*</span>
                    </label>
                    <textarea id="global_rejection_reason_input" class="form-control border-danger-subtle" rows="4" placeholder="Please specify why this request is being rejected..." required></textarea>
                    <div id="global_rejection_reason_error" class="text-danger small mt-1" style="display: none;">
                        <i class="las la-exclamation-circle me-1"></i>Please enter a reason for rejection before proceeding.
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light py-2">
                <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger px-4 fw-semibold" id="global_rejection_next_btn">
                    Next: Confirm Rejection <i class="las la-arrow-right ms-1"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Global Step 2: Final Confirmation Modal -->
<div class="modal fade" id="globalRejectionConfirmModal" tabindex="-1" aria-hidden="true" style="z-index: 1090;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg text-center p-3">
            <div class="modal-body text-center p-4">
                <div class="text-danger mb-3">
                    <i class="las la-exclamation-triangle display-3"></i>
                </div>
                <h4 class="fw-bold text-dark mb-2">Are you sure?</h4>
                <p class="text-muted small mb-3">Are you sure you want to reject this request with the specified reason?</p>
                <div class="p-3 bg-light rounded text-start small mb-4 border border-danger-subtle">
                    <strong class="text-danger d-block mb-1"><i class="las la-comment-alt me-1"></i>Reason for Rejection:</strong>
                    <div id="global_confirm_reason_text" class="text-dark text-break fw-medium"></div>
                </div>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-light border px-4 fw-semibold" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger px-4 fw-bold" id="global_rejection_final_confirm_btn">
                        <i class="las la-check-circle me-1"></i>Yes, Confirm Rejection
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.globalRejectionCallback = null;

    window.openTwoStepRejectionFlow = function(initialReason, onConfirmCallback) {
        window.globalRejectionCallback = onConfirmCallback;
        if (typeof jQuery !== 'undefined') {
            $('#global_rejection_reason_input').val(initialReason || '');
            $('#global_rejection_reason_error').hide();
            $('#globalRejectionReasonModal').appendTo('body').modal('show');
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        if (typeof jQuery === 'undefined') return;

        $(document).on('click', '#global_rejection_next_btn', function() {
            const reason = $('#global_rejection_reason_input').val().trim();
            if (!reason) {
                $('#global_rejection_reason_error').show();
                return;
            }
            $('#global_rejection_reason_error').hide();
            $('#globalRejectionReasonModal').modal('hide');

            $('#global_confirm_reason_text').text(reason);

            setTimeout(function() {
                $('#globalRejectionConfirmModal').appendTo('body').modal('show');
            }, 350);
        });

        $(document).on('click', '#global_rejection_final_confirm_btn', function() {
            const reason = $('#global_rejection_reason_input').val().trim();
            $('#globalRejectionConfirmModal').modal('hide');

            if (typeof window.globalRejectionCallback === 'function') {
                window.globalRejectionCallback(reason);
            }
        });
    });
</script>
