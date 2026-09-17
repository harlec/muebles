/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './resources/views/**/*.php',
    './public/js/**/*.js',
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Roboto', 'sans-serif'],
      },
      colors: {
        // Tokens de design_handoff_dashboard_multilocal/README.md
        // Los valores reales viven como CSS custom properties (public/css/input.css)
        // para poder alternar tema claro/oscuro sin duplicar la paleta aquí.
        bg: 'var(--bg)',
        card: 'var(--card)',
        chip: 'var(--chip)',
        inset: 'var(--inset)',
        track: 'var(--track)',
        ink: 'var(--ink)',
        body: 'var(--body)',
        body2: 'var(--body2)',
        mut: 'var(--mut)',
        faint: 'var(--faint)',
        ghosttxt: 'var(--ghost-txt)',
        rule: 'var(--rule)',
        acc: '#ED0B4C',
        acctxt: 'var(--acc-txt)',
        accsoft: 'var(--acc-soft)',
        accbd: 'var(--acc-bd)',
        rotbg: 'var(--rot-bg)',
        rotfg: 'var(--rot-fg)',
        ok: 'var(--ok)',
        bd: 'var(--bd)',
        bd2: 'var(--bd2)',
        bd3: 'var(--bd3)',
        bd4: 'var(--bd4)',
      },
      borderRadius: {
        panel: '18px',
        kpi: '16px',
        chip: '14px',
        tab: '10px',
      },
      keyframes: {
        pulseDot: {
          '0%, 100%': { opacity: 1, transform: 'scale(1)' },
          '50%': { opacity: 0.25, transform: 'scale(.75)' },
        },
        fadeUp: {
          from: { opacity: 0, transform: 'translateY(6px)' },
          to: { opacity: 1, transform: 'translateY(0)' },
        },
      },
      animation: {
        pulseDot: 'pulseDot 1.4s infinite',
        fadeUp: 'fadeUp .35s ease-out',
      },
    },
  },
  plugins: [],
};
