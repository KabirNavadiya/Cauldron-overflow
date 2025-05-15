import { Controller } from '@hotwired/stimulus';
import axios from 'axios';

export default class extends Controller {
    static targets = ['button', 'output'];

    generate(event) {
        event.preventDefault(); // optional, prevents form submission if it's in a form

        axios.post('/verify/getLink')
            .then(response => {
                const data = response.data;
                console.log("kabir");
                
                if (data.success) {
                    this.outputTarget.innerHTML = `
                        <div class="alert alert-success text-break">
                            <strong>Verify your email:</strong><br>
                            <a href="${data.link}" target="_blank">${data.link}</a>
                        </div>
                    `;
                } else {
                    this.outputTarget.innerHTML = `
                        <div class="alert alert-danger">${data.error}</div>
                    `;
                }
            })
            .catch(error => {
                this.outputTarget.innerHTML = `
                    <div class="alert alert-danger">An error occurred.</div>
                `;
                console.error(error);
            });
    }
}
