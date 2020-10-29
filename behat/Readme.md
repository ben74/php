# Behat for windows
---
- Since the behat/selenium/webdriver/chrome versions changes might break it all, it's more reliable to use docker :
 `
 cd..;
 fig up -d phpgit_php74;fig up -d selenium;echo "/home/behat/behat-portable.sh docker" | docker exec -i phpgit_php74 "bin/bash"
 
---
- Passing additional information through lang paramater => a  json file with various informations in it 
`
cd ..;#repo root
docker volume create tmpShm;docker-compose up -d selenium;bash behat/behat-portable.sh docker;

1) install imagick extension for your php version : php71-imagickDll.7z
2) php imagick-test.php;#writes imagick.gif if ok

Caution : chromedriver + selenium.jar + chrome browser are all version dependant 
For all system ( out of docker environment execution ) :: find the right chromedriver version here :: https://chromedriver.chromium.org/downloads + Selenium.jar (https://www.selenium.dev/downloads/)
1) start chromedriver : pskill java.exe;$PATHTOJAVAEXECUTABLE -Dwebdriver.chrome.driver=chromedriver-v86.exe -jar selenium.jar &
2) bash behat-portable.sh
=> /c/prog/xamp7/php71/php -ddisplay_errors=1 gateway.php -f pretty benz --tags benz --lang='configuration.json' --config localhost.yml;


- sorry if I loss that initial composer.json file ..
`

FeatureContext::42 <<< where the extra magic lies
---
WIP .. This is not perfect at all .. this is only a demonstration for educative purposes, of a portable behat environement connecting to 192.168.99.100:4444
( the docker selenium virtual machine specified in docker-compose.yml )

"c:/Program Files/Android Studio/jre/bin/java.exe" -Dwebdriver.chrome.driver=chromedriverw32.exe -jar selenium.jar &


php71 imagick-test.php;#check imagick and librairies ( dll ) or lib are ready for usage

## Usage :
  docker-compose -up selenium
  
## Then if you've a nice php setup with imagick on windows :
  bash behat-portable.sh win 2 3;

## Otherwise using another linked docker host : 
  bash behat-portable/behat-portable.sh useSeleniumAtDocker 2 3;

## Need a Full VM with everything installed in ( including behat-portable ) ? Please have a look at :
  https://bitbucket.org/ben74/docker-alpine-php-fpm-apache-mysql-selenium-chrome
