<table class="table table-striped-columns border">
    <thead>
    <tr>
        <td colspan="7" class="text-center">League Table</td>
    </tr>
    <tr>
        <th>Team</th>
        <th>PTS</th>
        <th>P</th>
        <th>W</th>
        <th>D</th>
        <th>L</th>
        <th>GD</th>
    </tr>
    </thead>
    <tbody>
    @foreach($teams[$week] as $team)
        <tr>
            <td>{{$team['name']}}</td>
            <td class="text-center">{{$team['points']}}</td>
            <td class="text-center">{{$team['games']}}</td>
            <td class="text-center">{{$team['wins']}}</td>
            <td class="text-center">{{$team['draws']}}</td>
            <td class="text-center">{{$team['losses']}}</td>
            <td class="text-center">{{$team['goals']}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
