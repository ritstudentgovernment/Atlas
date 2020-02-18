@component('mail::message')
# Success!
@if($spot->approved_classification_id)
Your <b>{{ $spot->name }} Spot</b> has been successfully submitted for review, but it isn't published yet. You'll get an email when it is approved by {{ env('ORG_SHORT_NAME') }}'s moderators!
@else
Your <b>{{ $spot->name }} Spot</b> has been successfully created.
@endif

Thanks,<br />
{{ env('ORG_NAME') }}
@endcomponent`
