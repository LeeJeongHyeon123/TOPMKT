import axios, { AxiosInstance, AxiosRequestConfig, AxiosResponse, AxiosError } from 'axios';

// API 기본 설정
const API_BASE_URL = process.env.REACT_APP_API_URL || 'http://localhost/topmkt/api';
const API_TIMEOUT = 30000; // 30초

// CSRF 토큰 관리
let csrfToken: string | null = null;

/**
 * CSRF 토큰 가져오기
 */
const getCsrfToken = async (): Promise<string> => {
  if (csrfToken) {
    return csrfToken;
  }

  try {
    const response = await axios.get(`${API_BASE_URL}/csrf-token`);
    csrfToken = response.data.csrf_token;
    return csrfToken;
  } catch (error) {
    console.warn('CSRF 토큰 가져오기 실패:', error);
    return '';
  }
};

/**
 * Axios 인스턴스 생성
 */
const apiClient: AxiosInstance = axios.create({
  baseURL: API_BASE_URL,
  timeout: API_TIMEOUT,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  },
  withCredentials: true, // 쿠키 전송을 위해 필요
});

/**
 * 요청 인터셉터
 */
apiClient.interceptors.request.use(
  async (config: AxiosRequestConfig) => {
    // 인증 토큰 추가
    const token = localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
    if (token) {
      config.headers = config.headers || {};
      config.headers.Authorization = `Bearer ${token}`;
    }

    // CSRF 토큰 추가 (POST, PUT, DELETE 요청에만)
    if (['post', 'put', 'patch', 'delete'].includes(config.method?.toLowerCase() || '')) {
      try {
        const csrf = await getCsrfToken();
        if (csrf) {
          config.headers = config.headers || {};
          config.headers['X-CSRF-TOKEN'] = csrf;
        }
      } catch (error) {
        console.warn('CSRF 토큰 설정 실패:', error);
      }
    }

    // 요청 로깅 (개발 모드에서만)
    if (process.env.NODE_ENV === 'development') {
      console.log(`🚀 [${config.method?.toUpperCase()}] ${config.url}`, {
        params: config.params,
        data: config.data,
        headers: config.headers,
      });
    }

    return config;
  },
  (error: AxiosError) => {
    console.error('요청 인터셉터 오류:', error);
    return Promise.reject(error);
  }
);

/**
 * 응답 인터셉터
 */
apiClient.interceptors.response.use(
  (response: AxiosResponse) => {
    // 응답 로깅 (개발 모드에서만)
    if (process.env.NODE_ENV === 'development') {
      console.log(`✅ [${response.config.method?.toUpperCase()}] ${response.config.url}`, {
        status: response.status,
        data: response.data,
      });
    }

    return response;
  },
  async (error: AxiosError) => {
    const originalRequest = error.config as AxiosRequestConfig & { _retry?: boolean };

    // 응답 오류 로깅
    console.error(`❌ [${originalRequest?.method?.toUpperCase()}] ${originalRequest?.url}`, {
      status: error.response?.status,
      data: error.response?.data,
      message: error.message,
    });

    // 401 Unauthorized 처리
    if (error.response?.status === 401 && !originalRequest._retry) {
      originalRequest._retry = true;

      try {
        // 토큰 재발급 시도
        const refreshResponse = await axios.post(`${API_BASE_URL}/auth/refresh`, {}, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token')}`,
          },
          withCredentials: true,
        });

        if (refreshResponse.data.success && refreshResponse.data.data.token) {
          const newToken = refreshResponse.data.data.token;
          
          // 새 토큰 저장
          const storage = localStorage.getItem('auth_token') ? localStorage : sessionStorage;
          storage.setItem('auth_token', newToken);

          // 원본 요청에 새 토큰 적용
          if (originalRequest.headers) {
            originalRequest.headers.Authorization = `Bearer ${newToken}`;
          }

          // 원본 요청 재시도
          return apiClient(originalRequest);
        }
      } catch (refreshError) {
        console.error('토큰 갱신 실패:', refreshError);
        
        // 토큰 갱신 실패 시 로컬 스토리지 정리
        localStorage.removeItem('auth_token');
        sessionStorage.removeItem('auth_token');
        localStorage.removeItem('user_data');
        sessionStorage.removeItem('user_data');

        // 로그인 페이지로 리다이렉트
        window.location.href = '/auth/login';
        return Promise.reject(refreshError);
      }
    }

    // 403 Forbidden 처리
    if (error.response?.status === 403) {
      console.warn('접근 권한이 없습니다.');
    }

    // 422 Validation Error 처리
    if (error.response?.status === 422) {
      console.warn('유효성 검사 오류:', error.response.data);
    }

    // 429 Too Many Requests 처리
    if (error.response?.status === 429) {
      console.warn('요청 제한 초과:', error.response.data);
    }

    // 500 Internal Server Error 처리
    if (error.response?.status === 500) {
      console.error('서버 내부 오류:', error.response.data);
    }

    // 네트워크 오류 처리
    if (!error.response) {
      console.error('네트워크 오류:', error.message);
    }

    return Promise.reject(error);
  }
);

/**
 * API 헬퍼 함수들
 */

/**
 * GET 요청 (retry 로직 포함)
 */
export const get = async <T = any>(
  url: string, 
  config?: AxiosRequestConfig,
  retries: number = 3
): Promise<AxiosResponse<T>> => {
  for (let i = 0; i < retries; i++) {
    try {
      return await apiClient.get<T>(url, config);
    } catch (error) {
      if (i === retries - 1) throw error;
      
      // 지수 백오프 적용 (1초, 2초, 4초)
      await new Promise(resolve => setTimeout(resolve, Math.pow(2, i) * 1000));
    }
  }
  throw new Error('최대 재시도 횟수 초과');
};

/**
 * POST 요청
 */
export const post = async <T = any>(
  url: string, 
  data?: any, 
  config?: AxiosRequestConfig
): Promise<AxiosResponse<T>> => {
  return apiClient.post<T>(url, data, config);
};

/**
 * PUT 요청
 */
export const put = async <T = any>(
  url: string, 
  data?: any, 
  config?: AxiosRequestConfig
): Promise<AxiosResponse<T>> => {
  return apiClient.put<T>(url, data, config);
};

/**
 * PATCH 요청
 */
export const patch = async <T = any>(
  url: string, 
  data?: any, 
  config?: AxiosRequestConfig
): Promise<AxiosResponse<T>> => {
  return apiClient.patch<T>(url, data, config);
};

/**
 * DELETE 요청
 */
export const del = async <T = any>(
  url: string, 
  config?: AxiosRequestConfig
): Promise<AxiosResponse<T>> => {
  return apiClient.delete<T>(url, config);
};

/**
 * 파일 업로드
 */
export const uploadFile = async (
  url: string,
  file: File,
  onUploadProgress?: (progressEvent: any) => void,
  additionalData?: Record<string, any>
): Promise<AxiosResponse> => {
  const formData = new FormData();
  formData.append('file', file);
  
  // 추가 데이터가 있으면 함께 전송
  if (additionalData) {
    Object.entries(additionalData).forEach(([key, value]) => {
      formData.append(key, value);
    });
  }

  return apiClient.post(url, formData, {
    headers: {
      'Content-Type': 'multipart/form-data',
    },
    onUploadProgress,
  });
};

/**
 * 이미지 업로드 (미리보기 포함)
 */
export const uploadImage = async (
  url: string,
  file: File,
  onUploadProgress?: (progressEvent: any) => void
): Promise<AxiosResponse> => {
  // 파일 타입 검증
  if (!file.type.startsWith('image/')) {
    throw new Error('이미지 파일만 업로드할 수 있습니다.');
  }

  // 파일 크기 검증 (10MB 제한)
  if (file.size > 10 * 1024 * 1024) {
    throw new Error('파일 크기는 10MB를 초과할 수 없습니다.');
  }

  return uploadFile(url, file, onUploadProgress);
};

/**
 * 다중 파일 업로드
 */
export const uploadMultipleFiles = async (
  url: string,
  files: File[],
  onUploadProgress?: (progressEvent: any) => void
): Promise<AxiosResponse> => {
  const formData = new FormData();
  
  files.forEach((file, index) => {
    formData.append(`files[${index}]`, file);
  });

  return apiClient.post(url, formData, {
    headers: {
      'Content-Type': 'multipart/form-data',
    },
    onUploadProgress,
  });
};

/**
 * 다운로드
 */
export const downloadFile = async (
  url: string,
  filename?: string,
  onDownloadProgress?: (progressEvent: any) => void
): Promise<void> => {
  const response = await apiClient.get(url, {
    responseType: 'blob',
    onDownloadProgress,
  });

  // Blob URL 생성
  const blob = new Blob([response.data]);
  const downloadUrl = window.URL.createObjectURL(blob);

  // 다운로드 링크 생성 및 클릭
  const link = document.createElement('a');
  link.href = downloadUrl;
  link.download = filename || 'download';
  document.body.appendChild(link);
  link.click();

  // 정리
  document.body.removeChild(link);
  window.URL.revokeObjectURL(downloadUrl);
};

/**
 * API 상태 확인
 */
export const checkApiHealth = async (): Promise<boolean> => {
  try {
    const response = await apiClient.get('/health');
    return response.status === 200;
  } catch (error) {
    console.error('API 상태 확인 실패:', error);
    return false;
  }
};

/**
 * 캐시 관리
 */
const cache = new Map<string, { data: any; timestamp: number; ttl: number }>();

export const getCachedData = <T = any>(key: string): T | null => {
  const cached = cache.get(key);
  if (!cached) return null;

  if (Date.now() - cached.timestamp > cached.ttl) {
    cache.delete(key);
    return null;
  }

  return cached.data;
};

export const setCachedData = <T = any>(key: string, data: T, ttl: number = 300000): void => {
  cache.set(key, {
    data,
    timestamp: Date.now(),
    ttl,
  });
};

export const clearCache = (pattern?: string): void => {
  if (pattern) {
    const regex = new RegExp(pattern);
    for (const key of cache.keys()) {
      if (regex.test(key)) {
        cache.delete(key);
      }
    }
  } else {
    cache.clear();
  }
};

/**
 * 요청 취소 관리
 */
export const createCancelToken = () => axios.CancelToken.source();

export const isRequestCancelled = (error: any): boolean => {
  return axios.isCancel(error);
};

export default apiClient;