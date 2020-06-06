<?
class dynamicThumbnailsView extends base{
    static function main(){}
}

$title='Dynamic Thumbnails Generator : webp && Lazyload !';
require_once 'z/header.php';

?>
<fieldset><legend><?=$title?></legend>
    <img data-src="/z/tn-w200-h50-alpow.png.webp" class="ok"/>
    <img data-src="/z/tn-w500-alpow.png" class="notseenbeforescrolling"/>
    <img data-src="/z/tn-h100-alpow.png" class="ok"/>
    <img data-src="/z/tn-h100-w100-alpow.png" class="notseenbeforescrolling"/>
<hr><pre>Scroll to get lazyload in action or read console output :)
tn-w500
tn-h500
tn-h{{500}}-w{{500}}
</pre>
    <?
    echo str_repeat('<br>',100);?>
    <img data-src="/z/tn-w1000-h50-alpow.png.webp" class="bottom"/>
</fieldset>
<style>img{border:1px dashed #F00;}</style>
<script src="/z/lazyload.js"></script>

