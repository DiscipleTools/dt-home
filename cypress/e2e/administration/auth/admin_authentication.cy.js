describe('Admin Authentication', () => {

  // Successfully access (login) admin backend.
  it('Successfully access (login) admin backend.', () => {

    // Capture admin credentials.
    const dt_config = cy.config('dt');
    const username = dt_config.credentials.admin.username;
    const password = dt_config.credentials.admin.password;

    // Login to WP Admin area.
    cy.loginAdmin(username, password);
  })
})
