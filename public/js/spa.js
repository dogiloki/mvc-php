class Component{

    static TYPE_WIRES=Object.freeze({
        CHANGE:{
            id:Symbol(),
            text:"change"
        },
        CLICK:{
            id:Symbol(),
            text:"click"
        },
        IGNORE:{
            id:Symbol(),
            text:"ignore"
        }
    });

    constructor(component){
        let name=component.tagName.substring(10);
        this.content=component;
        this.name=name;
        this.wires=[];
        this.params={};
        this.getParams();
        this.render();
    }

    getWires(){
        let collection=this.content.getElementsByTagName('*');
        for(let a=0; a<collection.length; a++){
            let element=collection[a];
            let attributes=Array.from(element.attributes);
            attributes.forEach((attribute)=>{
                if(attribute.name.startsWith("wire:")){
                    let wire=attribute.name.substring(5);
                    let listener=wire;
                    let attrib=attribute.value;
                    //let listener=wire.match(/^([^()]+)/)[1];
                    //let attrib=wire.match(/"([^"]+)"/)[1];
                    this.setWires(element, listener, attrib);
                }
            });
        }
    }

    

    setWires(element, listener, attrib){
        let wire={
            "element":element,
            "listener":listener,
            "attrib":attrib,
            getValue:()=>{
                return element.value??element.innerHTML??element.src??element.href??element.data;
            }
        };
        //console.log(this.content.querySelector((element.tagName)+"[wire\\:"+listener+"=\""+attrib+"\"]"));
        if(listener==Component.TYPE_WIRES.IGNORE.text){

        }else{
            element.addEventListener(listener,()=>{
                this.params[attrib]=wire.getValue();
                this.render();
            });
        }
        this.wires.push(wire);
    }

    render(){
        Fetch.post("component/"+this.name,(data)=>{
            let doc=new DOMParser().parseFromString(data,"text/html");
            this.content.innerHTML=data;
            this.getWires();
        },{
            "body":new URLSearchParams(this.params)
        });
    }

}