// Basic API configuration
const API_BASE_URL = process.env.NODE_ENV === 'production' 
  ? 'https://www.topmktx.com' 
  : 'http://localhost:3000';

const apiRequest = async (endpoint: string, options: RequestInit = {}) => {
  const url = `${API_BASE_URL}${endpoint}`;
  
  const defaultOptions: RequestInit = {
    headers: {
      ...options.headers,
    },
    ...options,
  };

  try {
    const response = await fetch(url, defaultOptions);
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    
    return await response.json();
  } catch (error) {
    console.error(`API request failed for ${endpoint}:`, error);
    throw error;
  }
};

// Create API object with HTTP methods
const api = {
  get: (endpoint: string) => apiRequest(endpoint, { method: 'GET' }),
  post: (endpoint: string, data?: any, options?: RequestInit) => {
    const body = data instanceof FormData ? data : (data ? JSON.stringify(data) : undefined);
    const baseHeaders: Record<string, string> = data instanceof FormData ? {} : { 'Content-Type': 'application/json' };
    
    return apiRequest(endpoint, { 
      method: 'POST', 
      body,
      ...options,
      headers: { ...baseHeaders, ...(options?.headers as Record<string, string> || {}) }
    });
  },
  put: (endpoint: string, data?: any, options?: RequestInit) => {
    const body = data instanceof FormData ? data : (data ? JSON.stringify(data) : undefined);
    const baseHeaders: Record<string, string> = data instanceof FormData ? {} : { 'Content-Type': 'application/json' };
    
    return apiRequest(endpoint, { 
      method: 'PUT', 
      body,
      ...options,
      headers: { ...baseHeaders, ...(options?.headers as Record<string, string> || {}) }
    });
  },
  delete: (endpoint: string) => apiRequest(endpoint, { method: 'DELETE' }),
};

export default api;