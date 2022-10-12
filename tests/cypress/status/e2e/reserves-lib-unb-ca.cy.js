const reservesBase = 'lib.unb.ca/reserves/index.php'
describe('Reserves', {baseUrl: reservesBase, groups: ['core','reserves']}, () => {

  context('Main page', {baseUrl: reservesBase}, () => {
    beforeEach(() => {
      cy.on('uncaught:exception', () => {
        return false;
      })
    })

    specify('Search for "arts1000" should find 2+ results', () => {
      cy.visit('/')
      cy.get('form#searchReserves').within(() => {
        cy.get('#keywords')
          .type('arts1000')
        cy.get('#semester')
          .select(0)
      }).submit()
      cy.url()
        .should('contain', 'arts1000')
      cy.get('#quicksearch-results tr')
        .should('have.lengthOf.at.least', 2)
    });
  })

  context('Reserves: Readings records', {baseUrl: `${url}/viewReserves/13631`}, () => {
    specify('3+ readings should be listed under 2012/FY ARTS*1000*FR13Y', () => {
      cy.visit('/')
      cy.get('.heading-container')
        .first()
        .should('be.visible')
      cy.get('.recordRow')
        .should('have.lengthOf.at.least', 3)
    });
  })

  context('Reserves: OPAC availability', {baseUrl: `${url}/viewReserves/18082`}, () => {
    specify('"The Arts 1000 Reader" should be available', () => {
      cy.visit('/')
      cy.get('.itemPhysical .availability-where i')
        .should('be.empty')
      cy.get('.itemPhysical .availability-where')
        .should('not.contain.text', 'No record found')
    });

  })
})
