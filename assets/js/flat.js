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
                tooglePannelInserter();
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
        /*
        //console.log(json);

        //render "a propos"
        var aproposJson = json.pages.find(obj => obj.slug === 'presentation');
        var renderedApropos = json.rendered.pages[json.pages.indexOf(aproposJson)];
        //console.log(json.rendered.pages.indexOf(aproposJson));
        //console.log(renderedApropos);

        insertOne(renderedApropos,document.getElementById('about'),true);

        //render "projets"
        var projetsjson = json.travaux;
        for(let i = 0;i < projetsjson.length ;i++){
            insertOne(json.rendered.travaux[i],document.querySelector("#projects .articles"),false);
        }

        //render "contact"
        var contactjson = json.pages.find(obj => obj.slug === 'contact');
        var renderedContact = json.rendered.pages[json.pages.indexOf(contactjson)];
        insertOne(renderedContact,document.getElementById('contact'),false);
*/
        var buttons = document.querySelectorAll("article div button");
        console.log(buttons);

        for(let i = 0;i<buttons.length;i++){
            let el = buttons[i];
            el.addEventListener('click', showmore);
        }
    }

    function insertOne(rendered,DadTarget,removeOldContent){
        if(removeOldContent){
            DadTarget.innerHTML = "";
        }
        DadTarget.innerHTML += rendered;
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
    


    function tooglePannelInserter(){
        document.querySelector("#layout").setAttribute("pannelinserter",true);
    }

})();