# DT Home Plugin Hooks Documentation

This document lists all the action and filter hooks available in the DT Home plugin, providing developers with the ability to extend and customize the plugin's functionality.

## Action Hooks

### `dt_home_app_render`

**Description:** Triggered when an app is being rendered, allowing developers to perform custom rendering logic for their apps.

**Location:** `src/Controllers/MagicLink/AppController.php`

**Parameters:**
- `$app` (array) - The app configuration array

**Usage Example:**
```php
add_action( 'dt_home_app_render', function( $app ) {
    if ( $app['slug'] === 'my-custom-app' ) {
        // Custom rendering logic here
        echo '<div>Custom app content</div>';
    }
}, 10, 1 );
```

## Filter Hooks

### `dt_home_apps`

**Description:** Filters the array of registered apps, allowing developers to add, modify, or remove apps from the home screen.

**Location:** `src/Services/Apps.php`

**Parameters:**
- `$apps` (array) - Array of app configurations

**Usage Example:**
```php
add_filter( 'dt_home_apps', function( $apps ) {
    $apps[] = [
        'name' => 'My Custom App',
        'slug' => 'my-custom-app',
        'type' => 'Web View',
        'icon' => '/path/to/icon.svg',
        'url' => 'https://example.com',
        'sort' => 10,
        'is_hidden' => false
    ];
    return $apps;
}, 10, 1 );
```

### `dt_home_app_template`

**Description:** Filters the template used for rendering an app, allowing developers to provide custom templates.

**Location:** `src/Controllers/MagicLink/AppController.php`

**Parameters:**
- `$template` (string) - The current template string
- `$app` (array) - The app configuration array

**Usage Example:**
```php
add_filter( 'dt_home_app_template', function( $template, $app ) {
    if ( $app['slug'] === 'my-custom-app' ) {
        return '<div class="custom-template">Custom content for ' . $app['name'] . '</div>';
    }
    return $template;
}, 10, 2 );
```

### `dt_home_webview_url`

**Description:** Filters the URL used for webview apps, allowing developers to modify or customize app URLs.

**Location:** `src/Controllers/MagicLink/AppController.php`

**Parameters:**
- `$url` (string) - The current URL
- `$app` (array) - The app configuration array

**Usage Example:**
```php
add_filter( 'dt_home_webview_url', function( $url, $app ) {
    if ( $app['slug'] === 'my-custom-app' ) {
        return $url . '?custom_param=value';
    }
    return $url;
}, 10, 2 );
```

## Namespaced Filter Hooks

The following hooks use the plugin's namespace (`dt-home.`) and are accessed via the `namespace_string()` helper function:

### `dt-home.settings_tabs`

**Description:** Filters the tabs displayed in the plugin settings page.

**Location:** `resources/views/layouts/settings.php`, `src/Services/Settings.php`

**Parameters:**
- `$tabs` (array) - Array of tab configurations

**Usage Example:**
```php
add_filter( \DT\Home\namespace_string( 'settings_tabs' ), function( $tabs ) {
    $tabs[] = [
        'label' => __( 'Custom Tab', 'dt-home' ),
        'tab' => 'custom'
    ];
    return $tabs;
}, 10, 1 );
```

### `dt-home.response_renderer`

**Description:** Filters the response renderer used for handling template responses.

**Location:** `src/MagicLinks/MagicLink.php`, `src/Providers/RouteServiceProvider.php`, `src/Services/Template.php`

**Parameters:**
- `$renderer` (mixed) - The current renderer (usually false by default)

**Usage Example:**
```php
add_filter( \DT\Home\namespace_string( 'response_renderer' ), function( $renderer ) {
    return new CustomResponseRenderer();
}, 10, 1 );
```

### `dt-home.magic_links`

**Description:** Filters the array of registered magic links for the plugin.

**Location:** `src/Providers/MagicLinkServiceProvider.php`

**Parameters:**
- `$magic_links` (array) - Array of magic link class names

**Usage Example:**
```php
add_filter( \DT\Home\namespace_string( 'magic_links' ), function( $magic_links ) {
    $magic_links[] = 'MyCustomMagicLink';
    return $magic_links;
}, 10, 1 );
```

### `dt-home.allowed_scripts`

**Description:** Filters the array of JavaScript handles allowed to load in magic link contexts.

**Location:** `src/Services/Assets.php`, `src/Providers/AssetServiceProvider.php`, `src/Apps/BiblePlugin.php`

**Parameters:**
- `$scripts` (array) - Array of script handles

**Usage Example:**
```php
add_filter( \DT\Home\namespace_string( 'allowed_scripts' ), function( $scripts ) {
    $scripts[] = 'my-custom-script';
    return $scripts;
}, 10, 1 );
```

### `dt-home.allowed_styles`

**Description:** Filters the array of CSS handles allowed to load in magic link contexts.

**Location:** `src/Services/Assets.php`, `src/Providers/AssetServiceProvider.php`, `src/Apps/BiblePlugin.php`

**Parameters:**
- `$styles` (array) - Array of style handles

**Usage Example:**
```php
add_filter( \DT\Home\namespace_string( 'allowed_styles' ), function( $styles ) {
    $styles[] = 'my-custom-styles';
    return $styles;
}, 10, 1 );
```

### `dt-home.javascript_globals`

**Description:** Filters the JavaScript global variables made available to frontend scripts.

**Location:** `src/Services/Assets.php`, `src/Providers/AssetServiceProvider.php`

**Parameters:**
- `$globals` (array) - Array of global variables

**Usage Example:**
```php
add_filter( \DT\Home\namespace_string( 'javascript_globals' ), function( $globals ) {
    $globals['custom_data'] = [
        'api_url' => home_url( '/wp-json/my-plugin/v1/' ),
        'user_id' => get_current_user_id()
    ];
    return $globals;
}, 10, 1 );
```

## External Filter Hooks Used

The plugin also utilizes some filter hooks from other systems:

### `dt_magic_url_register_types`

**Description:** From the Disciple Tools core, used to register magic URL types.

**Location:** `src/Services/MagicApps.php`, `src/helpers.php`

**Usage:** The plugin filters this to access available magic link apps.

### `login_errors`

**Description:** WordPress core hook for filtering login errors.

**Location:** `src/Controllers/LoginController.php`

**Usage:** Used to filter and display login errors in custom login forms.

## Hook Usage Patterns

### Creating Custom Apps

To create a custom app that integrates with the DT Home plugin:

```php
class MyCustomApp extends \DT\Home\Apps\App {
    public function config(): array {
        return [
            'name' => 'My Custom App',
            'slug' => 'my-custom-app',
            'type' => 'Web View',
            'icon' => '/path/to/icon.svg',
            'url' => 'https://example.com',
            'sort' => 10,
            'is_hidden' => false
        ];
    }

    public function authorized(): bool {
        return current_user_can( 'read' );
    }

    public function render() {
        echo '<div>Custom app rendering</div>';
    }

    public function template() {
        return '<div class="custom-template">Custom template content</div>';
    }

    public function url( $url, $app ) {
        return $url . '?custom_param=value';
    }
}

// Register the app
new MyCustomApp();
```

### Customizing Assets

To add custom scripts and styles that work with magic links:

```php
// Enqueue your assets
wp_enqueue_script( 'my-custom-script', plugin_dir_url( __FILE__ ) . 'script.js', [], '1.0', true );
wp_enqueue_style( 'my-custom-style', plugin_dir_url( __FILE__ ) . 'style.css', [], '1.0' );

// Allow them in magic link contexts
add_filter( \DT\Home\namespace_string( 'allowed_scripts' ), function( $scripts ) {
    $scripts[] = 'my-custom-script';
    return $scripts;
}, 10, 1 );

add_filter( \DT\Home\namespace_string( 'allowed_styles' ), function( $styles ) {
    $styles[] = 'my-custom-style';
    return $styles;
}, 10, 1 );
```

## Notes

- All namespaced hooks should be accessed using the `\DT\Home\namespace_string()` helper function
- When working with magic links, ensure your scripts and styles are added to the allowed lists
- The plugin follows WordPress coding standards for hook naming and implementation
- Some hooks may require specific capabilities or authorization checks
