## Administration

Documentation describing Home Screen configuration features.

- [Managing Home Screen Plugin Functionality](./admin/README.md)

## Apps

Documentation describing frontend Home Screen Apps functionality.

- [Home Screen Frontend Apps Functionality](./apps/README.md)

## Training Videos

Documentation describing frontend Home Screen Training Video functionality.

- [Home Screen Frontend Training Videos Functionality](./train/README.md)

## Custom Apps

Documentation describing the methods available to developers, for the registration of new custom apps.

- [Registering New Custom Apps](./custom/README.md)

## Frequently Asked Questions

__What's The Deal With Dark Mode In The Samsung Web Browser?__

Although the Samsung browser feature automatically applies it's own dark mode to web pages; it is somewhat restricted, without first seeking the web developer's consent; which results in limited transformational context. Some examples of this, can be seen when attempting to transform images with superimposed text, or small images often used as icons and logos; will tend to get merged with the darker background.

Although processing filters have been introduced, there is typically a performance hit; with an end result that is not always accurate.

Web developers can now mitigate these issues, with the use of [prefers-color-scheme media query](https://developer.mozilla.org/en-US/docs/Web/CSS/@media/prefers-color-scheme) and [color-scheme meta-tag](https://www.w3.org/TR/css-color-adjust-1/) CSS properties.

With these features, web developers are able to identify user preferences and provide alternate styles to the content and achieve the desired theme.

It has also been noted that dark mode works well, when also enabled within Samsung's web browser settings, as described in the summary below:

1. First, enable dark mode within host operating system settings.
2. Once this setting has been enabled, open Samsung's web browser and navigate to the settings menu.
3. From there, you should find the Light Theme Sites/Dark Theme Sites option; which should be toggled accordingly, to enable dark mode.
4. Next, navigate back to the browser settings page and select the Labs view, where the following options should be displayed:
   - Use System Font For Webpages.
   - Use Website Dark Theme.
5. Enable the **Use Website Dark Theme** option, in order to fully support dark mode within Samsung's web browser.

More detailed descriptions for supporting dark mode and enabling the feature within Samsung web browsers, can be found in the links below.

- [Dark Mode in Samsung Internet](https://developer.samsung.com/internet/blog/en/2020/12/15/dark-mode-in-samsung-internet)
- [Samsung’s Internet browser now has Night Mode for easy browsing in the dark](https://thenextweb.com/news/samsung-internet-night-mode)
- [Give your eyes a break with Night and High Contrast Modes](https://medium.com/samsung-internet-dev/samsung-internet-v6-2-now-stable-ab7f95ed8b4b)
