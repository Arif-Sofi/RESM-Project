import React, { InputHTMLAttributes } from 'react';

interface CheckboxProps extends InputHTMLAttributes<HTMLInputElement> {
  label?: string;
  description?: string;
  error?: string;
}

export default function Checkbox({
  label,
  description,
  error,
  className = '',
  id,
  ...props
}: CheckboxProps) {
  const checkboxId = id || label?.toLowerCase().replace(/\s+/g, '-');

  return (
    <div className="relative flex items-start">
      <div className="flex items-center h-5">
        <input
          id={checkboxId}
          type="checkbox"
          className={`
            h-4 w-4 rounded border-gray-300 text-indigo-600
            focus:ring-indigo-500 focus:ring-offset-0 focus:ring-2
            ${error ? 'border-red-300' : ''}
            ${className}
          `}
          {...props}
        />
      </div>
      {(label || description) && (
        <div className="ml-3 text-sm">
          {label && (
            <label
              htmlFor={checkboxId}
              className={`font-medium ${error ? 'text-red-600' : 'text-gray-700'}`}
            >
              {label}
            </label>
          )}
          {description && (
            <p className="text-gray-500">{description}</p>
          )}
          {error && (
            <p className="mt-1 text-sm text-red-600">{error}</p>
          )}
        </div>
      )}
    </div>
  );
}
