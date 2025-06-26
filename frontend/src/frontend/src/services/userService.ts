// 사용자 관련 API 서비스
import apiClient, { handleApiError } from '@/config/api'
import { ApiResponse, User, ProfileUpdateRequest } from '@/types'

export class UserService {
  
  /**
   * 사용자 프로필 조회
   */
  static async getProfile(userId?: number): Promise<ApiResponse<{ user: User }>> {
    try {
      const url = userId ? `/users/${userId}` : '/users/me';
      const response = await apiClient.get(url);
      return response.data;
    } catch (error) {
      throw new Error(handleApiError(error));
    }
  }

  /**
   * 프로필 업데이트
   */
  static async updateProfile(userData: ProfileUpdateRequest): Promise<ApiResponse<{ user: User }>> {
    try {
      const response = await apiClient.post('/profile/update', userData);
      return response.data;
    } catch (error) {
      throw new Error(handleApiError(error));
    }
  }

  /**
   * 프로필 이미지 업로드
   */
  static async uploadProfileImage(imageFile: File): Promise<ApiResponse<{ url: string }>> {
    try {
      const formData = new FormData();
      formData.append('profile_image', imageFile);

      const response = await apiClient.post('/profile/upload-image', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      });
      return response.data;
    } catch (error) {
      throw new Error(handleApiError(error));
    }
  }

  /**
   * 공개 프로필 조회 (닉네임으로)
   */
  static async getPublicProfile(nickname: string): Promise<ApiResponse<{ user: User }>> {
    try {
      const response = await apiClient.get(`/profile/${nickname}`);
      return response.data;
    } catch (error) {
      throw new Error(handleApiError(error));
    }
  }

  /**
   * 프로필 이미지 URL 조회
   */
  static async getProfileImageUrl(userId: number): Promise<ApiResponse<{ url: string }>> {
    try {
      const response = await apiClient.get(`/api/users/${userId}/profile-image`);
      return response.data;
    } catch (error) {
      throw new Error(handleApiError(error));
    }
  }

  /**
   * 사용자 검색
   */
  static async searchUsers(
    query: string, 
    limit: number = 10
  ): Promise<ApiResponse<{ users: User[] }>> {
    try {
      const response = await apiClient.post('/api/users/search', {
        query,
        limit
      });
      return response.data;
    } catch (error) {
      throw new Error(handleApiError(error));
    }
  }

  /**
   * 내 활동 통계 조회
   */
  static async getMyStats(): Promise<ApiResponse<{
    posts_count: number;
    comments_count: number;
    likes_received: number;
    lectures_created: number;
    events_created: number;
  }>> {
    try {
      const response = await apiClient.get('/api/users/my-stats');
      return response.data;
    } catch (error) {
      throw new Error(handleApiError(error));
    }
  }

  /**
   * 사용자 활동 기록 조회
   */
  static async getActivityLog(
    page: number = 1,
    limit: number = 20
  ): Promise<ApiResponse<{
    activities: any[];
    pagination: any;
  }>> {
    try {
      const response = await apiClient.get('/api/users/activity-log', {
        params: { page, limit }
      });
      return response.data;
    } catch (error) {
      throw new Error(handleApiError(error));
    }
  }

  /**
   * 비밀번호 변경
   */
  static async changePassword(
    currentPassword: string,
    newPassword: string
  ): Promise<ApiResponse> {
    try {
      const response = await apiClient.post('/profile/change-password', {
        current_password: currentPassword,
        new_password: newPassword,
        new_password_confirmation: newPassword
      });
      return response.data;
    } catch (error) {
      throw new Error(handleApiError(error));
    }
  }

  /**
   * 계정 설정 업데이트
   */
  static async updateSettings(settings: {
    email_notifications?: boolean;
    sms_notifications?: boolean;
    marketing_agreed?: boolean;
    privacy_level?: 'public' | 'private' | 'friends';
  }): Promise<ApiResponse> {
    try {
      const response = await apiClient.post('/profile/update-settings', settings);
      return response.data;
    } catch (error) {
      throw new Error(handleApiError(error));
    }
  }

  /**
   * 계정 설정 조회
   */
  static async getSettings(): Promise<ApiResponse<{
    email_notifications: boolean;
    sms_notifications: boolean;
    marketing_agreed: boolean;
    privacy_level: string;
  }>> {
    try {
      const response = await apiClient.get('/profile/settings');
      return response.data;
    } catch (error) {
      throw new Error(handleApiError(error));
    }
  }

  /**
   * 소셜 링크 업데이트
   */
  static async updateSocialLinks(socialLinks: {
    website?: string;
    facebook?: string;
    twitter?: string;
    instagram?: string;
    linkedin?: string;
    youtube?: string;
    blog?: string;
  }): Promise<ApiResponse> {
    try {
      const response = await apiClient.post('/profile/update-social-links', {
        social_links: JSON.stringify(socialLinks)
      });
      return response.data;
    } catch (error) {
      throw new Error(handleApiError(error));
    }
  }

  /**
   * 팔로우/언팔로우 (향후 구현용)
   */
  static async toggleFollow(userId: number): Promise<ApiResponse<{ is_following: boolean }>> {
    try {
      const response = await apiClient.post(`/api/users/${userId}/follow`);
      return response.data;
    } catch (error) {
      throw new Error(handleApiError(error));
    }
  }

  /**
   * 팔로워 목록 조회 (향후 구현용)
   */
  static async getFollowers(
    userId: number,
    page: number = 1
  ): Promise<ApiResponse<{
    followers: User[];
    pagination: any;
  }>> {
    try {
      const response = await apiClient.get(`/api/users/${userId}/followers`, {
        params: { page }
      });
      return response.data;
    } catch (error) {
      throw new Error(handleApiError(error));
    }
  }

  /**
   * 팔로잉 목록 조회 (향후 구현용)
   */
  static async getFollowing(
    userId: number,
    page: number = 1
  ): Promise<ApiResponse<{
    following: User[];
    pagination: any;
  }>> {
    try {
      const response = await apiClient.get(`/api/users/${userId}/following`, {
        params: { page }
      });
      return response.data;
    } catch (error) {
      throw new Error(handleApiError(error));
    }
  }
}

export default UserService;