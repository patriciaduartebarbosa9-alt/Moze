let scriptPath = new URL(document.currentScript.src).pathname;
let  basePath = new URL('../../', window.location.origin + scriptPath).pathname;


$(document).ready(function () {
    var totalSlides = 0;
    let slideIndex = 0;
    const slider = document.querySelector('#slider');
    $.ajax({
        url: basePath + '/api/gallery.php',
        type: 'GET',
        dataType: 'json',
        success: function (data) {
            console.log(data);
            // Fetch the Mustache template
            $.get(basePath + '/assets/templates/gallery.mustache', function (template) {
                // Render the template with the data
                const rendered = Mustache.render(template, data);
                // Insert the rendered HTML into the page
                $('#slider').html(rendered);
                totalSlides = data.images.length;
                console.log(totalSlides)
                if (totalSlides> 0) {
                    showSlides(slideIndex);
                }

            });
        },
        error: function (xhr, status, error) {
            console.error('AJAX Error:', status, error);
        }
    });

    function showSlides(index) {
        if (index >= totalSlides) {
            slideIndex = 0;
        } else if (index < 0) {
            slideIndex = totalSlides - 1;
        } else {
            slideIndex = index;
        }
        const offset = -slideIndex * 100;
        slider.style.transform = 'translateX(' + offset + '%)';
    }
    window.plusSlides = function (n) {
        showSlides(slideIndex + n);
    }


});
