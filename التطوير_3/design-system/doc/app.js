// Initialize dark mode from localStorage
window.addEventListener('DOMContentLoaded', () => {
    const savedTheme = localStorage.getItem('theme')
    if (savedTheme === 'dark') {
        document.documentElement.classList.add('dark')
    }
})

console.log('TDM Design System loaded successfully')
