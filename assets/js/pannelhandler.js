AFRAME.registerComponent('pannelhandler',{
    schema:{type:'boolean',default:false},
    init : function(){
        var ready = this.data;
        if(ready){
            console.log(this);
            console.log("ready");
            var el = this.el;
            var displays = el.querySelectorAll(".PannelHolder>*");
            console.log(displays);
            for (let i = 0; i < displays.length; i++){
                
            }

        }
    }
});