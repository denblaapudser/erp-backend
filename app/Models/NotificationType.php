<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationType extends Model
{
    public $timestamps = false;

    protected static function booted()
    {
        static::created(function ($notificationType) {
            $users = User::all();
            foreach ($users as $user) {
                $user->notificationTypes()->attach($notificationType->id, ['is_active' => $notificationType->send_by_default]);
            }
        });
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_notification_types')->withPivot('is_active');
    }
}
