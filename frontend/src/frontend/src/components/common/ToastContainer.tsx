import React from 'react';
import { useToast } from '../../context/ToastContext';
import { cn } from '../../utils/cn';

const ToastContainer: React.FC = () => {
  const { toasts, removeToast } = useToast();

  const getToastIcon = (type: string) => {
    switch (type) {
      case 'success':
        return (
          <svg className="w-6 h-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        );
      case 'error':
        return (
          <svg className="w-6 h-6 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        );
      case 'warning':
        return (
          <svg className="w-6 h-6 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
          </svg>
        );
      case 'info':
        return (
          <svg className="w-6 h-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        );
      default:
        return null;
    }
  };

  const getToastStyles = (type: string) => {
    switch (type) {
      case 'success':
        return 'bg-white border-l-4 border-green-400 shadow-lg';
      case 'error':
        return 'bg-white border-l-4 border-red-400 shadow-lg';
      case 'warning':
        return 'bg-white border-l-4 border-yellow-400 shadow-lg';
      case 'info':
        return 'bg-white border-l-4 border-blue-400 shadow-lg';
      default:
        return 'bg-white border-l-4 border-gray-400 shadow-lg';
    }
  };

  if (toasts.length === 0) return null;

  return (
    <div className="fixed top-20 right-4 z-[9999] space-y-4 max-w-md w-full pointer-events-none">
      {toasts.map((toast) => (
        <div
          key={toast.id}
          className={cn(
            'transform transition-all duration-300 ease-in-out',
            'animate-in slide-in-from-right-full fade-in',
            getToastStyles(toast.type),
            'rounded-lg p-5 backdrop-blur-sm pointer-events-auto min-h-[80px]'
          )}
        >
          <div className="flex items-center min-h-[48px]">
            <div className="flex-shrink-0">
              {getToastIcon(toast.type)}
            </div>
            <div className="ml-3 flex-1">
              {toast.title && (
                <p className="text-base font-semibold text-gray-900 mb-2">
                  {toast.title}
                </p>
              )}
              <p className="text-sm text-gray-700 leading-relaxed">
                {toast.message}
              </p>
              {toast.action && (
                <div className="mt-3">
                  <button
                    onClick={toast.action.onClick}
                    className="text-sm font-medium text-blue-600 hover:text-blue-500 transition-colors"
                  >
                    {toast.action.label}
                  </button>
                </div>
              )}
            </div>
            <div className="ml-4 flex-shrink-0">
              <button
                onClick={() => removeToast(toast.id)}
                className="inline-flex text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600 transition-colors"
              >
                <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            </div>
          </div>
        </div>
      ))}
    </div>
  );
};

export default ToastContainer;