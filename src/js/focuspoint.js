/*
 * NovaeZEnhancedImageAssetBundle.
 *
 * @package   NovaeZEnhancedImageAssetBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZEnhancedImageAssetBundle/blob/master/LICENSE
 *
 */

import { FocusedImage } from 'image-focus/dist/image-focus.es5';

const CONTAINER_SELECTOR = 'enhancedimage--wrapper';
const IMAGE_SELECTOR = 'enhancedimage--img';
const THROTTLE_DELAY = 125;
let elements;

class Image {
    constructor(element) {
        this.sources = new Map();
        this.element = element;
        this.currentSrc = null;
    }

    /**
     * @param {string} urlString
     * @param {Focus} focusPoint
     */
    addSource(urlString, focusPoint) {
        const source = { url: new URL(urlString, location), focusPoint: focusPoint };
        this.sources.set(source.url.pathname, source);
    }

    getSource(url) {
        return this.sources.get(url.pathname);
    }

    updateFocusPoint(forceUpdate) {
        if (this.currentSrc === this.element.currentSrc && forceUpdate !== true) return false;
        const currentSource = this.getSource(new URL(this.element.currentSrc, location));
        if (!currentSource) return false;

        new FocusedImage(this.element, currentSource.focusPoint);
        this.element.classList.add('focused');
        this.currentSrc = this.element.currentSrc;
    }
}

const throttle = (func) => {
    let inThrottle;
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

        const imageElement = loadedElements[i].getElementsByClassName(IMAGE_SELECTOR)[0];

        let image = imageElement._image;
        if (!image) {
            image = new Image(imageElement);

            image.addSource(imageElement.getAttribute('srcset'), {
                x: imageElement.getAttribute('data-focus-x'),
                y: imageElement.getAttribute('data-focus-y'),
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
                    x: source.getAttribute('data-focus-x'),
                    y: source.getAttribute('data-focus-y'),
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
    if (window.MutationObserver) {
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
