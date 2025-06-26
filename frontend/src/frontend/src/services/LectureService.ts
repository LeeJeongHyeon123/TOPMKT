import api from './api';

export interface Lecture {
  id: number;
  title: string;
  description: string;
  instructor_id: number;
  instructor_name: string;
  instructor_bio?: string;
  instructor_profile_image?: string;
  start_date: string;
  end_date: string;
  start_time: string;
  end_time: string;
  location?: string;
  is_online: boolean;
  meeting_url?: string;
  max_participants?: number;
  current_participants: number;
  price: number;
  currency: string;
  status: 'draft' | 'published' | 'cancelled' | 'completed';
  category?: string;
  tags?: string[];
  images?: string[];
  created_at: string;
  updated_at: string;
  is_registered?: boolean;
}

export interface LectureRegistration {
  id: number;
  lecture_id: number;
  user_id: number;
  registered_at: string;
  status: 'registered' | 'attended' | 'cancelled';
  payment_status?: 'pending' | 'paid' | 'refunded';
}

export interface LectureFilters {
  search?: string;
  category?: string;
  instructor?: string;
  date_from?: string;
  date_to?: string;
  is_online?: boolean;
  price_min?: number;
  price_max?: number;
  status?: string;
  sort?: 'date_asc' | 'date_desc' | 'price_asc' | 'price_desc' | 'popular';
  page?: number;
  limit?: number;
}

class LectureService {
  // 강의 목록 조회
  async getLectures(filters: LectureFilters = {}) {
    const params = new URLSearchParams();
    
    Object.entries(filters).forEach(([key, value]) => {
      if (value !== undefined && value !== null) {
        params.append(key, value.toString());
      }
    });

    const response = await api.get(`/api/lectures?${params.toString()}`);
    return response.data;
  }

  // 강의 상세 조회
  async getLecture(id: number) {
    const response = await api.get(`/api/lectures/${id}`);
    return response.data;
  }

  // 강의 생성 (강사/관리자)
  async createLecture(data: {
    title: string;
    description: string;
    start_date: string;
    end_date: string;
    start_time: string;
    end_time: string;
    location?: string;
    is_online: boolean;
    meeting_url?: string;
    max_participants?: number;
    price: number;
    currency: string;
    category?: string;
    tags?: string[];
  }) {
    const response = await api.post('/api/lectures', data);
    return response.data;
  }

  // 강의 수정
  async updateLecture(id: number, data: Partial<Lecture>) {
    const response = await api.put(`/api/lectures/${id}`, data);
    return response.data;
  }

  // 강의 삭제
  async deleteLecture(id: number) {
    const response = await api.delete(`/api/lectures/${id}`);
    return response.data;
  }

  // 강의 신청
  async registerForLecture(id: number) {
    const response = await api.post(`/api/lectures/${id}/register`);
    return response.data;
  }

  // 강의 신청 취소
  async cancelRegistration(id: number) {
    const response = await api.delete(`/api/lectures/${id}/register`);
    return response.data;
  }

  // 내 강의 신청 목록
  async getMyRegistrations() {
    const response = await api.get('/api/lectures/my-registrations');
    return response.data;
  }

  // 강의 참석자 목록 (강사/관리자)
  async getLectureParticipants(id: number) {
    const response = await api.get(`/api/lectures/${id}/participants`);
    return response.data;
  }

  // 강의 출석 체크 (강사/관리자)
  async markAttendance(lectureId: number, userId: number, attended: boolean) {
    const response = await api.post(`/api/lectures/${lectureId}/attendance`, {
      user_id: userId,
      attended
    });
    return response.data;
  }

  // 강의 카테고리 목록
  async getCategories() {
    const response = await api.get('/api/lectures/categories');
    return response.data;
  }

  // 인기 강의 조회
  async getPopularLectures(limit: number = 10) {
    const response = await api.get(`/api/lectures/popular?limit=${limit}`);
    return response.data;
  }

  // 추천 강의 조회
  async getRecommendedLectures(limit: number = 10) {
    const response = await api.get(`/api/lectures/recommended?limit=${limit}`);
    return response.data;
  }

  // 강의 이미지 업로드
  async uploadLectureImage(lectureId: number, file: File) {
    const formData = new FormData();
    formData.append('image', file);
    
    const response = await api.post(`/api/lectures/${lectureId}/images`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    return response.data;
  }

  // 강의 이미지 삭제
  async deleteLectureImage(lectureId: number, imageId: number) {
    const response = await api.delete(`/api/lectures/${lectureId}/images/${imageId}`);
    return response.data;
  }
}

export default new LectureService();