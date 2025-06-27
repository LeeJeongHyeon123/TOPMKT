import React, { useState, useEffect } from 'react';
import { useParams, useNavigate, Link } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import CommunityService, { CommunityPost, CommunityComment } from '../../services/CommunityService';
import '../../assets/css/community.css';

const PostDetailPage: React.FC = () => {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const { isAuthenticated } = useAuth();

  const [post, setPost] = useState<CommunityPost | null>(null);
  const [comments, setComments] = useState<CommunityComment[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [userPermissions, setUserPermissions] = useState<any>({});
  const [userActions, setUserActions] = useState<any>({});
  const [commentText, setCommentText] = useState('');
  const [submittingComment, setSubmittingComment] = useState(false);
  const [replyingTo, setReplyingTo] = useState<number | null>(null);
  const [replyText, setReplyText] = useState('');

  // 게시글 상세 정보 조회
  useEffect(() => {
    const fetchPost = async () => {
      if (!id) return;

      setLoading(true);
      setError(null);
      
      try {
        const response = await CommunityService.getPost(parseInt(id));

        if (response.success && response.data) {
          setPost(response.data.post);
          setUserPermissions(response.data.user_permissions || {});
          setUserActions(response.data.user_actions || {});
          
          // 댓글 로드
          const commentsResponse = await CommunityService.getComments(parseInt(id));
          if (commentsResponse.success && commentsResponse.data) {
            setComments(commentsResponse.data.comments || []);
          }
        } else {
          setError('게시글을 찾을 수 없습니다.');
        }
      } catch (err: any) {
        console.error('게시글 조회 실패:', err);
        setError(err.response?.data?.message || '게시글을 불러오는데 실패했습니다.');
      } finally {
        setLoading(false);
      }
    };

    fetchPost();
  }, [id]);

  // 좋아요 토글
  const handleLike = async () => {
    if (!isAuthenticated) {
      alert('로그인이 필요한 서비스입니다.');
      return;
    }

    if (!post) return;

    try {
      const response = userActions.is_liked 
        ? await CommunityService.unlikePost(post.id)
        : await CommunityService.likePost(post.id);

      if (response.success) {
        setUserActions((prev: any) => ({ ...prev, is_liked: !prev.is_liked }));
        setPost((prev: CommunityPost | null) => prev ? {
          ...prev,
          like_count: prev.like_count + (userActions.is_liked ? -1 : 1)
        } : null);
      }
    } catch (err: any) {
      console.error('좋아요 처리 실패:', err);
      alert('좋아요 처리 중 오류가 발생했습니다.');
    }
  };

  // 댓글 작성
  const handleCommentSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    
    if (!isAuthenticated) {
      alert('로그인이 필요한 서비스입니다.');
      return;
    }

    if (!post || !commentText.trim()) return;

    setSubmittingComment(true);
    try {
      const response = await CommunityService.createComment(post.id, {
        content: commentText.trim()
      });

      if (response.success && response.data) {
        setComments(prev => [...prev, response.data]);
        setCommentText('');
        alert('댓글이 등록되었습니다.');
        
        // 게시글의 댓글 수 업데이트
        setPost(prev => prev ? { ...prev, comment_count: prev.comment_count + 1 } : null);
      }
    } catch (err: any) {
      console.error('댓글 등록 실패:', err);
      alert(err.response?.data?.message || '댓글 등록에 실패했습니다.');
    } finally {
      setSubmittingComment(false);
    }
  };

  // 답글 작성
  const handleReplySubmit = async (parentId: number) => {
    if (!isAuthenticated) {
      alert('로그인이 필요한 서비스입니다.');
      return;
    }

    if (!post || !replyText.trim()) return;

    setSubmittingComment(true);
    try {
      const response = await CommunityService.createComment(post.id, {
        content: replyText.trim(),
        parent_id: parentId
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
        alert('답글이 등록되었습니다.');
      }
    } catch (err: any) {
      console.error('답글 등록 실패:', err);
      alert(err.response?.data?.message || '답글 등록에 실패했습니다.');
    } finally {
      setSubmittingComment(false);
    }
  };

  // 게시글 삭제
  const handleDeletePost = async () => {
    if (!post || !window.confirm('정말로 이 게시글을 삭제하시겠습니까?')) return;

    try {
      const response = await CommunityService.deletePost(post.id);

      if (response.success) {
        alert('게시글이 삭제되었습니다.');
        navigate('/community');
      }
    } catch (err: any) {
      console.error('게시글 삭제 실패:', err);
      alert(err.response?.data?.message || '게시글 삭제에 실패했습니다.');
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
      <div className="loading-container">
        <div className="loading-content">
          <div className="loading-icon">
            <div className="rocket-main">🚀</div>
            <div className="loading-spinner"></div>
          </div>
          <p>게시글을 불러오는 중...</p>
        </div>
      </div>
    );
  }

  if (error || !post) {
    return (
      <div className="error-container">
        <div className="error-content">
          <h2>게시글을 찾을 수 없습니다</h2>
          <p>{error || '요청하신 게시글이 존재하지 않거나 삭제되었습니다.'}</p>
          <Link to="/community" className="back-button">
            커뮤니티로 돌아가기
          </Link>
        </div>
      </div>
    );
  }

  return (
    <div className="post-detail-container">
      {/* 플로팅 백 버튼 */}
      <Link to="/community" className="floating-back-btn">
        <i className="fas fa-arrow-left"></i>
      </Link>

      {/* 게시글 헤더 */}
      <div className="post-header">
        <div className="post-header-content">
          <h1 className="post-title">{post.title}</h1>
          <div className="post-meta">
            <div className="author-info">
              <div className="author-avatar">
                {post.profile_image ? (
                  <img
                    src={`https://www.topmktx.com${post.profile_image}`}
                    alt={post.author_name}
                    onError={(e) => {
                      e.currentTarget.style.display = 'none';
                      const nextEl = e.currentTarget.nextElementSibling as HTMLElement;
                      if (nextEl) nextEl.style.display = 'flex';
                    }}
                  />
                ) : null}
                <div className="author-initial" style={post.profile_image ? { display: 'none' } : {}}>
                  {post.author_name.charAt(0)}
                </div>
              </div>
              <div className="author-details">
                <span className="author-name">{post.author_name}</span>
                <span className="post-date">{formatDate(post.created_at)}</span>
              </div>
            </div>
            <div className="post-actions">
              {userPermissions.can_edit && (
                <Link to={`/community/${post.id}/edit`} className="btn btn-edit">
                  <i className="fas fa-edit"></i> 수정
                </Link>
              )}
              {userPermissions.can_delete && (
                <button onClick={handleDeletePost} className="btn btn-delete">
                  <i className="fas fa-trash"></i> 삭제
                </button>
              )}
            </div>
          </div>
        </div>
      </div>

      {/* 게시글 본문 */}
      <div className="post-content">
        <div className="content-body" dangerouslySetInnerHTML={{ __html: post.content }} />
        
        {/* 게시글 하단 정보 */}
        <div className="post-footer">
          <div className="post-stats">
            <span className="stat-item">
              <i className="fas fa-eye"></i>
              조회 {post.view_count.toLocaleString()}
            </span>
            <span className="stat-item">
              <i className="fas fa-heart"></i>
              좋아요 {post.like_count.toLocaleString()}
            </span>
            <span className="stat-item">
              <i className="fas fa-comments"></i>
              댓글 {post.comment_count.toLocaleString()}
            </span>
          </div>
          
          <div className="post-actions-bottom">
            <button
              onClick={handleLike}
              className={`like-btn ${userActions.is_liked ? 'liked' : ''}`}
            >
              {userActions.is_liked ? '❤️' : '🤍'} 좋아요 {post.like_count}
            </button>
            
            <button className="share-btn">
              <i className="fas fa-share"></i> 공유하기
            </button>
          </div>
        </div>
      </div>

      {/* 댓글 섹션 */}
      <div className="comments-section">
        <div className="comments-header">
          <h3>댓글 {comments.length}개</h3>
        </div>

        {/* 댓글 작성 폼 */}
        {isAuthenticated ? (
          <form onSubmit={handleCommentSubmit} className="comment-form">
            <div className="form-group">
              <textarea
                value={commentText}
                onChange={(e) => setCommentText(e.target.value)}
                placeholder="댓글을 작성해주세요..."
                rows={4}
                maxLength={500}
                className="comment-textarea"
              />
            </div>
            <div className="form-footer">
              <span className="char-count">
                {commentText.length}/500
              </span>
              <button
                type="submit"
                disabled={!commentText.trim() || commentText.length > 500 || submittingComment}
                className="submit-btn"
              >
                {submittingComment ? '등록중...' : '댓글 등록'}
              </button>
            </div>
          </form>
        ) : (
          <div className="login-required">
            <p>댓글을 작성하려면 로그인이 필요합니다.</p>
            <Link to="/auth/login" className="login-btn">
              로그인하기
            </Link>
          </div>
        )}

        {/* 댓글 목록 */}
        <div className="comments-list">
          {comments.map((comment) => (
            <div key={comment.id} className="comment-item">
              <div className="comment-content">
                <div className="comment-avatar">
                  {comment.author_profile_image ? (
                    <img
                      src={`https://www.topmktx.com${comment.author_profile_image}`}
                      alt={comment.author_name}
                      onError={(e) => {
                        e.currentTarget.style.display = 'none';
                        const nextEl = e.currentTarget.nextElementSibling as HTMLElement;
                        if (nextEl) nextEl.style.display = 'flex';
                      }}
                    />
                  ) : null}
                  <div className="comment-initial" style={comment.author_profile_image ? { display: 'none' } : {}}>
                    {comment.author_name.charAt(0)}
                  </div>
                </div>
                <div className="comment-body">
                  <div className="comment-header">
                    <span className="comment-author">{comment.author_name}</span>
                    <span className="comment-date">{formatDate(comment.created_at)}</span>
                  </div>
                  <div className="comment-text">{comment.content}</div>
                  <div className="comment-actions">
                    <button
                      onClick={() => setReplyingTo(replyingTo === comment.id ? null : comment.id)}
                      className="reply-btn"
                    >
                      답글
                    </button>
                    {comment.replies && comment.replies.length > 0 && (
                      <span className="reply-count">답글 {comment.replies.length}개</span>
                    )}
                  </div>

                  {/* 답글 작성 폼 */}
                  {replyingTo === comment.id && isAuthenticated && (
                    <div className="reply-form">
                      <textarea
                        value={replyText}
                        onChange={(e) => setReplyText(e.target.value)}
                        placeholder="답글을 작성해주세요..."
                        rows={3}
                        className="reply-textarea"
                      />
                      <div className="reply-form-actions">
                        <button
                          onClick={() => {
                            setReplyingTo(null);
                            setReplyText('');
                          }}
                          className="cancel-btn"
                        >
                          취소
                        </button>
                        <button
                          onClick={() => handleReplySubmit(comment.id)}
                          disabled={!replyText.trim() || submittingComment}
                          className="submit-btn"
                        >
                          {submittingComment ? '등록중...' : '답글 등록'}
                        </button>
                      </div>
                    </div>
                  )}

                  {/* 답글 목록 */}
                  {comment.replies && comment.replies.length > 0 && (
                    <div className="replies-list">
                      {comment.replies.map((reply) => (
                        <div key={reply.id} className="reply-item">
                          <div className="reply-avatar">
                            {reply.author_profile_image ? (
                              <img
                                src={`https://www.topmktx.com${reply.author_profile_image}`}
                                alt={reply.author_name}
                                onError={(e) => {
                                  e.currentTarget.style.display = 'none';
                                  const nextEl = e.currentTarget.nextElementSibling as HTMLElement;
                                  if (nextEl) nextEl.style.display = 'flex';
                                }}
                              />
                            ) : null}
                            <div className="reply-initial" style={reply.author_profile_image ? { display: 'none' } : {}}>
                              {reply.author_name.charAt(0)}
                            </div>
                          </div>
                          <div className="reply-body">
                            <div className="reply-header">
                              <span className="reply-author">{reply.author_name}</span>
                              <span className="reply-date">{formatDate(reply.created_at)}</span>
                            </div>
                            <div className="reply-text">{reply.content}</div>
                          </div>
                        </div>
                      ))}
                    </div>
                  )}
                </div>
              </div>
            </div>
          ))}
          
          {comments.length === 0 && (
            <div className="no-comments">
              <div className="no-comments-icon">
                <i className="fas fa-comments"></i>
              </div>
              <h3>아직 댓글이 없습니다</h3>
              <p>첫 번째 댓글을 작성해보세요!</p>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default PostDetailPage;