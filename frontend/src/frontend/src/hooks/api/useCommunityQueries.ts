import { useQuery, useInfiniteQuery, useMutation, useQueryClient } from '@tanstack/react-query';
import CommunityService, { CommunityFilters } from '../../services/CommunityService';

// Query Keys 상수 정의
export const COMMUNITY_QUERY_KEYS = {
  all: ['community'] as const,
  posts: () => [...COMMUNITY_QUERY_KEYS.all, 'posts'] as const,
  post: (id: number) => [...COMMUNITY_QUERY_KEYS.all, 'post', id] as const,
  postsList: (filters: CommunityFilters) => [...COMMUNITY_QUERY_KEYS.posts(), 'list', filters] as const,
  comments: (postId: number) => [...COMMUNITY_QUERY_KEYS.all, 'comments', postId] as const,
  categories: () => [...COMMUNITY_QUERY_KEYS.all, 'categories'] as const,
} as const;

// 무한 스크롤을 위한 게시글 목록 훅
export const useInfinitePosts = (filters: Omit<CommunityFilters, 'page'> = {}) => {
  return useInfiniteQuery({
    queryKey: COMMUNITY_QUERY_KEYS.postsList(filters),
    queryFn: async ({ pageParam = 1 }) => {
      const result = await CommunityService.getPosts({
        ...filters,
        page: pageParam,
        limit: 20,
      });
      
      if (!result.success || !result.data) {
        throw new Error(result.message || '게시글을 불러오는데 실패했습니다.');
      }
      
      return {
        posts: result.data.posts || [],
        pagination: result.data.pagination || {
          current_page: pageParam,
          total_pages: 1,
          total_count: 0,
          has_next: false,
          has_prev: pageParam > 1,
        },
      };
    },
    getNextPageParam: (lastPage) => {
      const { pagination } = lastPage;
      return pagination.has_next ? pagination.current_page + 1 : undefined;
    },
    getPreviousPageParam: (firstPage) => {
      const { pagination } = firstPage;
      return pagination.has_prev ? pagination.current_page - 1 : undefined;
    },
    initialPageParam: 1,
    // 검색어나 필터가 변경될 때만 새로 요청
    staleTime: filters.search ? 0 : 5 * 60 * 1000, // 검색 시에는 즉시 갱신
    gcTime: 10 * 60 * 1000,
  });
};

// 단일 게시글 조회 훅
export const usePost = (id: number, enabled: boolean = true) => {
  return useQuery({
    queryKey: COMMUNITY_QUERY_KEYS.post(id),
    queryFn: async () => {
      const result = await CommunityService.getPost(id);
      
      if (!result.success || !result.data) {
        throw new Error(result.message || '게시글을 불러오는데 실패했습니다.');
      }
      
      return result.data;
    },
    enabled,
    staleTime: 3 * 60 * 1000, // 3분
    gcTime: 10 * 60 * 1000,
  });
};

// 댓글 목록 조회 훅
export const useComments = (postId: number, enabled: boolean = true) => {
  return useQuery({
    queryKey: COMMUNITY_QUERY_KEYS.comments(postId),
    queryFn: async () => {
      const result = await CommunityService.getComments(postId);
      
      if (!result.success || !result.data) {
        throw new Error(result.message || '댓글을 불러오는데 실패했습니다.');
      }
      
      return result.data;
    },
    enabled,
    staleTime: 2 * 60 * 1000, // 2분
    gcTime: 5 * 60 * 1000,
  });
};

// 카테고리 목록 조회 훅
export const useCategories = () => {
  return useQuery({
    queryKey: COMMUNITY_QUERY_KEYS.categories(),
    queryFn: async () => {
      const result = await CommunityService.getCategories();
      
      if (!result.success || !result.data) {
        throw new Error(result.message || '카테고리를 불러오는데 실패했습니다.');
      }
      
      return result.data;
    },
    staleTime: 30 * 60 * 1000, // 30분 (카테고리는 자주 변경되지 않음)
    gcTime: 60 * 60 * 1000, // 1시간
  });
};

// 게시글 생성 뮤테이션
export const useCreatePost = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: (data: {
      title: string;
      content: string;
      category?: string;
      tags?: string[];
    }) => CommunityService.createPost(data),
    onSuccess: (result) => {
      // 게시글 목록 캐시 무효화
      queryClient.invalidateQueries({
        queryKey: COMMUNITY_QUERY_KEYS.posts(),
      });
      
      // 새 게시글을 캐시에 추가
      if (result.success && result.data) {
        queryClient.setQueryData(
          COMMUNITY_QUERY_KEYS.post(result.data.id),
          result.data
        );
      }
    },
    onError: (error) => {
      console.error('게시글 생성 실패:', error);
    },
  });
};

// 게시글 수정 뮤테이션
export const useUpdatePost = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: ({ id, data }: {
      id: number;
      data: {
        title?: string;
        content?: string;
        category?: string;
        tags?: string[];
      };
    }) => CommunityService.updatePost(id, data),
    onSuccess: (_, variables) => {
      // 해당 게시글 캐시 무효화
      queryClient.invalidateQueries({
        queryKey: COMMUNITY_QUERY_KEYS.post(variables.id),
      });
      
      // 게시글 목록 캐시 무효화
      queryClient.invalidateQueries({
        queryKey: COMMUNITY_QUERY_KEYS.posts(),
      });
    },
    onError: (error) => {
      console.error('게시글 수정 실패:', error);
    },
  });
};

// 게시글 삭제 뮤테이션
export const useDeletePost = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: (id: number) => CommunityService.deletePost(id),
    onSuccess: (_, id) => {
      // 해당 게시글 캐시 제거
      queryClient.removeQueries({
        queryKey: COMMUNITY_QUERY_KEYS.post(id),
      });
      
      // 게시글 목록 캐시 무효화
      queryClient.invalidateQueries({
        queryKey: COMMUNITY_QUERY_KEYS.posts(),
      });
      
      // 댓글 캐시 제거
      queryClient.removeQueries({
        queryKey: COMMUNITY_QUERY_KEYS.comments(id),
      });
    },
    onError: (error) => {
      console.error('게시글 삭제 실패:', error);
    },
  });
};

// 좋아요 토글 뮤테이션
export const useToggleLike = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: ({ id, isLiked }: { id: number; isLiked: boolean }) => {
      return isLiked 
        ? CommunityService.unlikePost(id)
        : CommunityService.likePost(id);
    },
    onMutate: async ({ id, isLiked }) => {
      // 낙관적 업데이트
      await queryClient.cancelQueries({
        queryKey: COMMUNITY_QUERY_KEYS.post(id),
      });

      const previousPost = queryClient.getQueryData(COMMUNITY_QUERY_KEYS.post(id));

      // 캐시 데이터 임시 업데이트
      queryClient.setQueryData(COMMUNITY_QUERY_KEYS.post(id), (old: any) => {
        if (!old) return old;
        return {
          ...old,
          is_liked: !isLiked,
          like_count: old.like_count + (isLiked ? -1 : 1),
        };
      });

      return { previousPost };
    },
    onError: (_, variables, context) => {
      // 에러 시 이전 데이터로 롤백
      if (context?.previousPost) {
        queryClient.setQueryData(
          COMMUNITY_QUERY_KEYS.post(variables.id),
          context.previousPost
        );
      }
    },
    onSettled: (_, __, variables) => {
      // 최종적으로 서버 데이터로 동기화
      queryClient.invalidateQueries({
        queryKey: COMMUNITY_QUERY_KEYS.post(variables.id),
      });
    },
  });
};

// 댓글 작성 뮤테이션
export const useCreateComment = () => {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: ({ postId, data }: {
      postId: number;
      data: {
        content: string;
        parent_id?: number;
      };
    }) => CommunityService.createComment(postId, data),
    onSuccess: (_, variables) => {
      // 댓글 목록 캐시 무효화
      queryClient.invalidateQueries({
        queryKey: COMMUNITY_QUERY_KEYS.comments(variables.postId),
      });
      
      // 게시글의 댓글 수 증가 (낙관적 업데이트)
      queryClient.setQueryData(
        COMMUNITY_QUERY_KEYS.post(variables.postId),
        (old: any) => {
          if (!old) return old;
          return {
            ...old,
            comment_count: old.comment_count + 1,
          };
        }
      );
    },
    onError: (error) => {
      console.error('댓글 작성 실패:', error);
    },
  });
};