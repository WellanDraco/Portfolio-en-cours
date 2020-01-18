AFRAME.registerComponent('camerarig', {
    init: function () {
       //test mobile
        console.log(AFRAME.utils.device.isMobile());
        if(AFRAME.utils.device.isMobile()){
            console.log("mobile");
        }
    }
});