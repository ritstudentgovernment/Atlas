@component('mail::message')
# Thank you for using {{config('app.name')}}

@if($spot->approved_classification)
Your <b>{{ $spot->name }} Spot</b> was determined by {{ env('ORG_SHORT_NAME') }}'s moderators to not be eligible for publishing. Please feel free to try again, ensuring you are abiding by our community guidelines.
@else
Your <b class="bold">{{ $spot->name }} Spot</b> has been removed by a moderator.
@endif

<br />
If you believe this was an error, please contact us at <a href="mailto:{{ env('CONTACT_EMAIL') }}" >{{ env('CONTACT_EMAIL') }}</a>.

Thanks,<br />
{{ env('ORG_NAME') }}
@endcomponent
