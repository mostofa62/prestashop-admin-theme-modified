var owl = $('.owl-carousel');
  owl.owlCarousel({
      loop:true,
      nav:true,
      //center:true,
      //autoWidth:true,
      stagePadding: 0,
      navText: ['<i class="fa fa-angle-left" aria-hidden="true"></i>', '<i class="fa fa-angle-right" aria-hidden="true"></i>'],
      margin:0,
      responsive:{
          0:{
              items:1              
          },
          600:{
              items:3
          },            
          960:{
              items:4
          },
          1200:{
              items:4
          }
      }
  });
  owl.on('mousewheel', '.owl-stage', function (e) {
      if (e.deltaY>0) {
          owl.trigger('next.owl');
      } else {
          owl.trigger('prev.owl');
      }
      e.preventDefault();
  });