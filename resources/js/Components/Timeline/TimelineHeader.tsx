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
}

export default function TimelineHeader({
  dateRange,
  currentView,
  onViewChange,
  onPrevious,
  onNext,
  onToday,
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
        </div>

        {/* Right: View Switcher and Legend */}
        <div className="flex items-center gap-4">
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
          <div className="flex items-center gap-3 text-xs">
            <div className="flex items-center gap-1">
              <div className="w-3 h-3 rounded bg-green-500"></div>
              <span className="text-gray-600">Approved</span>
            </div>
            <div className="flex items-center gap-1">
              <div className="w-3 h-3 rounded bg-yellow-500"></div>
              <span className="text-gray-600">Pending</span>
            </div>
            <div className="flex items-center gap-1">
              <div className="w-3 h-3 rounded bg-red-500"></div>
              <span className="text-gray-600">Rejected</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
