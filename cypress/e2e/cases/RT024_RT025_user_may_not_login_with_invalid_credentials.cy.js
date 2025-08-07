describe('RT024, RT025 - User May Not Login With Invalid Credentials.', () => {

  before(() => {
    cy.npmHomeScreenInit();
  })

  // Successfully login and access home screen general tab and check requires login setting.
  it('Successfully login and access home screen general tab and check requires login setting.', () => {
    cy.session(
      'admin_login_and_check_requires_login_general_setting',
      () => {

        const general_tab_url_path = '/wp-admin/admin.php?page=dt_home&tab=general';

        cy.adminGeneralSettingsInit();

        // Obtain alias handle onto require login checkbox.
        cy.get('input#dt_home_require_login').as('require_login_checkbox');

        // Force to checked state and commit updates.
        cy.get('@require_login_checkbox').check();
        cy.get('#ml_email_main_col_update_but').as('update_but');
        cy.get('@update_but').click();

        // Revisit general tab and confirm require login checkbox is selected.
        cy.visit(general_tab_url_path)
        .get('@require_login_checkbox')
        .should('be.checked');
      }
    );
  })

  // RT024 - Navigate to frontend home screen login and ensure invalid password based logins fail.
  it('RT024 - Navigate to frontend home screen login and ensure invalid password based logins fail.', () => {
    cy.session(
      'rt024_frontend_home_screen_invalid_password_logins_fail',
      () => {

        /**
         * Ensure uncaught exceptions do not fail test run; however, any thrown
         * exceptions must not be ignored and a ticket must be raised, in order
         * to resolve identified exception.
         *
         * TODO:
         *  - Resolve any identified exceptions.
         */

        cy.on('uncaught:exception', (err, runnable) => {
          // Returning false here prevents Cypress from failing the test
          return false;
        });

        // Capture admin credentials.
        const dt_config = cy.config('dt');
        const username = dt_config.credentials.admin.username;
        const password = dt_config.credentials.admin.password;

        // Attempt to log in, using an invalid username.
        cy.loginHomeScreen(username, 'INVALID');

        // Ensure we're still on the login page; following auto-refresh.
        cy.url().should('include', '/apps/login');

      }
    );
  })

  // RT025 - Navigate to frontend home screen login and ensure invalid username based logins fail.
  it('RT025 - Navigate to frontend home screen login and ensure invalid username based logins fail.', () => {
    cy.session(
      'rt025_frontend_home_screen_invalid_username_logins_fail',
      () => {

        /**
         * Ensure uncaught exceptions do not fail test run; however, any thrown
         * exceptions must not be ignored and a ticket must be raised, in order
         * to resolve identified exception.
         *
         * TODO:
         *  - Resolve any identified exceptions.
         */

        cy.on('uncaught:exception', (err, runnable) => {
          // Returning false here prevents Cypress from failing the test
          return false;
        });

        // Capture admin credentials.
        const dt_config = cy.config('dt');
        const username = dt_config.credentials.admin.username;
        const password = dt_config.credentials.admin.password;

        // Attempt to log in, using an invalid username.
        cy.loginHomeScreen('INVALID', password);

        // Ensure we're still on the login page; following auto-refresh.
        cy.url().should('include', '/apps/login');

      }
    );
  })

});
