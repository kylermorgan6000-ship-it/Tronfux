/**
 * Simple API client
 * - NO Sanctum
 * - NO session auth
 * - NO credentials
 * - NO rate limiting on frontend
 * - Plain JSON fetch
 */

export interface ApiResponse<T = any> {
  data?: T
  message?: string
  error?: string
  errors?: Record<string, string[]>
}

class ApiClient {
  private baseUrl = '/api'

  private async request<T>(
    method: string,
    path: string,
    data?: any
  ): Promise<T> {
    const response = await fetch(this.baseUrl + path, {
      method,
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
      },
      body:
        data && method !== 'GET'
          ? JSON.stringify(data)
          : undefined,
    })

    if (!response.ok) {
      const text = await response.text()
      throw new Error(text || `HTTP ${response.status}`)
    }

    // No content
    if (response.status === 204) {
      return null as T
    }

    return await response.json()
  }

  get<T = any>(path: string): Promise<T> {
    return this.request<T>('GET', path)
  }

  post<T = any>(path: string, data?: any): Promise<T> {
    return this.request<T>('POST', path, data)
  }

  put<T = any>(path: string, data?: any): Promise<T> {
    return this.request<T>('PUT', path, data)
  }

  patch<T = any>(path: string, data?: any): Promise<T> {
    return this.request<T>('PATCH', path, data)
  }

  delete<T = any>(path: string): Promise<T> {
    return this.request<T>('DELETE', path)
  }
}

export const api = new ApiClient()