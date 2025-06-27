// 탑마케팅 플랫폼 TypeScript 타입 정의
// 기존 PHP 프로젝트의 데이터 구조를 기반으로 작성

// === 기본 API 응답 타입 ===
export interface ApiResponse<T = any> {
  success: boolean;
  message: string;
  data?: T;
  errors?: Record<string, string>;
}

// === 사용자 관련 타입 ===
export interface User {
  id: number;
  nickname: string;
  phone: string;
  email: string;
  bio?: string;
  birth_date?: string;
  gender?: 'M' | 'F' | 'OTHER';
  profile_image_original?: string;
  profile_image_profile?: string;
  profile_image_thumb?: string;
  website_url?: string;
  social_links?: string; // JSON string
  marketing_agreed: boolean;
  phone_verified: boolean;
  email_verified: boolean;
  status: 'active' | 'inactive' | 'suspended' | 'deleted';
  role: 'ROLE_USER' | 'ROLE_CORP' | 'ROLE_ADMIN';
  last_login?: string;
  created_at: string;
  updated_at: string;
}

export interface AuthTokens {
  access_token: string;
  refresh_token: string;
  expires_in: number;
}

export interface LoginRequest {
  phone: string;
  password: string;
  remember?: boolean;
}

export interface SignupRequest {
  nickname: string;
  phone: string;
  email: string;
  password: string;
  password_confirmation: string;
  verification_code: string;
  marketing_agreed?: boolean;
}

export interface ProfileUpdateRequest {
  nickname?: string;
  email?: string;
  bio?: string;
  birth_date?: string;
  gender?: 'M' | 'F' | 'OTHER';
  website_url?: string;
  social_links?: string;
}

// === 게시글 관련 타입 ===
export interface Post {
  id: number;
  user_id: number;
  category_id?: number;
  title: string;
  content: string;
  image_path?: string;
  view_count: number;
  like_count: number;
  comment_count: number;
  status: 'published' | 'draft' | 'deleted';
  created_at: string;
  updated_at: string;
  user?: User; // 작성자 정보
}

export interface PostCreateRequest {
  title: string;
  content: string;
  category_id?: number;
  image?: File;
}

export interface PostUpdateRequest extends Partial<PostCreateRequest> {
  id: number;
}

// === 댓글 관련 타입 ===
export interface Comment {
  id: number;
  post_id: number;
  user_id: number;
  parent_id?: number;
  content: string;
  status: 'active' | 'deleted';
  created_at: string;
  updated_at: string;
  user?: User; // 작성자 정보
  replies?: Comment[]; // 대댓글
}

export interface CommentCreateRequest {
  post_id: number;
  content: string;
  parent_id?: number;
}

// === 강의/이벤트 관련 타입 ===
export interface Lecture {
  id: number;
  user_id: number;
  title: string;
  description: string;
  instructor_name: string;
  instructor_info?: string;
  start_date: string;
  end_date: string;
  start_time: string;
  end_time: string;
  timezone: string;
  location_type: 'online' | 'offline' | 'hybrid';
  venue_name?: string;
  venue_address?: string;
  venue_latitude?: number;
  venue_longitude?: number;
  online_link?: string;
  max_participants?: number;
  registration_fee: number;
  registration_deadline?: string;
  category: 'seminar' | 'workshop' | 'conference' | 'webinar' | 'training';
  content_type: 'lecture' | 'event';
  status: 'draft' | 'published' | 'cancelled' | 'completed';
  is_featured: boolean;
  view_count: number;
  registration_count: number;
  banner_image?: string;
  youtube_video?: string;
  instructor_image?: string;
  instructors_json?: string; // JSON string
  lecture_images?: string; // JSON string
  created_at: string;
  updated_at: string;
  user?: User; // 등록자 정보
}

export interface LectureCreateRequest {
  title: string;
  description: string;
  instructor_name: string;
  instructor_info?: string;
  start_date: string;
  end_date: string;
  start_time: string;
  end_time: string;
  location_type: 'online' | 'offline' | 'hybrid';
  venue_name?: string;
  venue_address?: string;
  online_link?: string;
  max_participants?: number;
  registration_fee?: number;
  registration_deadline?: string;
  category: 'seminar' | 'workshop' | 'conference' | 'webinar' | 'training';
  content_type: 'lecture' | 'event';
}

// === 페이지네이션 타입 ===
export interface PaginationMeta {
  current_page: number;
  total_pages: number;
  total_items: number;
  items_per_page: number;
  has_next: boolean;
  has_prev: boolean;
}

export interface PaginatedResponse<T> {
  data: T[];
  meta: PaginationMeta;
}

// === 검색 및 필터 타입 ===
export interface SearchFilters {
  search?: string;
  category?: string;
  status?: string;
  sort?: 'latest' | 'oldest' | 'popular' | 'views';
  page?: number;
  limit?: number;
}

// === 채팅 관련 타입 ===
export interface ChatRoom {
  id: string;
  name: string;
  description?: string;
  members: string[]; // user IDs
  created_at: string;
  updated_at: string;
}

export interface ChatMessage {
  id: string;
  room_id: string;
  user_id: string;
  content: string;
  type: 'text' | 'image' | 'file';
  timestamp: string;
  user?: User;
}

// === 기업회원 관련 타입 ===
export interface CorporateProfile {
  id: number;
  user_id: number;
  company_name: string;
  business_registration: string;
  company_address?: string;
  company_phone?: string;
  company_email?: string;
  company_website?: string;
  business_type?: string;
  employee_count?: string;
  annual_revenue?: string;
  introduction?: string;
  logo_image?: string;
  status: 'pending' | 'approved' | 'rejected';
  approved_at?: string;
  created_at: string;
  updated_at: string;
}

// === 알림 관련 타입 ===
export interface Notification {
  id: number;
  user_id: number;
  type: string;
  reference_id?: number;
  message: string;
  is_read: boolean;
  created_at: string;
}

// === 폼 유효성 검사 타입 ===
export interface FormErrors {
  [key: string]: string | undefined;
}

export interface FormState<T> {
  data: T;
  errors: FormErrors;
  isSubmitting: boolean;
  isValid: boolean;
}

// === 로딩 상태 타입 ===
export interface LoadingState {
  isLoading: boolean;
  error: string | null;
}

// === 미디어 업로드 타입 ===
export interface MediaUploadResponse {
  success: boolean;
  url: string;
  filename: string;
  size: number;
  type: string;
}

// === 설정 관련 타입 ===
export interface AppSettings {
  site_name: string;
  site_description: string;
  contact_email: string;
  contact_phone: string;
  social_links: Record<string, string>;
  features: {
    registration_enabled: boolean;
    chat_enabled: boolean;
    lecture_enabled: boolean;
    corporate_enabled: boolean;
  };
}

// === 통계 관련 타입 ===
export interface DashboardStats {
  total_users: number;
  total_posts: number;
  total_lectures: number;
  total_events: number;
  monthly_registrations: number;
  popular_lectures: Lecture[];
  recent_posts: Post[];
}

// === 이벤트 타입 (캘린더용) ===
export interface CalendarEvent {
  id: number;
  title: string;
  start: string;
  end: string;
  type: 'lecture' | 'event';
  location?: string;
  instructor?: string;
  registration_fee?: number;
  status: string;
}

// === 유틸리티 타입 ===
export type Optional<T, K extends keyof T> = Omit<T, K> & Partial<Pick<T, K>>;
export type RequireOnly<T, K extends keyof T> = Partial<T> & Required<Pick<T, K>>;

// === 환경별 설정 타입 ===
export interface AppConfig {
  API_BASE_URL: string;
  FIREBASE_CONFIG: {
    apiKey: string;
    authDomain: string;
    databaseURL: string;
    projectId: string;
    storageBucket: string;
    messagingSenderId: string;
    appId: string;
  };
  RECAPTCHA_SITE_KEY: string;
  GOOGLE_MAPS_API_KEY: string;
}