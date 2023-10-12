class Component{

    static uniqueValue(){
        let timestamp=new Date().getTime();
        let random=Math.floor(Math.random()*1000)
        return `${timestamp}${random}`;
    }

    constructor(){
        this.name=null;
        this.active_events={};
        this.data=[];
        this.wires=[];
    }

    addWires(element){
        let attributes=element.attributes;
        for(let index=0; index<attributes.length; index++){
            let attribute=attributes[index];
            if(!attribute.name.startsWith('wire:')){
                continue;
            }
            let wire=new Wire();
            let event_split=attribute.name.split(":")[1].split(".");
            let attrib_split=attribute.value.split(".");
            wire.element=element;
            wire.wire_event=event_split[0];
            if(wire.wire_event=="name"){
                this.name=attribute.value;
                continue;
            }
            if(wire.wire_event=="render"){
                this.name=attribute.value;
                wire.renderable=true;
            }
            wire.event=event_split[1]??null;
            wire.attrib=attrib_split[0];
            wire.element_attrib=attrib_split[1]??"value";
            this.wires.push(wire);
        }
    }

    addWiresEvents(){
        this.wires.forEach((wire)=>{
            switch(wire.wire_event){
                case Wire.WIRE_EVENTS.SYNC.name:{
                    if(wire.event!=null){
                        let listener=wire.element.addEventListener(wire.event,(evt)=>{
                            if(!this.active_events[listener]){
                                return;
                            }
                            this.active_events[listener]=false;
                            setTimeout(()=>{
                                let element=evt.target;
                                this.data[wire.attrib]=element[wire.element_attrib];
                                this.render(); 
                                this.active_events[listener]=true;
                            },wire.delay);
                        });
                        this.active_events[listener]=true;
                    }
                    break;
                }
                case Wire.WIRE_EVENTS.CLICK.name:{
                    wire.element.addEventListener(wire.event??"click",(evt)=>{
                        this.render({
                            name:wire.attrib
                        });
                    });
                    break;
                }
            }
        });
        this.render();
    }

    loadWires(json){
        this.wires.forEach((wire)=>{
            if(wire.renderable){
                wire.element.innerHTML=json.html;
            }
            switch(wire.wire_event){
                case Wire.WIRE_EVENTS.SYNC.name:{
                    if(wire.element!=document.activeElement){
                        wire.element[wire.element_attrib]=this.data[wire.attrib];
                    }
                    break;
                }
            }
        });
    }

    render(method){
        return new Promise((resolve,reject)=>{
            Fetch.post('component/'+this.name,(data)=>{
                let json=JSON.parse(data);
                if(json.direct!=null){
                    window.location=json.direct;
                }
                this.data=json.data;
                this.loadWires(json);
                resolve();
            },{
                'Content-Type':'application/json',
                'Accept':'text/html',
                'credentials':'include',
                'body':JSON.stringify({
                    data:this.data,
                    method:method,
                    _token:_token
                })
            });
        });
    }

}