export * from './models';
export * from './calendar';
export * from './api';

import { User } from './models';

// Inertia shared props type
export interface SharedProps {
  auth: {
    user: User;
  };
  flash?: {
    success?: string;
    error?: string;
    message?: string;
  };
}

// Page props extend from shared props
export interface PageProps<T = Record<string, unknown>> extends SharedProps {
  [key: string]: any;
}
