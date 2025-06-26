import api from './api';

export interface Event {
  id: number;
  title: string;
  description: string;
  organizer_id: number;
  organizer_name: string;
  organizer_profile_image?: string;
  start_date: string;
  end_date: string;
  start_time: string;
  end_time: string;
  location?: string;
  is_online: boolean;
  meeting_url?: string;
  max_participants?: number;
  current_participants: number;
  registration_fee: number;
  currency: string;
  status: 'draft' | 'published' | 'cancelled' | 'completed';
  category?: string;
  tags?: string[];
  images?: string[];
  requirements?: string;
  benefits?: string;
  agenda?: string;
  created_at: string;
  updated_at: string;
  is_registered?: boolean;
  registration_deadline?: string;
}

export interface EventRegistration {
  id: number;
  event_id: number;
  user_id: number;
  registered_at: string;
  status: 'registered' | 'attended' | 'cancelled';
  payment_status?: 'pending' | 'paid' | 'refunded';
}

export interface EventFilters {
  search?: string;
  category?: string;
  organizer?: string;
  date_from?: string;
  date_to?: string;
  is_online?: boolean;
  fee_min?: number;
  fee_max?: number;
  status?: string;
  sort?: 'date_asc' | 'date_desc' | 'fee_asc' | 'fee_desc' | 'popular';
  page?: number;
  limit?: number;
}

class EventService {
  // 이벤트 목록 조회
  async getEvents(filters: EventFilters = {}) {
    const params = new URLSearchParams();
    
    Object.entries(filters).forEach(([key, value]) => {
      if (value !== undefined && value !== null) {
        params.append(key, value.toString());
      }
    });

    const response = await api.get(`/api/events?${params.toString()}`);
    return response.data;
  }

  // 이벤트 상세 조회
  async getEvent(id: number) {
    const response = await api.get(`/api/events/${id}`);
    return response.data;
  }

  // 이벤트 생성 (주최자/관리자)
  async createEvent(data: {
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
    registration_fee: number;
    currency: string;
    category?: string;
    tags?: string[];
    requirements?: string;
    benefits?: string;
    agenda?: string;
    registration_deadline?: string;
  }) {
    const response = await api.post('/api/events', data);
    return response.data;
  }

  // 이벤트 수정
  async updateEvent(id: number, data: Partial<Event>) {
    const response = await api.put(`/api/events/${id}`, data);
    return response.data;
  }

  // 이벤트 삭제
  async deleteEvent(id: number) {
    const response = await api.delete(`/api/events/${id}`);
    return response.data;
  }

  // 이벤트 참가 신청
  async registerForEvent(id: number) {
    const response = await api.post(`/api/events/${id}/register`);
    return response.data;
  }

  // 이벤트 참가 신청 취소
  async cancelRegistration(id: number) {
    const response = await api.delete(`/api/events/${id}/register`);
    return response.data;
  }

  // 내 이벤트 참가 목록
  async getMyRegistrations() {
    const response = await api.get('/api/events/my-registrations');
    return response.data;
  }

  // 이벤트 참가자 목록 (주최자/관리자)
  async getEventParticipants(id: number) {
    const response = await api.get(`/api/events/${id}/participants`);
    return response.data;
  }

  // 이벤트 출석 체크 (주최자/관리자)
  async markAttendance(eventId: number, userId: number, attended: boolean) {
    const response = await api.post(`/api/events/${eventId}/attendance`, {
      user_id: userId,
      attended
    });
    return response.data;
  }

  // 이벤트 카테고리 목록
  async getCategories() {
    const response = await api.get('/api/events/categories');
    return response.data;
  }

  // 인기 이벤트 조회
  async getPopularEvents(limit: number = 10) {
    const response = await api.get(`/api/events/popular?limit=${limit}`);
    return response.data;
  }

  // 추천 이벤트 조회
  async getRecommendedEvents(limit: number = 10) {
    const response = await api.get(`/api/events/recommended?limit=${limit}`);
    return response.data;
  }

  // 이벤트 이미지 업로드
  async uploadEventImage(eventId: number, file: File) {
    const formData = new FormData();
    formData.append('image', file);
    
    const response = await api.post(`/api/events/${eventId}/images`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data',
      },
    });
    return response.data;
  }

  // 이벤트 이미지 삭제
  async deleteEventImage(eventId: number, imageId: number) {
    const response = await api.delete(`/api/events/${eventId}/images/${imageId}`);
    return response.data;
  }

  // 이벤트 일정 캘린더 조회
  async getEventCalendar(year: number, month: number) {
    const response = await api.get(`/api/events/calendar?year=${year}&month=${month}`);
    return response.data;
  }

  // 다가오는 이벤트 조회
  async getUpcomingEvents(limit: number = 10) {
    const response = await api.get(`/api/events/upcoming?limit=${limit}`);
    return response.data;
  }
}

export default new EventService();