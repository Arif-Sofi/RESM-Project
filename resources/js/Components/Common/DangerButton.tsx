import React, { ButtonHTMLAttributes } from 'react';
import Button from './Button';

interface DangerButtonProps extends ButtonHTMLAttributes<HTMLButtonElement> {
  size?: 'sm' | 'md' | 'lg';
  isLoading?: boolean;
}

export default function DangerButton({ ...props }: DangerButtonProps) {
  return <Button variant="danger" {...props} />;
}
