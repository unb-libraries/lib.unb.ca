(function ($, Drupal) {
    Drupal.behaviors.invite = {
        attach(context, settings) {

          $('#block-pagetitle .invite', context).each(function(index, btn) {
              const originalText = btn.children[0].innerText
              const setLabel = (label) => btn.children[0].innerText = label

              $(this).click(async function (event ) {
                  event.preventDefault()
                  const { origin, pathname } = window.location
                  const url = `${origin}/saml/login?destination=${pathname}`
                  await navigator.clipboard.writeText(url)
                  setLabel('URL copied')
                  const listener = btn.addEventListener('blur', () => {
                      setLabel(originalText)
                      btn.removeEventListener('blur', listener)
                  })

              })
          })
        },
    };
})(jQuery, Drupal);
