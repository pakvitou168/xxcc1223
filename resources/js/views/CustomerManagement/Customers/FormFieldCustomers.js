export default {
    "individual": [
        {
            component: "div",
            class: 'grid grid-cols-3 gap-x-10',
            children: [
                {
                    type: 'select',
                    name: 'customer_type',
                    holder: 'Customer Type',
                    validate: 'required',
                    value: 'IC',
                    disabled: false,
                    label: 'Customer Type ',
                    "@input": 'changeForm'
                }
            ]
        },
        {
            component: "h3",
            children: "INDIVIDUAL CUSTOMER",
            class: 'w-full bg-blue-50 py-2 my-2',
        },
        {
            component: "div",
            class: 'grid grid-cols-3 gap-x-10',
            children: [
                {
                    type: 'text',
                    name: 'name_kh',
                    placeholder: 'Individual Name in Khmer',
                    label: 'Individual Name in Khmer',
                },
                {
                    type: 'text',
                    name: 'name_en',
                    placeholder: 'Individual Name in LATIN',
                    label: 'Individual Name in LATIN *',
                    validation: 'required',
                    validationName: 'Individual Name in LATIN',
                },
                {
                    type: 'select',
                    name: 'cust_classification',
                    validation: 'required',
                    options: { '01-001': 'DIPLOMAT' },
                    label: 'Business/Occupation *',
                    validation: 'required',
                    validationName: 'Business/Occupation',
                },
                {
                    type: 'select',
                    name: 'gender',
                    validation: 'required',
                    options: { M: 'Male', F: 'Female' },
                    label: 'Gender *',
                },
                {
                    type: 'date',
                    name: 'date_of_birth',
                    placeholder: 'Date of Birth',
                    validationName: 'Date of Birth',
                    label: 'Date of Birth *',
                    validation: 'required',
                    validationName: 'Date of Birth',
                },

            ]
        },
        {
            component: "h3",
            children: "IDENTITY",
            class: 'w-full bg-blue-50 py-2 my-2',
        },
        {
            component: "div",
            class: 'grid grid-cols-3 gap-x-10',
            children: [
                {
                    type: 'select',
                    name: 'identity_type',
                    options: { N: 'National ID Card', P: 'Passport', F: "Family Book", T: "TID Number" },
                    label: 'Identity Type *',
                    validation: 'required',
                    validationName: 'Identity Type',
                },
                {
                    type: 'text',
                    name: 'identity_no',
                    placeholder: 'Identity No.',
                    label: 'Identity No. *',
                    validationName: 'Identity No.',
                    validation: 'required',
                },
                {
                    type: 'text',
                    name: 'national',
                    placeholder: 'National',
                    label: 'National *',
                    validationName: 'National',
                    validation: 'required',
                },
                {
                    type: 'text',
                    name: 'nationality',
                    placeholder: 'Nationality',
                    label: 'Nationality *',
                    validationName: 'Nationality',
                    validation: 'required',
                },
                {
                    type: 'date',
                    name: 'identity_iss_date',
                    label: 'Identity Issue Date',
                },
                {
                    type: 'date',
                    name: 'identity_exp_date',
                    label: 'Identity Expire Date',
                },
                {
                    type: 'select',
                    name: 'province',
                    label: 'City/Province',
                    placeholder: 'City/Province',
                    validationName: 'City/Province',
                    "@input": 'updateDistrictOptions'
                },
                {
                    type: 'select',
                    name: 'district',
                    label: 'District',
                    placeholder: 'District',
                    validationName: 'District',
                    "@input": 'updateCommuneOptions'
                },
                {
                    type: 'select',
                    name: 'commune',
                    label: 'Commune',
                    placeholder: 'Commune',
                    validationName: 'Commune',
                    "@input": 'updatePostalCode'
                },
                {
                    type: 'textarea',
                    name: 'village_en',
                    label: 'Village',
                    placeholder: 'Village',
                },
                {
                    type: 'textarea',
                    name: 'address_en',
                    label: 'Address',
                    validationName: 'Address',
                },
                {
                    type: 'select',
                    name: 'country_code',
                    label: 'Country *',
                    validation: 'required',
                    validationName: 'Country',
                    "@input": 'listenToCountrySelected'
                },
            ]
        },
        {
            component: "h3",
            children: "CONTACT INFORMATION",
            class: 'w-full bg-blue-50 py-2 my-2',
        },
        {
            component: "div",
            children: []
        },
        {
            component: "div",
            children: []
        }
    ],
    "corporate": [
        {
            component: "div",
            class: 'grid grid-cols-3 gap-x-10',
            children: [
            ]
        },
        {
            component: "h3",
            children: "CORPORATE CUSTOMER",
            class: 'w-full bg-blue-50 py-2 my-2',
        },
        {
            component: "div",
            class: 'grid grid-cols-3 gap-x-10',
            children: [
                {
                    type: 'text',
                    name: 'name_kh',
                    placeholder: 'Company Name Khmer',
                    label: 'Company Name Khmer',
                },
                {
                    type: 'text',
                    name: 'name_en',
                    placeholder: 'Company Name Latin',
                    label: 'Company Name Latin *',
                    validation: 'required',
                    validationName: 'Company Name Latin',
                },
                {
                    type: 'text',
                    name: 'tin_code',
                    placeholder: "TIN Code",
                    label: 'TIN Code *',
                    validation: 'required',
                    validationName: 'TIN Code'
                },
                {
                    type: 'date',
                    name: 'incorporate_date',
                    label: 'Incorporate Date',
                },
                {
                    type: 'select',
                    name: 'cust_classification',
                    placeholder: 'Choose an option',
                    options: { '02-001': 'Advertising Company' },
                    label: 'Business/Occupation *',
                    validation: 'required',
                    validationName: 'Business/Occupation',
                },
                {
                    type: 'text',
                    name: 'business_registration_no',
                    placeholder: 'Business Registration No.',
                    label: 'Business Registration No.',
                },
                {
                    type: 'select',
                    name: 'country_code',
                    placeholder: 'Country',
                    label: 'Country',
                    validationName: 'Country',
                    "@input": 'listenToCountrySelected'
                },
                {
                    type: 'select',
                    name: 'province',
                    label: 'City/Province',
                    placeholder: 'City/Province',
                    validationName: 'City/Province',
                    "@input": 'updateDistrictOptions'
                },
                {
                    type: 'select',
                    name: 'district',
                    label: 'District',
                    placeholder: 'District',
                    validationName: 'District',
                    "@input": 'updateCommuneOptions'
                },
                {
                    type: 'select',
                    name: 'commune',
                    label: 'Commune',
                    placeholder: 'Commune',
                    validationName: 'Commune',
                    "@input": 'updatePostalCode'
                },
                {
                    type: 'textarea',
                    name: 'village_en',
                    label: 'Village',
                    placeholder: 'Village',
                },
                {
                    type: 'textarea',
                    name: 'address_en',
                    placeholder: 'Address',
                    label: 'Address',
                    validationName: 'Address',
                },
                {
                    type: 'text',
                    name: 'postal_code',
                    class: 'hidden'
                },
            ]
        },
        {
            component: "h3",
            children: "CONTACT INFORMATION",
            class: 'w-full bg-blue-50 py-2 my-2',
        },
        {
            component: "div",
            children: []
        },

        {
            component: "div",
            children: []
        },
        {
            component: "div",
            children: []
        },
    ],
    "contact": [
        {
            type: 'group',
            repeatable: true,
            name: 'contactgroup',
            addLabel: 'Add Row',
            children: [
                {
                    component: 'div',
                    class: 'grid grid-cols-3 gap-x-10',
                    children: [
                        {
                            name: 'contact_level',
                            type: 'select',
                            label: 'Contact Level',
                            options: { PRIMARY: 'PRIMARY' },
                        },
                        {
                            name: 'contact_type',
                            type: 'select',
                            label: 'Contact Type',
                            options: { TELEPHONE: "TELEPHONE" },
                        },
                        {
                            name: 'contact_info',
                            placeholder: 'Contact Info',
                            label: 'Contact Info *',
                            validationName: 'Contact Info',
                            validation: 'required',
                        },
                        {
                            name: 'customer_no',
                            type: 'hidden',
                        },
                    ]
                }
            ]
        }
    ],
    "additional": [
        {
            component: "h3",
            children: "ADDITIONAL INFORMATION",
            class: 'w-full bg-blue-50 py-2 my-2',
        },
        {
            component: 'div',
            class: 'grid grid-cols-3 gap-x-10',
            children: [
                {
                    type: 'number',
                    name: 'broker_id',
                    placeholder: 'Distribution Channel',
                    label: 'Distribution Channel',
                },
                {
                    type: 'text',
                    name: 'language_code',
                    placeholder: 'Language Code',
                    validationName: 'Language Code',
                    validation: 'max:2',
                    label: 'Language Code',
                },
                {
                    type: 'select',
                    name: 'risk_category',
                    placeholder: 'Choose an option',
                    options: { L: 'Low', M: 'Medium', H: "High" },
                    label: 'Risk Category',
                },
            ]
        }
    ]
}
