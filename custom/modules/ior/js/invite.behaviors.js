(function ($, Drupal) {
    Drupal.behaviors.invite = {
        attach(context, settings) {
          $('#block-pagetitle .invite', context).each(function() {
              $(this).click(async function (event ) {
                  const { origin, pathname } = window.location
                  const url = `${origin}/saml/login?destination=${pathname}`
                  await navigator.clipboard.writeText(url)
              })
          })
        },
    };
})(jQuery, Drupal);
