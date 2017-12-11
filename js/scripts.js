window.setTimeout(function() {
    $(".alert-banner").fadeTo(700, 0).slideUp(300, function(){
        $(this).remove();
    });
}, 5000);

var loadPuzzleData = function(url, callback) {
    $.get(url, {}, function(response) {
        loadTemplate(response, callback);
    });
};

var loadTemplate = function(response, callback) {
    var data = response;
    var now = Date.now();
    $.get('/templates/puzzle_row.mst', function(template) {
        $.each(data, function(key, puzzleData) {
            var ssID = puzzleData['SpreadsheetId'];
            ssID = ssID.replace(/^.+ccc\?key=/, "");
            puzzleData['SpreadsheetURL'] = "https://docs.google.com/spreadsheets/d/" + ssID;

            puzzleData['SinceCreated'] = now - new Date(puzzleData['CreatedAt']);
            puzzleData['SinceUpdated'] = now - new Date(puzzleData['UpdatedAt']);

            var rendered = Mustache.render(template, puzzleData);

            var parents = puzzleData['PuzzleParents'];
            if (!parents || parents.length == 0) {
                parents = [{ParentId: 0}];
            }

            $.each(parents, function(key, parent) {
                var tableID = parent['ParentId'];
                var tbody = $('#table-' + tableID).find('tbody');
                if (tableID == puzzleData['Id']) {
                    tbody.prepend(rendered);
                } else {
                    tbody.append(rendered);
                }
            });
        });
        if (callback) {
            callback();
        }
    });
};

$(function() {
    $('[data-filter-list] a').click(function() {
        $(this).tab('show');
        var filter = $(this).data('filter-by');
        var filterNot = $(this).data('filter-by-not');
        $('[data-filter]').show();
        if (filter) {
            $('[data-filter]').hide();
            $('[data-filter~=' + filter + ']').show();
        } else if (filterNot) {
            $('[data-filter~=' + filterNot + ']').hide();
        }
    });
});
