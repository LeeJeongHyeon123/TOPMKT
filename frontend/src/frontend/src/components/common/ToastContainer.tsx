import React from 'react';
import { useToast } from '../../context/ToastContext';
import { cn } from '../../utils/cn';

const ToastContainer: React.FC = () => {
  const { toasts, removeToast } = useToast();

  const getToastIcon = (type: string) => {
    switch (type) {
      case 'success':
        return (
          <svg className="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3">
            <path strokeLinecap="round" strokeLinejoin="round" d="M5 13l4 4L19 7" />
          </svg>
        );
      case 'error':
        return (
          <svg className="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3">
            <path strokeLinecap="round" strokeLinejoin="round" d="M18 6L6 18M6 6l12 12" />
          </svg>
        );
      case 'warning':
        return (
          <svg className="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3">
            <path strokeLinecap="round" strokeLinejoin="round" d="M12 9v3m0 3h.01" />
          </svg>
        );
      case 'info':
        return (
          <svg className="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3">
            <circle cx="12" cy="12" r="10"/>
            <path strokeLinecap="round" strokeLinejoin="round" d="M12 16v-4M12 8h.01"/>
          </svg>
        );
      default:
        return null;
    }
  };

  const getToastStyles = (_type: string) => {
    return 'premium-toast';
  };

  // Premium color functions
  const getToastBackground = (type: string) => {
    switch (type) {
      case 'success':
        return 'linear-gradient(135deg, rgba(16, 185, 129, 0.95) 0%, rgba(5, 150, 105, 0.98) 100%)';
      case 'error':
        return 'linear-gradient(135deg, rgba(239, 68, 68, 0.95) 0%, rgba(220, 38, 38, 0.98) 100%)';
      case 'warning':
        return 'linear-gradient(135deg, rgba(245, 158, 11, 0.95) 0%, rgba(217, 119, 6, 0.98) 100%)';
      case 'info':
        return 'linear-gradient(135deg, rgba(59, 130, 246, 0.95) 0%, rgba(37, 99, 235, 0.98) 100%)';
      default:
        return 'linear-gradient(135deg, rgba(107, 114, 128, 0.95) 0%, rgba(75, 85, 99, 0.98) 100%)';
    }
  };

  const getToastBorderColor = (type: string) => {
    switch (type) {
      case 'success':
        return 'rgba(16, 185, 129, 0.3)';
      case 'error':
        return 'rgba(239, 68, 68, 0.3)';
      case 'warning':
        return 'rgba(245, 158, 11, 0.3)';
      case 'info':
        return 'rgba(59, 130, 246, 0.3)';
      default:
        return 'rgba(107, 114, 128, 0.3)';
    }
  };

  const getToastShadow = (type: string) => {
    switch (type) {
      case 'success':
        return '0 25px 50px -12px rgba(16, 185, 129, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.1)';
      case 'error':
        return '0 25px 50px -12px rgba(239, 68, 68, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.1)';
      case 'warning':
        return '0 25px 50px -12px rgba(245, 158, 11, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.1)';
      case 'info':
        return '0 25px 50px -12px rgba(59, 130, 246, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.1)';
      default:
        return '0 25px 50px -12px rgba(107, 114, 128, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.1)';
    }
  };

  const getToastGradientBorder = (type: string) => {
    switch (type) {
      case 'success':
        return 'linear-gradient(135deg, rgba(16, 185, 129, 0.6), rgba(5, 150, 105, 0.4))';
      case 'error':
        return 'linear-gradient(135deg, rgba(239, 68, 68, 0.6), rgba(220, 38, 38, 0.4))';
      case 'warning':
        return 'linear-gradient(135deg, rgba(245, 158, 11, 0.6), rgba(217, 119, 6, 0.4))';
      case 'info':
        return 'linear-gradient(135deg, rgba(59, 130, 246, 0.6), rgba(37, 99, 235, 0.4))';
      default:
        return 'linear-gradient(135deg, rgba(107, 114, 128, 0.6), rgba(75, 85, 99, 0.4))';
    }
  };

  const getIconBackground = (type: string) => {
    switch (type) {
      case 'success':
        return 'linear-gradient(135deg, rgba(255, 255, 255, 0.25), rgba(255, 255, 255, 0.1))';
      case 'error':
        return 'linear-gradient(135deg, rgba(255, 255, 255, 0.25), rgba(255, 255, 255, 0.1))';
      case 'warning':
        return 'linear-gradient(135deg, rgba(255, 255, 255, 0.25), rgba(255, 255, 255, 0.1))';
      case 'info':
        return 'linear-gradient(135deg, rgba(255, 255, 255, 0.25), rgba(255, 255, 255, 0.1))';
      default:
        return 'linear-gradient(135deg, rgba(255, 255, 255, 0.25), rgba(255, 255, 255, 0.1))';
    }
  };

  const getIconShadow = (_type: string) => {
    return '0 4px 12px rgba(0, 0, 0, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.2)';
  };

  const getTextColor = (_type: string, element: 'title' | 'message') => {
    return element === 'title' ? 'rgba(255, 255, 255, 0.95)' : 'rgba(255, 255, 255, 0.85)';
  };

  const getActionButtonBackground = (_type: string) => {
    return 'rgba(255, 255, 255, 0.2)';
  };

  const getActionButtonColor = (_type: string) => {
    return 'rgba(255, 255, 255, 0.95)';
  };

  const getCloseButtonColor = (_type: string) => {
    return 'rgba(255, 255, 255, 0.8)';
  };

  if (toasts.length === 0) return null;

  return (
    <div 
      className="fixed top-6 right-6 z-[9999] flex flex-col gap-4 max-w-md w-full pointer-events-none"
      style={{
        fontFamily: '"-apple-system", "BlinkMacSystemFont", "Segoe UI", "Roboto", "Helvetica Neue", "Arial", sans-serif'
      }}
    >
      {toasts.map((toast) => (
        <div
          key={toast.id}
          className={cn(
            'relative pointer-events-auto transform transition-all duration-500 ease-out',
            'animate-in slide-in-from-right-full fade-in',
            'rounded-2xl overflow-hidden backdrop-blur-xl border',
            'shadow-2xl hover:shadow-3xl hover:-translate-y-1 hover:scale-[1.02]',
            getToastStyles(toast.type)
          )}
          style={{
            background: getToastBackground(toast.type),
            borderColor: getToastBorderColor(toast.type),
            boxShadow: getToastShadow(toast.type),
            animation: 'slideInToast 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards'
          }}
        >
          {/* Gradient Border Effect */}
          <div 
            className="absolute inset-0 rounded-2xl p-[1px]"
            style={{
              background: getToastGradientBorder(toast.type),
              mask: 'linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0)',
              maskComposite: 'exclude',
              WebkitMask: 'linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0)',
              WebkitMaskComposite: 'exclude'
            }}
          />
          
          {/* Content */}
          <div className="relative z-10 flex items-center gap-4 p-6">
            {/* Icon */}
            <div className="flex-shrink-0">
              <div 
                className="w-8 h-8 rounded-full flex items-center justify-center relative overflow-hidden"
                style={{
                  background: getIconBackground(toast.type),
                  boxShadow: getIconShadow(toast.type)
                }}
              >
                <div className="absolute inset-0 animate-pulse opacity-20 rounded-full" style={{ background: 'radial-gradient(circle, rgba(255,255,255,0.8) 0%, transparent 70%)' }} />
                {getToastIcon(toast.type)}
              </div>
            </div>
            
            {/* Text Content */}
            <div className="flex-1 min-w-0 flex flex-col justify-center">
              {toast.title && (
                <div 
                  className="font-bold text-lg mb-2 leading-snug"
                  style={{ color: getTextColor(toast.type, 'title') }}
                >
                  {toast.title}
                </div>
              )}
              <div 
                className="text-base leading-relaxed break-words"
                style={{ color: getTextColor(toast.type, 'message') }}
              >
                {toast.message}
              </div>
              {toast.action && (
                <div className="mt-3">
                  <button
                    onClick={toast.action.onClick}
                    className="inline-flex items-center px-3 py-2 text-sm font-semibold rounded-lg transition-all duration-200 hover:scale-105 active:scale-95"
                    style={{
                      background: getActionButtonBackground(toast.type),
                      color: getActionButtonColor(toast.type),
                      boxShadow: '0 2px 8px rgba(0,0,0,0.1)'
                    }}
                  >
                    {toast.action.label}
                  </button>
                </div>
              )}
            </div>
            
            {/* Close Button */}
            <button
              onClick={() => removeToast(toast.id)}
              className="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center transition-all duration-200 hover:scale-110 active:scale-95 group self-start mt-1"
              style={{
                background: 'rgba(255, 255, 255, 0.1)',
                backdropFilter: 'blur(10px)'
              }}
              onMouseEnter={(e) => {
                e.currentTarget.style.background = 'rgba(255, 255, 255, 0.2)';
              }}
              onMouseLeave={(e) => {
                e.currentTarget.style.background = 'rgba(255, 255, 255, 0.1)';
              }}
            >
              <svg 
                className="w-3.5 h-3.5 transition-transform duration-200 group-hover:rotate-90" 
                viewBox="0 0 24 24" 
                fill="none" 
                stroke="currentColor" 
                strokeWidth="2.5"
                style={{ color: getCloseButtonColor(toast.type) }}
              >
                <path strokeLinecap="round" strokeLinejoin="round" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>
      ))}
      
      <style dangerouslySetInnerHTML={{
        __html: `
          @keyframes slideInToast {
            0% {
              transform: translateX(100%) scale(0.9);
              opacity: 0;
            }
            60% {
              transform: translateX(-8px) scale(1.05);
              opacity: 0.9;
            }
            100% {
              transform: translateX(0) scale(1);
              opacity: 1;
            }
          }
        `
      }} />
    </div>
  );
};

export default ToastContainer;