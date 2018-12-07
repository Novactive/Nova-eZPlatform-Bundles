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

    toTreeNode(language) {
        return {
            id: this.id,
            parent: this.parentId,
            text: this.translateProperty(this.name, language),
            data: {
                position: this.position,
                url: this.url,
                target: this.target,
            },
            state: this.state,
            type: this.type,
        };
    }

    translateProperty(property, language) {
        try {
            const values = JSON.parse(property);
            return values[language];
        } catch (e) {
            return property;
        }
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
