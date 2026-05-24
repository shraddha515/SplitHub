@forelse ($transfers as $transfer)
    <div class="transfer-row">
        <div>
            <strong>{{ $transfer['from'] }}</strong>
            <span>owes {{ $transfer['to'] }}</span>
        </div>
        <strong>₹{{ number_format($transfer['amount'], 2) }}</strong>
    </div>
@empty
    <div class="empty-mini">All settled. No pending balances.</div>
@endforelse
