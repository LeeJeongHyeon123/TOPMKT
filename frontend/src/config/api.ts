// API 설정 및 Axios 클라이언트 구성
import axios, { AxiosInstance, AxiosRequestConfig, AxiosResponse } from 'axios';
import { ApiResponse } from '../types';

// API 기본 설정
export const API_CONFIG = {
  BASE_URL: process.env.REACT_APP_API_BASE_URL || 'http://localhost',
  TIMEOUT: 30000,
  RETRY_ATTEMPTS: 3,
  RETRY_DELAY: 1000,
};

// Axios 인스턴스 생성
const apiClient: AxiosInstance = axios.create({
  baseURL: API_CONFIG.BASE_URL,
  timeout: API_CONFIG.TIMEOUT,
  withCredentials: true, // JWT 쿠키 전송을 위해 필요
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest', // PHP에서 AJAX 요청 식별용
  },
});

// CSRF 토큰 관리
let csrfToken: string | null = null;

export const setCsrfToken = (token: string) => {
  csrfToken = token;
};

export const getCsrfToken = (): string | null => {
  return csrfToken;
};

// 요청 인터셉터
apiClient.interceptors.request.use(
  (config) => {
    // CSRF 토큰 추가 (POST, PUT, DELETE 요청에만)
    if (['post', 'put', 'delete', 'patch'].includes(config.method || '')) {
      if (csrfToken) {
        if (config.data instanceof FormData) {
          config.data.append('csrf_token', csrfToken);
        } else if (config.data && typeof config.data === 'object') {
          config.data.csrf_token = csrfToken;
        }
      }
    }

    // 요청 로깅 (개발 환경에서만)
    if (process.env.NODE_ENV === 'development') {
      console.log(`[API Request] ${config.method?.toUpperCase()} ${config.url}`, {
        data: config.data,
        params: config.params,
      });
    }

    return config;
  },
  (error) => {
    console.error('[API Request Error]', error);
    return Promise.reject(error);
  }
);

// 응답 인터셉터
apiClient.interceptors.response.use(
  (response: AxiosResponse) => {
    // 응답 로깅 (개발 환경에서만)
    if (process.env.NODE_ENV === 'development') {
      console.log(`[API Response] ${response.config.method?.toUpperCase()} ${response.config.url}`, {
        status: response.status,
        data: response.data,
      });
    }

    return response;
  },
  async (error) => {
    const originalRequest = error.config;

    // 401 Unauthorized - JWT 토큰 만료
    if (error.response?.status === 401 && !originalRequest._retry) {
      originalRequest._retry = true;

      try {
        // 토큰 갱신 시도
        await apiClient.post('/auth/refresh');
        
        // 원래 요청 재시도
        return apiClient(originalRequest);
      } catch (refreshError) {
        // 토큰 갱신 실패 - 로그인 페이지로 리다이렉트
        console.error('[Token Refresh Failed]', refreshError);
        
        // 브라우저 환경에서만 리다이렉트
        if (typeof window !== 'undefined') {
          window.location.href = '/auth/login';
        }
        
        return Promise.reject(refreshError);
      }
    }

    // 403 Forbidden - 권한 없음
    if (error.response?.status === 403) {
      console.error('[Access Forbidden]', error.response.data);
    }

    // 419 CSRF 토큰 만료 (Laravel/PHP 표준)
    if (error.response?.status === 419) {
      console.error('[CSRF Token Expired]', error.response.data);
      
      // CSRF 토큰 재요청
      try {
        const response = await fetch('/csrf-token');
        const data = await response.json();
        setCsrfToken(data.token);
        
        // 원래 요청 재시도
        return apiClient(originalRequest);
      } catch (csrfError) {
        console.error('[CSRF Token Refresh Failed]', csrfError);
      }
    }

    // 429 Too Many Requests - Rate Limiting
    if (error.response?.status === 429) {
      console.warn('[Rate Limit Exceeded]', error.response.data);
    }

    // 에러 로깅
    console.error('[API Response Error]', {
      status: error.response?.status,
      statusText: error.response?.statusText,
      url: error.config?.url,
      method: error.config?.method,
      data: error.response?.data,
    });

    return Promise.reject(error);
  }
);

// API 응답 타입 가드
export const isApiResponse = <T>(data: any): data is ApiResponse<T> => {
  return (
    typeof data === 'object' &&
    data !== null &&
    typeof data.success === 'boolean' &&
    typeof data.message === 'string'
  );
};

// API 에러 핸들러
export const handleApiError = (error: any): string => {
  if (error.response?.data) {
    const data = error.response.data;
    
    // 표준 API 응답 형식
    if (isApiResponse(data)) {
      return data.message || '알 수 없는 오류가 발생했습니다.';
    }
    
    // 유효성 검사 오류
    if (data.errors && typeof data.errors === 'object') {
      const errorMessages = Object.values(data.errors).filter(Boolean);
      return errorMessages.length > 0 ? errorMessages[0] as string : '입력값이 올바르지 않습니다.';
    }
    
    // 단순 문자열 메시지
    if (typeof data.message === 'string') {
      return data.message;
    }
  }

  // 네트워크 오류
  if (error.code === 'NETWORK_ERROR' || !error.response) {
    return '네트워크 연결을 확인해주세요.';
  }

  // HTTP 상태 코드별 기본 메시지
  switch (error.response?.status) {
    case 400:
      return '잘못된 요청입니다.';
    case 401:
      return '인증이 필요합니다.';
    case 403:
      return '접근 권한이 없습니다.';
    case 404:
      return '요청한 페이지를 찾을 수 없습니다.';
    case 419:
      return '보안 토큰이 만료되었습니다. 페이지를 새로고침해주세요.';
    case 422:
      return '입력값이 올바르지 않습니다.';
    case 429:
      return '요청이 너무 많습니다. 잠시 후 다시 시도해주세요.';
    case 500:
      return '서버 오류가 발생했습니다.';
    case 503:
      return '서비스를 일시적으로 사용할 수 없습니다.';
    default:
      return '알 수 없는 오류가 발생했습니다.';
  }
};

// 파일 업로드용 설정
export const createFormDataConfig = (data: FormData): AxiosRequestConfig => ({
  headers: {
    'Content-Type': 'multipart/form-data',
  },
  data,
});

// API 재시도 로직
export const retryRequest = async <T>(
  requestFn: () => Promise<AxiosResponse<T>>,
  maxAttempts: number = API_CONFIG.RETRY_ATTEMPTS,
  delay: number = API_CONFIG.RETRY_DELAY
): Promise<AxiosResponse<T>> => {
  let lastError: any;

  for (let attempt = 1; attempt <= maxAttempts; attempt++) {
    try {
      return await requestFn();
    } catch (error: any) {
      lastError = error;

      // 재시도하지 않을 조건들
      if (
        error.response?.status === 401 || // 인증 실패
        error.response?.status === 403 || // 권한 없음
        error.response?.status === 404 || // 리소스 없음
        error.response?.status === 422    // 유효성 검사 실패
      ) {
        throw error;
      }

      // 마지막 시도가 아니면 잠시 대기
      if (attempt < maxAttempts) {
        await new Promise(resolve => setTimeout(resolve, delay * attempt));
      }
    }
  }

  throw lastError;
};

// 디버깅용 API 상태 체크
export const checkApiHealth = async (): Promise<boolean> => {
  try {
    const response = await apiClient.get('/health');
    return response.status === 200;
  } catch (error) {
    console.error('[API Health Check Failed]', error);
    return false;
  }
};

export default apiClient;