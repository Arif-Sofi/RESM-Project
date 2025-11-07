import React, { ButtonHTMLAttributes } from 'react';
import Button from './Button';

interface SecondaryButtonProps extends ButtonHTMLAttributes<HTMLButtonElement> {
  size?: 'sm' | 'md' | 'lg';
  isLoading?: boolean;
}

export default function SecondaryButton({ ...props }: SecondaryButtonProps) {
  return <Button variant="secondary" {...props} />;
}
