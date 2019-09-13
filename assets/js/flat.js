// travaux = https://back.arthur-moug.in/wp-json/wp/v2/travaux
// media = https://back.arthur-moug.in/wp-json/wp/v2/media
// pages = https://back.arthur-moug.in/wp-json/wp/v2/pages
// posts = https://back.arthur-moug.in/wp-json/wp/v2/posts


//Liste des apis à appeller
var content = [
    {url : 'travaux',
    contenu : [],
    query : new XMLHttpRequest()},
    {url : 'pages',
    contenu : [],
    query : new XMLHttpRequest()},
    {url : 'posts',
    contenu : [],
    query : new XMLHttpRequest()}
];
let i;

//appel de chaque API
for(i = 0; i<content.length; i++){
    var el = content[i];
    el.query.onreadystatechange = function (e) {
        if(this.readyState === XMLHttpRequest.DONE){
            if(this.status === 200){
                importFromRequest(this.responseText);
            }else {
                console.log("Status de la réponse: %d (%s)", this.status, this.statusText);
            }
        }
    }
    el.query.open('GET', 'https://back.arthur-moug.in/wp-json/wp/v2/'+el.url, true);
    el.query.send(null);
}

//remplissage de l'objet avec les reponses de l'api
function importFromRequest(r){
    var rep = JSON.parse(r);

    //si le tableau est vide
    if(!rep[0]) return;

    console.log(rep);
    

}