$(function () {
    NovaeZResponsiveImages.init();
});

var NovaeZResponsiveImages = function () {
    function _init() {
        var isMobile = window.innerWidth <= 640,
            isRetina = window.devicePixelRatio > 1,
            $imgLazyload = $("img[unveiled]");

        $imgLazyload.each(function () {
            var $this = $(this),
                desktopImgSrc = $this.attr('data-desktop'),
                mobileImgSrc = $this.attr('data-mobile'),
                retinaImgSrc = $this.attr('data-retina'),
                dataSrc = isMobile ? mobileImgSrc : (isRetina ? retinaImgSrc : desktopImgSrc);

            $this.attr('data-src', dataSrc);
        }).unveil(200, function () {
            $(this).load(function () {
                this.style.opacity = 1;
            });
        });
    }

    return {init: _init};
}();
