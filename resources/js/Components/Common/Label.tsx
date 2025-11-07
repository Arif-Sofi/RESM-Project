import React, { LabelHTMLAttributes } from 'react';

interface LabelProps extends LabelHTMLAttributes<HTMLLabelElement> {
  required?: boolean;
  value?: string;
}

export default function Label({
  required = false,
  value,
  className = '',
  children,
  ...props
}: LabelProps) {
  return (
    <label
      className={`block text-sm font-medium text-gray-700 ${className}`}
      {...props}
    >
      {value || children}
      {required && <span className="text-red-500 ml-1">*</span>}
    </label>
  );
}
