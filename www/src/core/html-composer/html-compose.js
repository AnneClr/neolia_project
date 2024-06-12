import { HTMLComponent } from "./html-component"

export class HtmlCompose extends HTMLComponent {
    children = []

    constructor(componentType, ...args) {
        super(componentType, args)
    }

    addComponent(component) {
        this.children.push(component)
        component.parent = this
        return this
    }

    build() {
        const el = document.createElement(this.componentType)
        if (this.args && this.args.length > 0) {
            const attributes = this.args[1]
            for (const attribute in attributes) {
                if (attribute === 'classes') {
                    attributes[attribute].forEach((cssClass) => {
                        el.classList.add(cssClass)
                    })
                } else {
                    if (attribute === 'events') {
                        // Manage events...
                        const events = attributes[attribute]
                        events.forEach((event) => {
                            el.setAttribute(event[0], event[1])
                        })
                    } else {
                        if (attribute === 'type' || attribute === 'id')
                            el.setAttribute(attribute, attributes[attribute])   
                    }
                }
            }
        }

        if (this.content && this.content.toString().trim().length) {
            el.textContent = this.content
        }
        if (this.children.length) {
            for (const child of this.children) {
                el.appendChild(child.build())
            }
        }
        return el        
    }
}