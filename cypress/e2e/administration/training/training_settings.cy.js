describe('Admin Home Screen Training Settings Test Cases', () => {
    // Successfully login and access home screen training tab.
    it('Successfully login and access home screen training tab.', () => {
        cy.session('training_settings', () => {
            cy.adminTrainingSettingsInit()
        })
    })

    // Can add a new training video.
    it('Can add a new training video.', () => {
        cy.session('can_add_a_new_training_video.', () => {
            cy.adminTrainingSettingsInit()

            // Click add training button and navigate to create training video view.
            cy.get(
                'a[href*="admin.php?page=dt_home&tab=training&action=create"]'
            ).click()

            // Specify training video name.
            const video_name = 'Cypress Test Video'
            cy.get('#name').invoke('attr', 'value', video_name)

            // Specify embedded video code.
            const video_embed_code =
                '<iframe width="560" height="315" src="https://www.youtube.com/embed/EuCyqloPH0I?si=_VGXzezr2Esqdvpq" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>'
            cy.get('#embed_video').type(video_embed_code)

            // Specify video anchor.
            const video_anchor = 'cypress_test_video'
            cy.get('#anchor').invoke('attr', 'value', video_anchor)

            // Specify training video sort order.
            cy.get('#sort').invoke('attr', 'value', 0)

            // Keep remaining defaults and submit form.
            cy.get('#ml_email_main_col_update_but').click()

            // Confirm refreshed training video list contains created video.
            cy.contains(video_name)
            cy.contains(video_anchor)
        })
    })

    // Can edit training video.
    it('Can edit training video.', () => {
        cy.session('can_edit_training_video', () => {
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

    // Can sort video up and down.
    it('Can sort video up and down.', () => {
        cy.session('can_sort_video_up_and_down', () => {
            cy.adminTrainingSettingsInit()

            // Locate and click up option for previously created video.
            const video_anchor = 'cypress_test_video'
            cy.contains('tr', video_anchor)
                .should('have.lengthOf', 1)
                .find(
                    `a[href*="admin.php?page=dt_home&tab=training&action=up/"]`
                )
                .click()

            // Following page refresh, click on down, to revert back to previous sort position.
            cy.contains('tr', video_anchor)
                .should('have.lengthOf', 1)
                .find(
                    `a[href*="admin.php?page=dt_home&tab=training&action=down/"]`
                )
                .click()
        })
    })

    // Can delete video.
    it('Can delete video.', () => {
        cy.session('can_delete_video', () => {
            cy.adminTrainingSettingsInit()

            // Locate and click delete option for previously created video.
            const video_name = 'Cypress Test Video [UPDATED]'

            cy.contains('tr', video_name, { timeout: 10000 })
                .should('have.lengthOf', 1)
                .find('i.fas.fa-trash.action-icon', {
                    timeout: 10000,
                })
                .click()
            // Click ok for displayed confirmation.
            cy.on('window:confirm', () => true)

            // Wait for a short period to ensure the deletion process completes.
            cy.wait(5000)

            // Refresh the page to ensure the DOM is updated.
            cy.reload()

            // Following page refresh, confirm video has been deleted.
            cy.contains('Cypress Test Video [UPDATED]').should('not.exist')
            cy.contains(video_name, { timeout: 20000 }).should('not.exist')
        })
    })
})
