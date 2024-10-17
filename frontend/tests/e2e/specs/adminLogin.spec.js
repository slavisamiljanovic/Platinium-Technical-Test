describe('Admin Login', () => {
  it('should log in with valid credentials', () => {
    cy.visit('/login')
    cy.get('input[id="email"]').type('admin1@telekod.com')
    cy.get('input[id="password"]').type('password1')
    cy.get('button[type="submit"]').click()
    cy.url().should('eq', 'http://localhost:8080/dashboard')
  })

  it('should show error message on failed login', () => {
    cy.visit('/login')
    cy.get('input[id="email"]').type('wrong@example.com')
    cy.get('input[id="password"]').type('wrongpassword')
    cy.get('button[type="submit"]').click()
    cy.get('.response-error').should('contain', 'Unauthorized: E-mail or password is not valid')
  })
})
