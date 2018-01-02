window.setTimeout(function() {
    $(".alert-banner").fadeTo(700, 0).slideUp(300, function(){
        $(this).remove();
    });
}, 5000);

var loadPuzzleData = function(url, callback) {
    $.get(url, {}, function(response) {
        loadPuzzleTemplate(response, callback, 'PuzzleParents', 'ParentId');
    });
};

var loadPuzzleTagData = function(url, callback) {
    $.get(url, {}, function(response) {
        loadPuzzleTemplate(response, callback, 'TagAlerts', 'TagId');
    });
};

var loadPuzzleTemplate = function(response, callback, parentListName, parentIDName) {
    var data = response;
    var now = Date.now();
    $.get('/templates/puzzle_row.mst', function(template) {
        $.each(data, function(key, puzzleData) {
            var ssID = puzzleData['SpreadsheetId'];
            ssID = ssID.replace(/^.+ccc\?key=/, "");
            puzzleData['SpreadsheetURL'] = "https://docs.google.com/spreadsheets/d/" + ssID;
            puzzleData['SlackURL'] = "http://" + slackDomain + ".slack.com/messages/"+puzzleData['SlackChannel'];
            puzzleData['SinceCreated'] = now - new Date(puzzleData['CreatedAt']);
            puzzleData['SinceUpdated'] = now - new Date(puzzleData['UpdatedAt']);

            var rendered = Mustache.render(template, puzzleData);

            var parents = puzzleData[parentListName];
            if (!parents || parents.length == 0) {
                var emptySet = {};
                emptySet[parentIDName] = 0;
                parents = [emptySet];
            }

            $.each(parents, function(key, parent) {
                var tableID = parent[parentIDName];
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

var loadMemberData = function(url, callback) {
    $.get(url, {}, function(response) {
        loadMemberTemplate(response, callback);
    });
};

var loadMemberTemplate = function(response, callback) {
    $.get('/templates/member_row.mst', function(template) {
        $.each(response, function(key, memberData) {
            if(memberData['PuzzleId'] != null) {
                memberData['PuzzleTitle'] = $('#table-' + memberData['PuzzleId']).data('title');
            }
            memberData['slackDomain'] = slackDomain;

            var rendered = Mustache.render(template, memberData);
            var puzzleID = memberData['PuzzleId'] || 0;
            var tbody = $('#table-' + puzzleID).find('tbody');
            tbody.append(rendered);

            $('#table-alpha tbody').append(rendered);
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
