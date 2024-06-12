import { AbstractControl } from "./abstract-control"
import { Handler } from "../event-handler/forms/handler"

export class Control extends AbstractControl {
    constructor(name, state = null, validators = null) {
        super()
        this.controlName = name
        this.value = state || ''
        this.validators = validators || []

        // Store validators in registry
        this.validators.forEach((validator) => {
            const validationErrorJson = `{"type": {"${validator.name}": true}}`
            this.validatorRegistry.set(
                validator.name,
                JSON.parse(validationErrorJson)
            )
        })

        Handler.placeErrors(this)
    }

    validate() {
        const newErrors = new Map()
        this.validators.forEach(validator => {
            const error = validator(this.value)
            if (error) {
                newErrors.set(validator.name, error)
            }
        })
        this.errors = newErrors // Use the setter
        return this.errors.size > 0 ? [...this.errors.values()][0] : null
    }
}
