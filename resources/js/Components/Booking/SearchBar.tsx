import React from 'react';

interface Filters {
  hasProjector: boolean;
  minCapacity: number | null;
}

interface Props {
  value: string;
  onChange: (value: string) => void;
  filters: Filters;
  onFiltersChange: (filters: Filters) => void;
}

export default function SearchBar({ value, onChange, filters, onFiltersChange }: Props) {
  const handleClearFilters = () => {
    onChange('');
    onFiltersChange({
      hasProjector: false,
      minCapacity: null,
    });
  };

  const hasActiveFilters = value || filters.hasProjector || filters.minCapacity;

  return (
    <div className="mt-4 space-y-3">
      {/* Search Input */}
      <div className="relative">
        <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <svg className="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
        </div>
        <input
          type="text"
          id="room-search"
          name="search"
          value={value}
          onChange={(e) => onChange(e.target.value)}
          autoComplete="off"
          className="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
          placeholder="Search rooms..."
        />
      </div>

      {/* Filters */}
      <div className="flex items-center space-x-3 flex-wrap gap-2">
        <label className="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-md text-sm hover:bg-gray-50 cursor-pointer">
          <input
            type="checkbox"
            id="has-projector"
            name="hasProjector"
            checked={filters.hasProjector}
            onChange={(e) => onFiltersChange({ ...filters, hasProjector: e.target.checked })}
            className="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
          />
          <span className="ml-2 text-gray-700">Has Projector</span>
        </label>

        <div className="inline-flex items-center px-3 py-1.5 bg-white border border-gray-300 rounded-md text-sm">
          <label htmlFor="min-capacity" className="text-gray-700 mr-2">
            Min Capacity:
          </label>
          <input
            id="min-capacity"
            type="number"
            min="1"
            value={filters.minCapacity || ''}
            onChange={(e) => onFiltersChange({
              ...filters,
              minCapacity: e.target.value ? parseInt(e.target.value) : null
            })}
            className="w-16 px-2 py-0.5 border border-gray-300 rounded text-sm focus:ring-indigo-500 focus:border-indigo-500"
            placeholder="Any"
          />
        </div>

        {hasActiveFilters && (
          <button
            onClick={handleClearFilters}
            className="inline-flex items-center px-3 py-1.5 text-sm text-indigo-600 hover:text-indigo-800"
          >
            <svg className="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
            </svg>
            Clear Filters
          </button>
        )}
      </div>
    </div>
  );
}
