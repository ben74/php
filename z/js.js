/*ok*/var dev = 1, post=[],postdata = [], shutdowns = [], errors = [], ww = window.innerWidth, desktop = 1, mobile = 0, site_img_path = site_img_path || '', lang = lang || 'fr', ip
 = ip || '', cl = console.log,nf = function () {return;}, inputs,selects,textareas,url,str,json,x,topH,hash=location.hash.replace(/^#/gi,'').replace(/#/gi,'&'),qs=parseQuery(hash);;

if (window.location.href.indexOf('.home') < 0 && window.location.href.indexOf('.preprod2') < -1) {
	dev = 0;
	cl = nf;
}

window.onerror = errorhandler;
if (ww < 1000) {
	desktop = 0;
	mobile = 1;
}

window.onbeforeunload=function(e){//F5, click, etc ..
	var ael=document.activeElement;//BODY,1 => is refresh or new url typed or closed, but can't do that much upon closing except message
	cl('unloads',ael,ael.tagName,shutdowns.length,shutdowns);
	for(var i in shutdowns){shutdowns[i]();}
	return;//No unload dialog -- sends analytics stats for going elsewhere
	//return true;
	//e.preventDefault();//prevents Nav
	//(e || window.event).returnValue = null;     // Gecko + IE
	//return confirmationMessage;//Etes vous certain de vouloir quitter cette page ?
}
function sc(name,value,days=365) {
	var date = new Date();
	date.setTime(date.getTime() + (days*24*60*60*1000));
	expires = "; expires=" + date.toUTCString();
	document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}


function ajaxForm(el,callback){
	callback=callback||nf;str='';url=el.action;postdata=[];
	inputs=el.querySelectorAll('input');inputs.forEach(inputName);
	selects=el.querySelectorAll('select');selects.forEach(inputName);
	textareas=el.querySelectorAll('textarea');textareas.forEach(inputName);
	for(var i in postdata){
		cl('s',i,postdata[i]['name'],postdata[i]['value']);
		//post.push(postdata[i]['name']+'='+postdata[i]['value']);
		str+='&'+postdata[i]['name']+'='+postdata[i]['value'];
	}
	ajax(url,el.method.toUpperCase(),str,callback);
	return false;
}

function ajax(url,method,data,callback){
	url=url||'?';method=method||'POST';data=data||'';callback=callback||nf;
	var res,xhr = new XMLHttpRequest();
	xhr.open(method, url, true);
	xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
	if(method=='POST')xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	xhr.onload = function () {
		callback(this.responseText);
		cl({'response':this.responseText});
	};
	xhr.send(data);
	cl({url,method,data});
}

function errorhandler(desc, page, line, chr, errobj) {
	if(/bat\.js|facebook\.net/i.test(page))return true;//dontcare
	var err=(desc+page+line+'').toLowerCase();
	if(errors.indexOf(err)>-1)return true;
	cl(desc, page+':'+line, chr);//desc, page, line, chr, //	script error.
	//if(errobj)console.error(errobj);//desc, page, line, chr, //	Script error. :0 0 => is undefined function
	if(dev)return true;
	ajax('/tag.php','POST',('jsx='+desc+'&page='+page+'&line='+line+'&loc='+window.location.href).toLowerCase());
	return true;
}

/*jquery ajaxify form*/
function defer(method, timeout) {
	var timeout = timeout || 50;
	if (window.jQuery) {
		method();
	} else {
		setTimeout(function () {
			defer(method)
		}, timeout);
	}
}

function inputName(input) {
	if (input.name && input.value && !input.disabled) {
		if (['radio','checkbox'].indexOf(input.type)>-1) {
			if (input.checked) {
				postdata.push({name: input.name, value: input.value});
				return input.value;
			}
		} else {
			postdata.push({name: input.name, value: input.value});
			return input.value;
		}
		//postdata.concat({name:input.name,value:input.value});
		//postdata[input.name]=input.value;
	}
	return;
}

function isJson(str) {
	try {
		JSON.parse(str);
	} catch (e) {
		return false;
	}
	return true;
}

window.addEventListener('hashchange',onHashChange, false);//au démarrage ;)
function onHashChange(){
	hash=location.hash.replace(/^#/gi,'').replace(/#/gi,'&');//plusieurs à la suite
	if(!hash)return;
	qs=parseQuery(hash);x=topH=0;
	if(qs['c']) {qs['click']='.'+qs['c'];}//#c=bestsellers
	if(qs['click']){//#click=.bestsellers
		x=document.querySelector(qs['click']);
		if(x){
			x.click();
			topH=(window.scrollY)+(x.getBoundingClientRect().top);
			setTimeout(function(){window.scrollTo(0,topH-50);},25);
			location.hash='';//neutralize
			//top=getOffset(x)['top'];
		}
	}
	cl({hash,qs,x,topH});
}

function parseQuery(queryString) {
	var query = {};
	var pairs = (queryString[0] === '?' ? queryString.substr(1) : queryString).split('&');
	for (var i = 0; i < pairs.length; i++) {
		var pair = pairs[i].split('=');
		query[decodeURIComponent(pair[0])] = decodeURIComponent(pair[1] || '');
	}
	return query;
}

function getOffset(el) {
	const rect = el.getBoundingClientRect();
	return {
		left: rect.left + window.scrollX,
		top: rect.top + window.scrollY
	};
}

function mobileAndTabletCheck() {
    let check = false;
    (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
    return check;
};

function getCookie(e){return document.cookie.length>0&&(begin=document.cookie.indexOf(e+"="),-1!=begin)?(begin+=e.length+1,end=document.cookie.indexOf(";",begin),-1==end&&(end=document.cookie.length),unescape(document.cookie.substring(begin,end))):null}   

function setCookie(name,value,expires,path,domain,secure){path=path||'/';var today = new Date();today.setTime(today.getTime());if(expires){expires = expires * 1000* 3600* 24;} else{expires=1000*60*30;}var expires_date = new Date( today.getTime() + (expires) );document.cookie=name+"="+escape(value)+ ((expires)?";expires="+expires_date.toGMTString():"")+ ((path)?";path="+path:"")+ ((domain)?";domain="+domain:"")+ ( ( secure ) ? ";secure" : "" );}

Object.size = function(obj) {var size = 0, key;for (key in obj) {if (obj.hasOwnProperty(key)) size++;}return size;};