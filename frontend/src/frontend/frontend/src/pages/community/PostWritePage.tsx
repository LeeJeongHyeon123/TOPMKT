import React, { useState, useEffect } from 'react';
import { useNavigate, useParams, Link } from 'react-router-dom';
import { Post, CreatePostRequest, UpdatePostRequest } from '../../types';
import { useAuth } from '../../context/AuthContext';
import { useToast } from '../../context/ToastContext';
import { useApi } from '../../hooks/useApi';
import Button from '../../components/common/Button';
import Input from '../../components/common/Input';
import LoadingSpinner from '../../components/common/LoadingSpinner';

const PostWritePage: React.FC = () => {
  const { id } = useParams<{ id?: string }>();
  const navigate = useNavigate();
  const { user, isAuthenticated } = useAuth();
  const { success, error } = useToast();
  const { request } = useApi();

  const [loading, setLoading] = useState(!!id); // 수정 모드일 때만 로딩
  const [submitting, setSubmitting] = useState(false);
  const [formData, setFormData] = useState({
    title: '',
    content: '',
  });
  const [errors, setErrors] = useState<Record<string, string>>({});

  const isEditMode = !!id;

  // 로그인 체크
  useEffect(() => {
    if (!isAuthenticated) {
      error('로그인이 필요한 서비스입니다.');
      navigate('/auth/login', { state: { from: location.pathname } });
    }
  }, [isAuthenticated]);

  // 수정 모드일 때 기존 게시글 데이터 로드
  useEffect(() => {
    if (isEditMode && id) {
      const fetchPost = async () => {
        setLoading(true);
        try {
          const response = await request<Post>({
            url: `/posts/${id}`,
            method: 'GET',
          });

          if (response.success && response.data) {
            const post = response.data;
            
            // 작성자 확인
            if (user && user.id !== post.user.id) {
              error('게시글을 수정할 권한이 없습니다.');
              navigate('/community');
              return;
            }

            setFormData({
              title: post.title,
              content: post.content,
            });
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
    }
  }, [isEditMode, id, user]);

  // 폼 유효성 검사
  const validateForm = () => {
    const newErrors: Record<string, string> = {};

    if (!formData.title.trim()) {
      newErrors.title = '제목을 입력해주세요.';
    } else if (formData.title.length < 2) {
      newErrors.title = '제목은 2자 이상 입력해주세요.';
    } else if (formData.title.length > 200) {
      newErrors.title = '제목은 200자 이하로 입력해주세요.';
    }

    if (!formData.content.trim()) {
      newErrors.content = '내용을 입력해주세요.';
    } else if (formData.content.length < 10) {
      newErrors.content = '내용은 10자 이상 입력해주세요.';
    } else if (formData.content.length > 10000) {
      newErrors.content = '내용은 10,000자 이하로 입력해주세요.';
    }

    setErrors(newErrors);
    return Object.keys(newErrors).length === 0;
  };

  // 입력 변경 처리
  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
    const { name, value } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: value
    }));

    // 에러 메시지 클리어
    if (errors[name]) {
      setErrors(prev => ({
        ...prev,
        [name]: ''
      }));
    }
  };

  // 폼 제출
  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();

    if (!validateForm()) {
      return;
    }

    setSubmitting(true);
    try {
      if (isEditMode && id) {
        // 게시글 수정
        const updateData: UpdatePostRequest = {
          title: formData.title.trim(),
          content: formData.content.trim(),
        };

        const response = await request<Post>({
          url: `/posts/${id}`,
          method: 'PUT',
          data: updateData,
        });

        if (response.success) {
          success('게시글이 수정되었습니다.');
          navigate(`/community/${id}`);
        }
      } else {
        // 새 게시글 작성
        const createData: CreatePostRequest = {
          title: formData.title.trim(),
          content: formData.content.trim(),
        };

        const response = await request<Post>({
          url: '/posts',
          method: 'POST',
          data: createData,
        });

        if (response.success && response.data) {
          success('게시글이 등록되었습니다.');
          navigate(`/community/${response.data.id}`);
        }
      }
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : 
        isEditMode ? '게시글 수정에 실패했습니다.' : '게시글 등록에 실패했습니다.';
      error(errorMessage);
    } finally {
      setSubmitting(false);
    }
  };

  // 임시저장 (향후 구현)
  const handleSaveDraft = async () => {
    if (!formData.title.trim() && !formData.content.trim()) {
      error('저장할 내용이 없습니다.');
      return;
    }

    // TODO: 임시저장 API 구현
    success('임시저장되었습니다.');
  };

  if (loading) {
    return (
      <div className="min-h-screen flex items-center justify-center">
        <LoadingSpinner size="lg" message="게시글 정보를 불러오는 중..." />
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

        {/* 헤더 */}
        <div className="bg-white rounded-xl shadow-sm p-6 mb-6">
          <h1 className="text-3xl font-bold text-gray-900">
            {isEditMode ? '게시글 수정' : '새 게시글 작성'}
          </h1>
          <p className="text-gray-600 mt-2">
            {isEditMode 
              ? '게시글을 수정하여 더 나은 내용으로 업데이트하세요.'
              : '다른 회원들과 소중한 경험과 지식을 공유해보세요.'
            }
          </p>
        </div>

        {/* 게시글 작성 폼 */}
        <form onSubmit={handleSubmit} className="bg-white rounded-xl shadow-sm p-6">
          <div className="space-y-6">
            {/* 제목 입력 */}
            <div>
              <Input
                label="제목"
                name="title"
                value={formData.title}
                onChange={handleInputChange}
                error={errors.title}
                placeholder="게시글 제목을 입력해주세요"
                required
                fullWidth
                maxLength={200}
                hint={`${formData.title.length}/200`}
              />
            </div>

            {/* 내용 입력 */}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">
                내용 <span className="text-red-500">*</span>
              </label>
              <textarea
                name="content"
                value={formData.content}
                onChange={handleInputChange}
                placeholder="내용을 입력해주세요. 마크다운 문법을 사용할 수 있습니다."
                rows={15}
                className={`w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none ${
                  errors.content ? 'border-red-300' : 'border-gray-300'
                }`}
                maxLength={10000}
              />
              <div className="flex justify-between items-center mt-2">
                {errors.content && (
                  <p className="text-red-600 text-sm">{errors.content}</p>
                )}
                <div className="text-sm text-gray-500 ml-auto">
                  {formData.content.length.toLocaleString()}/10,000
                </div>
              </div>
            </div>

            {/* 작성 가이드 */}
            <div className="bg-blue-50 rounded-lg p-4">
              <h3 className="text-sm font-semibold text-blue-900 mb-2">
                📝 작성 가이드
              </h3>
              <ul className="text-sm text-blue-800 space-y-1">
                <li>• 다른 회원들에게 도움이 되는 내용을 작성해주세요</li>
                <li>• 구체적이고 명확한 제목을 사용해주세요</li>
                <li>• 마크다운 문법을 사용하여 내용을 꾸밀 수 있습니다</li>
                <li>• 개인정보나 민감한 정보는 포함하지 마세요</li>
              </ul>
            </div>

            {/* 버튼 영역 */}
            <div className="flex justify-between items-center pt-6 border-t border-gray-200">
              <div className="flex space-x-3">
                <Button
                  type="button"
                  variant="ghost"
                  onClick={() => navigate('/community')}
                >
                  취소
                </Button>
                <Button
                  type="button"
                  variant="outline"
                  onClick={handleSaveDraft}
                  disabled={submitting}
                >
                  임시저장
                </Button>
              </div>

              <Button
                type="submit"
                loading={submitting}
                disabled={!formData.title.trim() || !formData.content.trim()}
                className="bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700"
              >
                {isEditMode ? '수정 완료' : '게시글 등록'}
              </Button>
            </div>
          </div>
        </form>

        {/* 마크다운 가이드 */}
        <div className="mt-6 bg-white rounded-xl shadow-sm p-6">
          <details className="group">
            <summary className="cursor-pointer text-lg font-semibold text-gray-900 flex items-center">
              <svg className="w-5 h-5 mr-2 transform group-open:rotate-90 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
              </svg>
              마크다운 사용법
            </summary>
            <div className="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
              <div>
                <h4 className="font-semibold text-gray-900 mb-2">기본 문법</h4>
                <div className="space-y-2 text-gray-600">
                  <div><code className="bg-gray-100 px-1 rounded"># 제목 1</code></div>
                  <div><code className="bg-gray-100 px-1 rounded">## 제목 2</code></div>
                  <div><code className="bg-gray-100 px-1 rounded">**굵은 글씨**</code></div>
                  <div><code className="bg-gray-100 px-1 rounded">*기울임 글씨*</code></div>
                  <div><code className="bg-gray-100 px-1 rounded">`코드`</code></div>
                </div>
              </div>
              <div>
                <h4 className="font-semibold text-gray-900 mb-2">고급 기능</h4>
                <div className="space-y-2 text-gray-600">
                  <div><code className="bg-gray-100 px-1 rounded">[링크](URL)</code></div>
                  <div><code className="bg-gray-100 px-1 rounded">![이미지](URL)</code></div>
                  <div><code className="bg-gray-100 px-1 rounded">- 목록 항목</code></div>
                  <div><code className="bg-gray-100 px-1 rounded">1. 번호 목록</code></div>
                  <div><code className="bg-gray-100 px-1 rounded">> 인용문</code></div>
                </div>
              </div>
            </div>
          </details>
        </div>
      </div>
    </div>
  );
};

export default PostWritePage;