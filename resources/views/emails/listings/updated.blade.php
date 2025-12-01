<x-mail::message>
# {{ __('updates.email_header') }}

{{-- Logic to display Manual Text OR Translated System Message --}}
@if($update->type === 'manual')
## {{ $update->title }}
{{ $update->content }}
@else
{{-- Translate the key using the stored attributes --}}
{{ __($update->translation_key, $update->attributes) }}
@endif

<x-mail::button :url="route('listings.show', $listing)">
{{ __('updates.view_listing') }}
</x-mail::button>

{{ __('updates.footer') }}
</x-mail::message>