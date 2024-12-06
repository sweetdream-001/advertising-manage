$(document).ready(function() {
    $(window).on('scroll', function() {
        $('.post-list-item').each(function() {
            var top_of_element = $(this).offset().top;
            var bottom_of_element = top_of_element + $(this).outerHeight();
            var top_of_screen = $(window).scrollTop();
            var bottom_of_screen = top_of_screen + $(window).innerHeight();

            if ((bottom_of_screen > top_of_element) && (top_of_screen < bottom_of_element)) {
                $(this).addClass('pt-active-div');
                var iframe = $(this).find('.yt-content iframe');
                if (iframe.length) {
                    var iframeSrc = iframe.attr('src');
                    // Check if autoplay and mute parameters are already present
                    if (iframeSrc.indexOf('autoplay=1') === -1 && iframeSrc.indexOf('mute=1') === -1) {
                        var separator = iframeSrc.indexOf('?') === -1 ? '?' : '&';
                        var iframeSrcNew = iframeSrc + separator + 'autoplay=1&mute=1';
                        iframe.attr('src', iframeSrcNew);
                    } else {
                        
                    }
                } else {
                    
                }
            } else {
                $(this).removeClass('pt-active-div');
                var iframe = $(this).find('.yt-content iframe');
                if (iframe.length) {
                    var iframeSrc = iframe.attr('src');
                    // Check if autoplay and mute parameters are already present
                    if (iframeSrc.indexOf('autoplay=1') ===  1 && iframeSrc.indexOf('mute=1') === 1) {
                        // var separator = iframeSrc.indexOf('?') === -1 ? '?' : '&';
                        var iframeSrcNew = iframeSrc;
                        iframe.attr('src', iframeSrcNew);
                    } else {
                        
                    }
                } else {
                    
                }
            }
        });
        
        $('.post-list-advert').each(function() {
            var top_of_element = $(this).offset().top;
            var bottom_of_element = top_of_element + $(this).outerHeight();
            var top_of_screen = $(window).scrollTop();
            var bottom_of_screen = top_of_screen + $(window).innerHeight();

            if ((bottom_of_screen > top_of_element) && (top_of_screen < bottom_of_element)) {
                $(this).addClass('pt-active-div');
                var iframe = $(this).find('.yt-content iframe');
                if (iframe.length) {
                    var iframeSrc = iframe.attr('src');
                    // Check if autoplay and mute parameters are already present
                    if (iframeSrc.indexOf('autoplay=1') === -1 && iframeSrc.indexOf('mute=1') === -1) {
                        var separator = iframeSrc.indexOf('?') === -1 ? '?' : '&';
                        var iframeSrcNew = iframeSrc + separator + 'autoplay=1&mute=1';
                        iframe.attr('src', iframeSrcNew);
                    } else {
                        
                    }
                } else {
                    
                }
            } else {
                $(this).removeClass('pt-active-div');
                var iframe = $(this).find('.yt-content iframe');
                if (iframe.length) {
                    var iframeSrc = iframe.attr('src');
                    // Check if autoplay and mute parameters are already present
                    if (iframeSrc.indexOf('autoplay=1') ===  1 && iframeSrc.indexOf('mute=1') === 1) {
                        // var separator = iframeSrc.indexOf('?') === -1 ? '?' : '&';
                        var iframeSrcNew = iframeSrc;
                        iframe.attr('src', iframeSrcNew);
                    } else {
                        
                    }
                } else {
                    
                }
            }
        });
    });
});