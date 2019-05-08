import { Focus } from 'image-focus/dist/types/interfaces.d';
import { FocusedImage } from 'image-focus';
import 'lazysizes';

(window as any).lazySizesConfig = (window as any).lazySizesConfig || {};

// use .lazy instead of .lazyload
(window as any).lazySizesConfig.lazyClass = 'enhancedimage--img--lazyload';

const regImg = /^img$/i;
const regPicture = /^picture$/i;

interface ImageElement extends HTMLImageElement {
    _image?: EnhancedImage;
}

interface Source {
    url: HTMLAnchorElement;
    focus: Focus;
}

const UrlParser = function(href: string) {
    var l = document.createElement('a');
    l.href = href;
    return l;
};

class EnhancedImage {
    sources: Map<string, Source>;
    element: HTMLImageElement;
    currentSrc: string;
    focusedImage: any;

    constructor(element: HTMLImageElement, lazyload?: boolean) {
        this.sources = new Map();
        this.element = element;
        this.currentSrc = null;
    }

    addSource(urlString: string, focus: Focus) {
        const url = UrlParser(urlString);
        const source = { url: url, focus: focus };
        this.sources.set(source.url.pathname, source);
    }

    setFocus(focus: Focus) {
        if (!this.focusedImage) this.focusedImage = new FocusedImage(this.element, { focus: focus });
        this.focusedImage.setFocus(focus);
    }

    updateFocusPoint(forceUpdate?: boolean) {
        if (this.currentSrc === this.element.currentSrc && forceUpdate !== true) return false;
        const currentSource = this.sources.get(UrlParser(this.element.currentSrc).pathname);
        if (!currentSource) return false;

        this.setFocus(currentSource.focus);
        this.element.classList.add('focused');
        this.currentSrc = this.element.currentSrc;
    }
}

(function(window: any, document: any) {
    const rAF = (function() {
        let running: boolean, waiting: boolean;
        const firstFns: Function[] = [];
        const secondFns: Function[] = [];
        let fns = firstFns;

        const run = function() {
            const runFns = fns;

            fns = firstFns.length ? secondFns : firstFns;

            running = true;
            waiting = false;

            while (runFns.length) {
                runFns.shift()();
            }

            running = false;
        };

        const rafBatch = function(fn: Function, queue?: boolean) {
            if (running && !queue) {
                fn.apply(this, arguments);
            } else {
                fns.push(fn);

                if (!waiting) {
                    waiting = true;
                    (document.hidden ? setTimeout : requestAnimationFrame)(run);
                }
            }
        };

        rafBatch._lsFlush = run;

        return rafBatch;
    })();

    const rAFIt = function(fn: Function, simple?: boolean) {
        return simple
            ? function() {
                  rAF(fn);
              }
            : function() {
                  const that = this;
                  const args = arguments;
                  rAF(function() {
                      fn.apply(that, args);
                  });
              };
    };

    const throttle = function(fn: Function) {
        let running: boolean;
        let lastTime = 0;
        const gDelay = window.lazySizesConfig.throttleDelay;
        let rICTimeout = window.lazySizesConfig.ricTimeout;
        const run = function() {
            running = false;
            lastTime = Date.now();
            fn();
        };
        const idleCallback =
            window.requestIdleCallback && rICTimeout > 49
                ? function() {
                      window.requestIdleCallback(run, { timeout: rICTimeout });

                      if (rICTimeout !== window.lazySizesConfig.ricTimeout) {
                          rICTimeout = window.lazySizesConfig.ricTimeout;
                      }
                  }
                : rAFIt(function() {
                      setTimeout(run);
                  }, true);

        return function(isPriority: boolean) {
            let delay;

            if ((isPriority = isPriority === true)) {
                rICTimeout = 33;
            }

            if (running) {
                return;
            }

            running = true;

            delay = gDelay - (Date.now() - lastTime);

            if (delay < 0) {
                delay = 0;
            }

            if (isPriority || delay < 9) {
                idleCallback();
            } else {
                setTimeout(idleCallback, delay);
            }
        };
    };

    const imagesList = document.getElementsByClassName('enhancedimage--img');
    const throttleResizeCallback = throttle(function(e: Event) {
        const imagesCount = imagesList.length;
        for (let i = 0; i < imagesCount; i++) {
            if (!imagesList[i]) {
                continue;
            }

            const image = (imagesList[i] as ImageElement)._image;
            if (image) {
                image.updateFocusPoint();
            }
        }
    });

    window.addEventListener('resize', throttleResizeCallback, true);

    document.addEventListener('lazyloaded', function(e: Event) {
        const elem = e.target as Element;
        if (!regImg.test(elem.nodeName) || !elem.classList.contains('enhancedimage--img')) return false;

        const imageElement = e.target as ImageElement,
            parent = imageElement.parentElement;

        let image = imageElement._image;
        if (!image) {
            image = new EnhancedImage(imageElement);

            image.addSource(imageElement.getAttribute('srcset'), {
                x: parseFloat(imageElement.getAttribute('data-focus-x')),
                y: parseFloat(imageElement.getAttribute('data-focus-y')),
            });

            if (parent && regPicture.test(parent.nodeName || '')) {
                const sources = parent.getElementsByTagName('source');
                const sourcesCount = sources.length;
                let j = 0;
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
            }
            imageElement._image = image;
        }

        image.updateFocusPoint();
    });
})(window, document);
