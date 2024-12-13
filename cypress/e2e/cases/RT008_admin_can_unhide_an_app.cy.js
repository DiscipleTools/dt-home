describe('RT008 - Administrator can unhide an app.', () => {
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
            cy.get('#is_hidden').check()

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

    // Admin can Show the app list page.
    it('Admin can Show the app list page.', () => {
        cy.session('admin_can_show_the_app_list_page', () => {
            cy.adminAppsSettingsInit()
            const app_name = shared_data.app_name
            // Confirm the app list page is displayed.
            cy.get(`a[data-slug="${shared_data.app_slug}"]`, {
                timeout: 10000,
            }).click()
            cy.contains(app_name)

            cy.get(`a[data-slug-edit="${shared_data.app_slug}"]`, {
                timeout: 10000,
            }).click()

            cy.get('#is_hidden').then(($checkbox) => {
                if ($checkbox.is(':checked')) {
                    cy.get('#is_hidden').uncheck()
                } else {
                    cy.get('#is_hidden').check()
                }
            })

            cy.on('uncaught:exception', (err, runnable) => {
                // Handle the exception
            })
        })
    })

    // Login to D.T frontend and obtain home screen plugin magic link.
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
                cy.on('uncaught:exception', (err, runnable) => {
                    // Returning false here prevents Cypress from failing the test
                    return false
                })

                // Fetch home screen magic link and attempt to navigate directly into Home Screen, without a login challenge.
                cy.visit(shared_data['home_screen_ml'])

                // Confirm newly created app is visible within frontend home screen.
                cy.get('dt-home-app-grid')
                    .shadow()
                    .find(`dt-home-app-icon[name*="${shared_data.app_name}"]`)
                    .should('exist')
            }
        )
    })

    // Can delete app.
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
