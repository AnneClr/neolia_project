import { TemplateLoader } from './../core/template/template-loader'
import { EventHandler } from '../core/event-handler/event-handler'
import { ClickEventHandler } from '../core/event-handler/click-event-handler'
import { Form } from './../core/forms/form'
import { LoginForm } from './login-form'
import { LoginService } from './login-service'
import { take } from 'rxjs'

export class Login {

    /**
     * @var TemplateLoader
     * Template loader utility
     */
    #loader

    /**
     * @var EventHandler
     * Click manager
     */
    #clickHandler

    /**
     * @var Form
     * Form manager
     */
    #form

    /** 
     * @var LoginService
    */
    #service = null

    constructor() {
        this.#service = new LoginService()
        this.init()
    }

    async init() {
        this.#form = new LoginForm('login-form')
        await this.#form.loadForm()
        this.#clickHandler = new ClickEventHandler(this)
        document.querySelector('form').addEventListener('submit', (event) => this.handleSubmit(event))
    }

    handleSubmit(event) {
        event.preventDefault()
        this.send()
    }

    /**
     * Negotiate login with backend
     */
    send() {
        const value = this.#form.value
        this.#service.signin(value)
            .pipe(
                take(1)
            )
            .subscribe({
                next: (response) => {
                    // Handle successful login
                    console.log('Login successful', response)
                },
                error: (error) => {
                    console.log(error);
                    this.showBackendError(error.message)
                },
                complete: () => {
                    this.#form.unsubscribe()
                }
            })
    }

    showBackendError(message) {
        let errorElement = document.querySelector('.backend-error-message')
        if (!errorElement) {
            errorElement = document.createElement('div')
            errorElement.className = 'backend-error-message'
            document.querySelector('form').insertBefore(errorElement, document.querySelector('form').firstChild)
            console.log(errorElement);
        }
        errorElement.textContent = message
    }
}
