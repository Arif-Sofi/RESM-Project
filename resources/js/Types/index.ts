export * from './models';
export * from './calendar';
export * from './api';

// Inertia shared props type
export interface SharedProps {
  auth: {
    user: {
      id: number;
      name: string;
      email: string;
    } | null;
  };
  flash: {
    success?: string;
    error?: string;
  };
}

// Page props extend from shared props
export interface PageProps<T = Record<string, unknown>> extends SharedProps {
  [key: string]: any;
}
