<x-mail::message>
# {{ __('updates.email_header') }}

@if(($updateData['type'] ?? '') === 'manual')
## {{ $updateData['title'] ?? 'Update' }}
{{ $updateData['message'] ?? '' }}
@else
{{ __($updateData['key'], $updateData['params'] ?? []) }}
@endif

<x-mail::button :url="$updateData['url'] ?? route('listings.show', $listing)">
{{ __('updates.view_listing') }}
</x-mail::button>

{{ __('updates.footer') }}
</x-mail::message>