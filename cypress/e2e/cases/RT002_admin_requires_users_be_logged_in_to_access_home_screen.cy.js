describe('RT002 - Admin Requires Users Be Logged In To Access Home Screen.', () => {
    let shared_data = {}

    before(() => {
        cy.npmHomeScreenInit()
    })

    // Successfully login and access home screen general tab and check requires login setting.
    it('Successfully login and access home screen general tab and check requires login setting.', () => {
        cy.session(
            'admin_login_and_check_requires_login_general_setting',
            () => {
                const general_tab_url_path =
                    '/wp-admin/admin.php?page=dt_home&tab=general'

                cy.adminGeneralSettingsInit()

                // Obtain alias handle onto require login checkbox.
                cy.get('input#dt_home_require_login').as(
                    'require_login_checkbox'
                )

                // Force to checked state and commit updates.
                cy.get('@require_login_checkbox').check()
                cy.get('#ml_email_main_col_update_but').as('update_but')
                cy.get('@update_but').click()

                // Revisit general tab and confirm require login checkbox is selected.
                cy.visit(general_tab_url_path)
                    .get('@require_login_checkbox')
                    .should('be.checked')
            }
        )
    })

    // Login to D.T frontend and obtain home screen plugin magic link.
    it('Login to D.T frontend and obtain home screen plugin magic link.', () => {
        cy.session('dt_frontend_login_and_obtain_home_screen_plugin_ml', () => {
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
                return false
            })

            // Capture admin credentials.
            const dt_config = cy.config('dt')
            const username = dt_config.credentials.admin.username
            const password = dt_config.credentials.admin.password

            // Fetch the home screen plugin magic link associated with admin user.
            cy.fetchDTUserHomeScreenML(username, password)
            cy.get('@home_screen_ml').then((ml) => {
                shared_data['home_screen_ml'] = ml
            })
        })
    })

    // Confirm non-logged in users are challenged when accessing home screen.
    it('Confirm non-logged in users are challenged when accessing home screen.', () => {
        cy.session(
            'confirm_non_logged_in_users_are_challenged_when_accessing_home_screen',
            () => {
                // Handle uncaught exceptions to prevent test failure.
                cy.on('uncaught:exception', (err, runnable) => {
                    // Returning false here prevents Cypress from failing the test
                    return false
                })

                // Fetch home screen magic link and attempt to navigate directly into Home Screen, without a login challenge.
                const home_screen_ml = shared_data['home_screen_ml']
                cy.visit(home_screen_ml)

                // Confirm user is presented with home screen login prompt.
                cy.get(`dt-text[name*="username"]`)
                cy.get(`dt-text[name*="password"]`)
            }
        )
    })
})
