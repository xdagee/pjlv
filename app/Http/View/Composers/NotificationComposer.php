<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationComposer
{
    /**
     * Bind data to the view.
     *
     * @param  \Illuminate\View\View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $user = Auth::user();

        if ($user) {
            $unreadNotifications = Notification::where('user_id', $user->id)
                ->whereNull('read_at')
                ->orderBy('created_at', 'desc')
                ->get();

            $view->with('unreadNotifications', $unreadNotifications);
        } else {
            $view->with('unreadNotifications', collect());
        }
    }
}
