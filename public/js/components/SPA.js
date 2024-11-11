import Wire from './Wire.js';
import Component from './Component.js';

export default class SPA{

    constructor(){
        this.components=[];
        let collection=document.querySelectorAll('[wire\\:name],[wire\\:render]');
        collection.forEach((element)=>{
            let name;
            let wires=[];
            for(const [wire_event_key,wire_event_value] of Object.entries(Wire.WIRE_EVENTS)){
                if(wire_event_value.name=="name"){
                    name=element.getAttribute("wire:name");
                    continue;
                }
                let value=element.getAttribute("wire:"+wire_event_value.name);
                if(value==null){
                    continue;
                }
                wires.push(new Wire({
                    element:element,
                    value:value,
                    wire_event:wire_event_value.name,
                    renderable:false,
                }));
            }
            let component=new Component({
                name:name,
                wires:wires
            });
            this.components.push(component);
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