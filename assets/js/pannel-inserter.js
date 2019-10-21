AFRAME.registerComponent('pannelinserter',{
    schema:{type:'boolean',default:false},
    update : function(){
        var ready = this.data;
        if(ready){
            console.log(this);
            console.log("ready");
            


        }
    }
});