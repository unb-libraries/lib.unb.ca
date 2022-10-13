const guidesBaseUrl = 'https://guides.lib.unb.ca'
describe('Subject guides', {baseUrl: guidesBaseUrl, groups: ['core']}, () => {

  context('Main page', {baseUrl: guidesBaseUrl}, () => {
    beforeEach(() => {
      cy.visit('/')
      cy.title()
          .should('contain', 'UNB Libraries Guides')
    })

    specify('Header should contain "Research by Subject"', () => {
      cy.get('#main h1')
          .should('contain', 'Research by Subject')
    });

    specify('List should contain 20+ entries', () => {
      cy.get('.research-guides .guide-item')
          .should('have.lengthOf.at.least', 20)
    });
  })

  context('All guides', {baseUrl: `${guidesBaseUrl}/research-guides`}, () => {
    specify('Table should contain at least 20 rows', () => {
      cy.visit('/')
      cy.get('td.views-field-title')
          .should('have.lengthOf.at.least', 20)
    });
  })

  context('Profile: Joanne Smyth', {baseUrl: `${guidesBaseUrl}/profile/jsmyth`}, () => {
    beforeEach(() => {
      cy.visit('')
    })

    specify('Header should contain "Joanne"', () => {
      cy.get('#main h1')
          .should('contain', 'Joanne')
    });

    specify('Sidebar should contain 5+ entries', () => {
      cy.get('.view-user-guides li')
          .should('have.lengthOf.at.least', 5)
    });
  })

  context('Category: History', {baseUrl: `${guidesBaseUrl}/category/history`}, () => {
    beforeEach(() => {
      cy.visit('/')
    })

    specify('Header should contain "History Guides', () => {
      cy.get('#main h1')
          .should('contain', 'History Guides')
    })

    specify('"Detailed Guides" should contain 1+ entries', () => {
      cy.get('ul.detailed li')
          .should('have.lengthOf.at.least', 1)
    });

    specify('"Course Guides" should contain 1+ entries', () => {
      cy.get('ul.course li')
          .should('have.lengthOf.at.least', 1)
    });

    specify('"Related Guides" should contain at least 1+ entries', () => {
      cy.get('ul.related li')
          .should('have.lengthOf.at.least', 1)
    });

    specify('"Top Article & Research Databases" should contain 1+ entries', () => {
      cy.get('.resourceListings.databases li')
          .should('have.lengthOf.at.least', 1)
    });

    specify('"Top Reference Materials" should contain 1+ entries', () => {
      cy.get('.resourceListings.reference li')
          .should('have.lengthOf.at.least', 1)
    });
  })

  context('Guide: Best Practices', {baseUrl: `${guidesBaseUrl}/guide/227`}, () => {
    specify('Header should contain "Best Practices Guide"', () => {
      cy.visit('/')
      cy.get('#main h1')
          .should('contain', 'Best Practices Guide')
    })
  })

})

