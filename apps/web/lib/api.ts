export interface ApiEnvelope<T> {
  success: boolean;
  data: T | null;
  error: { code: string; message: string } | null;
  meta: Record<string, unknown>;
}

export async function apiGet<T>(path: string): Promise<ApiEnvelope<T>> {
  const baseUrl = process.env.NEXT_PUBLIC_API_URL ?? 'http://localhost:8000/api/v1';
  const response = await fetch(`${baseUrl}${path}`, { next: { revalidate: 30 } });

  if (!response.ok) {
    throw new Error(`API request failed with status ${response.status}`);
  }

  return response.json() as Promise<ApiEnvelope<T>>;
}
