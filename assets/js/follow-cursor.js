AFRAME.registerComponent('followcursor',{
    schema:{
        initOrientation: {
            type:'vec2',
            default:{x:-101,y:0}
        },

    },
    init:function(){

        console.log(this);
        var el = this.el;
        var data = this.data;
        var maxX = screen.width;
        var maxY = screen.height;

        //fallback pour les anciennes versions de navigateur
        if (window.Event) {
            document.captureEvents(Event.MOUSEMOVE);
        }
        document.addEventListener('mousemove',UpdateOrientation);

        function UpdateOrientation(e) {
            var x = (window.Event) ? e.pageX : event.clientX + (document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft);
            var y = (window.Event) ? e.pageY : event.clientY + (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop);
            var CursorCoords = centerCoords({x:x,y:y});
            var CamRotation = {
                x:convPosToRot(CursorCoords.y + data.initOrientation.x),
                y:convPosToRot(CursorCoords.x + data.initOrientation.y)
            };
            applyOrientation(CamRotation);
        }

        function applyOrientation(a){
            el.object3D.rotation.x = a.x;
            el.object3D.rotation.y = a.y;
        }

        function centerCoords(cursorXY){
            cursorXY.x -= maxX/2;
            cursorXY.y -= maxY/2;
            return cursorXY;
        }

        function convPosToRot(a){
            //Convertion approximative du Pixel en Metre
            a = a/100;
            //on suppose que l'élément de repère est à 10m
            return Math.tan(a/5);
        }

        this.tick = AFRAME.utils.throttleTick(this.tick,500,this);
    },
    tick:function (t,dt) {

    }
});