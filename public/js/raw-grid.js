(function($){
    $.fn.rawGrid = function(options) {

        function _raw_resize() {
            $("div[class^='raw-grid-col-']").each(function(){
                if ($(this).position().left == $(this).parent().position().left) {
                    // $(this).css("margin-left", "0px");
                } else {
                    $(this).css("margin-left", "24px");
                }
            });
        }

        function _raw_init() {
            $(window).bind("resize", function(){
                _raw_resize();
            });
        }

        $(function(){
            _raw_init();

            $(window).load(function(){
                _raw_resize();
                $(window).trigger("raw-sterized");
            });
        });
    }
})(jQuery);