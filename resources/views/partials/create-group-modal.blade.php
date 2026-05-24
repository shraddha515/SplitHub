<div class="modal fade" id="createGroupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content modal-soft" method="POST" action="{{ route('groups.store') }}">
            @csrf
            <div class="modal-header">
                <h2 class="modal-title h5 fw-bold">Create group</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-grid gap-3">
                <div>
                    <label class="form-label">Group name</label>
                    <input class="form-control" name="name" placeholder="Trip to Goa" required>
                </div>
                <div>
                    <label class="form-label">Type</label>
                    <select class="form-select" name="type" required>
                        <option value="trip">Trip</option>
                        <option value="flatmates">Flatmates</option>
                        <option value="event">Event</option>
                        <option value="team">Team</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Currency</label>
                    <select class="form-select" name="currency" required>
                        <option value="INR">INR - ₹</option>
                        <option value="USD">USD - $</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark rounded-pill px-4">Create</button>
            </div>
        </form>
    </div>
</div>
