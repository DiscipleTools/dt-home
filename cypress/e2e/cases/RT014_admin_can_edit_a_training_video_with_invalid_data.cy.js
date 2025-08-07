describe('RT014 - Administrator attempts to edit a training video with invalid data.', () => {
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

    // Admin can add a new training video.
    it('Admin can add a new training video.', () => {
        cy.session('admin_can_add_a_new_training_video', () => {
            cy.adminTrainingSettingsInit()

            // Click add training video button and navigate to create video view.
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

            // Specify sort order.
            const sort = shared_data.sort
            cy.get('#sort').invoke('attr', 'value', sort)

            cy.get('#ml_email_main_col_update_but').click()

            // Confirm refreshed training videos list contains created training video.
            cy.contains(shared_data.video_name)
            cy.contains(shared_data.anchor)
        })
    })

    // Admin attempts to edit a training video with invalid data.
    it('Admin attempts to edit a training video with invalid data.', () => {
        cy.session(
            'admin_attempts_to_edit_a_training_video_with_invalid_data',
            () => {
                cy.adminTrainingSettingsInit()

                const video_anchor = 'cypress_test_video'
                cy.contains('tr', video_anchor)
                    .should('have.lengthOf', 1)
                    .find(
                        `a[href*="admin.php?page=dt_home&tab=training&action=edit/"]`
                    )
                    .click()

                // Capture the ID of the newly added training video.
                cy.url().then((url) => {
                    const id = url.split('action=edit/')[1]

                    // Update training video name with empty string.
                    cy.get('#name').clear().type(' ')

                    // Update embed video with empty string.
                    cy.get('#embed_video').clear()

                    // Update anchor with empty string.
                    cy.get('#anchor').clear().type(' ')

                    // Update sort order with empty string.
                    cy.get('#sort').clear().type(' ')

                    // Submit the form.
                    cy.get('#submit').click()
                })
            }
        )
    })

    // Admin can edit a training video with valid data.
    it('Admin attempts to edit a training video with valid data.', () => {
        cy.session(
            'admin_attempts_to_edit_a_training_video_with_valid_data',
            () => {
                cy.adminTrainingSettingsInit()

                const video_anchor = 'cypress_test_video'
                cy.contains('tr', video_anchor)
                    .should('have.lengthOf', 1)
                    .find(
                        `a[href*="admin.php?page=dt_home&tab=training&action=edit/"]`
                    )
                    .click()
                // Specify new static data.
                const new_video_name = 'Updated Cypress Test Video'
                const new_embed_video =
                    '<iframe width="560" height="315" src="https://www.youtube.com/embed/updated_video" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>'
                const new_anchor = 'updated_cypress_test_video'
                const new_sort = '2'

                // Update video name.
                cy.get('#name').clear().type(new_video_name)

                // Update embed video.
                cy.get('#embed_video').clear().type(new_embed_video)

                // Update anchor.
                cy.get('#anchor').clear().type(new_anchor)

                // Update sort order.
                cy.get('#sort').clear().type(new_sort)

                // Submit form.
                cy.get('#submit').click()

                // Confirm refreshed training videos list contains updated training video.
                cy.contains(new_video_name)
                cy.contains(new_anchor)
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

    // Admin can delete a training video.
    it('Admin can delete a training video.', () => {
        cy.session('admin_can_delete_a_training_video', () => {
            cy.adminTrainingSettingsInit()
            // Locate the training video row and click the delete button.

            cy.contains('tr', shared_data.anchor)
                .should('have.lengthOf', 1)
                .find(`a[onclick*="confirmDelete("]`)
                .click()
            // Confirm delete action.
            cy.on('window:confirm', () => true)

            // Confirm refreshed training videos list does not contain deleted training video.
            cy.contains(shared_data.video_name).should('not.exist')
        })
    })
})
