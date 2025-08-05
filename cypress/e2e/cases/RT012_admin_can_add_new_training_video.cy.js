describe('RT012 - Admin Can Add New Training Video.', () => {

  const seed = Math.floor(Math.random() * 100);
  let shared_data = {
    'video_name': `Cypress Test Video [${seed}]`,
    'video_anchor': `cypress_test_video_${seed}`,
    'video_embed_code': '<iframe width="560" height="315" src="https://www.youtube.com/embed/EuCyqloPH0I?si=_VGXzezr2Esqdvpq" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>'
  };

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

  // Admin can add a new training video.
  it('Admin can add a new training video.', () => {
    cy.session(
      'admin_can_add_a_new_training_video.',
      () => {

        cy.adminTrainingSettingsInit();

        // Click add training button and navigate to create training video view.
        cy.get('a[href*="admin.php?page=dt_home&tab=training&action=create"]').click();

        // Specify training video name.
        const video_name = shared_data.video_name;
        cy.get('#name').invoke('attr', 'value', video_name);

        // Specify embedded video code.
        const video_embed_code = shared_data.video_embed_code;
        cy.get('#embed_video').type(video_embed_code);

        // Specify video anchor.
        const video_anchor = shared_data.video_anchor;
        cy.get('#anchor').invoke('attr', 'value', video_anchor);

        // Specify training video sort order.
        cy.get('#sort').invoke('attr', 'value', 0);

        // Keep remaining defaults and submit form.
        cy.get('#ml_email_main_col_update_but').click();

        // Confirm refreshed training video list contains created video.
        cy.contains(video_name);
        cy.contains(video_anchor);
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

  // Confirm new training video is visible within home screen frontend.
  it('Confirm new training video is visible within home screen frontend.', () => {
    cy.session(
      'confirm_new_training_video_is_visible_within_home_screen_frontend.',
      () => {

        // Fetch home screen magic link and attempt to navigate directly into Home Screen, without a login challenge.
        cy.visit(shared_data['home_screen_ml'] + '/training');

        // Confirm newly created video is visible within frontend home screen.
        cy.get('dt-home-video-list').shadow().find(`div[id*="${shared_data.video_anchor}"]`);
      }
    );
  })

})
