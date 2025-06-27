import apiClient from '../config/api';
import { User, LoginRequest, SignupRequest, ApiResponse } from '../types';

interface AuthResponse {
  token: string;
  user: User;
  expires_in?: number;
}


interface PasswordResetRequest {
  phone: string;
  code: string;
  password: string;
  password_confirmation: string;
}

class AuthService {
  /**
   * 로그인
   */
  async login(phone: string, password: string, remember: boolean = false): Promise<AuthResponse> {
    try {
      const response = await apiClient.post<ApiResponse<AuthResponse>>('/auth/login', {
        phone,
        password,
        remember
      });

      if (response.data.success && response.data.data) {
        const { token, user } = response.data.data;
        
        // 토큰을 로컬 스토리지에 저장
        const storage = remember ? localStorage : sessionStorage;
        storage.setItem('auth_token', token);
        
        // 사용자 정보도 저장
        storage.setItem('user_data', JSON.stringify(user));
        
        return response.data.data;
      } else {
        throw new Error(response.data.message || '로그인에 실패했습니다.');
      }
    } catch (error: any) {
      if (error.response?.data?.message) {
        throw new Error(error.response.data.message);
      }
      throw new Error('로그인 중 오류가 발생했습니다.');
    }
  }

  /**
   * 회원가입
   */
  async signup(signupData: SignupRequest): Promise<AuthResponse> {
    try {
      const response = await apiClient.post<ApiResponse<AuthResponse>>('/auth/signup', signupData);

      if (response.data.success && response.data.data) {
        const { token, user } = response.data.data;
        
        // 토큰을 세션 스토리지에 저장 (회원가입 후 자동 로그인)
        sessionStorage.setItem('auth_token', token);
        sessionStorage.setItem('user_data', JSON.stringify(user));
        
        return response.data.data;
      } else {
        throw new Error(response.data.message || '회원가입에 실패했습니다.');
      }
    } catch (error: any) {
      if (error.response?.data?.message) {
        throw new Error(error.response.data.message);
      }
      throw new Error('회원가입 중 오류가 발생했습니다.');
    }
  }

  /**
   * 로그아웃
   */
  async logout(): Promise<void> {
    try {
      await apiClient.post('/auth/logout');
    } catch (error) {
      // 로그아웃 API 실패해도 로컬 토큰은 제거
      console.warn('로그아웃 API 호출 실패:', error);
    } finally {
      // 로컬 저장소에서 토큰과 사용자 정보 제거
      localStorage.removeItem('auth_token');
      sessionStorage.removeItem('auth_token');
      localStorage.removeItem('user_data');
      sessionStorage.removeItem('user_data');
    }
  }

  /**
   * 현재 사용자 정보 조회
   */
  async getCurrentUser(): Promise<User> {
    try {
      const response = await apiClient.get<ApiResponse<User>>('/auth/me');
      
      if (response.data.success && response.data.data) {
        // 최신 사용자 정보로 로컬 저장소 업데이트
        const storage = localStorage.getItem('auth_token') ? localStorage : sessionStorage;
        storage.setItem('user_data', JSON.stringify(response.data.data));
        
        return response.data.data;
      } else {
        throw new Error(response.data.message || '사용자 정보를 가져올 수 없습니다.');
      }
    } catch (error: any) {
      if (error.response?.status === 401) {
        // 토큰이 만료되었거나 유효하지 않은 경우
        this.logout();
        throw new Error('로그인이 필요합니다.');
      }
      throw new Error('사용자 정보를 가져오는 중 오류가 발생했습니다.');
    }
  }

  /**
   * 토큰 새로고침
   */
  async refreshToken(): Promise<string> {
    try {
      const response = await apiClient.post<ApiResponse<{ token: string }>>('/auth/refresh');
      
      if (response.data.success && response.data.data) {
        const { token } = response.data.data;
        
        // 새 토큰으로 업데이트
        const storage = localStorage.getItem('auth_token') ? localStorage : sessionStorage;
        storage.setItem('auth_token', token);
        
        return token;
      } else {
        throw new Error('토큰 갱신에 실패했습니다.');
      }
    } catch (error: any) {
      this.logout();
      throw new Error('인증이 만료되었습니다. 다시 로그인해주세요.');
    }
  }

  /**
   * 인증번호 발송
   */
  async sendVerificationCode(phone: string, type: 'SIGNUP' | 'PASSWORD_RESET' | 'PHONE_CHANGE'): Promise<void> {
    try {
      const response = await apiClient.post<ApiResponse<void>>('/auth/verification/send', {
        phone,
        type
      });

      if (!response.data.success) {
        throw new Error(response.data.message || '인증번호 발송에 실패했습니다.');
      }
    } catch (error: any) {
      if (error.response?.data?.message) {
        throw new Error(error.response.data.message);
      }
      throw new Error('인증번호 발송 중 오류가 발생했습니다.');
    }
  }

  /**
   * 인증번호 확인
   */
  async verifyCode(phone: string, code: string, type: 'SIGNUP' | 'PASSWORD_RESET' | 'PHONE_CHANGE'): Promise<void> {
    try {
      const response = await apiClient.post<ApiResponse<void>>('/auth/verification/verify', {
        phone,
        code,
        type
      });

      if (!response.data.success) {
        throw new Error(response.data.message || '인증번호가 올바르지 않습니다.');
      }
    } catch (error: any) {
      if (error.response?.data?.message) {
        throw new Error(error.response.data.message);
      }
      throw new Error('인증번호 확인 중 오류가 발생했습니다.');
    }
  }

  /**
   * 비밀번호 재설정 요청
   */
  async requestPasswordReset(phone: string): Promise<void> {
    try {
      const response = await apiClient.post<ApiResponse<void>>('/auth/password/reset-request', {
        phone
      });

      if (!response.data.success) {
        throw new Error(response.data.message || '비밀번호 재설정 요청에 실패했습니다.');
      }
    } catch (error: any) {
      if (error.response?.data?.message) {
        throw new Error(error.response.data.message);
      }
      throw new Error('비밀번호 재설정 요청 중 오류가 발생했습니다.');
    }
  }

  /**
   * 비밀번호 재설정
   */
  async resetPassword(data: PasswordResetRequest): Promise<void> {
    try {
      const response = await apiClient.post<ApiResponse<void>>('/auth/password/reset', data);

      if (!response.data.success) {
        throw new Error(response.data.message || '비밀번호 재설정에 실패했습니다.');
      }
    } catch (error: any) {
      if (error.response?.data?.message) {
        throw new Error(error.response.data.message);
      }
      throw new Error('비밀번호 재설정 중 오류가 발생했습니다.');
    }
  }

  /**
   * 비밀번호 변경
   */
  async changePassword(currentPassword: string, newPassword: string, confirmPassword: string): Promise<void> {
    try {
      const response = await apiClient.post<ApiResponse<void>>('/auth/password/change', {
        current_password: currentPassword,
        new_password: newPassword,
        new_password_confirmation: confirmPassword
      });

      if (!response.data.success) {
        throw new Error(response.data.message || '비밀번호 변경에 실패했습니다.');
      }
    } catch (error: any) {
      if (error.response?.data?.message) {
        throw new Error(error.response.data.message);
      }
      throw new Error('비밀번호 변경 중 오류가 발생했습니다.');
    }
  }

  /**
   * 이메일 인증 요청
   */
  async requestEmailVerification(): Promise<void> {
    try {
      const response = await apiClient.post<ApiResponse<void>>('/auth/email/verification-request');

      if (!response.data.success) {
        throw new Error(response.data.message || '이메일 인증 요청에 실패했습니다.');
      }
    } catch (error: any) {
      if (error.response?.data?.message) {
        throw new Error(error.response.data.message);
      }
      throw new Error('이메일 인증 요청 중 오류가 발생했습니다.');
    }
  }

  /**
   * 이메일 인증 확인
   */
  async verifyEmail(token: string): Promise<void> {
    try {
      const response = await apiClient.post<ApiResponse<void>>('/auth/email/verify', {
        token
      });

      if (!response.data.success) {
        throw new Error(response.data.message || '이메일 인증에 실패했습니다.');
      }
    } catch (error: any) {
      if (error.response?.data?.message) {
        throw new Error(error.response.data.message);
      }
      throw new Error('이메일 인증 중 오류가 발생했습니다.');
    }
  }

  /**
   * 로컬 스토리지에서 토큰 가져오기
   */
  getToken(): string | null {
    return localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
  }

  /**
   * 로컬 스토리지에서 사용자 정보 가져오기
   */
  getStoredUser(): User | null {
    try {
      const userData = localStorage.getItem('user_data') || sessionStorage.getItem('user_data');
      return userData ? JSON.parse(userData) : null;
    } catch (error) {
      console.error('사용자 데이터 파싱 오류:', error);
      return null;
    }
  }

  /**
   * 인증 상태 확인
   */
  isAuthenticated(): boolean {
    return !!this.getToken();
  }

  /**
   * 소셜 로그인 (추후 구현)
   */
  async socialLogin(provider: 'google' | 'kakao' | 'naver', accessToken: string): Promise<AuthResponse> {
    try {
      const response = await apiClient.post<ApiResponse<AuthResponse>>(`/auth/social/${provider}`, {
        access_token: accessToken
      });

      if (response.data.success && response.data.data) {
        const { token, user } = response.data.data;
        
        // 토큰을 세션 스토리지에 저장
        sessionStorage.setItem('auth_token', token);
        sessionStorage.setItem('user_data', JSON.stringify(user));
        
        return response.data.data;
      } else {
        throw new Error(response.data.message || '소셜 로그인에 실패했습니다.');
      }
    } catch (error: any) {
      if (error.response?.data?.message) {
        throw new Error(error.response.data.message);
      }
      throw new Error('소셜 로그인 중 오류가 발생했습니다.');
    }
  }
}

export default new AuthService();