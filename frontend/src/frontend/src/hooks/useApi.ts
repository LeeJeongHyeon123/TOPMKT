// API 호출을 위한 커스텀 훅
import { useState, useEffect, useCallback } from 'react';
import { useToast } from '../context/ToastContext';
import { useLoading } from '../context/LoadingContext';

interface UseApiOptions {
  showLoading?: boolean;
  showError?: boolean;
  showSuccess?: boolean;
  successMessage?: string;
  loadingMessage?: string;
}

interface UseApiState<T> {
  data: T | null;
  loading: boolean;
  error: string | null;
}

interface UseApiReturn<T> extends UseApiState<T> {
  execute: (...args: any[]) => Promise<T | null>;
  reset: () => void;
}

/**
 * API 호출을 위한 커스텀 훅
 */
export function useApi<T = any>(
  apiFunction: (...args: any[]) => Promise<T>,
  options: UseApiOptions = {}
): UseApiReturn<T> {
  const {
    showLoading = true,
    showError = true,
    showSuccess = false,
    successMessage,
    loadingMessage,
  } = options;

  const [state, setState] = useState<UseApiState<T>>({
    data: null,
    loading: false,
    error: null,
  });

  const { addToast } = useToast();
  const { startRequest, endRequest } = useLoading();

  const execute = useCallback(async (...args: any[]): Promise<T | null> => {
    try {
      setState(prev => ({ ...prev, loading: true, error: null }));
      
      if (showLoading) {
        startRequest();
      }

      const result = await apiFunction(...args);
      
      setState(prev => ({ ...prev, data: result, loading: false }));

      if (showSuccess && successMessage) {
        addToast({
          type: 'success',
          message: successMessage,
        });
      }

      return result;
    } catch (error) {
      const errorMessage = error instanceof Error ? error.message : '알 수 없는 오류가 발생했습니다.';
      
      setState(prev => ({ ...prev, error: errorMessage, loading: false }));

      if (showError) {
        addToast({
          type: 'error',
          message: errorMessage,
        });
      }

      return null;
    } finally {
      if (showLoading) {
        endRequest();
      }
    }
  }, [apiFunction, addToast, startRequest, endRequest, showLoading, showError, showSuccess, successMessage, loadingMessage]);

  const reset = useCallback(() => {
    setState({
      data: null,
      loading: false,
      error: null,
    });
  }, []);

  return {
    ...state,
    execute,
    reset,
  };
}

/**
 * 페이지 로드 시 자동으로 API를 호출하는 훅
 */
export function useApiOnMount<T = any>(
  apiFunction: (...args: any[]) => Promise<T>,
  args: any[] = [],
  options: UseApiOptions = {}
): UseApiState<T> & { refetch: () => Promise<T | null> } {
  const { data, loading, error, execute } = useApi(apiFunction, options);

  const refetch = useCallback(() => {
    return execute(...args);
  }, [execute, args]);

  useEffect(() => {
    refetch();
  }, [refetch]);

  return {
    data,
    loading,
    error,
    refetch,
  };
}

/**
 * 폼 제출을 위한 커스텀 훅
 */
export function useApiForm<T = any>(
  apiFunction: (...args: any[]) => Promise<T>,
  options: UseApiOptions & {
    onSuccess?: (data: T) => void;
    onError?: (error: string) => void;
    resetOnSuccess?: boolean;
  } = {}
): UseApiReturn<T> & {
  submitForm: (formData: any) => Promise<T | null>;
} {
  const {
    onSuccess,
    onError,
    resetOnSuccess = false,
    ...apiOptions
  } = options;

  const { data, loading, error, execute, reset } = useApi(apiFunction, {
    showLoading: true,
    showError: true,
    ...apiOptions,
  });

  const submitForm = useCallback(async (formData: any): Promise<T | null> => {
    const result = await execute(formData);
    
    if (result) {
      onSuccess?.(result);
      if (resetOnSuccess) {
        reset();
      }
    } else if (error) {
      onError?.(error);
    }

    return result;
  }, [execute, onSuccess, onError, resetOnSuccess, reset, error]);

  return {
    data,
    loading,
    error,
    execute,
    reset,
    submitForm,
  };
}

/**
 * 페이지네이션을 위한 커스텀 훅
 */
export function useApiPagination<T = any>(
  apiFunction: (page: number, ...args: any[]) => Promise<{ data: T[]; meta: any }>,
  initialArgs: any[] = [],
  options: UseApiOptions = {}
) {
  const [page, setPage] = useState(1);
  const [allData, setAllData] = useState<T[]>([]);
  const [meta, setMeta] = useState<any>(null);

  const { loading, error, execute } = useApi(apiFunction, options);

  const loadPage = useCallback(async (pageNumber: number, ...args: any[]) => {
    const result = await execute(pageNumber, ...args);
    
    if (result) {
      if (pageNumber === 1) {
        setAllData(result.data);
      } else {
        setAllData(prev => [...prev, ...result.data]);
      }
      setMeta(result.meta);
      setPage(pageNumber);
    }

    return result;
  }, [execute]);

  const loadMore = useCallback(() => {
    if (meta && meta.has_next) {
      return loadPage(page + 1, ...initialArgs);
    }
    return Promise.resolve(null);
  }, [loadPage, page, meta, initialArgs]);

  const refresh = useCallback(() => {
    setAllData([]);
    setMeta(null);
    setPage(1);
    return loadPage(1, ...initialArgs);
  }, [loadPage, initialArgs]);

  useEffect(() => {
    refresh();
  }, [refresh]);

  return {
    data: allData,
    meta,
    loading,
    error,
    page,
    loadPage,
    loadMore,
    refresh,
    hasMore: meta?.has_next || false,
  };
}

export default useApi;