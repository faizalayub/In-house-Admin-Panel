<script setup>
import PrimeDataTable from 'primevue/datatable'

defineOptions({ inheritAttrs: false })

const pt = {
    root: {
        class: 'relative'
    },
    mask: {
        class: 'absolute inset-0 z-10 flex items-center justify-center bg-white/70'
    },
    loadingIcon: {
        class: 'text-2xl text-primary animate-spin'
    },
    header: {
        class: ''
    },
    pcPaginator: {
        class: ''
    },
    tableContainer: {
        class: 'overflow-x-auto'
    },
    virtualScroller: {
        class: ''
    },
    table: {
        class: 'w-full text-sm'
    },
    virtualScrollerSpacer: {
        class: ''
    },
    footer: {
        class: 'border-t border-gray-100'
    },
    thead: {
        class: ''
    },
    headerRow: {
        class: 'border-b border-gray-200 bg-gray-50'
    },
    tbody: {
        class: 'divide-y divide-gray-100'
    },
    rowGroupHeader: {
        class: ''
    },
    rowGroupHeaderCell: {
        class: ''
    },
    bodyRow: {
        class: ''
    },
    row: {
        class: 'group hover:bg-gray-50/60 transition-colors'
    },
    rowExpansion: {
        class: ''
    },
    rowExpansionCell: {
        class: ''
    },
    rowGroupFooter: {
        class: ''
    },
    rowGroupFooterCell: {
        class: ''
    },
    emptyMessage: {
        class: 'text-center'
    },
    emptyMessageCell: {
        class: 'px-3 py-8 text-center text-gray-400'
    },
    tfoot: {
        class: ''
    },
    footerRow: {
        class: ''
    },
    columnResizeIndicator: {
        class: ''
    },
    rowReorderIndicatorUp: {
        class: ''
    },
    rowReorderIndicatorDown: {
        class: ''
    },
    columnGroup: {
        class: ''
    },
    column: {
        root: {
            class: ''
        },
        headerCell: ({ context }) => {
            return {
                class: ['text-left px-3 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide whitespace-nowrap', {
                    'cursor-pointer select-none hover:bg-gray-100 transition-colors': context.sortable
                }]
            };
        },
        columnResizer: {
            class: ''
        },
        columnHeaderContent: {
            class: 'flex items-center gap-1'
        },
        columnTitle: {
            class: ''
        },
        sort: ({ context }) => ({
            class: [context.sorted ? 'text-primary-500' : 'text-surface-700', context.sorted ? 'dark:text-primary-400' : 'dark:text-white/80']
        }),
        sortIcon: {
            class: 'w-3 h-3 ml-0.5 text-gray-400'
        },
        pcSortBadge: {
            class: ''
        },
        pcHeaderCheckbox: {
            class: ''
        },
        filter: {
            class: ''
        },
        filterElementContainer: {
            class: ''
        },
        pcColumnFilterButton: {
            class: ''
        },
        filterMenuIcon: {
            class: ''
        },
        pcColumnFilterClearButton: {
            class: ''
        },
        filterClearIcon: {
            class: ''
        },
        filterOverlay: {
            class: ''
        },
        filterConstraintList: {
            class: ''
        },
        filterConstraint: {
            class: ''
        },
        filterConstraintSeparator: {
            class: ''
        },
        filterOperator: {
            class: ''
        },
        pcFilterOperatorDropdown: {
            class: ''
        },
        filterRuleList: {
            class: ''
        },
        filterRule: {
            class: ''
        },
        pcFilterConstraintDropdown: {
            class: ''
        },
        filterRemove: {
            class: ''
        },
        pcFilterRemoveRuleButton: {
            class: ''
        },
        filterAddButtonContainer: {
            class: ''
        },
        pcFilterAddRuleButton: {
            class: ''
        },
        filterButtonbar: {
            class: ''
        },
        pcFilterClearButton: {
            class: ''
        },
        pcFilterApplyButton: {
            class: ''
        },
        rowToggleButton: {
            class: ''
        },
        rowToggleIcon: {
            class: ''
        },
        nodeToggleButton: {
            class: ''
        },
        nodeToggleIcon: {
            class: ''
        },
        bodyCell: {
            class: 'px-3 py-3'
        },
        bodyCellContent: {
            class: ''
        },
        reorderableRowHandle: {
            class: ''
        },
        pcRowRadiobutton: {
            class: ''
        },
        pcRowCheckbox: {
            class: ''
        },
        pcNodeCheckbox: {
            class: ''
        },
        pcRowEditorInit: {
            class: ''
        },
        pcRowEditorSave: {
            class: ''
        },
        pcRowEditorCancel: {
            class: ''
        },
        footerCell: {
            class: ''
        },
        columnFooter: {
            class: ''
        }
    }
}
</script>

<template>
    <PrimeDataTable v-bind="$attrs" :pt="pt">
        <template v-for="(_, name) in $slots" #[name]="slotProps">
            <slot :name="name" v-bind="slotProps ?? {}" />
        </template>
    </PrimeDataTable>
</template>
