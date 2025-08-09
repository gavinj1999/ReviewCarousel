define([
    'jquery',
    'slick',
    'domReady!'
], function ($, slick) {
    'use strict';
    console.log('Carousel JS module loaded with jQuery:', $, 'and slick:', slick);
    return function (config, element) {
        if (!element) {
            console.error('No element provided to Carousel JS');
            return;
        }
        console.log('Carousel JS initialized with element:', element);
        var $carousel = $(element); // Use the element directly
        if ($carousel.length === 0) {
            console.error('Carousel element not found:', element);
            return;
        }
        console.log('Found carousel element:', $carousel);
        try {
            $carousel.slick({
                slidesToShow: 4,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 5000,
                arrows: false,
                useTransform: true,
                dots: true,
                responsive: [
                    {
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 2
                        }
                    },
                    {
                        breakpoint: 600,
                        settings: {
                            slidesToShow: 1
                        }
                    }
                ]
            }).on('init', function(event, slick) {
                console.log('Slick Slider initialized successfully');
                $carousel.addClass('slick-initialized'); // Add class to show carousel
            }).on('error', function(event, slick, errorMessage) {
                console.error('Slick Slider error:', errorMessage);
            });
        } catch (e) {
            console.error('Error initializing Slick Slider:', e);
        }
    };
});

