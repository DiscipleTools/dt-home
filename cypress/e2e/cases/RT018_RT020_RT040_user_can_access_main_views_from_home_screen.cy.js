describe('RT018, RT020, RT040 - User Can Access Main Views From Home Screen.', () => {

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

  // RT018 - User can access apps view from home screen.
  it('RT018 - User can access apps view from home screen.', () => {
    cy.session(
      'rt018_user_can_access_apps_view_from_home_screen',
      () => {

        // Fetch home screen magic link and attempt to navigate directly into Home Screen, without a login challenge.
        cy.visit(shared_data['home_screen_ml']);

        // Confirm apps list is visible within frontend home screen.
        cy.get('dt-home-app-grid');
      }
    );
  })

  // RT020, RT040 - User can access training view from home screen and presented with videos.
  it('RT020, RT040 - User can access training view from home screen and presented with videos.', () => {
    cy.session(
      'rt020_rt040_user_can_access_training_view_with_videos_list',
      () => {

        // Fetch home screen magic link and attempt to navigate directly into Home Screen, without a login challenge.
        cy.visit(shared_data['home_screen_ml'] + '/training');

        // Confirm training videos list is visible within frontend home screen.
        cy.get('dt-home-video-list');
      }
    );
  })
})
