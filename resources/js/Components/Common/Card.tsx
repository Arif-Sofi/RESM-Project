import React, { PropsWithChildren, HTMLAttributes } from 'react';

interface CardProps extends PropsWithChildren, HTMLAttributes<HTMLDivElement> {
  title?: string;
  description?: string;
  footer?: React.ReactNode;
  padding?: 'none' | 'sm' | 'md' | 'lg';
}

export default function Card({
  title,
  description,
  footer,
  padding = 'md',
  className = '',
  children,
  ...props
}: CardProps) {
  const paddingClasses = {
    none: '',
    sm: 'p-4',
    md: 'p-6',
    lg: 'p-8',
  };

  return (
    <div
      className={`bg-white shadow-sm rounded-lg border border-gray-200 ${className}`}
      {...props}
    >
      {(title || description) && (
        <div className={`${paddingClasses[padding]} border-b border-gray-200`}>
          {title && <h3 className="text-lg font-medium text-gray-900">{title}</h3>}
          {description && <p className="mt-1 text-sm text-gray-500">{description}</p>}
        </div>
      )}

      <div className={paddingClasses[padding]}>{children}</div>

      {footer && (
        <div className={`${paddingClasses[padding]} border-t border-gray-200 bg-gray-50`}>
          {footer}
        </div>
      )}
    </div>
  );
}
