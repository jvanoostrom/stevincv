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


    // Materialize initialisation
    $(".button-collapse").sideNav();

    $('ul.tabs').tabs();

    $(".dropdown-button").dropdown({
        belowOrigin: true // Displays dropdown below the button
    });

    $('.datepicker').pickadate({
        selectMonths: true, // Creates a dropdown to control month
        selectYears: 200, // Creates a dropdown of 15 years to control year
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
        format: 'yyyy-mm-dd',
        closeOnSelect: true,
        closeOnClear: true
    });

    $('.collapsible').collapsible();

    $('select').material_select();

    // Notification slides
    $(".succes-entity").delay(200).slideDown(300).delay(2000).slideUp(300);
    $(".error-entity").delay(200).slideDown(300).delay(5000).slideUp(300);

    $('[class^=delete-button-]').click(function() {
        var classes = $(this).attr('class').split( '-' );

        $('.delete-entity-' + classes[2]).delay(200).slideDown(300).delay(5000).slideUp(300);
    });

    // Personalia page
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
        $('div .btn').each(function() {
            if ($(this).hasClass('disabled')) {
                $(this).removeClass('disabled');
            }
            else {
                $(this).addClass('disabled');
            }

        })

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

    // Curriculumvitae page
    // CurriculumvitaeProjects
    var actualProjectCount = $('.project').length;
    var $checkBoxProjects = $("input[type=checkbox][id^=curriculumvitae_curriculumvitaeProjects]");
    var countCheckedProjects = $checkBoxProjects.filter(":checked").length;
    var maxBoxesProjects = 3;

    $('#add-another-project').click(function(e) {
        e.preventDefault();

        var projectList = $('#project-fields-list');
        var projectListDiv = $('#project-div');

        // grab the prototype template
        var newWidget = projectList.attr('data-prototype');
        // replace the "__name__" used in the id and name of the prototype
        // with a number that's unique to your projects
        newWidget = newWidget.replace(/__name__/g, projectCount);


        // create a new list element and add it to the list

        var newDiv = $('<div class="row project"></div>').html(newWidget);
        newDiv.appendTo(projectListDiv);
        actualProjectCount++;

        $('input[id$="_project"]').each(function(){
            var val = $(this).val();
            $('option[value=' + val + ']').remove();
        });

        $('select').material_select();
        $checkBoxProjects = $("input[type=checkbox][id^=curriculumvitae_curriculumvitaeProjects]");
        countCheckedProjects = $checkBoxProjects.filter(":checked").length;

        if(countCheckedProjects > 2) {
            $('input[type=checkbox][id="curriculumvitae_curriculumvitaeProjects_'+projectCount+'_important"]').attr('disabled',true);
        }
        if(actualProjectCount > 5) {
            $('#add-another-project-div').hide();
        }
        projectCount++;
    });

    $('#project-div').on('click', 'a.delete-project', function(e){
        e.preventDefault();
        var $element = $(this).closest('div.project').find('input[id$="_project"]');
        var valRemoved = $element.val();
        var labelRemoved = $('label[for="'+$element.attr('id')+'"]').text();

        $('select.initialized').append($('<option>', {
            value: valRemoved,
            text: labelRemoved
        }));
        $('.select-dropdown').append('<li><span>'+labelRemoved+'</span></li>');

        $(this).closest('.project').remove();

        actualProjectCount--;
        $checkBoxProjects = $("input[type=checkbox][id^=curriculumvitae_curriculumvitaeProjects]");
        countCheckedProjects = $checkBoxProjects.filter(":checked").length;
        if (countCheckedProjects < maxBoxesProjects)
        {
            $checkBoxProjects.not(":checked").attr("disabled", false);
        }
        if(actualProjectCount < 6) {
            $('#add-another-project-div').show();
        }
    });


    // Limit number of important projects in CV
    if (countCheckedProjects >= maxBoxesProjects)
    {
        $checkBoxProjects.not(":checked").attr("disabled", true);
    }

    $(document).on('change', 'input[type=checkbox][id^=curriculumvitae_curriculumvitaeProjects]', function(){

        countCheckedProjects = $checkBoxProjects.filter(":checked").length;
        if (countCheckedProjects >= maxBoxesProjects)
        {
            $checkBoxProjects.not(":checked").attr("disabled", true);
        }
        else
        {
            $checkBoxProjects.attr("disabled", false);
        }
    });

    // Limit number of education in CV
    var $checkBoxEducation = $("input[type=checkbox][id^=curriculumvitae_education]");
    var maxBoxesEducation = 4;
    var countCheckedEducation = $checkBoxEducation.filter(":checked").length;
    if (countCheckedEducation >= maxBoxesEducation)
    {
        $checkBoxEducation.not(":checked").attr("disabled", true);
    }

    $checkBoxEducation.change(function() {
        var countCheckedEducation = $checkBoxEducation.filter(":checked").length;

        if (countCheckedEducation >= maxBoxesEducation)
        {
            $checkBoxEducation.not(":checked").attr("disabled", true);
        }
        else
        {
            $checkBoxEducation.attr("disabled", false);
        }
    });


    // Limit number of certificates in CV
    var $checkBoxCertificates = $("input[type=checkbox][id^=curriculumvitae_certificates]");
    var maxBoxesCertificates = 6;
    var countCheckedCertificates = $checkBoxCertificates.filter(":checked").length;
    if (countCheckedCertificates >= maxBoxesCertificates)
    {
        $checkBoxCertificates.not(":checked").attr("disabled", true);
    }

    $checkBoxCertificates.change(function() {
        var countCheckedCertificates = $checkBoxCertificates.filter(":checked").length;

        if (countCheckedCertificates >= maxBoxesCertificates)
        {
            $checkBoxCertificates.not(":checked").attr("disabled", true);
        }
        else
        {
            $checkBoxCertificates.attr("disabled", false);
        }
    });

    // Limit number of extracurricular in CV
    var $checkBoxExtracurricular = $("input[type=checkbox][id^=curriculumvitae_extracurricular]");
    var $checkBoxPublications = $("input[type=checkbox][id^=curriculumvitae_publications]");
    var maxBoxesExtracurricular = 3;
    var countCheckedExtracurricular = $checkBoxExtracurricular.filter(":checked").length;
    if (countCheckedExtracurricular >= 1)
    {
        $checkBoxPublications.not(":checked").attr("disabled", true);
        $("#tooltip_publication").show();
    }
    if (countCheckedExtracurricular >= maxBoxesExtracurricular)
    {
        $checkBoxExtracurricular.not(":checked").attr("disabled", true);
    }

    $checkBoxExtracurricular.change(function() {
        var countCheckedExtracurricular = $checkBoxExtracurricular.filter(":checked").length;
        if (countCheckedExtracurricular >= 1)
        {
            $checkBoxPublications.not(":checked").attr("disabled", true);
            $("#tooltip_publication").show();
        }
        else
        {
            $checkBoxPublications.attr("disabled", false);
            $("#tooltip_publication").hide();
        }
        if (countCheckedExtracurricular >= maxBoxesExtracurricular)
        {
            $checkBoxExtracurricular.not(":checked").attr("disabled", true);
        }
        else
        {
            $checkBoxExtracurricular.attr("disabled", false);
        }
    });

    // Limit number of publications in CV
    var maxBoxesPublications = 3;
    var countCheckedPublications = $checkBoxPublications.filter(":checked").length;
    if (countCheckedPublications >= 1)
    {
        $checkBoxExtracurricular.not(":checked").attr("disabled", true);
        $("#tooltip_extracurricular").show();
    }

    if(countCheckedPublications >= maxBoxesPublications)
    {
        $checkBoxPublications.not(":checked").attr("disabled", true);
    }

    $checkBoxPublications.change(function() {
        var countCheckedPublications = $checkBoxPublications.filter(":checked").length;

        if (countCheckedPublications >= 1)
        {
            $checkBoxExtracurricular.not(":checked").attr("disabled", true);
            $("#tooltip_extracurricular").show();
        }
        else
        {
            $checkBoxExtracurricular.attr("disabled", false);
            $("#tooltip_extracurricular").hide();
        }

        if(countCheckedPublications >= maxBoxesPublications)
        {
            $checkBoxPublications.not(":checked").attr("disabled", true);
        }
        else
        {
            $checkBoxPublications.attr("disabled", false);
        }
    });

    // tooltip
    $('[id^=tooltip]').hover(function(){
        // Hover over code
        var title = $(this).attr('title');
        $(this).data('tipText', title).removeAttr('title');
        $('<p class="tooltip"></p>')
            .text(title)
            .appendTo('body')
            .fadeIn('slow');
    }, function() {
        // Hover out code
        $(this).attr('title', $(this).data('tipText'));
        $('.tooltip').remove();
    }).mousemove(function(e) {
        var mousex = e.pageX + 20; //Get X coordinates
        var mousey = e.pageY + 10; //Get Y coordinates
        $('.tooltip')
            .css({ top: mousey, left: mousex })
    });

    $('#addSkillLink').click(function() {
        $('#addSkill').toggle('slow');
    });

    var toggled = false;
    $('[class^=editSkillLink-]').click( function() {

        var classes = $(this).attr('class').split( '-' );
        $('.edit-toggle-off-'+ classes[1]).toggle();
        $('.edit-toggle-on-'+ classes[1]).toggle();
        if(toggled)
        {
            $('[class^=edit-toggle-on]').not('.edit-toggle-on-'+ classes[1]).hide();
            $('[class^=edit-toggle-off]').not('.edit-toggle-off-'+ classes[1]).show();
        }
        toggled = true;

    });

    $('[id^=editSkillTd-]').dblclick( function() {

        var classes = $(this).attr('id').split( '-' );
        $('.edit-toggle-off-'+ classes[1]).toggle();
        $('.edit-toggle-on-'+ classes[1]).toggle();
        if(toggled)
        {
            $('[class^=edit-toggle-on]').not('.edit-toggle-on-'+ classes[1]).hide();
            $('[class^=edit-toggle-off]').not('.edit-toggle-off-'+ classes[1]).show();
        }
        toggled = true;

    });

    $('[class^=askillSubmitLink-]').click(function() {
        var classes = $(this).attr('class').split( '-' );
        $('#edit-skill-'+classes[1]).submit();
    });


});