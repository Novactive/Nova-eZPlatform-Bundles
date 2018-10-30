export default class MenuItem {
    constructor(props) {
        this.id = String(
            props.id ||
                '_' +
                    Math.random()
                        .toString(36)
                        .substr(2, 9)
        );
        this.parentId = props.parentId || '#';
        this.name = props.name;
        this.position = props.position || 0;
        this.url = props.url || '';
        this.target = props.target || '';
        this.state = props.state;
        this.type = props.type;
    }

    toTreeNode() {
        return {
            id: this.id,
            parent: this.parentId,
            text: this.name,
            data: {
                position: this.position,
                url: this.url,
                target: this.target,
            },
            state: this.state,
            type: this.type,
        };
    }

    isEnabled() {
        return this.state === undefined || !this.state.disabled;
    }

    static fromTreeNode(node, position = null) {
        return new MenuItem({
            id: node.id,
            parentId: node.parent,
            name: node.text,
            position: position || node.data.position,
            url: node.data.url,
            target: node.data.target,
            state: node.state,
            type: node.type,
        });
    }
}
