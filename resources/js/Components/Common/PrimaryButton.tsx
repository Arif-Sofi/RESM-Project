import React, { ButtonHTMLAttributes } from 'react';
import Button from './Button';

interface PrimaryButtonProps extends ButtonHTMLAttributes<HTMLButtonElement> {
  size?: 'sm' | 'md' | 'lg';
  isLoading?: boolean;
}

export default function PrimaryButton({ ...props }: PrimaryButtonProps) {
  return <Button variant="primary" {...props} />;
}
