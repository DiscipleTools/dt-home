<sp-theme id="theme" system="classic" scale="medium">
    <div class="plugin cloak">
        <div class="plugin__main">
            <div class="container">
                <div>
                    <?php
                    // phpcs:ignore
                    echo $this->section('content') ?>
                </div>
            </div>
        </div>
    </div>
</sp-theme>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const themeElement = document.getElementById('theme')
        const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)').matches

        if (prefersDarkScheme) {
            themeElement.setAttribute('color', 'dark')
        } else {
            themeElement.setAttribute('color', 'light')
        }
    })
</script>
