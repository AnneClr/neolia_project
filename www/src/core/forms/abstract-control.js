export class AbstractControl {
  #form = null
  #controlName = ''
  #value = ''
  #errors = new Map()
  #validators = []
  #validatorRegistry = new Map()
  #supportFormField = null

  get controlName() {
    return this.#controlName
  }

  set controlName(val) {
    this.#controlName = val
  }

  get value() {
    return this.#value
  }

  set value(val) {
    this.#value = val
  }

  get supportFormField() {
    return this.#supportFormField
  }

  set supportFormField(field) {
    this.#supportFormField = field
  }

  get errors() {
    return this.#errors
  }

  set errors(value) {
    if (value instanceof Map) {
      this.#errors = value
    } else {
      throw new Error('errors must be a Map')
    }
  }

  set validators(validators) {
    this.#validators = validators
  }

  get validators() {
    return this.#validators
  }

  set validatorRegistry(registry) {
    this.#validatorRegistry = registry
  }

  get validatorRegistry() {
    return this.#validatorRegistry
  }

  getValidationErrorType(validator) {
    return this.#validatorRegistry.get(validator.name)
  }

  hasErrors() {
    return this.#errors && this.#errors.size > 0
  }

  hasError(error) {
    return [...this.#errors.values()].some((_error) => Equals.areEquals(_error, error))
  }

  addError(validatorName, error) {
    if (this.#errors.size > 0) {
      return false
    }
    this.#errors.set(validatorName, error)
    return true
  }

  removeError(error) {
    return this.#errors.delete(error)
  }

  addValidator(validator) {
    this.#validators.push(validator)
    return this
  }

  setSupportFormField() {
    const supportFormField = document.querySelector(`[data-rel="${this.#controlName}"]`)
    if (supportFormField !== null) {
      this.#supportFormField = supportFormField
    } else {
      throw new Error(`Cannot find a field with name: ${this.#controlName}`)
    }
  }

  #dumpErrors() {
    let output = ''
    this.#errors.forEach((error) => {
      output += `${JSON.stringify(error)} \n`
    })
    return output
  }
}
