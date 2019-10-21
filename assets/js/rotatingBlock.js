AFRAME.registerComponent('rotatingblock',{
    schema:{
       target:{
           type:'string'
       }
    },
    update : function(){
        console.log(this);
    }
});