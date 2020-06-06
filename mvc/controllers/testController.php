<?
class testController extends base{}
try {
$request = new Request("https://amphp.org/");
$response = yield  $http->request($request);

if ($response->getStatus() !== 200) {
throw new HttpException;
}
} catch (HttpException $e)  {
// handle error
}
