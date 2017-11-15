/**
 * A couple of examples to show case what can be done with the MagicSuggest component
 */
$(function() {

    $('#ms-tagbox').magicSuggest({
        placeholder: 'Enter one or multiple tags'
    });

    $('#ms-filter').magicSuggest({
        placeholder: 'Select...',
        allowFreeEntries: false,
        data: [{
            id: 1,
            name: 'Location',
            nb: 34
        }, {
            id: 2,
            name: 'Keyword',
            nb: 106
        }],
        selectionPosition: 'bottom',
        selectionStacked: true,
        selectionRenderer: function(data){
            return data.name + ' (<b>' + data.nb + '</b>)';
        }
    });

    $('#ms-scrabble').magicSuggest({
        placeholder: 'Type some real or fake fruits',
        data: ['Banana', 'Apple', 'Orange', 'Lemon']
    });

    $('#ms-emails').magicSuggest({
        placeholder: 'Enter recipients...',
        data: [{
            name: 'Georges Washington',
            email: 'georges.washington@whitehouse.gov'
        },{
            name: 'Theodore Roosevelt',
            email: 'theodore.roosevelt@whitehouse.gov'
        },{
            name: 'Benjamin Franklin',
            email: 'benjamin.franlin@whitehouse.gov'
        },{
            name: 'Abraham Lincoln',
            email: 'abraham.lincoln@whitehouse.gov'
        }],
        valueField: 'email',
        renderer: function(data){
            return data.name + ' (<b>' + data.email + '</b>)';
        },
        resultAsString: true
    });

    // note that it would be a lot more proper to use CSS classes here instead of inline style
    $('#ms-complex-templating').magicSuggest({
        data: 'random.json',
        renderer: function(data){
            return '<div style="padding: 5px; overflow:hidden;">' +
                '<div style="float: left;"><img src="' + data.picture + '" /></div>' +
                '<div style="float: left; margin-left: 5px">' +
                    '<div style="font-weight: bold; color: #333; font-size: 10px; line-height: 11px">' + data.name + '</div>' +
                    '<div style="color: #999; font-size: 9px">' + data.email + '</div>' +
                '</div>' +
            '</div><div style="clear:both;"></div>'; // make sure we have closed our dom stuff
        }
    });

});