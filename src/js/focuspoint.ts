import { Focus } from 'image-focus/dist/types/interfaces.d';
import { FocusedImage } from 'image-focus';

const CONTAINER_SELECTOR = 'enhancedimage--wrapper';
const IMAGE_SELECTOR = 'enhancedimage--img';
const THROTTLE_DELAY = 125;
let elements: HTMLCollection;

interface ImageElement extends HTMLImageElement {
    _image?: Image;
}
interface Source {
    url: URL;
    focusPoint: Focus;
}

class Image {
    sources: Map<string, Source>;
    element: HTMLImageElement;
    currentSrc: string;
    constructor(element: HTMLImageElement) {
        this.sources = new Map();
        this.element = element;
        this.currentSrc = null;
    }

    addSource(urlString: string, focusPoint: Focus) {
        const url = new URL(urlString, window.location.toString());
        const source = { url: url, focusPoint: focusPoint };
        this.sources.set(source.url.pathname, source);
    }

    getSource(url: URL): Source | undefined {
        return this.sources.get(url.pathname);
    }

    updateFocusPoint(forceUpdate?: boolean) {
        if (this.currentSrc === this.element.currentSrc && forceUpdate !== true) return false;
        const currentSource = this.getSource(new URL(this.element.currentSrc, window.location.toString()));
        if (!currentSource) return false;

        new FocusedImage(this.element, currentSource.focusPoint);
        this.element.classList.add('focused');
        this.currentSrc = this.element.currentSrc;
    }
}

const throttle = (func: Function) => {
    let inThrottle = false;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => (inThrottle = false), THROTTLE_DELAY);
        }
    };
};

const checkElements = function() {
    const loadedElements = elements;
    const elementsCount = loadedElements.length;
    let i = 0,
        j;

    for (; i < elementsCount; i++) {
        if (!loadedElements[i]) {
            continue;
        }

        const imageElement = (loadedElements[i].getElementsByClassName(IMAGE_SELECTOR)[0]) as ImageElement;

        let image = imageElement._image;
        if (!image) {
            image = new Image(imageElement);

            image.addSource(imageElement.getAttribute('srcset'), {
                x: parseFloat(imageElement.getAttribute('data-focus-x')),
                y: parseFloat(imageElement.getAttribute('data-focus-y')),
            });

            const sources = loadedElements[i].getElementsByTagName('source');
            const sourcesCount = sources.length;
            j = 0;
            for (; j < sourcesCount; j++) {
                if (!sources[j]) {
                    continue;
                }
                const source = sources[j];
                image.addSource(source.getAttribute('srcset'), {
                    x: parseFloat(source.getAttribute('data-focus-x')),
                    y: parseFloat(source.getAttribute('data-focus-y')),
                });
            }
            imageElement._image = image;
        }

        image.updateFocusPoint();
    }
};

const throttledCheckElements = throttle(checkElements);

(function(window, document) {
    const docElem = document.documentElement;
    elements = document.getElementsByClassName(CONTAINER_SELECTOR);

    addEventListener('resize', throttledCheckElements, true);
    if (window && MutationObserver) {
        new MutationObserver(throttledCheckElements).observe(docElem, { childList: true, subtree: true, attributes: true });
    } else {
        docElem['addEventListener']('DOMNodeInserted', throttledCheckElements, true);
        docElem['addEventListener']('DOMAttrModified', throttledCheckElements, true);
        setInterval(throttledCheckElements, 999);
    }

    if (/d$|^c/.test(document.readyState)) {
        throttledCheckElements();
    } else {
        addEventListener('load', throttledCheckElements);
        document['addEventListener']('DOMContentLoaded', throttledCheckElements);
        setTimeout(throttledCheckElements, 20000);
    }
})(window, document);
