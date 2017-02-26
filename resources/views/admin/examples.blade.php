@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <a href="{{ route('admin.home') }}">&laquo; Dashboard</a>
            <br />
            <div class="panel panel-default">
                <div class="panel-heading">Valid Excel Spreadsheet Examples</div>

                <div class="panel-body">
                    <ul>
                        <li>
                            <strong>Teams</strong>
                             <p><a href="/admin_faq_excel_examples/AllValidTeams.xlsx" target="_blank">Download example of valid Player spreadsheet for import</a>.</p>
                        </li>
                        <li>
                            <strong>Players</strong>
                            <p><a href="/admin_faq_excel_examples/AllValidPlayers.xlsx" target="_blank">Download example of valid Player spreadsheet for import</a>.</p>
                            <p class="text-muted">Remember to create the necessary teams referenced in the Players spreadsheet before importing players.</p>
                        </li>

                        <li>
                            <strong>Rounds</strong>
                            <p><a href="/admin_faq_excel_examples/AllValidRounds.xlsx" target="_blank">Download example of valid Player spreadsheet for import</a>.</p>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
