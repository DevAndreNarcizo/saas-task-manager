'use client';

import { useState } from 'react';

export default function BillingPage() {
  const [loading, setLoading] = useState(false);

  const orgId = 1; // TODO: obter do contexto de autenticação

  const handleUpgrade = async () => {
    setLoading(true);
    try {
      const res = await fetch('/api/organizations/' + orgId + '/billing/checkout', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ price_id: 'pro_monthly', organization_id: orgId }),
      });
      const data = await res.json();
      if (data.url) window.location.href = data.url;
    } catch (err) {
      console.error('Upgrade failed:', err);
    } finally {
      setLoading(false);
    }
  };

  return (
    <main className="min-h-screen px-6 py-8">
      <section className="mx-auto max-w-3xl rounded-lg border border-neutral-200 bg-white p-6">
        <h1 className="text-2xl font-semibold">Billing</h1>
        <p className="mt-2 text-sm text-neutral-600">Stripe Checkout and Customer Portal are wired through the Laravel API.</p>
        <button
          className="mt-6 rounded-md bg-neutral-950 px-4 py-2 text-sm font-medium text-white disabled:opacity-50"
          type="button"
          onClick={handleUpgrade}
          disabled={loading}
        >
          {loading ? 'Processing...' : 'Upgrade to Pro'}
        </button>
      </section>
    </main>
  );
}
