@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading"><h4>FAQ</h4></div>

                <div class="panel-body">
                    <h3>Your Team</h3>
                    <ul>
                        <li>
                            <strong>How do I join my Team?</strong>
                            <p>When you sign in for the first time, you will be taken to your <a href="{{ route('home') }}">Dashboard</a>, where you can find the "Find a Team" button.</p>
                            <p>Click this button and you will be taken to a listing of all available teams (teams that currently do not have a Coach).</p>
                            <p>Click the "Join" button for the Team that you are a Coach of and that's it.</p>
                            <p>If you visit your <a href="{{ route('home') }}">Dashboard</a>, you will now see your current Team.</p>
                        </li>
                        <li>
                            <strong>How do I "manage" my Team?</strong>
                             <p>To manage your Team, just click the "Manage" button in your <a href="{{ route('home') }}">Dashboard</a>. Alternatively, you can click the "My Team" link in the navigation bar (top of the page).</p>
                        </li>
                        <li>
                            <strong>What actions can I perform on a Team?</strong>
                            <p>You can add players, fill in round details and also leave the team, if need be.</p>
                        </li>
                    </ul>

                    <h3>Players</h3>
                    <ul>
                        <li>
                            <strong>What is temporary Player?</strong>
                            <p>A termporary Player is exactly the same as a normal Player, except that temporary players will be listed seperately on the same page.</p>
                            <p>The main goal of this seperation is to allow you, the Coach to find players that are more likely to be involved in many rounds quicker, when filling in Round details.</p>
                        </li>
                        <li>
                            <strong>Why can't I delete a Player</strong>
                             <p>If a Player has played in atleast one round, you may not delete them. This purpose of this is to keep all information available to the administrative staff.</p>
                        </li>
                    </ul>

                    <h3>Rounds</h3>
                    <ul>
                        <li>
                            <strong>What is a Round?</strong>
                            <p>A Round is basically a match that consists of a date, players from your Team and attributes for each of the players.</p>
                            <p>As administrators make rounds available, you may fill in your team's details.</p>
                        </li>
                        <li>
                            <strong>Why is there a default date for each Round?</strong>
                             <p>Administrators will set a default date for each Round, but you may change this date within the Round data entry page to suit a particular Round your Team played in.</p>
                            <p>If you do not fill in your Team's data for a Round, a reminder email will be sent to your email address associated with your {{ config('app.name', 'Laravel') }} account.</p>
                        </li>
                        <li>
                            <strong>What details do I have to provide when I fill in a rounds' data?</strong>
                             <p>You will be offered to select (click) on players that played in a particular Round, as well as how many quarters each Player played and select which were the best and second best players of the game.</p>
                        </li>
                        <li>
                            <strong>What happens if I make a mistake with a rounds data entry?</strong>
                             <p>If you make a mistake, just go back and make the correct selections and attributes and update the details.</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
