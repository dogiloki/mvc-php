export default class Wire{

    static WIRE_EVENTS=Object.freeze({
        NAME:{
            name:"name"
        },
        SYNC:{
            name:"sync"
        },
        CLICK:{
            name:"click"
        }
    });

    constructor({
        element=null,
        value=null,
        wire_event=null,
        delay=250,
        renderable=false,
    }){
        this.element=element;
        this.value=value;
        this.wire_event=wire_event;
        //this.event;
        //this.attrib;
        //this.element_attrib;
        this.delay=delay;
        this.renderable=renderable;
    }

    getElementValue(){
        return this.element.value??this.element.textContent;
    }

    setElementValue(element_value){
        if(this.element.hasAttribute("value")){
            this.element.value=element_value;
        }else{
            this.element.textContent=element_value;
        }
    }

}