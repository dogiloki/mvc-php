import Util from '../Util.js';
import XHR from '../XHR.js';
import Wire from './Wire.js';

export default class Component{

    constructor({
        name=null,
        wires=[]
    }){
        this.name=name;
        //this.active_events={};
        this.properties={};
        this.wires=wires;
        this.loadWires();
        this.render();
    }

    loadWires(){
        this.wires.forEach((wire)=>{
            switch(wire.wire_event){
                case Wire.WIRE_EVENTS.SYNC.name:{
                    wire.element.addEventListener('keyup',(evt)=>{
                        this.properties[wire.value]=wire.getElementValue();
                        setTimeout(()=>{
                            this.render();
                        },wire.delay);
                    });
                    break;
                }
            }
        });
    }

    setProperties(properties){
        this.wires.forEach((wire)=>{
            switch(wire.wire_event){
                case Wire.WIRE_EVENTS.SYNC.name:{
                    wire.setElementValue(properties[wire.value]);
                    this.properties[wire.value]=properties[wire.value];
                    break;
                }
            }
        });
        
    }

    render(){
        XHR.request({
            method:"POST",
            uri:"component/"+this.name,
            data:{
                properties:JSON.stringify(this.properties)
            },
            action:(xhr)=>{
                xhr.responseType="json";
            },
            load:(xhr)=>{
                let data=xhr.response;
                this.setProperties(data.properties);
            }
        })
    }

}