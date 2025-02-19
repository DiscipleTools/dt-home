describe('RT013 - Administrator can edit an Training video with valid data.', () => {
    const seed = Math.floor(Math.random() * 100)

    let shared_data = {
        video_name: `Cypress Test Video [${seed}]`,
        embed_video: `<iframe width="560" height="315" src="https://www.youtube.com/embed/9xwazD5SyVg?si=gtjVeUnaciS_RVFV" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>_${seed}`,
        anchor: 'cypress_test_video',
        sort: '1',
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

    // Admin can add a new training video.
    it('Admin can add a new training video.', () => {
        cy.session('admin_can_add_a_new_training_video', () => {
            cy.adminTrainingSettingsInit()

            // Click add app button and navigate to create video view.
            cy.get(
                'a[href*="admin.php?page=dt_home&tab=training&action=create"]'
            ).click()

            // Specify video name.
            const video_name = shared_data.video_name
            cy.get('#name').invoke('attr', 'value', video_name)

            // Specify embed video.
            const embed_video = shared_data.embed_video
            cy.get('#embed_video').clear().type(embed_video)

            // Specify anchor.
            const anchor = shared_data.anchor
            cy.get('#anchor').invoke('attr', 'value', anchor)

            // Specify app url.
            const sort = shared_data.sort
            cy.get('#sort').invoke('attr', 'value', sort)

            cy.get('#ml_email_main_col_update_but').click()

            // Confirm refreshed apps lists contains created app.
            cy.contains(shared_data.video_name)
            cy.contains(shared_data.anchor)
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
    it('Confirm new training video is visible within home screen frontend.', () => {
        cy.session(
            'confirm_new_training_video_is_visible_within_home_screen_frontend.',
            () => {
                cy.on('uncaught:exception', (err, runnable) => {
                    // Returning false here prevents Cypress from failing the test
                    return false
                })
                // Fetch home screen magic link and attempt to navigate directly into Home Screen, without a login challenge.
                cy.visit(shared_data['home_screen_ml'] + '/training')

                // Confirm newly created video is visible within frontend home screen.
                cy.get('dt-home-video-list')
                    .shadow()
                    .find(`div[id*="${shared_data.anchor}"]`)
            }
        )
    })

    // Can edit training video.
    it('Admin can edit an training video.', () => {
        cy.session('admin_can_edit_an_training_video', () => {
            cy.adminTrainingSettingsInit()

            // Locate and click edit option for previously created video.
            const video_anchor = 'cypress_test_video'
            cy.contains('tr', video_anchor)
                .should('have.lengthOf', 1)
                .find(
                    `a[href*="admin.php?page=dt_home&tab=training&action=edit/"]`
                )
                .click()

            // Update the video's name.
            const video_name = 'Cypress Test Video [UPDATED]'
            cy.get('#name').invoke('attr', 'value', video_name)

            // Keep remaining fields untouched and submit form.
            cy.get('#submit').click()

            // Confirm refreshed training video list contains updated video.
            cy.contains(video_name)
        })
    })

    // Confirm updated training video is visible within training videos frontend.
    it('Confirm updated training video is visible within training video frontend.', () => {
        cy.session(
            'confirm_updated_training_video_is_visible_within_training_video_frontend.',
            () => {
                cy.on('uncaught:exception', (err, runnable) => {
                    // Returning false here prevents Cypress from failing the test
                    return false
                })
                // Fetch home screen magic link and attempt to navigate directly into Home Screen, without a login challenge.
                cy.visit(shared_data['home_screen_ml'] + '/training')

                // Confirm newly created video is visible within frontend home screen.
                cy.get('dt-home-video-list')
                    .shadow()
                    .find(`div[id*="${shared_data.anchor}"]`)
            }
        )
    })

    // Admin can delete training video.
    it('Can delete video.', () => {
        cy.session('can_delete_video', () => {
            cy.adminTrainingSettingsInit()
            cy.on('uncaught:exception', (err, runnable) => {
                // Returning false here prevents Cypress from failing the test
                return false
            })
            // Locate and click delete option for previously created video.
            cy.contains('tr', shared_data.video_name)
                .should('have.lengthOf', 1)
                .find(`a[onclick*="confirmDelete("]`)
                .click()

            // Click ok for displayed confirmation.
            cy.on('window:confirm', () => true)

            // Following page refresh, confirm video has been deleted.
            cy.contains(shared_data.video_name).should('not.exist')
        })
    })
})
