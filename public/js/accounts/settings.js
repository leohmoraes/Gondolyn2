$(function(){

    pickAState();

    $("#Country").bind('change', function(){
        if ($(this).val() === 'CA') {
            pickAState();
        };
    });

});

function pickAState () {
    var provinces = [
        "Alberta",
        "British Columbia",
        "Manitoba",
        "New Brunswick",
        "Newfoundland &amp; Labrador",
        "Northwest Territories",
        "Nova Scotia",
        "Nunavut",
        "Ontario",
        "Prince Edward Island",
        "Quebec",
        "Saskatchewan",
        "Yukon",
    ];

    $('#State').typeahead({
        minLength: 1,
        highlight: true
    },
    {
        name: 'provinces',
        source: typeaheadMatcher(provinces)
    });
}