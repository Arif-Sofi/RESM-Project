import React from 'react';
import { Room } from '@/Types/models';

interface Props {
  rooms: Room[];
  selectedRoomIds: number[];
  onSelectionChange: (roomIds: number[]) => void;
}

export default function RoomSidebar({ rooms, selectedRoomIds, onSelectionChange }: Props) {
  const handleRoomToggle = (roomId: number) => {
    if (selectedRoomIds.includes(roomId)) {
      // Remove from selection
      onSelectionChange(selectedRoomIds.filter(id => id !== roomId));
    } else {
      // Add to selection
      onSelectionChange([...selectedRoomIds, roomId]);
    }
  };

  const handleSelectAll = () => {
    if (selectedRoomIds.length === rooms.length) {
      // Deselect all
      onSelectionChange([]);
    } else {
      // Select all
      onSelectionChange(rooms.map(r => r.id));
    }
  };

  const allSelected = selectedRoomIds.length === rooms.length;
  const someSelected = selectedRoomIds.length > 0 && !allSelected;

  return (
    <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
      <div className="flex items-center justify-between mb-5">
        <h3 className="text-base font-semibold text-gray-900">Rooms</h3>
        <button
          onClick={handleSelectAll}
          className="text-sm text-indigo-600 hover:text-indigo-800 font-medium"
        >
          {allSelected ? 'Clear All' : 'Select All'}
        </button>
      </div>

      {selectedRoomIds.length > 0 && selectedRoomIds.length < rooms.length && (
        <div className="mb-4 px-3 py-2.5 bg-indigo-50 border border-indigo-100 rounded-md text-sm text-indigo-700">
          Showing {selectedRoomIds.length} of {rooms.length} rooms
        </div>
      )}

      <div className="space-y-3 max-h-[600px] overflow-y-auto pr-1">
        {rooms.map((room) => {
          const isSelected = selectedRoomIds.includes(room.id);

          return (
            <button
              key={room.id}
              onClick={() => handleRoomToggle(room.id)}
              className={`
                w-full text-left px-4 py-3.5 rounded-lg border transition-all
                ${isSelected
                  ? 'bg-indigo-50 border-indigo-300 shadow-sm'
                  : 'bg-white border-gray-200 hover:border-gray-300 hover:bg-gray-50'
                }
              `}
            >
              <div className="flex items-start justify-between">
                <div className="flex-1 min-w-0">
                  <div className="flex items-center mb-2.5">
                    <div className={`
                      w-5 h-5 rounded border mr-2.5 flex items-center justify-center flex-shrink-0
                      ${isSelected ? 'bg-indigo-600 border-indigo-600' : 'border-gray-300'}
                    `}>
                      {isSelected && (
                        <svg className="w-3.5 h-3.5 text-white" fill="currentColor" viewBox="0 0 12 12">
                          <path d="M10 3L4.5 8.5L2 6" stroke="currentColor" strokeWidth="2" fill="none" strokeLinecap="round" strokeLinejoin="round"/>
                        </svg>
                      )}
                    </div>
                    <span className="text-sm font-semibold text-gray-900 truncate">
                      {room.name}
                    </span>
                  </div>

                  <div className="ml-7 space-y-1.5">
                    <div className="flex items-center text-sm text-gray-600">
                      <svg className="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                      </svg>
                      <span>{room.capacity} capacity</span>
                    </div>

                    {room.building && (
                      <div className="flex items-center text-sm text-gray-600">
                        <svg className="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span>{room.building}</span>
                      </div>
                    )}

                    <div className="flex items-center flex-wrap gap-2 text-xs pt-1">
                      {room.has_projector && (
                        <span className="inline-flex items-center px-2 py-1 rounded-md bg-green-100 text-green-700 font-medium">
                          Projector
                        </span>
                      )}
                      {room.has_whiteboard && (
                        <span className="inline-flex items-center px-2 py-1 rounded-md bg-blue-100 text-blue-700 font-medium">
                          Whiteboard
                        </span>
                      )}
                    </div>
                  </div>
                </div>
              </div>
            </button>
          );
        })}
      </div>
    </div>
  );
}
