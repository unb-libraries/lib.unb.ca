const libBaseUrl = 'https://lib.unb.ca'
describe('UNB Libraries Website', {baseUrl: libBaseUrl, groups: ['core']}, () => {

  beforeEach(() => {
    // Ignore error thrown by NewRelic JS-agent.
    cy.on('uncaught:exception', (_) => {
      return false;
    });
  })

  context('Front page', {baseUrl: libBaseUrl}, () => {
    beforeEach(() => {
      cy.visit('/', {block: {intranetApi: false}})
      cy.title()
        .should('contain', 'University of New Brunswick Libraries')
    })

    specify('Title, description, URL meta tags should be available', () => {
      cy.get('head meta[property="og:title"]')
        .its('0.content')
        .should('contain', 'University of New Brunswick Libraries')
      cy.get('head meta[property="og:description"]')
        .its('0.content')
        .should('contain', 'University of New Brunswick Libraries')
      cy.get('head meta[property="og:url"]')
        .its('0.content')
        .should('contain', 'https://lib.unb.ca')
    });

    specify('Header navigation should contain 6+ primary, 4+ secondary items', () => {
      cy.get('#navbar-main ul li')
        .should('have.lengthOf.at.least', 6)
        .contains('Search & Borrow')
        .click()
        .next()
        .within(() => {
          cy.get('a.nav-link')
            .should('have.lengthOf.at.least', 15)
        })
        .click()

      cy.get('#navbar-top > div > nav > ul > li > a')
        .should('have.lengthOf.at.least', 4)
        .contains('Feedback')
        .its('0.href')
        .should('match', /\/services\/your-comments-and-suggestions/)
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

    specify('"Quick Links" block should contain 4+ items', () => {
      cy.visit('')
      cy.get('#quicklinks-wrapper > ul > li')
        .should('have.lengthOf.at.least', 4)
    });

    specify('"Quick Links" block should contain "Book a Study Space"', () => {
      cy.visit('')
      cy.get('#quicklinks-buttons a')
        .contains('Book a Study Space')
        .its('0.href')
        .should('match', /\/services\/bookings/)
    });

    specify('"Quick Links" block should contain "Document Delivery"', () => {
      cy.visit('')
      cy.get('#quicklinks-wrapper > ul > li')
        .should('have.lengthOf.at.least', 4)
        .contains('Document Delivery')
        .its('0.href')
        .should('match', /\/services\/docdel/)
    });

    specify('AskUs widget should be available', () => {
      cy.get('#ask-us')
        .should('contain', 'Ask Us')
    });

    specify('"Hours" should be available for all locations', () => {
      cy.get('.table-lib-hours td')
        .each((location) => {
          cy.wrap(location)
            .should('not.contain', 'Unavailable')
        })
    });

    specify('"Research Guides" block should contain 30+ subjects', () => {
      cy.get('#research-guides')
        .within(() => {
          cy.get('> div a')
            .its('0.href')
            .should('contain', '/guides/view-all')
          cy.get('#database_subjects_chosen')
            .click()
            .within(() => {
              cy.get('ul.chosen-results li')
                .should('have.lengthOf.at.least', 30)
            })
        })
        .click(1, 1)
    });

    specify('"Library News" section should contain 2+ articles', () => {
      cy.get('#block-mainpagecontent')
        .contains('Library News')
        .parents()
        .within(() => {
          cy.get('.library-news h3')
            .should('have.lengthOf.at.least', 2)
        })
    })

    specify('Links to pages (10+), social media (3+), and other resources (6+) should be displayed', () => {
      cy.get('.footer-top li > a')
        .should('have.lengthOf.at.least', 10)
        .contains('Science & Forestry')
        .its('0.href')
        .should('match', /\/about\/science-forestry-library/)

      cy.get('.footer-middle li > a')
        .should('have.lengthOf.at.least', 3)
        .contains('Twitter')
        .its('0.href')
        .should('match', /twitter\.com\/UNBLibraries/)

      cy.get('.footer-bottom li > a')
        .should('have.lengthOf.at.least', 6)
        .contains('Ask Us')
        .its('0.href')
        .should('match', /\/help\/ask-us/)
    });
  })

  const hilPageUrl = `${libBaseUrl}/about/harriet-irving-library`
  context('URL shortener', {baseUrl: hilPageUrl}, () => {
    const redirectUrl = 'https://go.lib.unb.ca/hil'
    specify(`${redirectUrl} should redirect here`, () => {
      cy.shouldRedirect(redirectUrl, hilPageUrl)
    })
  })

  context('Group Study Room Booking', {baseUrl: `${libBaseUrl}/services/bookings`}, () => {

    specify('Selecting building, seating zone, should lead to LibCal page', () => {
      cy.visit('/')
      cy.get('.booking-spaces a')
        .first()
        .click()
      cy.title()
        .should('contain', 'Seating Zones')
      cy.get('.booking-spaces a')
        .first()
        .click()
      cy.title()
        .should('contain', 'LibCal')
    });
  })

  context('Document delivery policies agreement form', {baseUrl: `${libBaseUrl}/services/docdel`}, () => {
    beforeEach(() => {
      cy.visit('')
      cy.title()
        .should('contain', 'Document Delivery')
    })

    specify('Should require agreeing to policies, then redirect to authentication form', () => {
      cy.get('form').within(() => {
        cy.get('input[type="checkbox"]')
          .check({force: true})
        cy.get('input[type="submit"]')
          .its('0.disabled')
          .should('be.false')
      }).submit()
      cy.url()
        .should('match', /web\.lib\.unb\.ca\/docdel_auth/)
      cy.get('form#docdel_auth')
        .should('be.visible')
    });
  })

  context('Document delivery request form', {baseUrl: `${libBaseUrl}/docdel_auth/index.php?genre=journal&rft.jtitle=uitest`}, () => {
    specify('Should require login, then show pre-populated request form', () => {
      cy.visit('')
      cy.get('form#docdel_auth').within(() => {
        cy.get('#user')
          .type(Cypress.env('docdel_user'))
        cy.get('#pass')
          .type(Cypress.env('docdel_pass'))
      }).submit()
      cy.origin('https://relais-host.com', () => {
        cy.get('#txtPublicationTitle', {timeout: 20000})
          .should('contain.value', 'uitest')
      })
    });
  })
})
