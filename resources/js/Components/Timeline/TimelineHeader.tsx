import React from 'react';
import { ViewType, DateRange, formatDateRangeHeader } from '@/Utils/timelineUtils';
import Button from '@/Components/Common/Button';

interface TimelineHeaderProps {
  dateRange: DateRange;
  currentView: ViewType;
  onViewChange: (view: ViewType) => void;
  onPrevious: () => void;
  onNext: () => void;
  onToday: () => void;
  onZoomIn?: () => void;
  onZoomOut?: () => void;
  onFitAll?: () => void;
  currentZoom?: number;
  show24Hours?: boolean;
  onToggle24Hours?: (checked: boolean) => void;
}

export default function TimelineHeader({
  dateRange,
  currentView,
  onViewChange,
  onPrevious,
  onNext,
  onToday,
  onZoomIn,
  onZoomOut,
  onFitAll,
  currentZoom,
  show24Hours,
  onToggle24Hours,
}: TimelineHeaderProps) {
  const headerText = formatDateRangeHeader(dateRange, currentView);

  return (
    <div className="bg-white border-b border-gray-200 p-4">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        {/* Left: Navigation */}
        <div className="flex items-center gap-3">
          <Button onClick={onToday} variant="secondary" size="sm">
            Today
          </Button>
          <div className="flex items-center gap-1">
            <button
              onClick={onPrevious}
              className="p-2 rounded hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              aria-label="Previous"
            >
              <svg className="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 19l-7-7 7-7" />
              </svg>
            </button>
            <button
              onClick={onNext}
              className="p-2 rounded hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
              aria-label="Next"
            >
              <svg className="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
              </svg>
            </button>
          </div>
          <h2 className="text-xl font-semibold text-gray-900">{headerText}</h2>

          {/* Zoom Controls */}
          {onZoomIn && onZoomOut && onFitAll && (
            <div className="flex items-center gap-1 border-l border-gray-300 pl-3 ml-3">
              <button
                onClick={onZoomOut}
                className="p-2 rounded hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                aria-label="Zoom Out"
                title="Zoom Out"
              >
                <svg className="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7" />
                </svg>
              </button>
              <button
                onClick={onFitAll}
                className="px-3 py-2 text-xs font-medium rounded hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-gray-700 whitespace-nowrap"
                title="Fit All Hours"
              >
                Fit All
              </button>
              <button
                onClick={onZoomIn}
                className="p-2 rounded hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                aria-label="Zoom In"
                title="Zoom In"
              >
                <svg className="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                </svg>
              </button>
            </div>
          )}

          {/* 24 Hour Toggle */}
          {onToggle24Hours && (
            <div className="flex items-center gap-2 border-l border-gray-300 pl-3 ml-3">
              <input
                type="checkbox"
                id="show24Hours"
                checked={show24Hours || false}
                onChange={(e) => onToggle24Hours(e.target.checked)}
                className="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
              />
              <label htmlFor="show24Hours" className="text-sm text-gray-700 whitespace-nowrap cursor-pointer">
                Show 24 Hours
              </label>
            </div>
          )}
        </div>

        {/* Right: View Switcher and Legend */}
        <div className="flex items-center gap-4 flex-wrap">
          {/* View Switcher */}
          <div className="flex rounded-md shadow-sm" role="group">
            <button
              type="button"
              onClick={() => onViewChange('day')}
              className={`
                px-4 py-2 text-sm font-medium rounded-l-md border
                ${
                  currentView === 'day'
                    ? 'bg-indigo-600 text-white border-indigo-600 z-10'
                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                }
                focus:z-10 focus:outline-none focus:ring-2 focus:ring-indigo-500
              `}
            >
              Day
            </button>
            <button
              type="button"
              onClick={() => onViewChange('week')}
              className={`
                px-4 py-2 text-sm font-medium border-t border-b -ml-px
                ${
                  currentView === 'week'
                    ? 'bg-indigo-600 text-white border-indigo-600 z-10'
                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                }
                focus:z-10 focus:outline-none focus:ring-2 focus:ring-indigo-500
              `}
            >
              Week
            </button>
            <button
              type="button"
              onClick={() => onViewChange('month')}
              className={`
                px-4 py-2 text-sm font-medium rounded-r-md border -ml-px
                ${
                  currentView === 'month'
                    ? 'bg-indigo-600 text-white border-indigo-600 z-10'
                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
                }
                focus:z-10 focus:outline-none focus:ring-2 focus:ring-indigo-500
              `}
            >
              Month
            </button>
          </div>

          {/* Status Legend */}
          <div className="flex items-center gap-3 text-xs flex-shrink-0">
            <div className="flex items-center gap-1">
              <div className="w-3 h-3 rounded bg-green-500 flex-shrink-0"></div>
              <span className="text-gray-600 whitespace-nowrap">Approved</span>
            </div>
            <div className="flex items-center gap-1">
              <div className="w-3 h-3 rounded bg-yellow-500 flex-shrink-0"></div>
              <span className="text-gray-600 whitespace-nowrap">Pending</span>
            </div>
            <div className="flex items-center gap-1">
              <div className="w-3 h-3 rounded bg-red-500 flex-shrink-0"></div>
              <span className="text-gray-600 whitespace-nowrap">Rejected</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
