import Util from '../Util.js';
import XHR from '../XHR.js';

export default class Wire{

    static WIRE_EVENTS=Object.freeze({
        NAME:{
            name:"name"
        },
        RENDER:{
            name:"render"
        },
        MODEL:{
            name:"model"
        },
        SYNC:{
            name:"sync"
        },
        CLICK:{
            name:"click"
        }
    });

    static TYPE_VALUATION={
        "input":"value",
        "textarea":"value",
    };

    static TYPE_VALUATION_DEFAULT="textContent";

    constructor({
        element=null,
        wire_event=null,
        model=null,
        property=null,
        delay=250,
        renderable=false,
    }){
        this.element=element;
        this.wire_event=wire_event;
        this.model=model;
        this.property=property;
        this.delay=delay;
        this.renderable=renderable;
        this.properties={};
        this.loadEvents();
    }

    getValue(){
        return this.element[Wire.TYPE_VALUATION[this.element.localName]??Wire.TYPE_VALUATION_DEFAULT];
    }

    setValue(value){
        this.element[Wire.TYPE_VALUATION[this.element.localName]??Wire.TYPE_VALUATION_DEFAULT]=value;
    }

    loadEvents(){
        switch(this.wire_event){
            case Wire.WIRE_EVENTS.SYNC.name:{
                this.element.addEventListener('keyup',(evt)=>{
                    this.properties[this.property]=this.getValue(); 
                    setTimeout(()=>{
                        this.render();
                    },this.delay);
                });
                this.render(false);
                break;
            }
            case Wire.WIRE_EVENTS.RENDER.name:
            case Wire.WIRE_EVENTS.MODEL.name:{
                this.render(false);
                break;
            }
        }
    }

    setProperties(properties,content){
        switch(this.wire_event){
            case Wire.WIRE_EVENTS.SYNC.name:
            case Wire.WIRE_EVENTS.MODEL.name:{
                if(this.element!==document.activeElement){
                    this.setValue(properties[this.property]);
                    this.properties[this.property]=properties[this.property];
                }
                for(let wire of window.spa.components[this.model]){
                    if(wire.element!==document.activeElement && !wire.renderable){
                        wire.element[Wire.TYPE_VALUATION[wire.element.localName]??Wire.TYPE_VALUATION_DEFAULT]=properties[this.property];
                    }
                    if(wire.renderable){
                        wire.element.innerHTML=content;
                    }
                }
                break;
            }
        }
    }

    render({
        set_properties=true
    }={}){
        XHR.request({
            method:"POST",
            uri:"component/"+this.model,
            data:{
                "set_properties":set_properties,
                "renderable":this.renderable,
                "properties":JSON.stringify(this.properties)
            },
            action:(xhr)=>{
                xhr.responseType="json";
            },
            load:(xhr)=>{
                let data=xhr.response;
                if(this.renderable){
                    this.element.innerHTML=data.content;
                }else{
                    this.setProperties(data.properties,data.content); 
                }
            }
        })
    }

}