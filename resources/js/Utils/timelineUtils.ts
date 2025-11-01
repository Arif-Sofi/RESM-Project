import {
  format,
  startOfDay,
  endOfDay,
  startOfWeek,
  endOfWeek,
  startOfMonth,
  endOfMonth,
  addDays,
  addWeeks,
  addMonths,
  subDays,
  subWeeks,
  subMonths,
  isSameDay,
  isWithinInterval,
  differenceInMinutes,
  parseISO,
  setHours,
  setMinutes,
  isBefore,
  isAfter,
} from 'date-fns';

// Re-export commonly used date-fns functions for convenience
export {
  format,
  isSameDay,
  setHours,
  setMinutes,
  isBefore,
  isAfter,
  isWithinInterval,
  differenceInMinutes,
} from 'date-fns';

export type ViewType = 'day' | 'week' | 'month';

export interface TimeSlot {
  hour: number;
  label: string;
  date: Date;
}

export interface DateRange {
  start: Date;
  end: Date;
}

export interface Position {
  left: number;
  width: number;
}

// Business hours configuration
export const BUSINESS_HOURS = {
  startHour: 8,  // 8 AM
  endHour: 18,   // 6 PM
  slotDuration: 60, // minutes
};

/**
 * Get date range for a given view type and current date
 */
export function getDateRangeForView(date: Date, view: ViewType): DateRange {
  switch (view) {
    case 'day':
      return {
        start: startOfDay(date),
        end: endOfDay(date),
      };
    case 'week':
      return {
        start: startOfWeek(date, { weekStartsOn: 1 }), // Start on Monday
        end: endOfWeek(date, { weekStartsOn: 1 }),
      };
    case 'month':
      return {
        start: startOfMonth(date),
        end: endOfMonth(date),
      };
  }
}

/**
 * Navigate to next period
 */
export function goToNext(date: Date, view: ViewType): Date {
  switch (view) {
    case 'day':
      return addDays(date, 1);
    case 'week':
      return addWeeks(date, 1);
    case 'month':
      return addMonths(date, 1);
  }
}

/**
 * Navigate to previous period
 */
export function goToPrevious(date: Date, view: ViewType): Date {
  switch (view) {
    case 'day':
      return subDays(date, 1);
    case 'week':
      return subWeeks(date, 1);
    case 'month':
      return subMonths(date, 1);
  }
}

/**
 * Generate time slots for a single day
 */
export function generateTimeSlots(date: Date): TimeSlot[] {
  const slots: TimeSlot[] = [];
  const { startHour, endHour } = BUSINESS_HOURS;

  for (let hour = startHour; hour <= endHour; hour++) {
    slots.push({
      hour,
      label: format(setHours(setMinutes(date, 0), hour), 'h a'),
      date: setHours(setMinutes(date, 0), hour),
    });
  }

  return slots;
}

/**
 * Get all days in the current view
 */
export function getDaysInView(dateRange: DateRange, view: ViewType): Date[] {
  const days: Date[] = [];
  let current = dateRange.start;

  while (isBefore(current, dateRange.end) || isSameDay(current, dateRange.end)) {
    days.push(current);
    current = addDays(current, 1);
  }

  return days;
}

/**
 * Calculate position and width of an event on the timeline
 * Returns percentage values for CSS
 */
export function calculateEventPosition(
  eventStart: Date,
  eventEnd: Date,
  dayStart: Date,
  view: ViewType
): Position {
  const { startHour, endHour } = BUSINESS_HOURS;

  // Create boundary times for the day
  const boundaryStart = setHours(setMinutes(dayStart, 0), startHour);
  const boundaryEnd = setHours(setMinutes(dayStart, 0), endHour);

  // Clamp event times to business hours
  const clampedStart = isBefore(eventStart, boundaryStart) ? boundaryStart : eventStart;
  const clampedEnd = isAfter(eventEnd, boundaryEnd) ? boundaryEnd : eventEnd;

  // Calculate total business hours in minutes
  const totalMinutes = (endHour - startHour) * 60;

  // Calculate offset from start of business hours
  const startOffset = differenceInMinutes(clampedStart, boundaryStart);
  const duration = differenceInMinutes(clampedEnd, clampedStart);

  // Convert to percentage
  const left = (startOffset / totalMinutes) * 100;
  const width = (duration / totalMinutes) * 100;

  return { left, width };
}

/**
 * Check if a time is within business hours
 */
export function isWithinBusinessHours(date: Date): boolean {
  const hour = date.getHours();
  return hour >= BUSINESS_HOURS.startHour && hour < BUSINESS_HOURS.endHour;
}

/**
 * Check if a date is in the past
 */
export function isPastDate(date: Date): boolean {
  return isBefore(date, new Date());
}

/**
 * Round time to nearest slot (e.g., nearest hour)
 */
export function roundToNearestSlot(date: Date): Date {
  const minutes = date.getMinutes();
  const roundedMinutes = Math.round(minutes / BUSINESS_HOURS.slotDuration) * BUSINESS_HOURS.slotDuration;
  return setMinutes(date, roundedMinutes % 60);
}

/**
 * Format date range for header display
 */
export function formatDateRangeHeader(dateRange: DateRange, view: ViewType): string {
  switch (view) {
    case 'day':
      return format(dateRange.start, 'MMMM d, yyyy');
    case 'week':
      if (isSameDay(startOfMonth(dateRange.start), startOfMonth(dateRange.end))) {
        return format(dateRange.start, 'MMMM yyyy');
      }
      return `${format(dateRange.start, 'MMM d')} - ${format(dateRange.end, 'MMM d, yyyy')}`;
    case 'month':
      return format(dateRange.start, 'MMMM yyyy');
  }
}

/**
 * Convert time string to minutes since start of day
 */
export function timeToMinutes(time: string): number {
  const date = parseISO(time);
  return date.getHours() * 60 + date.getMinutes();
}

/**
 * Check if two events overlap
 */
export function eventsOverlap(
  event1Start: Date,
  event1End: Date,
  event2Start: Date,
  event2End: Date
): boolean {
  return (
    isBefore(event1Start, event2End) && isAfter(event1End, event2Start)
  );
}

/**
 * Get column index for a date in week/month view
 */
export function getColumnIndex(date: Date, viewDays: Date[]): number {
  return viewDays.findIndex(day => isSameDay(day, date));
}

/**
 * Parse ISO date string to Date object
 */
export function parseISOSafe(dateString: string): Date {
  return parseISO(dateString);
}

/**
 * Format time for display (e.g., "2:00 PM")
 */
export function formatTime(date: Date): string {
  return format(date, 'h:mm a');
}

/**
 * Format date for display (e.g., "Jan 15")
 */
export function formatDate(date: Date): string {
  return format(date, 'MMM d');
}

/**
 * Calculate the number of columns needed for the view
 */
export function getColumnCount(view: ViewType): number {
  switch (view) {
    case 'day':
      return 1;
    case 'week':
      return 7;
    case 'month':
      return 31; // Max days in a month
  }
}

/**
 * Check if a date falls on a weekend
 */
export function isWeekend(date: Date): boolean {
  const day = date.getDay();
  return day === 0 || day === 6; // Sunday or Saturday
}
