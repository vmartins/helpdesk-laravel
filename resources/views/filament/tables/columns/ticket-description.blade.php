@php
    $openedOn = __("opened on");
    $createdAt = $ticket->created_at->format(app(\App\Settings\GeneralSettings::class)->date_format);
@endphp
<span style='font-size: 12px;'>
    #{{ $ticket->id }} · {{ $ticket->owner->name }} {{ $openedOn }} {{ $createdAt }}
</span>