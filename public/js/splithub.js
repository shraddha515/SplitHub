(function () {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    function currency(amount) {
        return `₹${Number(amount || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    }

    function closeModal(form) {
        const modal = form.closest('.modal');
        if (!modal || !window.bootstrap) return;
        bootstrap.Modal.getOrCreateInstance(modal).hide();
    }

    function renderBalances(summary) {
        const list = document.getElementById('balanceList');
        const total = document.getElementById('groupTotal');
        if (total) total.textContent = currency(summary.total);
        if (!list) return;

        if (!summary.simplified || summary.simplified.length === 0) {
            list.innerHTML = '<div class="empty-mini">All settled. No pending balances.</div>';
            return;
        }

        list.innerHTML = summary.simplified.map((transfer) => `
            <div class="transfer-row">
                <div>
                    <strong>${escapeHtml(transfer.from)}</strong>
                    <span>owes ${escapeHtml(transfer.to)}</span>
                </div>
                <strong>${currency(transfer.amount)}</strong>
            </div>
        `).join('');
    }

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, (char) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;',
        }[char]));
    }

    async function refreshGroupBalances() {
        const shell = document.querySelector('[data-balance-url]');
        if (!shell) return;
        const response = await fetch(shell.dataset.balanceUrl, {
            headers: { Accept: 'application/json' },
        });
        if (response.ok) renderBalances(await response.json());
    }

    document.querySelectorAll('.ajax-form').forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            form.classList.add('ajax-loading');

            try {
                const response = await fetch(form.action, {
                    method: form.method || 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrf,
                    },
                    body: new FormData(form),
                });

                const payload = await response.json().catch(() => ({}));
                if (!response.ok) {
                    alert(payload.message || 'Please check the form and try again.');
                    return;
                }

                closeModal(form);
                form.reset();
                await refreshGroupBalances();

                if (form.action.includes('/members')) {
                    window.location.reload();
                }
            } catch (error) {
                alert('Network error. Please try again.');
            } finally {
                form.classList.remove('ajax-loading');
            }
        });
    });

    document.querySelectorAll('.split-type, .split-amount').forEach((input) => {
        input.addEventListener('input', updateParticipantValues);
        input.addEventListener('change', updateParticipantValues);
    });

    document.querySelectorAll('.participant-check').forEach((checkbox) => {
        checkbox.addEventListener('change', updateParticipantValues);
    });

    function updateParticipantValues() {
        const modal = document.getElementById('expenseModal');
        if (!modal) return;

        const type = modal.querySelector('.split-type')?.value || 'equal';
        const amount = Number(modal.querySelector('.split-amount')?.value || 0);
        const rows = [...modal.querySelectorAll('.participant-list .split-line')];
        const activeRows = rows.filter((row) => row.querySelector('.participant-check')?.checked);

        rows.forEach((row) => {
            const value = row.querySelector('.participant-value');
            const hidden = row.querySelector('input[type="hidden"]');
            const active = row.querySelector('.participant-check')?.checked;
            value.disabled = !active || type === 'equal';
            hidden.disabled = !active;
            if (!active) value.value = '';
        });

        if (type === 'equal' && activeRows.length > 0 && amount > 0) {
            const share = amount / activeRows.length;
            activeRows.forEach((row) => {
                const input = row.querySelector('.participant-value');
                input.value = share.toFixed(2);
                input.placeholder = share.toFixed(2);
            });
        }
    }

    const chart = document.getElementById('monthlyChart');
    if (chart && chart.dataset.reportUrl && window.Chart) {
        fetch(chart.dataset.reportUrl, { headers: { Accept: 'application/json' } })
            .then((response) => response.json())
            .then((rows) => {
                const grouped = rows.reduce((carry, row) => {
                    carry[row.month] = (carry[row.month] || 0) + Number(row.total);
                    return carry;
                }, {});

                new Chart(chart, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(grouped),
                        datasets: [{
                            label: 'Monthly expense',
                            data: Object.values(grouped),
                            backgroundColor: ['#2563eb', '#0f766e', '#f59e0b', '#e11d48'],
                            borderRadius: 8,
                        }],
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: '#e2e8f0' } },
                            x: { grid: { display: false } },
                        },
                    },
                });
            });
    }

    updateParticipantValues();
})();
