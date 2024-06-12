import { BehaviorSubject, fromEvent } from 'rxjs'
import { HtmlCompose } from './../../html-composer/html-compose'
import { Control } from './../../forms/control'
export class Handler {
    /**
     * Subscriptions array
     * @var Array<Subscription>
     */
    static _subscriptions = []

    /**
     * Default error messages
     * @var Map<string, string>
     */
    static _errorMessages = new Map([
        ['required', 'Ce champ ne peut pas être vide'],
        ['email', 'Ce champ ne correspond pas à une adresse email'],
        ['phone', 'Le nombre de caractères est insuffisant']
    ])

    /**
     * RxJs subject
     * @var BehaviorSubject<boolean>
     */
    static _changeEvent = new BehaviorSubject(false)
    
    static unsubscribe() {
        Handler._subscriptions.forEach((subscription) => subscription.unsubscribe())
    }

    /**
     * @obsolete Do not use this methods. Errors are managed in formHandler
     * @param control
     */
    static placeErrors(control) {
        this.processValidators(control)
    }

    /**
     * Form handler
     * @param {Form} _form
     */
    static formHandler(_form) {
        const form = document.querySelector('form')
        _form.controls.forEach((control) => {
            control.validators.forEach((validator) => {
                const JSONError = `{"type": {"${validator.name}": true}}`
                control.addError(validator.name, JSON.parse(JSONError))
            })
        })
       this._subscriptions.push(
        this._changeEvent
            .subscribe((_change) => {
                const _submitButton = document.querySelector(`[submit]`)
                const _isValid = _form.valid
                if (_isValid) {
                    _submitButton.removeAttribute('disabled')
                } else {
                    _submitButton.setAttribute('disabled', 'disabled')
                }
            })
       ) 
    }

    /**
     * Remove error div on focus
     * @param Control control 
     */
    static focusHandler(control) {
        Handler._subscriptions.push(
            fromEvent(control.supportFormField, 'focus')
                .subscribe((event) => {
                    const container = control.supportFormField.parentElement
                    const errorEl = container.querySelector(`div.error`)
                    if (errorEl) {
                        errorEl.remove()
                    }
                })
        )
    }
    
    /**
     * Check for errors and display error div below field
     * @param AbstractControl control 
     */
     static blurHandler(control) {
        Handler._subscriptions.push(
            fromEvent(control.supportFormField, 'blur')
                .subscribe((event) => {
                    this.processValidators(control)
                    if (control.hasErrors()) {
                        const container = control.supportFormField.parentElement
                        const composer = new HtmlCompose('div')
                        composer.args = {classes: ['error']}

                        composer.content = Handler._getErrorMessage(control)

                        const errorDiv = composer.build()
                        console.log(errorDiv)
                        container.appendChild(errorDiv)
                    }
                })
        )
    }

    static processValidators(control) {
        let validationResult
        
        this._changeEvent.next(true)
        
        if (control.validators.length > 0) {
            control.validators.forEach((validator) => {
                validationResult = validator(control)
                if (validationResult && !control.hasErrors()) {
                    control.addError(validator.name, {type: validationResult})
                    return // No Need to pass other validators
                } else {
                    const result = control.removeError(
                        validator.name
                    )
                    return
                }
            }) 
        }
    }

    static _getErrorMessage(control) {
        let errorMsg  =''
        control.errors.forEach((_error, key) => {
            errorMsg = Handler._errorMessages.get(key)
        })
        return errorMsg
    }
}