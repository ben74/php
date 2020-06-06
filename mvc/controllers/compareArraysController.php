<?#compare-arrays.php "a:1{}" "a:2{}"
/* Goal :php resursive array difference highlighter-> recursive array diff
command line : serialized or json form, after a swim, session injection then
*/
class compareArraysController{static function main(){}}
if(isset($argv)){#cli

}else{

$a1='[{"remises_regles_id":"858","remises_regles_active":"Y","remises_id":"472","remises_regles_conditions":"a:2:{i:0;a:2:{s:4:\"type\";s:14:\"TOUTE_COMMANDE\";s:15:\"paramconditions\";a:0:{}}s:7:\"userFid\";i:1174;}","remises_regles_appliqueremises":"a:2:{s:4:\"type\";s:7:\"PERCENT\";s:6:\"params\";a:3:{s:7:\"percent\";d:5;s:5:\"cible\";s:12:\"TOT_COMMANDE\";s:10:\"paramcible\";a:0:{}}}","remises_regles_ordre":"2"}]';
$a2='[{"remises_regles_id":"830","remises_regles_active":"Y","remises_id":"453","remises_regles_conditions":"a:3:{i:0;a:2:{s:4:\"type\";s:14:\"TOUTE_COMMANDE\";s:15:\"paramconditions\";a:0:{}}i:1;a:2:{s:4:\"type\";s:15:\"NUMBER_PRODUITS\";s:15:\"paramconditions\";a:1:{s:3:\"min\";i:1;}}s:7:\"userFid\";i:1174;}","remises_regles_appliqueremises":"a:2:{s:4:\"type\";s:7:\"PERCENT\";s:6:\"params\";a:3:{s:7:\"percent\";d:5;s:5:\"cible\";s:12:\"TOT_COMMANDE\";s:10:\"paramcible\";a:0:{}}}","remises_regles_ordre":"4"}]';

    if($_POST['a1'] and $_POST['a2']){
        $a1=$_POST['a1'];$a2=$_POST['a2'];
        $u1=unserRec($a1);$u2=unserRec($a2);$j1=json_encode($u1);$j2=json_encode($u2);
        $d1=arrayRecursiveDiff($u1,$u2);
        $d2=arrayRecursiveDiff($u2,$u1);
        echo"<pre>Présent dans 1 absents dans 2:";print_r($d1);
        echo"<hr>Présent dans 2 absents dans 2:";print_r($d2);
        echo"<hr>j1:<textarea>$j1</textarea>";
        echo"<hr>j2:<textarea>$j2</textarea>";
    }
    

    echo "<title>Compare 2 data arrays</title><link rel=stylesheet href=/z/styles.css><form method=post><input type=submit value=compare accesskey=s>
    <table>
        <tr><td>a1</td><td><textarea name=a1>" .$a1."</textarea></td></tr>
        <tr><td>a2</td><td><textarea name=a2>".$a2."</textarea></td></tr>
        </table><input type=submit value=compare></form>
<style>textarea{width:100%;height:3rem;}input[type=submit]{width:100%;cursor:pointer;}table{width:100%}
form textarea{height:25vh;}
</style>";
    return;
}

function jsserpos($v){
    if(in_array(substr($v,0,2),['a:','O:']))return'ser';
    if(in_array(substr($v,0,1),['[','{']))return'json';
    if(in_array(substr($v,0,5),['array']))return'var_export';
    return false;
}

function unserRec($s,$lv=0){
    $s=trim($s);if(jsserpos($s)=='ser'){$x=unserialize($s);}
    elseif(jsserpos($s)=='json'){$x=json_decode($s,1);if(!$x)$x=[];}
    elseif(jsserpos($s)=='var_export'){eval("\$x=$s;");if(!$x)$x=[];}
    elseif(is_string($s)){return $s;/*ne doit idéalement par parvenir ici*/}
    #nb:peuvent être des propriétés objets: nécessitant leur classes et autoloaders ..
    if(is_array($x)){
        foreach($x as $k=>&$v){
            if(is_string($v) and jsserpos($v)){
                $v=unserRec($v,$lv+1);
                $a=1;
            }
            if(is_array($v)){#au premier niveau, évidemment ..
                foreach($v as $k2=>&$v2){
                    if(is_string($v2) and jsserpos($v2)) {
                        $v2 = unserRec($v2, $lv + 1);
                        $a=1;
                    }
                }unset($v2);
            }
        }unset($v);
    }
    return $x;
}

function arrayRecursiveDiff($aArray1, $aArray2) {
  $aReturn = array();

  foreach ($aArray1 as $mKey => $mValue) {
    if (array_key_exists($mKey, $aArray2)) {
      if (is_array($mValue)) {
        $aRecursiveDiff = arrayRecursiveDiff($mValue, $aArray2[$mKey]);
        if (count($aRecursiveDiff)) { $aReturn[$mKey] = $aRecursiveDiff; }
      } else {
        if ($mValue != $aArray2[$mKey]) {
          $aReturn[$mKey] = $mValue;
        }
      }
    } else {
      $aReturn[$mKey] = $mValue;
    }
  }
  return $aReturn;
} 
