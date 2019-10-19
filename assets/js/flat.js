(function(){
    "use strict";

    //get content
    const request = new XMLHttpRequest();
    request.open('GET','https://arthur-moug.in/api/',true);
    request.onload = function (e) {
        if (request.readyState === 4) {
            if (request.status === 200) {
                //console.log(request.responseText);
                var rep = JSON.parse( request.responseText );
                //console.log(rep);
                renderAll(rep);
            } else {
                console.error(request.statusText);
                return null;
            }
        }
    };
    request.onerror = function (e) {
        console.error(request.statusText);
    };
    request.send();

    //render content
    function renderAll(json){
        //console.log(json);

        //render "a propos"
        var aproposJson = json.pages.find(obj => obj.slug === 'presentation');
        renderOne(aproposJson,document.getElementById('about'),true);

        //render "projets"
        var projetsjson = json.travaux;
        for(let i = 0;i < projetsjson.length ;i++){
            renderOne(projetsjson[i],document.querySelector("#projects .articles"),false);
        }

        //render "contact"
        var contactjson = json.pages.find(obj => obj.slug === 'contact');
        renderOne(contactjson,document.getElementById('contact'),true);

        var buttons = document.querySelectorAll("article div button");
        //console.log(buttons);

        for(let i = 0;i<buttons.length;i++){
            let el = buttons[i];
            el.addEventListener('click', showmore);

        }
    }

    function showmore(event){
        let e = event.target;
        let excerpt = e.parentNode.parentNode.childNodes[1];
        let content = e.parentNode.parentNode.childNodes[2];
        excerpt.classList.toggle('show');
        excerpt.classList.toggle('hide');
        content.classList.toggle('show');
        content.classList.toggle('hide');
    };

    function renderOne(article,DadTarget,removeOldContent){
        //console.log(article,DadTarget);

        //logic
        let UseExcerpt = (article.excerpt.rendered && (article.excerpt.rendered.trim() !== article.content.rendered.trim()));
        let haveUrl = article.x_metadata.hasOwnProperty("url");
        let haveThumbnail = article.featured_media !== 0;

        /**
         * <article>
         *     <div class='titleContainer'>
         *         <h1>titre</h1>
         *         if(haveUrl) <a href='url'>url</a>
         *         if(haveThumbnail) <img  srcset  src>
         *     </div>
         *     if(UseExcerpt){
         *          <div class='excerpt show'>
         *              <p>excerpt</p>
         *              <button>more</button>
         *          <div>
         *          <div class='content hide'>
         *              content
         *          </div>
         *     } else {
         *          <div class='content show'>
         *              content
         *          </div>
         *     }
         *
         * </article>
         */
        //article container (la div sert pour etre le parent document et transformer l'article en parent dom section
        var AC = "<div><article>" +
            "<div class='titleContainer'>" +
            "<h2>" + article.title.rendered + "</h2>" +
            ((haveUrl)? ("<a href='" + article.x_metadata.url + "'>" + article.x_metadata.url + "</a>"): "");

        if(haveThumbnail){

            let thumbnails = [
                {
                    name : 'media',
                    url : article.x_featured_media,
                    size : 0
                },
                {
                    name : 'media_medium',
                    url : article.x_featured_media_medium,
                    size : 0
                },
                {
                    name : 'media_large',
                    url : article.x_featured_media_large,
                    size : 0
                },
                {
                    name : 'media_original',
                    url : article.x_featured_media_original,
                    size : 0
                },
            ];

            //on extrait des informations principales du fichier original
            //console.log("thumbnails: ",thumbnails);
            var originalSplitedName = thumbnails[3].url.split(".");
            //console.log("originalSplitedName: ",originalSplitedName);
            var originalName = originalSplitedName[0]+"."+originalSplitedName[1]+"."+originalSplitedName[2];
            //console.log("originalName: ",originalName);
            var extention = "."+ originalSplitedName[2];
            //console.log("extention: ",extention);

            // on se ballade dans les 3 premières images pour chercher si elles ont des dimensions dans leur nom

            for(let i = 0;i<thumbnails.length -1;i++){

                let el = thumbnails[i];
                //on extrait du nom les contenus connus
                var shortUrl = el.url.replace(originalName,'').replace(extention,'');

                if(shortUrl !== "") {

                    //si il y a bien des dimensions, on les nettois et on met la largeur dans la valeur size
                    //console.log(shortUrl);
                    var splitedUrl = shortUrl.split('-');
                    var dimensions = splitedUrl[splitedUrl.length -1];
                    //console.log(dimensions);
                    el.size = dimensions.split('x')[0];
                    //console.log(el.size );

                    //console.log("test");

                }

            }
            //console.log(thumbnails);

            //on crée le srcset
            AC += "<img alt='thumbnail de "+article.title.rendered+"' srcset='";
            for(let i = 0;i<thumbnails.length -1;i++){
                let el = thumbnails[i];
                if(el.size !== 0){
                    AC += el.url + " " + el.size + "w, "
                }
            }
            AC+= "' src='"+thumbnails[3].url+"'></img>";
        }

        if(haveUrl){
            let u = article.x_metadata.url;
            AC += "<a href='" + u + "'>" + u + "</a>";
        }
        //fin de la div de titre
        AC += "</div>";

        let contentHTML = article.content.rendered.trim().replace('\n','<br>');

        //si c'est un article
        if(article.slug === "contact"){
            //on regarde dans les metas

            contentHTML += "<ul>";

            let contacts = article.x_metadata;
            for(let prop in contacts){
                //console.log(prop);
                let NotAccepted = new RegExp("_");
                //on cherche a exclure les trucs avec un _
                if(!NotAccepted.test(prop)){
                    //console.log("accepted");

                    if(prop === "email") contacts[prop] = "mailto:"+ contacts[prop];
                    if(prop === "téléphone") contacts[prop] = "tel:"+ contacts[prop].replace(/\s+/g,'');

                    contentHTML += "<li class='"+ prop + "'><a href='"+contacts[prop]+"'>"+ prop +"</a></li>";

                }

            }

            contentHTML += "</ul>";
        }

        if(UseExcerpt){

            AC+="<div class='excerpt show'>" + article.excerpt.rendered + "<button>en savoir plus</button></div>";
            AC+="<div class='content hide'><div class='inner'>"+ contentHTML + "</div><button>réduire</button></div>";

        }
        else {

            AC+="<div class='content show'>"+ contentHTML + "</div>";

        }



        AC += "</article></div>";
        //console.log(AC);

        if(removeOldContent){
            DadTarget.innerHTML = "";
        }
        const parser = new DOMParser();
        var DomAC = parser.parseFromString(AC,"text/html");
        var realAC = DomAC.querySelector('article');
        //console.log(realAC);
        DadTarget.appendChild(realAC);
    }
    



})();