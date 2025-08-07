describe('Frontend Authentication', () => {
  before(() => {
    cy.npmHomeScreenInit();
  })

  it('Successfully access home screen app.', () => {
    cy.loginHomeScreen('admin','admin');
  })
})
