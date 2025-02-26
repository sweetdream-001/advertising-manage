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

document.addEventListener("DOMContentLoaded", () => {
  const videoFrames = document.querySelectorAll(".youtube-video");
  const videoAdFrames = document.querySelectorAll(".youtube-ad-video");

  // Initialize Intersection Observer
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      const iframe = entry.target;
      try {
        // Ensure iframe is lazy-loaded
        if (entry.isIntersecting) {
          if (!iframe.src) {
            iframe.src = `${iframe.dataset.src}?enablejsapi=1&autoplay=1&loop=1&playlist=${iframe.dataset.videoId}`;
          }
          iframe.contentWindow.postMessage(
            '{"event":"command","func":"playVideo","args":""}',
            "*"
          );
        } else {
          iframe.contentWindow.postMessage(
            '{"event":"command","func":"pauseVideo","args":""}',
            "*"
          );
        }
      } catch (e) {
        console.error("Error interacting with iframe:", e);
      }
    });
  });

  setTimeout(() => {
    // Observe each video frame
    videoFrames.forEach((iframe) => observer.observe(iframe));
    videoAdFrames.forEach((iframe) => observer.observe(iframe));
  }, 3000);
});

// document.addEventListener("DOMContentLoaded", () => {
//   const videoFrames = document.querySelectorAll(".youtube-video");
//   const videoAdFrames = document.querySelectorAll(".youtube-ad-video");

//   // Initialize Intersection Observer
//   const observer = new IntersectionObserver((entries) => {
//       entries.forEach((entry) => {
//       console.log(entry, 'entry')
//       const iframe = entry.target;
//         try {
//           // Ensure iframe is lazy-loaded
//           if (entry.isIntersecting) {
//               if (!iframe.src) {
//               iframe.src = iframe.dataset.src;
//               }
//               iframe.contentWindow.postMessage('{"event":"command","func":"playVideo","args":""}', '*');
//           } else {
//               iframe.contentWindow.postMessage('{"event":"command","func":"pauseVideo","args":""}', '*');
//           }
//           console.log("workked"); 
//             console.log(iframe); 
//         } catch(e){
//             console.log("failed"); 
//             console.log(iframe); 
//             console.log(e)
//         }
//       });
//   });

// setTimeout(() => {
//     console.log('calling ob')
//   // Observe each video frame
//   videoFrames.forEach((iframe) => observer.observe(iframe));
//   videoAdFrames.forEach((iframe) => observer.observe(iframe));
// }, 3000)
// });
