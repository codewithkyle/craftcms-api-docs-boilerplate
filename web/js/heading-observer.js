class HeadingObserver extends HTMLElement {
    constructor() {
        super();
        this.observer = new IntersectionObserver(this.handleIntersect.bind(this), {
            root: null,
            rootMargin: '0% 0% -90%',
            threshold: [0, 1],
        });
    }

    connectedCallback() {
        this.observer.observe(this);
    }

    disconnectedCallback() {
        this.observer.disconnect();
    }

    handleIntersect(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const heading = entry.target.querySelector('[id]');
                window.dispatchEvent(new CustomEvent('observed', {
                    detail: {
                        id: heading.id,
                    },
                }));
            }
        });
    }
}
if (!customElements.get('heading-observer')) {
    customElements.define('heading-observer', HeadingObserver);
}
