class NavObserver extends HTMLElement {
    constructor() {
        super();
    }

    connectedCallback() {
        this.style.setProperty('--line-offset', '0px');
        window.addEventListener('observed', this.handleObserved.bind(this));
        window.addEventListener('scroll', this.handleScroll.bind(this));
        this.querySelectorAll('[href*="#"]').forEach(a => {
            a.addEventListener('click', event => {
                this.moveLine(event.target);
            });
        });
    }

    disconnectedCallback() {
        window.removeEventListener('observed', this.handleObserved.bind(this));
        window.removeEventListener('scroll', this.handleScroll.bind(this));
    }

    handleObserved(event) {
        const id = event.detail.id;
        const navItem = this.querySelector(`[href="#${id}"], [href="/${id}"]`);
        this.moveLine(navItem);
    }

    moveLine(navItem) {
        if (navItem) {
            const offset = navItem.getBoundingClientRect().top - this.getBoundingClientRect().top;
            this.style.setProperty('--line-offset', `${offset}px`);
        }
    }

    handleScroll() {
        if (this.isBottomOfPage()) {
            this.style.setProperty('--line-offset', this.getBoundingClientRect().height - 32 + 'px');
        }
    }

    isBottomOfPage() {
        const scrollTop = window.scrollY || document.documentElement.scrollTop;
        const windowHeight = window.innerHeight || document.documentElement.clientHeight;
        const documentHeight = document.documentElement.scrollHeight;
        return scrollTop + windowHeight >= documentHeight - 5;
    }
}
if (!customElements.get('nav-observer')) {
    customElements.define('nav-observer', NavObserver);
}
