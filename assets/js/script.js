var elements = document.querySelector('header');
Stickyfill.addOne(elements);

console.log("Le premier qui regarde dans la console a perdu...");
setTimeout(function(){console.log("et vous avez perdu");}, 5000);

//Ce site est concu pour etre No-script friendly... cool non ?
