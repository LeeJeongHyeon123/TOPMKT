import { forwardRef, ButtonHTMLAttributes, ReactNode } from 'react'
import { cn } from '@/utils/cn'

export interface ButtonProps extends ButtonHTMLAttributes<HTMLButtonElement> {
  variant?: 'primary' | 'secondary' | 'outline' | 'ghost' | 'danger';
  size?: 'sm' | 'md' | 'lg' | 'xl';
  loading?: boolean;
  fullWidth?: boolean;
  leftIcon?: ReactNode;
  rightIcon?: ReactNode;
  children: ReactNode;
}

const Button = forwardRef<HTMLButtonElement, ButtonProps>(
  (
    {
      className,
      variant = 'primary',
      size = 'md',
      loading = false,
      fullWidth = false,
      leftIcon,
      rightIcon,
      disabled,
      children,
      ...props
    },
    ref
  ) => {
    const baseStyles = [
      'inline-flex items-center justify-center',
      'font-medium transition-all duration-200',
      'focus:outline-none focus:ring-2 focus:ring-offset-2',
      'disabled:opacity-50 disabled:cursor-not-allowed',
      'rounded-lg'
    ];

    const variants = {
      primary: [
        'bg-gradient-to-r from-blue-800 to-blue-700 text-white',
        'hover:from-blue-900 hover:to-blue-800 active:from-blue-900 active:to-blue-800',
        'focus:ring-blue-600',
        'shadow-sm hover:shadow-md'
      ],
      secondary: [
        'bg-gray-100 text-gray-900',
        'hover:bg-gray-200 active:bg-gray-300',
        'focus:ring-gray-500',
        'border border-gray-300'
      ],
      outline: [
        'bg-transparent text-blue-800 border-2 border-blue-800',
        'hover:bg-blue-50 active:bg-blue-100',
        'focus:ring-blue-600'
      ],
      ghost: [
        'bg-transparent text-gray-700',
        'hover:bg-gray-100 active:bg-gray-200',
        'focus:ring-gray-500'
      ],
      danger: [
        'bg-red-600 text-white',
        'hover:bg-red-700 active:bg-red-800',
        'focus:ring-red-500',
        'shadow-sm hover:shadow-md'
      ]
    };

    const sizes = {
      sm: 'text-sm px-3 py-1.5 gap-1.5',
      md: 'text-sm px-4 py-2 gap-2',
      lg: 'text-base px-6 py-3 gap-2',
      xl: 'text-lg px-8 py-4 gap-3'
    };

    const widthStyles = fullWidth ? 'w-full' : '';

    const isDisabled = disabled || loading;

    return (
      <button
        className={cn(
          baseStyles.join(' '),
          variants[variant].join(' '),
          sizes[size],
          widthStyles,
          className
        )}
        disabled={isDisabled}
        ref={ref}
        {...props}
      >
        {loading ? (
          <>
            <svg 
              className="animate-spin h-4 w-4" 
              fill="none" 
              viewBox="0 0 24 24"
            >
              <circle 
                className="opacity-25" 
                cx="12" 
                cy="12" 
                r="10" 
                stroke="currentColor" 
                strokeWidth="4"
              />
              <path 
                className="opacity-75" 
                fill="currentColor" 
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              />
            </svg>
            처리 중...
          </>
        ) : (
          <>
            {leftIcon && (
              <span className="flex-shrink-0">
                {leftIcon}
              </span>
            )}
            <span>{children}</span>
            {rightIcon && (
              <span className="flex-shrink-0">
                {rightIcon}
              </span>
            )}
          </>
        )}
      </button>
    );
  }
);

Button.displayName = 'Button';

export default Button;