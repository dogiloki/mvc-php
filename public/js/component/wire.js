class Wire{

    static TYPE_WIRES=Object.freeze({
        MODEL:{
            id:Symbol(),
            text:"model",
        },
        CLICK:{
            id:Symbol(),
            text:"click",
        },
    });

    constructor(element,type,content){
        this.element=element;
        this.type=type;
        this.content=content;
    }

    getValue(){
        let value=null;
        switch(this.element.tagName){
            case "TEXTAREA":
            case "SELECT": value=this.element.value; break;
            case "INPUT":{
                switch(this.element.type){
                    case "checkbox": value=this.element.checked; break;
                    default: value=this.element.value; break;
                }
            }
        }
        return value;
    }

    setValue(value){
        switch(this.element.tagName){
            case "TEXTAREA":
            case "SELECT": this.element.value=value; break;
            case "INPUT":{
                switch(this.element.type){
                    case "checkbox": this.element.checked=value; break;
                    default: this.element.value=value; break;
                }
            }
        }
    }

    getAction(){
        let content=this.content;
        let method=content.match(/^([^()]+)/)[1];
        let params=content.match(/\(([^)]*)\)/)[1].replaceAll('"','').replaceAll("'","");
        return {
            "method": method,
            "params": params.replaceAll(" ","")==""?[]:params.split(",")
        };
    }

}