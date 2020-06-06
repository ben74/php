/**aliases*/
gpn=GPN;nf=function(){},cl=console.log;
//cl=nf;
// variables
var si={},d=document,sr=d.currentScript.src,canvas,folder,context,dropArea,count,destinationUrl,result,list = [],totalSize = 0,totalProgress = 0
,limit=gpn('limit')||999999999
,max=gpn('max')||99
,k=gpn('k')||'myfile';

function dg(x){return d.getElementById(x);}

si.i1=setInterval(function(){
	cl('i1');
	if(d.querySelectorAll('canvas').length){
		folder=dg('folder');
		dropArea=dg('dropArea');count=dg('count');destinationUrl=dg('url');result=dg('result');canvas=d.querySelector('canvas');context=canvas.getContext('2d');
		clearInterval(si.i1);FIRE();cl('fire');
	}
},200);


// initialisation
function FIRE(){
    function initHandlers() {
        dropArea.addEventListener('drop', handleDrop, false);
//drag,dragenter,dragleave,dragover,dragend
        dropArea.addEventListener('dragover', handleDragOver, false);
        dropArea.addEventListener('dragleave', cancelDrop, false);
    }

    // affichage de la progression
    function drawProgress(progress) {
        context.clearRect(0, 0, canvas.width, canvas.height); // effacer le canvas
        context.beginPath();
        context.strokeStyle = '#4B9500';
        context.fillStyle = '#4B9500';
        context.fillRect(0, 0, progress * 500, 20);
        context.closePath();
        // affichage de la progression (mode texte)
        context.font = '16px Verdana';
        context.fillStyle = '#000';
        context.fillText('Progression : ' + Math.floor(progress*100) + ' %', 50, 15);
    }

    // survol lors du déplacement
    function handleDragOver(event) {
        event.stopPropagation();
        event.preventDefault();
        dropArea.className = 'hover';
    }

    // drop canceled
    function cancelDrop(event) {
        dropArea.className = '';
    }

    // drop done
    function handleDrop(event) {
        event.stopPropagation();
        event.preventDefault();
        dropArea.className = '';
        processFiles(event.dataTransfer.files);
    }

    // traitement du lot de fichiers
    function processFiles(filelist) {
        if (!filelist || !filelist.length || list.length) return;
        totalSize = 0;
        totalProgress = 0;
        result.textContent = '';
/** one by one **/
        for (var i = 0; i < filelist.length && i < max; i++) {
            list.push(filelist[i]);
            totalSize += filelist[i].size;
        }
        uploadNext();
    }

    // à la fin, traiter le fichier suivant
    function handleComplete(size) {
        totalProgress += size;
        drawProgress(totalProgress / totalSize);
        uploadNext();
    }

    // mise à jour de la progression
    function handleProgress(event) {
        var progress = totalProgress + event.loaded;
        drawProgress(progress / totalSize);
    }

    // transfert du fichier
    function uploadFile(file, status) {
        // création de l'objet XMLHttpRequest
        var xhr = new XMLHttpRequest();
        xhr.open('POST',destinationUrl.value);
				
        xhr.onload=function(a,b,c){cl({a,b,c});result.innerHTML +=  '<li><a target=upped href="' + this.responseText + '">' + this.responseText + '</a>';handleComplete(file.size);};//'<li>'+this.responseText;
        xhr.onerror=function(a,b,c){cl({a,b,c});result.textContent = this.responseText;handleComplete(file.size);};
        xhr.upload.onprogress=function(a,b,c){handleProgress(a);}/** +8MO => fails cl({a,b,c}); */
        xhr.upload.onloadstart=function(a,b,c){cl({a,b,c});}
        // création de l'objet FormData
        var formData=new FormData();
        formData.append('folder',folder.value);
        formData.append(k,file);
        xhr.send(formData);
    }

    // transfert du fichier suivant
    function uploadNext() {
        if (list.length) {
            count.textContent = list.length - 1;
            dropArea.className = 'uploading';

            var nextFile = list.shift();
            if (nextFile.size >= limit) { // 256 kb
                result.innerHTML += '<div class="f">Fichier trop gros (dépassement de la taille maximale)</div>';
                handleComplete(nextFile.size);
            } else {
                uploadFile(nextFile, status);
            }
        } else {
            dropArea.className = '';
        }
    }

    initHandlers();
}


var d = document, items, files, xhr, formData, extension;
document.onpaste = function (e) {
    items = e.clipboardData.items;
    files = [];
    console.error({items});
    for (var i = 0, len = items.length; i < len; ++i) {
        var item = items[i];
        if (item.kind === 'file') {
            submitFileForm(item.getAsFile(), 'paste');
        }
    }
};

function submitFileForm(file, type) {
    extension = file.type.match(/\/([a-z0-9]+)/i)[1].toLowerCase();
    formData = new FormData();
    formData.append('paste', file, 'image_file');
    formData.append('extension', extension);
    formData.append('mimetype', file.type);
    formData.append('submission-type', type);
    xhr = new XMLHttpRequest();
//xhr.responseType='blob';//for showing instant image result
    xhr.open('POST',postto);
    xhr.onload = function (a, b, c) {
        console.error({extension, a, b, c, xhr});
        if (xhr.status == 200) {
            d.getElementById('result').innerHTML += '<li><a target=upped href="' + xhr.responseText + '">' + xhr.responseText + '</a>';
            return;
            console.error({a, b, c, xhr});//var img = new Image();img.src = (window.URL || window.webkitURL).createObjectURL( xhr.response );d.body.appendChild(img);//result
        }
    };
    xhr.send(formData);
}


//todo : explode query string parameters
function GPN(x,url){
	
	var url=url || sr || location.search.substr(1),result=null,v=[];//i=0 = base url
	if(url.indexOf('&'+x+'=')<0 && url.indexOf('?'+x+'=')<0)return null;
	var t=url.replace(/\?/g,'&').split('&'),resA={};
	
	for(var k = 1; k < t.length; k++) {
		v=t[k].split("=");
		if(v[1] == 'undefined' || !v[1])v[1]='';
		res=decodeURIComponent(v[1]);
		if (x !==1 && v[0] === x)return res;
		resA[v[0]]=res;
	}
	return resA;
}
