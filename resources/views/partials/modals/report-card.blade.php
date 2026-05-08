<!-- Report Card Modal -->
<div class="modal fade" id="reportCardModal" tabindex="-1" aria-labelledby="reportCardModalLabel" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background-color: #0a0a1a; border: 1px solid #00f0ff; box-shadow: 0 0 20px rgba(0, 240, 255, 0.2);">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title orbitron text-cyan" id="reportCardModalLabel"><i class="bi bi-flag-fill"></i> REPORT CARD</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="reportCardForm" action="" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="small text-muted mb-4">If you believe this card violates our terms of service or community guidelines, please report it below. Our moderation team will review it shortly.</p>
                    
                    <div class="mb-3">
                        <label class="form-label small text-info">REASON FOR REPORT</label>
                        <select name="reason" class="form-select bg-dark text-white border-info" required>
                            <option value="">Select a reason...</option>
                            <option value="Intellectual Property / Copyright">Intellectual Property / Copyright</option>
                            <option value="Inappropriate Content / NSFW">Inappropriate Content / NSFW</option>
                            <option value="Hate Speech / Harassment">Hate Speech / Harassment</option>
                            <option value="Spam / Terms Violation">Spam / Terms Violation</option>
                        </select>
                    </div>

                    <div class="mb-0">
                        <label class="form-label small text-info">ADDITIONAL NOTES (OPTIONAL)</label>
                        <textarea name="notes" class="form-control bg-dark text-white border-info" rows="3" placeholder="Provide more context..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pb-4 justify-content-center">
                    <button type="submit" class="btn btn-neon px-5">SUBMIT REPORT</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    window.openReportModal = function(cardId) {
        const form = document.getElementById('reportCardForm');
        if (form) {
            form.action = `/cards/${cardId}/report`;
            const modal = new bootstrap.Modal(document.getElementById('reportCardModal'));
            modal.show();
        }
    };
</script>
