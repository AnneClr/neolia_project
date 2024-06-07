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
    }

    /**
     * Negociate login with backend
     */
    send() {
        
        const value = this.#form.value
        this.#service.signin(value)
            .pipe(
                take(1)
            )
            .subscribe({
                next: (response) => {
                    
                },
                error: (error) => {
                    /**
                     * Your logic here
                     */
                },
                complete: () => {
                    this.#form.unsubscribe()
                    //document.querySelector('form').remove()
                }
            })
    }
}
