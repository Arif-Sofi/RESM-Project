import React, { useState } from 'react';
import { Room, Booking } from '@/Types/models';
import TimelineHeader from './TimelineHeader';
import TimelineGrid from './TimelineGrid';
import {
  ViewType,
  getDateRangeForView,
  goToNext,
  goToPrevious,
  parseISOSafe,
} from '@/Utils/timelineUtils';

interface CustomTimelineProps {
  rooms: Room[];
  bookings: Booking[];
  initialDateRange: { start: string; end: string };
  onEventClick: (booking: Booking) => void;
  onSlotSelect: (room: Room, start: Date, end: Date) => void;
}

export default function CustomTimeline({
  rooms,
  bookings,
  initialDateRange,
  onEventClick,
  onSlotSelect,
}: CustomTimelineProps) {
  // Initialize with day view and current date
  const [currentView, setCurrentView] = useState<ViewType>('day');
  const [currentDate, setCurrentDate] = useState<Date>(
    initialDateRange?.start ? parseISOSafe(initialDateRange.start) : new Date()
  );

  // Calculate date range for current view
  const dateRange = getDateRangeForView(currentDate, currentView);

  // Navigation handlers
  const handlePrevious = () => {
    setCurrentDate(prev => goToPrevious(prev, currentView));
  };

  const handleNext = () => {
    setCurrentDate(prev => goToNext(prev, currentView));
  };

  const handleToday = () => {
    setCurrentDate(new Date());
  };

  const handleViewChange = (view: ViewType) => {
    setCurrentView(view);
  };

  // Slot click handler - creates a 1-hour booking slot
  const handleSlotClick = (room: Room, date: Date) => {
    const start = date;
    const end = new Date(date.getTime() + 60 * 60 * 1000); // Add 1 hour
    onSlotSelect(room, start, end);
  };

  // Note: For now, we only support day view.
  // Week and month views would require different grid layouts
  if (currentView !== 'day') {
    return (
      <div className="space-y-4">
        <TimelineHeader
          dateRange={dateRange}
          currentView={currentView}
          onViewChange={handleViewChange}
          onPrevious={handlePrevious}
          onNext={handleNext}
          onToday={handleToday}
        />
        <div className="bg-white border rounded-lg p-12 text-center">
          <svg className="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <h3 className="text-lg font-medium text-gray-900 mb-2">
            {currentView === 'week' ? 'Week' : 'Month'} View Coming Soon
          </h3>
          <p className="text-gray-500 mb-4">
            The {currentView} view is currently under development.
          </p>
          <button
            onClick={() => setCurrentView('day')}
            className="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
          >
            Switch to Day View
          </button>
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-4">
      <TimelineHeader
        dateRange={dateRange}
        currentView={currentView}
        onViewChange={handleViewChange}
        onPrevious={handlePrevious}
        onNext={handleNext}
        onToday={handleToday}
      />
      <TimelineGrid
        rooms={rooms}
        bookings={bookings}
        dayStart={dateRange.start}
        onEventClick={onEventClick}
        onSlotClick={handleSlotClick}
      />
    </div>
  );
}
