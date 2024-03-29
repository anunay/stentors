/*
 --
 -- images.js - Librairie de fonctions pour gerer les images en JavaScript
 --
 -- Par CIBLE Solutions d'affaires
 --     www.ciblesolutions.com
 --
 -- Plusieurs de ces fonctions sont des adaptations des fonctions
 -- de Macromedia DreamWeaver et de Adobe GoLive.
 --
 -- R�VISIONS
 -- 2008/12/17 JPB-    Cr�ation
 --
*/

// Variable globale: PreloadFlag - Indique que les images ont ete pre-chargees
var PreloadFlag = false;

// Variable globale: ArrayPreload - Indique quelles sont les images a pre-charger
// ainsi : ArrayPreload[ArrayPreload.length] = "Source de l'image";
var ArrayPreload = new Array();

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * changeImage() - Remplace la source des images donnees
 *
 * Parametres :
 *   Une suite de Name, Source, Name, Source, ...
 */
function changeImages()
{
	if (document.images && (PreloadFlag == true))
    {
		for (var i=0; i < changeImages.arguments.length; i += 2)
        {
			document[changeImages.arguments[i]].src = changeImages.arguments[i+1];
		}
	}

	void 0;
}

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * preloadImages() - Pre-charge les images contenues dans ArrayPreload
 *                   en memoire
 */
function preloadImages()
{

	if (document.images)
	{
		for (var i = 0; i < ArrayPreload.length; i++)
		{
            (new Image).src = ArrayPreload[i];
		}

		PreloadFlag = true;
	}
	
	void 0;
}

function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}

<!--
function MM_reloadPage(init) {  //reloads the window if Nav4 resized
  if (init==true) with (navigator) {if ((appName=="Netscape")&&(parseInt(appVersion)==4)) {
    document.MM_pgW=innerWidth; document.MM_pgH=innerHeight; onresize=MM_reloadPage; }}
  else if (innerWidth!=document.MM_pgW || innerHeight!=document.MM_pgH) location.reload();
}
MM_reloadPage(true);






