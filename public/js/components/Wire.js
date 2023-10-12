class Wire{

    static WIRE_EVENTS=Object.freeze({
        SYNC:{
            name:"sync"
        },
        CLICK:{
            name:"click"
        }
    });

    constructor(){
        this.element;
        this.wire_event;
        this.event;
        this.attrib;
        this.element_attrib;
        this.delay=150;
        this.renderable=false;
    }

}