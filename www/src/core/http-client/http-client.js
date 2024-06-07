import { from, map, Observable, of, throwError } from "rxjs"

export class HttpClient {
    #_method  = 'GET'
    #_uri = ''
    #_contentType = 'application/json'
    #_body = ''
    #_fetchOptions = {}

    /**
     * 
     * @param {string} uri 
     * @returns Observable<any>
     */
    get(uri) {
        this.#_uri = uri

        this.#_fetchOptions = {
            method: 'get',
            mode: 'cors',
            headers: {
                "Content-Type": this.#_contentType
            }
        }

        return this.#send()

    }

    /**
     * 
     * @param {string} uri 
     * @param {any} body 
     * @returns Observable<any>
     */
    post(uri, body) {
        this.#_method = 'post'
        this.#_uri = uri
        this.#_body = JSON.stringify(body)

        this.#_fetchOptions = {
            method: 'post',
            mode: 'cors',
            headers: {
                "Content-Type": this.#_contentType
            },
            body: this.#_body
        }
        return this.#send()
        
    }

    /**
     * 
     * @param {string} uri 
     * @param {any} body 
     * @returns Observable<any>
     */
    put(uri, body) {
        this.method = 'put'
        this.uri = uri
        
        this.body = JSON.stringify(body)

        this.fetchOptions = {
            method: 'put',
            mode: 'cors',
            headers: {
                "Content-Type": this.#_contentType
            },
            body: this.#_body
        }
        return this.#send()        
    }

   #send() {
        const apiCall = fetch(
            this.#_uri,
            this.#_fetchOptions
        )
        .then((response) => {
            if (response.ok) {
                return response.json()
            }
            throw new Error(`Something went wrong calling ${this.method.toUpperCase()} ${this.uri} (${JSON.stringify(response)})`)
        })
        .then((responseJson) => responseJson)

        return from(apiCall)
    }
}
