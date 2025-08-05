describe('Admin Home Screen Apps Settings Test Cases', () => {
    // Successfully login and access home screen apps tab.
    it('Successfully login and access home screen apps tab.', () => {
        cy.session('apps_settings', () => {
            cy.adminAppsSettingsInit()
        })
    })

    // Can add a new app.
    it('Can add a new app.', () => {
        cy.session('can_add_a_new_app', () => {
            cy.adminAppsSettingsInit()

            // Click add app button and navigate to create app view.
            cy.get('a[href*="admin.php?page=dt_home&tab=app&action=create"]', {
                timeout: 10000,
            }).click()

            // Specify app name.
            const app_name = 'Cypress Test App'
            cy.get('#name').invoke('attr', 'value', app_name)

            // Specify app slug.
            const app_slug = 'cypress_test_app'
            cy.get('#slug').invoke('attr', 'value', app_slug)

            // Specify app type.
            const app_type = 'Link'
            cy.get('#type').select(app_type)

            // Specify app to be opened within new tab.
            cy.get('#open_in_new_tab').check()

            // Specify app font icon.
            const app_icon = 'mdi mdi-ab-testing'
            cy.get('#icon').invoke('attr', 'value', app_icon)

            // Specify app url.
            const app_url = 'https://www.cypress.io'
            cy.get('#url').invoke('attr', 'value', app_url)

            // Keep defaults (visibility, exportability, roles, etc...) and submit form.
            cy.get('#ml_email_main_col_update_but').click()

            // Confirm refreshed apps lists contains created app.
            cy.contains(app_name)
            cy.contains(app_slug)
        })
    })

    // Can edit an app.
    it('Can edit an app.', () => {
        cy.session('can_edit_an_app', () => {
            cy.adminAppsSettingsInit()

            // Locate and click edit option for previously created app.
            const app_slug = 'cypress_test_app'
            cy.get(
                `a[href*="admin.php?page=dt_home&tab=app&action=edit/${app_slug}"]`
            )
                .should('be.visible')
                .click()

            // Update the app's name.
            const app_name = 'Cypress Test App [UPDATED]'
            cy.get('#name').invoke('attr', 'value', app_name)

            // Keep remaining fields untouched and submit form.
            cy.get('#submit').click()

            // Confirm refreshed apps lists contains updated app.
            cy.contains(app_name)
        })
    })

    // Can hide and unhide an app.
    it('Can hide and unhide an app.', () => {
        cy.session('can_hide_and_unhide_an_app', () => {
            cy.adminAppsSettingsInit()

            // Locate and click hide option for previously created app.
            const app_slug = 'cypress_test_app'
            cy.get(
                `a[href*="admin.php?page=dt_home&tab=app&action=hide/${app_slug}"]`
            ).click()

            // Following page refresh, click on unhide, to revert back to a displayed state.
            cy.get(
                `a[href*="admin.php?page=dt_home&tab=app&action=unhide/${app_slug}"]`
            ).click()
        })
    })

    // Can sort app up and down.
    it('Can sort app up and down.', () => {
        cy.session('can_sort_app_up_and_down', () => {
            cy.adminAppsSettingsInit()

            // Locate and click up option for previously created app.
            const app_slug = 'cypress_test_app'
            cy.get(
                `a[href*="admin.php?page=dt_home&tab=app&action=up/${app_slug}"]`
            ).click()

            // Following page refresh, click on down, to revert back to previous sort position.
            cy.get(
                `a[href*="admin.php?page=dt_home&tab=app&action=down/${app_slug}"]`
            ).click()
        })
    })

    // Can delete app.
    it('Can delete app.', () => {
        cy.session('can_delete_app', () => {
            cy.adminAppsSettingsInit()

            // Locate and click delete option for previously created app.
            const app_slug = 'cypress_test_app'
            cy.get(`a[onclick*="deleteApp('${app_slug}')"]`).click()

            // Click ok for displayed confirmation.
            cy.on('window:confirm', () => true)

            // Following page refresh, confirm app has been deleted.
            cy.contains(app_slug).should('not.exist')
        })
    })
})
