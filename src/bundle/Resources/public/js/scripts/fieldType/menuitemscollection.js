/*
 * NovaeZMenuManagerBundle.
 *
 * @package   NovaeZMenuManagerBundle
 *
 * @author    Novactive <f.alexandre@novactive.com>
 * @copyright 2018 Novactive
 * @license   https://github.com/Novactive/NovaeZMenuManagerBundle/blob/master/LICENSE
 */

(function(global, React, ReactDOM, $) {
    const SELECTOR_FIELD = '.menu-edit__items-collection';
    const SELECTOR_MENU_MANAGER = '.menu-manager';
    const SELECTOR_INPUT = '.ez-data-source__input';

    document.querySelectorAll(SELECTOR_FIELD).forEach((fieldContainer) => {
        const input = fieldContainer.querySelector(SELECTOR_INPUT);
        global.Novactive.MenuManagerRenderer.render(fieldContainer.querySelector(SELECTOR_MENU_MANAGER), input);
    });
})(window, window.React, window.ReactDOM, window.jQuery);
