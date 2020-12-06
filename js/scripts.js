window.setTimeout(function() {
    $(".alert-banner").fadeTo(700, 0).slideUp(300, function(){
        $(this).remove();
    });
}, 5000);


$('.puzzle-list').on('mouseenter', 'tr', function(e) {
    $(this).addClass('hover');
});

$('.puzzle-list').on('mouseleave', 'tr', function(e) {
    $(this).removeClass('hover');
});

var loadPuzzleData = function(url, callback) {
    var activeFilter = $('[data-toggle=tab].active').first();
    $.get(url, {}, function(response) {
        loadPuzzleTemplate(response, callback, 'PuzzleParents', 'ParentId');
        activeFilter.click();
    });
};

var loadPuzzleTemplate = function(response, callback, parentListName, parentIDName) {
    var data = response;
    var now = Date.now();
    var template = $('#puzzle_row').html();
    $('tbody').empty();
    $.each(data, function(key, puzzleData) {
        var ssID = puzzleData['SpreadsheetId'];
        ssID = ssID.replace(/^.+ccc\?key=/, "");
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
    $('[data-tooltip]').tooltip();
    if (callback) {
        callback();
    }
};

var loadMemberData = function(url, callback) {
    $.get(url, {}, function(response) {
        loadMemberTemplate(response, callback);
    });
};

var loadMemberTemplate = function(response, callback) {
    var template = $('#member_row').html();
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
};

$(function() {
    $('[data-filter-list] a').click(function() {
        $(this).tab('show');
        var filter = $(this).data('filter-by');
        var filterNot = $(this).data('filter-by-not');
        $('[data-filter]').show();
        $('.solution-cell').hide();
        if (filter) {
            $('[data-filter]').hide();
            $('[data-filter~=' + filter + ']').show();
        } else if (filterNot) {
            $('body').data('filter', '');
            $('[data-filter~=' + filterNot + ']').hide();
        } else {
            $('.solution-cell').show();
        }
    });

    $('.copy-solutions').click(function(e) {
        e.preventDefault();
        var solutions = $.makeArray($(this).closest('table').find('tbody tr').map(function() { return $(this).data('solution'); }));
        var solutionList = solutions.join("\n");
        $('aside').append('<textarea id="copyBox">'+solutionList+'</textarea>')
        var val = $('#copyBox').val();
        $("#copyBox").select();
        document.execCommand("Copy");
        $("#copyBox").remove();
    })
});
