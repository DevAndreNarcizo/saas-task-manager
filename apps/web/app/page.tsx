import { CreditCard, FolderKanban, ShieldCheck } from 'lucide-react';

const limits = [
  { label: 'Free', value: '3 projects / 10 tasks' },
  { label: 'Pro', value: 'Unlimited projects and tasks' }
];

export default function DashboardPage() {
  return (
    <main className="min-h-screen px-6 py-8">
      <section className="mx-auto max-w-6xl">
        <div className="mb-8 flex items-center justify-between">
          <div>
            <h1 className="text-3xl font-semibold tracking-normal">SaaS Task Manager</h1>
            <p className="mt-2 text-sm text-neutral-600">Projects, tasks, roles and subscription limits in one workspace.</p>
          </div>
          <a className="rounded-md bg-neutral-950 px-4 py-2 text-sm font-medium text-white" href="/billing">Billing</a>
        </div>

        <div className="grid gap-4 md:grid-cols-3">
          <article className="rounded-lg border border-neutral-200 bg-white p-5">
            <FolderKanban className="mb-4 h-5 w-5" />
            <h2 className="text-base font-semibold">Projects</h2>
            <p className="mt-2 text-sm text-neutral-600">Track delivery across teams with clear ownership.</p>
          </article>
          <article className="rounded-lg border border-neutral-200 bg-white p-5">
            <ShieldCheck className="mb-4 h-5 w-5" />
            <h2 className="text-base font-semibold">RBAC</h2>
            <p className="mt-2 text-sm text-neutral-600">Admin, member and viewer permissions enforced by the API.</p>
          </article>
          <article className="rounded-lg border border-neutral-200 bg-white p-5">
            <CreditCard className="mb-4 h-5 w-5" />
            <h2 className="text-base font-semibold">Stripe</h2>
            <p className="mt-2 text-sm text-neutral-600">Checkout and webhooks keep subscription state synchronized.</p>
          </article>
        </div>

        <div className="mt-8 rounded-lg border border-neutral-200 bg-white">
          {limits.map((item) => (
            <div className="flex items-center justify-between border-b border-neutral-100 px-5 py-4 last:border-0" key={item.label}>
              <span className="font-medium">{item.label}</span>
              <span className="text-sm text-neutral-600">{item.value}</span>
            </div>
          ))}
        </div>
      </section>
    </main>
  );
}
