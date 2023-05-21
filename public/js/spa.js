class SPA{

    constructor(){
        let collection=document.getElementsByTagName('*');
        this.components={};
        this.wires={};
        for(let i=0;i<collection.length;i++){
            let element=collection[i];
            let name=element.tagName.toLowerCase();
            if(name.startsWith("component:")){
                let component=name.substring(10);
                this.components[component]=element.attributes;
                this.renderComponent(component);
            }
            let attributes=element.attributes;
            for(let index=0; index<attributes.length; index++){
                let attribute=attributes[index];
                if(attribute.name.startsWith("wire:")){
                    let wire=attribute.name.substring(5);
                    this.wires['model']={
                        "element":element,
                        "attribute":wire
                    };
                    this.listenerModel(element,wire);
                }
            }
        }
    }
    
    renderComponent(name){
        let content=Array.from(document.getElementsByTagName('component:'+name))[0];
        let attributes=Array.from(this.components[name]);
        let params={};
        attributes.forEach((attribute)=>{
            params[attribute.name]=attribute.value;
        });
        Fetch.post("component/"+name,(data)=>{
            content.innerHTML=data;
        },{
            "dataType":"html",
            "body":new URLSearchParams(params)
        });
    }

    listenerModel(element,wire){
        element.addEventListener('keyup',()=>{
            let value=element.value;
            let params={};
            let method=wire.match(/^([^()]+)/)[1];
            let attrib=wire.match(/"([^"]+)"/)[1];
            let content=Array.from(document.getElementsByTagName('component:'+method))[0];
            params[attrib]=value;
            Fetch.post("component/"+method,(data)=>{
                content.innerHTML=data;
            },{
                "body":new URLSearchParams(params)
            });
        });
    }

}

window.spa=new SPA();