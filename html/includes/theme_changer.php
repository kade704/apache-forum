<script>
    function triggerTheme() {
        let themeName = localStorage.getItem('theme');
        if (!themeName) themeName = 'light';
        else {
            if (themeName == 'light') themeName = 'dark';
            else themeName = 'light';
        }

        document.documentElement.setAttribute('data-theme', themeName);
        localStorage.setItem('theme', themeName);
    }

    document.addEventListener('DOMContentLoaded', () => {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            document.documentElement.setAttribute('data-theme', savedTheme);
        }
    });
</script>
