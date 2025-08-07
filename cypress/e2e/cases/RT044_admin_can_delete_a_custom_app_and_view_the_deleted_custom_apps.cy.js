describe('RT044: The administrator can delete a custom app and view the deleted custom apps', () => {
    let shared_data = {
        app_name: `Disciple.Tools`,
        app_slug: `disciple-tools`,
        app_type: 'Web View',
    }

    before(() => {
        cy.npmHomeScreenInit()
    })

    it('The administrator can delete a custom app', () => {
        cy.session('admin_can_delete_a_custom_app', () => {
            cy.adminAppsSettingsInit()

            cy.get(
                `a[onclick*="softdelete('${shared_data.app_slug}')"]`
            ).click()

            // Click ok for displayed confirmation.
            cy.on('window:confirm', () => true)

            cy.reload()
            cy.contains('Exact', shared_data.app_name, {
                timeout: 10000,
            }).should('not.exist')
        })
    })

    // Verify the deleted app is not shown in the available apps
    it('The administrator can view the deleted custom apps', () => {
        cy.session('admin_can_view_deleted_custom_apps', () => {
            cy.adminAppsSettingsInit()

            // Click on the available apps button to view the deleted apps
            cy.get(
                'a[href*="admin.php?page=dt_home&tab=app&action=available_app"]'
            ).click()

            // verify the deleted app is shown
            cy.contains(shared_data.app_name, {
                timeout: 10000,
            }).should('exist')
        })
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

    // Confirm the Deleted app is removed within home screen frontend.
    it('Confirm the Deleted app is removed within home screen frontend.', () => {
        cy.session(
            'confirm_deleted_app_is_removed_within_home_screen_frontend',
            () => {
                cy.on('uncaught:exception', (err, runnable) => {
                    // Returning false here prevents Cypress from failing the test
                    return false
                })

                // Ensure the reset option has been enabled beforehand.
                cy.adminGeneralSettingsInit()
                cy.get('#dt_home_reset_apps').check()
                cy.get('#ml_email_main_col_update_but').click()

                cy.visit(shared_data['home_screen_ml'])

                cy.resetFrontendApps()

                // Confirm deleted app is not visible within frontend home screen.
                cy.get('dt-home-app-grid')
                    .shadow()
                    .find(`dt-home-app-icon[name*="${shared_data.app_name}"]`)
                    .should('not.exist')
            }
        )
    })

    // Restore the deleted app
    it('The administrator can restore the deleted custom app', () => {
        cy.session('admin_can_restore_deleted_custom_app', () => {
            cy.adminAppsSettingsInit()

            cy.get(
                'a[href*="admin.php?page=dt_home&tab=app&action=available_app"]'
            ).click()

            cy.get(
                `a[onclick*="restore_app('${shared_data.app_slug}')"]`
            ).click()

            // Click ok for displayed confirmation.
            cy.on('window:confirm', () => true)

            cy.reload()
            cy.contains('Exact', shared_data.app_name, {
                timeout: 10000,
            }).should('not.exist')
        })
    })
})
