import React, { useState, useEffect } from 'react';
import { useParams, useNavigate, Link } from 'react-router-dom';
import { Post, Comment, CreateCommentRequest } from '../../types';
import { useAuth } from '../../context/AuthContext';
import { useToast } from '../../context/ToastContext';
import { useApi } from '../../hooks/useApi';
import Button from '../../components/common/Button';
import LoadingSpinner from '../../components/common/LoadingSpinner';

const PostDetailPage: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const { user, isAuthenticated } = useAuth();
  const { success, error } = useToast();
  const { execute } = useApi(async (data: any) => data);

  const [post, setPost] = useState<Post | null>(null);
  const [comments, setComments] = useState<Comment[]>([]);
  const [loading, setLoading] = useState(true);
  const [isLiked, setIsLiked] = useState(false);
  const [likesCount, setLikesCount] = useState(0);
  const [commentText, setCommentText] = useState('');
  const [submittingComment, setSubmittingComment] = useState(false);
  const [replyingTo, setReplyingTo] = useState<number | null>(null);
  const [replyText, setReplyText] = useState('');

  // 게시글 상세 정보 조회
  useEffect(() => {
    const fetchPost = async () => {
      if (!id) return;

      setLoading(true);
      try {
        const response = await execute({
          url: `/posts/${id}`,
          method: 'GET',
        });

        if (response.success && response.data) {
          setPost(response.data);
          setIsLiked(response.data.is_liked || false);
          setLikesCount(response.data.likes_count);
          setComments(response.data.comments || []);
        }
      } catch (err) {
        console.error('게시글 조회 실패:', err);
        error('게시글을 불러올 수 없습니다.');
        navigate('/community');
      } finally {
        setLoading(false);
      }
    };

    fetchPost();
  }, [id]);

  // 좋아요 토글
  const handleLike = async () => {
    if (!isAuthenticated) {
      error('로그인이 필요한 서비스입니다.');
      return;
    }

    if (!post) return;

    try {
      const response = await execute({
        url: `/posts/${post.id}/like`,
        method: isLiked ? 'DELETE' : 'POST',
      });

      if (response.success) {
        setIsLiked(!isLiked);
        setLikesCount(prev => isLiked ? prev - 1 : prev + 1);
      }
    } catch (err) {
      console.error('좋아요 처리 실패:', err);
    }
  };

  // 댓글 작성
  const handleCommentSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!isAuthenticated) {
      error('로그인이 필요한 서비스입니다.');
      return;
    }

    if (!post || !commentText.trim()) return;

    setSubmittingComment(true);
    try {
      const commentData: CreateCommentRequest = {
        post_id: post.id,
        content: commentText.trim(),
      };

      const response = await execute({
        url: '/comments',
        method: 'POST',
        data: commentData,
      });

      if (response.success && response.data) {
        setComments(prev => [...prev, response.data]);
        setCommentText('');
        success('댓글이 등록되었습니다.');
        
        // 게시글의 댓글 수 업데이트
        setPost(prev => prev ? { ...prev, comments_count: prev.comments_count + 1 } : null);
      }
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : '댓글 등록에 실패했습니다.';
      error(errorMessage);
    } finally {
      setSubmittingComment(false);
    }
  };

  // 답글 작성
  const handleReplySubmit = async (parentId: number) => {
    if (!isAuthenticated) {
      error('로그인이 필요한 서비스입니다.');
      return;
    }

    if (!post || !replyText.trim()) return;

    setSubmittingComment(true);
    try {
      const replyData: CreateCommentRequest = {
        post_id: post.id,
        parent_id: parentId,
        content: replyText.trim(),
      };

      const response = await execute({
        url: '/comments',
        method: 'POST',
        data: replyData,
      });

      if (response.success && response.data) {
        // 댓글 목록 업데이트 - 부모 댓글에 답글 추가
        setComments(prev => prev.map(comment => {
          if (comment.id === parentId) {
            return {
              ...comment,
              replies: [...(comment.replies || []), response.data]
            };
          }
          return comment;
        }));
        
        setReplyText('');
        setReplyingTo(null);
        success('답글이 등록되었습니다.');
      }
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : '답글 등록에 실패했습니다.';
      error(errorMessage);
    } finally {
      setSubmittingComment(false);
    }
  };

  // 게시글 삭제
  const handleDeletePost = async () => {
    if (!post || !window.confirm('정말로 이 게시글을 삭제하시겠습니까?')) return;

    try {
      const response = await execute({
        url: `/posts/${post.id}`,
        method: 'DELETE',
      });

      if (response.success) {
        success('게시글이 삭제되었습니다.');
        navigate('/community');
      }
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : '게시글 삭제에 실패했습니다.';
      error(errorMessage);
    }
  };

  // 날짜 포맷팅
  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('ko-KR', {
      year: 'numeric',
      month: 'long',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <LoadingSpinner size="lg" message="게시글을 불러오는 중..." />
      </div>
    );
  }

  if (!post) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <div className="text-center">
          <h2 className="text-2xl font-bold text-gray-900 mb-4">게시글을 찾을 수 없습니다</h2>
          <Link to="/community">
            <Button>커뮤니티로 돌아가기</Button>
          </Link>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50">
      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* 네비게이션 */}
        <div className="mb-6">
          <Link
            to="/community"
            className="inline-flex items-center text-blue-600 hover:text-blue-700"
          >
            <svg className="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
            </svg>
            커뮤니티로 돌아가기
          </Link>
        </div>

        {/* 게시글 내용 */}
        <div className="bg-white rounded-xl shadow-sm p-8 mb-6">
          {/* 게시글 헤더 */}
          <div className="border-b border-gray-200 pb-6 mb-6">
            <h1 className="text-3xl font-bold text-gray-900 mb-4">
              {post.title}
            </h1>
            
            <div className="flex items-center justify-between">
              <div className="flex items-center">
                <div className="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center mr-4">
                  {post.user.profile_image ? (
                    <img
                      src={post.user.profile_image}
                      alt={post.user.nickname}
                      className="w-12 h-12 rounded-full object-cover"
                    />
                  ) : (
                    <svg className="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                  )}
                </div>
                <div>
                  <div className="font-semibold text-gray-900">
                    {post.user.nickname}
                  </div>
                  <div className="text-sm text-gray-600">
                    {formatDate(post.created_at)}
                  </div>
                </div>
              </div>

              {/* 게시글 관리 버튼 (작성자만) */}
              {user && user.id === post.user.id && (
                <div className="flex space-x-2">
                  <Link to={`/community/${post.id}/edit`}>
                    <Button variant="outline" size="sm">
                      수정
                    </Button>
                  </Link>
                  <Button
                    variant="outline"
                    size="sm"
                    onClick={handleDeletePost}
                    className="text-red-600 border-red-200 hover:bg-red-50"
                  >
                    삭제
                  </Button>
                </div>
              )}
            </div>
          </div>

          {/* 게시글 본문 */}
          <div 
            className="prose max-w-none mb-6"
            dangerouslySetInnerHTML={{ __html: post.content }}
          />

          {/* 통계 및 액션 */}
          <div className="flex items-center justify-between pt-6 border-t border-gray-200">
            <div className="flex items-center space-x-6 text-sm text-gray-600">
              <span className="flex items-center">
                <svg className="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                조회 {post.views.toLocaleString()}
              </span>
              <span className="flex items-center">
                <svg className="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                좋아요 {likesCount.toLocaleString()}
              </span>
              <span className="flex items-center">
                <svg className="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                댓글 {post.comments_count.toLocaleString()}
              </span>
            </div>

            <div className="flex space-x-3">
              <Button
                onClick={handleLike}
                variant={isLiked ? 'primary' : 'outline'}
                leftIcon={
                  <svg className="w-5 h-5" fill={isLiked ? 'currentColor' : 'none'} viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                  </svg>
                }
              >
                {isLiked ? '좋아요 취소' : '좋아요'}
              </Button>
              
              <Button
                variant="outline"
                leftIcon={
                  <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z" />
                  </svg>
                }
              >
                공유하기
              </Button>
            </div>
          </div>
        </div>

        {/* 댓글 섹션 */}
        <div className="bg-white rounded-xl shadow-sm p-6">
          <h2 className="text-2xl font-bold text-gray-900 mb-6">
            댓글 {comments.length.toLocaleString()}개
          </h2>

          {/* 댓글 작성 폼 */}
          {isAuthenticated ? (
            <form onSubmit={handleCommentSubmit} className="mb-8">
              <div className="mb-4">
                <textarea
                  value={commentText}
                  onChange={(e) => setCommentText(e.target.value)}
                  placeholder="댓글을 작성해주세요..."
                  rows={4}
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                />
              </div>
              <div className="flex justify-between items-center">
                <span className="text-sm text-gray-500">
                  {commentText.length}/500
                </span>
                <Button
                  type="submit"
                  loading={submittingComment}
                  disabled={!commentText.trim() || commentText.length > 500}
                >
                  댓글 등록
                </Button>
              </div>
            </form>
          ) : (
            <div className="mb-8 p-4 bg-gray-50 rounded-lg text-center">
              <p className="text-gray-600 mb-4">댓글을 작성하려면 로그인이 필요합니다.</p>
              <Link to="/auth/login">
                <Button>로그인하기</Button>
              </Link>
            </div>
          )}

          {/* 댓글 목록 */}
          <div className="space-y-6">
            {comments.map((comment) => (
              <div key={comment.id} className="border-b border-gray-100 pb-6 last:border-b-0">
                {/* 댓글 */}
                <div className="flex items-start space-x-3">
                  <div className="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center">
                    {comment.user.profile_image ? (
                      <img
                        src={comment.user.profile_image}
                        alt={comment.user.nickname}
                        className="w-10 h-10 rounded-full object-cover"
                      />
                    ) : (
                      <svg className="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                      </svg>
                    )}
                  </div>
                  <div className="flex-1">
                    <div className="flex items-center space-x-2 mb-1">
                      <span className="font-medium text-gray-900">
                        {comment.user.nickname}
                      </span>
                      <span className="text-sm text-gray-500">
                        {formatDate(comment.created_at)}
                      </span>
                    </div>
                    <p className="text-gray-700 mb-2">
                      {comment.content}
                    </p>
                    <div className="flex items-center space-x-4">
                      <button
                        onClick={() => setReplyingTo(replyingTo === comment.id ? null : comment.id)}
                        className="text-sm text-blue-600 hover:text-blue-700"
                      >
                        답글
                      </button>
                      {comment.replies && comment.replies.length > 0 && (
                        <span className="text-sm text-gray-500">
                          답글 {comment.replies.length}개
                        </span>
                      )}
                    </div>

                    {/* 답글 작성 폼 */}
                    {replyingTo === comment.id && isAuthenticated && (
                      <div className="mt-4 pl-4 border-l-2 border-gray-200">
                        <textarea
                          value={replyText}
                          onChange={(e) => setReplyText(e.target.value)}
                          placeholder="답글을 작성해주세요..."
                          rows={3}
                          className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none mb-2"
                        />
                        <div className="flex justify-end space-x-2">
                          <Button
                            variant="ghost"
                            size="sm"
                            onClick={() => {
                              setReplyingTo(null);
                              setReplyText('');
                            }}
                          >
                            취소
                          </Button>
                          <Button
                            size="sm"
                            onClick={() => handleReplySubmit(comment.id)}
                            loading={submittingComment}
                            disabled={!replyText.trim()}
                          >
                            답글 등록
                          </Button>
                        </div>
                      </div>
                    )}

                    {/* 답글 목록 */}
                    {comment.replies && comment.replies.length > 0 && (
                      <div className="mt-4 pl-4 border-l-2 border-gray-200 space-y-4">
                        {comment.replies.map((reply) => (
                          <div key={reply.id} className="flex items-start space-x-3">
                            <div className="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                              {reply.user.profile_image ? (
                                <img
                                  src={reply.user.profile_image}
                                  alt={reply.user.nickname}
                                  className="w-8 h-8 rounded-full object-cover"
                                />
                              ) : (
                                <svg className="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                              )}
                            </div>
                            <div className="flex-1">
                              <div className="flex items-center space-x-2 mb-1">
                                <span className="font-medium text-gray-900">
                                  {reply.user.nickname}
                                </span>
                                <span className="text-sm text-gray-500">
                                  {formatDate(reply.created_at)}
                                </span>
                              </div>
                              <p className="text-gray-700">
                                {reply.content}
                              </p>
                            </div>
                          </div>
                        ))}
                      </div>
                    )}
                  </div>
                </div>
              </div>
            ))}
          </div>

          {comments.length === 0 && (
            <div className="text-center py-8">
              <div className="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg className="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
              </div>
              <h3 className="text-lg font-medium text-gray-900 mb-2">
                아직 댓글이 없습니다
              </h3>
              <p className="text-gray-600">
                첫 번째 댓글을 작성해보세요!
              </p>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default PostDetailPage;