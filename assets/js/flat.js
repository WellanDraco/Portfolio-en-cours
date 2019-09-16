// travaux = https://back.arthur-moug.in/wp-json/wp/v2/travaux
// media = https://back.arthur-moug.in/wp-json/wp/v2/media
// pages = https://back.arthur-moug.in/wp-json/wp/v2/pages
// posts = https://back.arthur-moug.in/wp-json/wp/v2/posts


//Liste des apis à appeller
var content = {
    travaux: {
        url: 'travaux',
        contenu: {},
        query: new XMLHttpRequest()
    },
    page: {
        url: 'pages',
        contenu: {},
        query: new XMLHttpRequest()
    },
    posts: {
        url: 'posts',
        contenu: {},
        query: new XMLHttpRequest()
    }
};
var contentlist = [
    content.travaux,
    content.page,
    content.posts
]
let i;
var countDown = 0;

//appel de chaque API
for(i = 0; i<contentlist.length; i++){
    var el = contentlist[i];
    el.query.onreadystatechange = function (e) {
        if(this.readyState === XMLHttpRequest.DONE){
            if(this.status === 200){
                importFromRequest(this.responseText);
            }else {
                console.log("Status de la réponse: %d (%s)", this.status, this.statusText);
            }
            countDown--;
            if(!countDown) updateDom();
        }
    }
    el.query.open('GET', 'https://back.arthur-moug.in/wp-json/wp/v2/'+el.url, true);
    el.query.send(null);
    countDown++;
}

//remplissage de l'objet avec les reponses de l'api
function importFromRequest(r){
    var rep = JSON.parse(r);

    //si le tableau est vide, on s'arrête;
    if(!rep[0]) return;

    //sinon on met tous les éléments dans leur tableau
    for(i = 0;i< rep.length;i++){
        //dans la section contenu de l'objet désigné par le type de la réponse dans le parent "content"
        content[rep[i].type].contenu = rep[i];
    }
}

function updateDom(){
    console.log("dom updated")
}