<p>
    Please fill in your Round {{ $round->name }} data for {{ strtoupper($team->name) }}.
</p>
<p>
    Click <a href="{{ env('APP_URL') . '/teams/' . $team->slug . '/rounds/' . $round->id}}">here</a> to fill in your round data.
</p>
<p>
    If the above link didn't work, please copy and paste the following into your Browser's address bar: <em>{{ env('APP_URL') . '/teams/' . $team->slug . '/rounds/' . $round->id}}</em>
</p>
<p>
    Regards,
    <br />
    Admin @ {{ env('APP_NAME') }}.
</p>
