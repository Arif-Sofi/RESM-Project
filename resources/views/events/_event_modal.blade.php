<!-- イベント追加用モーダル -->
<div class="modal fade" id="createEventModal" tabindex="-1" aria-labelledby="createEventModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createEventModalLabel">新しいイベントを追加</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createEventForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="eventTitle" class="form-label">タイトル</label>
                        <input type="text" class="form-control" id="eventTitle" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="eventDescription" class="form-label">説明</label>
                        <textarea class="form-control" id="eventDescription" name="description"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="eventStart" class="form-label">開始日時</label>
                        <input type="datetime-local" class="form-control" id="eventStart" name="start_at" required>
                    </div>
                    <div class="mb-3">
                        <label for="eventEnd" class="form-label">終了日時 (任意)</label>
                        <input type="datetime-local" class="form-control" id="eventEnd" name="end_at">
                    </div>
                    <div class="mb-3">
                        <label for="eventStaff" class="form-label">参加スタッフ (複数選択可)</label>
                        <select class="form-select" id="eventStaff" name="staff[]" multiple
                            aria-label="Select Staff" style="height: 150px;">
                            @isset($users)
                                @foreach ($users as $user)
                                    @if (Auth::id() !== $user->id) {{-- 自分自身を除外 --}}
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endif
                                @endforeach
                            @endisset
                        </select>
                        <small class="form-text text-muted">Ctrl (または Command) キーを押しながらクリックすると複数選択できます。</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                    <button type="submit" class="btn btn-primary">保存</button>
                </div>
            </form>
        </div>
    </div>
</div>
