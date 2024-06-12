import { Handler } from "../core/event-handler/forms/handler"
import { TemplateLoader } from "../core/template/template-loader"
import { Form } from './../core/forms/form'
import { Control } from './../core/forms/control'
import { Validators } from './../core/forms/validator/validators'

import './../scss/material/button.scss'
import './../scss/material/input.scss'

export class LoginForm extends Form {
    #formSelector = null

    constructor(formSelector) {
        super()
        this.#formSelector = formSelector
    }

    #setFields() {
        this
            .addControl(new Control('username', '', [Validators.required]))
            .addControl(new Control('userpassword', '', [Validators.required]))

        // Place form handler    
        Handler.formHandler(this)
    }

    async loadForm() {
        const templateLoader = new TemplateLoader('login-form')
        const el = await templateLoader.loadFromFile()

        if (!el) {
            throw new Error(`Unable to load component`)
        }
        document.querySelector('main').appendChild(el.documentElement)

        const form = document.querySelector('form')

        form.querySelectorAll('[data-rel]').forEach((el) => {
            this.formFields.push(el)
        })

        this.#setFields()

        this.formFields.forEach(field => {
            field.addEventListener('input', () => this.validateField(field))
        })

        return form
    }

    validateField(field) {
        const controlName = field.getAttribute('data-rel')
        const control = this.getControl(controlName)
        control.value = field.value

        const error = control.value === '' ? 'Ce champ ne peut être vide.' : null

        if (error) {
            this.showError(field, error)
        } else {
            this.clearError(field)
        }
    }

    showError(field, message) {
        let errorElement = field.nextElementSibling
        if (!errorElement || !errorElement.classList.contains('error-message')) {
            errorElement = document.createElement('div')
            errorElement.className = 'error-message'
            errorElement.style.color = 'rgb(225, 100, 10)'
            field.parentNode.insertBefore(errorElement, field.nextSibling)
        }
        errorElement.textContent = message || "Erreur inconnue"
    }

    clearError(field) {
        let errorElement = field.nextElementSibling
        if (errorElement && errorElement.classList.contains('error-message')) {
            errorElement.remove()
        }
    }
}
