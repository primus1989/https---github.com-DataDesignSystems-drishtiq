(function($) {
    $(document).ready(function(){

    var color_code = $("#uni-cpo-attr-color-code").val();

    if ( color_code ) {
        $("#uni-cpo-attr-color-code").spectrum({
            color: color_code,
            flat: true,
            showInput: true,
            preferredFormat: "hex",
            allowEmpty:true,
            showInitial: true,
            clickoutFiresChange: true
        });
    } else {
        $("#uni-cpo-attr-color-code").spectrum({
            color: "",
            flat: true,
            showInput: true,
            preferredFormat: "hex",
            allowEmpty:true,
            showInitial: true,
            clickoutFiresChange: true
        });
    }

    });
})(jQuery);