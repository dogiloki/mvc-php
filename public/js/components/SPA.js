import Wire from './Wire.js';
import Component from './Component.js';

export default class SPA{

    static UUID(value=null){
        let timestamp=new Date().getTime();
        let random=Math.floor(Math.random()*1000);
        value??=`${timestamp}${random}`;
        return value;
    }

    constructor(){
        this.components={};
        let collection=document.querySelectorAll('[wire\\:'+Wire.WIRE_EVENTS.NAME.name+'],[wire\\:'+Wire.WIRE_EVENTS.RENDER.name+']');
        collection.forEach((element)=>{
            let wires=[];
            let index=0;
            for(const [wire_event_key,wire_event_value] of Object.entries(Wire.WIRE_EVENTS)){
                let attrib=element.getAttribute("wire:"+wire_event_value.name);
                if(attrib==null){
                    continue;
                }
                let renderable=false;
                let attrib_split=attrib.split(".");
                let model=attrib_split[0]??null;
                let property=attrib_split[1]??null;
                if(property==null){
                    property=model;
                    model=element.getAttribute("wire:"+Wire.WIRE_EVENTS.NAME.name);
                }
                if(wire_event_value.name==Wire.WIRE_EVENTS.NAME.name){
                    continue;
                }else
                if(wire_event_value.name==Wire.WIRE_EVENTS.RENDER.name){
                    model=attrib;
                    attrib=null;
                    renderable=true;
                }
                let wire=new Wire({
                    element:element,
                    model:model,
                    property:property,
                    wire_event:wire_event_value.name,
                    renderable:renderable,
                });
                wires.push(wire);
                this.components[model]??=[];
                if(!this.components[model].some(item=>item==element)){
                    this.components[model].push(wire);
                }
            }
            index++;
        });
        this.loadRenders();
    }

    loadRenders(){
        for(let name in this.components){
            let component=this.components[name];
            //console.log(component);
        }
    }
    
}

window.spa=new SPA();