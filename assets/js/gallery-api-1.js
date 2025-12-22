let scriptPath = new URL(document.currentScript.src).pathname;
let basePath = new URL('../../', window.location.origin + scriptPath).pathname;
var totalSlides = 0;
let slideIndex = 0;

$(document).ready(function () {
    const slider = document.querySelector('#slider');

    // Fetch the JSON file
    $.getJSON(basePath + '/api/gallery.php', function (data) {
        console.log('JSON Data:', data);

        // Fetch the Mustache template
        $.get(basePath + '/assets/templates/gallery.mustache', function (template) {
            // Render the template with the data
            console.log('TEMPLATE:', template);
            const rendered = Mustache.render(template, data);
            console.log('RENDERED:', rendered);


            // Insert the rendered HTML into the page
            $('#slider').html(rendered);
            totalSlides = data.images.length;
            console.log(totalSlides)
            if (totalSlides > 0) {
                showSlides(slideIndex);
            }
        });
    }).fail(function (error) {
        console.error('Error fetching JSON file:', error);
    });
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


