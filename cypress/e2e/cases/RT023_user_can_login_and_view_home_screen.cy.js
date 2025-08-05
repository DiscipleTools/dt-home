describe('RT023 - User Can Login And View Home Screen.', () => {

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

        // Force to unchecked state and commit updates.
        cy.get('@require_login_checkbox').uncheck();
        cy.get('#ml_email_main_col_update_but').as('update_but');
        cy.get('@update_but').click();

        // Revisit general tab and confirm require login checkbox is selected.
        cy.visit(general_tab_url_path)
        .get('@require_login_checkbox')
        .should('not.be.checked');
      }
    );
  })

  // Login to home screen frontend and confirm apps list is shown.
  it('Login to home screen frontend and confirm apps list is shown.', () => {
    cy.session(
      'login_to_home_screen_frontend_and_confirm_apps_list_is_shown',
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

        // Login and Confirm apps list is visible within frontend home screen.
        cy.loginHomeScreen(username, password);
        cy.get('dt-home-app-grid');
      }
    );
  })
})
