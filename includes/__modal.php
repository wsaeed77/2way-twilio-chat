<!-- Modal -->
<div class="modal fade" id="composeModal" tabindex="-1" aria-labelledby="composeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="composeModalLabel">Compose Message</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="composeMessageForm">
                    <div class="mb-3">
                        <label for="toNumber" class="form-label">To Number:</label>
                        <select class="form-control" id="toNumber" required>
                            <!-- Options will be added here dynamically -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="messageText" class="form-label">Message:</label>
                        <textarea class="form-control" id="messageText" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-danger" form="composeMessageForm">Send Message</button>
            </div>
        </div>
    </div>
</div>
