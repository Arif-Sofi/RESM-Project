<x-modal name="editEventModal" maxWidth="xl" focusable>
    <form id="editEventForm">
        @csrf
        <div class="px-6 py-4">
            <div class="text-lg font-medium text-gray-900">
                {{ __('イベントを編集 ') }}
            </div>

            <div class="mt-4">
                {{-- Hidden input for event ID --}}
                <input type="hidden" id="editEventId" name="event_id">

                <div class="mb-3">
                    <x-input-label for="editEventTitle" value="タイトル" />
                    <x-text-input id="editEventTitle" class="block mt-1 w-full" type="text" name="title"
                        required />
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>
                <div class="mb-3">
                    <x-input-label for="editEventDescription" value="説明" />
                    <textarea id="editEventDescription"
                        class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        name="description" style="height: 5rem"></textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
                <div class="mb-3">
                    <x-input-label for="editEventStart" value="開始日時" />
                    <x-text-input id="editEventStart" class="block mt-1 w-full" type="datetime-local" name="start_at"
                        required />
                    <x-input-error :messages="$errors->get('start_at')" class="mt-2" />
                </div>
                {{-- <div class="mb-3">
                    <x-input-label for="editEventEnd" value="終了日時 (任意)" />
                    <x-text-input id="editEventEnd" class="block mt-1 w-full" type="datetime-local" name="end_at" />
                    <x-input-error :messages="$errors->get('end_at')" class="mt-2" />
                </div> --}}
                <div class="mb-3">
                    <div class="flex items-center justify-between">
                        <x-input-label for="editEventStaff" value="参加スタッフ (複数選択可)" />
                        <x-secondary-button id="cancelAllStaffButtonEdit" type="button" class="mr-2">
                            {{ __('全スタッフの選択を解除') }}
                        </x-secondary-button>
                        <x-secondary-button id="selectAllStaffButtonEdit" type="button">
                            {{ __('全スタッフを選択') }}
                        </x-secondary-button>
                    </div>
                    <select id="editEventStaff"
                        class="block mt-1 w-full rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                        name="staff[]" multiple aria-label="Select Staff" style="height: 20rem;">
                        @isset($users)
                            @foreach ($users as $user)
                                @if (Auth::id() !== $user->id)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endif
                            @endforeach
                        @endisset
                    </select>
                    <p class="text-sm text-gray-500 dark:text-gray-300">Ctrl (または Command) キーを押しながらクリックすると複数選択できます。</p>
                    <x-input-error :messages="$errors->get('staff')" class="mt-2" />
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between px-6 py-3 bg-gray-100 text-end">
            <x-danger-button id="deleteEventButton" type="button">
                {{ __('削除') }}
            </x-danger-button>
            <div>
                <x-primary-button type="submit" class="mr-3">
                    {{ __('更新') }}
                </x-primary-button>

                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('キャンセル') }}
                </x-secondary-button>
            </div>
        </div>
    </form>
</x-modal>
