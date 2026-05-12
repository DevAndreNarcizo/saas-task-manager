export default function BillingPage() {
  return (
    <main className="min-h-screen px-6 py-8">
      <section className="mx-auto max-w-3xl rounded-lg border border-neutral-200 bg-white p-6">
        <h1 className="text-2xl font-semibold">Billing</h1>
        <p className="mt-2 text-sm text-neutral-600">Stripe Checkout and Customer Portal are wired through the Laravel API.</p>
        <button className="mt-6 rounded-md bg-neutral-950 px-4 py-2 text-sm font-medium text-white" type="button">
          Upgrade to Pro
        </button>
      </section>
    </main>
  );
}
