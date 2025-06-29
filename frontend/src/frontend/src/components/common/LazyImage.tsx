import React, { useState, useRef, useEffect, useCallback } from 'react';
import { useIntersectionObserver } from '../../hooks/useIntersectionObserver';

interface LazyImageProps {
  src: string;
  alt: string;
  className?: string;
  placeholderSrc?: string;
  fallbackSrc?: string;
  width?: number;
  height?: number;
  objectFit?: 'cover' | 'contain' | 'fill' | 'scale-down' | 'none';
  loading?: 'lazy' | 'eager';
  sizes?: string;
  srcSet?: string;
  priority?: boolean;
  onLoad?: () => void;
  onError?: () => void;
  style?: React.CSSProperties;
}

interface ImageState {
  isLoading: boolean;
  isLoaded: boolean;
  hasError: boolean;
  currentSrc: string;
}

const LazyImage: React.FC<LazyImageProps> = ({
  src,
  alt,
  className = '',
  placeholderSrc = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIwIiBoZWlnaHQ9IjMyMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxOCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkxvYWRpbmcuLi48L3RleHQ+PC9zdmc+',
  fallbackSrc = '/assets/images/image-error.svg',
  width,
  height,
  objectFit = 'cover',
  loading = 'lazy',
  sizes,
  srcSet,
  priority = false,
  onLoad,
  onError,
  style,
  ...props
}) => {
  const [imageState, setImageState] = useState<ImageState>({
    isLoading: true,
    isLoaded: false,
    hasError: false,
    currentSrc: placeholderSrc
  });

  const imgRef = useRef<HTMLImageElement>(null);
  const { targetRef, isIntersecting } = useIntersectionObserver({
    threshold: 0.1,
    rootMargin: '50px',
    enabled: loading === 'lazy' && !priority
  });

  // 이미지 로드 상태 관리
  const handleImageLoad = useCallback(() => {
    setImageState(prev => ({
      ...prev,
      isLoading: false,
      isLoaded: true,
      hasError: false
    }));
    onLoad?.();
  }, [onLoad]);

  const handleImageError = useCallback(() => {
    setImageState(prev => ({
      ...prev,
      isLoading: false,
      isLoaded: false,
      hasError: true,
      currentSrc: fallbackSrc
    }));
    onError?.();
  }, [fallbackSrc, onError]);

  // 프리로드 이미지 생성
  const preloadImage = useCallback((imageSrc: string) => {
    const img = new Image();
    
    img.onload = () => {
      setImageState(prev => ({
        ...prev,
        currentSrc: imageSrc
      }));
      handleImageLoad();
    };
    
    img.onerror = handleImageError;
    
    // srcSet과 sizes 설정
    if (srcSet) {
      img.srcset = srcSet;
    }
    if (sizes) {
      img.sizes = sizes;
    }
    
    img.src = imageSrc;
  }, [srcSet, sizes, handleImageLoad, handleImageError]);

  // 교차점 감지 또는 우선순위가 높은 경우 이미지 로드
  useEffect(() => {
    if ((isIntersecting || priority || loading === 'eager') && src && !imageState.isLoaded && !imageState.hasError) {
      preloadImage(src);
    }
  }, [isIntersecting, priority, loading, src, imageState.isLoaded, imageState.hasError, preloadImage]);

  // WebP 지원 확인
  const [supportsWebP, setSupportsWebP] = useState<boolean | null>(null);

  useEffect(() => {
    const checkWebPSupport = () => {
      const canvas = document.createElement('canvas');
      canvas.width = 1;
      canvas.height = 1;
      const dataURL = canvas.toDataURL('image/webp');
      setSupportsWebP(dataURL.startsWith('data:image/webp'));
    };

    checkWebPSupport();
  }, []);

  // 최적화된 이미지 URL 생성
  const getOptimizedSrc = useCallback((originalSrc: string) => {
    if (!originalSrc || originalSrc.startsWith('data:') || originalSrc.startsWith('blob:')) {
      return originalSrc;
    }

    // WebP 지원 시 확장자 변경
    if (supportsWebP && !originalSrc.includes('.svg')) {
      return originalSrc.replace(/\.(jpg|jpeg|png)$/i, '.webp');
    }

    return originalSrc;
  }, [supportsWebP]);

  // 반응형 이미지 처리
  const generateSrcSet = useCallback((originalSrc: string) => {
    if (srcSet) return srcSet;
    if (!originalSrc || originalSrc.startsWith('data:') || originalSrc.startsWith('blob:')) {
      return undefined;
    }

    const optimizedSrc = getOptimizedSrc(originalSrc);
    const baseSrc = optimizedSrc.replace(/\.[^/.]+$/, '');
    const extension = optimizedSrc.split('.').pop();

    // 다양한 해상도용 srcSet 생성
    return [
      `${baseSrc}_1x.${extension} 1x`,
      `${baseSrc}_2x.${extension} 2x`,
      `${baseSrc}_3x.${extension} 3x`
    ].join(', ');
  }, [srcSet, getOptimizedSrc]);

  const combinedStyle: React.CSSProperties = {
    ...style,
    objectFit,
    width: width ? `${width}px` : style?.width,
    height: height ? `${height}px` : style?.height,
    transition: 'opacity 0.3s ease, filter 0.3s ease',
    opacity: imageState.isLoaded ? 1 : 0.8,
    filter: imageState.isLoading ? 'blur(1px)' : 'none'
  };

  return (
    <div 
      ref={targetRef}
      className={`lazy-image-container ${className}`}
      style={{
        position: 'relative',
        overflow: 'hidden',
        display: 'inline-block',
        width: width ? `${width}px` : '100%',
        height: height ? `${height}px` : 'auto'
      }}
    >
      <img
        ref={imgRef}
        src={getOptimizedSrc(imageState.currentSrc)}
        srcSet={generateSrcSet(imageState.currentSrc)}
        sizes={sizes}
        alt={alt}
        className={`lazy-image ${className}`}
        style={combinedStyle}
        onLoad={handleImageLoad}
        onError={handleImageError}
        loading={priority ? 'eager' : 'lazy'}
        decoding="async"
        {...props}
      />
      
      {/* 로딩 오버레이 */}
      {imageState.isLoading && (
        <div className="lazy-image-loading">
          <div className="loading-spinner"></div>
        </div>
      )}
      
      {/* 에러 오버레이 */}
      {imageState.hasError && (
        <div className="lazy-image-error">
          <i className="fas fa-exclamation-triangle"></i>
          <span>이미지 로드 실패</span>
        </div>
      )}

      {/* 스타일 */}
      <style>{`
        .lazy-image-container {
          background: #f3f4f6;
          border-radius: 4px;
        }

        .lazy-image {
          width: 100%;
          height: 100%;
          display: block;
        }

        .lazy-image-loading {
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          display: flex;
          align-items: center;
          justify-content: center;
          background: rgba(255, 255, 255, 0.8);
          backdrop-filter: blur(2px);
        }

        .loading-spinner {
          width: 24px;
          height: 24px;
          border: 2px solid #e5e7eb;
          border-top: 2px solid #3b82f6;
          border-radius: 50%;
          animation: lazy-spin 1s linear infinite;
        }

        .lazy-image-error {
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          bottom: 0;
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          background: #f9fafb;
          color: #6b7280;
          font-size: 0.875rem;
          text-align: center;
          padding: 1rem;
        }

        .lazy-image-error i {
          font-size: 1.5rem;
          margin-bottom: 0.5rem;
          color: #ef4444;
        }

        @keyframes lazy-spin {
          from {
            transform: rotate(0deg);
          }
          to {
            transform: rotate(360deg);
          }
        }

        /* 블러 효과를 위한 플레이스홀더 */
        .lazy-image[src*="data:image/svg"] {
          filter: blur(2px);
        }

        /* 고해상도 디스플레이 최적화 */
        @media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
          .lazy-image {
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
          }
        }

        /* 다크 모드 지원 */
        @media (prefers-color-scheme: dark) {
          .lazy-image-container {
            background: #374151;
          }
          
          .lazy-image-error {
            background: #1f2937;
            color: #9ca3af;
          }
          
          .lazy-image-loading {
            background: rgba(31, 41, 55, 0.8);
          }
        }

        /* 접근성: 애니메이션 줄이기 */
        @media (prefers-reduced-motion: reduce) {
          .lazy-image {
            transition: none;
          }
          
          .loading-spinner {
            animation: none;
          }
        }

        /* 인쇄 최적화 */
        @media print {
          .lazy-image-loading,
          .lazy-image-error {
            display: none;
          }
        }
      `}</style>
    </div>
  );
};

export default LazyImage;