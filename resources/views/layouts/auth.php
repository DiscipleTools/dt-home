<sp-theme scale="medium"
          color="light"
          id="main-theme"
>
    <div class="plugin cloak">
        <div class="plugin__main">
            <div class="container">
                <div class="auth-header">
                    <dt-home-theme-toggle></dt-home-theme-toggle>
                </div>
                <div>
					<?php
                    // phpcs:ignore
                    echo $this->section( 'content' ) ?>
                </div>
            </div>
        </div>
    </div>
</sp-theme>
