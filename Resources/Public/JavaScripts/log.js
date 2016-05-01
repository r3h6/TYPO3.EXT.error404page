define(["jquery"], function($) {
    $(document).ready(function($) {
        $('table.log').on('click', '.log-error-url', function(){
            var $details = $(this).closest('.log-row').find('.log-error-details')
            if ($details.hasClass('open')){
                $details.removeClass('open').slideUp('slow');
            } else {
                $details.addClass('open').slideDown('slow');
            }
        });
    });
});