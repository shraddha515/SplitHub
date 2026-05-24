<div class="modal fade" id="memberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content modal-soft ajax-form" method="POST" action="{{ route('groups.members.store', $group) }}">
            @csrf
            <div class="modal-header">
                <h2 class="modal-title h5 fw-bold">Add member</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-grid gap-3">
                <div>
                    <label class="form-label">Name for new guest</label>
                    <input class="form-control" name="name" placeholder="Aarav Mehta">
                </div>
                <div>
                    <label class="form-label">Email</label>
                    <input class="form-control" type="email" name="email" placeholder="friend@example.com">
                </div>
                <div>
                    <label class="form-label">Mobile</label>
                    <input class="form-control" name="mobile" placeholder="+91 98765 43210">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark rounded-pill px-4">Add member</button>
            </div>
        </form>
    </div>
</div>
