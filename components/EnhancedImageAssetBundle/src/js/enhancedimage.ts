/*
 * NovaeZEnhancedImageAssetBundle.
 *
 * @package   NovaeZEnhancedImageAssetBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2019 Novactive
 * @license   https://github.com/Novactive/NovaeZEnhancedImageAssetBundle/blob/master/LICENSE
 */

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

interface Size {
    width: number;
    height: number;
}

interface Source {
    url: HTMLAnchorElement;
    focus: Focus;
    size: Size;
}

const UrlParser = function (href: string): HTMLAnchorElement {
    var l = document.createElement('a');
    l.href = href;
    return l;
};

class EnhancedImage {
    private sources: Map<string, Source>;
    private element: HTMLImageElement;
    private currentSrc: string;
    private focusedImage: any;

    public constructor(element: HTMLImageElement) {
        this.sources = new Map();
        this.element = element;
        this.currentSrc = null;
    }

    protected parseSrc(src: string): HTMLAnchorElement[] {
        let urls = [];
        const urlStrings = src.split(',');
        for (const urlString of urlStrings) {
            const [url] = urlString.trim().split(' ');
            urls.push(UrlParser(url));
        }

        return urls;
    }

    public addSource(src: string, focus: Focus, size: Size): void {
        const urls = this.parseSrc(src);
        for (const url of urls) {
            const source = { url: url, focus: focus, size: size };
            this.sources.set(source.url.pathname, source);
        }
    }

    public setFocus(focus: Focus): void {
        if (!this.focusedImage)
            this.focusedImage = new FocusedImage(this.element, {
                focus: focus
            });
        this.focusedImage.setFocus(focus);
    }

    public updateFocusPoint(forceUpdate?: boolean): void {
        if (this.currentSrc === this.element.currentSrc && forceUpdate !== true)
            return;
        const elCurrentSrc = this.element.currentSrc || this.element.src;
        const currentSource = this.sources.get(
            UrlParser(elCurrentSrc).pathname
        );
        if (!currentSource) return;

        this.setFocus(currentSource.focus);
        this.element.classList.add('focused');
        this.currentSrc = elCurrentSrc;
    }
}

(function (window: any, document: any): void {
    const rAF = (function (): Function {
        let running: boolean, waiting: boolean;
        const firstFns: Function[] = [];
        const secondFns: Function[] = [];
        let fns = firstFns;

        const run = function (): void {
            const runFns = fns;

            fns = firstFns.length ? secondFns : firstFns;

            running = true;
            waiting = false;

            while (runFns.length) {
                runFns.shift()();
            }

            running = false;
        };

        const rafBatch = function (fn: Function, queue?: boolean): void {
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

    const rAFIt = function (fn: Function, simple?: boolean): Function {
        return simple
            ? function (): void {
                  rAF(fn);
              }
            : function (): void {
                  const that = this;
                  const args = arguments;
                  rAF(function (): void {
                      fn.apply(that, args);
                  });
              };
    };

    const throttle = function (fn: Function): Function {
        let running: boolean;
        let lastTime = 0;
        const gDelay = window.lazySizesConfig.throttleDelay;
        let rICTimeout = window.lazySizesConfig.ricTimeout;
        const run = function (): void {
            running = false;
            lastTime = Date.now();
            fn();
        };
        const idleCallback =
            window.requestIdleCallback && rICTimeout > 49
                ? function (): void {
                      window.requestIdleCallback(run, { timeout: rICTimeout });

                      if (rICTimeout !== window.lazySizesConfig.ricTimeout) {
                          rICTimeout = window.lazySizesConfig.ricTimeout;
                      }
                  }
                : rAFIt(function (): void {
                      setTimeout(run);
                  }, true);

        return function (isPriority: boolean): void {
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

    const imagesList = document.getElementsByClassName(
        'enhancedimage--focused-img'
    );
    const throttleResizeCallback = throttle(function (): void {
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

    document.addEventListener('lazyloaded', function (e: Event): void {
        const elem = e.target as Element;
        if (
            !regImg.test(elem.nodeName) ||
            !elem.classList.contains('enhancedimage--focused-img')
        )
            return;

        const imageElement = e.target as ImageElement;
        const parent = imageElement.parentElement;

        let image = imageElement._image;
        if (!image) {
            image = new EnhancedImage(imageElement);

            image.addSource(
                imageElement.getAttribute('srcset'),
                {
                    x: parseFloat(imageElement.getAttribute('data-focus-x')),
                    y: parseFloat(imageElement.getAttribute('data-focus-y'))
                },
                {
                    width: parseInt(imageElement.getAttribute('data-width')),
                    height: parseInt(imageElement.getAttribute('data-height'))
                }
            );

            if (parent && regPicture.test(parent.nodeName || '')) {
                const sources = parent.getElementsByTagName('source');
                const sourcesCount = sources.length;
                let j = 0;
                for (; j < sourcesCount; j++) {
                    if (!sources[j]) {
                        continue;
                    }
                    const source = sources[j];
                    image.addSource(
                        source.getAttribute('srcset'),
                        {
                            x: parseFloat(source.getAttribute('data-focus-x')),
                            y: parseFloat(source.getAttribute('data-focus-y'))
                        },
                        {
                            width: parseInt(source.getAttribute('data-width')),
                            height: parseInt(source.getAttribute('data-height'))
                        }
                    );
                }
            }
            imageElement._image = image;
        }

        image.updateFocusPoint();
    });
})(window, document);
