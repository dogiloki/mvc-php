class Component{

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

    getWires(){
        let component=this;
        let collection=component.element.getElementsByTagName('*');
        for(let a=0; a<collection.length; a++){
            let element=collection[a];
            element.setAttribute("id",component.name+"-"+a);
            let attributes=Array.from(element.attributes);
            attributes.forEach((attribute)=>{
                if(attribute.name.startsWith("wire:")){
                    let text=attribute.name.substring(5);
                    let type=text;
                    let content=attribute.value;
                    let wire=new Wire(element,type,content)
                    component.wires.push(wire);
                    component.setEventListener(wire);
                }
            });
        }
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
            let collection_old=Array.from(component.element.getElementsByTagName('*'));
            let collection_new=Array.from(doc.body.getElementsByTagName('*'));
            if(collection_old.length==0){
                component.element.innerHTML=html;
                this.getWires();
            }else{
                try{
                    collection_old.forEach((element_old,index)=>{
                        let element_new=collection_new[index]??null;
                        if(element_new==null && element_old!=null){
                            component.element.removeChild(element_old);
                        }
                        if(element_new.textContent!=element_old.textContent && element_new!=null && element_old!=null){
                            component.element.replaceChild(element_new,element_old);
                        }
                    });
                }catch(error){

                }
            }
            this.syncVars(json.vars);
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