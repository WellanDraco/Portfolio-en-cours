AFRAME.registerComponent('pannelinserter',{
    schema:{
       target:{
           type:'string'
       }
    },
    update : function(){
        console.log(this);
    }
});