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
  const [slotWidth, setSlotWidth] = useState<number>(80); // Default 80px per hour
  const [show24Hours, setShow24Hours] = useState<boolean>(false); // Default to business hours (6 AM - 7 PM)
  const [containerWidth, setContainerWidth] = useState<number>(0); // Measured container width

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

  // Zoom handlers
  const handleZoomIn = () => {
    setSlotWidth(prev => Math.min(prev + 20, 200)); // Max 200px
  };

  const handleZoomOut = () => {
    setSlotWidth(prev => Math.max(prev - 20, 40)); // Min 40px
  };

  const handleFitAll = () => {
    // Use measured container width if available, otherwise estimate
    const measuredWidth = containerWidth > 0 ? containerWidth : window.innerWidth - 400;

    // Room column width: w-52 = 13rem = 208px
    const roomColumnWidth = 208;

    // Get number of hours based on current toggle state
    const hourSlots = show24Hours ? 24 : 13;

    // Calculate available width for timeline slots
    // Note: We don't subtract scrollbar width from measured container width
    // because clientWidth already excludes the scrollbar
    const availableWidth = measuredWidth - roomColumnWidth;

    // Calculate fit width with minimum of 40px per slot
    // We use floor to ensure slots fit within the container
    const fitWidth = Math.max(Math.floor(availableWidth / hourSlots), 40);

    setSlotWidth(fitWidth);
  };

  const handleToggle24Hours = (checked: boolean) => {
    setShow24Hours(checked);
    // Optionally adjust slot width when toggling to maintain reasonable size
    if (checked && slotWidth > 100) {
      // When switching to 24 hours, reduce slot width if it's too large
      setSlotWidth(Math.min(slotWidth, 80));
    }
  };

  const handleContainerMeasured = (width: number) => {
    setContainerWidth(width);
  };

  // Slot click handler - now receives start and end dates directly from TimelineRow
  const handleSlotClick = (room: Room, startDate: Date, endDate: Date) => {
    onSlotSelect(room, startDate, endDate);
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
        onZoomIn={handleZoomIn}
        onZoomOut={handleZoomOut}
        onFitAll={handleFitAll}
        currentZoom={slotWidth}
        show24Hours={show24Hours}
        onToggle24Hours={handleToggle24Hours}
      />
      <TimelineGrid
        rooms={rooms}
        bookings={bookings}
        dayStart={dateRange.start}
        onEventClick={onEventClick}
        onSlotClick={handleSlotClick}
        slotWidth={slotWidth}
        show24Hours={show24Hours}
        onContainerMeasured={handleContainerMeasured}
      />
    </div>
  );
}
