<?php
/**
 * @var array $apps
 */
use function DT\Home\magic_url;

$filtered_apps = array_filter( $apps, function ( $app ) {
    return $app['is_hidden'] !== true &&
        isset($app['magic_link_meta']);
})
?>
<script type="application/javascript">
    function toggleAppsSelector() {
        var selector = document.querySelector('.launcher-apps-selector');
        selector.classList.toggle('open');
    }
</script>
<div class="launcher-bottom-nav">
    <div class="nav-container">
        <button class="nav-item" onclick="toggleAppsSelector()">
            <i class="mdi mdi-apps"></i>
            <?php echo __( 'Apps', 'dt-home' ) ?>
        </button>
        <a href="<?php echo esc_url( magic_url( '' ) ); ?>" class="nav-item nav-item-home">
            <i class="mdi mdi-home"></i>
        </a>
        <a href="<?php echo esc_url( magic_url( 'logout' ) ); ?>" class="nav-item">
            <i class="mdi mdi-logout"></i>
            <?php echo __( 'Log Out', 'dt-home' ) ?>
        </a>
    </div>
</div>

<div class="launcher-apps-selector">
    <ul>
    <?php foreach ( $filtered_apps as $app ): ?>
        <li>
            <a href="<?php echo esc_url( magic_url( 'app/' . $app['slug'] ) ); ?>">

                <?php if (str_starts_with( $app['icon'], 'http') || str_starts_with( $app['icon'], '/' ) ): ?>
                    <img src="<?php echo esc_url( $app['icon'] ); ?>" alt="<?php echo esc_attr( $app['name'] ) || 'Link'; ?>" />
                <?php else: ?>
                    <i class="<?php echo esc_attr( $app['icon'] ); ?>"
                       aria-hidden="true"
                    ></i>
                <?php endif; ?>
                <span class="name"><?php echo $app['name'] ?></span>
            </a>
            <span class="muted" style="display:none;"><?php print_r($app) ?></span>
        </li>
    <?php endforeach; ?>
    </ul>
</div>
