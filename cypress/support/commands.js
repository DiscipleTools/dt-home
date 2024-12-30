// ***********************************************
// This example commands.js shows you how to
// create various custom commands and overwrite
// existing commands.
//
// For more comprehensive examples of custom
// commands please read more here:
// https://on.cypress.io/custom-commands
// ***********************************************
//
//
// -- This is a parent command --
// Cypress.Commands.add('login', (email, password) => { ... })
//
//
// -- This is a child command --
// Cypress.Commands.add('drag', { prevSubject: 'element'}, (subject, options) => { ... })
//
//
// -- This is a dual command --
// Cypress.Commands.add('dismiss', { prevSubject: 'optional'}, (subject, options) => { ... })
//
//
// -- This will overwrite an existing command --
// Cypress.Commands.overwrite('visit', (originalFn, url, options) => { ... })

// -- Initialization NPM Home Screen Plugin Environment -- //
Cypress.Commands.add('npmHomeScreenInit', () => {
    cy.exec('composer install')
    cy.exec('composer update')
    cy.exec('npm install')
    cy.exec('npm run build')
    cy.exec(
        'cp -R ./node_modules/@disciple.tools/web-components/dist/generated ./dist/assets/'
    )
    cy.exec(
        'cp -R ./node_modules/@disciple.tools/web-components/dist/lit-localize-*.js ./dist/assets/'
    )
})

// -- Administration Login -- //
Cypress.Commands.add('loginAdmin', (username, password) => {
    // Navigate to WP Admin login page.
    cy.visit('/wp-admin')

    // Specify credentials and submit.
    cy.get('#user_login').invoke('attr', 'value', username)
    cy.get('#user_pass').invoke('attr', 'value', password)
    cy.get('#wp-submit').click()
})

// -- Frontend D.T Login -- //
Cypress.Commands.add('loginDT', (username, password) => {
    // Navigate to DT frontend login page.
    cy.visit('/wp-login.php')

    // Specify credentials and submit.
    cy.get('#user_login').type(username)
    cy.get('#user_pass').type(password)
    cy.get('#wp-submit').click()
})

// -- Frontend Home Screen Login -- //
Cypress.Commands.add('loginHomeScreen', (username, password) => {
    // Handle uncaught exceptions
    cy.on('uncaught:exception', (err, runnable) => {
        // Returning false here prevents Cypress from failing the test
        return false
    })

    // Navigate to Home Screen login page.
    cy.visit('/apps/login')

    // Extract nonce value, to be included within post request.
    cy.get('input[name="_wpnonce"]')
        .invoke('attr', 'value')
        .then((nonce) => {
            // Create headers, also including nonce value.
            const headers = {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                'X-WP-Nonce': nonce,
            }

            // Submit post request to backend login controller.
            cy.request({
                method: 'POST',
                url: '/apps/login',
                form: false,
                headers: headers,
                body: {
                    username: username,
                    password: password,
                    //_wpnonce: nonce // DO NOT SPECIFY NONCE HERE, OR ENTIRE BODY WILL BE DELETED WITHIN BACKEND!
                },
            }).then((response) => {
                // Ensure we have an OK (200) response and force page refresh.
                expect(response.status).to.eq(200)
                cy.reload(true)
            })
        })
})

// -- Admin General Settings Initialization -- //
Cypress.Commands.add('adminGeneralSettingsInit', () => {
    const general_tab_url_path = '/wp-admin/admin.php?page=dt_home&tab=general'

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

    // Login to WP Admin area.
    cy.loginAdmin(username, password)

    // Access Home Screen plugin area on the general tab.
    cy.visit(general_tab_url_path)
})

// -- Admin Apps Settings Initialization -- //
Cypress.Commands.add('adminAppsSettingsInit', () => {
    const app_tab_url_path = '/wp-admin/admin.php?page=dt_home&tab=app'

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

    // Login to WP Admin area.
    cy.loginAdmin(username, password)

    // Access Home Screen plugin area on the apps tab.
    cy.visit(app_tab_url_path)
})

// -- Admin Training Settings Initialization -- //
Cypress.Commands.add('adminTrainingSettingsInit', () => {
    const training_tab_url_path =
        '/wp-admin/admin.php?page=dt_home&tab=training'

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

    // Login to WP Admin area.
    cy.loginAdmin(username, password)

    // Access Home Screen plugin area on the apps tab.
    cy.visit(training_tab_url_path)
})

// -- Frontend D.T Login & Fetch User Settings Home Screen Plugin Magic Link -- //
Cypress.Commands.add(
    'fetchDTUserHomeScreenML',
    (username, password, logout = true) => {
        // Login to D.T frontend.
        cy.loginDT(username, password)

        // Navigate to frontend user settings view.
        cy.visit('/settings')

        // Ensure D.T Home Screen app is activated.
        cy.get('input#app_state_apps_launcher_magic_key').as(
            'home_screen_ml_state_checkbox'
        )
        cy.get('@home_screen_ml_state_checkbox').then(($checkbox) => {
            // Trigger a click if currently in an unchecked state.
            if ($checkbox.prop('checked') === false) {
                cy.get('@home_screen_ml_state_checkbox').click()
            }

            // Identify the corresponding app-link action button and extract magic link.
            cy.get(`a[href*="/apps/launcher/"]`).then(($app_link) => {
                // Persist identified home screen magic link, for use further down stream.
                const home_screen_ml = $app_link.attr('href')
                cy.wrap(home_screen_ml).as('home_screen_ml')

                // If required, force a user logout, based on the first link encountered; as there are typically multiple options on a given page.
                if (logout) {
                    cy.get(
                        `a[href*="/wp-login.php?action=logout&"]:first`
                    ).click({ force: true })
                }
            })
        })
    }
)
// Reset frontend apps
Cypress.Commands.add('resetFrontendApps', () => {
    // click the + button to display the popup model
    cy.get('dt-home-footer')
        .shadow()
        .find('sp-button.trigger-button[slot="trigger"]')
        .click()

    // Click the reset button to restore the apps to their default state
    cy.get('dt-home-footer')
        .shadow()
        .find('sp-action-button[slot="buttons"][label="Reset"]')
        .click()

    // Click ok for displayed confirmation.
    cy.on('window:confirm', () => true)
})

// -- Admin Roles Settings Initialization -- //
Cypress.Commands.add('adminRolesSettingsInit', () => {
    const app_tab_url_path = '/wp-admin/admin.php?page=dt_options&tab=roles'

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

    // Login to WP Admin area.
    cy.loginAdmin(username, password)

    // Access Home Screen plugin area on the apps tab.
    cy.visit(app_tab_url_path)
})

// -- admin Users Settings Init -- //
Cypress.Commands.add('adminUsersSettingsInit', () => {
    const app_tab_url_path = '/wp-admin/users.php'

    /**
     * Ensure uncaught exceptions do not fail test run; however, any thrown
     * exceptions must not be ignored and a ticket must be raised, in order
     * to resolve identified exception.
     *
     * TODO:
     * - Resolve any identified exceptions.
     */
    cy.on('uncaught:exception', (err, runnable) => {
        // Returning false here prevents Cypress from failing the test
        return false
    })

    // Capture admin credentials.
    const dt_config = cy.config('dt')
    const username = dt_config.credentials.admin.username
    const password = dt_config.credentials.admin.password

    // Login to WP Admin area.
    cy.loginAdmin(username, password)

    // Access Home Screen plugin area on the apps tab.
    cy.visit(app_tab_url_path)
})

Cypress.Commands.add('createTestUser', (user_data) => {
    cy.session('create_test_user', () => {
        // login as an administrator and go to the Users section.
        cy.adminUsersSettingsInit()

        cy.get('#the-list').then(($list) => {
            if ($list.find(`a:contains(${user_data.email})`).length > 0) {
                // User exists
                cy.log('User exists')
            } else {
                // User does not exist
                cy.get('a.page-title-action').click()

                // Fill in the new user details.
                cy.get('#eman').type(user_data.username)
                cy.get('#liame').type(user_data.email)
                cy.get('#create-user').click()
            }
        })
    })
})

// -- Admin delete user Initialization -- //
Cypress.Commands.add('deleteTestUser', (user_email) => {
    const app_tab_url_path = '/wp-admin/users.php'

    /**
   * Ensure uncaught exceptions do not fail test run; however, any thrown
   * exceptions must not be ignored and a ticket must be raised, in order
   * to resolve identified exception.
   *

   */
    cy.on('uncaught:exception', (err, runnable) => {
        // Returning false here prevents Cypress from failing the test
        return false
    })

    // Capture admin credentials.
    const dt_config = cy.config('dt')
    const username = dt_config.credentials.admin.username
    const password = dt_config.credentials.admin.password

    // Login to WP Admin area.
    cy.loginAdmin(username, password)

    // Access Home Screen plugin area on the apps tab.
    cy.visit(app_tab_url_path)

    cy.get('tr')
        .contains('td', user_email)
        .parent()
        .find('.delete a')
        .click({ force: true })
    //confirm delete user
    cy.get('input#submit.button.button-primary').click({ force: true })
})
