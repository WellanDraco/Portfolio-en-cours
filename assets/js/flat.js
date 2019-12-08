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
                renderAll();
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
    function renderAll(){

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