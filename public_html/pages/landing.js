document.addEventListener("DOMContentLoaded", function() {
  const scrollRevealOption = {
    distance: "50px",
    origin: "bottom",
    duration: 1000,
  };


  ScrollReveal().reveal(".header__container p, .header__container h1", {
    ...scrollRevealOption,
    delay: 0,
  });

 
  ScrollReveal().reveal(".about__image img", {
    ...scrollRevealOption,
    origin: "left",
    delay: 0,
  });
  ScrollReveal().reveal(".about__image2 img", {
    ...scrollRevealOption,
    origin: "left",
    delay: 0,
  });

  
  ScrollReveal().reveal(".about__content .section__subheader, .about__content .section__header", {
    ...scrollRevealOption,
    delay: 500,
  });
  ScrollReveal().reveal(".about__content2 .section__subheader2, .about__content2 .section__header2", {
    ...scrollRevealOption,
    delay: 500,
  });
  ScrollReveal().reveal(".about__content .section__description", {
    ...scrollRevealOption,
    delay: 1000,
  });

  ScrollReveal().reveal(".about__content2 .section__description2", {
    ...scrollRevealOption,
    delay: 1000,
  });
  ScrollReveal().reveal(".about__btn", {
    ...scrollRevealOption,
    delay: 1500,
  });
  ScrollReveal().reveal(".about__btn2", {
    ...scrollRevealOption,
    delay: 1500,
  });
  ScrollReveal().reveal(".tracking__btn", {
    ...scrollRevealOption,
    delay: 1500,
  });
  ScrollReveal().reveal(".Contactus", {
    ...scrollRevealOption,
    delay: 1000,
  });
  ScrollReveal().reveal(".contact__content", {
    ...scrollRevealOption,
    delay: 1000,
  });
  ScrollReveal().reveal(".logo2", {
    ...scrollRevealOption,
    delay: 1000,
  });
  ScrollReveal().reveal(".footer", {
    ...scrollRevealOption,
    delay: 1000,
  });


  ScrollReveal().reveal(".feature__card", {
    ...scrollRevealOption,
    interval: 500,
  });


  ScrollReveal().reveal(".service__list li", {
    ...scrollRevealOption,
    origin: "right",
    interval: 500,
  });

  ScrollReveal().reveal(".tracking-input", {
    ...scrollRevealOption,
    delay: 0,
  });
  ScrollReveal().reveal(".input-icon", {
    ...scrollRevealOption,
    delay: 500,
  });
  ScrollReveal().reveal(".tracking-btn", {
    ...scrollRevealOption,
    delay: 1000,
  });
});