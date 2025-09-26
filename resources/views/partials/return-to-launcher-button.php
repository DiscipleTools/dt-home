<?php
use function DT\Home\route_url;
?>
<style>
    .icon-link {
        position: absolute;
        bottom: 16px;
        left: 16px;
        display: inline-flex; /* or block, depending on your requirements */
        text-decoration: none; /* optional, removes the underline */
        color: inherit; /* optional, inherits the text color */
    }

    .icon-link__inner {
        --spectrum-icon-size: 44px; /* Size of the icon */
        color: var(--return-button-icon-color, #ffffff); /* Color of the icon itself */
        background-color: var(--return-button-bg-color, #326A82); /* Background color of the circular shape */
        border-radius: 50%; /* Circular shape */
        padding: 10px; /* Space between the icon and the background */
        box-shadow: var(--return-button-shadow, 0 4px 10px rgba(0, 0, 0, 0.25)); /* Shadow effect */
        display: flex; /* Use flex to center the icon */
        justify-content: center; /* Center icon horizontally */
        align-items: center; /* Center icon vertically */
        width: var(--spectrum-icon-size); /* Same as --spectrum-icon-size */
        height: var(--spectrum-icon-size); /* Same as --spectrum-icon-size */
        box-sizing: border-box; /* Include padding and border in the width and height */
        transition: background-color 0.3s, transform 0.3s, box-shadow 0.3s; /* Smooth transitions */
        /* Do not include bottom and left properties here */
    }

    .icon-link__inner:hover {
        background-color: var(--return-button-hover-bg-color, #2A5F75); /* Hover background color */
        transform: scale(1.1); /* Hover scale effect */
        box-shadow: var(--return-button-hover-shadow, 0 6px 15px rgba(0, 0, 0, 0.3)); /* Enhanced shadow on hover */
    }

    /* Dark mode styles */
    :host-context(sp-theme[color="dark"]) .icon-link__inner {
        --return-button-icon-color: #ffffff;
        --return-button-bg-color: #4a9eff;
        --return-button-shadow: 0 4px 10px rgba(0, 0, 0, 0.4);
        --return-button-hover-bg-color: #3a8eef;
        --return-button-hover-shadow: 0 6px 15px rgba(0, 0, 0, 0.5);
    }
</style>

<script>
    // Theme detection and application for return button
    function applyReturnButtonTheme() {
        const themeElement = document.querySelector('sp-theme');
        const isDark = themeElement && themeElement.color === 'dark';
        const iconLink = document.querySelector('.icon-link__inner');
        
        if (iconLink) {
            if (isDark) {
                iconLink.style.setProperty('--return-button-icon-color', '#ffffff');
                iconLink.style.setProperty('--return-button-bg-color', '#4a9eff');
                iconLink.style.setProperty('--return-button-shadow', '0 4px 10px rgba(0, 0, 0, 0.4)');
                iconLink.style.setProperty('--return-button-hover-bg-color', '#3a8eef');
                iconLink.style.setProperty('--return-button-hover-shadow', '0 6px 15px rgba(0, 0, 0, 0.5)');
            } else {
                iconLink.style.setProperty('--return-button-icon-color', '#ffffff');
                iconLink.style.setProperty('--return-button-bg-color', '#326A82');
                iconLink.style.setProperty('--return-button-shadow', '0 4px 10px rgba(0, 0, 0, 0.25)');
                iconLink.style.setProperty('--return-button-hover-bg-color', '#2A5F75');
                iconLink.style.setProperty('--return-button-hover-shadow', '0 6px 15px rgba(0, 0, 0, 0.3)');
            }
        }
    }

    // Apply theme on load
    document.addEventListener('DOMContentLoaded', applyReturnButtonTheme);
    
    // Listen for theme changes
    document.addEventListener('theme-changed', applyReturnButtonTheme);
</script>
<a href="<?php echo esc_url( route_url() ); ?>" class="icon-link">
    <div class="icon-link__inner">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 36" role="img" fill="currentColor" height="24" width="24" aria-hidden="true" aria-label="">
            <path d="M10 10H2V3a1 1 0 0 1 1-1h7ZM14 2h8v8h-8zM34 10h-8V2h7a1 1 0 0 1 1 1ZM2 14h8v8H2zM14 14h8v8h-8zM26 14h8v8h-8zM10 34H3a1 1 0 0 1-1-1v-7h8ZM14 26h8v8h-8zM33 34h-7v-8h8v7a1 1 0 0 1-1 1Z"></path>
        </svg>
    </div>
</a>
