const libReservesBaseUrl = 'https://lib.unb.ca'
describe('UNB Libraries Website', {baseUrl: libReservesBaseUrl, groups: ['core','reserves']}, () => {

    context('Front page', {baseUrl: libReservesBaseUrl}, () => {
        specify('Header should contain "Reserves" tab', () => {
            cy.visit('')
            cy.get('#searchBtn2')
                .click()
            cy.get('#searchPanel2')
                .within(() => {
                    cy.get('#semester > option')
                        .should('have.lengthOf.at.least', 10)
                        .and('contain', 'All semesters')
                    cy.get('#searchReservesSubmit')
                        .should('be.visible')
                })
        })
    })
})
