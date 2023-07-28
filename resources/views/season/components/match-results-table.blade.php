<table class="table table-striped border">
    <thead>
    <tr>
        <td colspan="3" class="text-center">Match Results</td>
    </tr>
    <tr>
        <td colspan="3"
            class="text-center">{{(new NumberFormatter('en_US', NumberFormatter::ORDINAL))->format($week)}}
            Week Match Results
        </td>
    </tr>
    </thead>
    <tbody>
    @foreach($games as $game)
        <tr>
            <td>{{$game->hostTeam->name}}</td>
            <td>{{$game->host_team_score}} : {{$game->guest_team_score}}</td>
            <td>{{$game->guestTeam->name}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
