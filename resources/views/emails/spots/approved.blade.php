@component('mail::message')
# Congratulations!

Your <b>{{ $spot->name }} Spot</b> was approved by {{ env('ORG_SHORT_NAME') }}'s moderators. It is now published for all to see.<br />

Thank you for your contribution to the community.

Thanks,<br />
{{ env('ORG_NAME') }}
@endcomponent
