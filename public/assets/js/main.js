//deplacement de header on scroll
$(window).scroll(function() {
    if($(this).scrollTop() > 100) {
      $('#topbar').removeClass('fixed-top');
      $('topbar').addClass('topbar-scrolled');
      $('.header').addClass('header-scrolled');
    } else {
      $('#topbar').addClass('fixed-top');
      $('topbar').removeClass('topbar-scrolled');
      $('.header').removeClass('header-scrolled');
    }
  })

//active menu on click
$('ul.navbar-nav li.nav-item a.nav-link').click(function() {
    var activeMenuOnClick = $(this).data('menu');
    $('ul.navbar-nav li.nav-item a.nav-link').removeClass('active');
    sessionStorage.setItem('activeMenuOnClick', activeMenuOnClick);
  });
  
  $(document).ready(function() {
    var activeMenuOnClick = sessionStorage.getItem('activeMenuOnClick');
    if (activeMenuOnClick) {
      $('ul.navbar-nav li.nav-item a.nav-link[data-menu="Accueil"]').removeClass('active');
      $('ul.navbar-nav li.nav-item a.nav-link[data-menu="' + activeMenuOnClick + '"]').addClass('active');
    }
  });

  $('.produit-cart').click(function() {
    var activeMenuCart = $('ul.navbar-nav li.nav-item a.nav-link[data-menu="Cart"]');
    sessionStorage.setItem('activeMenuCart', activeMenuCart);
  });
  
  $(document).ready(function() {
    var activeMenuCart = sessionStorage.getItem('activeMenuCart');
    if (activeMenuCart) {
      $('ul.navbar-nav li.nav-item a.nav-link[data-menu="Cart"]').addClass('active');
      $('ul.navbar-nav li.nav-item a.nav-link').removeClass('active');

    }
  });


  $('#btn-menu').click(function() {
    var activeMenuProduit = $('ul.navbar-nav li.nav-item a.nav-link[data-menu="Produit"]');
    $('ul.navbar-nav li.nav-item a.nav-link[data-menu="Accueil"]').removeClass('active');
    sessionStorage.setItem('activeMenuProduit', activeMenuProduit);
  });
  
  $(document).ready(function() {
    var activeMenuProduit = sessionStorage.getItem('activeMenuProduit');
    if (activeMenuProduit) {
      $('ul.navbar-nav li.nav-item a.nav-link[data-menu="Accueil"]').removeClass('active');
      $('ul.navbar-nav li.nav-item a.nav-link[data-menu="Produit"]').addClass('active');
    }
  });

  $('#history').click(function() {
    var activeMenuAbout = $('ul.navbar-nav li.nav-item a.nav-link[data-menu="about"]');
    $('ul.navbar-nav li.nav-item a.nav-link[data-menu="Accueil"]').removeClass('active');
    sessionStorage.setItem('activeMenuAbout', activeMenuAbout);
  });
  
  $(document).ready(function() {
    var activeMenuAbout = sessionStorage.getItem('activeMenuAbout');
    if (activeMenuAbout) {
      $('ul.navbar-nav li.nav-item a.nav-link[data-menu="Accueil"]').removeClass('active');
      $('ul.navbar-nav li.nav-item a.nav-link[data-menu="about"]').addClass('active');
    }
  });

  //affichage dropdown itel on hover
  $(document).ready(function () {
    $('.nav-item.dropdown').hover(
        function () {
            $(this).find('.dropdown-menu').slideDown(200); 
        },
        function () {
            $(this).find('.dropdown-menu').slideUp(200);
        }
    );
});


//add class active on pagination slide
$(document).ready(function () {
  $('#carouselExampleAutoplaying').on('slid.bs.carousel', function () {
      $('.carousel-indicators li').removeClass('active');

      var activeSlideIndex = $('#carouselExampleAutoplaying .carousel-inner .carousel-item.active').index();
      $('.carousel-indicators li:eq(' + activeSlideIndex + ')').addClass('active');
  });
});

//READ MORE AND READ LESS ABOUT
$(document).ready(function() {
  $('.more').hide();
  $('#readLess').hide();
  $('#history').hide();

  $('#readMore').click(function() {
    $('.less').hide();
    $('.more').show();
    $('#readLess').show();
    $('#history').show();
    $(this).hide();
  })

  $('#readLess').click(function() {
    $('.less').show();
    $('.more').hide();
    $('#readMore').show();
    $('#history').hide();
    $(this).hide();
  })

})


//owl-carousel

$(document).ready(function() {
  $(".customer_owl-carousel").owlCarousel({
    loop: true,
    margin: 0,
    dots: false,
    nav: true,
    navText: [],
    autoplay: true,
    autoplayHoverPause: true,
    navText: [
        '<i class="fa fa-angle-left" aria-hidden="true"></i>',
        '<i class="fa fa-angle-right" aria-hidden="true"></i>'
    ],
    responsive: {
        0: {
            items: 1
        },
        768: {
            items: 1
        },
        1000: {
            items: 2
        }
    }
  });
})


//filtrer les produits en cliquand un menu

$(document).ready(function () {
  var dataMenuValues = [];

  $("#menu-filters li").on("click", function () {
    var filterValue = $(this).data("filter");

    dataMenuValues = [];

    $(".card-col").each(function () {
      var produitValue = $(this).data("menu");
      dataMenuValues.push(produitValue);
    });

    if (filterValue === "*") {
      $(".card-col").show();
    } else {
      $(".card-col").hide();
      $.each(dataMenuValues, function (index, value) {
        if (filterValue === value) {
          $(".card-col[data-menu='" + value + "']").show();
        }
      });
    }
  });
});


//add end remove class active for menu list

$(document).ready(function() {
  $('#menu-filters .menu-link').click(function() {
    $('#menu-filters .menu-link').removeClass('active');
    $(this).addClass('active');
  })
})

//service readMore

$(document).ready(function() {
  $('.more-description').hide();
  $('.btn-short').hide();

  $('.btn-more').click(function() {
    var description = $(this).closest('.liste-service-content').find('.description');
    var short = description.find('.short-description');
    var dots = description.find('.dots');
    var moreText = description.find('.more-description');
    var btnLess = $(this).next('.btn-short');

    $(this).closest('.liste-service-content').removeClass('service-height');

    short.hide();
    dots.hide();
    $(this).hide();
    moreText.show();
    btnLess.show();
  });

  $('.btn-short').click(function() {
    var description = $(this).closest('.liste-service-content').find('.description');
    var short = description.find('.short-description');
    var dots = description.find('.dots');
    var moreText = description.find('.more-description');
    var btnMore = $(this).prev('.btn-more');
    $(this).closest('.liste-service-content').addClass('service-height');

    moreText.hide();
    $(this).hide();
    short.show();
    dots.show();
    btnMore.show();
  });
});

//hide and show description testimonial

$(document).ready(function() {
  $('.testimonial-more p').hide();
  $('.testimonial-readLess').hide();

  $('.testimonial-readMore').click(function() {
    var description = $(this).closest('.card').find('.description');
    var short = description.find('.testimonial-less p');
    var moreText = description.find('.testimonial-more p');
    var btnLess = $(this).next('.testimonial-readLess');

    $(this).closest('.testimonial-content .card').removeClass('height');
    $(this).closest('.liste-testimonial-content .card').removeClass('list-height');

    short.hide();
    $(this).hide();
    moreText.show();
    btnLess.show();
  });

  $('.testimonial-readLess').click(function() {
    var description = $(this).closest('.card').find('.description');
    var short = description.find('.testimonial-less p');
    var moreText = description.find('.testimonial-more p');
    var btnMore = $(this).prev('.testimonial-readMore');

    $(this).closest('.testimonial-content .card').addClass('height');
    $(this).closest('.liste-testimonial-content .card').addClass('list-height');


    moreText.hide();
    $(this).hide();
    short.show();
    btnMore.show();
  });
});

$(document).ready(function() {
  $('.plus-testimonial p').hide();
  $('.link-minus').hide();

  $('.link-plus').click(function() {
    var description = $(this).closest('.description');
    var short = description.find('.minus-testimonial p');
    var moreText = description.find('.plus-testimonial p');
    var btnLess = description.find('.link-minus');

    $(this).closest('.detail-box').removeClass('custom-heigth');

    short.hide();
    $(this).hide();
    moreText.show();
    btnLess.show();
  });

  $('.link-minus').click(function() {
    var description = $(this).closest('.description');
    var short = description.find('.minus-testimonial p');
    var moreText = description.find('.plus-testimonial p');
    var btnMore = description.find('.link-plus');

    $(this).closest('.detail-box').addClass('custom-heigth');

    moreText.hide();
    $(this).hide();
    short.show();
    btnMore.show();
  });
});



//password toogle
$(document).ready(function() {
  // Au début, cacher le champ texte et afficher l'input password
  $('.toggle-password').hide();
  $('.toggle-text').show();

  // Quand l'icône de l'œil est cliquée pour afficher le mot de passe
  $('.toggle-text').click(function() {
      var passwordInput = $('.password-input');
      var passwordText = $('.password-text');
      var iconeye = $('.toggle-text');
      var iconeyeslash = $('.toggle-password');
      
      passwordInput.hide();  // Cacher le champ password
      passwordText.val(passwordInput.val()).show().removeAttr('readonly').focus();  // Afficher le texte et le rendre modifiable
      iconeye.hide();  // Cacher l'icône de l'œil
      iconeyeslash.show();  // Afficher l'icône d'œil barré
  });

  // Quand l'icône de l'œil barré est cliquée pour cacher le mot de passe
  $('.toggle-password').click(function() {
      var passwordInput = $('.password-input');
      var passwordText = $('.password-text');
      var iconeye = $('.toggle-text');
      var iconeyeslash = $('.toggle-password');
      
      passwordInput.val(passwordText.val()).show().focus();  // Remettre la valeur du texte dans le champ password et afficher
      passwordText.hide();  // Cacher le champ texte
      iconeye.show();  // Afficher l'icône de l'œil
      iconeyeslash.hide();  // Cacher l'icône d'œil barré
  });

  // Synchroniser les champs avant la soumission du formulaire
  $('form').submit(function() {
      // Si le mot de passe est actuellement affiché en texte
      if ($('.password-text').is(':visible')) {
          var passwordInput = $('.password-input');
          var passwordText = $('.password-text');
          // Copier la valeur du champ texte dans le champ password
          passwordInput.val(passwordText.val());
      }
  });
});



//collection type

$("#add-adresse").click(function() {
  const index = +$('#widgets-counter').val();

  const tmpl = $('#register_user_form_adresses').data('prototype').replace(/__name__/g, index);
  
  $('#register_user_form_adresses').append(tmpl);

  $('#widgets-counter').val(index + 1);

  handleDelete();
});

function handleDelete(){
    $('button[data-action="delete"]').click(function() {
        const target = this.dataset.target;
        $(target).remove();
    });
}

function updateCounter() {
    const count = +$('#register_user_form_adresses div.form-group').length;

    $('#widgets-counter').val(count);
}

updateCounter();
handleDelete();

//personnaliser message de confiramtion lors d'une suppression

$(document).ready(function() {
  $('.delete-link').click(function(e) {
      e.preventDefault(); // Empêche le lien de suivre immédiatement
      var deleteUrl = $(this).attr('href'); // Obtenir l'URL de suppression spécifique à ce lien
      $('#confirmation-modal').fadeIn(); // Affiche la boîte de dialogue avec une animation fadeIn

      // Stocker l'URL de suppression spécifique dans un attribut data-*
      $('#confirm-button').data('delete-url', deleteUrl);
  });

  $('#confirm-button').click(function() {
      // Obtenir l'URL de suppression spécifique à partir des données
      var deleteUrl = $(this).data('delete-url');

      // Effectuer une requête AJAX POST pour supprimer l'élément
      $.ajax({
          url: deleteUrl,
          type: 'POST',
          data: {
              _token: '{{ csrf_token() }}' // Inclure le jeton CSRF
          },
          success: function(response) {
              // Gérer la réponse si nécessaire
              console.log(response);
              // Rediriger ou effectuer d'autres actions après la suppression
              window.location.href = "";
          },
          error: function(error) {
              // Gérer les erreurs si nécessaire
              console.error(error);
          }
      });
  });

  $('#cancel-button').click(function() {
      // Si l'utilisateur clique sur "Annuler", cachez la boîte de dialogue
      $('#confirmation-modal').fadeOut();
  });
});

//ckeditor

document.addEventListener('DOMContentLoaded', function () {
  ClassicEditor
      .create(document.querySelector('.ckeditor'))
      .then(editor => {
          editor.model.document.on('change:data', () => {
              const hiddenTextarea = document.querySelector('.ckeditor-hidden');
              hiddenTextarea.value = editor.getData();
          });
      })
      .catch(error => {
          console.error(error);
      });
});

//date-picker

$(document).ready(function() {
  var dateFormat = 'dd/mm/yyyy';
  var dateNow = new Date();
  dateNow.setDate(dateNow.getDate() + 1)

  $.fn.datepicker.dates = {
      days: ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"],
      daysShort: ["Dim.", "Lun.", "Mar.", "Mer.", "Jeu.", "Ven.", "Sam."],
      daysMin: ["D", "L", "M", "M", "J", "V", "S"],
      months: ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"],
      monthsShort: ["Janv.", "Févr.", "Mars", "Avr.", "Mai", "Juin", "Juil.", "Août", "Sept.", "Oct.", "Nov.", "Déc."],
      today: "Aujourd'hui",
      clear: "Effacer",
      format: "dd/mm/yyyy",
      weekStart: 1,
      monthsTitle: "Mois",
      yearSuffix: ""
  };

  $('#booking_form_dateBooking').datepicker({
      format: dateFormat,
      todayHighlight: true,
      daysOfWeekDisabled: '0',
      startDate: dateNow,
      //datesDisabled: dateReserved,
      //datesDisabled: joursFeries.concat(dateConge),
     
  });
});


//stripe for order
$(document).ready(function() {
var stripePublicKey = document.getElementById('stripe-public-key').getAttribute('data-stripe');

  var stripe = Stripe(stripePublicKey, {
    locale: 'fr',
  });
  var checkoutButton = document.getElementById('checkout-button');
  
  var referenceValue = $("#reference").data("reference");
  
  checkoutButton.addEventListener('click', function() {
    fetch("/feane/commande/create-session/" + referenceValue, {
      method: "POST",
    })
    .then(function (response) {
      return response.json();
    })
    .then(function (session) {
      if(session.error == 'order') {
        window.location.replace('{{path("app_order")}}')
      } else {
        return stripe.redirectToCheckout({ sessionId: session.id });
    }
    
    })
    .then(function(result) {
      if(result.error) {
        alert(result.error.message);
      }
    })
    .catch(function (error) {
      console.error("Error:", error);
    });
  });
})

//stripe for booking

$(document).ready(function() {
var stripePublicKey = document.getElementById('stripe_public_key').getAttribute('data-stripe');

  var stripeBooking = Stripe(stripePublicKey, {
    locale: 'fr',
  });
  
  var bookingCheckoutButton = document.getElementById('checkout-button-booking');
  
  var bookingReferenceValue = $("#reference-booking").data("reference");
  var bookingIdValue = $("#id-booking").data("identification");
  
  var url = '{{ path("app_booking_add", {"id": "REPLACE_WITH_BOOKING_ID"}) }}';
  
  url = url.replace("REPLACE_WITH_BOOKING_ID", bookingIdValue);
  
  bookingCheckoutButton.addEventListener('click', function() {
    fetch("/feane/reservation/create-session/" + bookingReferenceValue, {
      method: "POST",
    })
    .then(function (response) {
      return response.json();
    })
    .then(function (session) {
      if(session.error == 'booking') {
        window.location.replace(url);
      } else {
        return stripeBooking.redirectToCheckout({ sessionId: session.id });
    }
    
    })
    .then(function(result) {
      if(result.error) {
        alert(result.error.message);
      }
    })
    .catch(function (error) {
      console.error("Error:", error);
    });
  });
})


