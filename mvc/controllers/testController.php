<?
#phpx cli.php test 1
class testController extends cli{
    static function main(){
        $a2='ep';
        $ob=base::set(['a'=>date('YmdHis'),'cachedValueRemainingProcesses'=>cacheGet($a2)]);
        $array=base::get();
        print_r($array);
    }
}
return;
?>

try {
$request = new Request('https://amphp.org/');
$response = yield  $http->request($request);

if ($response->getStatus() !== 200) {
throw new HttpException;
}
} catch (HttpException $e)  {
// handle error
}
