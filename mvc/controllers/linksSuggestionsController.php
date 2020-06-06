<?
class linksSuggestionsController extends base{}
#Using https://github.com/kraaden/autocomplete
require_once 'z/header.php';
nocache();
$suggestions = "[{'label':'pomme','url':'url-pomme','id':1},{'label':'poire','url':'url-poire','id':2},{'label':'raisin','url':'url-raisin','id':3},{'label':'une longue phrase qui va matcher tout ce que lon y met au clavier dans lautosuggestion','url':'url-defauturl','id':4}]";
if (0) {
    $f = 'z/db/suggestions.json';
    if (!is_file($f)) {
        file_put_contents($f, $suggestions);
    } else {
        $suggestions = json_decode(file_get_contents($f), 1);
    }
}

$f = 'z/db/links.json';
if (!is_file($f)) {
    $json = "[]";
    file_put_contents($f, $json);
}

if ($_POST) {#submitted values
    $json = [];
    foreach ($_POST['label'] as $k => $v) {
        $json[] = ['label' => $v, 'url' => $_POST['url'][$k], 'id' => $_POST['id'][$k]];
    }
    file_put_contents($f, json_encode($json));
} else {
    $json = json_decode(file_get_contents($f), 1);
}
$a = 1;
?>
<link rel=stylesheet href='/vendor/autocomplete.kraaden.min.css'/>
<style>
    #table {
        width: 100%
    }

    .cmspageid {
        width: 10vw;
    }

    .autocomplete {
        color: #000;
    }

    input[type=submit], button {
        width: 100%
    }
</style>


<fieldset>
    <legend>Suggestions</legend>
    <form method=POST>
        <table id=table>
            <thead>
            <tr>
                <th>Label</th>
                <th>Url</th>
                <th>Cmsid</th>
            </tr>
            </thead>
            <?
            foreach ($json as $k => $v) {
                echo "<tr>
                <td><input class='label' name='label[]' value='$v[label]'></td>
                <td><input name='url[]' class=url value='$v[url]' placeholder='please type in here for suggestions to pop up'></td>
                <td><input name='id[]' class=cms value='$v[id]'></td>
            </tr>";
            }
            ?>
            <tr>
                <td><input class='label' name='label[]' value=''></td>
                <td><input name='url[]' class=url value='' placeholder='please type in here for suggestions to pop up'></td>
                <td><input name='id[]' class=cms value=''></td>
            </tr>
        </table>
        <button id=add>Add new line</button>
        Hint : try : pomme, poire, raisin, longue
        <input type=submit value=submit>
    </form>
</fieldset>


<script src='/vendor/autocomplete.kraaden.min.js'></script>
<script>
    var suggestions = <?=$suggestions?>;

    function autoCompletes(input) {//('autocompletion => fills neighboor cmspageid'){
        autocomplete({
            input: input,
            fetch: function (text, update) {
                text = text.toLowerCase();
                var suggested = suggestions.filter(n => n.label.toLowerCase().indexOf(text) > -1);//startsWith
                //console.log(suggestions);
                update(suggested);
            },
            onSelect: function (item) {
                var pn = input.parentNode.parentNode;
                input.value = item.url;
                pn.querySelector('.label').value = item.label;
                pn.querySelector('.cms').value = item.id;
                //console.log('closest .cmspageid',input,item.id);
            }
        });
    }


    document.querySelector('#add').onclick = function () {
        var tr, x = document.querySelector('#table'),
            c = "<td><input name='label[]' value='' class=label></td><td><input name='url[]' class=url value='' placeholder='please type in here for suggestions to pop up'></td><td><input name='id[]' class=cms value=''></td>";
        tr = x.insertRow();
        tr.innerHTML = c;
        document.querySelectorAll('.url:not(.a1)').forEach(function (a) {
            a.className += ' a1';
            autoCompletes(a);
        });
        return false;
    };
    document.querySelectorAll('.url:not(.a1)').forEach(function (a) {
        console.log(a);
        a.className += ' a1';
        autoCompletes(a);
    });
</script>
