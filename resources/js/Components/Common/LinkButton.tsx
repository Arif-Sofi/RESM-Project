import React, { AnchorHTMLAttributes } from 'react';
import { Link } from '@inertiajs/react';

interface LinkButtonProps extends AnchorHTMLAttributes<HTMLAnchorElement> {
  href: string;
  variant?: 'primary' | 'secondary' | 'danger' | 'link';
  size?: 'sm' | 'md' | 'lg';
  external?: boolean;
}

export default function LinkButton({
  href,
  variant = 'link',
  size = 'md',
  external = false,
  className = '',
  children,
  ...props
}: LinkButtonProps) {
  const baseStyles = 'inline-flex items-center justify-center font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors';

  const variants = {
    primary: 'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500',
    secondary: 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 focus:ring-indigo-500',
    danger: 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
    link: 'text-indigo-600 hover:text-indigo-800 focus:ring-indigo-500',
  };

  const sizes = {
    sm: 'px-3 py-1.5 text-sm',
    md: 'px-4 py-2 text-sm',
    lg: 'px-6 py-3 text-base',
  };

  const classes = `
    ${variant === 'link' ? 'underline-offset-4 hover:underline' : baseStyles}
    ${variant !== 'link' ? variants[variant] : variants.link}
    ${variant !== 'link' ? sizes[size] : ''}
    ${className}
  `;

  if (external) {
    return (
      <a
        href={href}
        className={classes}
        target="_blank"
        rel="noopener noreferrer"
        {...props}
      >
        {children}
      </a>
    );
  }

  return (
    <Link href={href} className={classes} {...props}>
      {children}
    </Link>
  );
}
