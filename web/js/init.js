$( document ).ready(function() {

    // var tags = new Bloodhound({
    //     datumTokenizer: Bloodhound.tokenizers.obj.whitespace('text'),
    //     queryTokenizer: Bloodhound.tokenizers.whitespace,
    //     prefetch: '/json/tags.json'
    // });
    //
    // tags.initialize();
    //
    // $('.typeahead-input').materialtags({
    //     trimValue: true,
    //     typeaheadjs: [{
    //         highlight   : true
    //     },
    //         {
    //             name: 'tags',
    //             displayKey: 'name',
    //             valueKey: 'name',
    //             source: tags.ttAdapter()
    //         }]
    // });

	$(".button-collapse").sideNav();

    $(".succes-entity").delay(200).slideDown(300).delay(2000).slideUp(300);

    $(".delete-button").click(function() {
        $(".delete-entity").delay(200).slideDown(300);
    });

    $('ul.tabs').tabs();

    $('#profiel-keywords').materialtags({
        maxTags: 3
    });

    $(".dropdown-button").dropdown({
        belowOrigin: true // Displays dropdown below the button
    });

    $('.datepicker').pickadate({
        selectMonths: true, // Creates a dropdown to control month
        selectYears: 50, // Creates a dropdown of 15 years to control year
        monthsFull: ['Januari', 'Februari', 'Maart', 'April', 'Mei', 'Juni', 'Juli', 'Augustus', 'September', 'Oktober', 'November', 'December'],
        monthsShort: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dec'],
        weekdaysFull: ['Zondag', 'Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag', 'Zaterdag'],
        weekdaysShort: ['Zo', 'Ma', 'Di', 'Wo', 'Do', 'Vr', 'Za'],
        today: 'Nu',
        clear: 'Leeg',
        close: 'Sluit',
        labelMonthNext: 'Volgende maand',
        labelMonthPrev: 'Vorige maand',
        labelMonthSelect: 'Selecteer maand',
        labelYearSelect: 'Selecteer jaar',
        formatSubmit: 'yyyy-mm-dd',
		format: 'dd-mm-yyyy',
        closeOnSelect: true,
        closeOnClear: true
    });

    $('#clicker').click(function() {
        $('input').each(function() {
            if ($(this).attr('disabled')) {
                $(this).removeAttr('disabled');
            }
            else {
                $(this).attr({
                    'disabled': 'disabled'
                });
            }
        });
        $('button[type=submit]').each(function() {
            if ($(this).attr('disabled')) {
                $(this).removeAttr('disabled');
            }
            else {
                $(this).attr({
                    'disabled': 'disabled'
                });
            }
        });

        $('#clicker').each(function() {
            if ($(this).attr('disabled')) {
                $(this).removeAttr('disabled');
            }
            else {
                $(this).attr({
                    'disabled': 'disabled'
                });
            }
        });
    });

    $('input.n-tag').autocomplete({
        data: {
            "Apple": null,
            "Microsoft": null,
            "Google": 'http://placehold.it/250x250'
        }
    });




});