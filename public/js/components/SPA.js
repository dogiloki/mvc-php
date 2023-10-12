class SPA{

    constructor(){
        this.components={};
        let collection=document.querySelectorAll('[wire\\:name],[wire\\:render]');
        collection.forEach((element)=>{
            let name=element.getAttribute("wire:name")??element.getAttribute("wire:render");
            let component=this.components[name]??new Component();
            component.addWires(element);
            this.components[name]=component;
        });
        this.loadRenders();
    }

    loadRenders(){
        for(let name in this.components){
            let component=this.components[name];
            component.addWiresEvents();
        }
    }
    
}

window.spa=new SPA();