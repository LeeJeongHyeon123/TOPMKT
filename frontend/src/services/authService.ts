// 인증 관련 API 서비스
import apiClient, { handleApiError } from '../config/api';
import { ApiResponse, User, LoginRequest, SignupRequest } from '../types';

export class AuthService {
  
  /**
   * 로그인
   */
  static async login(credentials: LoginRequest): Promise<ApiResponse<{ user: User }>> {
    try {
      const response = await apiClient.post('/auth/login', credentials);
      return response.data;
    } catch (error) {
      throw new Error(handleApiError(error));
    }
  }

  /**
   * 회원가입
   */
  static async signup(userData: SignupRequest): Promise<ApiResponse<{ user: User }>> {
    try {
      const response = await apiClient.post('/auth/signup', userData);
      return response.data;
    } catch (error) {
      throw new Error(handleApiError(error));
    }
  }

  /**
   * 로그아웃
   */
  static async logout(): Promise<ApiResponse> {
    try {
      const response = await apiClient.post('/auth/logout');
      return response.data;
    } catch (error) {
      throw new Error(handleApiError(error));
    }
  }

  /**
   * 현재 사용자 정보 조회
   */
  static async getCurrentUser(): Promise<ApiResponse<{ user: User }>> {
    try {
      const response = await apiClient.get('/auth/me');
      return response.data;
    } catch (error) {
      throw new Error(handleApiError(error));
    }
  }

  /**
   * JWT 토큰 갱신
   */
  static async refreshToken(): Promise<ApiResponse> {
    try {
      const response = await apiClient.post('/auth/refresh');
      return response.data;
    } catch (error) {
      throw new Error(handleApiError(error));
    }
  }

  /**
   * SMS 인증번호 발송
   */
  static async sendVerificationCode(
    phone: string, 
    type: 'SIGNUP' | 'LOGIN' | 'PASSWORD_RESET' = 'SIGNUP'
  ): Promise<ApiResponse> {
    try {
      const response = await apiClient.post('/auth/send-verification', {
        phone,
        type
      });
      return response.data;
    } catch (error) {
      throw new Error(handleApiError(error));
    }
  }

  /**
   * SMS 인증번호 확인
   */
  static async verifyCode(
    phone: string, 
    code: string, 
    type: 'SIGNUP' | 'LOGIN' | 'PASSWORD_RESET' = 'SIGNUP'
  ): Promise<ApiResponse> {
    try {
      const response = await apiClient.post('/auth/verify-code', {
        phone,
        code,
        type
      });
      return response.data;
    } catch (error) {
      throw new Error(handleApiError(error));
    }
  }

  /**
   * 비밀번호 재설정 요청
   */
  static async requestPasswordReset(phone: string): Promise<ApiResponse> {
    try {
      const response = await apiClient.post('/auth/password-reset-request', {
        phone
      });
      return response.data;
    } catch (error) {
      throw new Error(handleApiError(error));
    }
  }

  /**
   * 비밀번호 재설정 완료
   */
  static async resetPassword(
    phone: string, 
    code: string, 
    newPassword: string
  ): Promise<ApiResponse> {
    try {
      const response = await apiClient.post('/auth/password-reset', {
        phone,
        code,
        password: newPassword,
        password_confirmation: newPassword
      });
      return response.data;
    } catch (error) {
      throw new Error(handleApiError(error));
    }
  }

  /**
   * 로그인 상태 확인
   */
  static async checkAuthStatus(): Promise<boolean> {
    try {
      const response = await this.getCurrentUser();
      return response.success && !!response.data?.user;
    } catch (error) {
      return false;
    }
  }

  /**
   * 계정 중복 확인 - 닉네임
   */
  static async checkNicknameAvailability(nickname: string): Promise<ApiResponse<{ available: boolean }>> {
    try {
      const response = await apiClient.post('/auth/check-nickname', {
        nickname
      });
      return response.data;
    } catch (error) {
      throw new Error(handleApiError(error));
    }
  }

  /**
   * 계정 중복 확인 - 이메일
   */
  static async checkEmailAvailability(email: string): Promise<ApiResponse<{ available: boolean }>> {
    try {
      const response = await apiClient.post('/auth/check-email', {
        email
      });
      return response.data;
    } catch (error) {
      throw new Error(handleApiError(error));
    }
  }

  /**
   * 계정 중복 확인 - 휴대폰
   */
  static async checkPhoneAvailability(phone: string): Promise<ApiResponse<{ available: boolean }>> {
    try {
      const response = await apiClient.post('/auth/check-phone', {
        phone
      });
      return response.data;
    } catch (error) {
      throw new Error(handleApiError(error));
    }
  }

  /**
   * 소셜 로그인 (향후 확장용)
   */
  static async socialLogin(
    provider: 'google' | 'facebook' | 'kakao' | 'naver',
    accessToken: string
  ): Promise<ApiResponse<{ user: User }>> {
    try {
      const response = await apiClient.post(`/auth/social/${provider}`, {
        access_token: accessToken
      });
      return response.data;
    } catch (error) {
      throw new Error(handleApiError(error));
    }
  }

  /**
   * 이메일 인증 요청
   */
  static async requestEmailVerification(): Promise<ApiResponse> {
    try {
      const response = await apiClient.post('/auth/email-verification-request');
      return response.data;
    } catch (error) {
      throw new Error(handleApiError(error));
    }
  }

  /**
   * 이메일 인증 완료
   */
  static async verifyEmail(token: string): Promise<ApiResponse> {
    try {
      const response = await apiClient.post('/auth/verify-email', {
        token
      });
      return response.data;
    } catch (error) {
      throw new Error(handleApiError(error));
    }
  }

  /**
   * 계정 삭제 요청
   */
  static async deleteAccount(password: string): Promise<ApiResponse> {
    try {
      const response = await apiClient.post('/auth/delete-account', {
        password
      });
      return response.data;
    } catch (error) {
      throw new Error(handleApiError(error));
    }
  }

  /**
   * 로그인 기록 조회
   */
  static async getLoginHistory(): Promise<ApiResponse<{ sessions: any[] }>> {
    try {
      const response = await apiClient.get('/auth/login-history');
      return response.data;
    } catch (error) {
      throw new Error(handleApiError(error));
    }
  }

  /**
   * 특정 세션 종료
   */
  static async terminateSession(sessionId: string): Promise<ApiResponse> {
    try {
      const response = await apiClient.post('/auth/terminate-session', {
        session_id: sessionId
      });
      return response.data;
    } catch (error) {
      throw new Error(handleApiError(error));
    }
  }

  /**
   * 모든 다른 세션 종료
   */
  static async terminateOtherSessions(): Promise<ApiResponse> {
    try {
      const response = await apiClient.post('/auth/terminate-other-sessions');
      return response.data;
    } catch (error) {
      throw new Error(handleApiError(error));
    }
  }
}

export default AuthService;