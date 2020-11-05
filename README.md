# ⚡Php8 FPM Nginx Fast, Scripts, Pearls & Treasures🚀
---
<a href='//alpow.fr/#o:gh:phpgit' target=1><img src='https://i.snipboard.io/zWQLKt.jpg#https://i.snipboard.io/wkLYV1.jpg'/></a>
- Want to run and test asap ?
---
> `docker-compose up -d phpgit_php8;ip=$(docker-machine ip default);echo "goto http://$ip"`;
---
- Aka : a gentle introduction to Alpine Framework 
- poc for asynchronous php might be made quite easy ;)
- conf in app/params.json

- Behats/selenium suite works better with fixed version of chrome/selenium image along fixed behat composer dependencies, so many changes between third party librairies cause such
 a mess .. also replace docker ip adress 192.168.99.104 according to your needs 
 `docker-compose up -d phpgit_php74;docker-compose up -d selenium;echo "/home/behat/behat-portable.sh docker" | docker exec -i phpgit_php74 "bin/bash"`
---

- This repo contains mostly snippets of code for educative purposes, keeping it the most simple and stupid logic exposed, php random stuff, demos, functions, most of these samples are gross but leads the way on how to accomplish some things .. Code snippets are not cleaned, taken from several various projects
- Focus on : "get it done", "keep it simple and stupid" => examples avoid spreading through 36+ various abstract class files maze so students might loose track of what's going on too early ..
- Agnostic and framework free - so compactibility rate is 100% ;)
- Configs are in : app/common.php => $mainHost
- Php default configuration : conf/php56.ini ( short_open_tags, auto_prepend_file )
---
- $a=1;# ordinary stand for : place where the xdebug breakpoints are


![visitors](https://visitor-badge.glitch.me/badge?page_id=gh:ben74:phpgit)