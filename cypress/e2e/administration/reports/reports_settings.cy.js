describe('Admin Home Screen Reports Settings Test Cases', () => {

  const reports_test_config = {
    'users': {
      'label': 'Users',
      'tab': 'analytics_reports_tab_users',
      'metrics': [
        {
          'metric': 'users-auth',
          'label': 'Authentication',
          'chart_type': 'pie',
          'filters': [
            'date-range'
          ]
        },
        {
          'metric': 'users-failures',
          'label': 'Failures',
          'chart_type': 'count',
          'filters': [
            'date-range'
          ]
        }
      ]
    },
    'apps': {
      'label': 'Apps',
      'tab': 'analytics_reports_tab_apps',
      'metrics': [
        {
          'metric': 'apps-totals',
          'label': 'Total Counts',
          'chart_type': 'count',
          'filters': []
        },
        {
          'metric': 'apps-creation-types',
          'label': 'Creation Types',
          'chart_type': 'h-bar',
          'filters': [
            'date-range'
          ]
        },
        {
          'metric': 'apps-app-types',
          'label': 'App Types',
          'chart_type': 'v-bar',
          'filters': [
            'date-range'
          ]
        },
        {
          'metric': 'apps-coded-deletes',
          'label': 'Deleted Coded Apps',
          'chart_type': 'count',
          'filters': [
            'date-range'
          ]
        },
        {
          'metric': 'apps-custom-deletes',
          'label': 'Deleted Custom Apps',
          'chart_type': 'count',
          'filters': [
            'date-range'
          ]
        }
      ]
    },
    'training': {
      'label': 'Training Videos',
      'tab': 'analytics_reports_tab_training',
      'metrics': [
        {
          'metric': 'training-totals',
          'label': 'Total Counts',
          'chart_type': 'count',
          'filters': [
            'date-range'
          ]
        },
        {
          'metric': 'training-deletes',
          'label': 'Deleted Videos',
          'chart_type': 'count',
          'filters': [
            'date-range'
          ]
        }
      ]
    },
    'usage': {
      'label': 'Usage',
      'tab': 'analytics_reports_tab_usage',
      'metrics': [
        {
          'metric': 'usage-shares',
          'label': 'Shared Links',
          'chart_type': 'count',
          'filters': [
            'date-range'
          ]
        }
      ]
    }
  }

  // Successfully login and access reports tab.
  it('Successfully login and access reports tab.', () => {
    cy.session('successfully_login_and_access_reports_tab', () => {
      cy.adminReportsSettingsInit();
    })
  })

  // Test local analytics reports.
  it('Test local analytics reports.', () => {
    cy.session('test_local_analytics_reports', () => {
      cy.adminReportsSettingsInit();

      Object.entries(reports_test_config).forEach(([report, config]) => {

        cy.log(`Testing local analytics ${report} reports.`);

        // Confirm analytics section exists.
        cy.contains(config.label);

        // Confirm metric tabs exists.
        config.metrics.forEach((metric) => {
          cy.get(`a[data-metric="${metric.metric}"]`)
          .click()
          .then(() => {

            // Confirm specified filters are correctly displayed.
            metric.filters.forEach((filter) => {
              switch (filter) {
                case 'date-range':
                  cy.get(`#${config.tab}`)
                  .find('#analytics_reports_tab_filters')
                  .find('dt-analytics-reports-filter-date-range');
                  break;
              }
            });

            // Confirm specified chart type is displayed.
            let chart_tag = 'dt-analytics-reports-chart-count';
            switch (metric.chart_type) {
              case 'pie':
                chart_tag = 'dt-analytics-reports-chart-pie';
                break;
              case 'v-bar':
                chart_tag = 'dt-analytics-reports-chart-v-bar';
                break;
              case 'h-bar':
                chart_tag = 'dt-analytics-reports-chart-h-bar';
                break;
            }
            cy.get(`#${config.tab}`)
            .find('#analytics_reports_tab_charts')
            .find(chart_tag);
          });
        });

      });
    })
  })

})
