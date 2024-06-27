$('.autoplay').slick({
    slidesToShow: 5,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 2000,
    arrows: false,


    // the magic
    responsive: [{

        breakpoint: 1024,
        settings: {
            slidesToShow: 4,
            infinite: false
        }

    },
    {

        breakpoint: 768,
        settings: {
            slidesToShow: 2,
            dots: false

        }

    },
    {

        breakpoint: 540,
        settings: {
            slidesToShow: 2,
            dots: false
        }

    }, {

        breakpoint: 300,
        settings: "unslick" // destroys slick

    }]
});







$('.autoplay-country').slick({
    slidesToShow: 5,
    slidesToScroll: 1,
    // autoplay: true,
    autoplaySpeed: 2000,
    arrows: false,
    infinite: true,
    rows: 2,

    // the magic
    responsive: [{

        breakpoint: 1024,
        settings: {
            slidesToShow: 4,
            infinite: false
        }

    },
    {

        breakpoint: 768,
        settings: {
            slidesToShow: 3,
            dots: false

        }

    },
    {

        breakpoint: 540,
        settings: {
            slidesToShow: 2,
            dots: false
        }

    }, {

        breakpoint: 300,
        settings: "unslick" // destroys slick

    }]
});




$('.autoplay-block').slick({
    slidesToShow: 4,
    slidesToScroll: 1,
    // autoplay: true,
    autoplaySpeed: 2000,
    arrows: false,
    infinite: true,
    rows: 2,

    // the magic
    responsive: [{

        breakpoint: 769,
        settings: {
            slidesToShow: 3,
            dots: false
        }

    },

        {

        breakpoint: 540,
        settings: {
            slidesToShow: 3,
            dots: false,
            rows: 1,
        }

    }, {

        breakpoint: 300,
        settings: "unslick" // destroys slick

    }]
});

// -------------------
// Bài viết
// -------------------
$('.you-may-like').slick({
    slidesToShow: 4,
    slidesToScroll: 1,
    autoplaySpeed: 5000,
    autoplay: true,
    infinite: true,
    arrows: false,

    // the magic
    responsive: [{

        breakpoint: 1024,
        settings: {
            slidesToShow: 4,
            infinite: false
        }

    },
        {

            breakpoint: 769,
            settings: {
                slidesToShow: 3,
                dots: false

            }

        },
        {

            breakpoint: 540,
            settings: {
                autoplay: true,
                slidesToShow: 1,
                dots: false
            }

        }, {

            breakpoint: 300,
            settings: "unslick" // destroys slick

        }]
});
