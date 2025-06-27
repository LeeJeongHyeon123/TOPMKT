import { get, post, put, del } from '../config/api';

export interface CommunityPost {
  id: number;
  user_id: number;
  title: string;
  content: string;
  content_preview?: string;
  image_path?: string;
  view_count: number;
  like_count: number;
  comment_count: number;
  status: string;
  created_at: string;
  updated_at?: string;
  author_name: string;
  profile_image?: string;
  is_liked?: boolean;
  is_pinned?: boolean;
}

export interface CommunityComment {
  id: number;
  post_id: number;
  content: string;
  author_id: number;
  author_name: string;
  author_nickname: string;
  author_profile_image?: string;
  created_at: string;
  updated_at: string;
  likes: number;
  is_liked?: boolean;
  parent_id?: number;
  replies?: CommunityComment[];
}

export interface CommunityFilters {
  search?: string;
  filter?: 'all' | 'title' | 'content' | 'author';
  page?: number;
  limit?: number;
}

class CommunityService {
  // 커뮤니티 게시글 목록 조회
  async getPosts(filters: CommunityFilters = {}) {
    const params = new URLSearchParams();
    
    if (filters.search) params.append('search', filters.search);
    if (filters.filter && filters.filter !== 'all') params.append('filter', filters.filter);
    if (filters.page) params.append('page', filters.page.toString());
    if (filters.limit) params.append('limit', filters.limit.toString());

    const queryString = params.toString();
    const url = `/api/community/posts${queryString ? `?${queryString}` : ''}`;
    
    const response = await get(url);
    return response.data;
  }

  // 게시글 상세 조회
  async getPost(id: number) {
    const response = await get(`/api/community/posts/${id}`);
    return response.data;
  }

  // 게시글 작성
  async createPost(data: {
    title: string;
    content: string;
    category?: string;
    tags?: string[];
  }) {
    const response = await post('/api/community/posts', data);
    return response.data;
  }

  // 게시글 수정
  async updatePost(id: number, data: {
    title?: string;
    content?: string;
    category?: string;
    tags?: string[];
  }) {
    const response = await put(`/api/community/posts/${id}`, data);
    return response.data;
  }

  // 게시글 삭제
  async deletePost(id: number) {
    const response = await del(`/api/community/posts/${id}`);
    return response.data;
  }

  // 게시글 좋아요
  async likePost(id: number) {
    const response = await post(`/api/community/posts/${id}/like`);
    return response.data;
  }

  // 게시글 좋아요 취소
  async unlikePost(id: number) {
    const response = await del(`/api/community/posts/${id}/like`);
    return response.data;
  }

  // 댓글 목록 조회
  async getComments(postId: number) {
    const response = await get(`/api/community/posts/${postId}/comments`);
    return response.data;
  }

  // 댓글 작성
  async createComment(postId: number, data: {
    content: string;
    parent_id?: number;
  }) {
    const response = await post(`/api/community/posts/${postId}/comments`, data);
    return response.data;
  }

  // 댓글 수정
  async updateComment(postId: number, commentId: number, data: {
    content: string;
  }) {
    const response = await put(`/api/community/posts/${postId}/comments/${commentId}`, data);
    return response.data;
  }

  // 댓글 삭제
  async deleteComment(postId: number, commentId: number) {
    const response = await del(`/api/community/posts/${postId}/comments/${commentId}`);
    return response.data;
  }

  // 댓글 좋아요
  async likeComment(postId: number, commentId: number) {
    const response = await post(`/api/community/posts/${postId}/comments/${commentId}/like`);
    return response.data;
  }

  // 댓글 좋아요 취소
  async unlikeComment(postId: number, commentId: number) {
    const response = await del(`/api/community/posts/${postId}/comments/${commentId}/like`);
    return response.data;
  }

  // 카테고리 목록 조회
  async getCategories() {
    const response = await get('/api/community/categories');
    return response.data;
  }
}

export default new CommunityService();