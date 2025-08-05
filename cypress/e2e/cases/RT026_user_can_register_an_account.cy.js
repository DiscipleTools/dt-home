describe('RT026 - User Can Register An Account.', () => {

  const seed = Math.floor(Math.random() * 100);
  let shared_data = {
    'username': `cypress_test_user_${seed}`,
    'email': `cypress_test_user_${seed}@test.local`,
    'password': `cypress_test_user_${seed}_pwd`
  };

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

  // Navigate to frontend home screen user registration and create new account.
  it('Navigate to frontend home screen user registration and create new account.', () => {
    cy.session(
      'frontend_home_screen_new_user_account_registration',
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

        // Navigate to registration view.
        cy.visit('/apps/register');

        // Extract nonce value, to be included within post request.
        cy.get('input[name="_wpnonce"]')
        .invoke('attr', 'value')
        .then(nonce => {

          // Create headers, also including nonce value.
          const headers = {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-WP-Nonce': nonce
          };

          // Submit post request to backend register controller.
          cy.request({
            method: 'POST',
            url: '/apps/register',
            form: false,
            failOnStatusCode: false,
            headers: headers,
            body: {
              username: shared_data.username,
              email: shared_data.email,
              password: shared_data.password,
              confirm_password: shared_data.password
            }
          })
          .then((response) => {

            // Ensure we have a response and force page refresh.
            cy.reload(true);
          });
        });
      }
    );
  })

  // Confirm D.T WP account has been created for new user.
  it('Confirm D.T WP account has been created for new user.', () => {
    cy.session(
      'confirm_dt_wp_account_has_been_created_for_new_user',
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

        // Capture admin credentials and login to D.T WP backend.
        const dt_config = cy.config('dt');
        cy.loginAdmin(dt_config.credentials.admin.username, dt_config.credentials.admin.password);

        // Navigate to admin users page and search for recently created user.
        cy.visit('/wp-admin/users.php');
        cy.get('#user-search-input').type(shared_data.username);
        cy.get('#search-submit').click();

        // Finally, confirm new user account exists, with correct role assignment.
        cy.contains(shared_data.email);
        cy.get('td.roles.column-roles[data-colname="Roles"]').contains('Multiplier');

      }
    );
  })

})
