$(document).ready(function(){

    /* Au chargement de la page */
    var credits = $("#credits");
    credits.hide();

    /* Evenements sur credits */
    $("span:contains('Crédits')").mouseover(function(){
        credits.fadeIn();
    });
    $("#copyright").mouseleave(function(){
        credits.fadeOut();
    });

    /* Evenements sur section#gestion */
    $('#global').append('<section id="gestion"></section>');
    $('#gestion').append('<aside id="error"></aside>')
        .append('<aside id="message"></aside>')
        .append('<aside id="debug"></aside>')
        .append('<aside id="jsonError"></aside>')
        .append('<aside id="kint"></aside>');
    $('#gestion aside').hide();

    /* ERROR */
    $('#gestion aside').dblclick(function() {
        $(this).fadeOut(500);
    })


    /* Rendre les liens des crédits ouvrable dans une nouvelle fenetre */
    $("#credits a").attr("target","_blank");

    /* Appel AJAX */
    $('.menu a').click(function (event) {
        event.preventDefault();
        console.log(this);
        $('.menu a').removeClass('selected'); //on enlève la classe selected à tous les lien des enu, pour qu'aucun n'ai le style de selecteed
        $(this).addClass('selected');   //on ajoute la classe selected au lien sur lequel on clque, on lui applique donc le style de selected, qui est anciennement 'focu'
        appelAjax(this);    //this car on passe l'objet en cours à la fct, ici c'est donc le lien sur lequel on a cliqué
    });

    /* focus sur l' accueil */
    $("#menu a:first").focus();
});

function appelAjax(elem){
    //var request = $(elem).focus().attr('href').split(".")[0];
    //$.get('index.php?rq='+request, gereRetour );
    $.ajaxSetup({processData: false, contentType: false});
    var request = 'UknownUri';
    var data = new FormData();
    console.log(arguments.callee.name,elem);
    switch (true) {
        case Boolean(elem.href):
            request = $(elem).attr('href').split(".html")[0];
            break;
        case Boolean(elem.action):
            request = $(elem).attr('action').split('.html')[0];
            data = new FormData(elem);
            break;
    }

    //var data={};
    //var request = $(elem).attr('href').split('.html')[0];
    data.append('senderId' ,elem.id);
    $.post("?rq=" + request, data, gereRetour);
}

var myData = [];

function gereRetour(retour) {
    retour = testeJson(retour);
    for (var action in retour) {
        switch (action) {
            case 'display':
                $('#contenu').html(retour[action]);
                break;

            case 'error':
                $('#' + action).html(retour[action]).fadeIn(1200);
                break;

            case 'makeTable':
                var table = makeTable(retour[action]);
                $('#contenu').html(table).fadeIn(1200);
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
                $('#formSelect').html(makeOptions(myData['allGroups'], 'id', 'nom'));
                $('#formTP05').change(function () {
                    appelAjax(this);})
                //alert('plop');
                break;

            case 'data':
                //console.log(retour[action]);
                myData['allGroups'] = JSON.parse(retour[action]);
                $('#debug').html(makeTable(JSON.parse(retour[action]))).fadeIn(500);
                break;

            case 'debug':
                $('#' + action).html(retour[action]).fadeIn(1200);
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
    var out = '<table class="myTab ' + elementType + '">'
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

//-------Phase03---------

function makeOptions(list, value, displayTxt) {
    var option ='';
    list.forEach(function(x) {
        option += '<option value=' + x[value] + '>' + x[displayTxt] + '</option>\n'
    });
    return option;
}