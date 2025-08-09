var config = {
    paths: {
        'slick': 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js',
        'slick-fallback': 'Vendor_ReviewCarousel/js/slick.min' // Local fallback
    },
    shim: {
        'slick': {
            deps: ['jquery']
        },
        'slick-fallback': {
            deps: ['jquery']
        }
    },
    map: {
        '*': {
            'Vendor_ReviewCarousel/carousel': 'Vendor_ReviewCarousel/js/carousel'
        }
    }
};