var myData = [];

$(document).ready(function() {

    /**
     * Page de démarrage
     */

    var credits = $('#credits');
    credits.hide();

    // Les target pour les liens de #credits
    $('#credits a').each(function() {
        $(this).attr('target', '_blanck');
    });

    // premier élement du menu sélectionné
    $('#menu a:first').addClass('selected');

    // les aside de #gestion
    $('#global').after('<section id="gestion"></section>');
    $('#gestion').append('<aside id="error"></aside>')
        .append('<aside id="message"></aside>')
        .append('<aside id="debug"></aside>')
        .append('<aside id="jsonError"></aside>')
        .append('<aside id="kint"></aside>');
    $('#gestion aside').hide();

    //créer les menus avec JQUERY ui
    $('#menu').menu({
        position: {my: "center top", at: "center bottom"}
    });


    /**
     * Gérer les event
     */

    // montre "crédits" quand souris dessus
    $('#auteur + span').mouseover(function() {
        credits.fadeIn();
    });

    // cache "credits" quand la souris quitte
    $('#copyright').mouseleave(function() {
        credits.fadeOut();
    });

    // gérer les appels ajax
    $('.menu a').click(function (event) {
        event.preventDefault();
        $('.menu a').removeClass('selected');
        $(this).toggleClass('selected');
        appelAjax(this);
    });

    // cacher l'aside#error après un double click
    $('#gestion aside').dblclick(function() {
        $(this).fadeOut(500);
    })


});


function appelAjax(elem) {
    $.ajaxSetup({processData: false, contentType: false});
    var data = new FormData();
    var request = 'unknownUri';
    switch (true) {
        case Boolean(elem.href):
            request = $(elem).attr('href').split(".html")[0];
            break;

        case Boolean(elem.action):
            request = $(elem).attr('action').split('.html')[0];
            data = new FormData(elem);
            break;
    }
    data.append('senderId', elem.id);
    $.post("?rq=" + request, data, gereRetour);

}

function filtrage() {
    var v = $(this).val();
    $(this).removeClass()
    switch($(this).parent().find('input:checked').val()) {
        case 'I': $(this).addClass('I'); break;
        case 'B': v = '^' + v; $(this).addClass('B'); break;
        case 'E': v += '$'; $(this).addClass('E'); break;
    }
    var r = new RegExp(v, 'i');
    var l = myData['allGroups'].filter(function(x) {
        return x.nom.match(r);
    });
    $('#formSelect').html(makeOptions(l, 'nom', 'nom'));
}

function gereRetour(retour) {
    retour = testeJson(retour);
    var destination = '#contenu';
    if ($(retour['destination']).length > 0) {
        destination = retour['destination'];
        delete (retour['destination']);
    }
    $('#gestion aside').fadeOut(400);
    for (var action in retour){
        switch (action) {
            case 'display':
                $('#contenu').html(retour[action]);
                break;

            case 'debug':
            case 'error':
                $('#' + action).html(retour[action]).fadeIn(1200);
                break;

            case 'makeTable':
                var table = makeTable(retour[action]);
                $(destination).html(table).fadeIn(500);
                break;

            case 'jsonError':
                var html = '<b>Error : </b><br>'
                    + retour[action].error
                    + '<hr><b>Json : </b><br>'
                    + retour[action].json;
                $('#' + action).html(html).fadeIn(1200);
                break;

            case 'formTP05':
                $('#contenu').html(retour[action]);
                $('#formSelect').change(function () {
                    appelAjax(this.form);
                });
                $('#formSearch').submit(function(evt) {
                    evt.preventDefault();
                }).find('input[name=tp05Text]').keyup(filtrage);
                $('#formSearch').find('input[type=radio]').change(function () {
                    $('#formSearch input[name=tp05Text]').trigger('keyup');
                });
                break;
            case 'data':
                myData['allGroups'] = JSON.parse(retour[action]);
                $('#formSelect').html(makeOptions(myData.allGroups, 'nom', 'nom'));
                //$('#debug').html(makeTable(JSON.parse(retour[action]))).fadeIn(1200);
                break;
            case 'kint':
                $('#kint').html(retour[action]).fadeIn(1200);
                break;

            case 'cacher':
                $(retour[action]).fadeOut(500);
                break;

            case 'montrer':
                $(retour[action]).fadeIn(500);
                break;

            case 'formConfig':
                $('#contenu').html(retour[action]);
                $('#sauveConfig').submit(function(evt) {
                    evt.preventDefault();
                    appelAjax(this);
                })
                break;

            case 'layout':
                var infos = JSON.parse(retour[action]);
                $('#titre').html('<img id="logo" alt="logo" src="' + infos.logoPath + '" />' + infos.titre);
                break;
            case 'loginConnect':
                $('#contenu').html(retour[action]);
                $('#formLogin').submit(function(evt) { //erreur 404 sans ça
                  evt.preventDefault();
                appelAjax(this);
                });
                $('#logMdPP').click(function(evt) { //lien
                    appelAjax(this);
                    evt.preventDefault();
                });
                break;

            case 'userConnu':
                myData['user'] = JSON.parse(retour[action]);
                $('#contenu').html('Bienvenue ' + myData['user'].uPseudo);
                $('body').css('background', '#4C4F22');
                break;

            default:
                console.log('Action inconnue : ' + action);
                console.log(retour[action]);
                break;
        }
    }
}

function testeJson(json) {
    var parsed;
    try {
        parsed = JSON.parse(json);
    } catch(e) {
        parsed = {"jsonError": {'error': e, 'json': json}};
    }

    return parsed;
}

function makeOptions(list, value, displayTxt) {
    var option ='';
    list.forEach(function(x) {
        option += '<option value=' + x[value] + '>' + x[displayTxt] + '</option>\n'
    });
    return option;
}

function makeTableFromObject(tab) {
    var firstElement = tab[Object.keys(tab)[0]];
    var elementType = firstElement.constructor.name;
    var fonction = 'makeThead' + elementType;
    var out = '<table class="myTab ' + elementType +  '">'
        + window[fonction](firstElement, elementType)
        + makeTbody(tab, 'Object')
        + '</table>';

    return out;
}

function makeTableFromArray(tab) {
    var firstElement = tab[Object.keys(tab)[0]];
    var elementType = firstElement.constructor.name;
    var fonction = 'makeThead' + elementType;
    var out = '<table class="myTab ' + elementType +  '">'
        + window[fonction](firstElement, elementType)
        + makeTbody(tab, 'Array')
        + '</table>';

    return out;
}

function makeTheadObject(el, type='Array') {
    var out = '<thead>\t<tr>\n\t\t<th>' + (type == 'Array' ? 'index' : 'clé') + '</th>'
        + Object.keys(el).map(function(x) {return '\t\n<th>' + x + '</th>'}).join('\n')
        +'\t</tr>\n</thead>\n';

    return out;
}

function makeTheadArray(el, type='Array') {
    var out = '<thead>\t<tr>\n\t\t<th>' + (type == 'Array' ? 'index' : 'clé') + '</th>'
        + Object.keys(el).map(function(x) {return '\t\n<th>col_' + x + '</th>'}).join('\n')
        +'\t</tr>\n</thead>\n';

    return out;
}

function makeTbody(tab, type='Array') {
    var out = '<tbody>'
        + Object.keys(tab)
            .map(function (k) { return '\t<tr id=' + (type == 'Array' ? 'lig_': '') + k + '>\n\t\t<td>' + k + '</td>\n'
                + Object.keys(tab[k])
                    .map(function(x) {return '\t\t<td>' + tab[k][x] + '</td>';}).join('\n')
                + '\t</tr>';
            }).join('\n');
    + '</tbody>';

    return out;
}

function makeTable(tab) {
    var fonction = 'makeTableFrom' + tab.constructor.name;
    var out = window[fonction](tab);
    return out;
}

