class Component{

    constructor(name,element){
        this.name=name;
        this.element=element;
        this.params={};
        this.elements_json=JSON.parse(this.element.textContent);
        this.active_events={};
        this.getParams();
        this.loadEvents();
        this.render();
    }

    getParams(){
        let collection=Array.from(this.element.attributes);
        collection.forEach((attribute)=>{
            this.params[attribute.name]=attribute.value;
        });
    }

    loadEvents(){
        if(this.elements_json.events!=null){
            this.elements_json.events.forEach((event)=>{
                let listener=document.getElementById(event.id).addEventListener(event.event,(evt)=>{
                    if(this.active_events[listener]){
                        this.active_events[listener]=false;
                        setTimeout(()=>{
                            this.loadWires(this.elements_json.wires,(id,id_key,value_ley)=>{
                                this.params[value_ley]=document.getElementById(id)[id_key];
                            });
                            this.render({
                                name:event.method,
                                params:event.params??[]
                            });
                            this.active_events[listener]=true;
                        },event.delay??0);
                    }
                });
                this.active_events[listener]=true;
            });
        }
    }

    loadWires(data,action){
        data??={};
        let wires_id=Object.keys(data);
        let wires_param=Object.values(data);
        wires_id.forEach((wire_id,wire_index)=>{
            let wire_id_split=wire_id.split('.');
            let id=wire_id_split[0];
            let id_key=wire_id_split[1];
            let value_ley=wires_param[wire_index];
            action(id,id_key,value_ley);
        });
    }

    render(method){
        let json_send={
            params:this.params,
            method:method
        };
        Fetch.post('component/'+this.name,(data)=>{
            let json=JSON.parse(data);
            if(json.direct!=null){
                window.location=json.direct;
            }
            this.element.innerHTML=json.html;
            this.loadWires(this.elements_json.wires,(id,id_key,value_ley)=>{
                document.getElementById(id)[id_key]=json.params[value_ley];
            });
            this.params=json.params;
        },{
            'Content-Type':'application/json',
            'Accept':'text/html',
            'credentials':'include',
            'body':new URLSearchParams({
                json:JSON.stringify(json_send),
                _token:_token
            })
        });
    }

}