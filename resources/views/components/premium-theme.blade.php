<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        darkMode: 'class',
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Tajawal', 'sans-serif'],
                    display: ['Tajawal', 'sans-serif'],
                },
                colors: {
                    primary: {
                        50: '#fff8f1',
                        100: '#ffedd5',
                        400: '#fb923c',
                        500: '#f97316',
                        600: '#ea580c',
                        700: '#c2410c',
                    },
                    amber: {
                        50: '#fffbf0',
                        100: '#fef3c7',
                        400: '#fbbf24',
                        500: '#f59e0b',
                        600: '#d97706',
                    },
                    gray: {
                        850: '#1f2937',
                        900: '#111827',
                        950: '#030712',
                    }
                },
                boxShadow: {
                    'glass': '0 8px 32px 0 rgba(31, 38, 135, 0.05)',
                    'soft': '0 4px 20px -2px rgba(0, 0, 0, 0.05)',
                    'glow': '0 0 15px rgba(245, 158, 11, 0.15)',
                },
                animation: {
                    'fade-in': 'fadeIn 0.5s ease-out forwards',
                    'slide-up': 'slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                    'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                },
                keyframes: {
                    fadeIn: {
                        '0%': { opacity: '0' },
                        '100%': { opacity: '1' },
                    },
                    slideUp: {
                        '0%': { opacity: '0', transform: 'translateY(20px)' },
                        '100%': { opacity: '1', transform: 'translateY(0)' },
                    }
                }
            }
        }
    }
</script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@200;300;400;500;700;800;900&display=swap');

    :root {
        --bg-color: #f1f5f9; /* Slate 200 - More contrast */
    }

    body {
        font-family: 'Tajawal', sans-serif;
        background-color: var(--bg-color);
        color: #334155;
        -webkit-font-smoothing: antialiased;
    }

    /* Modern Card Style */
    .modern-card {
        background: white;
        border: 1px solid #f1f5f9;
        border-radius: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.01), 0 2px 4px -1px rgba(0, 0, 0, 0.01);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .modern-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.01);
        border-color: #e2e8f0;
    }

    /* Typography Polish */
    h1, h2, h3, h4, h5, h6 {
        letter-spacing: -0.01em;
    }

    /* Form Elements */
    textarea, input {
        font-family: 'Tajawal', sans-serif;
    }

    /* Scrollbar */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 100vh; }
    ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        lucide.createIcons();
    });
</script>
