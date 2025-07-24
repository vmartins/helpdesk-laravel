<?php

namespace App\Support\Notifications;

interface ShouldBeDebounce
{
    public function getDebounceCacheKey(object $notifiable, string $channel): string;

    public function viaDebounceWait(object $notifiable): array;
}