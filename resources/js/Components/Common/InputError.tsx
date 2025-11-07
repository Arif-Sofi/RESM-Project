import React, { HTMLAttributes } from 'react';

interface InputErrorProps extends HTMLAttributes<HTMLParagraphElement> {
  message?: string;
}

export default function InputError({
  message,
  className = '',
  ...props
}: InputErrorProps) {
  return message ? (
    <p className={`text-sm text-red-600 mt-1 ${className}`} {...props}>
      {message}
    </p>
  ) : null;
}
