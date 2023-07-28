function nextWeek(callback) {
    $(this).prop('disabled', true);
    let url = $('#next-week').data('url');
    $.ajax({
        url: url,
        type: 'POST',
        success: function (result) {
            let html = '';
            html += '<div class="col-5">';
            html += result.teams_table;
            html += '</div>';

            html += '<div class="col-4">';
            html += result.match_results_table;
            html += '</div>';

            html += '<div class="col-3">';
            html += result.probabilities_table;
            html += '</div>';
            $('#tables').append(html);

            if (result.season_finished) {
                $('#next-week').prop('disabled', true);
                $('#play-all').prop('disabled', true);
                $('#new-season-form').show();
            } else {
                $('#next-week').prop('disabled', false);
                $('#play-all').prop('disabled', false);
                $('#new-season-form').hide();
            }
            callback(result.season_finished);
        }
    });
}
$(document).ready(function () {
    $('#next-week').click(function () {
        nextWeek(null);
    });

    $('#play-all').click(function () {
            nextWeek(function (season_finished) {
                if (!season_finished) {
                    $('#play-all').click();
                } else {
                    $('#new-season-form').show();
                }
            });
        }
    );
});
