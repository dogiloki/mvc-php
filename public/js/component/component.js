class Component{

    /* SIN USO */
    static cache_hash={};

    static async hash(text){
        if(text.length>64){
            text=btoa(text);
        }
        if(Component.cache_hash[text]!=null){
            return Component.cache_hash[text];
        }
        let buffer=await crypto.subtle.digest("SHA-256",new TextEncoder().encode(text));
        let array=Array.from(new Uint8Array(buffer));
        let hash=array.map(byte=>byte.toString(16).padStart(2,'0')).join('');
        Component.cache_hash[text]=hash;
        return hash;
    }
    /* SIN USO */
    

    constructor(name,element){
        this.name=name;
        this.element=element;
        this.vars=this.getVars(element);
        this.actions=[];
        this.wires=[];
        this.render();
    }

    getVars(element){
        let vars={};
        let attributes=Array.from(element.attributes);
        attributes.forEach((attribute)=>{
            let name=attribute.name;
            if(name.startsWith(":")){
                vars[name]=attribute.value;
            }
        });
        return vars;
    }

    getWires(key_focus=null,vars={}){
        let component=this;
        component.wires=[];
        let collection=component.element.getElementsByTagName('*');
        for(let a=0; a<collection.length; a++){
            let element=collection[a];
            let attributes=Array.from(element.attributes);
            let key=component.name+"-";
            let id=element.getAttribute("id");
            attributes.forEach((attribute)=>{
                if(attribute.name.startsWith("wire:")){
                    let text=attribute.name.substring(5);
                    let type=text;
                    let content=attribute.value;
                    let wire=new Wire(element,type,content)
                    component.wires.push(wire);
                    component.setEventListener(wire);
                    if(id!=null){
                        key+=id;
                    }else{
                        key+=element.tagName+"-"+attribute.name+"-"+content.match(/^([^()]+)/)[1];
                    }
                    key=key.toLowerCase();
                    element.setAttribute("key",key);
                }
            });
            if(key_focus==key){
                element.focus();
            }
        }
        this.syncVars(vars);
    }

    syncVars(vars){
        this.wires.map((wire)=>{
            let value=vars[wire.content];
            if(wire.getValue()!=value){
                wire.setValue(value);
                this.vars[wire.content]=value;
            }
            return wire;
        });
    }

    render(){
        let component=this;
        let component_send={...component};
        component_send.element=null;
        component_send.wires=component.wires.filter((wire)=>{
            return wire.listener!="none" && wire.listener!=null;
        });
        Fetch.post("component/"+this.name,(data)=>{
            let json=JSON.parse(data);
            let html=json.html;
            let vars=json.vars;
            let doc=new DOMParser().parseFromString(html,"text/html");
            try{
                let element_focus=document.activeElement;
                component.element.replaceChild(doc.body.firstChild,component.element.firstChild);
                this.getWires(element_focus.getAttribute("key"),vars);
            }catch(error){

            }
        },{
            "body":new URLSearchParams({json:JSON.stringify(component_send)})
        });
    }

    setEventListener(wire){
        switch(wire.type){
            case Wire.TYPE_WIRES.MODEL.text:{
                wire.element.addEventListener("input",()=>{
                    this.vars[wire.content]=wire.getValue();
                    this.render();
                });
                break;
            }
            case Wire.TYPE_WIRES.CLICK.text:{
                wire.element.addEventListener("click",()=>{
                    this.actions.push(wire.getAction());
                    this.render();
                    this.actions=[];
                });
                break;
            }
        }
    }
    
}