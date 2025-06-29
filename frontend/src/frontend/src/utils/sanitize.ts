import DOMPurify from 'dompurify';

// DOMPurify 설정
const sanitizerConfig = {
  ALLOWED_TAGS: [
    'p', 'br', 'strong', 'em', 'u', 'a', 'ul', 'ol', 'li',
    'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'blockquote'
  ],
  ALLOWED_ATTR: ['href', 'target', 'rel'],
  ALLOWED_URI_REGEXP: /^(?:(?:(?:f|ht)tps?|mailto|tel|callto|cid|xmpp):|[^a-z]|[a-z+.-]+(?:[^a-z+.-:]|$))/i,
  ADD_ATTR: ['target'],
  FORBID_TAGS: ['script', 'object', 'embed', 'form', 'input'],
  FORBID_ATTR: ['style', 'onerror', 'onload', 'onclick']
};

/**
 * HTML 콘텐츠를 안전하게 새니타이즈
 * @param dirty 새니타이즈할 HTML 문자열
 * @returns 새니타이즈된 안전한 HTML 문자열
 */
export const sanitizeHtml = (dirty: string): string => {
  if (!dirty) return '';
  
  try {
    return DOMPurify.sanitize(dirty, sanitizerConfig);
  } catch (error) {
    if (process.env.NODE_ENV === 'development') {
      console.error('HTML sanitization failed:', error);
    }
    // 새니타이즈 실패 시 모든 HTML 태그 제거
    return dirty.replace(/<[^>]*>/g, '');
  }
};

/**
 * React dangerouslySetInnerHTML을 위한 안전한 HTML 객체 생성
 * @param dirty 새니타이즈할 HTML 문자열
 * @returns React에서 사용할 수 있는 안전한 HTML 객체
 */
export const createSafeHtml = (dirty: string) => {
  return {
    __html: sanitizeHtml(dirty)
  };
};

/**
 * 텍스트만 추출 (HTML 태그 완전 제거)
 * @param html HTML이 포함된 문자열
 * @returns 순수 텍스트
 */
export const stripHtml = (html: string): string => {
  if (!html) return '';
  
  try {
    // 먼저 새니타이즈 후 텍스트만 추출
    const sanitized = DOMPurify.sanitize(html, { ALLOWED_TAGS: [] });
    return sanitized.trim();
  } catch (error) {
    if (process.env.NODE_ENV === 'development') {
      console.error('HTML stripping failed:', error);
    }
    // 실패 시 정규식으로 태그 제거
    return html.replace(/<[^>]*>/g, '').trim();
  }
};

/**
 * URL 안전성 검증
 * @param url 검증할 URL
 * @returns 안전한 URL 여부
 */
export const isSafeUrl = (url: string): boolean => {
  if (!url) return false;
  
  try {
    const parsed = new URL(url);
    // HTTP, HTTPS, mailto만 허용
    return ['http:', 'https:', 'mailto:'].includes(parsed.protocol);
  } catch {
    return false;
  }
};

/**
 * 사용자 입력 텍스트 기본 새니타이즈
 * @param input 사용자 입력 문자열
 * @returns 새니타이즈된 텍스트
 */
export const sanitizeUserInput = (input: string): string => {
  if (!input) return '';
  
  return input
    .trim()
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#x27;')
    .replace(/\//g, '&#x2F;');
};

export default {
  sanitizeHtml,
  createSafeHtml,
  stripHtml,
  isSafeUrl,
  sanitizeUserInput
};