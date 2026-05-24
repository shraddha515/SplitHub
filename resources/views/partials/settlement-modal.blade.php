<div class="modal fade" id="settlementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content modal-soft ajax-form" method="POST" action="{{ route('groups.settlements.store', $group) }}">
            @csrf
            <div class="modal-header">
                <h2 class="modal-title h5 fw-bold">Record settlement</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body d-grid gap-3">
                <div>
                    <label class="form-label">Paid by</label>
                    <select class="form-select" name="paid_by" required>
                        @foreach ($group->members as $member)
                            <option value="{{ $member->id }}">{{ $member->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Paid to</label>
                    <select class="form-select" name="paid_to" required>
                        @foreach ($group->members as $member)
                            <option value="{{ $member->id }}">{{ $member->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Amount</label>
                    <input class="form-control" name="amount" type="number" min="0.01" step="0.01" required>
                </div>
                <div>
                    <label class="form-label">Settled at</label>
                    <input class="form-control" name="settled_at" type="datetime-local" value="{{ now()->format('Y-m-d\\TH:i') }}" required>
                </div>
                <div>
                    <label class="form-label">Notes</label>
                    <textarea class="form-control" name="notes" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark rounded-pill px-4">Record</button>
            </div>
        </form>
    </div>
</div>
