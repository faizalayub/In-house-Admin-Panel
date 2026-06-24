export default {

    // ─── Button ────────────────────────────────────────────────────────────────
    button: {
        root: ({ props, context }) => ({
            class: [
                'inline-flex items-center justify-center gap-2 font-medium rounded-lg cursor-pointer',
                'transition-colors duration-150 select-none focus:outline-none',

                // ── Size ──
                { 'px-3 py-1.5 text-sm':    !props.size || props.size === 'normal' },
                { 'px-3 py-1.5 text-xs':  props.size === 'small' },
                { 'px-6 py-3 text-base': props.size === 'large' },

                // ── Solid variants ──
                {
                    'bg-primary-gradient text-white hover:brightness-90 active:brightness-75':
                        !props.text && !props.outlined && (!props.severity || props.severity === 'primary'),
                },
                {
                    'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50 active:bg-gray-100':
                        !props.text && !props.outlined && props.severity === 'secondary',
                },
                {
                    'bg-red-600 text-white hover:bg-red-700 active:bg-red-800':
                        !props.text && !props.outlined && props.severity === 'danger',
                },
                {
                    'bg-green-600 text-white hover:bg-green-700':
                        !props.text && !props.outlined && props.severity === 'success',
                },

                // ── Text variants ──
                {
                    'text-primary hover:bg-red-50':
                        props.text && (!props.severity || props.severity === 'primary'),
                },
                {
                    'text-gray-600 hover:bg-gray-100':
                        props.text && props.severity === 'secondary',
                },
                {
                    'text-red-600 hover:bg-red-50':
                        props.text && props.severity === 'danger',
                },

                // ── Fluid ──
                { 'w-full': props.fluid },

                // ── Disabled ──
                { 'opacity-50 cursor-not-allowed pointer-events-none': context.disabled },
            ],
        }),
        label: ({ props }) => ({
            class: props.label ? '' : 'hidden',
        }),
        icon: ({ props }) => ({
            class: [
                { 'mr-1.5': props.label && props.iconPos !== 'right' },
                { 'ml-1.5 order-1': props.label && props.iconPos === 'right' },
            ],
        }),
        loadingIcon: { class: 'animate-spin' },
    },

    // ─── InputText ─────────────────────────────────────────────────────────────
    inputtext: {
        root: ({ props, parent }) => ({
            class: [
                'border rounded-lg text-sm text-gray-900 bg-white placeholder-gray-400',
                'focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent',
                'transition-colors duration-150',
                props.invalid ? 'border-red-400 focus:ring-red-400' : 'border-gray-300',
                props.fluid  ? 'w-full' : '',
                // Extra left padding when sitting inside an IconField
                parent?.instance?.$name === 'IconField' ? 'pl-8' : 'px-3',
                'py-1.5',
                { 'opacity-50 cursor-not-allowed': props.disabled },
            ],
        }),
    },

    // ─── IconField ─────────────────────────────────────────────────────────────
    iconfield: {
        root: { class: 'relative flex items-center' },
    },

    // ─── InputIcon ─────────────────────────────────────────────────────────────
    inputicon: {
        root: { class: 'absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none z-10' },
    },

    // ─── Dialog ────────────────────────────────────────────────────────────────
    dialog: {
        root: {
            class: 'bg-white rounded-xl shadow-2xl w-full',
        },
        mask: {
            class: 'fixed inset-0 flex items-center justify-center z-50 p-3 animate-[dialog-backdrop-in_0.25s_ease-out_both]',
        },
        header: {
            class: 'flex items-center justify-between px-6 py-3 border-b border-gray-100',
        },
        title: {
            class: 'text-base font-semibold text-gray-900',
        },
        headerActions: {
            class: 'flex items-center',
        },
        closeButton: {
            class: [
                'ml-3 w-8 h-8 flex items-center justify-center rounded-lg',
                'text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors cursor-pointer',
            ],
        },
        closeIcon: {
            class: 'w-4 h-4',
        },
        content: {
            class: 'px-6 py-6',
        },
        footer: {
            class: 'px-6 py-3 border-t border-gray-100 flex items-center justify-end gap-2',
        },
        transition: {
            enterFromClass: 'opacity-0 scale-95 translate-y-2',
            enterActiveClass: 'transition-all duration-300 ease-out',
            enterToClass: 'opacity-100 scale-100 translate-y-0',
            leaveFromClass: 'opacity-100 scale-100 translate-y-0',
            leaveActiveClass: 'transition-all duration-200 ease-in',
            leaveToClass: 'opacity-0 scale-95 translate-y-2',
        },
    },

    // ─── DataTable ─────────────────────────────────────────────────────────────
    datatable: {
        root:            { class: 'relative' },
        header:          { class: 'px-3 py-3 border-b border-gray-100 bg-white' },
        wrapper:         { class: 'overflow-x-auto' },
        table:           { class: 'w-full text-sm' },
        thead:           {},
        headerRow:       { class: 'border-b border-gray-200' },
        headerCell:      ({ context }) => ({
            class: [
                'text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide bg-gray-50',
                { 'cursor-pointer select-none hover:bg-gray-100 transition-colors': context.sortable },
            ],
        }),
        headerCellContent: { class: 'flex items-center gap-1' },
        columnHeaderTitle: { class: 'whitespace-nowrap' },
        sortIcon:        { class: 'w-3 h-3 ml-0.5 text-gray-400' },
        tbody:           { class: 'divide-y divide-gray-100' },
        row:             ({ context }) => ({
            class: [
                'hover:bg-red-50/40 transition-colors',
                { 'bg-gray-50/40': context.index % 2 !== 0 },
            ],
        }),
        bodyCell:        { class: 'px-6 py-3' },
        emptyMessageCell:{ class: 'px-6 text-center text-gray-400' },
        footer:          { class: 'px-6 py-3 border-t border-gray-100 bg-white' },
    },

    // ─── Paginator ─────────────────────────────────────────────────────────────
    paginator: {
        root:    { class: 'flex items-center justify-between flex-wrap gap-3 px-6 py-3 bg-white border-t border-gray-100 text-sm text-gray-600' },
        content: { class: 'flex items-center gap-1' },
        first:   ({ context }) => ({
            class: [
                'w-8 h-8 flex items-center justify-center rounded-lg transition-colors',
                context.disabled ? 'text-gray-300 cursor-not-allowed' : 'hover:bg-gray-100 cursor-pointer',
            ],
        }),
        prev:    ({ context }) => ({
            class: [
                'w-8 h-8 flex items-center justify-center rounded-lg transition-colors',
                context.disabled ? 'text-gray-300 cursor-not-allowed' : 'hover:bg-gray-100 cursor-pointer',
            ],
        }),
        next:    ({ context }) => ({
            class: [
                'w-8 h-8 flex items-center justify-center rounded-lg transition-colors',
                context.disabled ? 'text-gray-300 cursor-not-allowed' : 'hover:bg-gray-100 cursor-pointer',
            ],
        }),
        last:    ({ context }) => ({
            class: [
                'w-8 h-8 flex items-center justify-center rounded-lg transition-colors',
                context.disabled ? 'text-gray-300 cursor-not-allowed' : 'hover:bg-gray-100 cursor-pointer',
            ],
        }),
        pages:   { class: 'flex items-center gap-1' },
        page:    ({ context }) => ({
            class: [
                'w-8 h-8 flex items-center justify-center rounded-lg text-sm transition-colors cursor-pointer',
                context.active
                    ? 'bg-primary-gradient text-white font-semibold'
                    : 'hover:bg-gray-100',
            ],
        }),
        current: { class: 'text-sm text-gray-500' },
    },

    // ─── Card ──────────────────────────────────────────────────────────────────
    card: {
        root:     { class: 'bg-white border border-gray-200 rounded-xl overflow-hidden' },
        body:     { class: 'p-6' },
        title:    { class: 'font-semibold text-gray-900 text-base mb-0.5' },
        subtitle: { class: 'text-sm text-gray-500 mb-3' },
        content:  {},
        footer:   { class: 'pt-3 mt-3 border-t border-gray-100' },
    },

    // ─── Panel ─────────────────────────────────────────────────────────────────
    panel: {
        root:             { class: 'bg-white border border-gray-200 rounded-xl overflow-hidden' },
        header:           { class: 'flex items-center justify-between px-6 py-3 bg-gray-50 border-b border-gray-100' },
        title:            { class: 'font-semibold text-gray-800 text-sm' },
        icons:            { class: 'flex items-center gap-1' },
        toggler:          {
            class: [
                'w-6 h-6 flex items-center justify-center rounded text-gray-400',
                'hover:text-gray-600 hover:bg-gray-200 transition-colors cursor-pointer',
            ],
        },
        togglerIcon:      { class: 'text-xs' },
        toggleableContent:{ class: 'transition-all duration-200' },
        content:          { class: 'p-6' },
    },

    // ─── Checkbox ──────────────────────────────────────────────────────────────
    checkbox: {
        root:  { class: 'relative inline-flex cursor-pointer select-none align-middle' },
        input: { class: 'absolute opacity-0 w-full h-full inset-0 cursor-pointer z-10 m-0 p-0' },
        box:   ({ context }) => ({
            class: [
                'w-4 h-4 rounded border flex items-center justify-center shrink-0',
                'transition-colors duration-150',
                context.checked
                    ? 'bg-primary-gradient border-primary'
                    : 'bg-white border-gray-300 hover:border-primary',
                { 'opacity-50 cursor-not-allowed': context.disabled },
            ],
        }),
        icon: { class: 'w-2.5 h-2.5 text-white' },
    },

    // ─── Tag ───────────────────────────────────────────────────────────────────
    tag: {
        root: ({ props }) => ({
            class: [
                'inline-flex items-center gap-1 px-3 py-0.5 rounded-full text-xs font-medium',
                {
                    'bg-red-50 text-primary':   !props.severity || props.severity === 'primary',
                    'bg-gray-100  text-gray-600':     props.severity === 'secondary',
                    'bg-green-50  text-green-700':    props.severity === 'success',
                    'bg-yellow-50 text-yellow-700':   props.severity === 'warn' || props.severity === 'warning',
                    'bg-red-50    text-red-700':      props.severity === 'danger',
                    'bg-sky-50    text-sky-700':      props.severity === 'info',
                },
            ],
        }),
        label: {},
        icon:  { class: 'text-xs' },
    },

    // ─── Chip ──────────────────────────────────────────────────────────────────
    chip: {
        root:       { class: 'inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full bg-gray-100 text-gray-700 text-xs font-medium' },
        label:      {},
        icon:       { class: 'text-xs text-gray-500' },
        removeIcon: { class: 'text-xs text-gray-400 hover:text-gray-600 cursor-pointer ml-0.5' },
    },

    // ─── Toast ─────────────────────────────────────────────────────────────────
    toast: {
        root:           { class: 'fixed top-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none w-80' },
        messageWrapper: { class: 'pointer-events-auto' },
        message:        ({ props }) => ({
            class: [
                'flex items-start gap-3 p-3 rounded-xl shadow-lg border',
                {
                    'bg-green-50  border-green-200':  props.message.severity === 'success',
                    'bg-red-50    border-red-200':    props.message.severity === 'error',
                    'bg-blue-50   border-blue-200':   props.message.severity === 'info',
                    'bg-yellow-50 border-yellow-200': props.message.severity === 'warn',
                },
            ],
        }),
        messageContent: { class: 'flex items-start gap-3 flex-1 min-w-0' },
        messageIcon:    ({ props }) => ({
            class: [
                'text-lg mt-0.5 shrink-0',
                {
                    'pi pi-check-circle text-green-600':      props.message.severity === 'success',
                    'pi pi-times-circle text-red-600':        props.message.severity === 'error',
                    'pi pi-info-circle text-blue-600':        props.message.severity === 'info',
                    'pi pi-exclamation-circle text-yellow-600': props.message.severity === 'warn',
                },
            ],
        }),
        messageText:    { class: 'flex-1 min-w-0' },
        summary:        ({ props }) => ({
            class: [
                'block font-semibold text-sm',
                {
                    'text-green-800':  props.message.severity === 'success',
                    'text-red-800':    props.message.severity === 'error',
                    'text-blue-800':   props.message.severity === 'info',
                    'text-yellow-800': props.message.severity === 'warn',
                },
            ],
        }),
        detail:         ({ props }) => ({
            class: [
                'block text-sm mt-0.5',
                {
                    'text-green-700':  props.message.severity === 'success',
                    'text-red-700':    props.message.severity === 'error',
                    'text-blue-700':   props.message.severity === 'info',
                    'text-yellow-700': props.message.severity === 'warn',
                },
            ],
        }),
        closeButton:    { class: 'ml-auto shrink-0 p-0.5 rounded text-gray-400 hover:text-gray-600 transition-colors cursor-pointer' },
        closeIcon:      { class: 'w-3.5 h-3.5' },
    },

}
