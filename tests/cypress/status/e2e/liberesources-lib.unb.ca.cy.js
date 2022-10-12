const host = 'https://lib.unb.ca'
describe('UNB Libraries Website', {baseUrl: host, groups: ['core','eresources']}, () => {

    context('Front page', {baseUrl: host}, () => {
        beforeEach(() => {
            cy.visit('')
        })

        specify('Header should contain "Databases" tab', () => {
            cy.get('#discovery-search nav')
                .contains('Databases')
                .click()
            cy.get('form[action="/eresources"] input[type="text"]')
                .click()
            cy.get('div.option')
                .should('have.lengthOf.at.least', 200)
                .and('contain', 'GeoRef')
        })

        specify('Header should contain "Journals & Newspapers" tab', () => {
            cy.get('#searchBtn4')
                .click()
            cy.get('#searchPanel4')
                .within(() => {
                    cy.get('a')
                        .contains('More Search Options')
                        .its('0.href')
                        .should('contain', 'https://lib.unb.ca/eresources?form_id=eres_journals')
                    cy.get('a')
                        .contains('Newspaper Guide')
                        .its('0.href')
                        .should('contain', 'https://lib.unb.ca/eresources/newspaper-guide')
                    cy.get('#search_results_journals > div > div > button')
                        .should('be.visible')
                })
        })

        specify('Header should contain "More" tab', () => {
            cy.get('#searchBtn5')
                .its('0.href')
                .should('contain', 'https://lib.unb.ca/eresources?form_id=eres_reference')
        })
    })
})
