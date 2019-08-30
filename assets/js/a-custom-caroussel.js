AFRAME.registerPrimitive('a-custom-caroussel',{
    defaultComponents:{
        layout:{type:'circle',radius:1},
        pannelinserter:{target:''}
    },
    mappings: {
        plane : 'layout.plane',
        radius : 'layout.radius',
        angle : 'layout.angle',
        target : 'pannelinserter.target'
    }
});