const guidesBaseUrl = 'https://lib.unb.ca'
describe('Research Guides', {baseUrl: guidesBaseUrl, groups: ['core']}, () => {

  context('Main page', {baseUrl: `${guidesBaseUrl}/guides`}, () => {
    beforeEach(() => {
      cy.visit('/')
      cy.title()
          .should('contain', 'Research Guides')
    })

    specify('Header should contain "Research by Subject"', () => {
      cy.get('#main h1')
          .should('contain', 'Research by Subject')
    });

    specify('List should contain 20+ entries', () => {
      cy.get('#block-mainpagecontent li')
          .should('have.lengthOf.at.least', 20)
    });
  })

  context('All guides', {baseUrl: `${guidesBaseUrl}/guides/view-all`}, () => {
    specify('Table should contain at least 20 rows', () => {
      cy.visit('/')
      cy.get('td.views-field-title')
          .should('have.lengthOf.at.least', 20)
    });
  })

  context('Profile: Joanne Smyth', {baseUrl: `${guidesBaseUrl}/profile/joanne-smyth`}, () => {
    beforeEach(() => {
      cy.visit('')
    })

    specify('Header should contain "Joanne"', () => {
      cy.get('#main h1')
          .should('contain', 'Joanne')
    });

    specify('Sidebar should contain 5+ entries', () => {
      cy.get('#block-mainpagecontent li')
          .should('have.lengthOf.at.least', 5)
    });
  })

  context('Category: History', {baseUrl: `${guidesBaseUrl}/guides/category/history`}, () => {
    beforeEach(() => {
      cy.visit('/')
    })

    specify('Header should contain "History Guides', () => {
      cy.get('#main h1')
          .should('contain', 'History Guides')
    })

    specify('"Detailed Guides" should contain 1+ entries', () => {
      cy.get('ul.detailed-guides')
          .should('have.lengthOf.at.least', 1)
    });

    specify('"Course Guides" should contain 1+ entries', () => {
      cy.get('ul.course-guides li')
          .should('have.lengthOf.at.least', 1)
    });

    specify('"Related Guides" should contain at least 1+ entries', () => {
      cy.get('ul.related-guides li')
          .should('have.lengthOf.at.least', 1)
    });

    specify('"Related Categories" should contain at least 1+ entries', () => {
      cy.get('ul.related-categories li')
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

  context('Guide: Help', {baseUrl: `${guidesBaseUrl}/guides/help`}, () => {
    specify('Header should contain "Help Guide"', () => {
      cy.visit('/')
      cy.get('#main h1')
          .should('contain', 'Help Guide')
    })
  })

})

