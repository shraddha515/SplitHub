<div class="modal fade" id="expenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form class="modal-content modal-soft ajax-form" method="POST" action="{{ route('groups.expenses.store', $group) }}">
            @csrf
            <div class="modal-header">
                <h2 class="modal-title h5 fw-bold">Add expense</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Title</label>
                        <input class="form-control" name="title" placeholder="Hotel booking" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Amount</label>
                        <input class="form-control split-amount" name="amount" type="number" min="0.01" step="0.01" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date</label>
                        <input class="form-control" name="expense_date" type="date" value="{{ now()->toDateString() }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category_id">
                            <option value="">Other</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Split type</label>
                        <select class="form-select split-type" name="split_type">
                            <option value="equal">Equal</option>
                            <option value="percentage">Percentage</option>
                            <option value="custom">Custom amount</option>
                        </select>
                    </div>
                </div>

                <div class="row g-4 mt-1">
                    <div class="col-lg-5">
                        <h3 class="h6 fw-bold">Who paid?</h3>
                        <div class="d-grid gap-2">
                            @foreach ($group->members as $member)
                                <label class="split-line">
                                    <span>{{ $member->name }}</span>
                                    <input type="hidden" name="payers[{{ $loop->index }}][user_id]" value="{{ $member->id }}">
                                    <input class="form-control" name="payers[{{ $loop->index }}][amount]" type="number" step="0.01" min="0" value="{{ $loop->first ? '' : 0 }}">
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <h3 class="h6 fw-bold">Who shares?</h3>
                        <div class="d-grid gap-2 participant-list">
                            @foreach ($group->members as $member)
                                <label class="split-line">
                                    <span>
                                        <input class="form-check-input me-2 participant-check" type="checkbox" checked>
                                        {{ $member->name }}
                                    </span>
                                    <input type="hidden" name="participants[{{ $loop->index }}][user_id]" value="{{ $member->id }}">
                                    <input class="form-control participant-value" name="participants[{{ $loop->index }}][value]" type="number" step="0.01" min="0" placeholder="Auto">
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control" name="notes" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark rounded-pill px-4">Save expense</button>
            </div>
        </form>
    </div>
</div>
