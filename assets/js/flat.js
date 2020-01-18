(function(){
    //render content
    function ActivateAll(){

        var buttons = document.querySelectorAll("article div button");

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
    }
    ActivateAll();

})();