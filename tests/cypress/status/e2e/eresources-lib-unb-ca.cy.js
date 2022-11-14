const eresourcesBaseUrl = 'https://lib.unb.ca/eresources'
describe('e-Resources', {baseUrl: eresourcesBaseUrl, retries: {runMode: 4}, groups: ['core','eresources']}, () => {

  context('Knowledge Base and Resolver', {baseUrl: eresourcesBaseUrl}, () => {
    specify('Searching "Journals & Newspapers" for "Biology" should find 10+ results', () => {
      cy.waitAfterFailedAttempts(3, 60000)

      cy.visit('?form_id=eres_journals')
      cy.get('form#eres-journals').within(() => {
        cy.get('input#journals-query')
          .type('Biology')
      }).submit()
      cy.get('ul.list-results li')
        .should('have.lengthOf.at.least', 10)
    })

    specify('Search results should link to JSTOR ', () => {
      cy.visit('?form_id=eres_journals&type=title')
      cy.get('form#eres-journals').within(() => {
        cy.get('input#journals-query')
          .type('Journal of Business of the University of Chicago')
      }).submit()
      cy.get('ul.list-results li a')
        .contains('JSTOR Arts & Sciences')
        .should('be.visible')
    })

    specify('"Academic Search Premier" record should be available', () => {
      cy.visit('?id=1')
      cy.get('#resource h3')
        .should('contain.text', 'Academic Search Premier')
    })
  })
})
