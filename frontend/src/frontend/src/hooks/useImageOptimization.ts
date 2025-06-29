import { useState, useEffect, useCallback, useRef } from 'react';

interface ImageOptimizationOptions {
  quality?: number;
  format?: 'webp' | 'jpeg' | 'png' | 'avif' | 'auto';
  maxWidth?: number;
  maxHeight?: number;
  enableProgressive?: boolean;
  enableLazyLoading?: boolean;
  preloadCritical?: boolean;
}

interface OptimizedImageResult {
  src: string;
  srcSet: string;
  sizes: string;
  placeholder: string;
  isLoading: boolean;
  error: string | null;
}

// WebP 지원 감지
let webpSupport: boolean | null = null;

const checkWebPSupport = (): Promise<boolean> => {
  if (webpSupport !== null) {
    return Promise.resolve(webpSupport);
  }

  return new Promise((resolve) => {
    const webP = new Image();
    webP.onload = webP.onerror = () => {
      webpSupport = webP.height === 2;
      resolve(webpSupport);
    };
    webP.src = 'data:image/webp;base64,UklGRjoAAABXRUJQVlA4IC4AAACyAgCdASoCAAIALmk0mk0iIiIiIgBoSygABc6WWgAA/veff/0PP8bA//LwYAAA';
  });
};

// AVIF 지원 감지
let avifSupport: boolean | null = null;

const checkAVIFSupport = (): Promise<boolean> => {
  if (avifSupport !== null) {
    return Promise.resolve(avifSupport);
  }

  return new Promise((resolve) => {
    const avif = new Image();
    avif.onload = avif.onerror = () => {
      avifSupport = avif.height === 2;
      resolve(avifSupport);
    };
    avif.src = 'data:image/avif;base64,AAAAIGZ0eXBhdmlmAAAAAGF2aWZtaWYxbWlhZk1BMUIAAADybWV0YQAAAAAAAAAoaGRscgAAAAAAAAAAcGljdAAAAAAAAAAAAAAAAGxpYmF2aWYAAAAADnBpdG0AAAAAAAEAAAAeaWxvYwAAAABEAAABAAEAAAABAAABGgAAAB0AAAAoaWluZgAAAAAAAQAAABppbmZlAgAAAAABAABhdjAxQ29sb3IAAAAAamlwcnAAAABLaXBjbwAAABRpc3BlAAAAAAAAAAIAAAACAAAAEHBpeGkAAAAAAwgICAAAAAxhdjFDgQ0MAAAAABNjb2xybmNseAACAAIAAYAAAAAXaXBtYQAAAAAAAAABAAEEAQKDBAAAACVtZGF0EgAKCBgABogQEAwgMg8f8D///8WfhwB8+ErK42A=';
  });
};

// 이미지 압축 함수
const compressImage = (
  file: File, 
  options: { quality: number; maxWidth: number; maxHeight: number; format: string }
): Promise<string> => {
  return new Promise((resolve, reject) => {
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    const img = new Image();

    img.onload = () => {
      // 크기 계산
      let { width, height } = img;
      const { maxWidth, maxHeight } = options;

      if (width > maxWidth || height > maxHeight) {
        const ratio = Math.min(maxWidth / width, maxHeight / height);
        width *= ratio;
        height *= ratio;
      }

      canvas.width = width;
      canvas.height = height;

      // 이미지 그리기
      ctx?.drawImage(img, 0, 0, width, height);

      // 압축된 이미지 데이터 생성
      const mimeType = options.format === 'webp' ? 'image/webp' : 
                      options.format === 'jpeg' ? 'image/jpeg' : 'image/png';
      
      const compressedDataUrl = canvas.toDataURL(mimeType, options.quality);
      resolve(compressedDataUrl);
    };

    img.onerror = reject;
    img.src = URL.createObjectURL(file);
  });
};

// 반응형 이미지 URL 생성
const generateResponsiveUrls = (
  baseSrc: string, 
  format: string,
  widths: number[] = [320, 640, 960, 1280, 1920]
): string[] => {
  if (baseSrc.startsWith('data:') || baseSrc.startsWith('blob:')) {
    return [baseSrc];
  }

  const extension = format === 'auto' ? 'webp' : format;
  const baseUrl = baseSrc.replace(/\.[^/.]+$/, '');
  
  return widths.map(width => `${baseUrl}_${width}w.${extension} ${width}w`);
};

// 플레이스홀더 생성
const generatePlaceholder = (width: number, height: number): string => {
  const canvas = document.createElement('canvas');
  const ctx = canvas.getContext('2d');
  
  canvas.width = width;
  canvas.height = height;
  
  if (ctx) {
    // 그라데이션 배경
    const gradient = ctx.createLinearGradient(0, 0, width, height);
    gradient.addColorStop(0, '#f0f0f0');
    gradient.addColorStop(1, '#e0e0e0');
    
    ctx.fillStyle = gradient;
    ctx.fillRect(0, 0, width, height);
    
    // 로딩 텍스트
    ctx.fillStyle = '#999';
    ctx.font = '14px Arial';
    ctx.textAlign = 'center';
    ctx.fillText('Loading...', width / 2, height / 2);
  }
  
  return canvas.toDataURL('image/png', 0.1);
};

export const useImageOptimization = (
  src: string,
  options: ImageOptimizationOptions = {}
): OptimizedImageResult => {
  const {
    quality = 0.8,
    format = 'auto',
    maxWidth = 1920,
    maxHeight = 1080,
    preloadCritical = false
  } = options;

  const [result, setResult] = useState<OptimizedImageResult>({
    src: '',
    srcSet: '',
    sizes: '(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw',
    placeholder: generatePlaceholder(maxWidth, maxHeight),
    isLoading: true,
    error: null
  });

  const abortControllerRef = useRef<AbortController | null>(null);

  const optimizeImage = useCallback(async (imageSrc: string) => {
    if (!imageSrc) return;

    // 이전 요청 취소
    if (abortControllerRef.current) {
      abortControllerRef.current.abort();
    }

    abortControllerRef.current = new AbortController();
    
    try {
      setResult(prev => ({ ...prev, isLoading: true, error: null }));

      // 포맷 결정
      const [supportsWebP, supportsAVIF] = await Promise.all([
        checkWebPSupport(),
        checkAVIFSupport()
      ]);

      let selectedFormat = format;
      if (format === 'auto') {
        selectedFormat = supportsAVIF ? 'avif' : supportsWebP ? 'webp' : 'jpeg';
      }

      // 기본 최적화된 URL 생성
      let optimizedSrc = imageSrc;
      
      // 외부 이미지 서비스 URL 처리 (예: Cloudinary, ImageKit 등)
      if (imageSrc.includes('cloudinary.com')) {
        optimizedSrc = imageSrc.replace('/upload/', `/upload/q_${Math.round(quality * 100)},f_${selectedFormat},w_${maxWidth},h_${maxHeight},c_limit/`);
      } else if (imageSrc.includes('imagekit.io')) {
        const params = new URLSearchParams({
          'tr': `q-${Math.round(quality * 100)},f-${selectedFormat},w-${maxWidth},h-${maxHeight},c-at_max`
        });
        optimizedSrc = `${imageSrc}?${params.toString()}`;
      } else {
        // 로컬 이미지나 기타 URL의 경우 확장자 변경
        if (selectedFormat !== 'auto' && !imageSrc.startsWith('data:') && !imageSrc.startsWith('blob:')) {
          optimizedSrc = imageSrc.replace(/\.(jpg|jpeg|png|webp|avif)$/i, `.${selectedFormat}`);
        }
      }

      // srcSet 생성
      const responsiveUrls = generateResponsiveUrls(optimizedSrc, selectedFormat);
      const srcSet = responsiveUrls.join(', ');

      // 이미지 프리로드 (critical한 경우)
      if (preloadCritical) {
        const link = document.createElement('link');
        link.rel = 'preload';
        link.as = 'image';
        link.href = optimizedSrc;
        if (srcSet) link.setAttribute('imagesrcset', srcSet);
        document.head.appendChild(link);
      }

      setResult({
        src: optimizedSrc,
        srcSet,
        sizes: '(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw',
        placeholder: generatePlaceholder(320, 240),
        isLoading: false,
        error: null
      });

    } catch (error) {
      if (error instanceof Error && error.name !== 'AbortError') {
        setResult(prev => ({
          ...prev,
          isLoading: false,
          error: error.message || '이미지 최적화 중 오류가 발생했습니다.'
        }));
      }
    }
  }, [quality, format, maxWidth, maxHeight, preloadCritical]);

  useEffect(() => {
    optimizeImage(src);
    
    return () => {
      if (abortControllerRef.current) {
        abortControllerRef.current.abort();
      }
    };
  }, [src, optimizeImage]);

  return result;
};

// 이미지 파일 압축 훅
export const useImageCompression = () => {
  const [isCompressing, setIsCompressing] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const compressImageFile = useCallback(async (
    file: File,
    options: Partial<ImageOptimizationOptions> = {}
  ): Promise<string> => {
    const {
      quality = 0.8,
      format = 'webp',
      maxWidth = 1920,
      maxHeight = 1080
    } = options;

    setIsCompressing(true);
    setError(null);

    try {
      const formatString = format === 'auto' ? 'webp' : format;
      const compressedUrl = await compressImage(file, {
        quality,
        maxWidth,
        maxHeight,
        format: formatString
      });

      setIsCompressing(false);
      return compressedUrl;
    } catch (err) {
      const errorMessage = err instanceof Error ? err.message : '이미지 압축 중 오류가 발생했습니다.';
      setError(errorMessage);
      setIsCompressing(false);
      throw new Error(errorMessage);
    }
  }, []);

  return {
    compressImageFile,
    isCompressing,
    error
  };
};

// 이미지 프리로딩 훅
export const useImagePreloader = () => {
  const [preloadedImages, setPreloadedImages] = useState<Set<string>>(new Set());
  const [failedImages, setFailedImages] = useState<Set<string>>(new Set());

  const preloadImage = useCallback((src: string): Promise<void> => {
    return new Promise((resolve, reject) => {
      if (preloadedImages.has(src)) {
        resolve();
        return;
      }

      if (failedImages.has(src)) {
        reject(new Error('Image previously failed to load'));
        return;
      }

      const img = new Image();
      
      img.onload = () => {
        setPreloadedImages(prev => new Set(prev).add(src));
        resolve();
      };
      
      img.onerror = () => {
        setFailedImages(prev => new Set(prev).add(src));
        reject(new Error('Failed to preload image'));
      };
      
      img.src = src;
    });
  }, [preloadedImages, failedImages]);

  const preloadImages = useCallback(async (sources: string[]): Promise<void[]> => {
    const results = await Promise.allSettled(sources.map(preloadImage));
    return results.map(() => void 0);
  }, [preloadImage]);

  const isPreloaded = useCallback((src: string): boolean => {
    return preloadedImages.has(src);
  }, [preloadedImages]);

  const hasFailed = useCallback((src: string): boolean => {
    return failedImages.has(src);
  }, [failedImages]);

  return {
    preloadImage,
    preloadImages,
    isPreloaded,
    hasFailed,
    preloadedCount: preloadedImages.size,
    failedCount: failedImages.size
  };
};