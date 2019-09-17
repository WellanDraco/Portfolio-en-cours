(function(){
    "use strict";

    // get content

    const request = new XMLHttpRequest();
    request.open('GET','http://arthur-moug.in/api/',false);
    request.send();

    //s'il n'y a pas de contenu
    if(request.status !== 200){
        return null;
    }

    console.log(request);


})();