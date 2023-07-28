<table class="table table-striped border">
    <thead>
    <tr>
        <td colspan="2"
            class="text-center">{{(new NumberFormatter('en_US', NumberFormatter::ORDINAL))->format($week)}}
            Week Predictions of Championship
        </td>
    </tr>
    </thead>
    <tbody>
    @foreach($probabilities[$week] as $teamName => $score)
        <tr>
            <td>{{$teamName}}</td>
            <td>%{{round($score * 100)}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
