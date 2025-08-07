describe('RT003 - Admin Can Add New App.', () => {
    const seed = Math.floor(Math.random() * 100)
    let shared_data = {
        app_name: `Cypress Test App [${seed}]`,
        app_slug: `cypress_test_app_${seed}`,
        app_type: 'Link',
        app_icon: 'mdi mdi-ab-testing',
        app_url: 'https://www.cypress.io',
    }

    before(() => {
        cy.npmHomeScreenInit()
    })

    // Successfully login and access home screen general tab and uncheck requires login setting.
    it('Successfully login and access home screen general tab and uncheck requires login setting.', () => {
        cy.session(
            'admin_login_and_uncheck_requires_login_general_setting',
            () => {
                const general_tab_url_path =
                    '/wp-admin/admin.php?page=dt_home&tab=general'

                cy.adminGeneralSettingsInit()

                // Obtain alias handle onto require login checkbox.
                cy.get('input#dt_home_require_login').as(
                    'require_login_checkbox'
                )

                // Force to unchecked state and commit updates.
                cy.get('@require_login_checkbox').uncheck()
                cy.get('#ml_email_main_col_update_but').as('update_but')
                cy.get('@update_but').click()

                // Revisit general tab and confirm require login checkbox is not selected.
                cy.visit(general_tab_url_path)
                    .get('@require_login_checkbox')
                    .should('not.be.checked')
            }
        )
    })

    // Admin can add a new app.
    it('Admin can add a new app.', () => {
        cy.session('admin_can_add_a_new_app', () => {
            cy.adminAppsSettingsInit()

            // Click add app button and navigate to create app view.
            cy.get(
                'a[href*="admin.php?page=dt_home&tab=app&action=create"]'
            ).click()

            // Specify app name.
            const app_name = shared_data.app_name
            cy.get('#name').invoke('attr', 'value', app_name)

            // Specify app slug.
            const app_slug = shared_data.app_slug
            cy.get('#slug').invoke('attr', 'value', app_slug)

            // Specify app type.
            const app_type = shared_data.app_type
            cy.get('#type').select(app_type)

            // Specify app to be opened within new tab.
            cy.get('#open_in_new_tab').check()

            // Ensure new app is visible.
            cy.get('#is_hidden').uncheck()

            // Specify app font icon.
            const app_icon = shared_data.app_icon
            cy.get('#icon').invoke('attr', 'value', app_icon)

            // Specify app url.
            const app_url = shared_data.app_url
            cy.get('#url').invoke('attr', 'value', app_url)

            // Keep defaults (visibility, exportability, roles, etc...) and submit form.
            cy.get('#ml_email_main_col_update_but').click()

            // Confirm refreshed apps lists contains created app.
            cy.contains(app_name)
            cy.contains(app_slug)
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

    // Confirm new app is visible within home screen frontend.
    it('Confirm new app is visible within home screen frontend.', () => {
        cy.session(
            'confirm_new_app_is_visible_within_home_screen_frontend.',
            () => {
                // Fetch home screen magic link and attempt to navigate directly into Home Screen, without a login challenge.

                cy.visit(shared_data['home_screen_ml'])

                // Confirm newly created app is visible within frontend home screen.
                cy.get('dt-home-app-grid')
                    .shadow()
                    .find(`dt-home-app-icon[name*="${shared_data.app_name}"]`)
            }
        )
    })

    // Delete the created app.
    it('Delete the created app.', () => {
        cy.session('delete_the_created_app', () => {
            cy.adminAppsSettingsInit()

            // Click delete app button and confirm deletion.
            cy.contains('tr', shared_data.app_slug, { timeout: 10000 })
                .should('have.lengthOf', 1)
                .find(`.delete-apps`, { timeout: 10000 })
                .click()

            cy.on('window:confirm', () => true)
            // Confirm app deletion.

            // Confirm app no longer exists within apps list.

            cy.contains(shared_data.app_name).should('not.exist')
            cy.contains(shared_data.app_slug).should('not.exist')
        })
    })
})
