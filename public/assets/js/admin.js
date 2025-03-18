//SHOW AND HIDE PASSWORD 
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

$(document).ready(function () {
    $(document).on("click", ".produit-link", function (e) {
        e.preventDefault();

        if (parseInt($(this).data("count")) > 0) {
            var count = $(this).data("count");
            var list = $(this).data("list");
            var linkWrapper = $(this).parent(".produit-link-wrapper");
            $(this).html(list);

            $("li").css("list-style", "none");
            setTimeout(() => {
                $(this).children().children().attr("data-count", count);
            }, 500);
        }

        // $('.produit-list').attr('data-count', count);
    });

    $(document).on("click", ".produit-list", function (e) {
        e.preventDefault();
        var count = $(this).data("count");
        var linkWrapper = $(this).closest(".produit-link-wrapper");
        var list = $(this).closest(".produit-link").data("list");

        linkWrapper.html(
            '<a href="#" class="produit-link" data-count="' +
            count +
            '">' +
            count +
            "</a>"
        );

        linkWrapper.children(".produit-link").attr("data-list", list);
    });
});

//ckeditor


document.addEventListener('DOMContentLoaded', function () {
    ClassicEditor.create(document.querySelector('.ckeditor'));
});

$(document).ready(function() {
    const description = $('div .form-group div div.ck.ck-reset.ck-editor.ck-rounded-corners div.ck.ck-editor__main div.ck.ck-content.ck-editor__editable.ck-rounded-corners.ck-editor__editable_inline.ck-blurred p').html();
   
    $('#Service_description').val(description);
    $('#Produit_description').val(description);
    $('#Table_description').val(description);
    $('#Carrier_description').val(description);
    

})