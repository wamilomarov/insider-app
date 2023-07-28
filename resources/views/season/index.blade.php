@extends('template')

@section('content')
    @if(!is_null($season))
        <div class="grid">
            <div class="col-9">
                @foreach($season->games->groupBy('week')->sortBy('*.week') as $week => $gamesPerWeek)
                    <div class="row" id="tables">
                        <div class="col-5">
                            @include('season.components.teams-table', ['teams' => $teams, 'week' => $week])
                        </div>
                        <div class="col-4">
                            @include('season.components.match-results-table', ['games' => $gamesPerWeek, 'week' => $week])
                        </div>
                        <div class="col-3">
                            @include('season.components.probabilities-table', ['probabilities' => $probabilities, 'week' => $week])
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="row">
                <button class="btn col-2 m-1" type="submit" id="next-week" @if($season->is_finished) disabled @endif data-url="{{route('season.next-week', ['season' => $season->id])}}">Next Week</button>
                <button class="btn col-2 m-1" type="submit" id="play-all" @if($season->is_finished) disabled @endif>Play All</button>

                <form action="{{route('season.new')}}" method="post" class="col-3 mr-1"
                      id="new-season-form"
                      @if(!is_null($season) && !$season->is_finished) style="display: none" @endif>
                    @csrf
                    <button type="submit" class="btn btn-primary">New season</button>
                </form>
            </div>

            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
            <script src="{{asset('assets/js/season.js')}}"></script>

    @endif
@endsection
