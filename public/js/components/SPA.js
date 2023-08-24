class SPA{

    constructor(){
        this.components=[];
        let collection=Array.from(document.getElementsByTagName('*'));
        collection.forEach((element)=>{
            if(element.tagName.startsWith('COMPONENT:')){
                this.components.push(new Component(element.tagName.substring(10),element));
            }
        });
    }
    
}

window.spa=new SPA();