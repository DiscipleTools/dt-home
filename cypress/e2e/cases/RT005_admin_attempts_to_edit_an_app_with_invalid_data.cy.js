describe('RT005 - Administrator attempts to edit an app with invalid data.', () => {
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

    // Admin attempts to edit an app with invalid data.
    it('Admin attempts to edit an app with invalid data.', () => {
        cy.session('admin_attempts_to_edit_an_app_with_invalid_data', () => {
            cy.adminAppsSettingsInit()

            // Locate and click edit option for previously created app.
            const app_slug = shared_data.app_slug
            cy.get(
                `a[href*="admin.php?page=dt_home&tab=app&action=edit/${app_slug}"]`
            )
                .should('be.visible')
                .click()

            // Update app name with empty string.
            cy.get('#name').clear().type(' ')

            // Update app type with empty string.
            cy.get('#type').select('')

            // Update app font icon with empty string.
            cy.get('#icon').clear().type(' ')

            // Update app URL with empty string.
            cy.get('#url').clear().type(' ')

            // Submit the form.
            cy.get('#submit').click()

            // Confirm that submission does not proceed.
            cy.url({ timeout: 10000 }).should(
                'include',
                `admin.php?page=dt_home&tab=app&action=edit%2F${app_slug}`
            )
        })
    })

    // Admin can edit an app with valid data.
    it('Admin attempts to edit an app with valid data.', () => {
        cy.session('admin_attempts_to_edit_an_app_with_valid_data', () => {
            cy.adminAppsSettingsInit()

            cy.get(
                `a[href*="admin.php?page=dt_home&tab=app&action=edit/${shared_data.app_slug}"]`
            )
                .should('be.visible')
                .click()
            // Specify new static data.
            const new_app_name = 'Updated Cypress Test App'
            const new_app_icon = 'mdi mdi-update'
            const new_app_url = 'https://www.updated-cypress.io'

            // Update app name.
            cy.get('#name').clear().type(new_app_name)

            // Update app type.
            cy.get('#type').select(shared_data.app_type)

            // Specify app to be opened within new tab.
            cy.get('#open_in_new_tab').check()

            // Ensure new app is visible.
            cy.get('#is_hidden').uncheck()

            // Update app font icon.
            cy.get('#icon').clear().type(new_app_icon)

            // Update app url.
            cy.get('#url').clear().type(new_app_url)

            // Submit form.
            cy.get('#submit').click()

            // Confirm refreshed apps lists contains updated app.
            cy.contains(new_app_name)
            cy.contains(shared_data.app_slug)
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

    // Confirm updated app is visible within home screen frontend.
    it('Confirm updated app is visible within home screen frontend.', () => {
        cy.session(
            'confirm_updated_app_is_visible_within_home_screen_frontend.',
            () => {
                cy.on('uncaught:exception', (err, runnable) => {
                    // Returning false here prevents Cypress from failing the test
                    return false
                })

                // Fetch home screen magic link and attempt to navigate directly into Home Screen, without a login challenge.
                cy.visit(shared_data['home_screen_ml'])

                // Confirm newly created app is visible within frontend home screen.
                cy.get('dt-home-app-grid')
                    .shadow()
                    .find(`dt-home-app-icon[name*="Updated Cypress Test App"]`)
                    .should('exist')
            }
        )
    })
    // Admin can delete an app.
    it('Admin can delete an app.', () => {
        cy.session('admin_can_delete_an_app', () => {
            cy.adminAppsSettingsInit()
            // Locate the app row and click the delete button.

            cy.contains('tr', shared_data.app_slug, { timeout: 10000 })
                .should('have.lengthOf', 1)
                .find(`.delete-apps`, { timeout: 10000 })
                .click()

            // Confirm delete action.
            cy.on('window:confirm', () => true)

            // Confirm refreshed apps lists does not contain deleted app.
            cy.contains('Updated Cypress Test App').should('not.exist')
            cy.contains(shared_data.app_slug).should('not.exist')
        })
    })
})
