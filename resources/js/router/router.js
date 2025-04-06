
// Dashboard
import Dashboard from '../views/Dashboard.vue'

// import UserManagementRoute from './userManagementRoute';
import claimRoutes from '@/router/claimRoutes'

import hsRoutes from './hsRoutes'
import errorRoutes from '@/router/errorRoutes'
import paRoutes from './paRoutes';
import renewalRoutes from './renewalRoutes'
import travelRoutes from './travelRoutes';

// Customer Management
import CustomerIndex from "../views/CustomerManagement/Customers/Index.vue";
import CustomerForm from "../views/CustomerManagement/Customers/Form.vue";
import CustomerDetail from "../views/CustomerManagement/Customers/Detail.vue";

import CustomerClassificationIndex from '../views/CustomerManagement/CustomerClassification/Index.vue'
import CustomerClassificationForm from '../views/CustomerManagement/CustomerClassification/Form.vue'
import CustomerClassificationDetail from '../views/CustomerManagement/CustomerClassification/Detail.vue'

import CountryIndex from '../views/CustomerManagement/Country/Index.vue'
import CountryForm from '../views/CustomerManagement/Country/Form.vue'
import CountryDetail from '../views/CustomerManagement/Country/Detail.vue'

import CustomerProfileIndex from "../views/CustomerManagement/CustomerProfile/Index.vue"
import CustomerProfileDetail from "../views/CustomerManagement/CustomerProfile/Detail.vue"

// // End Customer Management

// // Cover
import CoverIndex from '../views/Cover/Index.vue';
import CoverForm from '../views/Cover/Form.vue';
import CoverDetail from '../views/Cover/Detail.vue';

// // Cover Packages
import CoverPackageIndex from '../views/ProductConfiguration/CoverPackage/Index.vue';
import CoverPackageForm from '../views/ProductConfiguration/CoverPackage/Form.vue';
import CoverPackageDetail from '../views/ProductConfiguration/CoverPackage/Detail.vue';

// // Model
import ModelIndex from '../views/ProductConfiguration/Model/Index.vue';
import ModelForm from '../views/ProductConfiguration/Model/Form.vue';
import ModelDetail from '../views/ProductConfiguration/Model/Detail.vue';

// // Vehicle Usages
import VehicleUsageIndex from '../views/ProductConfiguration/VehicleUsage/Index.vue';
import VehicleUsageForm from '../views/ProductConfiguration/VehicleUsage/Form.vue';
import VehicleUsageDetail from '../views/ProductConfiguration/VehicleUsage/Detail.vue';

// // Vehicle Classification
import VehicleClassificationIndex from '../views/ProductConfiguration/VehicleClassification/Index.vue';
import VehicleClassificationForm from '../views/ProductConfiguration/VehicleClassification/Form.vue';
import VehicleClassificationDetail from '../views/ProductConfiguration/VehicleClassification/Detail.vue';

// // Access Rule
import AccessRuleIndex from '../views/ProductConfiguration/AccessRule/Index.vue';
import AccessRuleForm from '../views/ProductConfiguration/AccessRule/Form.vue';
import AccessRuleDetail from '../views/ProductConfiguration/AccessRule/Detail.vue';

// Auto Quotation
import QuotationAutoIndex from '../views/Quotation/Auto/Index.vue';
import QuotationAutoForm from '../views/Quotation/Auto/Form.vue';
import QuotationAutoDetail from '../views/Quotation/Auto/Detail.vue';

// // Policy
import PolicyIndex from '../views/Policy/Index.vue';
import PolicyForm from '../views/Policy/Form.vue';
import PolicyDetail from '../views/Policy/Detail.vue';

// // Endorsement
import EndorsementIndex from '../views/Endorsement/Index.vue';
import EndorsementForm from '../views/Endorsement/Form.vue';
import EndorsementDetail from '../views/Endorsement/Detail.vue';

// // Bank Information
import BankInformationIndex from '../views/BankInformation/Index.vue';
import BankInformationForm from '../views/BankInformation/Form.vue';
import BankInformationDetail from '../views/BankInformation/Detail.vue';

// // product lines
import ProductLineIndex from '../views/ProductConfiguration/ProductLines/Index.vue';
import ProductLineForm from '../views/ProductConfiguration/ProductLines/Form.vue';
import ProductLineDetail from '../views/ProductConfiguration/ProductLines/Detail.vue';

// //Makes
import MakeIndex from '../views/ProductConfiguration/Make/Index.vue';
import MakeForm from '../views/ProductConfiguration/Make/Form.vue';
import MakeDetail from '../views/ProductConfiguration/Make/Detail.vue';

// //product
import ProductIndex from '../views/ProductConfiguration/Product/Index.vue';
import ProductForm from '../views/ProductConfiguration/Product/Form.vue';
import ProductDetail from '../views/ProductConfiguration/Product/Detail.vue';

// //Cover Component
import CoverCompIndex from '../views/ProductConfiguration/CoverComponent/Index.vue';
import CoverCompForm from '../views/ProductConfiguration/CoverComponent/Form.vue';
import CoverCompDetail from '../views/ProductConfiguration/CoverComponent/Detail.vue';

// //Deductible
import DeductibleIndex from '../views/ProductConfiguration/Deductible/Index.vue';
import DeductibleForm from '../views/ProductConfiguration/Deductible/Form.vue';
import DeductibleDetail from '../views/ProductConfiguration/Deductible/Detail.vue';

// //Clause Maintenance
import ClauseMaintenanceIndex from '../views/ProductConfiguration/Clause/Index.vue';
import ClauseMaintenanceForm from '../views/ProductConfiguration/Clause/Form.vue';
import ClauseMaintenanceDetail from '../views/ProductConfiguration/Clause/Detail.vue';

// // Comp-Formula
import FormulaIndex from '../views/ProductConfiguration/Formula/Index.vue';
import FormulaForm from '../views/ProductConfiguration/Formula/Form.vue';
import FormulaDetail from '../views/ProductConfiguration/Formula/Detail.vue';

// // comp-frm-element
import FrmElementIndex from '../views/ProductConfiguration/CompFrmElement/Index.vue';
import FrmElementForm from '../views/ProductConfiguration/CompFrmElement/Form.vue';
import FrmElementDetail from '../views/ProductConfiguration/CompFrmElement/Detail.vue';

// // comp-frm-expression
import FrmExpIndex from '../views/ProductConfiguration/CompFrmExpression/Index.vue';
import FrmExpForm from '../views/ProductConfiguration/CompFrmExpression/Form.vue';
import FrmExpDetail from '../views/ProductConfiguration/CompFrmExpression/Detail.vue';

// // no-claim-discount
import NoClaimDiscountIndex from '../views/ProductConfiguration/NoClaimDiscount/Index.vue';
import NoClaimDiscountForm from '../views/ProductConfiguration/NoClaimDiscount/Form.vue';
import NoClaimDiscountDetail from '../views/ProductConfiguration/NoClaimDiscount/Detail.vue';

// // Business Category
import BusinessCategoryIndex from '../views/BusinessManagement/BusinessCategory/Index.vue'
import BusinessCategoryForm from '../views/BusinessManagement/BusinessCategory/Form.vue'
import BusinessCategoryDetail from '../views/BusinessManagement/BusinessCategory/Detail.vue'

//product condition rating
import ProductConditionIndex from '../views/ProductConfiguration/ProductConditionRating/Index.vue';
import ProductConditionDetail from '../views/ProductConfiguration/ProductConditionRating/Detail.vue';
import ProductConditionForm from '../views/ProductConfiguration/ProductConditionRating/Form.vue';


// // Business Handler
import BusinessHandlerIndex from '../views/BusinessManagement/BusinessHandler/Index.vue'
import BusinessHandlerForm from '../views/BusinessManagement/BusinessHandler/Form.vue'
import BusinessHandlerDetail from '../views/BusinessManagement/BusinessHandler/Detail.vue'

// // Business Channel
import BusinessChannelIndex from '../views/BusinessManagement/BusinessChannel/Index.vue'
import BusinessChannelForm from '../views/BusinessManagement/BusinessChannel/Form.vue'
import BusinessChannelDetail from '../views/BusinessManagement/BusinessChannel/Detail.vue'

// // Policy Wording V
import PolicyWordingVersionIndex from '../views/ProductConfiguration/PolicyWordingVersion/Index.vue'
import PolicyWordingVersionForm from '../views/ProductConfiguration/PolicyWordingVersion/Form.vue'
import PolicyWordingVersionDetail from '../views/ProductConfiguration/PolicyWordingVersion/Detail.vue'

//Reinsurance
import ReinsuranceIndex from '../views/ReinsuranceManagement/Reinsurance/Index.vue';
import ReinsuranceForm from '../views/ReinsuranceManagement/Reinsurance/Form.vue';
import ReinsuranceDetail from '../views/ReinsuranceManagement/Reinsurance/Detail.vue';
// //Reinsurance Type
import ReinsuranceTypeIndex from '../views/ReinsuranceManagement/ReinsuranceType/Index.vue';
import ReinsuranceTypeForm from '../views/ReinsuranceManagement/ReinsuranceType/Form.vue';
import ReinsuranceTypeDetail from '../views/ReinsuranceManagement/ReinsuranceType/Detail.vue';
// //Reinsurance Partner Group
import ReinsurancePartnerGroupIndex from '../views/ReinsuranceManagement/ReinsurancePartnerGroup/Index.vue';
import ReinsurancePartnerGroupForm from '../views/ReinsuranceManagement/ReinsurancePartnerGroup/Form.vue';
import ReinsurancePartnerGroupDetail from '../views/ReinsuranceManagement/ReinsurancePartnerGroup/Detail.vue';
// //Reinsurance Partner
import ReinsurancePartnerIndex from '../views/ReinsuranceManagement/ReinsurancePartner/Index.vue';
import ReinsurancePartnerForm from '../views/ReinsuranceManagement/ReinsurancePartner/Form.vue';
import ReinsurancePartnerDetail from '../views/ReinsuranceManagement/ReinsurancePartner/Detail.vue';
// //Reinsurance Config
import ReinsuranceConfigIndex from '../views/ReinsuranceManagement/ReinsuranceConfig/Index.vue';
import ReinsuranceConfigForm from '../views/ReinsuranceManagement/ReinsuranceConfig/Form.vue';
import ReinsuranceConfigDetail from '../views/ReinsuranceManagement/ReinsuranceConfig/Detail.vue';

// // Exchange Rate
import ExchangeRateIndex from '../views/ProductConfiguration/ExchangeRate/Index.vue';
import ExchangeRateForm from '../views/ProductConfiguration/ExchangeRate/Form.vue';
import ExchangeRateDetail from '../views/ProductConfiguration/ExchangeRate/Detail.vue';

//Setup Surcharge
import SetupSurchargeIndex from '../views/SurchargeRule/Index.vue';
import SetupSurchargeForm from '../views/SurchargeRule/Form.vue';

// Auth
import ChangePasswordForm from '../views/Auth/ChangePassword.vue'
import securityRoutes from './securityRoutes';

// export default {
//     mode: 'history',
//     routes: [
//         {
//             path: '/',
//             name: 'Dashboard',
//             component: Dashboard,
//         },
// Block Customer Management
// {
//     path: '/customer-management/customer',
//     name: 'Customer',
//     component: CustomerIndex,
//     meta: { code: 'CUSTOMER', permission: 'VIEW' },
// },
// {
//     path: '/customer-management/customer/new',
//     name: 'CustomerNew',
//     component: CustomerForm,
//     meta: { code: 'CUSTOMER', permission: 'NEW' },
// },
// {
//     path: '/customer-management/customer/:id/edit',
//     name: 'CustomerEdit',
//     component: CustomerForm,
//     meta: { code: 'CUSTOMER', permission: 'UPDATE' },
// },
// {
//     path: '/customer-management/customer/:id',
//     name: 'CustomerDetail',
//     component: CustomerDetail,
//     meta: { code: 'CUSTOMER', permission: 'VIEW' },
// },

// Customer Classification
// {
//     path: '/customer-management/customer-classifications',
//     name: 'CustomerClassificationIndex',
//     component: CustomerClassificationIndex,
//     meta: { code: 'CUSTOMER_CLASSIFICATION', permission: 'VIEW' },
// },
// {
//     path: '/customer-management/customer-classifications/new',
//     name: 'CustomerClassificationCreate',
//     component: CustomerClassificationForm,
//     meta: { code: 'CUSTOMER_CLASSIFICATION', permission: 'NEW' },
// },
// {
//     path: '/customer-management/customer-classifications/:id/edit',
//     name: 'CustomerClassificationEdit',
//     component: CustomerClassificationForm,
//     meta: { code: 'CUSTOMER_CLASSIFICATION', permission: 'UPDATE' },
// },
// {
//     path: '/customer-management/customer-classifications/:id',
//     name: 'CustomerClassificationDetail',
//     component: CustomerClassificationDetail,
//     meta: { code: 'CUSTOMER_CLASSIFICATION', permission: 'VIEW' },
// },

// // Country
// {
//     path: '/customer-management/countries',
//     name: 'CountryIndex',
//     component: CountryIndex,
//     meta: { code: 'COUNTRY', permission: 'VIEW' },
// },
// {
//     path: '/customer-management/countries/new',
//     name: 'CountryCreate',
//     component: CountryForm,
//     meta: { code: 'COUNTRY', permission: 'NEW' },
// },
// {
//     path: '/customer-management/countries/:id/edit',
//     name: 'CountryUpdate',
//     component: CountryForm,
//     meta: { code: 'COUNTRY', permission: 'UPDATE' },
// },
// {
//     path: '/customer-management/countries/:id',
//     name: 'CountryDetail',
//     component: CountryDetail,
//     meta: { code: 'COUNTRY', permission: 'VIEW' },
// },

// // Cover
// {
//     path: '/product-configuration/covers',
//     name: 'CoverIndex',
//     component: CoverIndex,
//     meta: { code: 'COVER', permission: 'VIEW' },
// },
// {
//     path: '/product-configuration/covers/new',
//     name: 'CoverForm',
//     component: CoverForm,
//     meta: { code: 'COVER', permission: 'NEW' },
// },
// {
//     path: '/product-configuration/covers/:id/edit',
//     name: 'CoverUpdate',
//     component: CoverForm,
//     meta: { code: 'COVER', permission: 'UPDATE' },
// },
// {
//     path: '/product-configuration/covers/:id',
//     name: 'CoverDetail',
//     component: CoverDetail,
//     meta: { code: 'COVER', permission: 'VIEW' },
// },
// // Cover Packages
// {
//     path: '/product-configuration/cover-packages',
//     name: 'CoverPackageIndex',
//     component: CoverPackageIndex,
//     meta: { code: 'COVER_PACKAGE', permission: 'VIEW' },
// },
// {
//     path: '/product-configuration/cover-packages/new',
//     name: 'CoverPackageCreate',
//     component: CoverPackageForm,
//     meta: { code: 'COVER_PACKAGE', permission: 'NEW' },
// },
// {
//     path: '/product-configuration/cover-packages/:id/edit',
//     name: 'CoverPackageUpdate',
//     component: CoverPackageForm,
//     meta: { code: 'COVER_PACKAGE', permission: 'UPDATE' },
// },
// {
//     path: '/product-configuration/cover-packages/:id',
//     name: 'CoverPackageDetail',
//     component: CoverPackageDetail,
//     meta: { code: 'COVER_PACKAGE', permission: 'VIEW' },
// },
// //Model
// {
//     path: '/product-configuration/models',
//     name: 'ModelIndex',
//     component: ModelIndex,
//     meta: { code: 'MODEL', permission: 'VIEW' },
// },
// {
//     path: '/product-configuration/models/new',
//     name: 'ModelCreate',
//     component: ModelForm,
//     meta: { code: 'MODEL', permission: 'NEW' },
// },
// {
//     path: '/product-configuration/models/:id/edit',
//     name: 'ModelUpdate',
//     component: ModelForm,
//     meta: { code: 'MODEL', permission: 'UPDATE' },
// },
// {
//     path: '/product-configuration/models/:id',
//     name: 'ModelDetail',
//     component: ModelDetail,
//     meta: { code: 'MODEL', permission: 'VIEW' },
// },
// // Vehicle Usages
// {
//     path: '/product-configuration/vehicle-usages',
//     name: 'VehicleUsageIndex',
//     component: VehicleUsageIndex,
//     meta: { code: 'VEHICLE_USAGE', permission: 'VIEW' },
// },
// {
//     path: '/product-configuration/vehicle-usages/new',
//     name: 'VehicleUsageCreate',
//     component: VehicleUsageForm,
//     meta: { code: 'VEHICLE_USAGE', permission: 'NEW' },
// },
// {
//     path: '/product-configuration/vehicle-usages/:id/edit',
//     name: 'VehicleUsageUpdate',
//     component: VehicleUsageForm,
//     meta: { code: 'VEHICLE_USAGE', permission: 'UPDATE' },
// },
// {
//     path: '/product-configuration/vehicle-usages/:id',
//     name: 'VehicleUsageDetail',
//     component: VehicleUsageDetail,
//     meta: { code: 'VEHICLE_USAGE', permission: 'VIEW' },
// },
// //Vehicle Classification
// {
//     path: '/product-configuration/vehicle-classifications',
//     name: 'VehicleClassificationIndex',
//     component: VehicleClassificationIndex,
//     meta: { code: 'VEHICLE_CLASSIFICATION', permission: 'VIEW' },
// },
// {
//     path: '/product-configuration/vehicle-classifications/new',
//     name: 'VehicleClassificationCreate',
//     component: VehicleClassificationForm,
//     meta: { code: 'VEHICLE_CLASSIFICATION', permission: 'NEW' },
// },
// {
//     path: '/product-configuration/vehicle-classifications/:id/edit',
//     name: 'VehicleClassificationUpdate',
//     component: VehicleClassificationForm,
//     meta: { code: 'VEHICLE_CLASSIFICATION', permission: 'UPDATE' },
// },
// {
//     path: '/product-configuration/vehicle-classifications/:id',
//     name: 'VehicleClassificationDetail',
//     component: VehicleClassificationDetail,
//     meta: { code: 'VEHICLE_CLASSIFICATION', permission: 'VIEW' },
// },
// // Access Rule
// {
//     path: '/product-configuration/access-rules',
//     name: 'AccessRuleIndex',
//     component: AccessRuleIndex,
//     meta: { code: 'ACCESS_RULE', permission: 'VIEW' },
// },
// {
//     path: '/product-configuration/access-rules/new',
//     name: 'AccessRuleCreate',
//     component: AccessRuleForm,
//     meta: { code: 'ACCESS_RULE', permission: 'NEW' },
// },
// {
//     path: '/product-configuration/access-rules/:id/edit',
//     name: 'AccessRuleUpdate',
//     component: AccessRuleForm,
//     meta: { code: 'ACCESS_RULE', permission: 'UPDATE' },
// },
// {
//     path: '/product-configuration/access-rules/:id',
//     name: 'AccessRuleDetail',
//     component: AccessRuleDetail,
//     meta: { code: 'ACCESS_RULE', permission: 'VIEW' },
// },
// // Product-line
// {
//     path: '/product-configuration/product-lines',
//     name: 'ProductLineIndex',
//     component: ProductLineIndex,
//     meta: { code: 'PRODUCT_LINE', permission: 'VIEW' },
// },
// {
//     path: '/product-configuration/product-lines/new',
//     name: 'ProductLineForm',
//     component: ProductLineForm,
//     meta: { code: 'PRODUCT_LINE', permission: 'NEW' },
// },
// {
//     path: '/product-configuration/product-lines/:id/edit',
//     name: 'ProductLineUpdate',
//     component: ProductLineForm,
//     meta: { code: 'PRODUCT_LINE', permission: 'UPDATE' },
// },
// {
//     path: '/product-configuration/product-lines/detail/:id',
//     name: 'ProductLineDetail',
//     component: ProductLineDetail,
//     meta: { code: 'PRODUCT_LINE', permission: 'VIEW' },
// },
// // product_make
// {
//     path: '/product-configuration/makes',
//     name: 'MakeIndex',
//     component: MakeIndex,
//     meta: { code: 'MAKE', permission: 'VIEW' },
// },
// {
//     path: '/product-configuration/makes/new',
//     name: 'MakeCreate',
//     component: MakeForm,
//     meta: { code: 'MAKE', permission: 'NEW' },
// },
// {
//     path: '/product-configuration/makes/:id/edit',
//     name: 'MakeUpdate',
//     component: MakeForm,
//     meta: { code: 'MAKE', permission: 'UPDATE' },
// },
// {
//     path: '/product-configuration/makes/:id',
//     name: 'MakeDetail',
//     component: MakeDetail,
//     meta: { code: 'MAKE', permission: 'VIEW' },
// },
// // Auto Quotation
// {
//     path: '/quotation/autos',
//     name: 'QuotationAutoIndex',
//     component: QuotationAutoIndex,
//     meta: { code: 'AUTO', permission: 'VIEW' },
// },
// {
//     path: '/quotation/autos/new',
//     name: 'QuotationAutoCreate',
//     component: QuotationAutoForm,
//     meta: { code: 'AUTO', permission: 'NEW' },
// },
// {
//     path: '/quotation/autos/:id/edit',
//     name: 'QuotationAutoEdit',
//     component: QuotationAutoForm,
//     meta: { code: 'AUTO', permission: 'UPDATE' },
// },
// {
//     path: '/quotation/autos/:id',
//     name: 'QuotationAutoDetail',
//     component: QuotationAutoDetail,
//     meta: { code: 'AUTO', permission: 'VIEW' },
// },
// // Policy
// {
//     path: '/policies',
//     name: 'PolicyIndex',
//     component: PolicyIndex,
//     meta: { code: 'POLICY', permission: 'VIEW' },
// },
// {
//     path: '/policies/:id/edit',
//     name: 'PolicyEdit',
//     component: PolicyForm,
//     meta: { code: 'POLICY', permission: 'UPDATE' },
// },
// {
//     path: '/policies/:id',
//     name: 'PolicyDetail',
//     component: PolicyDetail,
//     meta: { code: 'POLICY', permission: 'VIEW' },
// },
// // Endorsement
// {
//     path: '/endorsements',
//     name: 'EndorsementIndex',
//     component: EndorsementIndex,
//     meta: { code: 'ENDORSEMENT', permission: 'VIEW' },
// },
// {
//     path: '/endorsements/:id/edit',
//     name: 'EndorsementEdit',
//     component: EndorsementForm,
//     meta: { code: 'ENDORSEMENT', permission: 'UPDATE' },
// },
// {
//     path: '/endorsements/:id',
//     name: 'EndorsementDetail',
//     component: EndorsementDetail,
//     meta: { code: 'ENDORSEMENT', permission: 'VIEW' },
// },
// // Bank Information
// {
//     path: '/bank-informations',
//     name: 'BankInformationIndex',
//     component: BankInformationIndex,
//     meta: { code: 'BANK_INFORMATION', permission: 'VIEW' },
// },
// {
//     path: '/bank-informations/new',
//     name: 'BankInformationForm',
//     component: BankInformationForm,
//     meta: { code: 'BANK_INFORMATION', permission: 'NEW' },
// },
// {
//     path: '/bank-informations/:id/edit',
//     name: 'BankInformationUpdate',
//     component: BankInformationForm,
//     meta: { code: 'BANK_INFORMATION', permission: 'UPDATE' },
// },
// {
//     path: '/bank-informations/:id',
//     name: 'BankInformationDetail',
//     component: BankInformationDetail,
//     meta: { code: 'BANK_INFORMATION', permission: 'VIEW' },
// },
// // Products
// {
//     path: '/product-configuration/products',
//     name: 'ProductIndex',
//     component: ProductIndex,
//     meta: { code: 'PRODUCT', permission: 'VIEW' }
// },
// {
//     path: '/product-configuration/products/new',
//     name: 'ProductForm',
//     component: ProductForm,
//     meta: { code: 'PRODUCT', permission: 'NEW' }
// },
// {
//     path: '/product-configuration/products/:id/edit',
//     name: 'ProductUpdate',
//     component: ProductForm,
//     meta: { code: 'PRODUCT', permission: 'UPDATE' }
// },
// {
//     path: '/product-configuration/products/detail/:id',
//     name: 'ProductDetail',
//     component: ProductDetail,
//     meta: { code: 'PRODUCT', permission: 'VIEW' }
// },
// // Cover Component
// {
//     path: '/product-configuration/cover-component',
//     name: 'CoverComponentIndex',
//     component: CoverCompIndex,
//     meta: { code: 'COVER_COMPONENT', permission: 'VIEW' }
// },
// {
//     path: '/product-configuration/cover-component/new',
//     name: 'CoverComponentNew',
//     component: CoverCompForm,
//     meta: { code: 'COVER_COMPONENT', permission: 'NEW' }
// },
// {
//     path: '/product-configuration/cover-component/:id/edit',
//     name: 'CoverComponentUpdate',
//     component: CoverCompForm,
//     meta: { code: 'COVER_COMPONENT', permission: 'UPDATE' }
// },
// {
//     path: '/product-configuration/cover-component/:id',
//     name: 'CoverCompDetail',
//     component: CoverCompDetail,
//     meta: { code: 'COVER_COMPONENT', permission: 'VIEW' }
// },
// // Deductible
// {
//     path: '/product-configuration/deductibles',
//     name: 'DeductibleIndex',
//     component: DeductibleIndex,
//     meta: { code: 'DEDUCTIBLE', permission: 'VIEW' },
// },
// {
//     path: '/product-configuration/deductibles/new',
//     name: 'DeductibleCreate',
//     component: DeductibleForm,
//     meta: { code: 'DEDUCTIBLE', permission: 'NEW' },
// },
// {
//     path: '/product-configuration/deductibles/:id/edit',
//     name: 'DeductibleUpdate',
//     component: DeductibleForm,
//     meta: { code: 'DEDUCTIBLE', permission: 'UPDATE' },
// },
// {
//     path: '/product-configuration/deductibles/:id',
//     name: 'DeductibleDetail',
//     component: DeductibleDetail,
//     meta: { code: 'DEDUCTIBLE', permission: 'VIEW' },
// },
// // Clause Maintenance
// {
//     path: '/product-configuration/clause-maintenances',
//     name: 'ClauseMaintenanceIndex',
//     component: ClauseMaintenanceIndex,
//     meta: { code: 'CLAUSE_MAINTENANCE', permission: 'VIEW' },
// },
// {
//     path: '/product-configuration/clause-maintenances/new',
//     name: 'ClauseMaintenanceCreate',
//     component: ClauseMaintenanceForm,
//     meta: { code: 'CLAUSE_MAINTENANCE', permission: 'NEW' },
// },
// {
//     path: '/product-configuration/clause-maintenances/:id/edit',
//     name: 'ClauseMaintenanceUpdate',
//     component: ClauseMaintenanceForm,
//     meta: { code: 'CLAUSE_MAINTENANCE', permission: 'UPDATE' },
// },
// {
//     path: '/product-configuration/clause-maintenances/:id',
//     name: 'ClauseMaintenanceDetail',
//     component: ClauseMaintenanceDetail,
//     meta: { code: 'CLAUSE_MAINTENANCE', permission: 'VIEW' },
// },
// // ProdConfig/Formula
// {
//     path: '/product-configuration/formula',
//     name: 'FormulaIndex',
//     component: FormulaIndex,
//     meta: { code: 'COMP_FORMULA', permission: 'VIEW' },
// },
// {
//     path: '/product-configuration/formula/new',
//     name: 'FormulaCreate',
//     component: FormulaForm,
//     meta: { code: 'COMP_FORMULA', permission: 'NEW' },
// },
// {
//     path: '/product-configuration/formula/:id/edit',
//     name: 'FormulaUpdate',
//     component: FormulaForm,
//     meta: { code: 'COMP_FORMULA', permission: 'UPDATE' },
// },
// {
//     path: '/product-configuration/formula/:id',
//     name: 'FormulaDetail',
//     component: FormulaDetail,
//     meta: { code: 'COMP_FORMULA', permission: 'VIEW' },
// },

// // ProdConfig/FormulaElement
// {
//     path: '/product-configuration/comp-frm-element',
//     name: 'FrmElementIndex',
//     component: FrmElementIndex,
//     meta: { code: 'COMP_FRM_ELEMENT', permission: 'VIEW' },
// },
// {
//     path: '/product-configuration/comp-frm-element/new',
//     name: 'FrmElementCreate',
//     component: FrmElementForm,
//     meta: { code: 'COMP_FRM_ELEMENT', permission: 'NEW' },
// },
// {
//     path: '/product-configuration/comp-frm-element/:id/edit',
//     name: 'FrmElementUpdate',
//     component: FrmElementForm,
//     meta: { code: 'COMP_FRM_ELEMENT', permission: 'UPDATE' },
// },
// {
//     path: '/product-configuration/comp-frm-element/:id',
//     name: 'FrmElementDetail',
//     component: FrmElementDetail,
//     meta: { code: 'COMP_FRM_ELEMENT', permission: 'VIEW' },
// },
// // ProdCofig Formula Expression
// {
//     path: '/product-configuration/comp-frm-expr',
//     name: 'FrmExpIndex',
//     component: FrmExpIndex,
//     meta: { code: 'COMP_FRM_EXPRESSION', permission: 'VIEW' },
// },
// {
//     path: '/product-configuration/comp-frm-expr/new',
//     name: 'FrmExpCreate',
//     component: FrmExpForm,
//     meta: { code: 'COMP_FRM_EXPRESSION', permission: 'NEW' },
// },
// {
//     path: '/product-configuration/comp-frm-expr/:id/edit',
//     name: 'FrmExpUpdate',
//     component: FrmExpForm,
//     meta: { code: 'COMP_FRM_EXPRESSION', permission: 'UPDATE' },
// },
// {
//     path: '/product-configuration/comp-frm-expr/:id',
//     name: 'FrmExpExpDetail',
//     component: FrmExpDetail,
//     meta: { code: 'COMP_FRM_EXPRESSION', permission: 'VIEW' },
// },

// // ProdCofig No claim discount
// {
//     path: '/product-configuration/no-claim-discounts',
//     name: 'NoClaimDiscountIndex',
//     component: NoClaimDiscountIndex,
//     meta: { code: 'NO_CLAIM_DISCOUNT', permission: 'VIEW' },
// },
// {
//     path: '/product-configuration/no-claim-discounts/new',
//     name: 'NoClaimDiscountCreate',
//     component: NoClaimDiscountForm,
//     meta: { code: 'NO_CLAIM_DISCOUNT', permission: 'NEW' },
// },
// {
//     path: '/product-configuration/no-claim-discounts/:id/edit',
//     name: 'NoClaimDiscountUpdate',
//     component: NoClaimDiscountForm,
//     meta: { code: 'NO_CLAIM_DISCOUNT', permission: 'UPDATE' },
// },
// {
//     path: '/product-configuration/no-claim-discounts/:id',
//     name: 'NoClaimDiscountDetail',
//     component: NoClaimDiscountDetail,
//     meta: { code: 'NO_CLAIM_DISCOUNT', permission: 'VIEW' },
// },
// // Business Category
// {
//     path: '/business-management/business-categories',
//     name: 'BusinessCategoryIndex',
//     component: BusinessCategoryIndex,
//     meta: { code: 'BUSINESS_CATEGORY', permission: 'VIEW' },
// },
// {
//     path: '/business-management/business-categories/new',
//     name: 'BusinessCategoryCreate',
//     component: BusinessCategoryForm,
//     meta: { code: 'BUSINESS_CATEGORY', permission: 'NEW' },
// },
// {
//     path: '/business-management/business-categories/:id/edit',
//     name: 'BusinessCategoryEdit',
//     component: BusinessCategoryForm,
//     meta: { code: 'BUSINESS_CATEGORY', permission: 'UPDATE' },
// },
// {
//     path: '/business-management/business-categories/:id',
//     name: 'BusinessCategoryDetail',
//     component: BusinessCategoryDetail,
//     meta: { code: 'BUSINESS_CATEGORY', permission: 'VIEW' },
// },
// // Business Handler
// {
//     path: '/business-management/business-handlers',
//     name: 'BusinessHandlerIndex',
//     component: BusinessHandlerIndex,
//     meta: { code: 'BUSINESS_HANDLER', permission: 'VIEW' },
// },
// {
//     path: '/business-management/business-handlers/new',
//     name: 'BusinessHandlerCreate',
//     component: BusinessHandlerForm,
//     meta: { code: 'BUSINESS_HANDLER', permission: 'NEW' },
// },
// {
//     path: '/business-management/business-handlers/:id/edit',
//     name: 'BusinessHandlerEdit',
//     component: BusinessHandlerForm,
//     meta: { code: 'BUSINESS_HANDLER', permission: 'UPDATE' },
// },
// {
//     path: '/business-management/business-handlers/:id',
//     name: 'BusinessHandlerDetail',
//     component: BusinessHandlerDetail,
//     meta: { code: 'BUSINESS_HANDLER', permission: 'VIEW' },
// },
// // Business HandlerChannel
// {
//     path: '/business-management/business-channels',
//     name: 'BusinessChannelIndex',
//     component: BusinessChannelIndex,
//     meta: { code: 'BUSINESS_CHANNEL', permission: 'VIEW' },
// },
// {
//     path: '/business-management/business-channels/new',
//     name: 'BusinessChannelCreate',
//     component: BusinessChannelForm,
//     meta: { code: 'BUSINESS_CHANNEL', permission: 'NEW' },
// },
// {
//     path: '/business-management/business-channels/:id/edit',
//     name: 'BusinessChannelEdit',
//     component: BusinessChannelForm,
//     meta: { code: 'BUSINESS_CHANNEL', permission: 'UPDATE' },
// },
// {
//     path: '/business-management/business-channels/:id',
//     name: 'BusinessChannelDetail',
//     component: BusinessChannelDetail,
//     meta: { code: 'BUSINESS_CHANNEL', permission: 'VIEW' },
// },

// // Policy Wording Version
// {
//     path: '/product-configuration/policy-wording-versions',
//     name: 'PolicyWordingVersionIndex',
//     component: PolicyWordingVersionIndex,
//     meta: { code: 'POLICY_WORDING_VERSION', permission: 'VIEW' },
// },
// {
//     path: '/product-configuration/policy-wording-versions/new',
//     name: 'PolicyWordingVersionCreate',
//     component: PolicyWordingVersionForm,
//     meta: { code: 'POLICY_WORDING_VERSION', permission: 'NEW' },
// },
// {
//     path: '/product-configuration/policy-wording-versions/:id/edit',
//     name: 'PolicyWordingVersionEdit',
//     component: PolicyWordingVersionForm,
//     meta: { code: 'POLICY_WORDING_VERSION', permission: 'UPDATE' },
// },
// {
//     path: '/product-configuration/policy-wording-versions/:id',
//     name: 'PolicyWordingVersionDetail',
//     component: PolicyWordingVersionDetail,
//     meta: { code: 'POLICY_WORDING_VERSION', permission: 'VIEW' },
// },
// // Reinsurance
// {
//     path: '/reinsurance-management/reinsurances',
//     name: 'ReinsuranceIndex',
//     component: ReinsuranceIndex,
//     meta: { code: 'REINSURANCE', permission: 'VIEW' },
// },
// {
//     path: '/reinsurance-management/reinsurances/new',
//     name: 'ReinsuranceCreate',
//     component: ReinsuranceForm,
//     meta: { code: 'REINSURANCE', permission: 'NEW' },
// },
// {
//     path: '/reinsurance-management/reinsurances/:id/edit',
//     name: 'ReinsuranceUpdate',
//     component: ReinsuranceForm,
//     meta: { code: 'REINSURANCE', permission: 'UPDATE' },
// },
// {
//     path: '/reinsurance-management/reinsurances/:id',
//     name: 'ReinsuranceDetail',
//     component: ReinsuranceDetail,
//     meta: { code: 'REINSURANCE', permission: 'VIEW' },
// },
// // Reinsurance Type
// {
//     path: '/reinsurance-management/reinsurance-types',
//     name: 'ReinsuranceTypeIndex',
//     component: ReinsuranceTypeIndex,
//     meta: { code: 'REINSURANCE_TYPE', permission: 'VIEW' },
// },
// {
//     path: '/reinsurance-management/reinsurance-types/new',
//     name: 'ReinsuranceTypeCreate',
//     component: ReinsuranceTypeForm,
//     meta: { code: 'REINSURANCE_TYPE', permission: 'NEW' },
// },
// {
//     path: '/reinsurance-management/reinsurance-types/:id/edit',
//     name: 'ReinsuranceTypeUpdate',
//     component: ReinsuranceTypeForm,
//     meta: { code: 'REINSURANCE_TYPE', permission: 'UPDATE' },
// },
// {
//     path: '/reinsurance-management/reinsurance-types/:id',
//     name: 'ReinsuranceTypeDetail',
//     component: ReinsuranceTypeDetail,
//     meta: { code: 'REINSURANCE_TYPE', permission: 'VIEW' },
// },
// // Reinsurance Partner Group
// {
//     path: '/reinsurance-management/reinsurance-partner-groups',
//     name: 'ReinsurancePartnerGroupIndex',
//     component: ReinsurancePartnerGroupIndex,
//     meta: { code: 'REINSURANCE_PARTNER_GROUP', permission: 'VIEW' },
// },
// {
//     path: '/reinsurance-management/reinsurance-partner-groups/new',
//     name: 'ReinsurancePartnerGroupCreate',
//     component: ReinsurancePartnerGroupForm,
//     meta: { code: 'REINSURANCE_PARTNER_GROUP', permission: 'NEW' },
// },
// {
//     path: '/reinsurance-management/reinsurance-partner-groups/:id/edit',
//     name: 'ReinsurancePartnerGroupUpdate',
//     component: ReinsurancePartnerGroupForm,
//     meta: { code: 'REINSURANCE_PARTNER_GROUP', permission: 'UPDATE' },
// },
// {
//     path: '/reinsurance-management/reinsurance-partner-groups/:id',
//     name: 'ReinsurancePartnerGroupDetail',
//     component: ReinsurancePartnerGroupDetail,
//     meta: { code: 'REINSURANCE_PARTNER_GROUP', permission: 'VIEW' },
// },
// // Reinsurance Partner
// {
//     path: '/reinsurance-management/reinsurance-partners',
//     name: 'ReinsurancePartnerIndex',
//     component: ReinsurancePartnerIndex,
//     meta: { code: 'REINSURANCE_PARTNER', permission: 'VIEW' },
// },
// {
//     path: '/reinsurance-management/reinsurance-partners/new',
//     name: 'ReinsurancePartnerCreate',
//     component: ReinsurancePartnerForm,
//     meta: { code: 'REINSURANCE_PARTNER', permission: 'NEW' },
// },
// {
//     path: '/reinsurance-management/reinsurance-partners/:id/edit',
//     name: 'ReinsurancePartnerUpdate',
//     component: ReinsurancePartnerForm,
//     meta: { code: 'REINSURANCE_PARTNER', permission: 'UPDATE' },
// },
// {
//     path: '/reinsurance-management/reinsurance-partners/:id',
//     name: 'ReinsurancePartnerDetail',
//     component: ReinsurancePartnerDetail,
//     meta: { code: 'REINSURANCE_PARTNER', permission: 'VIEW' },
// },
// // Reinsurance Config
// {
//     path: '/reinsurance-management/reinsurance-configs',
//     name: 'ReinsuranceConfigIndex',
//     component: ReinsuranceConfigIndex,
//     meta: { code: 'REINSURANCE_CONFIG', permission: 'VIEW' },
// },
// {
//     path: '/reinsurance-management/reinsurance-configs/new',
//     name: 'ReinsuranceConfigCreate',
//     component: ReinsuranceConfigForm,
//     meta: { code: 'REINSURANCE_CONFIG', permission: 'NEW' },
// },
// {
//     path: '/reinsurance-management/reinsurance-configs/:id/edit',
//     name: 'ReinsuranceConfigUpdate',
//     component: ReinsuranceConfigForm,
//     meta: { code: 'REINSURANCE_CONFIG', permission: 'UPDATE' },
// },
// {
//     path: '/reinsurance-management/reinsurance-configs/:id',
//     name: 'ReinsuranceConfigDetail',
//     component: ReinsuranceConfigDetail,
//     meta: { code: 'REINSURANCE_CONFIG', permission: 'VIEW' },
// },
// // Exchange Rate
// {
//     path: '/exchange-rate',
//     name: 'ExchangeRate',
//     component: ExchangeRateIndex,
//     meta: { code: 'EXCHANGE_RATE', permission: 'VIEW' },
// },
// {
//     path: '/exchange-rate/new',
//     name: 'ExchangeRateCreate',
//     component: ExchangeRateForm,
//     meta: { code: 'EXCHANGE_RATE', permission: 'NEW' },
// },
// {
//     path: '/exchange-rate/:id',
//     name: 'ExchangeRateDetail',
//     component: ExchangeRateDetail,
//     meta: { code: 'EXCHANGE_RATE', permission: 'VIEW' },
// },
// {
//     path: '/exchange-rate/:id/edit',
//     name: 'ExchangeRateEdit',
//     component: ExchangeRateForm,
//     meta: { code: 'EXCHANGE_RATE', permission: 'UPDATE' },
// },

// // Auth
// {
//     path: '/change-password',
//     name: 'ChangePasswordForm',
//     component: ChangePasswordForm,
// },

// ]
// .concat(UserManagementRoute)
// .concat(claimRoutes)
// .concat(errorRoutes)
// }
const routes = [
    {
        path: "/",
        name: "Dashboard",
        component: Dashboard,
    },
    {
        path: "/change-password",
        name: "ChangePasswordForm",
        component: ChangePasswordForm,
    },
    {
        path: "/customer-management/customer",
        name: "Customer",
        component: CustomerIndex,
        meta: { code: "CUSTOMER", permission: "VIEW" },
    },
    {
        path: "/customer-management/customer/new",
        name: "CustomerNew",
        component: CustomerForm,
        meta: { code: "CUSTOMER", permission: "NEW" },
    },
    {
        path: "/customer-management/customer/:id/edit",
        name: "CustomerEdit",
        component: CustomerForm,
        meta: { code: "CUSTOMER", permission: "UPDATE" },
    },
    {
        path: "/customer-management/customer/:id",
        name: "CustomerDetail",
        component: CustomerDetail,
        meta: { code: "CUSTOMER", permission: "VIEW" },
    },
    {
        path: "/quotation/autos",
        name: "QuotationAutoIndex",
        component: QuotationAutoIndex,
        meta: { code: "AUTO", permission: "VIEW" },
    },
    {
        path: "/quotation/autos/new",
        name: "QuotationAutoCreate",
        component: QuotationAutoForm,
        meta: { code: "AUTO", permission: "NEW" },
    },
    {
        path: "/quotation/autos/:id/edit",
        name: "QuotationAutoEdit",
        component: QuotationAutoForm,
        meta: { code: "AUTO", permission: "UPDATE" },
    },
    {
        path: "/quotation/autos/:id",
        name: "QuotationAutoDetail",
        component: QuotationAutoDetail,
        meta: { code: "AUTO", permission: "VIEW" },
    },
    {
        path: "/policies/auto",
        name: "PolicyIndex",
        component: PolicyIndex,
        meta: { code: "POLICY", permission: "VIEW" },
    },
    {
        path: "/policies/auto/:id/edit",
        name: "PolicyEdit",
        component: PolicyForm,
        meta: { code: "POLICY", permission: "UPDATE" },
    },
    {
        path: "/policies/auto/:id",
        name: "PolicyDetail",
        component: PolicyDetail,
        meta: { code: "POLICY", permission: "VIEW" },
    },
    {
        path: "/product-configuration/product-lines",
        name: "ProductLineIndex",
        component: ProductLineIndex,
        meta: { code: "PRODUCT_LINE", permission: "VIEW" },
    },
    {
        path: "/product-configuration/product-lines/new",
        name: "ProductLineForm",
        component: ProductLineForm,
        meta: { code: "PRODUCT_LINE", permission: "NEW" },
    },
    {
        path: "/product-configuration/product-lines/:id/edit",
        name: "ProductLineUpdate",
        component: ProductLineForm,
        meta: { code: "PRODUCT_LINE", permission: "UPDATE" },
    },
    {
        path: "/product-configuration/product-lines/detail/:id",
        name: "ProductLineDetail",
        component: ProductLineDetail,
        meta: { code: "PRODUCT_LINE", permission: "VIEW" },
    },
    {
        path: "/product-configuration/products",
        name: "ProductIndex",
        component: ProductIndex,
        meta: { code: "PRODUCT", permission: "VIEW" },
    },
    {
        path: "/product-configuration/products/new",
        name: "ProductForm",
        component: ProductForm,
        meta: { code: "PRODUCT", permission: "NEW" },
    },
    {
        path: "/product-configuration/products/:id/edit",
        name: "ProductUpdate",
        component: ProductForm,
        meta: { code: "PRODUCT", permission: "UPDATE" },
    },
    {
        path: "/product-configuration/products/detail/:id",
        name: "ProductDetail",
        component: ProductDetail,
        meta: { code: "PRODUCT", permission: "VIEW" },
    },
    {
        path: "/product-configuration/makes",
        name: "MakeIndex",
        component: MakeIndex,
        meta: { code: "MAKE", permission: "VIEW" },
    },
    {
        path: "/product-configuration/makes/new",
        name: "MakeCreate",
        component: MakeForm,
        meta: { code: "MAKE", permission: "NEW" },
    },
    {
        path: "/product-configuration/makes/:id/edit",
        name: "MakeUpdate",
        component: MakeForm,
        meta: { code: "MAKE", permission: "UPDATE" },
    },
    {
        path: "/product-configuration/makes/:id",
        name: "MakeDetail",
        component: MakeDetail,
        meta: { code: "MAKE", permission: "VIEW" },
    },
    {
        path: "/product-configuration/models",
        name: "ModelIndex",
        component: ModelIndex,
        meta: { code: "MODEL", permission: "VIEW" },
    },
    {
        path: "/product-configuration/models/new",
        name: "ModelCreate",
        component: ModelForm,
        meta: { code: "MODEL", permission: "NEW" },
    },
    {
        path: "/product-configuration/models/:id/edit",
        name: "ModelUpdate",
        component: ModelForm,
        meta: { code: "MODEL", permission: "UPDATE" },
    },
    {
        path: "/product-configuration/models/:id",
        name: "ModelDetail",
        component: ModelDetail,
        meta: { code: "MODEL", permission: "VIEW" },
    },
    {
        path: "/product-configuration/vehicle-usages",
        name: "VehicleUsageIndex",
        component: VehicleUsageIndex,
        meta: { code: "VEHICLE_USAGE", permission: "VIEW" },
    },
    {
        path: "/product-configuration/vehicle-usages/new",
        name: "VehicleUsageCreate",
        component: VehicleUsageForm,
        meta: { code: "VEHICLE_USAGE", permission: "NEW" },
    },
    {
        path: "/product-configuration/vehicle-usages/:id/edit",
        name: "VehicleUsageUpdate",
        component: VehicleUsageForm,
        meta: { code: "VEHICLE_USAGE", permission: "UPDATE" },
    },
    {
        path: "/product-configuration/vehicle-usages/:id",
        name: "VehicleUsageDetail",
        component: VehicleUsageDetail,
        meta: { code: "VEHICLE_USAGE", permission: "VIEW" },
    },
    {
        path: "/product-configuration/vehicle-classifications",
        name: "VehicleClassificationIndex",
        component: VehicleClassificationIndex,
        meta: { code: "VEHICLE_CLASSIFICATION", permission: "VIEW" },
    },
    {
        path: "/product-configuration/vehicle-classifications/new",
        name: "VehicleClassificationCreate",
        component: VehicleClassificationForm,
        meta: { code: "VEHICLE_CLASSIFICATION", permission: "NEW" },
    },
    {
        path: "/product-configuration/vehicle-classifications/:id/edit",
        name: "VehicleClassificationUpdate",
        component: VehicleClassificationForm,
        meta: { code: "VEHICLE_CLASSIFICATION", permission: "UPDATE" },
    },
    {
        path: "/product-configuration/vehicle-classifications/:id",
        name: "VehicleClassificationDetail",
        component: VehicleClassificationDetail,
        meta: { code: "VEHICLE_CLASSIFICATION", permission: "VIEW" },
    },
    {
        path: "/product-configuration/access-rules",
        name: "AccessRuleIndex",
        component: AccessRuleIndex,
        meta: { code: "ACCESS_RULE", permission: "VIEW" },
    },
    {
        path: "/product-configuration/access-rules/new",
        name: "AccessRuleCreate",
        component: AccessRuleForm,
        meta: { code: "ACCESS_RULE", permission: "NEW" },
    },
    {
        path: "/product-configuration/access-rules/:id/edit",
        name: "AccessRuleUpdate",
        component: AccessRuleForm,
        meta: { code: "ACCESS_RULE", permission: "UPDATE" },
    },
    {
        path: "/product-configuration/access-rules/:id",
        name: "AccessRuleDetail",
        component: AccessRuleDetail,
        meta: { code: "ACCESS_RULE", permission: "VIEW" },
    },
    {
        path: "/product-configuration/covers",
        name: "CoverIndex",
        component: CoverIndex,
        meta: { code: "COVER", permission: "VIEW" },
    },
    {
        path: "/product-configuration/covers/new",
        name: "CoverForm",
        component: CoverForm,
        meta: { code: "COVER", permission: "NEW" },
    },
    {
        path: "/product-configuration/covers/:id/edit",
        name: "CoverUpdate",
        component: CoverForm,
        meta: { code: "COVER", permission: "UPDATE" },
    },
    {
        path: "/product-configuration/covers/:id",
        name: "CoverDetail",
        component: CoverDetail,
        meta: { code: "COVER", permission: "VIEW" },
    },
    {
        path: "/product-configuration/cover-packages",
        name: "CoverPackageIndex",
        component: CoverPackageIndex,
        meta: { code: "COVER_PACKAGE", permission: "VIEW" },
    },
    {
        path: "/product-configuration/cover-packages/new",
        name: "CoverPackageCreate",
        component: CoverPackageForm,
        meta: { code: "COVER_PACKAGE", permission: "NEW" },
    },
    {
        path: "/product-configuration/cover-packages/:id/edit",
        name: "CoverPackageUpdate",
        component: CoverPackageForm,
        meta: { code: "COVER_PACKAGE", permission: "UPDATE" },
    },
    {
        path: "/product-configuration/cover-packages/:id",
        name: "CoverPackageDetail",
        component: CoverPackageDetail,
        meta: { code: "COVER_PACKAGE", permission: "VIEW" },
    },
    {
        path: "/product-configuration/cover-component",
        name: "CoverComponentIndex",
        component: CoverCompIndex,
        meta: { code: "COVER_COMPONENT", permission: "VIEW" },
    },
    {
        path: "/product-configuration/cover-component/new",
        name: "CoverComponentNew",
        component: CoverCompForm,
        meta: { code: "COVER_COMPONENT", permission: "NEW" },
    },
    {
        path: "/product-configuration/cover-component/:id/edit",
        name: "CoverComponentUpdate",
        component: CoverCompForm,
        meta: { code: "COVER_COMPONENT", permission: "UPDATE" },
    },
    {
        path: "/product-configuration/cover-component/:id",
        name: "CoverCompDetail",
        component: CoverCompDetail,
        meta: { code: "COVER_COMPONENT", permission: "VIEW" },
    },
    {
        path: "/product-configuration/clause-maintenances",
        name: "ClauseMaintenanceIndex",
        component: ClauseMaintenanceIndex,
        meta: { code: "CLAUSE_MAINTENANCE", permission: "VIEW" },
    },
    {
        path: "/product-configuration/clause-maintenances/new",
        name: "ClauseMaintenanceCreate",
        component: ClauseMaintenanceForm,
        meta: { code: "CLAUSE_MAINTENANCE", permission: "NEW" },
    },
    {
        path: "/product-configuration/clause-maintenances/:id/edit",
        name: "ClauseMaintenanceUpdate",
        component: ClauseMaintenanceForm,
        meta: { code: "CLAUSE_MAINTENANCE", permission: "UPDATE" },
    },
    {
        path: "/product-configuration/clause-maintenances/:id",
        name: "ClauseMaintenanceDetail",
        component: ClauseMaintenanceDetail,
        meta: { code: "CLAUSE_MAINTENANCE", permission: "VIEW" },
    },
    {
        path: "/product-configuration/deductibles",
        name: "DeductibleIndex",
        component: DeductibleIndex,
        meta: { code: "DEDUCTIBLE", permission: "VIEW" },
    },
    {
        path: "/product-configuration/deductibles/new",
        name: "DeductibleCreate",
        component: DeductibleForm,
        meta: { code: "DEDUCTIBLE", permission: "NEW" },
    },
    {
        path: "/product-configuration/deductibles/:id/edit",
        name: "DeductibleUpdate",
        component: DeductibleForm,
        meta: { code: "DEDUCTIBLE", permission: "UPDATE" },
    },
    {
        path: "/product-configuration/deductibles/:id",
        name: "DeductibleDetail",
        component: DeductibleDetail,
        meta: { code: "DEDUCTIBLE", permission: "VIEW" },
    },
    {
        path: "/product-configuration/formula",
        name: "FormulaIndex",
        component: FormulaIndex,
        meta: { code: "COMP_FORMULA", permission: "VIEW" },
    },
    {
        path: "/product-configuration/formula/new",
        name: "FormulaCreate",
        component: FormulaForm,
        meta: { code: "COMP_FORMULA", permission: "NEW" },
    },
    {
        path: "/product-configuration/formula/:id/edit",
        name: "FormulaUpdate",
        component: FormulaForm,
        meta: { code: "COMP_FORMULA", permission: "UPDATE" },
    },
    {
        path: "/product-configuration/formula/:id",
        name: "FormulaDetail",
        component: FormulaDetail,
        meta: { code: "COMP_FORMULA", permission: "VIEW" },
    },
    {
        path: "/product-configuration/comp-frm-element",
        name: "FrmElementIndex",
        component: FrmElementIndex,
        meta: { code: "COMP_FRM_ELEMENT", permission: "VIEW" },
    },
    {
        path: "/product-configuration/comp-frm-element/new",
        name: "FrmElementCreate",
        component: FrmElementForm,
        meta: { code: "COMP_FRM_ELEMENT", permission: "NEW" },
    },
    {
        path: "/product-configuration/comp-frm-element/:id/edit",
        name: "FrmElementUpdate",
        component: FrmElementForm,
        meta: { code: "COMP_FRM_ELEMENT", permission: "UPDATE" },
    },
    {
        path: "/product-configuration/comp-frm-element/:id",
        name: "FrmElementDetail",
        component: FrmElementDetail,
        meta: { code: "COMP_FRM_ELEMENT", permission: "VIEW" },
    },
    {
        path: "/product-configuration/comp-frm-expr",
        name: "FrmExpIndex",
        component: FrmExpIndex,
        meta: { code: "COMP_FRM_EXPRESSION", permission: "VIEW" },
    },
    {
        path: "/product-configuration/comp-frm-expr/new",
        name: "FrmExpCreate",
        component: FrmExpForm,
        meta: { code: "COMP_FRM_EXPRESSION", permission: "NEW" },
    },
    {
        path: "/product-configuration/comp-frm-expr/:id/edit",
        name: "FrmExpUpdate",
        component: FrmExpForm,
        meta: { code: "COMP_FRM_EXPRESSION", permission: "UPDATE" },
    },
    {
        path: "/product-configuration/comp-frm-expr/:id",
        name: "FrmExpExpDetail",
        component: FrmExpDetail,
        meta: { code: "COMP_FRM_EXPRESSION", permission: "VIEW" },
    },
    {
        path: "/product-configuration/policy-wording-versions",
        name: "PolicyWordingVersionIndex",
        component: PolicyWordingVersionIndex,
        meta: { code: "POLICY_WORDING_VERSION", permission: "VIEW" },
    },
    {
        path: "/product-configuration/policy-wording-versions/new",
        name: "PolicyWordingVersionCreate",
        component: PolicyWordingVersionForm,
        meta: { code: "POLICY_WORDING_VERSION", permission: "NEW" },
    },
    {
        path: "/product-configuration/policy-wording-versions/:id/edit",
        name: "PolicyWordingVersionEdit",
        component: PolicyWordingVersionForm,
        meta: { code: "POLICY_WORDING_VERSION", permission: "UPDATE" },
    },
    {
        path: "/product-configuration/policy-wording-versions/:id",
        name: "PolicyWordingVersionDetail",
        component: PolicyWordingVersionDetail,
        meta: { code: "POLICY_WORDING_VERSION", permission: "VIEW" },
    },
    {
        path: "/product-configuration/no-claim-discounts",
        name: "NoClaimDiscountIndex",
        component: NoClaimDiscountIndex,
        meta: { code: "NO_CLAIM_DISCOUNT", permission: "VIEW" },
    },
    {
        path: "/product-configuration/no-claim-discounts/new",
        name: "NoClaimDiscountCreate",
        component: NoClaimDiscountForm,
        meta: { code: "NO_CLAIM_DISCOUNT", permission: "NEW" },
    },
    {
        path: "/product-configuration/no-claim-discounts/:id/edit",
        name: "NoClaimDiscountUpdate",
        component: NoClaimDiscountForm,
        meta: { code: "NO_CLAIM_DISCOUNT", permission: "UPDATE" },
    },
    {
        path: "/product-configuration/no-claim-discounts/:id",
        name: "NoClaimDiscountDetail",
        component: NoClaimDiscountDetail,
        meta: { code: "NO_CLAIM_DISCOUNT", permission: "VIEW" },
    },
    {
        path: "/exchange-rate",
        name: "ExchangeRate",
        component: ExchangeRateIndex,
        meta: { code: "EXCHANGE_RATE", permission: "VIEW" },
    },
    {
        path: "/exchange-rate/new",
        name: "ExchangeRateCreate",
        component: ExchangeRateForm,
        meta: { code: "EXCHANGE_RATE", permission: "NEW" },
    },
    {
        path: "/exchange-rate/:id",
        name: "ExchangeRateDetail",
        component: ExchangeRateDetail,
        meta: { code: "EXCHANGE_RATE", permission: "VIEW" },
    },
    {
        path: "/exchange-rate/:id/edit",
        name: "ExchangeRateEdit",
        component: ExchangeRateForm,
        meta: { code: "EXCHANGE_RATE", permission: "UPDATE" },
    },
    {
        path: "/policies/auto",
        name: "PolicyIndex",
        component: PolicyIndex,
        meta: { code: "POLICY", permission: "VIEW" },
    },
    {
        path: "/policies/auto/:id/edit",
        name: "PolicyEdit",
        component: PolicyForm,
        meta: { code: "POLICY", permission: "UPDATE" },
    },
    {
        path: "/policies/auto/:id",
        name: "PolicyDetail",
        component: PolicyDetail,
        meta: { code: "POLICY", permission: "VIEW" },
    },
    {
        path: "/reinsurance-management/reinsurances",
        name: "ReinsuranceIndex",
        component: ReinsuranceIndex,
        meta: { code: "REINSURANCE", permission: "VIEW" },
    },
    {
        path: "/reinsurance-management/reinsurances/new",
        name: "ReinsuranceCreate",
        component: ReinsuranceForm,
        meta: { code: "REINSURANCE", permission: "NEW" },
    },
    {
        path: "/reinsurance-management/reinsurances/:id/edit",
        name: "ReinsuranceUpdate",
        component: ReinsuranceForm,
        meta: { code: "REINSURANCE", permission: "UPDATE" },
    },
    {
        path: "/reinsurance-management/reinsurances/:id",
        name: "ReinsuranceDetail",
        component: ReinsuranceDetail,
        meta: { code: "REINSURANCE", permission: "VIEW" },
    },
    // Reinsurance Type
    {
        path: "/reinsurance-management/reinsurance-types",
        name: "ReinsuranceTypeIndex",
        component: ReinsuranceTypeIndex,
        meta: { code: "REINSURANCE_TYPE", permission: "VIEW" },
    },
    {
        path: "/reinsurance-management/reinsurance-types/new",
        name: "ReinsuranceTypeCreate",
        component: ReinsuranceTypeForm,
        meta: { code: "REINSURANCE_TYPE", permission: "NEW" },
    },
    {
        path: "/reinsurance-management/reinsurance-types/:id/edit",
        name: "ReinsuranceTypeUpdate",
        component: ReinsuranceTypeForm,
        meta: { code: "REINSURANCE_TYPE", permission: "UPDATE" },
    },
    {
        path: "/reinsurance-management/reinsurance-types/:id",
        name: "ReinsuranceTypeDetail",
        component: ReinsuranceTypeDetail,
        meta: { code: "REINSURANCE_TYPE", permission: "VIEW" },
    },
    // Reinsurance Partner Group
    {
        path: "/reinsurance-management/reinsurance-partner-groups",
        name: "ReinsurancePartnerGroupIndex",
        component: ReinsurancePartnerGroupIndex,
        meta: { code: "REINSURANCE_PARTNER_GROUP", permission: "VIEW" },
    },
    {
        path: "/reinsurance-management/reinsurance-partner-groups/new",
        name: "ReinsurancePartnerGroupCreate",
        component: ReinsurancePartnerGroupForm,
        meta: { code: "REINSURANCE_PARTNER_GROUP", permission: "NEW" },
    },
    {
        path: "/reinsurance-management/reinsurance-partner-groups/:id/edit",
        name: "ReinsurancePartnerGroupUpdate",
        component: ReinsurancePartnerGroupForm,
        meta: { code: "REINSURANCE_PARTNER_GROUP", permission: "UPDATE" },
    },
    {
        path: "/reinsurance-management/reinsurance-partner-groups/:id",
        name: "ReinsurancePartnerGroupDetail",
        component: ReinsurancePartnerGroupDetail,
        meta: { code: "REINSURANCE_PARTNER_GROUP", permission: "VIEW" },
    },
    // Reinsurance Partner
    {
        path: "/reinsurance-management/reinsurance-partners",
        name: "ReinsurancePartnerIndex",
        component: ReinsurancePartnerIndex,
        meta: { code: "REINSURANCE_PARTNER", permission: "VIEW" },
    },
    {
        path: "/reinsurance-management/reinsurance-partners/new",
        name: "ReinsurancePartnerCreate",
        component: ReinsurancePartnerForm,
        meta: { code: "REINSURANCE_PARTNER", permission: "NEW" },
    },
    {
        path: "/reinsurance-management/reinsurance-partners/:id/edit",
        name: "ReinsurancePartnerUpdate",
        component: ReinsurancePartnerForm,
        meta: { code: "REINSURANCE_PARTNER", permission: "UPDATE" },
    },
    {
        path: "/reinsurance-management/reinsurance-partners/:id",
        name: "ReinsurancePartnerDetail",
        component: ReinsurancePartnerDetail,
        meta: { code: "REINSURANCE_PARTNER", permission: "VIEW" },
    },
    // Reinsurance Config
    {
        path: "/reinsurance-management/reinsurance-configs",
        name: "ReinsuranceConfigIndex",
        component: ReinsuranceConfigIndex,
        meta: { code: "REINSURANCE_CONFIG", permission: "VIEW" },
    },
    {
        path: "/reinsurance-management/reinsurance-configs/new",
        name: "ReinsuranceConfigCreate",
        component: ReinsuranceConfigForm,
        meta: { code: "REINSURANCE_CONFIG", permission: "NEW" },
    },
    {
        path: "/reinsurance-management/reinsurance-configs/:id/edit",
        name: "ReinsuranceConfigUpdate",
        component: ReinsuranceConfigForm,
        meta: { code: "REINSURANCE_CONFIG", permission: "UPDATE" },
    },
    {
        path: "/reinsurance-management/reinsurance-configs/:id",
        name: "ReinsuranceConfigDetail",
        component: ReinsuranceConfigDetail,
        meta: { code: "REINSURANCE_CONFIG", permission: "VIEW" },
    },
    {
        path: "/endorsements/auto",
        name: "EndorsementIndex",
        component: EndorsementIndex,
        meta: { code: "ENDORSEMENT", permission: "VIEW" },
    },
    {
        path: "/endorsements/auto/:id/edit",
        name: "EndorsementEdit",
        component: EndorsementForm,
        meta: { code: "ENDORSEMENT", permission: "UPDATE" },
    },
    {
        path: "/endorsements/auto/:id",
        name: "EndorsementDetail",
        component: EndorsementDetail,
        meta: { code: "ENDORSEMENT", permission: "VIEW" },
    },
    {
        path: "/customer-management/customer-classifications",
        name: "CustomerClassificationIndex",
        component: CustomerClassificationIndex,
        meta: { code: "CUSTOMER_CLASSIFICATION", permission: "VIEW" },
    },
    {
        path: "/customer-management/customer-classifications/new",
        name: "CustomerClassificationCreate",
        component: CustomerClassificationForm,
        meta: { code: "CUSTOMER_CLASSIFICATION", permission: "NEW" },
    },
    {
        path: "/customer-management/customer-classifications/:id/edit",
        name: "CustomerClassificationEdit",
        component: CustomerClassificationForm,
        meta: { code: "CUSTOMER_CLASSIFICATION", permission: "UPDATE" },
    },
    {
        path: "/customer-management/customer-classifications/:id",
        name: "CustomerClassificationDetail",
        component: CustomerClassificationDetail,
        meta: { code: "CUSTOMER_CLASSIFICATION", permission: "VIEW" },
    },
    //Customer Profile
    {
        path: '/customer-management/customer-profiles',
        name: 'CustomerProfile',
        component: CustomerProfileIndex,
        meta: { code: 'CUSTOMER_PROFILE', permission: 'VIEW' },
    },
    {
        path: '/customer-management/customer-profiles/:id',
        name: 'CustomerProfileDetail',
        component: CustomerProfileDetail,
        meta: { code: 'CUSTOMER_PROFILE', permission: 'VIEW' },
    },
    // Country
    {
        path: "/customer-management/countries",
        name: "CountryIndex",
        component: CountryIndex,
        meta: { code: "COUNTRY", permission: "VIEW" },
    },
    {
        path: "/customer-management/countries/new",
        name: "CountryCreate",
        component: CountryForm,
        meta: { code: "COUNTRY", permission: "NEW" },
    },
    {
        path: "/customer-management/countries/:id/edit",
        name: "CountryUpdate",
        component: CountryForm,
        meta: { code: "COUNTRY", permission: "UPDATE" },
    },
    {
        path: "/customer-management/countries/:id",
        name: "CountryDetail",
        component: CountryDetail,
        meta: { code: "COUNTRY", permission: "VIEW" },
    },
    //product condition rating
    {
        path: '/product-configuration/product-condition-rating',
        name: 'ProductConditionIndex',
        component: ProductConditionIndex,
        meta: { code: 'PRODUCT_CONDITION_RATING', permission: 'VIEW' }
    },
    {
        path: '/product-configuration/product-condition-rating/new',
        name: 'ProductConditionCreate',
        component: ProductConditionForm,
        meta: { code: 'PRODUCT_CONDITION_RATING', permission: 'NEW' }
    },
    {
        path: '/product-configuration/product-condition-rating/:id/edit',
        name: 'ProductConditionUpdate',
        component: ProductConditionForm,
        meta: { code: 'PRODUCT_CONDITION_RATING', permission: 'UPDATE' }
    },
    {
        path: '/product-configuration/product-condition-rating/:id',
        name: 'ProductConditionDetail',
        component: ProductConditionDetail,
        meta: { code: 'PRODUCT_CONDITION_RATING', permission: 'VIEW' }
    },
    // Business Category
    {
        path: "/business-management/business-categories",
        name: "BusinessCategoryIndex",
        component: BusinessCategoryIndex,
        meta: { code: "BUSINESS_CATEGORY", permission: "VIEW" },
    },
    {
        path: "/business-management/business-categories/new",
        name: "BusinessCategoryCreate",
        component: BusinessCategoryForm,
        meta: { code: "BUSINESS_CATEGORY", permission: "NEW" },
    },
    {
        path: "/business-management/business-categories/:id/edit",
        name: "BusinessCategoryEdit",
        component: BusinessCategoryForm,
        meta: { code: "BUSINESS_CATEGORY", permission: "UPDATE" },
    },
    {
        path: "/business-management/business-categories/:id",
        name: "BusinessCategoryDetail",
        component: BusinessCategoryDetail,
        meta: { code: "BUSINESS_CATEGORY", permission: "VIEW" },
    },
    // Business Handler
    {
        path: "/business-management/business-handlers",
        name: "BusinessHandlerIndex",
        component: BusinessHandlerIndex,
        meta: { code: "BUSINESS_HANDLER", permission: "VIEW" },
    },
    {
        path: "/business-management/business-handlers/new",
        name: "BusinessHandlerCreate",
        component: BusinessHandlerForm,
        meta: { code: "BUSINESS_HANDLER", permission: "NEW" },
    },
    {
        path: "/business-management/business-handlers/:id/edit",
        name: "BusinessHandlerEdit",
        component: BusinessHandlerForm,
        meta: { code: "BUSINESS_HANDLER", permission: "UPDATE" },
    },
    {
        path: "/business-management/business-handlers/:id",
        name: "BusinessHandlerDetail",
        component: BusinessHandlerDetail,
        meta: { code: "BUSINESS_HANDLER", permission: "VIEW" },
    },
    // Business HandlerChannel
    {
        path: "/business-management/business-channels",
        name: "BusinessChannelIndex",
        component: BusinessChannelIndex,
        meta: { code: "BUSINESS_CHANNEL", permission: "VIEW" },
    },
    {
        path: "/business-management/business-channels/new",
        name: "BusinessChannelCreate",
        component: BusinessChannelForm,
        meta: { code: "BUSINESS_CHANNEL", permission: "NEW" },
    },
    {
        path: "/business-management/business-channels/:id/edit",
        name: "BusinessChannelEdit",
        component: BusinessChannelForm,
        meta: { code: "BUSINESS_CHANNEL", permission: "UPDATE" },
    },
    {
        path: "/business-management/business-channels/:id",
        name: "BusinessChannelDetail",
        component: BusinessChannelDetail,
        meta: { code: "BUSINESS_CHANNEL", permission: "VIEW" },
    },
    // Bank Information
    {
        path: '/bank-informations',
        name: 'BankInformationIndex',
        component: BankInformationIndex,
        meta: { code: 'BANK_INFORMATION', permission: 'VIEW' },
    },
    {
        path: '/bank-informations/new',
        name: 'BankInformationForm',
        component: BankInformationForm,
        meta: { code: 'BANK_INFORMATION', permission: 'NEW' },
    },
    {
        path: '/bank-informations/:id/edit',
        name: 'BankInformationUpdate',
        component: BankInformationForm,
        meta: { code: 'BANK_INFORMATION', permission: 'UPDATE' },
    },
    {
        path: '/bank-informations/:id',
        name: 'BankInformationDetail',
        component: BankInformationDetail,
        meta: { code: 'BANK_INFORMATION', permission: 'VIEW' },
    },
    {
        path: '/surcharge-rules',
        name: 'SetupSurchargeIndex',
        component: SetupSurchargeIndex,
        meta: { code: 'SURCHARGE_RULE', permission: 'VIEW' },
    },
    {
        path: '/surcharge-rules/new',
        name: 'SetupSurchargeForm',
        component: SetupSurchargeForm,
        meta: { code: 'SURCHARGE_RULE', permission: 'NEW' },
    },
    {
        path: '/surcharge-rules/:id/edit',
        name: 'SetupSurchargeUpdate',
        component: SetupSurchargeForm,
        meta: { code: 'SURCHARGE_RULE', permission: 'UPDATE' },
    }

].concat(claimRoutes).concat(hsRoutes).concat(errorRoutes).concat(paRoutes).concat(renewalRoutes).concat(travelRoutes).concat(securityRoutes);
export {
    routes
}
