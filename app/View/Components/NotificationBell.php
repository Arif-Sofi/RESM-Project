<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class NotificationBell extends Component
{
    public $notifications;

    public $unreadCount;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        if (auth()->check()) {
            $this->notifications = auth()->user()->unreadNotifications->take(5);
            $this->unreadCount = auth()->user()->unreadNotifications->count();
        } else {
            $this->notifications = collect();
            $this->unreadCount = 0;
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.notification-bell');
    }
}
