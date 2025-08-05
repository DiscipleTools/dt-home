describe('Admin Home Screen General Settings Test Cases', () => {

  // Successfully login and access home screen general tab.
  it('Successfully login and access home screen general tab.', () => {
    cy.session(
      'general_settings',
      () => {
        cy.adminGeneralSettingsInit();
      }
    );
  })

  // Can update require users to login general setting.
  it('Can update require users to login general setting.', () => {
    cy.session(
      'update_require_users_setting',
      () => {

        const general_tab_url_path = '/wp-admin/admin.php?page=dt_home&tab=general';

        cy.adminGeneralSettingsInit();

        // Obtain alias handle onto require login checkbox.
        cy.get('input#dt_home_require_login').as('require_login_checkbox');

        // Force a checked state.
        cy.get('@require_login_checkbox').check();

        // Commit updates.
        cy.get('#ml_email_main_col_update_but').as('update_but');
        cy.get('@update_but').click();

        // Revisit general tab and confirm require login checkbox is selected.
        cy.visit(general_tab_url_path)
        .get('@require_login_checkbox')
        .should('be.checked');

        // Now, execute the reverse and uncheck setting.
        cy.get('@require_login_checkbox').uncheck();
        cy.get('@update_but').click();

        // Revisit general tab and confirm require login checkbox is not selected.
        cy.visit(general_tab_url_path)
        .get('@require_login_checkbox')
        .should('not.be.checked');
      }
    );
  })

  // Can update allow users to reset their apps general setting.
  it('Can update allow users to reset their apps general setting.', () => {
    cy.session(
      'update_allow_users_to_reset_setting',
      () => {

        const general_tab_url_path = '/wp-admin/admin.php?page=dt_home&tab=general';

        cy.adminGeneralSettingsInit();

        // Obtain alias handle onto allow apps reset checkbox.
        cy.get('input#dt_home_reset_apps').as('reset_apps_checkbox');

        // Force a checked state.
        cy.get('@reset_apps_checkbox').check();

        // Commit updates.
        cy.get('#ml_email_main_col_update_but').as('update_but');
        cy.get('@update_but').click();

        // Revisit general tab and confirm checkbox is selected.
        cy.visit(general_tab_url_path)
        .get('@reset_apps_checkbox')
        .should('be.checked');

        // Now, execute the reverse and uncheck setting.
        cy.get('@reset_apps_checkbox').uncheck();
        cy.get('@update_but').click();

        // Revisit general tab and confirm checkbox is no longer selected.
        cy.visit(general_tab_url_path)
        .get('@reset_apps_checkbox')
        .should('not.be.checked');
      }
    );
  })

  // Can update restrict access for some users general setting.
  it('Can update restrict access for some users general setting.', () => {
    cy.session(
      'update_restrict_access_setting',
      () => {

        const general_tab_url_path = '/wp-admin/admin.php?page=dt_home&tab=general';

        cy.adminGeneralSettingsInit();

        // Obtain alias handle onto checkbox.
        cy.get('input#dt_home_use_capabilities').as('restrict_access_checkbox');

        // Force a checked state.
        cy.get('@restrict_access_checkbox').check();

        // Commit updates.
        cy.get('#ml_email_main_col_update_but').as('update_but');
        cy.get('@update_but').click();

        // Revisit general tab and confirm checkbox is selected.
        cy.visit(general_tab_url_path)
        .get('@restrict_access_checkbox')
        .should('be.checked');

        // Now, execute the reverse and uncheck setting.
        cy.get('@restrict_access_checkbox').uncheck();
        cy.get('@update_but').click();

        // Revisit general tab and confirm checkbox is no longer selected.
        cy.visit(general_tab_url_path)
        .get('@restrict_access_checkbox')
        .should('not.be.checked');
      }
    );
  })

  // Can update add apps link to D.T menu general setting.
  it('Can update add apps link to D.T menu general setting.', () => {
    cy.session(
      'update_add_apps_link_dt_menu_setting',
      () => {

        const general_tab_url_path = '/wp-admin/admin.php?page=dt_home&tab=general';

        cy.adminGeneralSettingsInit();

        // Obtain alias handle onto checkbox.
        cy.get('input#dt_home_show_in_menu').as('add_apps_link_checkbox');

        // Force a checked state.
        cy.get('@add_apps_link_checkbox').check();

        // Commit updates.
        cy.get('#ml_email_main_col_update_but').as('update_but');
        cy.get('@update_but').click();

        // Revisit general tab and confirm checkbox is selected.
        cy.visit(general_tab_url_path)
        .get('@add_apps_link_checkbox')
        .should('be.checked');

        // Now, execute the reverse and uncheck setting.
        cy.get('@add_apps_link_checkbox').uncheck();
        cy.get('@update_but').click();

        // Revisit general tab and confirm checkbox is no longer selected.
        cy.visit(general_tab_url_path)
        .get('@add_apps_link_checkbox')
        .should('not.be.checked');
      }
    );
  })

})
