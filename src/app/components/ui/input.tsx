import type { InputHTMLAttributes } from 'react';

export function Input({ className = '', ...props }: InputHTMLAttributes<HTMLInputElement>) {
  return (
    <input
      className={`h-10 w-full rounded-lg border px-3 py-2 text-sm text-gray-900 outline-none transition focus:border-green-500 focus:ring-2 focus:ring-green-500/20 dark:text-white ${className}`.trim()}
      {...props}
    />
  );
}
