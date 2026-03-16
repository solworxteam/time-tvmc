import type { HTMLAttributes, ReactNode } from 'react';

interface BadgeProps extends HTMLAttributes<HTMLSpanElement> {
  children: ReactNode;
  variant?: 'default' | 'outline';
}

export function Badge({ children, className = '', variant = 'default', ...props }: BadgeProps) {
  const variantClasses = variant === 'outline'
    ? 'border border-current bg-transparent'
    : 'bg-green-600 text-white';

  return (
    <span
      className={`inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ${variantClasses} ${className}`.trim()}
      {...props}
    >
      {children}
    </span>
  );
}
