/* adapted from lazyload.js (c) Lorenzo Giuliani
 * MIT License (http://www.opensource.org/licenses/mit-license.html)
 *
 * expects a list of:
 * `<img src="blank.gif" data-src="my_image.png" width="600" height="400" class="lazy">`
 */
function nf(x){return;}

var msScrollExpire=200,scrolling=0,rect,tg='t1',q1 = 'img[data-src]:not(.'+tg+')'
    ,imgInViewport=[],images=[],loaded=[],to={}
    ,cl=console.log
;
if(document.location.host.indexOf('.home')<0 && document.location.host.indexOf('192.168')<0){
    cl=nf
}

!function(window){
    //cl(1);
    var $q = function(q, res){
            if (document.querySelectorAll) {
                res = document.querySelectorAll(q);
            } else {
                var d=document
                    , a=d.styleSheets[0] || d.createStyleSheet();
                a.addRule(q,'f:b');
                for(var l=d.all,b=0,c=[],f=l.length;b<f;b++)
                    l[b].currentStyle.f && c.push(l[b]);

                a.removeRule(0);
                res = c;
            }
            return res;
        }
        , addEventListener = function(evt, fn){
            window.addEventListener
                ? this.addEventListener(evt, fn, false)
                : (window.attachEvent)
                ? this.attachEvent('on' + evt, fn)
                : this['on' + evt] = fn;
        }
        , _has = function(obj, key) {
            return Object.prototype.hasOwnProperty.call(obj, key);
        }
    ;

    function loadImage (el, fn) {
        //if(fn)images.splice(fn, 1);//Removes from the whole stack
        var img = new Image(), src = el.getAttribute('data-src');//cl(src);
        el.className+=' '+tg;
        img.onload = function() {
            if (!! el.parent) el.parent.replaceChild(img, el)
            else el.src = src;
            //fn? fn() : null;
        }
        img.src = src;
    }

    function elementInViewport(el) {
        rect = el.getBoundingClientRect();
        var ok=(rect.top>= 0 && rect.left   >= 0 && rect.top <= (window.innerHeight || document.documentElement.clientHeight));
        cl(el,ok,rect);
        return ok;
    }

    function processScroll(){
        if(scrolling){cl('scrolling');return;}scrolling=1;clearTimeout(to['scroll']);to['scroll']=setTimeout(function(){cl('scroll timeout');scrolling=0;},msScrollExpire);
        // scrolling too much
        if(!images.length){cl('No more -- remove event listener');removeEventListener('scroll',processScroll);return;}
        var iml=images.length;imgInViewport=[];loaded=[];//cl('images1:',iml);
        for (var i = 0; i < iml; i++) {//removed
            if (elementInViewport(images[i])) {
                imgInViewport.push(images[i]);
//months.splice(1, 0, 'Feb');// inserts at index 1
                loadImage(images[i], i);
                loaded.push(i);//function () {images.splice(i, 1);});//Removes Loaded Image
            }
        };
        if(loaded.length){
            //for (var i = 0; i < loaded.length; i++) {
            for (var i = loaded.length-1; i >-1; i--) {//Array reverse splice, cauz it rebuilds indexes .. could delete to far image
                //cl(loaded[i],images[loaded[i]]);
                images.splice(loaded[i], 1);
            }
            cl('loaded',loaded.length,'Remaining to load',images.length,'currently loading in viewport',imgInViewport.length);
        }
    }

    // Array.prototype.slice.call is not callable under our lovely IE8
    var query=$q(q1);
    for (var i = 0; i < query.length; i++) {images.push(query[i]);};
    cl('images:',images);
    processScroll();
    addEventListener('scroll',processScroll);
}(this);
