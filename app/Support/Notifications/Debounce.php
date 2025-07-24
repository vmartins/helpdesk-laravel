<?php

namespace App\Support\Notifications;

use Illuminate\Support\Facades\Cache;

trait Debounce
{
    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $via = [];
        foreach ($this->viaDebounce($notifiable) as $channel => $implements) {
            if ($implements) {
                $wait = 0;
                $waits = $this->viaDebounceWait($notifiable);
                if (array_key_exists($channel, $waits)) {
                    $wait = $waits[$channel];
                }

                if (Cache::lock($this->getDebounceCacheKey($notifiable, $channel), $wait)->get()) {
                    $via[] = $channel;
                }
            } else {
                $via[] = $channel;
            }
        }

        return $via;
    }
}