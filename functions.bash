#usage : CurlPost "http://fullUrl" $PHPSESSID 'json_encoded(postdata)' $xdebugToggle "Cookie1=Value1"
#ex : CurlPost "https://phpgit.127.0.0.1.xip.io/reflector.php" xyz '{"q":"something"}' 1 "Cookie1=Value1"
#note post body has to be evaluated => see reflector.php
function CurlPost() { 
    out='';xd='a=b';
    if [ "$4" == "1" ]; then xd='XDEBUG_SESSION=XDEBUG_ECLIPSE';fi;
    curl -s -S -L -k -b "$xd;PHPSESSID=$2;$5;" -H "jsp: 1" -H "X-Requested-With: XMLHttpRequest" -H "Content-Type: application/json" -d "$3" $1; 
}
