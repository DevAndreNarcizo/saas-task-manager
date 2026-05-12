import './globals.css';
import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'SaaS Task Manager',
  description: 'Freemium task management SaaS with Stripe billing.'
};

export default function RootLayout({ children }: Readonly<{ children: React.ReactNode }>) {
  return (
    <html lang="en">
      <body>{children}</body>
    </html>
  );
}
