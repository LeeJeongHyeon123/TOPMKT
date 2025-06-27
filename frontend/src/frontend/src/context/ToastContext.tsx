// Toast 알림 관리를 위한 React Context
import React, { createContext, useContext, useState, ReactNode, useCallback } from 'react'

export type ToastType = 'success' | 'error' | 'warning' | 'info'

export interface Toast {
  id: string
  type: ToastType
  title?: string
  message: string
  duration?: number
  action?: {
    label: string
    onClick: () => void
  }
}

interface ToastContextType {
  toasts: Toast[]
  addToast: (toast: Omit<Toast, 'id'>) => void
  removeToast: (id: string) => void
  clearToasts: () => void
  success: (message: string, title?: string, duration?: number) => void
  error: (message: string, title?: string, duration?: number) => void
  warning: (message: string, title?: string, duration?: number) => void
  info: (message: string, title?: string, duration?: number) => void
}

const ToastContext = createContext<ToastContextType | undefined>(undefined)

interface ToastProviderProps {
  children: ReactNode
  maxToasts?: number
}

export const ToastProvider: React.FC<ToastProviderProps> = ({ 
  children, 
  maxToasts = 5 
}) => {
  const [toasts, setToasts] = useState<Toast[]>([])

  const addToast = useCallback((toast: Omit<Toast, 'id'>) => {
    const id = Math.random().toString(36).substr(2, 9)
    const newToast: Toast = {
      id,
      duration: toast.duration || 5000, // 기본 5초
      ...toast,
    }

    setToasts(prev => {
      const updated = [newToast, ...prev]
      // 최대 개수 제한
      return updated.slice(0, maxToasts)
    })

    // 자동 제거
    if (newToast.duration && newToast.duration > 0) {
      setTimeout(() => {
        setToasts(prev => prev.filter(toast => toast.id !== id));
      }, newToast.duration);
    }
  }, [maxToasts])

  const removeToast = useCallback((id: string) => {
    setToasts(prev => prev.filter(toast => toast.id !== id))
  }, [])

  const clearToasts = useCallback(() => {
    setToasts([])
  }, [])

  // 편의 메서드들
  const success = useCallback((message: string, title?: string, duration?: number) => {
    addToast({ type: 'success', message, title, duration: duration || 5000 })
  }, [addToast])

  const error = useCallback((message: string, title?: string, duration?: number) => {
    addToast({ type: 'error', message, title, duration: duration || 7000 }) // 에러는 더 오래 표시
  }, [addToast])

  const warning = useCallback((message: string, title?: string, duration?: number) => {
    addToast({ type: 'warning', message, title, duration: duration || 6000 })
  }, [addToast])

  const info = useCallback((message: string, title?: string, duration?: number) => {
    addToast({ type: 'info', message, title, duration: duration || 5000 })
  }, [addToast])

  const value: ToastContextType = {
    toasts,
    addToast,
    removeToast,
    clearToasts,
    success,
    error,
    warning,
    info,
  }

  return (
    <ToastContext.Provider value={value}>
      {children}
    </ToastContext.Provider>
  )
}

export const useToast = (): ToastContextType => {
  const context = useContext(ToastContext)
  if (context === undefined) {
    throw new Error('useToast must be used within a ToastProvider')
  }
  return context
}

export default ToastContext