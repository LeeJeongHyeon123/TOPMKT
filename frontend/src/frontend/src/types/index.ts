import React from 'react';

// ===== 기본 타입 정의 =====

export type UserRole = 'ROLE_USER' | 'ROLE_CORP' | 'ROLE_ADMIN';

export type PostStatus = 'PUBLISHED' | 'DRAFT' | 'DELETED';

export type LectureStatus = 'ACTIVE' | 'INACTIVE' | 'COMPLETED';

export type CommentStatus = 'ACTIVE' | 'DELETED';

// ===== 사용자 관련 타입 =====

export interface User {
  id: number;
  nickname: string;
  phone: string;
  email: string;
  role: UserRole;
  profile_image?: string;
  profile_image_thumb?: string;
  introduction?: string;
  marketing_agreed: boolean;
  phone_verified: boolean;
  email_verified: boolean;
  last_login_at?: string;
  created_at: string;
  updated_at: string;
  deleted_at?: string;
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
  marketing_agreed: boolean;
  terms_agreed: boolean;
  privacy_agreed: boolean;
}

export interface UpdateProfileRequest {
  nickname?: string;
  email?: string;
  introduction?: string;
  marketing_agreed?: boolean;
  profile_image?: File | string;
}

export interface ChangePasswordRequest {
  current_password: string;
  new_password: string;
  new_password_confirmation: string;
}

// ===== 게시글 관련 타입 =====

export interface Post {
  id: number;
  user_id: number;
  title: string;
  content: string;
  status: PostStatus;
  views: number;
  likes_count: number;
  comments_count: number;
  created_at: string;
  updated_at: string;
  deleted_at?: string;
  
  // 관계 데이터
  user: User;
  comments?: Comment[];
  is_liked?: boolean;
}

export interface CreatePostRequest {
  title: string;
  content: string;
}

export interface PostCreateRequest {
  title: string;
  content: string;
  category_id?: number;
  image?: File | string;
}

export interface PostUpdateRequest {
  title?: string;
  content?: string;
  status?: PostStatus;
  category_id?: number;
  image?: File | string;
}

// ===== 댓글 관련 타입 =====

export interface Comment {
  id: number;
  post_id: number;
  user_id: number;
  parent_id?: number;
  content: string;
  status: CommentStatus;
  created_at: string;
  updated_at: string;
  deleted_at?: string;
  
  // 관계 데이터
  user: User;
  replies?: Comment[];
  reply_count?: number;
}

export interface CreateCommentRequest {
  post_id: number;
  parent_id?: number;
  content: string;
}

export interface CommentCreateRequest {
  post_id: number;
  parent_id?: number;
  content: string;
}

export interface UpdateCommentRequest {
  content?: string;
  status?: CommentStatus;
}

// ===== 강의 관련 타입 =====

export interface Lecture {
  id: number;
  user_id: number;
  title: string;
  description: string;
  content: string;
  thumbnail?: string;
  video_url?: string;
  duration?: number;
  price: number;
  status: LectureStatus;
  views: number;
  likes_count: number;
  enrollment_count: number;
  created_at: string;
  updated_at: string;
  deleted_at?: string;
  
  // 관계 데이터
  instructor: User;
  is_enrolled?: boolean;
  is_liked?: boolean;
  progress?: number;
}

export interface CreateLectureRequest {
  title: string;
  description: string;
  content: string;
  thumbnail?: string;
  video_url?: string;
  duration?: number;
  price: number;
}

export interface LectureCreateRequest {
  title: string;
  description: string;
  content: string;
  thumbnail?: string;
  video_url?: string;
  duration?: number;
  price: number;
}

export interface UpdateLectureRequest {
  title?: string;
  description?: string;
  content?: string;
  thumbnail?: string;
  video_url?: string;
  duration?: number;
  price?: number;
  status?: LectureStatus;
}

// ===== 강의 등록 관련 타입 =====

export interface LectureEnrollment {
  id: number;
  user_id: number;
  lecture_id: number;
  progress: number;
  completed_at?: string;
  created_at: string;
  updated_at: string;
  
  // 관계 데이터
  user: User;
  lecture: Lecture;
}

// ===== 이벤트 관련 타입 =====

export interface Event {
  id: number;
  user_id: number;
  title: string;
  description: string;
  content: string;
  thumbnail?: string;
  start_date: string;
  end_date: string;
  location?: string;
  max_participants?: number;
  current_participants: number;
  price: number;
  status: 'UPCOMING' | 'ONGOING' | 'COMPLETED' | 'CANCELLED';
  created_at: string;
  updated_at: string;
  deleted_at?: string;
  
  // 관계 데이터
  organizer: User;
  is_registered?: boolean;
}

export interface CreateEventRequest {
  title: string;
  description: string;
  content: string;
  thumbnail?: string;
  start_date: string;
  end_date: string;
  location?: string;
  max_participants?: number;
  price: number;
}

export interface UpdateEventRequest {
  title?: string;
  description?: string;
  content?: string;
  thumbnail?: string;
  start_date?: string;
  end_date?: string;
  location?: string;
  max_participants?: number;
  price?: number;
  status?: 'UPCOMING' | 'ONGOING' | 'COMPLETED' | 'CANCELLED';
}

// ===== 캘린더 관련 타입 =====

export interface CalendarEvent {
  id: number;
  title: string;
  start: string;
  end: string;
  description?: string;
  location?: string;
  type: 'lecture' | 'event';
}

// ===== 인증 관련 타입 =====

export interface AuthResponse {
  success: boolean;
  message: string;
  data?: {
    user: User;
    token?: string;
  };
}

export interface VerificationRequest {
  phone: string;
}

export interface VerificationCheck {
  phone: string;
  code: string;
}

// ===== 프로필 관련 타입 =====

export interface ProfileUpdateRequest {
  nickname?: string;
  email?: string;
  introduction?: string;
  marketing_agreed?: boolean;
}

export interface ProfileImageUploadRequest {
  image: File;
}

// ===== API 응답 타입 =====

export interface ApiResponse<T = any> {
  success: boolean;
  message: string;
  data?: T;
  errors?: Record<string, string[]>;
}

export interface PaginationMeta {
  current_page: number;
  from: number;
  last_page: number;
  path: string;
  per_page: number;
  to: number;
  total: number;
  links: {
    first: string;
    last: string;
    prev?: string;
    next?: string;
  };
}

export interface PaginatedResponse<T = any> {
  data: T[];
  meta: PaginationMeta;
}

// ===== 검색 및 필터 타입 =====

export interface SearchParams {
  query?: string;
  page?: number;
  per_page?: number;
  sort_by?: string;
  sort_direction?: 'asc' | 'desc';
}

export interface SearchFilters {
  search?: string;
  page?: number;
  limit?: number;
  category?: string;
  status?: string;
  sort?: string;
}

export interface PostSearchParams extends SearchParams {
  status?: PostStatus;
  user_id?: number;
}

export interface LectureSearchParams extends SearchParams {
  status?: LectureStatus;
  instructor_id?: number;
  min_price?: number;
  max_price?: number;
  category?: string;
}

export interface UserSearchParams extends SearchParams {
  role?: UserRole;
  verified?: boolean;
}

// ===== 파일 업로드 타입 =====

export interface FileUploadResponse {
  url: string;
  filename: string;
  size: number;
  mime_type: string;
}

export interface ImageUploadResponse extends FileUploadResponse {
  width: number;
  height: number;
  thumbnail_url?: string;
}

// ===== 알림 관련 타입 =====

export interface Notification {
  id: number;
  user_id: number;
  type: 'POST_COMMENT' | 'LECTURE_ENROLLMENT' | 'EVENT_REGISTRATION' | 'SYSTEM';
  title: string;
  message: string;
  data?: Record<string, any>;
  read_at?: string;
  created_at: string;
}

// ===== 설정 관련 타입 =====

export interface UserSettings {
  notifications: {
    email: boolean;
    push: boolean;
    marketing: boolean;
  };
  privacy: {
    profile_public: boolean;
    show_email: boolean;
    show_phone: boolean;
  };
}

// ===== 좋아요/북마크 타입 =====

export interface Like {
  id: number;
  user_id: number;
  likeable_type: 'post' | 'lecture' | 'comment';
  likeable_id: number;
  created_at: string;
}

export interface Bookmark {
  id: number;
  user_id: number;
  bookmarkable_type: 'post' | 'lecture' | 'event';
  bookmarkable_id: number;
  created_at: string;
}

// ===== 통계 관련 타입 =====

export interface DashboardStats {
  total_users: number;
  total_posts: number;
  total_lectures: number;
  total_events: number;
  active_users_today: number;
  revenue_this_month: number;
}

export interface UserStats {
  posts_count: number;
  lectures_count: number;
  enrollments_count: number;
  followers_count: number;
  following_count: number;
}

// ===== 폼 관련 타입 =====

export interface FormError {
  field: string;
  message: string;
}

export interface FormState<T = any> {
  data: T;
  errors: Record<string, string>;
  isSubmitting: boolean;
  isValid: boolean;
}

// ===== 컴포넌트 Props 타입 =====

export interface ButtonProps {
  variant?: 'primary' | 'secondary' | 'outline' | 'ghost' | 'danger';
  size?: 'sm' | 'md' | 'lg';
  fullWidth?: boolean;
  loading?: boolean;
  disabled?: boolean;
  leftIcon?: React.ReactNode;
  rightIcon?: React.ReactNode;
  children: React.ReactNode;
  onClick?: () => void;
  type?: 'button' | 'submit' | 'reset';
  className?: string;
}

export interface InputProps {
  label?: string;
  placeholder?: string;
  hint?: string;
  error?: string;
  required?: boolean;
  disabled?: boolean;
  fullWidth?: boolean;
  leftIcon?: React.ReactNode;
  rightIcon?: React.ReactNode;
  type?: 'text' | 'email' | 'password' | 'tel' | 'number' | 'url';
  name: string;
  value: string;
  onChange: (e: React.ChangeEvent<HTMLInputElement>) => void;
  onBlur?: (e: React.FocusEvent<HTMLInputElement>) => void;
  onFocus?: (e: React.FocusEvent<HTMLInputElement>) => void;
  maxLength?: number;
  minLength?: number;
  pattern?: string;
  autoComplete?: string;
  className?: string;
}

// ===== Toast 알림 타입 =====

export interface Toast {
  id: string;
  type: 'success' | 'error' | 'warning' | 'info';
  title?: string;
  message: string;
  duration?: number;
  action?: {
    label: string;
    onClick: () => void;
  };
}

// ===== 라우팅 관련 타입 =====

export interface RouteConfig {
  path: string;
  component: React.ComponentType;
  exact?: boolean;
  auth?: boolean;
  role?: UserRole;
  title?: string;
}

// ===== 테마 관련 타입 =====

export interface Theme {
  colors: {
    primary: string;
    secondary: string;
    success: string;
    warning: string;
    error: string;
    info: string;
    background: string;
    surface: string;
    text: string;
  };
  fonts: {
    body: string;
    heading: string;
  };
  spacing: {
    xs: string;
    sm: string;
    md: string;
    lg: string;
    xl: string;
  };
}

// ===== 유틸리티 타입 =====

export type Optional<T, K extends keyof T> = Omit<T, K> & Partial<Pick<T, K>>;

export type RequireAtLeastOne<T, Keys extends keyof T = keyof T> =
  Pick<T, Exclude<keyof T, Keys>> & 
  {
    [K in Keys]-?: Required<Pick<T, K>> & Partial<Pick<T, Exclude<Keys, K>>>;
  }[Keys];

export type DeepPartial<T> = {
  [P in keyof T]?: T[P] extends object ? DeepPartial<T[P]> : T[P];
};

// ===== 내보내기 =====

export default {};