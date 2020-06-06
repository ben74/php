<?php
class niceMultiFormDynamicEditionController extends base{}
/*
https://mottie.github.io/tablesorter/docs/
nosql-multiedit.php
todo :: bind :: suggestion like codage
 todo : drag and drop demo
*/
require_once 'app/common.php';
nocache();
$db = 'z/db/postes.json';
if (!is_file($db)) {#setting defaults
    $defaultData = [3 => ["family" => "main", "desc" => "desc", "nb" => 2, "idcat" => 1], 1 => ["family" => "main", "desc" => "desc2", "nb" => 3, "idcat" => 2], 2 => ["family" => "other", "desc" => "desc3", "nb" => 4, "idcat" => 3]];
    file_put_contents($db, json_encode($defaultData));
}
$data = json_decode(file_get_contents($db), 1);

if ($_POST) {
    if ($_POST['del']) {
        #on movement ajax update oldkeys <=> newkeys
        foreach ($_POST['del'] as $del) {
            if (isset($data[$del])) {
                unset($data[$del]);
            }
        }
    }
    foreach ($_POST as $k => $v) {
        if (is_numeric($k)) {
            $data[$k] = array_merge($data[$k], $v);#keep the family
        }
    }
    if ($_POST['family']) {#insert
        $keys = array_keys($data);
        asort($keys);
        $nk = end($keys) + 1;
        $data[$nk] = ["family" => $_POST['family'], "desc" => $_POST['desc'], "nb" => $_POST['nb'], "idcat" => $_POST['idcat']];
    }
    file_put_contents($db, json_encode($data));
    r302('?#' . $nk);
}
#<link href="/z/styles.css" type="text/css" rel="stylesheet"/>
?><html><head><title>Multiedit</title></head>
<link href="//prechoix.fr/css.css?select2,common,/home/prechoix/CIEL/prechoix" type="text/css" rel="stylesheet"/>

<body>
<fieldset class="fbody">
    <center>
        <a href="/">HOME</a>
        <fieldset style="width:900Px;">
            <legend>Insertion d'une nouvelle référence :</legend>
            <center>
                <form action="" method="post" id="insert"><input class="k" type="hidden" name="table" value="city1"><input class="k" type="hidden" name="new" value="1">
                    <table>
                        <tbody>
                        <tr>
                            <td>Family</td>
                            <td><input name="family" onkeyup="checkInput(this,'[a-z ]+',1)"></td>
                        </tr>
                        <tr>
                            <td>Desc</td>
                            <td><input name="desc" onkeyup="checkInput(this,'[a-z ]+',1)"></td>
                        </tr>
                        <tr>
                            <td>Nb</td>
                            <td><input name="nb" type="numeric" onkeyup="checkInput(this,'[0-9]+',1)"></td>
                        </tr>
                        <tr>
                            <td>IdCat</td>
                            <td><input name="idcat" type="numeric" onkeyup="checkInput(this,'[0-9]',1)"></td>
                        </tr>
                        <tr>
                            <td colspan="2"><input type="submit" value="Insérer le poste" id="submit" class="submod" style="width:100%"></td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </center>
        </fieldset>

        <hr>
        <fieldset>
            <legend>Modification des postes de city1 :</legend>
            <form action="" method="post" onsubmit="return submitChanged(this,['checkbox'],3);">
                <input class="k" type="hidden" name="sent" value="1"><input class="k" type="hidden" name="table" value="city1">
                <center><input type="submit" value="modifier" class="submod" id="submit">

                    <table border="1" class="sortable" id="tn1">
                        <thead>
                        <tr>
                            <td><input class="search" alt="0" rel="tn1" placeholder="Rechercher"></td>
                            <td><input class="search" alt="1" rel="tn1" placeholder="Rechercher"></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>

                        <tr>
                            <th>family</th>
                            <th data-extractor='input'>desc</th>
                            <th data-extractor='input'>nb</th>
                            <th data-extractor='input'>Phase</th>
                            <td>Delete</td>
                        </tr>
                        </thead>
                        <tbody>

                        <? #todo onblur ajax edit + order :):)
                        $a = 1;
                        foreach ($data as $k => $v) {
                            echo "<tr>
                            <td><input type='hidden' name='$k" . "[old]' value='$v[family],$v[desc],$v[nb],$v[idcat]'>$v[family]</td>
                            <td><input class=desc name='$k" . "[desc]' value='$v[desc]' onkeyup=\"checkInput(this,'descs',1)\"></td>
                            <td><s>$v[nb]</s><input class='nb' name='$k" . "[nb]' value='$v[nb]' onkeyup=\"checkInput(this,'[0-9]*',1)\"></td>
                            <td><s>$v[idcat]</s><input class='nb' name='$k" . "[idcat]' value='$v[idcat]' onkeyup=\"checkInput(this,'[0-9]',1)\" type='numeric'></td>
                            <td><input type='checkbox' name='del[]' value='$k'></td>
                        </tr>";

                        }

                        ?>
                        </tbody>
                    </table>
                    <br><input type="submit" value="modifier" class="submod" id="submit"></center>
            </form>
Please checkout excellent : <a href='https://mottie.github.io/tablesorter/' target="99">tablesorter doc</a>
        </fieldset>
    </center>
</fieldset>
<style>
    html {
        font-size: 10px;
    }

    body {
        font-size: 2rem
    }

    .tablesorter-header /*.tablesorter-headerUnSorted*/
    {
        user-select: none
    }

    th {
        cursor: pointer;
        background: url('//x24.fr/0/sortgrey.png') right center no-repeat #EEE;
    }

    th:hover {
        background-color: #DDD;
    }

    .headerSortUp,.tablesorter-headerDesc  {
        background-image: url("//x24.fr/0/sort-up.png");
    }

    .headerSortDown,.tablesorter-headerAsc {
        background-image: url("//x24.fr/0/sort-down.png");
    }

    #h #b fieldset fieldset {
        padding: 0 1rem 0;
    }

    .desc {
        width: 50vw;
    }

    .search {
        padding-left: 3rem;
        width: 100%;
        background: rgba(164, 255, 247, 0.84) url('//x24.fr/0/search.16.png') no-repeat 4px 2px
    }

    .nb {
        width: 3rem;
    }
    s,.s{display:none}
</style>

<? #}{<script src="//prechoix.fr/js.js?-a17,-prechoix"></script>?>
<script src='//x24.fr/jq.js'></script>
<script src='//x24.fr/jquery.tablesorter.min.js#2.27.5'></script>
<script>var data=<?=json_encode($data)?>;
    //https://mottie.github.io/tablesorter/docs/
    $(function () {
        //console.log('ok');
        $.tablesorter.addParser({
            id: 'inputs', type: 'text', is: function (s) {return false;}
            , format: function (string, table, cell,cellIndex) {
                x = $('input', cell).val() || $(cell).text();
                console.log('x', x, string, cell, table,cellIndex);
                return x;
            }
        });
        $.tablesorter.addParser({id: 'numeric', type: 'numeric', is: function (s) {return false;}, format: function (string, table, cell) {var x = $('input', cell).val() || $(cell).text();console.log('x', x, string, cell, table);return x;}
        });
//https://mottie.github.io/tablesorter/docs/example-extractors-parsers.html
        $('.sortable').tablesorter({showProcessing: true, cancelSelection: true, sortMultiSortKey: "shiftKey", headers: {0: {sorter: 'text'}, 1: {extractor: 'input', sorter: 'inputs'}, 2: {extractor: 'input', sorter: 'numeric'}, 3: {extractor: 'input', sorter: 'numeric'}}});//is okay

        $('.search').on('keyup', function () {
            var el = this, $t = $(this), v = $t.val().toLowerCase(), alt = $t.attr('alt'), rel = $t.attr('rel');
            // remove all highlighted text passing all em tags
            //removeHighlighting($("table tr em"));
            $("#" + rel + " tr").each(function (index) {
                if (index > 1) {
                    var it, mi, text, $row = $(this), $tds = $row.find("td"), $seltd = $tds[alt];
                    if (!$seltd) return;
                    text = $seltd.textContent.toLowerCase();
                    if (!text && $seltd.firstChild && $seltd.firstChild.value) text = $seltd.firstChild.value.toLowerCase();
                    mi = text.indexOf(v);
                    if (mi < 0) $row.hide(); else $row.show();
                }
            });
        });
    });

function checkInput(x){
    return x;
}
</script>
</body>
</html>
