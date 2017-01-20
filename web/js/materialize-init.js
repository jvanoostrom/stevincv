/**
 * Create a citynames Bloodhound
 *
 * @type {Bloodhound}
 */
var tags = new Bloodhound({
    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('text'),
    queryTokenizer: Bloodhound.tokenizers.whitespace,
    prefetch: window.location.protocol + '//' + window.location.host + '/json/tags.json'
});

tags.initialize();

$('.typeahead-input').materialtags({
    trimValue: true,
    typeaheadjs: [{
        highlight   : true
    },
        {
            name: 'tags',
            displayKey: 'name',
            valueKey: 'name',
            source: tags.ttAdapter()
        }]
});