describe('RT001 - Admin Allows Non-Logged In Users To Access Home Screen.', () => {

  let shared_data = {};

  before(() => {
    cy.npmHomeScreenInit();
  })

  // Successfully login and access home screen general tab and uncheck requires login setting.
  it('Successfully login and access home screen general tab and uncheck requires login setting.', () => {
    cy.session(
      'admin_login_and_uncheck_requires_login_general_setting',
      () => {

        const general_tab_url_path = '/wp-admin/admin.php?page=dt_home&tab=general';

        cy.adminGeneralSettingsInit();

        // Obtain alias handle onto require login checkbox.
        cy.get('input#dt_home_require_login').as('require_login_checkbox');

        // Force to unchecked state and commit updates.
        cy.get('@require_login_checkbox').uncheck();
        cy.get('#ml_email_main_col_update_but').as('update_but');
        cy.get('@update_but').click();

        // Revisit general tab and confirm require login checkbox is not selected.
        cy.visit(general_tab_url_path)
        .get('@require_login_checkbox')
        .should('not.be.checked');
      }
    );
  })

  // Login to D.T frontend and obtain home screen plugin magic link.
  it('Login to D.T frontend and obtain home screen plugin magic link.', () => {
    cy.session(
      'dt_frontend_login_and_obtain_home_screen_plugin_ml',
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

        // Fetch the home screen plugin magic link associated with admin user.
        cy.fetchDTUserHomeScreenML(username, password);
        cy.get('@home_screen_ml').then(ml => {
          shared_data['home_screen_ml'] = ml;
        });

      }
    );
  })

  // Confirm non-logged in users can access home screen.
  it('Confirm non-logged in users can access home screen.', () => {
    cy.session(
      'confirm_non_logged_in_users_can_access_home_screen.',
      () => {

        // Fetch home screen magic link and attempt to navigate directly into Home Screen, without a login challenge.
        const home_screen_ml = shared_data['home_screen_ml'];
        cy.visit(home_screen_ml);

        // Confirm the copy text field is displayed and contain home screen magic link.
        cy.get('dt-copy-text').should('have.value', home_screen_ml + '/share');
      }
    );
  })

})
