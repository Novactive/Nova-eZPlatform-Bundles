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

(function (global, $) {
    const SELECTOR = '.enhancedimage--wrapper';
    $(SELECTOR).focusPoint();
    $(SELECTOR).on('focusChange', function () {
        var focusX = $(this).attr('data-focus-x'),
            focusY = $(this).attr('data-focus-y'),
            imageW = $(this).attr('data-image-w'),
            imageH = $(this).attr('data-image-h');
        $(this).data('imageW', imageW);
        $(this).data('imageH', imageH);
        $(this).data('focusX', focusX);
        $(this).data('focusY', focusY);
        $(this).adjustFocus();
    });
})(window, jQuery);
