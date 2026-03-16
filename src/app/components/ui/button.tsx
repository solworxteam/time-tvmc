import type { ButtonHTMLAttributes, ReactNode } from 'react';

type ButtonVariant = 'default' | 'ghost' | 'outline';
type ButtonSize = 'default' | 'icon';

interface ButtonProps extends ButtonHTMLAttributes<HTMLButtonElement> {
  children: ReactNode;
  variant?: ButtonVariant;
  size?: ButtonSize;
}

export function Button({
  children,
  className = '',
  variant = 'default',
  size = 'default',
  type = 'button',
  ...props
}: ButtonProps) {
  const baseClasses = 'inline-flex items-center justify-center rounded-lg font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60';
  const variantClasses = {
    default: 'bg-green-600 text-white hover:bg-green-700',
    ghost: 'bg-transparent text-inherit hover:bg-black/10',
    outline: 'border border-gray-300 text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700'
  };
  const sizeClasses = {
    default: 'px-4 py-2 text-sm',
    icon: 'h-10 w-10'
  };

  return (
    <button
      type={type}
      className={`${baseClasses} ${variantClasses[variant]} ${sizeClasses[size]} ${className}`.trim()}
      {...props}
    >
      {children}
    </button>
  );
}
