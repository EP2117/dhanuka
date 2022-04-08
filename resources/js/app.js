require('./bootstrap');

window.Vue = require('vue');

import VueRouter from 'vue-router';
Vue.use(VueRouter);

import VueAxios from 'vue-axios';
import axios from 'axios';

import App from './App.vue';
Vue.use(VueAxios, axios);

import Form from './utilities/Form';
window.Form = Form;

import select2 from 'select2';
window.select2 = select2;

import swal from 'sweetalert';
window.swal = swal;

import Dropzone from 'dropzone';
window.Dropzone = Dropzone;

import moment from 'moment';
import 'moment-timezone';
window.moment = moment;

import datePicker from 'vue-bootstrap-datetimepicker';
Vue.use(datePicker);

import { PaginationPlugin } from 'bootstrap-vue'
Vue.use(PaginationPlugin)


import HomeComponent from './components/HomeComponent.vue';
import Brand from './views/brand.vue';
import BrandForm from './views/brandForm.vue';
import Category from './views/category.vue';
import CategoryForm from './views/categoryForm.vue';
import Uom from './views/uom.vue';
import UomForm from './views/uomForm.vue';
import Product from './views/product.vue';
import ProductForm from './views/ProductForm.vue';
import Customer from './views/customer.vue';
import CustomerForm from './views/customerForm.vue';
import MainWarehouse from './views/mainWarehouse.vue';
import MainWarehouseForm from './views/mainWarehouseForm.vue';
import Transfer from './views/transfer.vue';
import TransferForm from './views/transferForm.vue';
import Receive from './views/receive.vue';
import TransferDetail from './views/transferDetail.vue';
import Sale from './views/sale.vue';
import SaleForm from './views/saleForm.vue';
import Collection from './views/collection.vue';
import CollectionForm from './views/collectionForm.vue';
import DailySaleProductRpt from './views/dailySaleProductRpt.vue';
import DailySaleRpt from './views/dailySaleRpt.vue';
import InventoryRpt from './views/inventoryRpt.vue';
import SOProductRpt from './views/saleOrderProductRpt.vue';
import SaleComparisonRpt from './views/saleComparisonRpt.vue';
import PendingApprovalRpt from './views/pendingApprovalRpt.vue';
import UomImport from './views/uomImport.vue';
import BrandImport from './views/brandImport.vue';
import CategoryImport from './views/categoryImport.vue';
import WarehouseImport from './views/warehouseImport.vue';
import ProductImport from './views/productImport.vue';
import ProductMinQtyImport from './views/productMinQtyImport.vue';
import StateImport from './views/stateImport.vue';
import TownshipImport from './views/townshipImport.vue';
import CustomerTypeImport from './views/customerTypeImport.vue';
import CustomerImport from './views/customerImport.vue';
import Order from './views/order.vue';
import OrderForm from './views/orderForm.vue';
import OrderApproval from './views/orderApproval.vue';
import User from './views/user.vue';
import UserForm from './views/userForm.vue';
import Delivery from './views/delivery.vue';
import Branch from './views/branch.vue';
import BranchForm from './views/branchForm.vue';
import Warehouse from './views/warehouse.vue';
import WarehouseForm from './views/warehouseForm.vue';
import SaleMan from './views/saleMan.vue';
import SaleManForm from './views/saleManForm.vue';
import SupplierForm from "./views/SupplierForm";
import Supplier  from "./views/Supplier.Vue";
import Purchase  from "./views/Purchase";
import PurchaseForm  from "./views/PurchaseForm";
import PurchaseCollection from "./views/PurchaseCollection"
import PurchaseCollectionForm from "./views/PurchaseCollectionForm"
import AccountHead from "./views/AccountHead";
import AccountHeadForm from "./views/AccountHeadForm";
import SubAccount from "./views/SubAccount/SubAccount";
import SubAccountForm from "./views/SubAccount/SubAccountForm";
import Receipt from "./views/Receipt/Receipt";
import ReceiptForm from "./views/Receipt/ReceiptForm";
import Payment from "./views/Payment/Payment";
import PaymentForm from "./views/Payment/PaymentForm";
import Cashbook from "./views/Cashbook/Cashbook";
import DailyPurchaseProductReport from "./views/Report/DailyPurchaseProductReport";
import CreditPaymentReport from "./views/Report/CreditPaymentReport";
import SupplierOpeningBalance from "./views/SupplierOpeningBalance/SupplierOpeningBalance";
import SupplierOpeningBalanceForm from "./views/SupplierOpeningBalance/SupplierOpeningBalanceForm";
import CustomerOpeningBalance from "./views/CustomerOpeningBalance/CustomerOpeningBalance";
import CustomerOpeningBalanceForm from "./views/CustomerOpeningBalance/CustomerOpeningBalanceForm";
import PurchaseOutstandingReport from "./views/Report/PurchaseOutstandingReport";
import SaleOutstandingReport from "./views/Report/SaleOutstandingReport";
import CreditCollectionReport from "./views/Report/CreditCollectionReport";
import InventoryAdjustment from "./views/Inventory/InventoryAdjustment";
import InventoryAdjustmentForm from "./views/Inventory/InventoryAdjustmentForm";
import InventoryValuation from "./views/Report/InventoryValuationReport";
import ProfitAndLoss from "./views/Report/ProfitAndLossReport";
import CustomerBlackListRpt from "./views/customerBlackListRpt";
import CustomerWiseRpt from "./views/customerWiseRpt";
import ProductCostingRpt from "./views/productCostingRpt";
import Ledger from "./views/Ledger/Ledger";
import SaleAdvance from "./views/saleAdvance";
import SaleAdvanceForm from "./views/saleAdvanceForm";
import PurchaseAdvance from "./views/purchaseAdvance";
import PurchaseAdvanceForm from "./views/purchaseAdvanceForm";
import Return from "./views/return";
import ReturnForm from "./views/ReturnForm";
import ReturnPayment from "./views/returnPayment";
import ReturnPaymentForm from "./views/ReturnPaymentForm";
import CustomerReturn from "./views/customerReturn";
import CustomerReturnForm from "./views/customerReturnForm";
import LandedCosting from "./views/landedCosting";
import LandedCostingForm from "./views/landedCostingForm";
import Currency from "./views/currency";
import CurrencyForm from "./views/currencyForm";
import CurrencyGainLossRpt from "./views/currencyGainLossRpt";
import SaleCurrencyGainLossRpt from "./views/saleCurrencyGainLossRpt";
import SaleReturnRpt from "./views/saleReturnRpt";
import SaleReturnProductRpt from "./views/saleReturnProductRpt";
import SaleReturnPaymentRpt from "./views/saleReturnPaymentRpt";
import ReturnOsRpt from "./views/returnOsRpt";

const routes = [
    {
      name: 'home',
      path: '/home',
      component: HomeComponent
    },
    {
        name:'product',
      path: '/product',
      component: Product
    },
    {
      name:'product_form',
      path: '/product/edit/:id',
      component: ProductForm
    },
    {
      name: 'product_form',
      path: '/product/new',
      component: ProductForm
    },

    {
      path: '/brand',
      component: Brand
    },
    {
      path: '/brand/edit/:id',
      component: BrandForm
    },
    {
      path: '/brand/new',
      component: BrandForm
    },

    {
      path: '/category',
      component: Category
    },
    {
      path: '/category/edit/:id',
      component: CategoryForm
    },
    {
      path: '/category/new',
      component: CategoryForm
    },

    {
      path: '/uom',
      component: Uom
    },
    {
      path: '/uom/edit/:id',
      component: UomForm
    },
    {
      path: '/uom/new',
      component: UomForm
    },

    {
      path: '/customer',
      component: Customer
    },
    {
      path: '/customer/edit/:id',
      component: CustomerForm
    },
    {
      path: '/customer/new',
      component: CustomerForm
    },

    {
      path: '/inventory/main-warehouse',
      component: MainWarehouse
    },
    {
      path: '/inventory/main-warehouse/new',
      component: MainWarehouseForm
    },
    {
      path: '/inventory/main-warehouse/edit/:id',
      component: MainWarehouseForm
    },
    {
      path: '/inventory/transfer',
      component: Transfer
    },
    {
      path: '/inventory/transfer/new',
      component: TransferForm
    },
    {
      path: '/inventory/transfer/edit/:id',
      component: TransferForm
    },
    {
      path: '/inventory/receive/:id',
      component: TransferDetail
    },
    {
      path: '/inventory/receive',
      component: Receive
    },
    {
      path: '/sale/:sale_type',
      component: Sale
    },
    {
      path: '/sale/:sale_type/new',
      component: SaleForm
    },
    {
      path: '/sale/:sale_type/edit/:id',
      component: SaleForm
    },
    {
      path: '/collection',
      component: Collection
    },
    {
      path: '/collection/new',
      component: CollectionForm
    },
    {
      path: '/collection/edit/:id',
      component: CollectionForm
    },
    {
      path: '/order',
      component: Order
    },
    {
      path: '/order/new',
      component: OrderForm
    },
    {
      path: '/order/edit/:id',
      component: OrderForm
    },
    {
      path: '/order_approval',
      component: OrderApproval
    },
    {
      path: '/delivery',
      component: Delivery
    },
    {
      path: '/users',
      component: User
    },
    {
      path: '/user/new',
      component: UserForm
    },
    {
      path: '/user/edit/:id',
      component: UserForm
    },
    {
      path: '/branch',
      component: Branch
    },
    {
      path: '/branch/new',
      component: BranchForm
    },
    {
      path: '/branch/edit/:id',
      component: BranchForm
    },
    {
      path: '/warehouse',
      component: Warehouse
    },
    {
      path: '/warehouse/new',
      component: WarehouseForm
    },
    {
      path: '/warehouse/edit/:id',
      component: WarehouseForm
    },
    {
      path: '/sale-man',
      component: SaleMan
    },
    {
      path: '/sale-man/new',
      component: SaleManForm
    },
    {
      path: '/sale-man/edit/:id',
      component: SaleManForm
    },
    {
      path: '/report/daily-sale-rpt',
      component: DailySaleRpt
    },
    {
      path: '/report/daily-sale-product-rpt',
      component: DailySaleProductRpt
    },
    {
      path: '/report/inventory-rpt',
      component: InventoryRpt
    },
    {
      path: '/report/so-product-rpt',
      component: SOProductRpt
    },
    {
      path: '/report/sale-comparison-rpt',
      component: SaleComparisonRpt
    },
    {
      path: '/report/pending-approval-rpt',
      component: PendingApprovalRpt
    },
    {
      path: '/report/blacklist-rpt',
      component: CustomerBlackListRpt
    },
    {
      path: '/report/customer-wise-rpt',
      component: CustomerWiseRpt
    },
    {
      path: '/report/product-costing-rpt',
      component: ProductCostingRpt
    },
    {
      path: '/import/uom',
      component: UomImport
    },
    {
      path: '/import/brand',
      component: BrandImport
    },
    {
      path: '/import/category',
      component: CategoryImport
    },
    {
      path: '/import/warehouse',
      component: WarehouseImport
    },
    {
      path: '/import/state',
      component: StateImport
    },
    {
      path: '/import/township',
      component: TownshipImport
    },
    {
      path: '/import/customer_type',
      component: CustomerTypeImport
    },
    {
      path: '/import/customer',
      component: CustomerImport
    },
    {
      path: '/import/product',
      component: ProductImport
    },
    {
      path: '/import/product_qty',
      component: ProductMinQtyImport
    },
    {
        name:'supplier',
        path: '/supplier',
        component: Supplier
    },
    {
        path: '/supplier/new',
        component: SupplierForm
    },
    {
        path: '/supplier/edit/:id',
        component: SupplierForm
    },
    {
        name:'purchase',
        path:'/purchase/:purchase_type',
        component:Purchase
    },
    {
        path:'/purchase/:purchase_type/create',
        component:PurchaseForm
    },
    {
        path:'/purchase/edit/:id',
        component:PurchaseForm
    },
    {
        name:'purchase_collection',
        path:'/purchase_collection',
        component: PurchaseCollection
    },
    {
        path:'/purchase_collection/new',
        component: PurchaseCollectionForm
    },
    {
        path:'/purchase_collection/edit/:id',
        component: PurchaseCollectionForm
    },
    {
        name:'account_head',
        path: '/account_head',
        component: AccountHead
    },
    {
        path: '/account_head/new',
        component: AccountHeadForm
    },
    {
        path: '/account_head/edit/:id',
        component: AccountHeadForm
    },
    {
        name:'sub_account',
        path: '/sub_account',
        component: SubAccount
    },
    {
        path: '/sub_account/new',
        component: SubAccountForm
    },
    {
        path: '/sub_account/edit/:id',
        component: SubAccountForm
    },
    {
        name:'receipt',
        path: '/receipt',
        component: Receipt
    },
    {
        path: '/receipt/new',
        component: ReceiptForm
    },
    {
        path: '/receipt/edit/:id',
        component: ReceiptForm
    },
    {
        name:'payment',
        path: '/payment',
        component: Payment
    },
    {
        path: '/payment/new',
        component: PaymentForm
    },
    {
        path: '/payment/edit/:id',
        component: PaymentForm
    },
    {
        name:'cashbook',
        path: '/cashbook',
        component: Cashbook
    },
    {
        name:'daily_purchase_product_report',
        path:'/report/daily_purchase_product_report',
        component: DailyPurchaseProductReport
    },
    {
        name:'credit_payment_report',
        path:'/report/credit_payment_report',
        component: CreditPaymentReport
    },
    {
      name:'supplier_ob',
      path:'/supplier_ob',
      component: SupplierOpeningBalance,
  },
  {
      path:'/supplier_ob/create',
      component: SupplierOpeningBalanceForm,
  },
  {
      path:'/supplier_ob/edit/:id',
      component: SupplierOpeningBalanceForm,
  },
  {
      name:'customer_ob',
      path:'/customer_ob',
      component: CustomerOpeningBalance,
  },
  {
      path:'/customer_ob/create',
      component: CustomerOpeningBalanceForm,
  },
  {
      path:'/customer_ob/edit/:id',
      component: CustomerOpeningBalanceForm,
  },
  {
    path:'/report/purchase_outstanding',
    component: PurchaseOutstandingReport,
},
{
  path:'/report/sale_outstanding',
  component: SaleOutstandingReport,  
},
{
  path:'/report/credit_collection',
  component: CreditCollectionReport,
},
{
  name:'inventory_adjustment',
  path:'/inventory/adjustment',
  component:InventoryAdjustment,
},
{
//  name:'inventory_adjustment_create',
 path:'/inventory/create_adjustment',
 component:InventoryAdjustmentForm,
},
{
 path:'/inventory_adjustment/:id/edit',
 component:InventoryAdjustmentForm,
},
{
  path:'/report/valuation',
  component:InventoryValuation,
 },
 {
   name:'ledger',
  path:'/ledger',
  component:Ledger,
 },
 {
   name:'profit_and_loss',
   path:'/report/profit_and_loss',
   component:ProfitAndLoss,
 },
 {
      name:'sale_advance',
      path:'/sale_advance',
      component: SaleAdvance,
  },
  {
      path: '/sale_advance/new',
      component: SaleAdvanceForm,
    },
  {
      path:'/sale_advance/edit/:id',
      component: SaleAdvanceForm,
  },
  {
      name:'purchase_advance',
      path:'/purchase_advance',
      component: PurchaseAdvance,
  },
  {
      path: '/purchase_advance/new',
      component: PurchaseAdvanceForm,
    },
  {
      path:'/purchase_advance/edit/:id',
      component: PurchaseAdvanceForm,
  },
  {
      name:'return',
      path:'/sale_return',
      component: Return,
  },
  {
      path: '/sale_return/new',
      component: ReturnForm,
    },
  {
      path:'/sale_return/edit/:id',
      component: ReturnForm,
  },
  {
      path:'/return_payment',
      component: ReturnPayment,
  },
  {
      path: '/return_payment/new',
      component: ReturnPaymentForm,
    },
  {
      path:'/return_payment/edit/:id',
      component: ReturnPaymentForm,
  },
  {
      path:'/customer_return',
      component: CustomerReturn,
  },
  {
      path: '/customer_return/new',
      component: CustomerReturnForm,
    },
  {
      path:'/customer_return/edit/:id',
      component: CustomerReturnForm,
  },
  {
      path:'/product_costing',
      component: LandedCosting,
  },
  {
      path: '/product_costing/new',
      component: LandedCostingForm,
    },
  {
      path:'/product_costing/edit/:id',
      component: LandedCostingForm,
  },
  {
      path:'/currency',
      component: Currency,
  },
  {
      path: '/currency/new',
      component: CurrencyForm,
  },
  {
      path: '/report/currency-gain-loss-rpt',
      component: CurrencyGainLossRpt,
  },
  {
      path: '/report/sale-currency-gain-loss-rpt',
      component: SaleCurrencyGainLossRpt,
  },
  {
      path: '/report/sale-return-rpt',
      component: SaleReturnRpt,
  },
  {
      path: '/report/sale-return-product-rpt',
      component: SaleReturnProductRpt,
  },
  {
      path: '/report/sale-return-payment-rpt',
      component: SaleReturnPaymentRpt,
  },
  {
      path: '/report/return-os-rpt',
      component: ReturnOsRpt,
  },
  
];

const router = new VueRouter({routes: routes});
//const app = new Vue(Vue.util.extend({ router }, App)).$mount('#app');
/*export default new VueRouter({
    routes,
    linkActiveClass : 'active'
});*/
const app = new Vue({
    el: '#app',
    router
});
