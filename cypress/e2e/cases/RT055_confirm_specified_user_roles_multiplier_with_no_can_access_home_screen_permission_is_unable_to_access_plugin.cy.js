describe('RT055: Confirm specified user roles Multiplier with no can access home screen permission is unable to access plugin.', () => {
    const shared_data = {
        app_name: 'Disciple.Tools',
        app_slug: 'disciple-tools',
        creation_type: 'code',
        app_type: 'Link',
        app_url: 'https://www.cypress.io',
    }
    const user_data = {
        username: 'CypressTestUser',
        email: 'cypresstest@gmail.com',
        password: 'cypress_test_123',
    }
    before(() => {
        cy.npmHomeScreenInit()
        cy.createTestUser(user_data)
    })
    // Administrator visits the Home Screen plugin General tab and confirms Restricted access for some users option is enabled.
    it('Administrator visits the Home Screen plugin General tab and confirms Restricted access for some users option is enabled.', () => {
        cy.session(
            'administrator_visits_the_home_screen_plugin_general_tab_and_confirms_restricted_access_for_some_users_option_is_enabled',
            () => {
                // login as an administrator and go to the Home Screen plugin General tab.
                cy.adminGeneralSettingsInit()

                // click the Restricted access for some users checkbox.
                cy.get('#dt_home_use_capabilities').check()

                // save the changes.
                cy.get('#ml_email_main_col_update_but').click()
            }
        )
    })

    // Administrator navigates to D.T Roles Settings and confirms Can Access Home Screen permission is disabled for Multiplier roles.
    it('Administrator navigates to D.T Roles Settings and confirms Can Access Home Screen permission is disabled for Multiplier roles.', () => {
        cy.session(
            'administrator_navigates_to_d.t_roles_settings_and_confirms_access_to_coded_app_permission_is_selected',
            () => {
                // login as an administrator and go to the Roles setting.
                cy.adminRolesSettingsInit()

                // Locate and click edit option for Multiplier role.
                cy.get('a[title="View capabilities for Multiplier"]')
                    .should('be.visible')
                    .click()

                // confirm access to coded app permission is selected.
                cy.get('#source-filter').type('Home Screen')

                cy.get(
                    'input[type="checkbox"][name="capabilities[]"][value="can_access_home_screen"]'
                ).uncheck({ force: true })

                cy.get(
                    'button[type="submit"].button.button-primary.button-large[title=" Create New Role"]'
                ).click()
            }
        )
    })

    // The administrator goes to the Users page, locates the new test user  with the Multiplier role, and sets the password.
    it('The administrator goes to the Users page, locates the new test user  with the Multiplier role, and sets the password.', () => {
        cy.session(
            'the_administrator_goes_to_the_users_page_locates_the_new_test_user_with_the_multiplier_role_and_sets_the_password',
            () => {
                // login as an administrator and go to the Users section.
                cy.adminUsersSettingsInit()

                // Locate and click edit option for Multiplier user.
                cy.get('a[href*="user-edit.php"]')
                    .contains('cypresstest@gmail.com')
                    .should('be.visible')
                    .click()
                // uncheck the default role
                cy.get('input[name="dt_multi_role_user_roles[]"]').uncheck()

                cy.get(
                    'input[name="dt_multi_role_user_roles[]"][value="multiplier"]'
                ).check()
                // verify that the user is a Multiplier
                cy.get(
                    'input[name="dt_multi_role_user_roles[]"][value="multiplier"]'
                ).should('be.checked')

                // set the password for the user
                cy.get('button.wp-generate-pw').click()

                // type the password
                cy.get('#pass1').clear().type(user_data.password)

                // update the user
                // click the update user button
                cy.get('input#submit.button.button-primary').click()
            }
        )
    })

    // Log in to the home screen and Confirm user encounters a 404 Plugin Not Found Exception.
    it('Log in to the home screen and Confirm user encounters a 404 Plugin Not Found Exception.', () => {
        cy.session(
            'log_in_to_the_home_screen_and_confirm_user_encounters_a_404_plugin_not_found_exception',
            () => {
                cy.on('uncaught:exception', (err, runnable) => {
                    // Returning false here prevents Cypress from failing the test
                    return false
                })

                cy.visit('/apps/login')
                cy.get('dt-text')
                    .shadow()
                    .find(`input[name*="username"]`)
                    .type(user_data.email)
                cy.get('dt-text')
                    .shadow()
                    .find(`input[name*="password"]`)
                    .type(user_data.password)

                cy.get('sp-button.login-sp-button-radius').click()

                cy.get('body')
                    .contains('Plugin not found')
                    .should('be.visible')
                    .should('exist')
            }
        )
    })
})
