describe('RT042 - User Attempts To Access An Invalid Shared Link.', () => {

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

        // Revisit general tab and confirm require login checkbox is selected.
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

  // User attempts to gain access with an invalid shared link.
  it('User attempts to gain access with an invalid shared link.', () => {
    cy.session(
      'user_attempts_to_gain_access_with_an_invalid_shared_link.',
      () => {

        // Fetch home screen magic link and invalidate.
        cy.visit(shared_data['home_screen_ml'] + '_INVALID');

        // Confirm user is presented with invalid magic link prompt.
        cy.contains('Invalid Link');
      }
    );
  })
})
