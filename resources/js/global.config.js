const passTrough =
{
    autocomplete: {
        input: {
            class: 'border px-2 py-2.5' // OR { class: 'w-16rem' }
        },
        dropdownbutton: {
            root: 'border'
        }
    },
    calendar: {
        root: 'w-full border rounded',
        input: {
            class: 'px-2 py-2.5 text-sm'
        }
    },
    inputtext: {
        root: 'w-full px-2 py-2 text-base border'
    },
    panel: {
        header: {
            class: 'bg-red-500'
        }
    },
    dropdown: {
        root: 'border py-0.5',
        input: {
            class: 'text-sm'
        },
        item: {
            class: 'text-sm'
        },
        filterInput: {
            class: "py-1 border"
        }
    },
    inputnumber: {
        input: {
            class: ''
        },
        buttongroup: {
            class: 'border rounded-r'
        }
    },
    button: {
        root: 'p-button'
    },
    textarea: {
        root: 'border px-2 py-1'
    },
    multiselect: {
        root: 'border',
        filterInput: {
            class: "py-1 border border-ra"
        },
        headercheckbox: {
            box: {
                class: "border"
            }
        },
        itemcheckbox: {
            box: {
                class: "border"
            }
        }
    },
    checkbox: {
        box: {
            class: "border border-slate-400"
        }
    },
    radiobutton: {
        box: {
            class: 'border border-slate-400'
        }
    },
    tree: {
        root:'p-0',
        input:
        {
            class: 'border px-2 py-2',
            placeholder: 'Search',
        }
    }
}
export {
    passTrough
}